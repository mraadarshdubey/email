<?php

namespace App\Http\Controllers;

use App\Models\EmailContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

/**
 * One-click unsubscribe (RFC 8058).
 *
 * Links are signed, so a recipient can opt out from an email without logging
 * in, while the address cannot be tampered with. Mailbox providers that support
 * List-Unsubscribe-Post call the POST route directly; humans clicking the link
 * in the footer get the GET confirmation page.
 */
class UnsubscribeController extends Controller
{
    /**
     * Build the signed unsubscribe URL for a recipient.
     */
    public static function urlFor(string $email): string
    {
        return URL::signedRoute('unsubscribe.show', ['email' => $email]);
    }

    /**
     * Human-facing page. Shown when someone clicks the footer link.
     */
    public function show(Request $request)
    {
        $email = (string) $request->query('email');

        return view('emails.unsubscribe', [
            'email' => $email,
            'done' => false,
            'postUrl' => URL::signedRoute('unsubscribe.perform', ['email' => $email]),
        ]);
    }

    /**
     * Performs the opt-out. Called by one-click unsubscribe (mailbox providers)
     * and by the confirmation button on the page above.
     */
    public function perform(Request $request)
    {
        $email = (string) $request->query('email');

        $updated = EmailContact::where('email', $email)
            ->update(['status' => 'unsubscribed']);

        Log::info('Unsubscribe processed', ['email' => $email, 'matched' => $updated]);

        // RFC 8058 one-click clients ignore the body; return 200 quickly.
        if ($request->isMethod('post') && ! $request->acceptsHtml()) {
            return response('Unsubscribed', 200);
        }

        return view('emails.unsubscribe', [
            'email' => $email,
            'done' => true,
            'postUrl' => null,
        ]);
    }
}
