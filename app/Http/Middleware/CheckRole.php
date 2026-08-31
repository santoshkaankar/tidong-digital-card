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

        $userRole = Auth::user()->role;

        // 2. Agar user ka role allowed roles me se hai -> Page kholne do
        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        // 3. Agar wrong user galat URL kholta hai -> Unke apne dashboard par bhejo
        if ($userRole === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($userRole === 'user' || $userRole === 'member') {
            return redirect()->route('member.dashboard');
        }

        if ($userRole === 'business' || $userRole === 'vendor') {
            return redirect()->route('vendor.dashboard');
        }

        return redirect('/');
    }
}