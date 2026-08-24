<?php

namespace App\Services;

use App\Models\RssFeed;

/**
 * The single place that processes due Sequences, Follow-ups, and RSS
 * checks. Called two ways:
 *   - opportunistically, throttled, from TickAutomations middleware — a
 *     fallback so automations still move even without a cron worker.
 *   - on a real schedule, from the `automations:tick` Artisan command, once
 *     the server has `php artisan schedule:run` wired into cron. This is
 *     the reliable path for production.
 */
class AutomationTickService
{
    public function run(): void
    {
        try {
            app(SequenceRunnerService::class)->processDue();
            app(FollowupRunnerService::class)->processDue();

            RssFeed::active()
                ->where(function ($q) {
                    $q->whereNull('last_checked_at')
                        ->orWhere('last_checked_at', '<=', now()->subMinutes(5));
                })
                ->get()
                ->each(fn (RssFeed $feed) => app(RssFeedService::class)->check($feed));
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
