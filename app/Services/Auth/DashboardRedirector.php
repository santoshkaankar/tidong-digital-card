<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Services\Platform\ModuleRegistry;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Select a dashboard only after checking both active account type and current
 * role/partner eligibility. Never infer dashboard access from a role name alone.
 */
class DashboardRedirector
{
    public function routeFor(User $user): ?string
    {
        // A platform account with a missing/expired role must NOT silently fall
        // through to a customer or vendor dashboard.
        if ($this->hasAnyPlatformAccountRecord($user)) {
            if ($this->hasActiveAccountType($user, 'platform_admin')) {
                return $this->isSuperAdmin($user) ? 'home' : null;
            }
            if ($this->hasActiveAccountType($user, 'admin')) {
                return $this->hasValidManagedRole($user) ? 'staff-admin.home' : null;
            }
            if ($this->hasActiveAccountType($user, 'platform_employee')) {
                return $this->hasValidManagedRole($user) ? 'employee.home' : null;
            }
            return null;
        }

        if (
            $this->hasActiveAccountType($user, 'delivery')
            && $this->hasValidRole($user, 'delivery_partner')
            && app(ModuleRegistry::class)->isEnabled('delivery')
            && Schema::hasTable('delivery_partners')
            && DB::table('delivery_partners')->where('user_id', $user->id)
                ->where('status', 'active')->exists()
        ) {
            return 'delivery.home';
        }

        // A customer may intentionally become a vendor; valid vendor accounts
        // take priority without removing customer marketplace access.
        if ($this->hasActiveAccountType($user, 'vendor') && $user->vendor()->exists()) {
            return 'vendor.home';
        }

        if ($this->hasActiveAccountType($user, 'customer')) {
            return 'customer.home';
        }

        return null;
    }

    public function urlFor(User $user): ?string
    {
        $route = $this->routeFor($user);
        return $route ? route($route) : null;
    }

    public function hasActiveAccountType(User $user, string $code): bool
    {
        return $user->userAccountTypes()
            ->where('status', 'active')
            ->whereHas('accountType', fn ($query) => $query
                ->where('code', $code)->where('is_active', true))
            ->exists();
    }

    public function hasAnyPlatformAccountRecord(User $user): bool
    {
        return $user->userAccountTypes()
            ->whereHas('accountType', fn ($query) => $query
                ->whereIn('code', ['platform_admin', 'admin', 'platform_employee']))
            ->exists();
    }

    public function isSuperAdmin(User $user): bool
    {
        return $this->hasActiveAccountType($user, 'platform_admin')
            && $this->hasValidRole($user, 'super_admin');
    }

    public function hasValidManagedRole(User $user): bool
    {
        // A delivery/vendor-only or super-admin role is not a platform staff role.
        return $this->validPlatformAssignments($user)
            ->whereHas('role', fn ($query) => $query->whereNotIn('code', [
                'super_admin', 'delivery_partner', 'vendor_owner',
                'vendor_manager', 'vendor_employee',
            ]))->exists();
    }

    public function hasValidRole(User $user, string $code): bool
    {
        return $this->validPlatformAssignments($user)
            ->whereHas('role', fn ($query) => $query->where('code', $code))
            ->exists();
    }

    private function validPlatformAssignments(User $user)
    {
        return $user->roleAssignments()
            ->where('is_active', true)
            ->where('scope_type', 'platform')
            ->whereNull('scope_id')
            ->where(fn ($query) => $query->whereNull('starts_at')
                ->orWhere('starts_at', '<=', now()))
            ->where(fn ($query) => $query->whereNull('expires_at')
                ->orWhere('expires_at', '>', now()));
    }
}
