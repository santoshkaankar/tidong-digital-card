<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\Customer\CustomerAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class CustomerSecurityController extends Controller
{
    public function edit(Request $request)
    {
        return view('customer.security', [
            'recentActivity' => $request->user()->customerAccountAuditLogs()
                ->orderByDesc('id')->limit(10)->get(),
        ]);
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password:web'],
            'password' => ['required', 'confirmed', Password::min(10)->letters()->numbers()],
        ]);

        DB::transaction(function () use ($request, $data) {
            $user = $request->user();
            $user->password = $data['password']; // User model hashes this attribute.
            $user->setRememberToken(Str::random(60)); // Revoke prior remember-me cookies.
            $user->save();
            CustomerAudit::record($request, 'password_changed');
        });
        $request->session()->regenerate();

        return back()->with('status', 'Password changed. Existing sessions may remain active.');
    }
}
