<?php

namespace App\Services;

use App\Jobs\SendIndividualEmailJob;
use App\Models\EmailAccount;
use App\Models\EmailTracking;
use Illuminate\Support\Str;

class TrackedMailerService
{
    public function __construct(private EmailTrackingService $tracking)
    {
    }

    public function send(int $userId, EmailAccount $account, string $recipient, string $subject, string $body, ?string $batchId = null): void
    {
        if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        $record = EmailTracking::create([
            'user_id' => $userId,
            'batch_id' => $batchId,
            'email_account_id' => $account->id,
            'token' => (string) Str::random(48),
            'recipient' => $recipient,
            'subject' => $subject,
            'sent_at' => now(),
        ]);

        $trackedBody = $this->tracking->prepare($body, $record->token);

        SendIndividualEmailJob::dispatch($account, $recipient, $subject, $trackedBody);
    }
}
