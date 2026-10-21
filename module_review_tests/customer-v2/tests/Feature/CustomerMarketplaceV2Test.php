<?php

namespace Tests\Feature;

use App\Models\Identity\AccountType;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class CustomerMarketplaceV2Test extends TestCase
{
    use DatabaseTransactions;

    // Unlike V1, these tests never run migrate:fresh / RefreshDatabase.
    protected function setUp(): void
    {
        parent::setUp();
        $driver = config('database.default');
        $database = config("database.connections.{$driver}.database");
        $safe = in_array($driver, ['mysql', 'pgsql'], true) && $database === 'digital_test_db';

        if (! $safe) {
            throw new \RuntimeException('STOP: Customer V2 tests require pre-migrated digital_test_db only.');
        }
    }

    private function customer(): User
    {
        $type = AccountType::firstOrCreate(['code' => 'customer'], [
            'name' => 'Customer', 'is_active' => true,
        ]);
        $user = User::factory()->create();
        $user->userAccountTypes()->create([
            'account_type_id' => $type->id, 'status' => 'active', 'activated_at' => now(),
        ]);
        return $user;
    }

    private function createStore(
        string $name,
        string $type = 'sublimation',
        string $businessStatus = 'approved',
        string $vendorStatus = 'approved',
        string $branchStatus = 'active',
        bool $businessAccepting = true,
        bool $branchAccepting = true
    ): int {
        $user = User::factory()->create();
        $vendorId = DB::table('vendors')->insertGetId([
            'user_id' => $user->id,
            'vendor_code' => 'CUSTV2-'.Str::upper(Str::random(16)),
            'display_name' => $name.' Vendor',
            'status' => $vendorStatus,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $businessId = DB::table('businesses')->insertGetId([
            'vendor_id' => $vendorId,
            'business_type' => $type,
            'name' => $name,
            'slug' => 'v2-'.Str::lower((string) Str::ulid()),
            'status' => $businessStatus,
            'is_accepting_orders' => $businessAccepting,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('branches')->insert([
            'business_id' => $businessId,
            'name' => 'Main Branch',
            'branch_code' => 'CUST-'.Str::upper(Str::random(12)),
            'status' => $branchStatus,
            'is_accepting_orders' => $branchAccepting,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        return $businessId;
    }

    public function test_customer_home_and_explore_use_app_navigation(): void
    {
        $this->actingAs($this->customer())->get(route('customer.home'))
            ->assertOk()
            ->assertSee('images/brand/tidong-official-logo.jpg')
            ->assertSee('Explore businesses')
            ->assertSee('My Orders');
        $this->get(route('customer.explore.index'))->assertOk()->assertSee('Sublimation &amp; Gifts', false);
        $this->get(route('customer.orders.index'))->assertOk()->assertSee('not linked to your login');
    }

    public function test_only_approved_and_explicitly_active_stores_are_public(): void
    {
        $this->createStore('Visible Gift Studio');
        $this->createStore('Unapproved Studio', 'sublimation', 'pending');
        $this->createStore('Pending Vendor Studio', 'sublimation', 'approved', 'pending');
        $this->createStore('Paused Studio', 'sublimation', 'approved', 'approved', 'active', false);
        $this->createStore('Closed Branch Studio', 'sublimation', 'approved', 'approved', 'active', true, false);
        $this->createStore('Inactive Branch Studio', 'sublimation', 'approved', 'approved', 'inactive');

        $this->actingAs($this->customer())->get(route('customer.explore.service', 'sublimation'))
            ->assertOk()
            ->assertSee('Visible Gift Studio')
            ->assertDontSee('Unapproved Studio')
            ->assertDontSee('Pending Vendor Studio')
            ->assertDontSee('Paused Studio')
            ->assertDontSee('Closed Branch Studio')
            ->assertDontSee('Inactive Branch Studio');
        $this->get(route('customer.home'))->assertOk()->assertSee('Explore stores');
    }

    public function test_cross_category_businesses_are_not_disclosed(): void
    {
        $this->createStore('My Test Restaurant', 'restaurant');
        $this->createStore('My Gift Shop', 'sublimation');
        $this->actingAs($this->customer())->get(route('customer.explore.service', 'sublimation'))
            ->assertOk()->assertSee('My Gift Shop')->assertDontSee('My Test Restaurant');
        $this->get(route('customer.explore.service', 'restaurant'))
            ->assertOk()->assertSee('My Test Restaurant')->assertDontSee('My Gift Shop');
    }

    public function test_unintegrated_or_unknown_service_is_not_browsable(): void
    {
        $this->createStore('Not yet integrated Grocery', 'grocery');
        $this->actingAs($this->customer())->get(route('customer.explore.service', 'grocery'))->assertNotFound();
        $this->get(route('customer.explore.service', 'wallet'))->assertNotFound();
        $this->get(route('customer.explore.service', 'made_up'))->assertNotFound();
        $this->get(route('customer.explore.index'))->assertOk()->assertSee('Not available yet');
    }

    public function test_search_limits_input_and_hides_nonmatching_stores(): void
    {
        $this->createStore('Sunrise Gift Studio');
        $this->createStore('Moonlight Gift Studio');
        $this->actingAs($this->customer())->get(route('customer.explore.service', [
            'service' => 'sublimation', 'q' => 'Sunrise',
        ]))->assertOk()->assertSee('Sunrise Gift Studio')->assertDontSee('Moonlight Gift Studio');
        $this->get(route('customer.explore.service', [
            'service' => 'sublimation', 'q' => str_repeat('a', 71),
        ]))->assertSessionHasErrors('q');
    }

    public function test_all_customer_discovery_routes_require_active_customer(): void
    {
        $this->get(route('customer.explore.index'))->assertRedirect(route('login'));
        $this->get(route('customer.orders.index'))->assertRedirect(route('login'));
        $user = $this->customer();
        $user->userAccountTypes()->update(['status' => 'inactive']);
        $this->actingAs($user)->get(route('customer.explore.index'))->assertForbidden();
        $this->get(route('customer.explore.service', 'sublimation'))->assertForbidden();
        $this->get(route('customer.orders.index'))->assertForbidden();
    }

    public function test_module_disable_flag_also_blocks_discovery(): void
    {
        config()->set('tidong_customer.enabled', false);
        $this->actingAs($this->customer())->get(route('customer.home'))->assertForbidden();
        $this->get(route('customer.explore.index'))->assertForbidden();
        $this->get(route('customer.orders.index'))->assertForbidden();
    }
}
