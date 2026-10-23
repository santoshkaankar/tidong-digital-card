<?php

namespace Tests\Feature;

use App\Models\Authorization\Role;
use App\Models\Identity\AccountType;
use App\Models\User;
use App\Services\Delivery\DeliveryIdentity;
use App\Services\Delivery\DeliveryService;
use App\Services\Delivery\VendorDeliveryAccess;
use App\Services\Platform\ModuleRegistry;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

/** Additive, isolated DB-transaction tests; DO NOT run on main or production DB. */
class DeliveryIdentityIsolationRegressionTest extends TestCase
{
    use \Tests\Support\ApprovesDeliveryPartnerForTest;
    protected function setUp(): void
    {
        parent::setUp();
        DB::beginTransaction();
    }

    protected function tearDown(): void
    {
        if (DB::transactionLevel() > 0) DB::rollBack();
        parent::tearDown();
    }

    private function userWithIdentity(string $account, string $role): User
    {
        $user = User::factory()->create(['email' => bin2hex(random_bytes(8)).'@example.test']);
        $type = AccountType::firstOrCreate(['code' => $account], ['name' => $account, 'is_active' => true]);
        $type->update(['is_active' => true]);
        $user->userAccountTypes()->create(['account_type_id' => $type->id,
            'status' => 'active', 'activated_at' => now()]);
        $this->addRole($user, $role);
        return $user;
    }

    private function addRole(User $user, string $roleCode): void
    {
        $role = Role::firstOrCreate(['code' => $roleCode],
            ['name' => $roleCode, 'is_system' => true]);
        $user->roleAssignments()->create(['role_id' => $role->id,
            'scope_type' => 'platform', 'scope_id' => null,
            'is_active' => true, 'starts_at' => now()->subMinute()]);
    }

    private function enable(): void
    {
        $admin = $this->userWithIdentity('platform_admin', 'super_admin');
        $modules = app(ModuleRegistry::class);
        if (! DB::table('platform_modules')->where('code', 'delivery')->exists()) {
            $modules->register('delivery', (int)$admin->id);
        }
        $modules->setEnabled('delivery', true, (int)$admin->id);
    }

