<?php

namespace App\Http\Middleware;

use App\Services\Auth\DashboardRedirector;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureManagedPlatformUser
{
    public function handle(Request $request, Closure $next, string $requiredAccountType): Response
    {
        $user = $request->user();
        if (! $user) {
            return redirect()->guest(route('login'));
        }

        $dashboard = app(DashboardRedirector::class);
        // No cross-dashboard exemption: Super Admin uses /admin, not /staff-admin.
        if (
            ! in_array($requiredAccountType, ['admin', 'platform_employee'], true)
            || $dashboard->isSuperAdmin($user)
            || ! $dashboard->hasActiveAccountType($user, $requiredAccountType)
            || ! $dashboard->hasValidManagedRole($user)
        ) {
            abort(403, 'Aapko is dashboard ko access karne ki permission nahi hai.');
        }

        return $next($request);
    }
}
