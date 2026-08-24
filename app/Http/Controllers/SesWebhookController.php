<?php

namespace App\Http\Controllers;

use App\Models\EmailContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Receives Amazon SES bounce and complaint notifications via SNS.
 *
 * Hard bounces are suppressed permanently and complaints ("mark as spam") are
 * treated as an opt-out, so a recipient who reacts badly once is never mailed
 * again. Keeping these rates low is what protects sender reputation.
 */
class SesWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = json_decode($request->getContent(), true);

        if (! is_array($payload)) {
            Log::warning('SES webhook: unreadable payload');

            return response('bad request', 400);
        }

        $type = $payload['Type'] ?? null;

        // SNS confirms a new subscription by asking us to visit a URL once.
        if ($type === 'SubscriptionConfirmation') {
            return $this->confirmSubscription($payload);
        }

        if ($type !== 'Notification') {
            return response('ignored', 200);
        }

        $message = json_decode($payload['Message'] ?? '', true);

        if (! is_array($message)) {
            Log::warning('SES webhook: unreadable inner message');

            return response('ok', 200);
        }

        match ($message['notificationType'] ?? null) {
            'Bounce' => $this->handleBounce($message),
            'Complaint' => $this->handleComplaint($message),
            default => null,
        };

        return response('ok', 200);
    }

    /**
     * Visit the SNS confirmation URL so the topic starts delivering to us.
     * Only amazonaws.com URLs are fetched, so a forged payload cannot make the
     * server call an arbitrary host.
     */
    private function confirmSubscription(array $payload)
    {
        $url = $payload['SubscribeURL'] ?? '';
        $host = parse_url($url, PHP_URL_HOST) ?: '';

        if (! str_ends_with($host, '.amazonaws.com')) {
            Log::warning('SES webhook: refused non-AWS SubscribeURL', ['host' => $host]);

            return response('bad request', 400);
        }

        try {
            Http::timeout(10)->get($url);
            Log::info('SES webhook: SNS subscription confirmed');
        } catch (\Throwable $e) {
            Log::error('SES webhook: subscription confirm failed', ['error' => $e->getMessage()]);
        }

        return response('ok', 200);
    }

    /**
     * Permanent bounces are suppressed. Transient ones are left alone so a
     * temporarily-full mailbox is not lost forever.
     */
    private function handleBounce(array $message): void
    {
        $isPermanent = ($message['bounce']['bounceType'] ?? '') === 'Permanent';

        foreach ($message['bounce']['bouncedRecipients'] ?? [] as $recipient) {
            $email = $recipient['emailAddress'] ?? null;

            if (! $email) {
                continue;
            }

            if ($isPermanent) {
                EmailContact::where('email', $email)->update(['status' => 'bounced']);
            }

            Log::info('SES bounce', [
                'email' => $email,
                'type' => $message['bounce']['bounceType'] ?? 'unknown',
                'suppressed' => $isPermanent,
            ]);
        }
    }

    /**
     * A complaint means the recipient hit "spam" — treat it as an opt-out.
     */
    private function handleComplaint(array $message): void
    {
        foreach ($message['complaint']['complainedRecipients'] ?? [] as $recipient) {
            $email = $recipient['emailAddress'] ?? null;

            if (! $email) {
                continue;
            }

            EmailContact::where('email', $email)->update(['status' => 'unsubscribed']);

            Log::warning('SES complaint — recipient suppressed', ['email' => $email]);
        }
    }
}