    /** Existing Vendor identity is reused across multiple approved businesses. */
    private function branchForVendor(User $owner): array
    {
        $vendorId = DB::table('vendors')->where('user_id', $owner->id)->value('id');
        if (! $vendorId) {
            $vendorId = DB::table('vendors')->insertGetId([
                'user_id' => $owner->id, 'vendor_code' => 'DS-'.bin2hex(random_bytes(5)),
                'display_name' => 'Delivery Security Vendor', 'status' => 'approved',
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
        $businessId = DB::table('businesses')->insertGetId([
            'vendor_id' => $vendorId, 'business_type' => 'restaurant',
            'name' => 'Security Test', 'slug' => 'delivery-'.bin2hex(random_bytes(5)),
            'status' => 'approved', 'created_at' => now(), 'updated_at' => now(),
        ]);
        $branchId = DB::table('branches')->insertGetId([
            'business_id' => $businessId, 'name' => 'Test Branch',
            'branch_code' => 'D'.bin2hex(random_bytes(5)), 'status' => 'active',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        return [$businessId, $branchId];
    }

    private function activate(DeliveryService $service, User $owner, int $businessId, User $rider): int
    {
        AccountType::firstOrCreate(['code' => 'delivery'], ['name' => 'Delivery', 'is_active' => true]);
        Role::firstOrCreate(['code' => 'delivery_partner'], ['name' => 'Delivery Partner', 'is_system' => true]);
        $partnerId = $service->inviteToBusiness($businessId, (int)$rider->id,
            'bike', null, (int)$owner->id);
        $service->verifyForBusiness($businessId, $partnerId, (int)$owner->id);
        $this->approvePlatformDeliveryForTest($partnerId);
        $service->setRosterStatus($businessId, $partnerId, 'active', (int)$owner->id);
        $service->setAvailability((int)$rider->id,true);
        return $partnerId;
    }

    private function job(DeliveryService $service, User $owner, int $businessId, int $branchId, int $partnerId): int
    {
        return $service->createJob([
            'business_id' => $businessId, 'branch_id' => $branchId, 'partner_id' => $partnerId,
            'recipient_name' => 'Test Recipient', 'recipient_phone' => '9999999999',
            'dropoff_address' => 'Test delivery address, Jaipur, Rajasthan',
        ], (int)$owner->id);
    }

    public function test_direct_service_call_cannot_progress_after_delivery_identity_is_revoked(): void
    {
        $this->enable();
        $owner = $this->userWithIdentity('vendor', 'vendor_owner');
        [$business, $branch] = $this->branchForVendor($owner);
        $rider = User::factory()->create();
        $service = app(DeliveryService::class);
        $partnerId = $this->activate($service, $owner, $business, $rider);
        $jobId = $this->job($service, $owner, $business, $branch, $partnerId);
        DB::table('user_account_types')->where('user_id', $rider->id)
            ->where('account_type_id', DB::table('account_types')->where('code', 'delivery')->value('id'))
            ->update(['status' => 'inactive']);
        $this->assertFalse(app(DeliveryIdentity::class)->hasActiveRiderCredentials((int)$rider->id));
        try {
            $service->progress($jobId, (int)$rider->id, 'accepted', true);
            $this->fail('A revoked rider cannot progress a delivery via direct service calls.');
        } catch (HttpException $e) {
            $this->assertSame(403, $e->getStatusCode());
        }
        $this->assertDatabaseHas('delivery_jobs', ['id' => $jobId, 'status' => 'assigned']);
    }

    public function test_business_roster_revocation_blocks_direct_rider_progress(): void
    {
        $this->enable();
        $owner = $this->userWithIdentity('vendor', 'vendor_owner');
        [$business, $branch] = $this->branchForVendor($owner);
        $rider = User::factory()->create();
        $service = app(DeliveryService::class);
        $partnerId = $this->activate($service, $owner, $business, $rider);
        $jobId = $this->job($service, $owner, $business, $branch, $partnerId);
        // Simulate an external compliance/system-level roster revocation.
        DB::table('delivery_business_partners')->where('business_id', $business)
            ->where('partner_id', $partnerId)->update(['status' => 'suspended']);
        try {
            $service->progress($jobId, (int)$rider->id, 'accepted', true);
            $this->fail('A revoked business roster must not permit status changes.');
        } catch (HttpException $e) {
            $this->assertSame(403, $e->getStatusCode());
        }
        $this->assertDatabaseHas('delivery_jobs', ['id' => $jobId, 'status' => 'assigned']);
    }

    public function test_platform_role_added_later_blocks_rider_and_vendor_identity(): void
    {
        $this->enable();
        $owner = $this->userWithIdentity('vendor', 'vendor_owner');
        [$business, $branch] = $this->branchForVendor($owner);
        $rider = User::factory()->create();
        $service = app(DeliveryService::class);
        $partnerId = $this->activate($service, $owner, $business, $rider);
        $jobId = $this->job($service, $owner, $business, $branch, $partnerId);
        $this->addRole($rider, 'admin_employee');
        $this->assertFalse(app(DeliveryIdentity::class)->hasActiveRiderCredentials((int)$rider->id));
        try {
            $service->progress($jobId, (int)$rider->id, 'accepted', true);
            $this->fail('Platform staff must not retain delivery permissions.');
        } catch (HttpException $e) {
            $this->assertSame(403, $e->getStatusCode());
        }
        $this->addRole($owner, 'operations_manager');
        $this->assertFalse(app(VendorDeliveryAccess::class)->allows(
            (int)$owner->id, $business, $branch, 'delivery.jobs.manage'));
    }

    public function test_reactivation_cannot_promote_a_now_ineligible_partner(): void
    {
        $this->enable();
        $owner = $this->userWithIdentity('vendor', 'vendor_owner');
        [$business] = $this->branchForVendor($owner);
        $rider = User::factory()->create();
        $service = app(DeliveryService::class);
        $partnerId = $this->activate($service, $owner, $business, $rider);
        $this->addRole($rider, 'support_manager');
        try {
            $service->setRosterStatus($business, $partnerId, 'active', (int)$owner->id);
            $this->fail('A partner who became platform staff must not be re-approved.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('partner', $e->errors());
        }
    }

    public function test_second_business_approval_preserves_existing_rider_offline_setting(): void
    {
        $this->enable();
        $owner = $this->userWithIdentity('vendor', 'vendor_owner');
        [$business] = $this->branchForVendor($owner);
        [$otherBusiness] = $this->branchForVendor($owner);
        $rider = User::factory()->create();
        $service = app(DeliveryService::class);
        $partnerId = $this->activate($service, $owner, $business, $rider);
        $service->setAvailability((int)$rider->id, false);
        $sameId = $service->inviteToBusiness($otherBusiness, (int)$rider->id, 'bike', null, (int)$owner->id);
        $this->assertSame($partnerId, $sameId);
        $service->verifyForBusiness($otherBusiness, $partnerId, (int)$owner->id);
        $service->setRosterStatus($otherBusiness, $partnerId, 'active', (int)$owner->id);
        $this->assertDatabaseHas('delivery_partners', [
            'id' => $partnerId, 'status' => 'active', 'is_available' => false,
        ]);
    }

    public function test_platform_staff_cannot_be_invited_via_preexisting_rider_record(): void
    {
        $this->enable();
        $owner = $this->userWithIdentity('vendor', 'vendor_owner');
        [$business] = $this->branchForVendor($owner);
        [$otherBusiness] = $this->branchForVendor($owner);
        $rider = User::factory()->create();
        $service = app(DeliveryService::class);
        $this->activate($service, $owner, $business, $rider);
        $this->addRole($rider, 'accounts_employee');
        try {
            $service->inviteToBusiness($otherBusiness, (int)$rider->id,
                'bike', null, (int)$owner->id);
            $this->fail('Platform staff must not be invited using a stale rider record.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('user_id', $e->errors());
        }
    }

    public function test_reassignment_cannot_target_a_partner_with_revoked_delivery_credentials(): void
    {
        $this->enable();
        $owner = $this->userWithIdentity('vendor', 'vendor_owner');
        [$business, $branch] = $this->branchForVendor($owner);
        $service = app(DeliveryService::class);
        $firstRider = User::factory()->create();
        $replacement = User::factory()->create();
        $firstId = $this->activate($service, $owner, $business, $firstRider);
        $replacementId = $this->activate($service, $owner, $business, $replacement);
        $jobId = $this->job($service, $owner, $business, $branch, $firstId);
        // The global partner row remains active while credentials are revoked.
        DB::table('user_account_types')->where('user_id', $replacement->id)
            ->where('account_type_id', DB::table('account_types')->where('code', 'delivery')->value('id'))
            ->update(['status' => 'inactive']);
        try {
            $service->reassign($jobId, $replacementId, (int)$owner->id);
            $this->fail('Reassignment must require a currently authorized rider.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('partner_id', $e->errors());
        }
        $this->assertDatabaseHas('delivery_jobs', [
            'id' => $jobId, 'partner_id' => $firstId, 'status' => 'assigned',
        ]);
    }

    public function test_vendor_cannot_grant_delivery_staff_access_to_platform_role_account(): void
    {
        $this->enable();
        $owner = $this->userWithIdentity('vendor', 'vendor_owner');
        [$business, $branch] = $this->branchForVendor($owner);
        $employee = User::factory()->create();
        $role = Role::firstOrCreate(['code' => 'vendor_employee'],
            ['name' => 'Vendor Employee', 'is_system' => true]);
        DB::table('employee_assignments')->insert([
            'user_id' => $employee->id, 'role_id' => $role->id,
            'business_id' => $business, 'branch_id' => $branch,
            'assigned_by' => $owner->id, 'starts_at' => now()->subMinute(),
            'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
        ]);
        $this->addRole($employee, 'approval_employee');
        try {
            app(VendorDeliveryAccess::class)->grant((int)$owner->id, (int)$employee->id,
                $business, $branch, 'delivery.jobs.manage', true);
            $this->fail('Platform personnel must not receive Vendor delivery grants.');
        } catch (HttpException $e) {
            $this->assertSame(403, $e->getStatusCode());
        }
        $this->assertDatabaseMissing('vendor_delivery_permissions', [
            'user_id' => $employee->id, 'business_id' => $business,
            'permission_code' => 'delivery.jobs.manage',
        ]);
    }
}
