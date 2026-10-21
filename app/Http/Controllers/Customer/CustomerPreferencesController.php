<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\Customer\CustomerAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CustomerPreferencesController extends Controller
{
    public function edit(Request $request)
    {
        return view('customer.preferences', ['profile' => $request->user()->customerProfile]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'preferred_locale' => ['required', 'string', Rule::in(config('tidong_customer.supported_locales', ['en']))],
            'marketing_opt_in' => ['required', 'boolean'],
        ]);

        DB::transaction(function () use ($request, $validated) {
            $user = $request->user();
            $user->newQuery()->whereKey($user->id)->lockForUpdate()->firstOrFail();
            $user->customerProfile()->updateOrCreate(
                ['user_id' => $user->id],
                ['preferred_locale' => $validated['preferred_locale'],
                 'marketing_opt_in' => (bool) $validated['marketing_opt_in']],
            );
            CustomerAudit::record($request, 'preferences_updated');
        });

        return back()->with('status', 'Preferences saved.');
    }
}
