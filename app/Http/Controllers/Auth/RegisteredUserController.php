<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'slug' => \Illuminate\Support\Str::slug($request->name) . '-' . rand(1000, 9999),
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'business_type' => $request->business_type ?? null,
        ]);

        event(new Registered($user));
        Auth::login($user);

        // Role-based redirection after registration matching web.php
        if ($user->role === 'admin') {
            return redirect(route('admin.dashboard', absolute: false));
        } elseif ($user->role === 'business') {
            return redirect(route('vendor.dashboard', absolute: false));
        } elseif ($user->role === 'employee') {
            return redirect(route('employee.dashboard', absolute: false));
        } else {
            return redirect(route('customer.dashboard', absolute: false));
        }
    }
}