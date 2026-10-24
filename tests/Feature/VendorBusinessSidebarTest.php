<?php

namespace Tests\Feature;

use App\Models\Authorization\Role;
use App\Models\Identity\AccountType;
use App\Models\User;
use App\Models\Vendor\Vendor;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class VendorBusinessSidebarTest extends TestCase
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
        if (DB::transactionLevel() > 0) DB::rollBack();
        parent::tearDown();
    }

    private function vendor(): array
    {
        $user = User::factory()->create();
        $type = AccountType::where('code', 'customer')->firstOrFail();
        $user->userAccountTypes()->create([
            'account_type_id' => $type->id, 'status' => 'active', 'activated_at' => now(),
        ]);
        $this->actingAs($user)->post(route('vendor.apply.store'), ['display_name' => 'Studio Vendor'])
            ->assertRedirect(route('vendor.home'));
        $vendor = Vendor::where('user_id', $user->id)->firstOrFail();
        $vendor->update(['status' => 'approved', 'approved_at' => now()]);
        return [$user, $vendor];
    }

    private function business(Vendor $vendor, string $type): object
    {
        return $vendor->businesses()->create([
            'name' => strtoupper($type).' Workshop',
            'business_type' => $type,
            'slug' => $type.'-'.uniqid(),
            'status' => 'approved',
            'is_accepting_orders' => false,
        ]);
    }

    public function test_guest_cannot_open_business_workspace(): void
    {
        $this->get(route('vendor.business.workspace', 1))->assertRedirect(route('login'));
    }

    public function test_vendor_can_switch_between_relevant_business_menus(): void
    {
        [$user, $vendor] = $this->vendor();
        $restaurant = $this->business($vendor, 'restaurant');
        $studio = $this->business($vendor, 'sublimation');

        $this->actingAs($user)->get(route('vendor.business.workspace', $restaurant->id))
            ->assertOk()->assertSee('Restaurant & Dining')->assertDontSee('Studio Overview');
        $this->actingAs($user)->get(route('vendor.business.workspace', $studio->id))
            ->assertOk()->assertSee('Sublimation Studio')->assertSee('Open Sublimation Studio')
            ->assertDontSee('Counter POS');
    }

    public function test_one_vendor_cannot_open_another_vendors_business(): void
    {
        [$userA, $vendorA] = $this->vendor();
        [$userB, $vendorB] = $this->vendor();
        $foreign = $this->business($vendorB, 'sublimation');
        $this->actingAs($userA)->get(route('vendor.business.workspace', $foreign->id))->assertNotFound();
    }

    public function test_branch_selector_rejects_branch_from_another_business(): void
    {
        [$user, $vendor] = $this->vendor();
        $first = $this->business($vendor, 'restaurant');
        $other = $this->business($vendor, 'sublimation');
        $foreignBranch = $other->branches()->create([
            'name' => 'Other branch', 'branch_code' => 'BR-'.uniqid(), 'status' => 'active',
            'is_accepting_orders' => false,
        ]);
        $this->actingAs($user)->get(route('vendor.business.workspace', [
            'business' => $first->id, 'branch' => $foreignBranch->id,
        ]))->assertNotFound();
    }

    public function test_vendor_home_renders_sidebar_and_business_dashboard_link(): void
    {
        [$user, $vendor] = $this->vendor();
        $studio = $this->business($vendor, 'sublimation');
        $this->actingAs($user)->get(route('vendor.home'))
            ->assertOk()->assertSee('Vendor Main Menu')->assertSee('Open Business Dashboard')
            ->assertSee(route('vendor.business.workspace', $studio->id));
    }

    public function test_vendor_dashboard_has_no_customer_dashboard_button(): void
    {
        [$user, $vendor] = $this->vendor();
        $this->business($vendor, 'restaurant');

        $this->actingAs($user)->get(route('vendor.home'))
            ->assertOk()
            ->assertSee('Vendor Dashboard')
            ->assertSee('My Businesses')
            ->assertDontSee('Customer Dashboard')
            ->assertSee('Total Outlets');
    }
}
