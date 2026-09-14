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
            'name'             => ['required', 'string', 'max:255'],
            'email'            => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'mobile'           => ['required', 'string', 'max:15', 'unique:users,mobile'],
            'password'         => ['required', 'confirmed', Rules\Password::defaults()],
            'role'             => ['required', 'string', 'in:member,business'],
            'business_type'    => ['nullable', 'string'],
            'sponsor_username' => ['nullable', 'string', 'min:12', 'exists:users,username'],
            'position'         => ['nullable', 'in:left,right'],
            'terms'            => ['accepted'],
        ]);

        $businessType = null;
        if ($request->role === 'business') {
            $businessType = $request->filled('business_type') 
                ? strtolower($request->business_type) 
                : 'food';
        }

        $parentId = null;
        $sponsorId = null;
        $position = null;

        if ($request->role === 'member') {
            $sponsorUsername = $request->sponsor_username;

            // Agar user ne sponsor ID nahi bhari, toh system ki pehli ID (Root User) ko default maan lo
            if (empty($sponsorUsername)) {
                $sponsor = User::orderBy('id', 'asc')->first();
            } else {
                $sponsor = User::where('username', $sponsorUsername)->first();
            }

            if ($sponsor) {
                $sponsorId = $sponsor->id;

                // Agar leg position select nahi ki, toh default 'left' set ho jayegi
                $preferredPosition = $request->filled('position') ? $request->position : 'left';

                // Binary Spillover Placement Logic
                $placement = $this->findPlacementNode($sponsor->id, $preferredPosition);
                $parentId = $placement['parent_id'];
                $position = $placement['position'];
            }
        }

        // Agar username nahi diya, toh minimum 12+ characters ka unique code auto-generate hoga
        $username = $request->filled('username') ? $request->username : 'TABS' . time() . rand(10, 99);

        $user = User::create([
            'name'          => $request->name,
            'username'      => $username,
            'email'         => $request->email,
            'mobile'        => $request->mobile,
            'password'      => Hash::make($request->password),
            'role'          => $request->role,
            'sponsor_id'    => $sponsorId,
            'parent_id'     => $parentId,
            'position'      => $position,
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

    private function findPlacementNode($startNodeId, $preferredPosition)
    {
        $currentId = $startNodeId;
        $currentPos = $preferredPosition;

        while (true) {
            $existingChild = User::where('parent_id', $currentId)
                                 ->where('position', $currentPos)
                                 ->first();

            if (!$existingChild) {
                return [
                    'parent_id' => $currentId,
                    'position' => $currentPos
                ];
            }

            $currentId = $existingChild->id;
        }
    }
}