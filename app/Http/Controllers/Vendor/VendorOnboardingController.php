<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Authorization\Role;
use App\Models\Identity\AccountType;
use App\Models\Vendor\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class VendorOnboardingController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        $this->forbidPlatformStaff($request);

        if ($request->user()->vendor()->exists()) {
            return redirect()->route('vendor.home');
        }

        return view('vendor.apply');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->forbidPlatformStaff($request);

        $validated = $request->validate([
            'display_name' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        DB::transaction(function () use ($request, $validated): void {
            // Serializes applications from two tabs of the same user.
            DB::table('users')->where('id', $request->user()->id)->lockForUpdate()->first();

            if (Vendor::withTrashed()->where('user_id', $request->user()->id)->exists()) {
                throw ValidationException::withMessages([
                    'display_name' => 'A Vendor application already exists for this account.',
                ]);
            }

            $accountType = AccountType::query()
                ->where('code', 'vendor')->where('is_active', true)->first();
            $ownerRole = Role::query()->where('code', 'vendor_owner')->first();

            if (! $accountType || ! $ownerRole) {
                throw ValidationException::withMessages([
                    'display_name' => 'Vendor registration is not configured. Please contact support.',
                ]);
            }

            $vendor = Vendor::create([
                'user_id' => $request->user()->id,
                'vendor_code' => 'VND-'.Str::upper((string) Str::ulid()),
                'display_name' => trim($validated['display_name']),
                'status' => 'pending',
            ]);

            $request->user()->userAccountTypes()->updateOrCreate(
                ['account_type_id' => $accountType->id],
                ['status' => 'active', 'activated_at' => now()]
            );

            $request->user()->roleAssignments()->create([
                'role_id' => $ownerRole->id,
                'scope_type' => 'vendor',
                'scope_id' => $vendor->id,
                'assigned_by' => $request->user()->id,
                'starts_at' => now(),
                'is_active' => true,
            ]);

            DB::table('audit_logs')->insert([
                'actor_user_id' => $request->user()->id,
                'action' => 'vendor.applied',
                'entity_type' => 'vendor',
                'entity_id' => $vendor->id,
                'new_values' => json_encode([
                    'status' => 'pending',
                    'vendor_code' => $vendor->vendor_code,
                    'display_name' => $vendor->display_name,
                ]),
                'ip_address' => $request->ip(),
                'created_at' => now(),
            ]);
        });

        return redirect()->route('vendor.home')
            ->with('success', 'Your Vendor application was submitted for review.');
    }

    private function forbidPlatformStaff(Request $request): void
    {
        abort_if($request->user()->userAccountTypes()
            ->whereHas('accountType', fn ($q) => $q->whereIn('code', [
                'platform_admin', 'admin', 'platform_employee',
            ]))
            ->exists(), 403, 'Platform staff cannot register as Vendors.');
    }
}
