<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Identity\AccountType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CustomerAuthController extends Controller
{
    public function showRegister()
    {
        abort(404, 'Public registration is closed.');
    }

    /**
     * Public registration is only for customers.
     */
    public function register(Request $request)
    {
        abort(403, 'Public registration is closed.');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Login for Super Admin, Admin, Platform Employee and Customer.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'The email or password is incorrect.',
            ]);
        }

        $request->session()->regenerate();

        $user = $request->user();

        // 1. Super Admin:
        // Both the platform_admin account type and valid super_admin role
        // are required.
        if (
            $this->hasActiveAccountType($user, 'platform_admin')
            && $this->hasValidPlatformRole($user, 'super_admin')
        ) {
            return redirect()->route('home');
        }

        // 2. Platform Admin / Employee:
        // The account type must be active and the platform role must be
        // valid now. A super_admin role cannot qualify through this branch.
        if (
    $this->hasActiveAccountType($user, 'admin')
    && $this->hasAnyValidManagedPlatformRole($user)
) {
    return redirect()->route('staff-admin.home');
}

if (
    $this->hasActiveAccountType($user, 'platform_employee')
    && $this->hasAnyValidManagedPlatformRole($user)
) {
    return redirect()->route('employee.home');
}

        // Delivery account uses an isolated role and approved partner profile.
        if (
            app(\App\Services\Platform\ModuleRegistry::class)->isEnabled('delivery')
            && $this->hasActiveAccountType($user, 'delivery')
            && $this->hasValidPlatformRole($user, 'delivery_partner')
            && \Illuminate\Support\Facades\Schema::hasTable('delivery_partners')
            && \Illuminate\Support\Facades\DB::table('delivery_partners')
                ->where('user_id', $user->id)->where('status', 'active')->exists()
        ) {
            return redirect()->route('delivery.home');
        }

        // 3. Customer:
        // Customers need an active customer account type.
        if ($this->hasActiveAccountType($user, 'customer')) {
            return redirect()->route('customer.home');
        }

        // Reject all other account types and invalid/inactive accounts.
        $this->logoutSession($request);

        throw ValidationException::withMessages([
            'email' => 'Is account ko login ke liye abhi permission nahi hai.',
        ]);
    }

    public function logout(Request $request)
    {
        $this->logoutSession($request);

        return redirect()->route('login');
    }

    /**
     * Check that the user has the requested active account type.
     */
    private function hasActiveAccountType(User $user, string $code): bool
    {
        return $user->userAccountTypes()
            ->where('status', 'active')
            ->whereHas('accountType', function ($query) use ($code) {
                $query->where('code', $code)
                    ->where('is_active', true);
            })
            ->exists();
    }

    /**
     * Check a specific valid platform role, including its start and expiry.
     */
    private function hasValidPlatformRole(User $user, string $roleCode): bool
    {
        return $user->roleAssignments()
            ->where('is_active', true)
            ->where('scope_type', 'platform')
            ->whereNull('scope_id')
            ->where(function ($query) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->whereHas('role', function ($query) use ($roleCode) {
                $query->where('code', $roleCode);
            })
            ->exists();
    }

    /**
     * Admin/Employee must have a valid platform role other than super_admin.
     */
    private function hasAnyValidManagedPlatformRole(User $user): bool
    {
        return $user->roleAssignments()
            ->where('is_active', true)
            ->where('scope_type', 'platform')
            ->whereNull('scope_id')
            ->where(function ($query) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->whereHas('role', function ($query) {
                $query->where('code', '!=', 'super_admin');
            })
            ->exists();
    }

    /**
     * Clear authentication and invalidate the session.
     */
    private function logoutSession(Request $request): void
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
