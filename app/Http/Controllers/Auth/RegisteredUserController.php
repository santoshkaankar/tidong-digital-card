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
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'mobile'        => ['required', 'string', 'max:15', 'unique:users,mobile'],
            'password'      => ['required', 'confirmed', Rules\Password::defaults()],
            'role'          => ['required', 'string', 'in:member,business'],
            'business_type' => ['nullable', 'string'],
            'terms'         => ['accepted'],
        ]);

        $businessType = null;
        if ($request->role === 'business') {
            $businessType = $request->filled('business_type') 
                ? strtolower($request->business_type) 
                : 'food';
        }

        $user = User::create([
            'name'          => $request->name,
            'username'      => $request->username,
            'email'         => $request->email,
            'mobile'        => $request->mobile,
            'password'      => Hash::make($request->password),
            'role'          => $request->role,
            'business_type' => $businessType,
            'vehicle_no'    => $request->vehicle_no ?? null,
            'license_no'    => $request->license_no ?? null,
        ]);

        event(new Registered($user));

        Auth::login($user);

        if ($user->role === 'admin') {
            return redirect(route('admin.dashboard', absolute: false));
        } elseif ($user->role === 'business') {
            return redirect(route('vendor.dashboard', absolute: false));
        } else {
            return redirect(route('member.dashboard', absolute: false));
        }
    }
}