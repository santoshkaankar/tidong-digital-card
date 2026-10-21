<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Identity\AccountType;
use App\Services\Auth\DashboardRedirector;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CustomerAuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Public registration is only for customers.
     */
    public function register(Request $request)
    {
        $data = $request->validate([
    'name' => ['required', 'string', 'max:255'],
    'email' => ['required', 'email', 'max:255', 'unique:users,email'],
    'password' => ['required', 'string', 'min:8', 'confirmed'],
]);

$user = DB::transaction(function () use ($data) {
    $customerType = AccountType::firstOrCreate(
        ['code' => 'customer'],
        ['name' => 'Customer', 'is_active' => true]
    );
    // This account can only be created as a Customer, never as an admin/vendor.
    if (! $customerType->is_active) {
        throw ValidationException::withMessages([
            'email' => 'Customer account type is not active. Please contact support.',
        ]);
    }

    $user = User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => \Illuminate\Support\Facades\Hash::make($data['password']),
    ]);
    $user->userAccountTypes()->create([
        'account_type_id' => $customerType->id,
        'status' => 'active',
        'activated_at' => now(),
    ]);
    return $user;
});

Auth::login($user);
$request->session()->regenerate();
return redirect()->route('customer.home');
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

        // Use the SAME eligibility checks as the already-logged-in guest
        // redirect. No account is sent to a dashboard without validation.
        $dashboard = app(DashboardRedirector::class)->routeFor($user);
        if ($dashboard !== null) {
            return redirect()->route($dashboard);
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
     * Clear authentication and invalidate the session.
     */
    private function logoutSession(Request $request): void
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
