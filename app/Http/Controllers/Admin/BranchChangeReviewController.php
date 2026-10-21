<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor\Branch;
use App\Models\Vendor\BranchChangeRequest;
use App\Models\Vendor\BranchLicense;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BranchChangeReviewController extends Controller
{
    private const BRANCH_ALLOWED = ['name','phone','email','address_line','city','india_post_office_id','state','district','post_office','pincode','latitude','longitude'];
    private const LICENSE_ALLOWED = ['license_type','license_number','registered_name','issuing_authority','issued_on','expires_on','notes'];

    private function baseName(): string
    {
        $segment = request()->segment(1);
        abort_unless(in_array($segment, ['admin', 'staff-admin', 'employee'], true), 404);
        return $segment.'.branch-changes.';
    }
    public function index(Request $request): View
    {
        $filter = $request->validate(['status' => ['nullable', 'in:pending,approved,rejected']]);
        $records = BranchChangeRequest::query()->with(['branch.business','vendor'])->when(
            $filter['status'] ?? 'pending', fn ($q, $status) => $q->where('status', $status)
        )->orderByDesc('id')->paginate(20)->withQueryString();
        return view('branch-changes.index', ['records' => $records, 'baseName' => $this->baseName()]);
    }
    public function show(Request $request, int $change): View
    {
        $record = BranchChangeRequest::query()->with(['branch.business','vendor'])->findOrFail($change);
        return view('branch-changes.show', ['record' => $record, 'baseName' => $this->baseName()]);
    }
    public function approve(Request $request, int $change): RedirectResponse
    {
        $input = $request->validate(['reason' => ['nullable','string','max:1000']]);
        DB::transaction(function () use ($request, $change, $input): void {
            $record = BranchChangeRequest::query()->whereKey($change)->lockForUpdate()->firstOrFail();
            $this->ensurePending($record);
            abort_if((int)$record->requested_by === (int)$request->user()->id, 403, 'Requester cannot approve own change.');
            $branch = Branch::whereKey($record->branch_id)->lockForUpdate()->firstOrFail();
            // The branch owner must remain the same Vendor who filed the request.
            abort_unless((int)$branch->business()->firstOrFail()->vendor_id === (int)$record->vendor_id, 409);
            $proposed = $record->proposed_values;
            $before = $record->old_values;
            if ($record->change_type === 'branch') {
                $this->assertAllowed($proposed, self::BRANCH_ALLOWED);
                $this->assertUnchanged($branch, $before);
                $branch->fill($proposed);
                $branch->save();
            } elseif ($record->change_type === 'license_create') {
                $this->assertAllowed($proposed, self::LICENSE_ALLOWED);
                if (! isset($proposed['license_type'], $proposed['license_number'])) abort(422);
                if (BranchLicense::where('branch_id', $branch->id)->where('license_type', $proposed['license_type'])->where('license_number', $proposed['license_number'])->exists()) {
                    throw ValidationException::withMessages(['license_number' => 'An identical licence already exists for this branch.']);
                }
                $branch->licenses()->create([...$proposed, 'recorded_by_user_id' => $record->requested_by]);
            } elseif ($record->change_type === 'license_update') {
                $this->assertAllowed($proposed, self::LICENSE_ALLOWED);
                $license = BranchLicense::where('branch_id', $branch->id)->whereKey($record->branch_license_id)->lockForUpdate()->firstOrFail();
                $this->assertUnchanged($license, $before);
                $number = $proposed['license_number'] ?? $license->license_number;
                if (BranchLicense::where('branch_id', $branch->id)->where('license_type', $license->license_type)->where('license_number', $number)->whereKeyNot($license->id)->exists()) {
                    throw ValidationException::withMessages(['license_number' => 'An identical licence already exists for this branch.']);
                }
                $license->fill($proposed);
                $license->save();
            } else abort(422, 'Unknown change type.');
            $record->update([
                'status' => 'approved', 'reviewed_by' => $request->user()->id,
                'review_reason' => $input['reason'] ?? null, 'reviewed_at' => now(),
            ]);
            $this->audit($request, $record, 'approved');
        });
        return redirect()->route($this->baseName().'show', $change)
            ->with('success', 'Change approved and applied. Existing order and payment records were not modified.');
    }
    public function reject(Request $request, int $change): RedirectResponse
    {
        $input = $request->validate(['reason' => ['required','string','min:5','max:1000']]);
        DB::transaction(function () use ($request, $change, $input): void {
            $record = BranchChangeRequest::whereKey($change)->lockForUpdate()->firstOrFail();
            $this->ensurePending($record);
            abort_if((int)$record->requested_by === (int)$request->user()->id, 403);
            $record->update(['status' => 'rejected', 'reviewed_by' => $request->user()->id,
                'review_reason' => $input['reason'], 'reviewed_at' => now()]);
            $this->audit($request, $record, 'rejected');
        });
        return redirect()->route($this->baseName().'show', $change)->with('success', 'Request rejected. Existing branch and licence values are untouched.');
    }
    private function assertAllowed(array $values, array $allowed): void
    {
        if (array_diff(array_keys($values), $allowed)) abort(422, 'Unexpected fields in request.');
    }
    private function ensurePending(BranchChangeRequest $record): void
    {
        if ($record->status !== 'pending') throw ValidationException::withMessages(['status' => 'This request has already been reviewed.']);
    }
    private function assertUnchanged(object $model, array $before): void
    {
        foreach ($before as $field => $value) {
            $current = $model->$field;
            if ($current instanceof \DateTimeInterface) $current = $current->format('Y-m-d');
            if (is_numeric($current) && is_numeric($value) && in_array($field, ['latitude','longitude'], true)) {
                if (number_format((float)$current, 7, '.', '') === number_format((float)$value, 7, '.', '')) continue;
            } elseif ((string)($current ?? '') === (string)($value ?? '')) continue;
            throw ValidationException::withMessages(['status' => 'Source data changed since the request. Manual review is required.']);
        }
    }
    private function audit(Request $request, BranchChangeRequest $record, string $action): void
    {
        DB::table('audit_logs')->insert([
            'actor_user_id' => $request->user()->id,
            'action' => 'branch.change.'.$action,
            'entity_type' => 'branch_change_request', 'entity_id' => $record->id,
            'new_values' => json_encode(['branch_id' => $record->branch_id, 'change_type' => $record->change_type,
                'status' => $action, 'fields' => array_keys($record->proposed_values)]),
            'reason' => $record->review_reason, 'ip_address' => $request->ip(), 'created_at' => now(),
        ]);
    }
}
