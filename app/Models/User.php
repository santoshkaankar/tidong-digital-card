<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Authorization\EmployeeAssignment;
use App\Models\Authorization\Role;
use App\Models\Authorization\UserRoleAssignment;
use App\Models\Identity\AccountType;
use App\Models\Identity\UserAccountType;
use App\Models\Vendor\Vendor;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    #[Fillable([
        'name',
        'email',
        'password',
    ])]
    #[Hidden([
        'password',
        'remember_token',
    ])]
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * User account types.
     */
    public function accountTypes()
    {
        return $this->belongsToMany(
            AccountType::class,
            'user_account_types',
            'user_id',
            'account_type_id'
        )->withPivot([
            'status',
            'activated_at',
        ])->withTimestamps();
    }

    /**
     * User account-type assignment records.
     */
    public function userAccountTypes()
    {
        return $this->hasMany(UserAccountType::class);
    }

    /**
     * User role assignments.
     */
    public function roleAssignments()
    {
        return $this->hasMany(UserRoleAssignment::class);
    }

    /**
     * Assigned roles.
     */
    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            'user_role_assignments',
            'user_id',
            'role_id'
        )->withPivot([
            'scope_type',
            'scope_id',
            'assigned_by',
            'starts_at',
            'expires_at',
            'is_active',
        ])->withTimestamps();
    }

    /**
     * Employee assignment history.
     */
    public function employeeAssignments()
    {
        return $this->hasMany(EmployeeAssignment::class);
    }

    /**
     * Vendor profile.
     */
    public function vendor()
    {
        return $this->hasOne(Vendor::class);
    }

    /**
     * Users who assigned roles or employees.
     */
    public function assignmentsMade()
    {
        return $this->hasMany(
            UserRoleAssignment::class,
            'assigned_by'
        );
    }

    /**
     * Users who approved vendors.
     */
    public function approvedVendors()
    {
        return $this->hasMany(
            Vendor::class,
            'approved_by'
        );
    }

    /**
     * Users who assigned employees.
     */
    public function employeeAssignmentsMade()
    {
        return $this->hasMany(
            EmployeeAssignment::class,
            'assigned_by'
        );
    }

    /**
     * Check an active platform role permission, honoring the Super Admin
     * only when it also has the active platform_admin account type.
     */
    /** Private customer profile, address book and audit relationships. */
    public function customerProfile() { return $this->hasOne(\App\Models\Customer\CustomerProfile::class); }
    public function customerAddresses() { return $this->hasMany(\App\Models\Customer\CustomerAddress::class); }
    public function customerAccountAuditLogs() { return $this->hasMany(\App\Models\Customer\CustomerAccountAuditLog::class); }
    public function hasPlatformPermission(string $permissionCode): bool
    {
        $validRoles = $this->roleAssignments()
            ->where('is_active', true)
            ->where('scope_type', 'platform')
            ->whereNull('scope_id')
            ->where(function ($query) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });

        // Super Admin cannot bypass permissions on role name alone.
        $isPlatformAdmin = $this->userAccountTypes()
            ->where('status', 'active')
            ->whereHas('accountType', function ($query) {
                $query->where('code', 'platform_admin')
                    ->where('is_active', true);
            })
            ->exists();

        if ($isPlatformAdmin && (clone $validRoles)
            ->whereHas('role', fn ($query) => $query->where('code', 'super_admin'))
            ->exists()) {
            return true;
        }

        return $validRoles
            ->whereHas('role', function ($query) use ($permissionCode) {
                $query->where('code', '!=', 'super_admin')
                    ->whereHas('permissions', function ($permissions) use ($permissionCode) {
                        $permissions->where('code', $permissionCode);
                    });
            })
            ->exists();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}