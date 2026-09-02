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
    'username' => ['nullable', 'string', 'max:255', 'unique:'.User::class],
    'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
    'mobile' => ['required', 'string', 'max:15', 'unique:'.User::class],
    'password' => ['required', 'confirmed', Rules\Password::defaults()],
    'role' => ['required', 'string', 'in:member,business'], // 'employee' is excluded here
    'service_type' => ['nullable', 'string', 'max:100'],
    'vehicle_no' => ['nullable', 'string', 'max:100'],
    'license_no' => ['nullable', 'string', 'max:100'],
]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username ?? null,
            'slug' => \Illuminate\Support\Str::slug($request->name) . '-' . rand(1000, 9999),
            'email' => $request->email,
            'mobile' => $request->mobile,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'business_type' => $request->business_type ?? null,
            'service_type' => $request->service_type ?? 'food',
            'vehicle_no' => $request->vehicle_no ?? null,
            'license_no' => $request->license_no ?? null,
        ]);

        event(new Registered($user));
        Auth::login($user);

        // Role-based redirection after registration
        if ($user->role === 'admin') {
            return redirect(route('admin.dashboard', absolute: false));
        } elseif ($user->role === 'business') {
            return redirect(route('vendor.dashboard', absolute: false));
        } elseif ($user->role === 'employee') {
            return redirect(route('employee.dashboard', absolute: false));
        } else {
            return redirect(route('member.dashboard', absolute: false));
        }
    }
}