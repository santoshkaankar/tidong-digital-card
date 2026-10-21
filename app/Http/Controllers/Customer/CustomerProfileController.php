<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\Customer\CustomerAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CustomerProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('customer.profile', [
            'profile' => $request->user()->customerProfile,
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:25', 'regex:/^[0-9+() \-]{7,25}$/'],
        ]);

        $emailChanged = $data['email'] !== $user->email;
        if ($emailChanged) {
            $request->validate(['current_password' => ['required', 'current_password:web']]);
        }

        DB::transaction(function () use ($request, $user, $data, $emailChanged) {
            // Serialize profile upserts if requests arrive together for a new account.
            $user->newQuery()->whereKey($user->id)->lockForUpdate()->firstOrFail();
            $user->name = $data['name'];
            $user->email = $data['email'];
            if ($emailChanged) {
                $user->email_verified_at = null;
            }
            $user->save();

            $user->customerProfile()->updateOrCreate(
                ['user_id' => $user->id],
                ['phone' => $data['phone'] ?? null],
            );
            CustomerAudit::record($request, $emailChanged ? 'profile_email_changed' : 'profile_updated');
        });

        return back()->with('status', 'Your profile was updated.');
    }
}
