<?php

namespace App\Services;

class EmailTrackingService
{
    /**
     * Inject an open-tracking pixel and rewrite outbound links to go
     * through the click-tracking redirect, for the given token.
     */
    public function prepare(string $html, string $token): string
    {
        $html = $this->rewriteLinks($html, $token);
        $html = $this->appendPixel($html, $token);

        return $html;
    }

    protected function rewriteLinks(string $html, string $token): string
    {
        return preg_replace_callback(
            '/href=(["\'])(https?:\/\/[^"\']+)\1/i',
            function (array $matches) use ($token) {
                $quote = $matches[1];
                $originalUrl = $matches[2];

                $trackedUrl = route('tracking.click', ['token' => $token])
                    . '?u=' . urlencode(base64_encode($originalUrl))
                    . '&sig=' . self::signUrl($token, $originalUrl);

                return 'href=' . $quote . $trackedUrl . $quote;
            },
            $html
        );
    }

    /**
     * HMAC binding a (token, url) pair so /t/c/{token}?u=...&sig=... can only
     * redirect to a URL this app actually generated — without it, anyone
     * could turn the click endpoint into an open redirect to any site.
     */
    public static function signUrl(string $token, string $url): string
    {
        return hash_hmac('sha256', $token . '|' . $url, config('app.key'));
    }

    protected function appendPixel(string $html, string $token): string
    {
        $pixelUrl = route('tracking.open', ['token' => $token]);
        $pixel = '<img src="' . $pixelUrl . '" width="1" height="1" style="display:none;width:1px;height:1px;border:0;" alt="">';

        if (stripos($html, '</body>') !== false) {
            return preg_replace('/<\/body>/i', $pixel . '</body>', $html, 1);
        }

        return $html . $pixel;
    }
}
