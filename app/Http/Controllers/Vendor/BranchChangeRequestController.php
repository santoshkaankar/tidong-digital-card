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

class BranchChangeRequestController extends Controller
{
    public function edit(Request $request, int $branch): View
    {
        $record = $this->ownedBranch($request, $branch);
        $history = BranchChangeRequest::query()->where('branch_id', $record->id)->orderByDesc('id')->limit(15)->get();
        return view('vendor.branch-edit', ['branch' => $record, 'history' => $history]);
    }

    public function submit(Request $request, int $branch): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'address_line' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'india_post_office_id' => ['nullable', 'integer', 'exists:india_post_offices,id'],
            'latitude' => ['nullable', 'numeric', 'between:6,38', 'required_with:longitude'],
            'longitude' => ['nullable', 'numeric', 'between:68,98', 'required_with:latitude'],
        ]);

        DB::transaction(function () use ($request, $branch, $validated): void {
            $record = $this->ownedBranch($request, $branch, true);
            $vendor = Vendor::query()->where('user_id', $request->user()->id)->lockForUpdate()->firstOrFail();
            if ($vendor->status !== 'approved') {
                throw ValidationException::withMessages(['vendor' => 'Vendor approval is required.']);
            }
            $this->assertNoPending($record->id);
            $keys = ['name', 'phone', 'email', 'address_line', 'city', 'india_post_office_id', 'state', 'district', 'post_office', 'pincode', 'latitude', 'longitude'];
            $before = $record->only($keys);
            $proposed = [];
            foreach (['name', 'phone', 'email', 'address_line', 'city'] as $field) {
                $value = $validated[$field] ?? null;
                if ($field === 'name') $value = trim($value);
                if ((string)($record->$field ?? '') !== (string)($value ?? '')) $proposed[$field] = $value;
            }
            // Postal identity is ALWAYS taken from our local, server-side directory.
            if (! empty($validated['india_post_office_id'])) {
                $office = DB::table('india_post_offices')->where('id', $validated['india_post_office_id'])->first();
                abort_unless($office, 422);
                foreach (['india_post_office_id' => 'id', 'state' => 'state', 'district' => 'district', 'post_office' => 'post_office', 'pincode' => 'pincode'] as $column => $officeColumn) {
                    if ((string)($record->$column ?? '') !== (string)($office->$officeColumn ?? '')) $proposed[$column] = $office->$officeColumn;
                }
            }
            foreach (['latitude', 'longitude'] as $coordinate) {
                if (array_key_exists($coordinate, $validated) && $validated[$coordinate] !== null) {
                    $value = number_format((float)$validated[$coordinate], 7, '.', '');
                    if ($value !== number_format((float)($record->$coordinate ?? 0), 7, '.', '')) $proposed[$coordinate] = $value;
                }
            }
            if (! $proposed) throw ValidationException::withMessages(['name' => 'No changes were made.']);
            $requestRecord = BranchChangeRequest::create([
                'branch_id' => $record->id, 'vendor_id' => $vendor->id, 'change_type' => 'branch',
                'old_values' => array_intersect_key($before, $proposed), 'proposed_values' => $proposed,
                'status' => 'pending', 'requested_by' => $request->user()->id,
            ]);
            DB::table('audit_logs')->insert([
                'actor_user_id' => $request->user()->id, 'action' => 'branch.change.requested',
                'entity_type' => 'branch_change_request', 'entity_id' => $requestRecord->id,
                'new_values' => json_encode(['branch_id' => $record->id, 'type' => 'branch', 'fields' => array_keys($proposed)]),
                'ip_address' => $request->ip(), 'created_at' => now(),
            ]);
        });
        return redirect()->route('vendor.branch.edit', $branch)->with('success', 'Changes sent to Admin for approval. Current branch details are unchanged.');
    }

    private function ownedBranch(Request $request, int $id, bool $lock = false): Branch
    {
        $vendor = Vendor::where('user_id', $request->user()->id)->firstOrFail();
        $query = Branch::query()->whereKey($id)->whereHas('business', fn ($q) => $q->where('vendor_id', $vendor->id));
        if ($lock) $query->lockForUpdate();
        return $query->firstOrFail();
    }
    private function assertNoPending(int $branchId): void
    {
        if (BranchChangeRequest::where('branch_id', $branchId)->where('status', 'pending')->exists()) {
            throw ValidationException::withMessages(['branch' => 'This branch has a pending change request. Wait for Admin review.']);
        }
    }
}
