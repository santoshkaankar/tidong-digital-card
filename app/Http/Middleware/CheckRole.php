<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
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
        $userRole = $user->role ?? 'customer';

        // Role Normalization (Aliases ko standardise karne ke liye)
        $normalizedRole = match ($userRole) {
            'user', 'customer' => 'member',
            'business' => 'vendor',
            default => $userRole,
        };

        // 2. Agar user ka role allowed roles list me hai -> Next request par jaane do
        if (in_array($userRole, $roles) || in_array($normalizedRole, $roles)) {
            return $next($request);
        }

        // 3. Safe Bypass: Agar vendor pehle se hi vendor URL par hai toh loop na bane
        if (($userRole === 'business' || $userRole === 'vendor') && $request->is('vendor/*')) {
            return $next($request);
        }

        // 4. STRICT LOCK: Agar wrong user kisi aur ka URL khole, usko USKE KHUD KE DASHBOARD par kheinch laao
        if ($userRole === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if (in_array($userRole, ['user', 'member', 'customer'])) {
            return redirect()->route('member.dashboard');
        }

        if ($userRole === 'delivery') {
            return redirect()->route('delivery.dashboard');
        }

        if ($userRole === 'employee') {
            return redirect()->route('employee.dashboard');
        }

        if ($userRole === 'business' || $userRole === 'vendor') {
            $targetRoute = match ($user->business_type ?? null) {
                'taxi' => 'vendor.taxi.dashboard',
                'hotel' => 'vendor.hotel.dashboard',
                'emporium' => 'vendor.emporium.dashboard',
                'food', 'restaurant' => 'vendor.restaurant.dashboard',
                'money_exchange' => 'vendor.exchange.dashboard',
                'tourist_guide' => 'vendor.guide.dashboard',
                default => 'vendor.dashboard',
            };

            if (Route::has($targetRoute)) {
                if ($request->routeIs($targetRoute)) {
                    return $next($request);
                }
                return redirect()->route($targetRoute);
            }

            return redirect()->route('vendor.dashboard');
        }

        return redirect('/');
    }
}