<?php

namespace Tests\Feature;

use App\Models\Authorization\Role;
use App\Models\Identity\AccountType;
use App\Models\User;
use App\Models\Vendor\Business;
use App\Models\Vendor\Vendor;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class VendorOnboardingFlowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::beginTransaction();

        foreach (['customer', 'vendor', 'admin'] as $code) {
            AccountType::firstOrCreate(['code' => $code], [
                'name' => ucfirst($code), 'is_active' => true,
            ]);
        }
        Role::firstOrCreate(['code' => 'vendor_owner'], [
            'name' => 'Vendor Owner', 'is_system' => true,
        ]);
    }

    protected function tearDown(): void
    {
        if (DB::transactionLevel() > 0) {
            DB::rollBack();
        }
        parent::tearDown();
    }

    private function customer(): User
    {
        $user = User::factory()->create();
        $type = AccountType::where('code', 'customer')->firstOrFail();
        $user->userAccountTypes()->create([
            'account_type_id' => $type->id,
            'status' => 'active',
            'activated_at' => now(),
        ]);

        return $user;
    }

    private function approvedVendor(): array
    {
        $user = $this->customer();
        $this->actingAs($user)->post(route('vendor.apply.store'), [
            'display_name' => 'Sample Vendor',
        ])->assertRedirect(route('vendor.home'));
        $vendor = Vendor::where('user_id', $user->id)->firstOrFail();
        $vendor->update(['status' => 'approved', 'approved_at' => now()]);

        return [$user, $vendor];
    }

    public function test_guest_cannot_open_vendor_area(): void
    {
        $this->get(route('vendor.home'))->assertRedirect(route('login'));
    }

    public function test_customer_can_apply_and_application_is_pending_and_audited(): void
    {
        $user = $this->customer();
        $this->actingAs($user)->post(route('vendor.apply.store'), [
            'display_name' => 'North Restaurant Group',
        ])->assertRedirect(route('vendor.home'));

        $vendor = Vendor::where('user_id', $user->id)->firstOrFail();
        $this->assertSame('pending', $vendor->status);
        $this->assertDatabaseHas('user_account_types', [
            'user_id' => $user->id,
            'account_type_id' => AccountType::where('code', 'vendor')->firstOrFail()->id,
            'status' => 'active',
        ]);
        $this->assertDatabaseHas('user_role_assignments', [
            'user_id' => $user->id,
            'scope_type' => 'vendor',
            'scope_id' => $vendor->id,
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'vendor.applied',
            'entity_type' => 'vendor',
            'entity_id' => $vendor->id,
        ]);
        $this->get(route('vendor.home'))->assertOk();
    }

    public function test_duplicate_application_is_rejected(): void
    {
        $user = $this->customer();
        $this->actingAs($user)->post(route('vendor.apply.store'), [
            'display_name' => 'First Application',
        ])->assertRedirect(route('vendor.home'));

        $this->post(route('vendor.apply.store'), [
            'display_name' => 'Second Application',
        ])->assertSessionHasErrors('display_name');

        $this->assertSame(1, Vendor::where('user_id', $user->id)->count());
    }

    public function test_platform_admin_cannot_apply_as_vendor(): void
    {
        $user = $this->customer();
        $type = AccountType::where('code', 'admin')->firstOrFail();
        $user->userAccountTypes()->create([
            'account_type_id' => $type->id,
            'status' => 'active',
        ]);

        $this->actingAs($user)->post(route('vendor.apply.store'), [
            'display_name' => 'Disallowed Vendor',
        ])->assertForbidden();
        $this->assertDatabaseMissing('vendors', ['user_id' => $user->id]);
    }

    public function test_pending_vendor_cannot_create_business(): void
    {
        $user = $this->customer();
        $this->actingAs($user)->post(route('vendor.apply.store'), [
            'display_name' => 'Pending Vendor',
        ]);

        $before = Business::count();

        $this->post(route('vendor.businesses.store'), [
            'name' => 'Coffee Store',
            'business_type' => 'restaurant',
        ])->assertSessionHasErrors('vendor');

        $this->assertSame($before, Business::count());
    }

    public function test_approved_vendor_can_create_pending_business_with_orders_disabled(): void
    {
        [$user, $vendor] = $this->approvedVendor();

        $this->actingAs($user)->post(route('vendor.businesses.store'), [
            'name' => 'Coffee Store',
            'business_type' => 'restaurant',
        ])->assertRedirect(route('vendor.home'));

        $business = Business::where('vendor_id', $vendor->id)->firstOrFail();
        $this->assertSame('pending', $business->status);
        $this->assertFalse($business->is_accepting_orders);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'business.created', 'entity_id' => $business->id,
        ]);
    }

    public function test_approved_vendor_can_add_branch_but_cannot_take_orders(): void
    {
        [$user, $vendor] = $this->approvedVendor();
        $business = $vendor->businesses()->create([
            'name' => 'Shop', 'business_type' => 'retail',
            'slug' => 'sample-shop-'.uniqid(), 'status' => 'pending',
            'is_accepting_orders' => false,
        ]);

        $this->actingAs($user)->post(route('vendor.branches.store', $business->id), [
            'name' => 'Main Outlet', 'city' => 'Kota',
        ])->assertRedirect(route('vendor.home'));

        $branch = $business->branches()->firstOrFail();
        $this->assertSame('active', $branch->status);
        $this->assertFalse($branch->is_accepting_orders);
        $this->assertSame('Kota', $branch->city);
    }

    public function test_vendor_cannot_create_branch_for_another_vendor(): void
    {
        [$userA, $vendorA] = $this->approvedVendor();
        [$userB, $vendorB] = $this->approvedVendor();
        $businessB = $vendorB->businesses()->create([
            'name' => 'Vendor B', 'business_type' => 'retail',
            'slug' => 'vendor-b-'.uniqid(), 'status' => 'approved',
            'is_accepting_orders' => false,
        ]);

        $this->actingAs($userA)->post(route('vendor.branches.store', $businessB->id), [
            'name' => 'Wrong Owner Branch',
        ])->assertNotFound();

        $this->assertSame(0, $businessB->branches()->count());
    }
}
