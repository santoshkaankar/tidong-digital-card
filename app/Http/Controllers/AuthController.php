<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required'
        ]);

        $loginInput = $request->input('login');

        if (filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
            $loginField = 'email';
        } elseif (is_numeric($loginInput)) {
            $loginField = 'mobile';
        } else {
            $loginField = 'username';
        }

        $credentials = [
            $loginField => $loginInput,
            'password' => $request->password
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            return $this->redirectToUserDashboard(Auth::user());
        }

        return back()->withErrors([
            'login' => 'Invalid credentials or incorrect password.',
        ])->onlyInput('login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:255|unique:users',
            'email' => 'required|email|unique:users',
            'mobile' => 'required|string|max:15|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,employee,business,member,vendor',
            'business_type' => 'nullable|string|max:255'
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username ?? null,
            'slug' => Str::slug($request->name) . '-' . rand(1000, 9999),
            'email' => $request->email,
            'mobile' => $request->mobile,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'business_type' => in_array($request->role, ['business', 'vendor']) ? $request->business_type : null
        ]);

        Auth::login($user);

        return $this->redirectToUserDashboard($user);
    }

    /**
     * Helper to handle dynamic redirects based on role and business type
     */
    private function redirectToUserDashboard($user)
    {
        $role = strtolower($user->role);

        if ($role === 'admin') {
            return Route::has('admin.dashboard') ? redirect()->route('admin.dashboard') : view('admin.dashboard');
        }

        if ($role === 'employee') {
            return Route::has('employee.dashboard') ? redirect()->route('employee.dashboard') : view('employee.dashboard');
        }

        if (in_array($role, ['business', 'vendor'])) {
            $routeName = match ($user->business_type) {
                'taxi' => 'vendor.taxi.dashboard',
                'hotel' => 'vendor.hotel.dashboard',
                'emporium' => 'vendor.emporium.dashboard',
                'food', 'restaurant' => 'vendor.restaurant.dashboard',
                'money_exchange' => 'vendor.exchange.dashboard',
                'tourist_guide' => 'vendor.guide.dashboard',
                default => null,
            };

            if ($routeName && Route::has($routeName)) {
                return redirect()->route($routeName);
            }

            return redirect('/');
        }

        return Route::has('member.dashboard') ? redirect()->route('member.dashboard') : view('member.dashboard');
    }

    // ==========================================
    // COMPLETE PASSWORD RESET LOGIC
    // ==========================================

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetPassword($token, Request $request)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', 'Password reset successfully! Please login.')
            : back()->withErrors(['email' => __($status)]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}