<?php

namespace App\Services\Delivery;

use Illuminate\Support\Facades\DB;

/** Shared fail-closed identity checks for every delivery entry point. */
class DeliveryIdentity
{
    /** Reject platform employees (including active platform-scoped staff roles). */
    public function isPlatformStaff(int $userId): bool
    {
        $hasAccount = DB::table('user_account_types as uat')
            ->join('account_types as at', 'at.id', '=', 'uat.account_type_id')
            ->where('uat.user_id', $userId)
            ->whereIn('at.code', ['admin', 'platform_admin', 'platform_employee'])
            ->exists();

        return $hasAccount || DB::table('user_role_assignments as ura')
            ->join('roles as r', 'r.id', '=', 'ura.role_id')
            ->where('ura.user_id', $userId)
            ->where('ura.scope_type', 'platform')
            ->where('ura.is_active', true)
            ->whereIn('r.code', [
                'super_admin', 'admin', 'admin_employee', 'operations_manager',
                'operations_employee', 'approval_manager', 'approval_employee',
                'support_manager', 'support_employee', 'delivery_manager',
                'accounts_manager', 'accounts_employee',
            ])->exists();
    }

    /** Existing vendor or platform identities may not be reactivated as riders. */
    public function isIneligibleRider(int $userId): bool
    {
        if ($this->isPlatformStaff($userId)) return true;

        return DB::table('user_account_types as uat')
            ->join('account_types as at', 'at.id', '=', 'uat.account_type_id')
            ->where('uat.user_id', $userId)->where('at.code', 'vendor')->exists();
    }

    /** The platform step is not vendor approval and cannot be inferred from status=active. */
    public function isPlatformApproved(int $userId): bool
    {
        $partner = DB::table('delivery_partners')->where('user_id', $userId)->first();
        if (! $partner || ! $partner->approved_by || ! $partner->approved_at) return false;
        return DB::table('audit_logs')->where('entity_type', 'delivery_partner')
            ->where('entity_id', $partner->id)
            ->where('action', 'delivery.partner.platform_approved')
            ->where('actor_user_id', $partner->approved_by)->exists();
    }

    /** All rider checks include platform approval; business roster checked at job boundary. */
    public function hasActiveRiderCredentials(int $userId): bool
    {
        if ($this->isIneligibleRider($userId)) return false;

        $account = DB::table('user_account_types as uat')
            ->join('account_types as at', 'at.id', '=', 'uat.account_type_id')
            ->where('uat.user_id', $userId)->where('uat.status', 'active')
            ->where('at.code', 'delivery')->where('at.is_active', true)->exists();
        if (! $account) return false;

        $role = DB::table('user_role_assignments as ura')
            ->join('roles as r', 'r.id', '=', 'ura.role_id')
            ->where('ura.user_id', $userId)->where('ura.is_active', true)
            ->where('ura.scope_type', 'platform')->whereNull('ura.scope_id')
            ->where('r.code', 'delivery_partner')
            ->where(fn ($query) => $query->whereNull('ura.starts_at')->orWhere('ura.starts_at', '<=', now()))
            ->where(fn ($query) => $query->whereNull('ura.expires_at')->orWhere('ura.expires_at', '>', now()))
            ->exists();
        if (! $role) return false;

        // Platform approval and business authorization are separate steps.
        // Never trust a vendor-created 'active' flag as platform approval.
        return DB::table('delivery_partners')->where('user_id', $userId)
            ->where('status', 'active')->exists() && $this->isPlatformApproved($userId);
    }
}
