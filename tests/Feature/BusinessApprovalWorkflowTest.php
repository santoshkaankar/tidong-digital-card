<?php

namespace Tests\Feature;

use App\Models\Authorization\Permission;
use App\Models\Authorization\Role;
use App\Models\Identity\AccountType;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class BusinessApprovalWorkflowTest extends TestCase
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

    private function staff(array $permissions = [], bool $super = false, string $accountCode = 'admin'): User
    {
        $key = strtolower(Str::random(12));
        $user = User::factory()->create(['email' => $key.'@example.test']);
        $typeCode = $super ? 'platform_admin' : $accountCode;
        $type = AccountType::firstOrCreate(['code' => $typeCode], [
            'name' => $typeCode, 'is_active' => true,
        ]);
        $user->userAccountTypes()->create([
            'account_type_id' => $type->id,
            'status' => 'active',
            'activated_at' => now(),
        ]);
        $role = Role::create([
            'code' => 'test_business_'.strtolower(Str::random(20)),
            'name' => 'Business Test Staff',
            'is_system' => false,
        ]);
        foreach ($permissions as $code) {
            $permission = Permission::firstOrCreate(['code' => $code], [
                'name' => $code,
                'module' => 'businesses',
            ]);
            $role->permissions()->syncWithoutDetaching([$permission->id]);
        }
        $user->roleAssignments()->create([
            'role_id' => $role->id,
            'scope_type' => 'platform',
            'scope_id' => null,
            'starts_at' => now()->subMinute(),
            'is_active' => true,
        ]);
        if ($super) {
            $superRole = Role::firstOrCreate(['code' => 'super_admin'], [
                'name' => 'Super Admin', 'is_system' => true,
            ]);
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

    private function pendingBusiness(string $vendorStatus = 'approved'): array
    {
        $owner = User::factory()->create();
        $vendorId = DB::table('vendors')->insertGetId([
            'user_id' => $owner->id,
            'vendor_code' => 'TEST-'.Str::upper(Str::random(16)),
            'display_name' => 'Test Vendor',
            'status' => $vendorStatus,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $businessId = DB::table('businesses')->insertGetId([
            'vendor_id' => $vendorId,
            'name' => 'Test Restaurant',
            'business_type' => 'restaurant',
            'slug' => 'test-'.Str::lower((string) Str::ulid()),
            'status' => 'pending',
            'is_accepting_orders' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [$businessId, $vendorId];
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('staff-admin.businesses.index'))->assertRedirect(route('login'));
    }

    public function test_staff_without_business_view_permission_is_forbidden(): void
    {
        $this->actingAs($this->staff())->get(route('staff-admin.businesses.index'))->assertForbidden();
    }

    public function test_staff_with_business_view_permission_can_open_list_and_details(): void
    {
        [$business] = $this->pendingBusiness();
        $this->actingAs($this->staff(['businesses.view']))
            ->get(route('staff-admin.businesses.index'))->assertOk()->assertSee('Test Restaurant');
        $this->get(route('staff-admin.businesses.show', $business))->assertOk();
    }

    public function test_staff_cannot_approve_without_permission(): void
    {
        [$business] = $this->pendingBusiness();
        $this->actingAs($this->staff(['businesses.view']))
            ->post(route('staff-admin.businesses.approve', $business))->assertForbidden();
        $this->assertDatabaseHas('businesses', ['id' => $business, 'status' => 'pending']);
    }

    public function test_staff_cannot_reject_without_permission(): void
    {
        [$business] = $this->pendingBusiness();
        $this->actingAs($this->staff(['businesses.view']))
            ->post(route('staff-admin.businesses.reject', $business), [
                'reason' => 'Not ready',
            ])->assertForbidden();
        $this->assertDatabaseHas('businesses', ['id' => $business, 'status' => 'pending']);
    }

    public function test_approval_writes_audit_and_keeps_orders_and_branches_off(): void
    {
        [$business] = $this->pendingBusiness();
        $branch = DB::table('branches')->insertGetId([
            'business_id' => $business,
            'name' => 'Main Branch',
            'branch_code' => 'TEST-'.Str::upper(Str::random(12)),
            'status' => 'active',
            'is_accepting_orders' => true, // legacy inconsistent value must be disabled
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $actor = $this->staff(['businesses.view', 'businesses.approve']);

        $this->actingAs($actor)->post(route('staff-admin.businesses.approve', $business), [
            'reason' => 'Verified ownership',
        ])->assertRedirect(route('staff-admin.businesses.show', $business));

        $this->assertDatabaseHas('businesses', [
            'id' => $business, 'status' => 'approved', 'is_accepting_orders' => 0,
        ]);
        $this->assertDatabaseHas('branches', ['id' => $branch, 'is_accepting_orders' => 0]);
        $this->assertDatabaseHas('audit_logs', [
            'actor_user_id' => $actor->id,
            'action' => 'business.approved',
            'entity_type' => 'business',
            'entity_id' => $business,
            'reason' => 'Verified ownership',
        ]);
    }

    public function test_rejection_requires_reason_and_writes_audit(): void
    {
        [$business] = $this->pendingBusiness();
        $actor = $this->staff(['businesses.reject']);

        $this->actingAs($actor)->post(route('staff-admin.businesses.reject', $business))
            ->assertSessionHasErrors('reason');
        $this->assertDatabaseHas('businesses', ['id' => $business, 'status' => 'pending']);

        $this->post(route('staff-admin.businesses.reject', $business), [
            'reason' => 'Incomplete verification',
        ])->assertRedirect(route('staff-admin.businesses.show', $business));
        $this->assertDatabaseHas('businesses', ['id' => $business, 'status' => 'rejected']);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'business.rejected', 'entity_id' => $business,
            'reason' => 'Incomplete verification',
        ]);
    }

    public function test_business_cannot_be_approved_before_vendor_is_approved(): void
    {
        [$business] = $this->pendingBusiness('pending');
        $this->actingAs($this->staff(['businesses.approve']))
            ->post(route('staff-admin.businesses.approve', $business))
            ->assertSessionHasErrors('vendor');
        $this->assertDatabaseHas('businesses', ['id' => $business, 'status' => 'pending']);
    }

    public function test_decided_business_cannot_be_decided_twice(): void
    {
        [$business] = $this->pendingBusiness();
        $this->actingAs($this->staff(['businesses.approve', 'businesses.reject']))
            ->post(route('staff-admin.businesses.approve', $business))->assertRedirect();
        $this->post(route('staff-admin.businesses.reject', $business), [
            'reason' => 'Changing my mind',
        ])->assertSessionHasErrors('status');
        $this->assertDatabaseHas('businesses', ['id' => $business, 'status' => 'approved']);
    }

    public function test_super_admin_can_approve_business(): void
    {
        [$business] = $this->pendingBusiness();
        $this->actingAs($this->staff([], true))
            ->post(route('admin.businesses.approve', $business))
            ->assertRedirect(route('admin.businesses.show', $business));
        $this->assertDatabaseHas('businesses', ['id' => $business, 'status' => 'approved']);
    }

    public function test_employee_must_have_business_permission(): void
    {
        $this->actingAs($this->staff([], false, 'platform_employee'))
            ->get(route('employee.businesses.index'))->assertForbidden();
        $this->actingAs($this->staff(['businesses.view'], false, 'platform_employee'))
            ->get(route('employee.businesses.index'))->assertOk();
    }
}
