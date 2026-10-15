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

        // 3. Safe Bypass: Agar vendor ya vendor_employee pehle se hi vendor URL par hai toh loop na bane
        if (($userRole === 'business' || $userRole === 'vendor' || $userRole === 'vendor_employee') && $request->is('vendor*')) {
            return $next($request);
        }

        // 4. STRICT LOCK: Users ko unke sahi Central Dashboard par bhejo
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

        // Vendor Employee ke liye Restaurant Dashboard par direct bypass
        if ($userRole === 'vendor_employee') {
            if (Route::has('vendor.restaurant.dashboard')) {
                return redirect()->route('vendor.restaurant.dashboard');
            }
            return redirect('/vendor/restaurant/dashboard');
        }

        // Vendor / Business user hamesha Master Dashboard par hi jayega
        if ($userRole === 'business' || $userRole === 'vendor') {
            if (Route::has('vendor.dashboard')) {
                return redirect()->route('vendor.dashboard');
            }
            return redirect()->route('businesses.index');
        }

        return redirect('/');
    }
}