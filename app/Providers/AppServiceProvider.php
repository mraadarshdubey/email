<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Throttle for the bulk "emails" queue so we stay under the provider's
        // send rate (Amazon SES). Consumed by SendEmailJob's middleware().
        RateLimiter::for('ses-send', function () {
            return Limit::perMinute(config('bulkmail.rate_per_minute', 700));
        });
    }
}
