<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // 1. Agar user login nahi hai -> Login page par bhejo
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $userRole = $user->role;

        // 2. Agar user ka role allowed roles me se hai -> Page kholne do
        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        // 3. Prevent Infinite Loop: Agar vendor pehle se hi sahi route par access kar raha hai
        if (($userRole === 'business' || $userRole === 'vendor') && $request->is('vendor/*')) {
            return $next($request);
        }

        // 4. Agar wrong user galat URL kholta hai -> Unke apne dedicated dashboard par bhejo
        if ($userRole === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($userRole === 'user' || $userRole === 'member') {
            return redirect()->route('member.dashboard');
        }

        if ($userRole === 'employee') {
            return redirect()->route('employee.dashboard');
        }

        if ($userRole === 'business' || $userRole === 'vendor') {
            $targetRoute = match ($user->business_type) {
                'taxi' => 'vendor.taxi.dashboard',
                'hotel' => 'vendor.hotel.dashboard',
                'emporium' => 'vendor.emporium.dashboard',
                'food', 'restaurant' => 'vendor.restaurant.dashboard',
                'money_exchange' => 'vendor.exchange.dashboard',
                'tourist_guide' => 'vendor.guide.dashboard',
                default => 'vendor.dashboard',
            };

            // Safeguard: Check if the user is already on the target route to prevent infinite loop
            if ($request->routeIs($targetRoute)) {
                return $next($request);
            }

            return redirect()->route($targetRoute);
        }

        return redirect('/');
    }
}