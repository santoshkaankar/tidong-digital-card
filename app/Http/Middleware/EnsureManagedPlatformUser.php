<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureManagedPlatformUser
{
    public function handle(
        Request $request,
        Closure $next,
        string $requiredAccountType
    ): Response {
        $user = $request->user();

        if (! $user) {
            return redirect()->guest(route('login'));
        }

        $hasRequiredAccountType = $user->userAccountTypes()
            ->where('status', 'active')
            ->whereHas('accountType', function ($query) use ($requiredAccountType) {
                $query->where('code', $requiredAccountType)
                    ->where('is_active', true);
            })
            ->exists();

        $hasValidPlatformRole = $user->roleAssignments()
            ->where('is_active', true)
            ->where('scope_type', 'platform')
            ->whereNull('scope_id')
            ->where(function ($query) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->whereHas('role', function ($query) {
                $query->where('code', '!=', 'super_admin');
            })
            ->exists();

        /*
         * Staff Admin:
         * Super Admin ko bhi access dena hai.
         *
         * Super Admin ki identity:
         * platform_admin account type + active super_admin platform role.
         */
        $isSuperAdmin = $user->userAccountTypes()
            ->where('status', 'active')
            ->whereHas('accountType', function ($query) {
                $query->where('code', 'platform_admin')
                    ->where('is_active', true);
            })
            ->exists()
            &&
            $user->roleAssignments()
                ->where('is_active', true)
                ->where('scope_type', 'platform')
                ->whereNull('scope_id')
                ->where(function ($query) {
                    $query->whereNull('starts_at')
                        ->orWhere('starts_at', '<=', now());
                })
                ->where(function ($query) {
                    $query->whereNull('expires_at')
                        ->orWhere('expires_at', '>', now());
                })
                ->whereHas('role', function ($query) {
                    $query->where('code', 'super_admin');
                })
                ->exists();

        /*
         * Super Admin ko staff-admin ke liye special access.
         * Baaki managed users ke liye existing rules same rahenge.
         */
        if ($requiredAccountType === 'admin' && $isSuperAdmin) {
            return $next($request);
        }

        if (! $hasRequiredAccountType || ! $hasValidPlatformRole) {
            abort(403, 'Aapko is dashboard ko access karne ki permission nahi hai.');
        }

        return $next($request);
    }
}
