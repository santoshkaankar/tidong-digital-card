<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        // अगर यूज़र का रोल Allowed Roles में नहीं है
        if (!in_array($user->role, $roles)) {
            
            // अगर Admin किसी Member पेज पर जाने की कोशिश करे -> Admin Dashboard भेजें
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            
            // अगर Member किसी Admin पेज पर जाने की कोशिश करे -> Member Dashboard भेजें
            if ($user->role === 'user' || $user->role === 'member') {
                return redirect()->route('member.dashboard');
            }

            return redirect('/');
        }

        return $next($request);
    }
}