<?php

namespace Tests\Feature;

use App\Models\Authorization\Permission;
use App\Models\Authorization\Role;
use App\Models\Identity\AccountType;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PlatformVendorPermissionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::beginTransaction();
    }

    protected function tearDown(): void
    {
        if (DB::transactionLevel() > 0) {
            DB::rollBack();
        }
        parent::tearDown();
    }

    private function makeAdmin(array $permissions = [], bool $superAdmin = false): User
    {
        $key = strtolower(bin2hex(random_bytes(6)));
        $user = User::factory()->create(['email' => 'staff-'.$key.'@example.test']);
        $accountType = AccountType::firstOrCreate(
            ['code' => $superAdmin ? 'platform_admin' : 'admin'],
            ['name' => $superAdmin ? 'Platform Admin' : 'Admin', 'is_active' => true]
        );
        $user->userAccountTypes()->create([
            'account_type_id' => $accountType->id,
            'status' => 'active',
            'activated_at' => now(),
        ]);
        $role = Role::create([
            'code' => 'test_vendor_'.$key,
            'name' => 'Test Vendor Staff',
            'description' => 'Test role',
            'is_system' => false,
        ]);
        foreach ($permissions as $code) {
            $permission = Permission::firstOrCreate(
                ['code' => $code],
                ['name' => $code, 'module' => 'vendors', 'description' => 'Test']
            );
            $role->permissions()->syncWithoutDetaching([$permission->id]);
        }
        $user->roleAssignments()->create([
            'role_id' => $role->id,
            'scope_type' => 'platform',
            'scope_id' => null,
            'starts_at' => now()->subMinute(),
            'is_active' => true,
        ]);
        if ($superAdmin) {
            $superRole = Role::firstOrCreate(
                ['code' => 'super_admin'],
                ['name' => 'Super Admin', 'description' => 'Test', 'is_system' => true]
            );
            $user->roleAssignments()->create([
                'role_id' => $superRole->id,
                'scope_type' => 'platform',
                'scope_id' => null,
                'starts_at' => now()->subMinute(),
                'is_active' => true,
            ]);
        }
        return $user;
    }

    public function test_staff_without_vendor_view_permission_is_forbidden(): void
    {
        $user = $this->makeAdmin();
        $this->actingAs($user)->get(route('staff-admin.vendors.index'))->assertForbidden();
    }

    public function test_staff_with_vendor_view_permission_can_open_index(): void
    {
        $user = $this->makeAdmin(['vendors.view']);
        $this->actingAs($user)->get(route('staff-admin.vendors.index'))->assertOk();
    }

    public function test_staff_cannot_approve_without_approve_permission(): void
    {
        $user = $this->makeAdmin(['vendors.view']);
        $this->actingAs($user)->post('/staff-admin/vendors/999999999/approve')->assertForbidden();
    }

    public function test_super_admin_can_open_own_vendor_index(): void
    {
        $user = $this->makeAdmin([], true);
        $this->actingAs($user)->get(route('admin.vendors.index'))->assertOk();
    }
}
