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
            'name'                => ['required', 'string', 'max:255'],
            'email'               => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'mobile'              => ['required', 'string', 'max:15', 'unique:users,mobile'],
            'password'            => ['required', 'confirmed', Rules\Password::defaults()],
            'role'                => ['required', 'string', 'in:member,business'],
            'business_type'       => ['nullable', 'string'],
            // Member ke liye sponsor ki referral_id database mein honi zaroori hai (Fake ID block)
            'sponsor_referral_id' => ['required_if:role,member', 'string', 'exists:users,referral_id'],
            'position'            => ['required_if:role,member', 'string', 'in:left,right'],
            'terms'               => ['accepted'],
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
        $referralId = null;

        if ($request->role === 'member') {
            // Sponsor ko uski referral_id se dhundhein
            $sponsor = User::where('referral_id', $request->sponsor_referral_id)->first();
            
            if (!$sponsor) {
                throw ValidationException::withMessages([
                    'sponsor_referral_id' => 'Yeh Sponsor Referral ID system mein mojood nahi hai!'
                ]);
            }

            $sponsorId = $sponsor->id;

            // Binary Spillover Placement Logic (Parent ID aur Position find karna)
            $placement = $this->findPlacementNode($sponsor->id, $request->position);
            $parentId = $placement['parent_id'];
            $position = $placement['position'];
        }

        // Agar user member hai toh uske liye 12 characters ki unique referral_id generate hogi
        if ($request->role === 'member') {
            do {
                $referralId = 'TABS' . strtoupper(bin2hex(random_bytes(4))); // Total 12 chars
            } while (User::where('referral_id', $referralId)->exists());
        }

        $user = User::create([
            'name'          => $request->name,
            'username'      => $request->username ?? $request->email, // Normal login username/email
            'referral_id'   => $referralId, // 12-character MLM Referral ID
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