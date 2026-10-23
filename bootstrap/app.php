<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function (): void {
            \Illuminate\Support\Facades\Route::middleware('web')
                ->prefix('admin')
                ->group(base_path('routes/admin.php'));

            \Illuminate\Support\Facades\Route::middleware('web')
                ->prefix('vendor')
                ->group(base_path('routes/vendor.php'));

            \Illuminate\Support\Facades\Route::middleware('web')
                ->prefix('customer')
                ->group(base_path('routes/customer.php'));

            \Illuminate\Support\Facades\Route::middleware('web')
                ->prefix('delivery')
                ->group(base_path('routes/delivery.php'));

            \Illuminate\Support\Facades\Route::middleware('web')
                ->prefix('marriage-planner')
                ->group(base_path('routes/marriage-planner.php'));

            \Illuminate\Support\Facades\Route::middleware('web')
                ->prefix('pos')
                ->group(base_path('routes/pos.php'));

            \Illuminate\Support\Facades\Route::middleware('web')
                ->prefix('kds')
                ->group(base_path('routes/kds.php'));

            \Illuminate\Support\Facades\Route::middleware('web')
                ->prefix('game')
                ->group(base_path('routes/game.php'));

                \Illuminate\Support\Facades\Route::middleware('web')
    ->prefix('staff-admin')
    ->group(base_path('routes/staff-admin.php'));

\Illuminate\Support\Facades\Route::middleware('web')
    ->prefix('employee')
    ->group(base_path('routes/employee.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Laravel's default guest redirect points all logged-in users to the
        // first 'home' route (Super Admin). Redirect by verified role instead.
        $middleware->redirectUsersTo(function (Request $request): string {
            $user = $request->user();
            return $user
                ? (app(\App\Services\Auth\DashboardRedirector::class)->urlFor($user) ?? url('/'))
                : url('/');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
