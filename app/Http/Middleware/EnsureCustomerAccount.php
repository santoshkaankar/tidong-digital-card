<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCustomerAccount
{
    /**
     * Allow only authenticated users with an active customer account.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        // TIDONG CUSTOMER MODULE GATE: no cross-role access on disable.
        abort_unless(config('tidong_customer.enabled', true)
            && app(\App\Services\Platform\ModuleRegistry::class)->isEnabled('customer'), 403);
        // Platform operators are not allowed into the customer dashboard,
        // even if an old/accidental customer assignment exists.
        if (app(\App\Services\Auth\DashboardRedirector::class)
            ->hasAnyPlatformAccountRecord($request->user())) {
            abort(403, 'Platform users cannot access the Customer area.');
        }
        $hasActiveCustomerAccount = $request->user()
            ->userAccountTypes()
            ->whereHas('accountType', function ($query) {
                $query->where('code', 'customer')
                    ->where('is_active', true);
            })
            ->where('status', 'active')
            ->exists();

        if (! $hasActiveCustomerAccount) {
            abort(403, 'You do not have access to the customer area.');
        }

        return $next($request);
    }
}
