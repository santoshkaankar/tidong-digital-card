<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDeliveryPartner
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user) return redirect()->guest(route('login'));
        abort_unless(app(\App\Services\Delivery\DeliveryIdentity::class)
            ->hasActiveRiderCredentials((int)$user->id), 403, 'Delivery partner access denied.');
        return $next($request);
    }
}
