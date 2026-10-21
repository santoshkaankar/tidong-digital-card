<?php

namespace App\Services\Delivery;

use Illuminate\Support\Facades\DB;

/** Fail closed: only firm owner or a currently authorized business/branch employee. */
class VendorDeliveryAccess
{
    public function allows(int $actorId, int $businessId, ?int $branchId, string $permission): bool
    {
        if (! in_array($permission, ['delivery.jobs.manage', 'delivery.partners.manage'], true)) {
            return false;
        }
        // Platform identities NEVER acquire business privileges, including via accidental dual membership.
        $isPlatform = DB::table('user_account_types as uat')
            ->join('account_types as at', 'at.id', '=', 'uat.account_type_id')
            ->where('uat.user_id', $actorId)->where('uat.status', 'active')
            ->whereIn('at.code', ['admin', 'platform_admin', 'platform_employee'])->exists();
        if ($isPlatform) return false;

        $business = DB::table('businesses as b')
            ->join('vendors as v', 'v.id', '=', 'b.vendor_id')
            ->where('b.id', $businessId)->where('b.status', 'approved')
            ->where('v.status', 'approved')->whereNull('b.deleted_at')->whereNull('v.deleted_at')
            ->first(['b.id', 'v.user_id']);
        if (! $business) return false;
        if ($branchId !== null && ! DB::table('branches')->where('id', $branchId)
            ->where('business_id', $businessId)->where('status', 'active')->whereNull('deleted_at')->exists()) {
            return false;
        }

        if ((int) $business->user_id === $actorId) {
            return DB::table('user_account_types as uat')
                ->join('account_types as at', 'at.id', '=', 'uat.account_type_id')
                ->where('uat.user_id', $actorId)->where('uat.status', 'active')
                ->where('at.code', 'vendor')->where('at.is_active', true)->exists();
        }

        // Employees need BOTH an active scoped employment assignment AND an
        // explicit individual grant by the owning Vendor. No global role inheritance.
        $assigned = DB::table('employee_assignments as ea')
            ->join('roles as r', 'r.id', '=', 'ea.role_id')
            ->where('ea.user_id', $actorId)->where('ea.business_id', $businessId)
            ->where('ea.is_active', true)->where('ea.starts_at', '<=', now())
            ->where(fn ($q) => $q->whereNull('ea.ends_at')->orWhere('ea.ends_at', '>', now()))
            ->when($branchId !== null,
                fn ($q) => $q->where(fn ($scope) => $scope->whereNull('ea.branch_id')->orWhere('ea.branch_id', $branchId)),
                fn ($q) => $q->whereNull('ea.branch_id'))
            ->whereIn('r.code', ['vendor_manager', 'vendor_employee', 'vendor_owner'])->exists();
        if (! $assigned) return false;

        $grants = DB::table('vendor_delivery_permissions')
            ->where('user_id', $actorId)->where('business_id', $businessId)
            ->where('permission_code', $permission);
        if ($branchId !== null) {
            $specific = (clone $grants)->where('branch_scope_key', 'branch:'.$branchId)->first();
            if ($specific) return (bool) $specific->is_enabled; // Scoped explicit deny overrides wider grants.
        }
        $wide = $grants->where('branch_scope_key', 'business')->first();
        return $wide && (bool) $wide->is_enabled;

    }

    public function assert(int $actorId, int $businessId, ?int $branchId, string $permission): void
    {
        abort_unless($this->allows($actorId, $businessId, $branchId, $permission), 403,
            'Only the business owner or an explicitly authorized business employee can perform this action.');
    }

    public function isOwner(int $actorId, int $businessId): bool
    {
        $business = DB::table('businesses as b')->join('vendors as v', 'v.id', '=', 'b.vendor_id')
            ->where('b.id', $businessId)->where('v.user_id', $actorId)
            ->whereNull('b.deleted_at')->whereNull('v.deleted_at')->first();
        return $business !== null && $this->allows($actorId, $businessId, null, 'delivery.jobs.manage');
    }

    public function grant(int $ownerId, int $employeeId, int $businessId, ?int $branchId, string $permission, bool $enabled): void
    {
        abort_unless($this->isOwner($ownerId, $businessId), 403);
        abort_unless(in_array($permission, ['delivery.jobs.manage', 'delivery.partners.manage'], true), 422);
        if ($permission === 'delivery.partners.manage' && $branchId !== null) {
            abort(422, 'Partner management permission must be business-wide.');
        }
        if ($branchId !== null) {
            abort_unless(DB::table('branches')->where('id', $branchId)
                ->where('business_id', $businessId)->whereNull('deleted_at')->exists(), 422);
        }
        $staffAccount = DB::table('user_account_types as uat')
            ->join('account_types as at', 'at.id', '=', 'uat.account_type_id')
            ->where('uat.user_id', $employeeId)->where('uat.status', 'active')
            ->whereIn('at.code', ['platform_admin', 'admin', 'platform_employee'])->exists();
        abort_if($staffAccount, 403);
        $assignment = DB::table('employee_assignments')->where('user_id', $employeeId)
            ->where('business_id', $businessId)->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>', now()))
            ->when($branchId !== null,
                fn ($q) => $q->where(fn ($scope) => $scope->whereNull('branch_id')->orWhere('branch_id', $branchId)),
                fn ($q) => $q->whereNull('branch_id'))->exists();
        abort_unless($assignment, 403, 'Employee must be actively assigned to this business/branch.');
        DB::transaction(function () use ($ownerId, $employeeId, $businessId, $branchId, $permission, $enabled): void {
            $record = DB::table('vendor_delivery_permissions')->where('user_id', $employeeId)
                ->where('business_id', $businessId)->where('permission_code', $permission)
                ->where('branch_scope_key', $branchId === null ? 'business' : 'branch:'.$branchId)
                ->lockForUpdate()->first();
            if ($record) {
                DB::table('vendor_delivery_permissions')->where('id', $record->id)->update([
                    'is_enabled' => $enabled, 'updated_by' => $ownerId, 'updated_at' => now(),
                ]);
            } else {
                DB::table('vendor_delivery_permissions')->insert([
                    'user_id' => $employeeId, 'business_id' => $businessId,
                    'branch_id' => $branchId,
                    'branch_scope_key' => $branchId === null ? 'business' : 'branch:'.$branchId,
                    'permission_code' => $permission,
                    'is_enabled' => $enabled, 'updated_by' => $ownerId,
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }
            DB::table('audit_logs')->insert([
                'actor_user_id' => $ownerId, 'action' => 'vendor.delivery.permission.changed',
                'entity_type' => 'business', 'entity_id' => $businessId,
                'old_values' => json_encode(['enabled' => $record ? (bool)$record->is_enabled : null]),
                'new_values' => json_encode(['employee_id' => $employeeId, 'branch_id' => $branchId,
                    'permission' => $permission, 'enabled' => $enabled]), 'created_at' => now(),
            ]);
        });
    }

    public function assertJob(int $actorId, int $jobId): object
    {
        $job = DB::table('delivery_jobs')->where('id', $jobId)->first();
        abort_unless($job, 404);
        abort_unless($this->allows($actorId, (int)$job->business_id, (int)$job->branch_id,
            'delivery.jobs.manage'), 404);
        return $job;
    }
}
