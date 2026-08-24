<?php

namespace App\Http\Middleware;

use App\Services\AutomationTickService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * Fallback path for environments with no cron worker: due sequence steps,
 * follow-ups, and RSS checks are processed opportunistically as
 * authenticated users browse the app — throttled so it only actually runs
 * every ~20 seconds, and failures never break the page.
 *
 * In production, `automations:tick` runs on a real schedule via cron
 * (see app/Console/Kernel.php) and this middleware becomes a no-op most of
 * the time since the work is already done — harmless either way.
 */
class TickAutomations
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cache::add() is atomic — only the first of any concurrent requests
        // wins the lock, so simultaneous page loads can't double-tick.
        if (Cache::add('automation_tick_lock', true, 20)) {
            app(AutomationTickService::class)->run();
        }

        return $next($request);
    }
}
