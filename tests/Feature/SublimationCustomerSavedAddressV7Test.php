<?php

namespace Tests\Feature;

use App\Models\Identity\AccountType;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

/** Ensures V7 order hardening preserves the installed Customer saved-address experience. */
class SublimationCustomerSavedAddressV7Test extends TestCase
{
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

    private function userWithRole(string $role): User
    {
        $user = User::factory()->create();
        $type = AccountType::firstOrCreate(['code' => $role], ['name' => ucfirst($role), 'is_active' => true]);
        $user->userAccountTypes()->create([
            'account_type_id' => $type->id, 'status' => 'active', 'activated_at' => now(),
        ]);
        return $user;
    }

    private function studio(): array
    {
        $owner = $this->userWithRole('vendor');
        $vendor = DB::table('vendors')->insertGetId([
            'user_id' => $owner->id, 'vendor_code' => 'S7-'.Str::random(18),
            'display_name' => 'Address Studio', 'status' => 'approved',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $business = DB::table('businesses')->insertGetId([
            'vendor_id' => $vendor, 'name' => 'Address Studio',
            'slug' => 's7-'.Str::lower((string) Str::ulid()),
            'business_type' => 'sublimation', 'status' => 'approved',
            'is_accepting_orders' => true, 'created_at' => now(), 'updated_at' => now(),
        ]);
        $branch = DB::table('branches')->insertGetId([
            'business_id' => $business, 'name' => 'Address Branch',
            'branch_code' => 'S7-'.Str::random(18), 'status' => 'active',
            'is_accepting_orders' => true, 'created_at' => now(), 'updated_at' => now(),
        ]);
        $product = DB::table('sublimation_products')->insertGetId([
            'business_id' => $business, 'branch_id' => $branch,
            'name' => 'Address Mug', 'category' => 'Mug',
            'blank_cost_paise' => 2000, 'print_cost_paise' => 1000,
            'packing_cost_paise' => 300, 'other_cost_paise' => 0,
            'sale_price_paise' => 9000, 'is_active' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        return [$business, $product];
    }

    private function address(User $user): int
    {
        return DB::table('customer_addresses')->insertGetId([
            'user_id' => $user->id, 'label' => 'Home',
            'recipient_name' => 'Customer Name', 'phone' => '9999999999',
            'address_line_1' => 'Test Road 10', 'address_line_2' => null,
            'city' => 'Jaipur', 'state' => 'Rajasthan', 'postal_code' => '302001',
            'country_code' => 'IN', 'is_default' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    private function checkout(int $product, array $values = []): array
    {
        return array_replace([
            'request_token' => (string) Str::uuid(), 'product_id' => $product,
            'customer_name' => 'Customer', 'customer_mobile' => '9999999999',
            'quantity' => 2, 'fulfillment_method' => 'delivery',
        ], $values);
    }

    public function test_own_saved_address_submits_once_and_store_navigation_is_preserved(): void
    {
        [$business, $product] = $this->studio();
        $buyer = $this->userWithRole('customer');
        $address = $this->address($buyer);
        $this->actingAs($buyer)->get(route('sublimation.shop', ['business' => $business]))
            ->assertOk()->assertSee('Address Mug');
        $this->get(route('sublimation.products.show', $product))
            ->assertOk()->assertSee('Saved delivery address')->assertSee('This Studio')
            ->assertSee('request_token');
        $payload = $this->checkout($product, ['delivery_address_id' => $address]);
        $url = route('sublimation.orders.place');
        $this->post($url, $payload)->assertSessionHasNoErrors();
        $this->post($url, $payload)->assertSessionHasNoErrors();
        $this->assertSame(1, DB::table('sublimation_orders')->where('customer_user_id', $buyer->id)->count());
        $order = DB::table('sublimation_orders')->where('customer_user_id', $buyer->id)->firstOrFail();
        $this->assertStringContainsString('Test Road 10', (string) $order->delivery_address);
    }

    public function test_other_customers_saved_address_is_rejected(): void
    {
        [, $product] = $this->studio();
        $attacker = $this->userWithRole('customer');
        $other = $this->userWithRole('customer');
        $address = $this->address($other);
        $this->actingAs($attacker)
            ->post(route('sublimation.orders.place'), $this->checkout($product, [
                'delivery_address_id' => $address, 'delivery_address' => 'Attacker input',
            ]))->assertSessionHasErrors('delivery_address_id');
        $this->assertSame(0, DB::table('sublimation_orders')->where('customer_user_id', $attacker->id)->count());
    }
}
