<?php

namespace Tests\Feature;

use App\Models\Identity\AccountType;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

/** Sublimation-only regressions; run only against a dedicated local test DB. */
class SublimationOrderFlowV6Test extends TestCase
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

    private function studio(): array
    {
        $user = User::factory()->create();
        $type = AccountType::firstOrCreate(['code' => 'vendor'], [
            'name' => 'Vendor', 'is_active' => true,
        ]);
        $user->userAccountTypes()->create([
            'account_type_id' => $type->id, 'status' => 'active', 'activated_at' => now(),
        ]);
        $vendor = DB::table('vendors')->insertGetId([
            'user_id' => $user->id, 'vendor_code' => 'S6-'.Str::random(18),
            'display_name' => 'V6 Studio', 'status' => 'approved',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $business = DB::table('businesses')->insertGetId([
            'vendor_id' => $vendor, 'name' => 'V6 Studio Business',
            'slug' => 's6-'.Str::lower((string) Str::ulid()),
            'business_type' => 'sublimation', 'status' => 'approved',
            'is_accepting_orders' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $branch = DB::table('branches')->insertGetId([
            'business_id' => $business, 'name' => 'V6 Branch',
            'branch_code' => 'S6-'.Str::random(18), 'status' => 'active',
            'is_accepting_orders' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $product = DB::table('sublimation_products')->insertGetId([
            'business_id' => $business, 'branch_id' => $branch,
            'name' => 'V6 Custom Mug', 'category' => 'Mug',
            'blank_cost_paise' => 3000, 'print_cost_paise' => 2000,
            'packing_cost_paise' => 500, 'other_cost_paise' => 0,
            'sale_price_paise' => 12000, 'is_active' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        return [$user, $business, $branch, $product];
    }

    private function form(int $branch, int $product, array $changes = []): array
    {
        return array_replace([
            'branch_id' => $branch, 'product_id' => $product,
            'customer_name' => 'Walk-in Buyer', 'quantity' => 2,
            'fulfillment_method' => 'pickup', 'request_token' => (string) Str::uuid(),
        ], $changes);
    }

    public function test_studio_can_create_delivery_job_then_dispatch_and_complete_it(): void
    {
        [$vendor, $business, $branch, $product] = $this->studio();
        $this->actingAs($vendor)->post(route('vendor.sublimation.orders.store', $business),
            $this->form($branch, $product, [
                'fulfillment_method' => 'delivery', 'customer_mobile' => '9999999999',
                'delivery_address' => 'Jaipur 302001, Sample Colony',
            ]))->assertSessionHasNoErrors();
        $item = DB::table('sublimation_orders')->where('business_id', $business)->firstOrFail();
        $this->assertSame('delivery', $item->fulfillment_method);
        $this->assertSame('Jaipur 302001, Sample Colony', $item->delivery_address);
        $this->assertSame(24000, (int) $item->total_sale_paise);
        $this->actingAs($vendor)->post(route('vendor.sublimation.orders.store', $business),
            $this->form($branch, $product, ['customer_name' => 'Another Pickup']))
            ->assertSessionHasNoErrors();
        $pickup = DB::table('sublimation_orders')->where('customer_name', 'Another Pickup')->firstOrFail();
        $this->assertSame('pickup', $pickup->fulfillment_method);
        $path = route('vendor.sublimation.orders.status', ['business' => $business, 'order' => $item->id]);
        $this->post($path, ['status' => 'approval_pending'])->assertSessionHasNoErrors();
        $this->post($path, ['status' => 'approved', 'approval_note' => 'Buyer approved print design in person'])
            ->assertSessionHasNoErrors();
        $this->post($path, ['status' => 'in_production'])->assertSessionHasNoErrors();
        $this->post($path, ['status' => 'ready'])->assertSessionHasNoErrors();
        $this->post($path, ['status' => 'delivered', 'delivery_confirmation_note' => 'Buyer received'])
            ->assertSessionHasErrors('status');
        $this->post($path, ['status' => 'dispatched', 'delivery_carrier' => 'Studio courier'])
            ->assertSessionHasNoErrors();
        $this->post($path, ['status' => 'delivered', 'delivery_confirmation_note' => 'Handed to buyer'])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('sublimation_orders', ['id' => $item->id, 'status' => 'delivered']);
    }

    public function test_delivery_requires_address_and_phone_and_does_not_save_invalid_jobs(): void
    {
        [$vendor, $business, $branch, $product] = $this->studio();
        $this->actingAs($vendor)->post(route('vendor.sublimation.orders.store', $business),
            $this->form($branch, $product, ['fulfillment_method' => 'delivery']))
            ->assertSessionHasErrors(['customer_mobile', 'delivery_address']);
        $this->assertSame(0, DB::table('sublimation_orders')->where('business_id', $business)->count());
    }

    public function test_cancel_or_rework_requires_reason_and_records_it_in_history(): void
    {
        [$vendor, $business, $branch, $product] = $this->studio();
        $this->actingAs($vendor)->post(route('vendor.sublimation.orders.store', $business),
            $this->form($branch, $product))->assertSessionHasNoErrors();
        $item = DB::table('sublimation_orders')->where('business_id', $business)->firstOrFail();
        $path = route('vendor.sublimation.orders.status', ['business' => $business, 'order' => $item->id]);
        $this->post($path, ['status' => 'cancelled'])->assertSessionHasErrors('status_note');
        $this->post($path, ['status' => 'approval_pending'])->assertSessionHasNoErrors();
        $this->post($path, ['status' => 'design_pending'])->assertSessionHasErrors('status_note');
        $this->post($path, ['status' => 'design_pending', 'status_note' => 'Customer requested another color'])
            ->assertSessionHasNoErrors();
        $event = DB::table('audit_logs')->where('entity_type', 'sublimation_order')
            ->where('entity_id', $item->id)->where('action', 'sublimation.order.status_changed')
            ->orderByDesc('id')->firstOrFail();
        $this->assertSame('Customer requested another color', json_decode($event->new_values, true)['status_note']);
    }

    public function test_legacy_vendor_requests_default_to_pickup_without_requiring_migration_again(): void
    {
        [$vendor, $business, $branch, $product] = $this->studio();
        $payload = $this->form($branch, $product);
        unset($payload['fulfillment_method']);
        $this->actingAs($vendor)->post(route('vendor.sublimation.orders.store', $business), $payload)
            ->assertSessionHasNoErrors();
        $order = DB::table('sublimation_orders')->where('business_id', $business)->firstOrFail();
        $this->assertSame('pickup', $order->fulfillment_method);
    }

    public function test_another_vendor_cannot_open_or_change_order(): void
    {
        [$owner, $business, $branch, $product] = $this->studio();
        $this->actingAs($owner)->post(route('vendor.sublimation.orders.store', $business),
            $this->form($branch, $product))->assertSessionHasNoErrors();
        $order = DB::table('sublimation_orders')->where('business_id', $business)->firstOrFail();
        [$otherVendor] = $this->studio();
        $this->actingAs($otherVendor)->get(route('vendor.sublimation.orders.show', [
            'business' => $business, 'order' => $order->id,
        ]))->assertNotFound();
        $this->post(route('vendor.sublimation.orders.status', [
            'business' => $business, 'order' => $order->id,
        ]), ['status' => 'cancelled', 'status_note' => 'Should not be allowed'])->assertNotFound();
        $this->assertDatabaseHas('sublimation_orders', ['id' => $order->id, 'status' => 'design_pending']);
    }

    public function test_queue_status_filter_stays_scoped_to_selected_branch_and_status(): void
    {
        [$vendor, $business, $branch, $product] = $this->studio();
        $this->actingAs($vendor);
        foreach (['Approval Sample', 'Design Sample'] as $name) {
            $this->post(route('vendor.sublimation.orders.store', $business),
                $this->form($branch, $product, ['customer_name' => $name]))->assertSessionHasNoErrors();
        }
        $approval = DB::table('sublimation_orders')->where('customer_name', 'Approval Sample')->firstOrFail();
        $this->post(route('vendor.sublimation.orders.status', [
            'business' => $business, 'order' => $approval->id,
        ]), ['status' => 'approval_pending'])->assertSessionHasNoErrors();
        $this->get(route('vendor.sublimation.orders.index', [
            'business' => $business, 'branch' => $branch, 'queue' => 'design', 'status' => 'approval_pending',
        ]))->assertOk()->assertSee('Approval Sample')->assertDontSee('Design Sample');
    }
}
