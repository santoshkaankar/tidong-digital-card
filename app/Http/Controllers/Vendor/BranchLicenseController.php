<?php
namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Vendor\Branch;
use App\Models\Vendor\BranchChangeRequest;
use App\Models\Vendor\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BranchLicenseController extends Controller
{
    private const TYPES = [
        'gstin' => 'GST Registration (GSTIN)', 'fssai' => 'FSSAI Licence / Registration',
        'trade_license' => 'Trade Licence', 'shop_establishment' => 'Shops and Establishments',
        'fire_noc' => 'Fire NOC', 'professional_tax' => 'Professional Tax', 'other' => 'Other Licence',
    ];
    private const FIELDS = ['license_type', 'license_number', 'registered_name', 'issuing_authority', 'issued_on', 'expires_on', 'notes'];

    public function index(Request $request, int $branch): View
    {
        $record = $this->ownedBranch($request, $branch);
        $record->load(['business', 'licenses' => fn ($query) => $query->orderBy('license_type')->orderBy('id')]);
        $pending = BranchChangeRequest::where('branch_id', $branch)->where('status', 'pending')->first();
        $history = BranchChangeRequest::where('branch_id', $branch)->whereIn('change_type', ['license_create', 'license_update'])->orderByDesc('id')->limit(15)->get();
        return view('vendor.branch-licenses', ['branch' => $record, 'types' => self::TYPES, 'pending' => $pending, 'history' => $history]);
    }

    public function store(Request $request, int $branch): RedirectResponse
    {
        $validated = $this->validated($request, true);
        $validated['license_number'] = $this->normalizeNumber($validated['license_type'], $validated['license_number']);
        $this->checkNumberFormat($validated['license_type'], $validated['license_number']);
        DB::transaction(function () use ($request, $branch, $validated): void {
            [$record, $vendor] = $this->ownedApprovedVendorBranch($request, $branch);
            $this->requireNoPending($branch);
            if ($record->licenses()->where('license_type', $validated['license_type'])
                ->where('license_number', $validated['license_number'])->exists()) {
                throw ValidationException::withMessages(['license_number' => 'This licence already exists for this branch.']);
            }
            $this->saveRequest($request, $record, $vendor->id, 'license_create', null, [], $validated);
        });
        return redirect()->route('vendor.branch.licenses.index', $branch)
            ->with('success', 'Licence submitted for Admin approval. Nothing is published or activated yet.');
    }

    public function update(Request $request, int $branch, int $license): RedirectResponse
    {
        $validated = $this->validated($request, false);
        DB::transaction(function () use ($request, $branch, $license, $validated): void {
            [$record, $vendor] = $this->ownedApprovedVendorBranch($request, $branch);
            $this->requireNoPending($branch);
            $entry = $record->licenses()->whereKey($license)->lockForUpdate()->firstOrFail();
            if (isset($validated['license_number']) && $validated['license_number'] !== '') {
                $validated['license_number'] = $this->normalizeNumber($entry->license_type, $validated['license_number']);
                $this->checkNumberFormat($entry->license_type, $validated['license_number']);
                if ($record->licenses()->where('license_type', $entry->license_type)
                    ->where('license_number', $validated['license_number'])->whereKeyNot($entry->id)->exists()) {
                    throw ValidationException::withMessages(['license_number' => 'The number already exists for this branch.']);
                }
            } else {
                unset($validated['license_number']);
            }
            $old = []; $new = [];
            foreach ($validated as $key => $value) {
                $previous = $entry->$key;
                if ($previous instanceof \DateTimeInterface) $previous = $previous->format('Y-m-d');
                if ((string)($previous ?? '') !== (string)($value ?? '')) {
                    $old[$key] = $previous;
                    $new[$key] = $value;
                }
            }
            if (! $new) throw ValidationException::withMessages(['license_number' => 'No changes to submit.']);
            $this->saveRequest($request, $record, $vendor->id, 'license_update', $entry->id, $old, $new);
        });
        return redirect()->route('vendor.branch.licenses.index', $branch)
            ->with('success', 'Licence edit requested. Existing approved values are unchanged until Admin approval.');
    }

    private function validated(Request $request, bool $creating): array
    {
        $rules = [
            'registered_name' => ['nullable', 'string', 'max:255'],
            'issuing_authority' => ['nullable', 'string', 'max:255'],
            'issued_on' => ['nullable', 'date'],
            'expires_on' => ['nullable', 'date', 'after_or_equal:issued_on'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
        if ($creating) {
            $rules['license_type'] = ['required', 'in:'.implode(',', array_keys(self::TYPES))];
            $rules['license_number'] = ['required', 'string', 'max:100'];
        } else $rules['license_number'] = ['nullable', 'string', 'max:100'];
        return $request->validate($rules);
    }

    private function ownedApprovedVendorBranch(Request $request, int $id): array
    {
        $record = $this->ownedBranch($request, $id, true);
        $vendor = Vendor::where('user_id', $request->user()->id)->lockForUpdate()->firstOrFail();
        if ($vendor->status !== 'approved') throw ValidationException::withMessages(['vendor' => 'Vendor must be approved first.']);
        return [$record, $vendor];
    }
    private function ownedBranch(Request $request, int $id, bool $lock = false): Branch
    {
        $vendor = Vendor::where('user_id', $request->user()->id)->firstOrFail();
        $query = Branch::query()->whereKey($id)->whereHas('business', fn ($q) => $q->where('vendor_id', $vendor->id));
        if ($lock) $query->lockForUpdate();
        return $query->firstOrFail();
    }
    private function requireNoPending(int $branch): void
    {
        if (BranchChangeRequest::where('branch_id', $branch)->where('status', 'pending')->exists()) {
            throw ValidationException::withMessages(['branch' => 'Another change request is awaiting Admin review for this branch.']);
        }
    }
    private function saveRequest(Request $request, Branch $branch, int $vendorId, string $kind, ?int $licenseId, array $old, array $new): void
    {
        $record = BranchChangeRequest::create([
            'branch_id' => $branch->id, 'vendor_id' => $vendorId, 'change_type' => $kind,
            'branch_license_id' => $licenseId, 'old_values' => $old,
            'proposed_values' => $new, 'status' => 'pending', 'requested_by' => $request->user()->id,
        ]);
        DB::table('audit_logs')->insert([
            'actor_user_id' => $request->user()->id, 'action' => 'branch.license.change_requested',
            'entity_type' => 'branch_change_request', 'entity_id' => $record->id,
            'new_values' => json_encode(['branch_id' => $branch->id, 'kind' => $kind, 'changed_fields' => array_keys($new)]),
            'ip_address' => $request->ip(), 'created_at' => now(),
        ]);
    }
    private function normalizeNumber(string $type, string $number): string
    {
        $number = trim($number);
        return in_array($type, ['gstin', 'fssai'], true) ? strtoupper($number) : $number;
    }
    private function checkNumberFormat(string $type, string $number): void
    {
        if ($type === 'gstin' && ! preg_match('/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z][1-9A-Z]Z[0-9A-Z]$/', $number)) {
            throw ValidationException::withMessages(['license_number' => 'GSTIN must have the correct 15-character format.']);
        }
        if ($type === 'fssai' && ! preg_match('/^[0-9]{14}$/', $number)) {
            throw ValidationException::withMessages(['license_number' => 'FSSAI number must contain 14 digits.']);
        }
    }
}
