<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
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

        // Pata lagayein ki user ne Email, Mobile, ya Username kya dala hai
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
            $role = strtolower(Auth::user()->role);

            if ($role == 'admin') {
                return Route::has('admin.dashboard') ? redirect()->route('admin.dashboard') : view('admin.dashboard');
            }
            if ($role == 'business') {
                return Route::has('vendor.dashboard') ? redirect()->route('vendor.dashboard') : view('vendor.dashboard');
            }
            if ($role == 'employee') {
                return Route::has('employee.dashboard') ? redirect()->route('employee.dashboard') : view('employee.dashboard');
            }

            return Route::has('member.dashboard') ? redirect()->route('member.dashboard') : view('member.dashboard');
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
            'role' => 'required|in:admin,employee,business,member',
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
            'business_type' => $request->role == 'business' ? $request->business_type : null
        ]);

        Auth::login($user);

        $role = strtolower($user->role);

        if ($role == 'business') {
            return Route::has('vendor.dashboard') ? redirect()->route('vendor.dashboard') : view('vendor.dashboard');
        } elseif ($role == 'admin') {
            return Route::has('admin.dashboard') ? redirect()->route('admin.dashboard') : view('admin.dashboard');
        } elseif ($role == 'employee') {
            return Route::has('employee.dashboard') ? redirect()->route('employee.dashboard') : view('employee.dashboard');
        }
        
        return Route::has('member.dashboard') ? redirect()->route('member.dashboard') : view('member.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}