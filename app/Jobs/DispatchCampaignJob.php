<?php

namespace App\Jobs;

use App\Models\OneTimeSender;
use App\Models\TempMailAddress;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Fans out a campaign into individual SendEmailJob instances.
 *
 * This runs on the queue (not in the web request) so that campaigns of any
 * size — 70,000+ recipients — are queued without blocking the HTTP request or
 * exhausting memory. Recipients are streamed from the DB in chunks.
 */
class DispatchCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Fanning out a large list can take a while; give it room.
    public $timeout = 3600;
    public $tries = 1;

    public function __construct(
        protected int $campaignId,
        protected array $mailData
    ) {
    }

    public function handle(): void
    {
        $chunkSize = (int) config('bulkmail.dispatch_chunk', 500);
        $dispatched = 0;

        // Stream this campaign's recipients in chunks so memory stays flat.
        TempMailAddress::where('campaign_id', $this->campaignId)
            ->select(['id', 'email'])
            ->chunkById($chunkSize, function ($rows) use (&$dispatched) {
                foreach ($rows as $row) {
                    if (! filter_var($row->email, FILTER_VALIDATE_EMAIL)) {
                        Log::warning('Invalid email skipped in dispatcher: ' . $row->email);
                        continue;
                    }

                    SendEmailJob::dispatch($row->email, $this->mailData)->onQueue('emails');
                    $dispatched++;
                }

                // Drop the rows we've already fanned out so the temp table
                // never grows unbounded and re-runs stay idempotent.
                TempMailAddress::whereIn('id', $rows->pluck('id'))->delete();
            });

        OneTimeSender::where('id', $this->campaignId)->update(['status' => 'queued']);

        Log::info('Campaign fan-out complete', [
            'campaign_id' => $this->campaignId,
            'dispatched' => $dispatched,
        ]);
    }

    public function failed(\Throwable $e): void
    {
        Log::error('Campaign dispatch failed', [
            'campaign_id' => $this->campaignId,
            'error' => $e->getMessage(),
        ]);

        OneTimeSender::where('id', $this->campaignId)->update(['status' => 'failed']);
    }
}
