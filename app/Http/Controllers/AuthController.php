<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

        // Check karo ki user ne email dala hai ya username (name)
        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        $credentials = [
            $loginField => $request->login,
            'password' => $request->password
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            $role = Auth::user()->role;
            if ($role == 'admin') return redirect()->route('admin.dashboard');
            if ($role == 'business') return redirect()->route('vendor.dashboard'); // Updated to vendor dashboard
            if ($role == 'employee') return redirect()->route('employee.dashboard');
            
            return redirect()->route('member.dashboard');
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
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,employee,business,member',
            'business_type' => 'nullable|string|max:255'
        ]);

        $user = User::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . rand(1000, 9999),
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'business_type' => $request->role == 'business' ? $request->business_type : null
        ]);

        Auth::login($user);

        if ($user->role == 'business') {
            return redirect()->route('vendor.dashboard'); // Updated to vendor dashboard
        } elseif ($user->role == 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role == 'employee') {
            return redirect()->route('employee.dashboard');
        }
        
        return redirect()->route('member.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}