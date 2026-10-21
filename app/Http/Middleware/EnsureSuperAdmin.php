<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Login nahi hai to login page par bhejein.
        if (! $user) {
            return redirect()->guest(route('login'));
        }

        // Active platform_admin account type zaroori hai.
        $hasActivePlatformAdminAccount = $user
            ->userAccountTypes()
            ->where('status', 'active')
            ->whereHas('accountType', function ($query) {
                $query
                    ->where('code', 'platform_admin')
                    ->where('is_active', true);
            })
            ->exists();

        // Active super_admin role, platform scope aur valid dates zaroori hain.
        $hasActiveSuperAdminRole = $user
            ->roleAssignments()
            ->where('is_active', true)
            ->where('scope_type', 'platform')
            ->whereNull('scope_id')
            ->where(function ($query) {
                $query
                    ->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($query) {
                $query
                    ->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->whereHas('role', function ($query) {
                $query->where('code', 'super_admin');
            })
            ->exists();

        // Dono checks pass hone par hi Super Admin area allow hoga.
        if (
            ! $hasActivePlatformAdminAccount
            || ! $hasActiveSuperAdminRole
        ) {
            abort(
                403,
                'You are not authorized to access the Super Admin area.'
            );
        }

        return $next($request);
    }
}
