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
            'name'                => ['required', 'string', 'max:255'],
            'username'            => ['nullable', 'string', 'max:255', 'unique:users'],
            'email'               => ['required', 'email', 'unique:users'],
            'mobile'              => ['required', 'string', 'max:15', 'unique:users'],
            'password'            => ['required', 'min:6'],
            'role'                => ['required', 'in:admin,employee,business,member,vendor'],
            'business_type'       => ['nullable', 'string', 'max:255'],
            'sponsor_referral_id' => ['nullable', 'string', 'exists:users,referral_id'],
            'position'            => ['required_if:role,member', 'string', 'in:left,right'],
        ]);

        $parentId = null;
        $sponsorId = null;
        $position = 'left';
        $referralId = null;

        if ($request->role === 'member') {
            $sponsorRefId = $request->sponsor_referral_id ?: 'TDMS6395GSSS';

            $sponsor = User::where('referral_id', $sponsorRefId)->first();
            
            if (!$sponsor) {
                return back()->withErrors(['sponsor_referral_id' => 'Yeh Sponsor Referral ID system mein mojood nahi hai!'])->withInput();
            }

            $sponsorId = $sponsor->id;
            $preferredPosition = $request->position ?: 'left';

            $placement = $this->findPlacementNode($sponsor->id, $preferredPosition);
            $parentId = $placement['parent_id'];
            $position = $placement['position'];

            do {
                $referralId = 'TABS' . strtoupper(Str::random(8));
            } while (User::where('referral_id', $referralId)->exists());
        }

        $user = User::create([
            'name'          => $request->name,
            'username'      => $request->username ?? null,
            'referral_id'   => $referralId,
            'sponsor_id'    => $sponsorId,
            'parent_id'     => $parentId,
            'position'      => $position,
            'slug'          => Str::slug($request->name) . '-' . rand(1000, 9999),
            'email'         => $request->email,
            'mobile'        => $request->mobile,
            'password'      => Hash::make($request->password),
            'role'          => $request->role,
            'business_type' => in_array($request->role, ['business', 'vendor']) ? $request->business_type : null
        ]);

        if ($request->role === 'member' && $parentId) {
            $currentParentId = $parentId;
            $currentPosition = $position;

            while ($currentParentId) {
                $parentUser = User::find($currentParentId);
                if (!$parentUser) {
                    break;
                }

                if ($currentPosition === 'left') {
                    $parentUser->increment('left_count');
                } else {
                    $parentUser->increment('right_count');
                }

                $currentPosition = $parentUser->position;
                $currentParentId = $parentUser->parent_id;
            }
        }

        Auth::login($user);

        return $this->redirectToUserDashboard($user);
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

    private function redirectToUserDashboard($user)
    {
        $role = strtolower($user->role);

        if ($role === 'admin') {
            return Route::has('admin.dashboard') ? redirect()->route('admin.dashboard') : view('admin.dashboard');
        }

        if ($role === 'employee') {
            return Route::has('employee.dashboard') ? redirect()->route('employee.dashboard') : view('employee.dashboard');
        }

        // Strict Check for Shopping Dashboard (Retail replaced)
        if ($user->business_type === 'shopping') {
            return Route::has('shopping.shop-dashboard') ? redirect()->route('shopping.shop-dashboard') : view('shopping.shop-dashboard');
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
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Auth::logout();
        return redirect()->route('login');
    }
}