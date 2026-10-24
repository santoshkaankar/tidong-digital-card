<?php

namespace Tests\Feature;

use App\Models\Identity\AccountType;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

/** Runs only against verified digital_test_db, with all source + Grocery V1/V2 migrations. */
final class GroceryOnlineStoreTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::beginTransaction(); // Never RefreshDatabase or migrate:fresh on a real environment.
    }

    protected function tearDown(): void
    {
        if (DB::transactionLevel() > 0) DB::rollBack();
        parent::tearDown();
    }

    private function account(string $code): User
    {
        $user = User::factory()->create();
        $type = AccountType::firstOrCreate(['code' => $code], ['name' => ucfirst($code), 'is_active' => true]);
        $user->userAccountTypes()->create(['account_type_id' => $type->id, 'status' => 'active', 'activated_at' => now()]);
        return $user;
    }

    private function setupStore(): array
    {
        $owner = $this->account('vendor');
        $customer = $this->account('customer');
        $vendorId = DB::table('vendors')->insertGetId([
            'user_id' => $owner->id, 'vendor_code' => 'GS-'.Str::upper((string)Str::ulid()),
            'display_name' => 'Store owner', 'status' => 'approved', 'created_at' => now(), 'updated_at' => now(),
        ]);
        $businessId = DB::table('businesses')->insertGetId([
            'vendor_id' => $vendorId, 'business_type' => 'grocery', 'name' => 'Fresh Grocery',
            'slug' => 'grocery-'.Str::lower((string)Str::ulid()), 'status' => 'approved',
            'is_accepting_orders' => true, 'created_at' => now(), 'updated_at' => now(),
        ]);
        $branchId = DB::table('branches')->insertGetId([
            'business_id' => $businessId, 'name' => 'Main Store',
            'branch_code' => 'GS-'.Str::upper((string)Str::ulid()),
            'state' => 'Rajasthan', 'city' => 'Kota', 'status' => 'active',
            'is_accepting_orders' => true, 'created_at' => now(), 'updated_at' => now(),
        ]);
        foreach (['module','catalogue','pricing','inventory','online_store','online_orders'] as $flag) {
            DB::table('grocery_feature_flags')->updateOrInsert(['feature' => $flag],
                ['enabled' => true, 'created_at' => now(), 'updated_at' => now()]);
        }
        $productId = DB::table('grocery_products')->insertGetId([
            'business_id' => $businessId, 'branch_id' => $branchId,
            'sku' => 'RICE-'.Str::random(6), 'name' => 'Organic Rice', 'category' => 'Staples', 'unit' => 'kg',
            'gst_rate_bps' => 500, 'tax_inclusive' => true,
            'offline_mrp_paise' => 10000, 'offline_sale_paise' => 9000,
            'online_mrp_paise' => 12000, 'online_sale_paise' => 10500,
            'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
        ]);
        $lot = DB::table('grocery_stock_lots')->insertGetId([
            'business_id' => $businessId, 'branch_id' => $branchId, 'product_id' => $productId,
            'lot_code' => 'ONLINE-1', 'available_qty_milli' => 10000,
            'received_qty_milli' => 10000, 'is_sellable' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        return [$owner, $customer, $businessId, $branchId, $productId, $lot];
    }

    private function checkout(User $customer, int $business, int $branch, int $product, string $qty = '2'): void
    {
        $this->actingAs($customer)->post(route('grocery.shop.cart.add', [$business, $branch]), [
            'product_id' => $product, 'quantity' => $qty,
        ])->assertRedirect();
        $cart = session('tidong_grocery_cart_v2');
        $this->actingAs($customer)->post(route('grocery.shop.checkout.place', [$business, $branch]), [
            'checkout_token' => $cart['token'], 'contact_name' => 'Test Buyer',
            'contact_phone' => '9876543210', 'fulfilment_type' => 'pickup',
        ])->assertRedirect();
    }

    public function test_checkout_allocates_stock_and_saves_tax_snapshot_without_payment(): void
    {
        [$owner, $customer, $business, $branch, $product, $lot] = $this->setupStore();
        $this->get(route('grocery.shop.catalog', [$business, $branch]))->assertOk()->assertSee('Organic Rice');
        $this->checkout($customer, $business, $branch, $product);
        $order = DB::table('grocery_online_orders')->where('business_id', $business)->firstOrFail();
        $this->assertSame('pending', $order->status);
        $this->assertSame('unpaid', $order->payment_status);
        $this->assertSame(21000, (int) $order->total_paise);
        $this->assertSame(20000, (int) $order->taxable_paise);
        $this->assertSame(8000, (int) DB::table('grocery_stock_lots')->where('id', $lot)->value('available_qty_milli'));
        $this->assertDatabaseHas('grocery_online_order_items', ['order_id' => $order->id, 'unit_sale_paise' => 10500]);
        $this->assertDatabaseHas('grocery_stock_movements', ['movement_type' => 'online_order', 'delta_qty_milli' => -2000]);
        $this->actingAs($customer)->get(route('grocery.shop.orders.show', $order->id))->assertOk();
    }

    public function test_cancellation_restores_stock_once_and_preserves_all_history(): void
    {
        [$owner, $customer, $business, $branch, $product, $lot] = $this->setupStore();
        $this->checkout($customer, $business, $branch, $product);
        $order = DB::table('grocery_online_orders')->where('business_id', $business)->firstOrFail();
        $this->actingAs($customer)->post(route('grocery.shop.orders.cancel', $order->id))->assertRedirect();
        $this->assertSame('cancelled', DB::table('grocery_online_orders')->where('id', $order->id)->value('status'));
        $this->assertSame(10000, (int) DB::table('grocery_stock_lots')->where('id', $lot)->value('available_qty_milli'));
        $this->assertSame(2, DB::table('grocery_stock_movements')->where('product_id', $product)->count());
        $this->actingAs($customer)->post(route('grocery.shop.orders.cancel', $order->id))->assertSessionHasErrors('status');
        $this->assertSame(10000, (int) DB::table('grocery_stock_lots')->where('id', $lot)->value('available_qty_milli'));
    }

    public function test_paid_order_cannot_be_cancelled_or_rejected_without_refund_reconciliation(): void
    {
        [$owner, $customer, $business, $branch, $product, $lot] = $this->setupStore();
        $this->checkout($customer, $business, $branch, $product);
        $order = DB::table('grocery_online_orders')->where('business_id', $business)->firstOrFail();

        // Simulates a payment status recorded by an authorized payment integration.
        DB::table('grocery_online_orders')->where('id', $order->id)->update(['payment_status' => 'paid']);
        $this->actingAs($customer)->post(route('grocery.shop.orders.cancel', $order->id))
            ->assertSessionHasErrors('payment');
        $this->actingAs($owner)->post(route('vendor.grocery.online.status', [$business, $order->id]), [
            'status' => 'rejected',
        ])->assertSessionHasErrors('payment');

        $this->assertSame('pending', DB::table('grocery_online_orders')->where('id', $order->id)->value('status'));
        $this->assertSame(8000, (int) DB::table('grocery_stock_lots')->where('id', $lot)->value('available_qty_milli'));
        $this->assertSame(1, DB::table('grocery_stock_movements')->where('product_id', $product)->count());
        $this->assertSame(1, DB::table('grocery_online_order_events')->where('order_id', $order->id)->count());
    }

    public function test_vendor_processes_order_but_other_vendor_cannot(): void
    {
        [$owner, $customer, $business, $branch, $product] = $this->setupStore();
        $this->checkout($customer, $business, $branch, $product);
        $order = DB::table('grocery_online_orders')->where('business_id', $business)->firstOrFail();
        $outsider = $this->account('vendor');
        $this->actingAs($outsider)->get(route('vendor.grocery.online.show', [$business, $order->id]))->assertNotFound();
        foreach (['confirmed', 'packing', 'ready', 'completed'] as $next) {
            $this->actingAs($owner)->post(route('vendor.grocery.online.status', [$business, $order->id]), [
                'status' => $next, 'note' => 'Processing order',
            ])->assertRedirect();
        }
        $this->assertSame('completed', DB::table('grocery_online_orders')->where('id', $order->id)->value('status'));
        $this->assertSame(5, DB::table('grocery_online_order_events')->where('order_id', $order->id)->count());
    }

    public function test_admin_off_blocks_checkout_while_order_history_remains(): void
    {
        [$owner, $customer, $business, $branch, $product] = $this->setupStore();
        $this->checkout($customer, $business, $branch, $product);
        $this->actingAs($customer)->post(route('grocery.shop.cart.add', [$business, $branch]), [
            'product_id' => $product, 'quantity' => '1',
        ])->assertRedirect();
        $cart = session('tidong_grocery_cart_v2');
        DB::table('grocery_feature_flags')->where('feature', 'online_orders')->update(['enabled' => false]);
        $this->actingAs($customer)->post(route('grocery.shop.checkout.place', [$business, $branch]), [
            'checkout_token' => $cart['token'], 'contact_name' => 'Test Buyer',
            'contact_phone' => '9876543210', 'fulfilment_type' => 'pickup',
        ])->assertForbidden();
        $this->assertSame(1, DB::table('grocery_online_orders')->where('business_id', $business)->count());
    }

    public function test_customer_cannot_view_someone_elses_order(): void
    {
        [$owner, $customer, $business, $branch, $product] = $this->setupStore();
        $this->checkout($customer, $business, $branch, $product);
        $order = DB::table('grocery_online_orders')->where('business_id', $business)->firstOrFail();
        $another = $this->account('customer');
        $this->actingAs($another)->get(route('grocery.shop.orders.show', $order->id))->assertNotFound();
    }
}
