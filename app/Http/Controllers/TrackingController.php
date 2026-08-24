<?php

namespace App\Http\Controllers;

use App\Models\EmailTracking;
use App\Models\EmailTrackingClick;
use App\Services\EmailTrackingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TrackingController extends Controller
{
    /**
     * 1x1 transparent GIF, base64-encoded.
     */
    private const PIXEL = 'R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBTAA7';

    public function open(string $token, Request $request): Response
    {
        $tracking = EmailTracking::where('token', $token)->first();

        if ($tracking) {
            $tracking->last_opened_at = now();
            $tracking->opened_at = $tracking->opened_at ?? now();
            $tracking->last_open_ip = $request->ip();
            $tracking->last_open_user_agent = substr((string) $request->userAgent(), 0, 500);
            $tracking->increment('open_count');
            $tracking->save();
        }

        return response(base64_decode(self::PIXEL))
            ->header('Content-Type', 'image/gif')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }

    public function click(string $token, Request $request)
    {
        $tracking = EmailTracking::where('token', $token)->first();
        $encoded = $request->query('u');
        $url = $encoded ? base64_decode($encoded) : null;
        $sig = (string) $request->query('sig');

        $sigValid = $url && hash_equals(EmailTrackingService::signUrl($token, $url), $sig);

        $isSafeUrl = $sigValid
            && filter_var($url, FILTER_VALIDATE_URL)
            && in_array(parse_url($url, PHP_URL_SCHEME), ['http', 'https'], true);

        if ($tracking) {
            $tracking->last_clicked_at = now();
            $tracking->clicked_at = $tracking->clicked_at ?? now();
            $tracking->increment('click_count');
            $tracking->save();

            EmailTrackingClick::create([
                'email_tracking_id' => $tracking->id,
                'url' => $isSafeUrl ? $url : $encoded,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'clicked_at' => now(),
            ]);
        }

        return $isSafeUrl ? redirect()->away($url) : redirect('/');
    }
}
