<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * Defaults to trusting everyone ('*') which is fine behind a dev tunnel
     * but must be locked down before going live: set TRUSTED_PROXIES in
     * .env to your load balancer / reverse proxy's real IP (or CIDR range,
     * comma-separated for more than one) once deployed — otherwise a
     * visitor can spoof X-Forwarded-For and fake their own IP in tracking
     * logs, rate limiting, etc.
     *
     * @var array<int, string>|string|null
     */
    protected $proxies;

    public function __construct()
    {
        $configured = env('TRUSTED_PROXIES', '*');
        $this->proxies = $configured === '*' ? '*' : array_map('trim', explode(',', $configured));
    }

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;
}
