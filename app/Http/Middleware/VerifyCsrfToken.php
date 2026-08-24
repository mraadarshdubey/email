<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // RFC 8058 one-click unsubscribe: mailbox providers POST here directly
        // and cannot send a CSRF token. The signed URL is what authenticates it.
        'unsubscribe',
    ];
}
