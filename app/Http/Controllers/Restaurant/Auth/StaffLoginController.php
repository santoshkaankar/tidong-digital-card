<?php

namespace App\Http\Controllers\Restaurant\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('vendor.restaurant.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (Auth::guard('restaurant_staff')->attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();

            $user = Auth::guard('restaurant_staff')->user();

            // Set active restaurant session for scoping
            session(['active_restaurant_id' => $user->restaurant_id]);

            // Role-based redirection
            if ($user->role === 'kitchen') {
                return redirect()->route('vendor.restaurant.kitchen.screen');
            } elseif ($user->role === 'cashier') {
                return redirect()->route('vendor.restaurant.pos.index');
            } elseif ($user->role === 'waiter') {
                return redirect()->route('vendor.restaurant.tables.index');
            } else {
                return redirect()->route('vendor.restaurant.dashboard');
            }
        }

        return back()->withErrors([
            'email' => 'Invalid credentials or unauthorized staff account.',
        ])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::guard('restaurant_staff')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('vendor.restaurant.staff.login');
    }
}