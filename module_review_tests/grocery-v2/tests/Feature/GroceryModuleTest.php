<?php

namespace Tests\Feature;

use App\Models\Authorization\Role;
use App\Models\Identity\AccountType;
use App\Models\User;
use App\Services\Grocery\GroceryFeatures;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

/** Run only against digital_test_db after separate test DB migrations. No RefreshDatabase. */
final class GroceryModuleTest extends TestCase
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

    private function userWithAccount(string $account): User
    {
        $user = User::factory()->create();
        $type = AccountType::firstOrCreate(['code' => $account], ['name' => ucfirst($account), 'is_active' => true]);
        $user->userAccountTypes()->create([
            'account_type_id' => $type->id, 'status' => 'active', 'activated_at' => now(),
        ]);
        return $user;
    }

    private function vendorWorkspace(bool $orders = true): array
    {
        $owner = $this->userWithAccount('vendor');
        $vendor = DB::table('vendors')->insertGetId([
            'user_id' => $owner->id, 'vendor_code' => 'GR-'.Str::upper((string) Str::ulid()),
            'display_name' => 'Grocery Test Owner', 'status' => 'approved',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $business = DB::table('businesses')->insertGetId([
            'vendor_id' => $vendor, 'business_type' => 'grocery', 'name' => 'Test Grocery',
            'slug' => 'test-grocery-'.Str::lower((string) Str::ulid()),
            'status' => 'approved', 'is_accepting_orders' => $orders,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $branch = DB::table('branches')->insertGetId([
            'business_id' => $business, 'name' => 'Main Store',
            'branch_code' => 'GR-'.Str::upper((string) Str::ulid()), 'state' => 'Rajasthan',
            'status' => 'active', 'is_accepting_orders' => $orders,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        return [$owner, $business, $branch];
    }

    private function enable(array $features = ['module','catalogue','pricing','inventory','counter_sales','reports']): void
    {
        foreach ($features as $name) {
            DB::table('grocery_feature_flags')->updateOrInsert(['feature' => $name], [
                'enabled' => true, 'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    private function product(int $business, int $branch): int
    {
        return DB::table('grocery_products')->insertGetId([
            'business_id' => $business, 'branch_id' => $branch,
            'sku' => 'SUGAR-1', 'name' => 'Sugar', 'category' => 'Staples', 'unit' => 'kg',
            'gst_rate_bps' => 500, 'tax_inclusive' => true,
            'offline_mrp_paise' => 12500, 'offline_sale_paise' => 10500,
            'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    private function stock(User $owner, int $business, int $branch, int $product): void
    {
        $this->actingAs($owner)->post(route('vendor.grocery.stock.receive', ['business' => $business]), [
            'branch_id' => $branch, 'product_id' => $product, 'lot_code' => 'BATCH-A', 'quantity' => '10',
        ])->assertRedirect();
    }

    public function test_guest_must_log_in(): void
    {
        $this->get(route('vendor.grocery.index', 123))->assertRedirect(route('login'));
    }

    public function test_missing_global_flags_fail_closed(): void
    {
        [$user, $business] = $this->vendorWorkspace();
        DB::table('grocery_feature_flags')->where('feature', 'module')->update(['enabled' => false]);
        $this->actingAs($user)->get(route('vendor.grocery.index', $business))->assertForbidden();
    }

    public function test_vendor_cannot_access_other_vendor_business(): void
    {
        [$owner, $business] = $this->vendorWorkspace();
        [$outsider] = $this->vendorWorkspace();
        $this->enable();
        $this->actingAs($outsider)->get(route('vendor.grocery.index', $business))->assertNotFound();
    }

    public function test_global_disable_overrides_branch_enable(): void
    {
        [$owner, $business, $branch] = $this->vendorWorkspace();
        $this->enable();
        $this->actingAs($owner)->post(route('vendor.grocery.branch-settings', $business), [
            'branch_id' => $branch, 'feature' => 'inventory', 'enabled' => 1,
        ])->assertRedirect();
        DB::table('grocery_feature_flags')->where('feature', 'inventory')->update(['enabled' => false]);
        $this->assertFalse(GroceryFeatures::enabled('inventory', $branch));
        $this->actingAs($owner)->post(route('vendor.grocery.stock.receive', $business), [
            'branch_id' => $branch, 'product_id' => 1, 'lot_code' => 'B1', 'quantity' => '1',
        ])->assertForbidden();
    }

    public function test_vendor_branch_off_rejects_new_stock(): void
    {
        [$owner, $business, $branch] = $this->vendorWorkspace();
        $this->enable();
        $this->actingAs($owner)->post(route('vendor.grocery.branch-settings', $business), [
            'branch_id' => $branch, 'feature' => 'inventory', 'enabled' => 0,
        ])->assertRedirect();
        $this->assertFalse(GroceryFeatures::enabled('inventory', $branch));
        $this->actingAs($owner)->post(route('vendor.grocery.stock.receive', $business), [
            'branch_id' => $branch, 'product_id' => 1, 'lot_code' => 'B1', 'quantity' => '1',
        ])->assertForbidden();
    }

    public function test_product_lot_sale_snapshot_and_reversal_preserve_history(): void
    {
        [$owner, $business, $branch] = $this->vendorWorkspace();
        $this->enable();
        $product = $this->product($business, $branch);
        $this->stock($owner, $business, $branch, $product);
        $this->actingAs($owner)->post(route('vendor.grocery.sales.store', $business), [
            'branch_id' => $branch, 'supply_state' => 'Rajasthan',
            'items' => [['product_id' => $product, 'quantity' => '2']],
        ])->assertRedirect();
        $sale = DB::table('grocery_sales')->where('business_id', $business)->firstOrFail();
        $this->assertSame(21000, (int) $sale->total_paise);
        $this->assertSame(20000, (int) $sale->taxable_paise);
        $this->assertSame(500, (int) $sale->cgst_paise);
        $this->assertSame(500, (int) $sale->sgst_paise);
        $lot = DB::table('grocery_stock_lots')->where('product_id', $product)->firstOrFail();
        $this->assertSame(8000, (int) $lot->available_qty_milli);
        $this->actingAs($owner)->patch(route('vendor.grocery.products.price', ['business' => $business, 'product' => $product]), [
            'branch_id' => $branch, 'offline_mrp' => '150', 'offline_sale' => '120',
        ])->assertRedirect();
        $this->assertDatabaseHas('grocery_sale_items', [
            'sale_id' => $sale->id, 'unit_sale_paise' => 10500, 'gst_rate_bps_snapshot' => 500,
            'discount_paise' => 4000, 'discount_rate_bps_snapshot' => 1600,
        ]);
        $this->actingAs($owner)->post(route('vendor.grocery.sales.reverse', ['business' => $business, 'sale' => $sale->id]), [
            'branch_id' => $branch, 'reason' => 'Customer returned purchase',
        ])->assertRedirect();
        $this->assertDatabaseHas('grocery_sales', ['id' => $sale->id, 'status' => 'reversed']);
        $this->assertSame(10000, (int) DB::table('grocery_stock_lots')->where('id', $lot->id)->value('available_qty_milli'));
        $this->assertSame(3, DB::table('grocery_stock_movements')->where('product_id', $product)->count());
        $this->assertDatabaseHas('grocery_sale_reversals', ['sale_id' => $sale->id]);
        $this->actingAs($owner)->post(route('vendor.grocery.sales.reverse', ['business' => $business, 'sale' => $sale->id]), [
            'branch_id' => $branch, 'reason' => 'Attempt a second reversal',
        ])->assertSessionHasErrors('reason');
    }

    public function test_billing_never_runs_on_unapproved_or_disabled_orders(): void
    {
        [$owner, $business, $branch] = $this->vendorWorkspace(false);
        $this->enable();
        $product = $this->product($business, $branch);
        $this->stock($owner, $business, $branch, $product);
        $this->actingAs($owner)->post(route('vendor.grocery.sales.store', $business), [
            'branch_id' => $branch, 'supply_state' => 'Rajasthan',
            'items' => [['product_id' => $product, 'quantity' => '1']],
        ])->assertForbidden();
        $this->assertSame(0, DB::table('grocery_sales')->where('business_id', $business)->count());
    }

    public function test_only_super_admin_can_manage_global_feature_switches(): void
    {
        [$vendor] = $this->vendorWorkspace();
        $this->actingAs($vendor)->get(route('admin.grocery.features.index'))->assertForbidden();
        $admin = $this->userWithAccount('platform_admin');
        $role = Role::firstOrCreate(['code' => 'super_admin'], ['name' => 'Super Admin', 'is_system' => true]);
        $admin->roleAssignments()->create([
            'role_id' => $role->id, 'scope_type' => 'platform', 'scope_id' => null,
            'starts_at' => now()->subMinute(), 'is_active' => true,
        ]);
        $this->actingAs($admin)->post(route('admin.grocery.features.update', 'module'), ['enabled' => 1])->assertRedirect();
        $this->assertTrue(GroceryFeatures::global('module'));
        $this->assertDatabaseHas('audit_logs', ['action' => 'grocery.feature.changed']);
    }

    public function test_sale_rejects_insufficient_stock_without_partial_inventory_change(): void
    {
        [$owner, $business, $branch] = $this->vendorWorkspace();
        $this->enable();
        $product = $this->product($business, $branch);
        $this->stock($owner, $business, $branch, $product);
        $this->actingAs($owner)->post(route('vendor.grocery.sales.store', $business), [
            'branch_id' => $branch, 'supply_state' => 'Rajasthan',
            'items' => [['product_id' => $product, 'quantity' => '11']],
        ])->assertSessionHasErrors('items');
        $this->assertSame(10000, (int)DB::table('grocery_stock_lots')->where('product_id', $product)->value('available_qty_milli'));
        $this->assertSame(0, DB::table('grocery_sales')->where('business_id', $business)->count());
    }

    public function test_admin_can_stop_new_sales_without_deleting_existing_sale(): void
    {
        [$owner, $business, $branch] = $this->vendorWorkspace();
        $this->enable();
        $product = $this->product($business, $branch);
        $this->stock($owner, $business, $branch, $product);
        $payload = [
            'branch_id' => $branch, 'supply_state' => 'Rajasthan',
            'items' => [['product_id' => $product, 'quantity' => '1']],
        ];
        $this->actingAs($owner)->post(route('vendor.grocery.sales.store', $business), $payload)->assertRedirect();
        DB::table('grocery_feature_flags')->where('feature', 'counter_sales')->update(['enabled' => false]);
        $this->actingAs($owner)->post(route('vendor.grocery.sales.store', $business), $payload)->assertForbidden();
        $this->assertSame(1, DB::table('grocery_sales')->where('business_id', $business)->count());
        $this->assertSame(9000, (int)DB::table('grocery_stock_lots')->where('product_id', $product)->value('available_qty_milli'));
    }
}
