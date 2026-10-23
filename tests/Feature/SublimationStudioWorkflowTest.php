<?php

namespace Tests\Feature;

use App\Models\Identity\AccountType;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class SublimationStudioWorkflowTest extends TestCase
{
    protected function setUp(): void { parent::setUp(); DB::beginTransaction(); }
    protected function tearDown(): void { if (DB::transactionLevel() > 0) DB::rollBack(); parent::tearDown(); }

    private function studio(string $businessType = 'sublimation'): array
    {
        $user = User::factory()->create();
        $type = AccountType::firstOrCreate(['code' => 'vendor'], ['name' => 'Vendor', 'is_active' => true]);
        $user->userAccountTypes()->create(['account_type_id' => $type->id, 'status' => 'active', 'activated_at' => now()]);
        $vendor = DB::table('vendors')->insertGetId([
            'user_id' => $user->id, 'vendor_code' => 'SUB-'.Str::random(18),
            'display_name' => 'Gift Studio', 'status' => 'approved',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $business = DB::table('businesses')->insertGetId([
            'vendor_id' => $vendor, 'name' => 'Tidong Gifts',
            'slug' => 'sub-'.Str::lower((string) Str::ulid()),
            'business_type' => $businessType, 'status' => 'approved',
            'is_accepting_orders' => false, 'created_at' => now(), 'updated_at' => now(),
        ]);
        $branch = DB::table('branches')->insertGetId([
            'business_id' => $business, 'name' => 'Main Studio',
            'branch_code' => 'BS-'.Str::random(18), 'status' => 'active',
            'is_accepting_orders' => false, 'created_at' => now(), 'updated_at' => now(),
        ]);

        return [$user, $business, $branch];
    }

    private function addProduct(int $business, int $branch): array
    {
        return [
            'branch_id' => $branch, 'name' => 'Custom Mug', 'category' => 'Mug',
            'blank_cost' => '50.00', 'print_cost' => '10.50',
            'packing_cost' => '5.00', 'other_cost' => '4.50', 'sale_price' => '180.00',
        ];
    }

    public function test_studio_is_private_and_only_for_its_business_type(): void
    {
        [$owner, $business] = $this->studio();
        $this->get(route('vendor.sublimation.index', $business))->assertRedirect(route('login'));
        $this->actingAs($owner)->get(route('vendor.sublimation.index', $business))->assertOk();
        $vendorId = (int) DB::table('vendors')->where('user_id', $owner->id)->value('id');
        $restaurantBusiness = DB::table('businesses')->insertGetId([
            'vendor_id' => $vendorId, 'name' => 'Not Sublimation',
            'slug' => 'not-sub-'.Str::lower((string) Str::ulid()),
            'business_type' => 'restaurant', 'status' => 'approved',
            'is_accepting_orders' => false, 'created_at' => now(), 'updated_at' => now(),
        ]);
        $this->get(route('vendor.sublimation.index', $restaurantBusiness))->assertNotFound();
    }

    public function test_other_vendor_cannot_view_or_create_products(): void
    {
        [$owner, $business, $branch] = $this->studio();
        [$other] = $this->studio();
        $this->actingAs($other)->get(route('vendor.sublimation.index', $business))->assertNotFound();
        $this->post(route('vendor.sublimation.products.store', $business), $this->addProduct($business, $branch))->assertNotFound();
        $this->assertDatabaseMissing('sublimation_products', ['business_id' => $business]);
        $this->actingAs($owner)->post(route('vendor.sublimation.products.store', $business), $this->addProduct($business, $branch))->assertSessionHasNoErrors();
        $this->assertDatabaseHas('sublimation_products', ['business_id' => $business, 'branch_id' => $branch, 'sale_price_paise' => 18000]);
    }

    public function test_orders_capture_price_and_cost_snapshots_and_keep_audit(): void
    {
        [$owner, $business, $branch] = $this->studio();
        $this->actingAs($owner)->post(route('vendor.sublimation.products.store', $business), $this->addProduct($business, $branch))
            ->assertSessionHasNoErrors();
        $product = DB::table('sublimation_products')->where('business_id', $business)->firstOrFail();
        $this->post(route('vendor.sublimation.orders.store', $business), [
            'request_token' => (string) Str::uuid(), 'branch_id' => $branch, 'product_id' => $product->id,
            'customer_name' => 'Gift Customer', 'quantity' => 2,
            'design_instructions' => 'Birthday name',
        ])->assertSessionHasNoErrors();
        $order = DB::table('sublimation_orders')->where('business_id', $business)->firstOrFail();
        $this->assertSame(7000, (int) $order->unit_cost_paise);
        $this->assertSame(18000, (int) $order->unit_sale_paise);
        $this->assertSame(36000, (int) $order->total_sale_paise);
        $this->assertSame('design_pending', $order->status);
        $this->assertDatabaseHas('audit_logs', ['action' => 'sublimation.order.created', 'entity_id' => $order->id]);
        $this->post(route('vendor.sublimation.orders.status', ['business' => $business, 'order' => $order->id]),
            ['status' => 'approved', 'approval_note' => 'Customer approved'])->assertSessionHasErrors('status');
        $this->post(route('vendor.sublimation.orders.status', ['business' => $business, 'order' => $order->id]),
            ['status' => 'approval_pending'])->assertSessionHasNoErrors();
        $this->post(route('vendor.sublimation.orders.status', ['business' => $business, 'order' => $order->id]),
            ['status' => 'approved', 'approval_note' => 'Approved via WhatsApp'])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('sublimation_orders', [
            'id' => $order->id, 'status' => 'approved', 'total_sale_paise' => 36000,
        ]);
        $this->assertDatabaseHas('audit_logs', ['action' => 'sublimation.order.status_changed', 'entity_id' => $order->id]);
    }

    public function test_samples_seed_in_one_branch_only_and_never_duplicate(): void
    {
        [$owner, $business, $branch] = $this->studio();
        [, $otherBusiness] = $this->studio();
        $this->artisan('studio:seed-samples', ['business' => $business, 'branch' => $branch, '--dry-run' => true])
            ->assertExitCode(0);
        $this->assertSame(0, DB::table('sublimation_products')->where('business_id', $business)->count());
        $this->artisan('studio:seed-samples', ['business' => $business, 'branch' => $branch])->assertExitCode(0);
        $this->artisan('studio:seed-samples', ['business' => $business, 'branch' => $branch])->assertExitCode(0);
        $this->assertSame(12, DB::table('sublimation_products')->where('business_id', $business)->count());
        $this->assertSame(0, DB::table('sublimation_products')->where('business_id', $otherBusiness)->count());
        $this->assertSame(0, DB::table('sublimation_products')->where('business_id', $business)->where('is_active', true)->count());
        $this->assertSame(0, DB::table('sublimation_orders')->where('business_id', $business)->count());
        $this->assertDatabaseHas('sublimation_products', [
            'business_id' => $business, 'branch_id' => $branch, 'name' => 'Custom Logo Mouse Pad', 'sale_price_paise' => 0,
        ]);
    }

    public function test_unpriced_sample_requires_owner_to_set_price_before_order(): void
    {
        [$owner, $business, $branch] = $this->studio();
        $this->artisan('studio:seed-samples', ['business' => $business, 'branch' => $branch])->assertExitCode(0);
        $product = DB::table('sublimation_products')->where('business_id', $business)
            ->where('name', 'Custom Logo Mouse Pad')->firstOrFail();
        $this->actingAs($owner)->post(route('vendor.sublimation.orders.store', $business), [
            'request_token' => (string) Str::uuid(), 'branch_id' => $branch, 'product_id' => $product->id,
            'customer_name' => 'Test Customer', 'quantity' => 1,
        ])->assertNotFound();
        $payload = $this->addProduct($business, $branch);
        $payload['name'] = $product->name;
        $payload['category'] = $product->category;
        $payload['is_active'] = '1';
        $this->patch(route('vendor.sublimation.products.update', ['business' => $business, 'product' => $product->id]),
            $payload)->assertSessionHasNoErrors();
        $this->assertDatabaseHas('sublimation_products', [
            'id' => $product->id, 'is_active' => true, 'sale_price_paise' => 18000,
        ]);
        $this->post(route('vendor.sublimation.orders.store', $business), [
            'request_token' => (string) Str::uuid(), 'branch_id' => $branch, 'product_id' => $product->id,
            'customer_name' => 'Test Customer', 'quantity' => 1,
        ])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('sublimation_orders', [
            'business_id' => $business, 'product_id' => $product->id, 'unit_sale_paise' => 18000,
        ]);
    }

    public function test_cannot_use_product_from_another_branch_or_vendor(): void
    {
        [$owner, $business, $branch] = $this->studio();
        $this->actingAs($owner)->post(route('vendor.sublimation.products.store', $business), $this->addProduct($business, $branch));
        $product = DB::table('sublimation_products')->where('business_id', $business)->firstOrFail();
        [, $otherBusiness, $otherBranch] = $this->studio();
        $this->post(route('vendor.sublimation.orders.store', $business), [
            'request_token' => (string) Str::uuid(), 'branch_id' => $otherBranch, 'product_id' => $product->id,
            'customer_name' => 'Test', 'quantity' => 1,
        ])->assertNotFound();
        $this->assertDatabaseMissing('sublimation_orders', ['business_id' => $business]);
    }
}
