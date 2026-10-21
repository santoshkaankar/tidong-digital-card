<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

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
        // Protect every public login endpoint against password guessing.
        // Use the normalized email + IP and an additional IP-wide ceiling.
        RateLimiter::for('login', function (Request $request) {
            $email = Str::lower(trim((string) $request->input('email', '')));
            $ip = (string) $request->ip();

            return [
                Limit::perMinute(5)->by('login-account:'.hash('sha256', $email.'|'.$ip)),
                Limit::perMinute(30)->by('login-ip:'.$ip),
            ];
        });

        // Route-level feature switch applies to all web module endpoints.
        app('router')->pushMiddlewareToGroup('web', \App\Http\Middleware\EnforceEnabledModule::class);

        // XAMPP serves this Laravel application from /digital/public.
        // In local development, use APP_URL as the canonical root for
        // generated links, redirects, and POST form actions.
        // Never force this URL in tests or deployed environments.
        if (app()->environment('local')) {
            $appUrl = trim((string) config('app.url'));
            if ($appUrl !== '') {
                URL::forceRootUrl(rtrim($appUrl, '/'));
            }
        }
    }
}
