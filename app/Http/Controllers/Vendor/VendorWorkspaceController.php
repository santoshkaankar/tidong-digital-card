<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Vendor\Business;
use App\Models\Vendor\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class VendorWorkspaceController extends Controller
{
    public function index(Request $request): View
    {
        $vendor = $this->vendor($request);
        $businesses = $vendor->businesses()
            ->with('branches')
            ->orderByDesc('id')
            ->get();

        return view('vendor.dashboard', compact('vendor', 'businesses'));
    }

    public function storeBusiness(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'business_type' => ['required', 'string', 'regex:/^[a-z][a-z0-9_]{1,79}$/'],
        ]);

        DB::transaction(function () use ($request, $validated): void {
            $vendor = $this->approvedVendorForUpdate($request);
            $business = $vendor->businesses()->create([
                'name' => trim($validated['name']),
                'business_type' => $validated['business_type'],
                'slug' => Str::limit(Str::slug($validated['name']) ?: 'business', 180, '').'-'.Str::lower((string) Str::ulid()),
                'status' => 'pending',
                'is_accepting_orders' => false,
            ]);

            $this->audit($request, 'business.created', 'business', $business->id, [
                'vendor_id' => $vendor->id,
                'status' => 'pending',
                'business_type' => $business->business_type,
                'name' => $business->name,
            ]);
        });

        return redirect()->route('vendor.home')
            ->with('success', 'Business created and sent for approval. Orders remain disabled.');
    }

    public function storeBranch(Request $request, int $business): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'address_line' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'pincode' => ['nullable', 'string', 'max:15'],
            'india_post_office_id' => ['nullable', 'integer', 'exists:india_post_offices,id'],
            'latitude' => ['nullable', 'numeric', 'between:6,38', 'required_with:longitude'],
            'longitude' => ['nullable', 'numeric', 'between:68,98', 'required_with:latitude'],
        ]);

        DB::transaction(function () use ($request, $validated, $business): void {
            $vendor = $this->approvedVendorForUpdate($request);
            // Ownership check prevents one Vendor creating branches under another Vendor.
            $ownedBusiness = Business::query()
                ->where('id', $business)
                ->where('vendor_id', $vendor->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! in_array($ownedBusiness->status, ['pending', 'approved'], true)) {
                throw ValidationException::withMessages([
                    'business' => 'Branches cannot be added to this Business status.',
                ]);
            }

            // Resolve postal identity on the SERVER, not from user-supplied state/PIN.
            $selectedOffice = null;
            if (! empty($validated['india_post_office_id'])) {
                $selectedOffice = DB::table('india_post_offices')
                    ->where('id', $validated['india_post_office_id'])->first();
                if (! $selectedOffice) {
                    throw ValidationException::withMessages(['india_post_office_id' => 'Choose a valid Post Office.']);
                }
                $validated['state'] = $selectedOffice->state;
                $validated['district'] = $selectedOffice->district;
                $validated['post_office'] = $selectedOffice->post_office;
                $validated['pincode'] = $selectedOffice->pincode;
            }

            $branch = $ownedBusiness->branches()->create([
                ...$validated,
                'name' => trim($validated['name']),
                'branch_code' => 'BR-'.Str::upper((string) Str::ulid()),
                'country_code' => 'IN',
                'status' => 'active',
                'is_accepting_orders' => false,
            ]);

            $this->audit($request, 'branch.created', 'branch', $branch->id, [
                'business_id' => $ownedBusiness->id,
                'vendor_id' => $vendor->id,
                'name' => $branch->name,
                'is_accepting_orders' => false,
            ]);
        });

        return redirect()->route('vendor.home')
            ->with('success', 'Branch created. Accepting orders is disabled until setup is complete.');
    }

    private function vendor(Request $request): Vendor
    {
        return Vendor::query()->where('user_id', $request->user()->id)->firstOrFail();
    }

    private function approvedVendorForUpdate(Request $request): Vendor
    {
        $vendor = Vendor::query()
            ->where('user_id', $request->user()->id)
            ->lockForUpdate()
            ->firstOrFail();

        if ($vendor->status !== 'approved') {
            throw ValidationException::withMessages([
                'vendor' => 'Your Vendor account must be approved before creating Businesses or Branches.',
            ]);
        }

        return $vendor;
    }

    private function audit(Request $request, string $action, string $entityType, int $entityId, array $newValues): void
    {
        DB::table('audit_logs')->insert([
            'actor_user_id' => $request->user()->id,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'new_values' => json_encode($newValues),
            'ip_address' => $request->ip(),
            'created_at' => now(),
        ]);
    }
}
