<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRestaurantAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $isVendor = Auth::check() && in_array(Auth::user()->role, ['vendor', 'business', 'admin']);
        $isStaff = Auth::guard('restaurant_staff')->check();

        if ($isVendor) {
            return $next($request);
        }

        if ($isStaff) {
            // Staff user ko active user set karna taaki baaki controllers me Auth::user() error na de
            Auth::setUser(Auth::guard('restaurant_staff')->user());
            return $next($request);
        }

        return redirect()->route('vendor.restaurant.staff.login');
    }
}