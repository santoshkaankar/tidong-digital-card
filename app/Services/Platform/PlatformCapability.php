<?php

namespace App\Services\Platform;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PlatformCapability
{
    private function account(User $user, string $type): bool
    {
        return $user->userAccountTypes()->where('status', 'active')
            ->whereHas('accountType', fn ($q) => $q->where('code', $type)->where('is_active', true))
            ->exists();
    }

    private function role(User $user, string $code): bool
    {
        return $user->roleAssignments()->where('is_active', true)
            ->where('scope_type', 'platform')->whereNull('scope_id')
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->whereHas('role', fn ($q) => $q->where('code', $code))->exists();
    }

    public function isSuperAdmin(User $user): bool
    {
        return $this->account($user, 'platform_admin') && $this->role($user, 'super_admin');
    }

    public function isAdmin(User $user): bool
    {
        return $this->account($user, 'admin') && $this->role($user, 'admin');
    }

    public function isEmployee(User $user): bool
    {
        if (! $this->account($user, 'admin') && ! $this->account($user, 'platform_employee')) {
            return false;
        }
        // Require a genuine, active managed role. An account type alone grants nothing.
        return $user->roleAssignments()->where('is_active', true)
            ->where('scope_type', 'platform')->whereNull('scope_id')
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->whereHas('role', fn ($q) => $q->whereNotIn('code', ['super_admin', 'admin', 'delivery_partner', 'vendor_owner', 'vendor_manager', 'vendor_employee']))
            ->exists();
    }

    public function isSupervisor(User $user): bool
    {
        return $this->isSuperAdmin($user) || $this->isAdmin($user);
    }

    public function allows(User $user, string $permission): bool
    {
        if (! in_array($permission, self::CAPABILITIES, true)) return false;
        if ($this->isSuperAdmin($user)) return true;
        if ($this->isAdmin($user)) return in_array($permission, self::CAPABILITIES, true);
        if (! $this->isEmployee($user)) return false;
        // Individual grants (not the whole admin_employee role) determine platform employee rights.
        if (Schema::hasTable('platform_employee_permissions')) {
            $grant = DB::table('platform_employee_permissions')
                ->where('user_id', $user->id)->where('permission_code', $permission)->first();
            if ($grant) return (bool) $grant->is_enabled; // Explicit revocation beats inherited role permissions.
        }
        // Existing role-based permissions remain valid for specialized employees.
        return $user->hasPlatformPermission($permission);
    }

    public const CAPABILITIES = [
        'modules.view', 'modules.register', 'modules.toggle',
        'features.toggle', 'delivery.diagnostics.view', 'delivery.partners.approve',
    ];
}
