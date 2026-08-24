<?php

namespace App\Http\Controllers;

use App\Models\ContactTag;
use App\Models\EmailAccount;
use App\Models\RssFeed;
use App\Services\RssFeedService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RssFeedController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $feeds = RssFeed::where('user_id', Auth::id())
            ->with('recipientTag', 'emailAccount')
            ->orderByDesc('created_at')
            ->get();

        return view('rss-feeds.index', compact('feeds'));
    }

    public function create()
    {
        return view('rss-feeds.create', $this->formData());
    }

    public function store(Request $request)
    {
        $data = $this->validateFeed($request);
        $data['user_id'] = Auth::id();

        RssFeed::create($data);

        return redirect()->route('rss-feeds.index')->with('success', 'RSS feed added.');
    }

    public function edit(RssFeed $rss_feed)
    {
        $this->authorizeOwner($rss_feed);

        return view('rss-feeds.edit', array_merge(
            $this->formData(),
            ['feed' => $rss_feed]
        ));
    }

    public function update(Request $request, RssFeed $rss_feed)
    {
        $this->authorizeOwner($rss_feed);
        $data = $this->validateFeed($request);
        $rss_feed->update($data);

        return redirect()->route('rss-feeds.index')->with('success', 'RSS feed updated.');
    }

    public function destroy(RssFeed $rss_feed)
    {
        $this->authorizeOwner($rss_feed);
        $rss_feed->delete();

        return back()->with('success', 'RSS feed removed.');
    }

    public function toggleActive(RssFeed $rss_feed)
    {
        $this->authorizeOwner($rss_feed);
        $rss_feed->update(['is_active' => !$rss_feed->is_active]);

        return back()->with('success', 'Feed ' . ($rss_feed->is_active ? 'activated' : 'paused') . '.');
    }

    public function checkNow(RssFeed $rss_feed, RssFeedService $service)
    {
        $this->authorizeOwner($rss_feed);
        $sent = $service->check($rss_feed->fresh());

        $message = is_null($rss_feed->fresh()->last_item_link)
            ? 'Could not read this feed — check the URL.'
            : ($sent > 0 ? "Digest sent to {$sent} contact(s)." : 'Checked — no new posts to send.');

        return back()->with('success', $message);
    }

    private function authorizeOwner(RssFeed $feed): void
    {
        abort_unless($feed->user_id === Auth::id(), 403);
    }

    private function formData(): array
    {
        return [
            'tags' => ContactTag::where('user_id', Auth::id())->get(),
            'emailAccounts' => EmailAccount::where('is_active', true)->get(),
        ];
    }

    private function validateFeed(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'feed_url' => 'required|url|max:500',
            'email_account_id' => 'required|exists:email_accounts,id',
            'recipient_tag_id' => 'required|exists:contact_tags,id',
            'is_active' => 'nullable|boolean',
        ]);

        $validator->validate();

        return [
            'name' => $request->name,
            'feed_url' => $request->feed_url,
            'email_account_id' => $request->email_account_id,
            'recipient_tag_id' => $request->recipient_tag_id,
            'is_active' => $request->has('is_active'),
        ];
    }
}
