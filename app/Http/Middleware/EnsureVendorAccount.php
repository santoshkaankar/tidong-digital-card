<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureVendorAccount
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->guest(route('login'));
        }

        // Platform operators cannot act as Vendors, even if a data error
        // accidentally assigns them a Vendor account type.
        if ($user->userAccountTypes()
            ->whereHas('accountType', fn ($q) => $q->whereIn('code', [
                'platform_admin', 'admin', 'platform_employee',
            ]))
            ->exists()) {
            abort(403, 'Platform staff cannot access Vendor accounts.');
        }

        $hasVendorAccount = $user->userAccountTypes()
            ->where('status', 'active')
            ->whereHas('accountType', fn ($q) => $q
                ->where('code', 'vendor')
                ->where('is_active', true))
            ->exists();

        if (! $hasVendorAccount || ! $user->vendor()->exists()) {
            abort(403, 'You do not have an active Vendor account.');
        }

        return $next($request);
    }
}
