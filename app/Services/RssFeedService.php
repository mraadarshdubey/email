<?php

namespace App\Services;

use App\Models\EmailContact;
use App\Models\RssFeed;
use Illuminate\Support\Facades\Http;

class RssFeedService
{
    public function __construct(private TrackedMailerService $mailer)
    {
    }

    /**
     * Fetch a feed, and if there are items newer than the last time we checked,
     * send a digest email to every contact tagged with the feed's recipient tag.
     *
     * On the very first check (no last_item_link yet) nothing is sent — we just
     * record the newest item, so subscribers don't get blasted with the entire
     * historical backlog.
     */
    public function check(RssFeed $feed): int
    {
        $items = $this->fetchItems($feed->feed_url);

        if (empty($items)) {
            $feed->update(['last_checked_at' => now()]);
            return 0;
        }

        $isFirstCheck = is_null($feed->last_item_link);
        $knownLinks = $isFirstCheck ? [] : [$feed->last_item_link];

        $newItems = [];
        foreach ($items as $item) {
            if (in_array($item['link'], $knownLinks, true)) {
                break;
            }
            $newItems[] = $item;
        }

        $feed->update([
            'last_item_link' => $items[0]['link'] ?? $feed->last_item_link,
            'last_checked_at' => now(),
        ]);

        if ($isFirstCheck || empty($newItems) || !$feed->emailAccount) {
            return 0;
        }

        $recipients = $feed->recipient_tag_id
            ? EmailContact::where('user_id', $feed->user_id)
                ->whereHas('tags', fn ($q) => $q->where('contact_tags.id', $feed->recipient_tag_id))
                ->active()
                ->get()
            : collect();

        if ($recipients->isEmpty()) {
            return 0;
        }

        $subject = count($newItems) === 1
            ? $newItems[0]['title']
            : $feed->name . ': ' . count($newItems) . ' new posts';

        $body = $this->renderDigest($feed->name, $newItems);
        $batchId = (string) \Illuminate\Support\Str::uuid();

        foreach ($recipients as $contact) {
            $this->mailer->send($feed->user_id, $feed->emailAccount, $contact->email, $subject, $body, $batchId);
        }

        $feed->increment('sent_count', $recipients->count());

        return $recipients->count();
    }

    protected function fetchItems(string $url): array
    {
        try {
            $response = Http::timeout(10)->withHeaders([
                'User-Agent' => 'Sendpeak RSS Reader/1.0',
            ])->get($url);

            if (!$response->successful()) {
                return [];
            }

            $xml = @simplexml_load_string($response->body());
            if ($xml === false) {
                return [];
            }
        } catch (\Throwable $e) {
            return [];
        }

        $items = [];

        // RSS 2.0
        if (isset($xml->channel->item)) {
            foreach ($xml->channel->item as $item) {
                $items[] = [
                    'title' => trim((string) $item->title),
                    'link' => trim((string) $item->link),
                    'description' => trim(strip_tags((string) $item->description)),
                ];
            }
        }
        // Atom
        elseif (isset($xml->entry)) {
            foreach ($xml->entry as $entry) {
                $link = '';
                if (isset($entry->link)) {
                    foreach ($entry->link as $l) {
                        $attrs = $l->attributes();
                        if (!isset($attrs['rel']) || (string) $attrs['rel'] === 'alternate') {
                            $link = (string) $attrs['href'];
                            break;
                        }
                    }
                }
                $items[] = [
                    'title' => trim((string) $entry->title),
                    'link' => trim($link),
                    'description' => trim(strip_tags((string) ($entry->summary ?? $entry->content ?? ''))),
                ];
            }
        }

        return $items;
    }

    protected function renderDigest(string $feedName, array $items): string
    {
        $rows = '';
        foreach ($items as $item) {
            $rows .= '<tr><td style="padding:16px 0;border-top:1px solid #e2e8f0;">'
                . '<a href="' . e($item['link']) . '" style="font-size:16px;font-weight:600;color:#0f172a;text-decoration:none;">' . e($item['title']) . '</a>'
                . ($item['description'] ? '<p style="margin:8px 0 0;font-size:14px;color:#64748b;line-height:1.6;">' . e(mb_substr($item['description'], 0, 220)) . '…</p>' : '')
                . '<a href="' . e($item['link']) . '" style="display:inline-block;margin-top:8px;font-size:13px;color:#2563eb;">Read more →</a>'
                . '</td></tr>';
        }

        return '<div style="max-width:560px;margin:0 auto;font-family:-apple-system,Segoe UI,Roboto,sans-serif;">'
            . '<h2 style="font-size:18px;color:#0f172a;">' . e($feedName) . '</h2>'
            . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0">' . $rows . '</table>'
            . '</div>';
    }
}
