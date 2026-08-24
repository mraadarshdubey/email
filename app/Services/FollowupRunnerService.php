<?php

namespace App\Services;

use App\Models\EmailFollowup;
use App\Models\EmailTracking;

class FollowupRunnerService
{
    public function __construct(private TrackedMailerService $mailer)
    {
    }

    /**
     * Send the follow-up email to every recipient of the source broadcast
     * who still hasn't opened/clicked, once the wait period has passed.
     * Each tracked email only ever gets one follow-up (followed_up_at is
     * stamped the moment it's sent).
     */
    public function processDue(?int $followupId = null): int
    {
        $query = EmailFollowup::active()->with(['emailTemplate', 'emailAccount']);

        if ($followupId) {
            $query->where('id', $followupId);
        }

        $sent = 0;

        foreach ($query->get() as $followup) {
            $sent += $this->runFollowup($followup);
        }

        return $sent;
    }

    protected function runFollowup(EmailFollowup $followup): int
    {
        if (!$followup->emailTemplate || !$followup->emailAccount) {
            return 0;
        }

        $due = EmailTracking::forBatchKey($followup->source_batch_id)
            ->whereNull('followed_up_at')
            ->where('sent_at', '<=', now()->subMinutes($followup->wait_minutes))
            ->when($followup->condition === 'not_opened', function ($q) {
                $q->whereNull('opened_at');
            }, function ($q) {
                $q->whereNull('clicked_at');
            })
            ->get();

        $sentCount = 0;

        foreach ($due as $tracking) {
            $this->mailer->send(
                $followup->user_id,
                $followup->emailAccount,
                $tracking->recipient,
                $followup->emailTemplate->subject,
                $followup->emailTemplate->body
            );

            $tracking->update(['followed_up_at' => now()]);
            $sentCount++;
        }

        if ($sentCount > 0) {
            $followup->increment('sent_count', $sentCount);
        }

        $followup->update(['last_run_at' => now()]);

        return $sentCount;
    }
}
