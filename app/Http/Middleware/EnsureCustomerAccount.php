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
