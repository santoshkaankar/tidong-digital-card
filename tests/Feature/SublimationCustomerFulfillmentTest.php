<?php

namespace Tests\Feature;

use App\Models\Identity\AccountType;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

/** Written for the isolated local test database; intentionally not run during packaging. */
class SublimationCustomerFulfillmentTest extends TestCase
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

    private function setupStudio(): array
    {
        $vendorUser = User::factory()->create();
        $vendorType = AccountType::firstOrCreate(['code'=>'vendor'], ['name'=>'Vendor','is_active'=>true]);
        $vendorUser->userAccountTypes()->create([
            'account_type_id'=>$vendorType->id, 'status'=>'active', 'activated_at'=>now(),
        ]);
        $vendor = DB::table('vendors')->insertGetId([
            'user_id'=>$vendorUser->id, 'vendor_code'=>'S2-'.Str::random(18),
            'display_name'=>'Custom Gifts', 'status'=>'approved',
            'created_at'=>now(), 'updated_at'=>now(),
        ]);
        $business = DB::table('businesses')->insertGetId([
            'vendor_id'=>$vendor, 'name'=>'Approved Studio', 'slug'=>'s2-'.Str::lower((string) Str::ulid()),
            'business_type'=>'sublimation', 'status'=>'approved', 'is_accepting_orders'=>true,
            'created_at'=>now(), 'updated_at'=>now(),
        ]);
        $branch = DB::table('branches')->insertGetId([
            'business_id'=>$business, 'name'=>'Studio Branch', 'branch_code'=>'B2-'.Str::random(18),
            'status'=>'active','is_accepting_orders'=>true, 'created_at'=>now(), 'updated_at'=>now(),
        ]);
        $product = DB::table('sublimation_products')->insertGetId([
            'business_id'=>$business, 'branch_id'=>$branch, 'name'=>'Personalized Mug',
            'category'=>'Mug', 'blank_cost_paise'=>5000, 'print_cost_paise'=>1000,
            'packing_cost_paise'=>500, 'other_cost_paise'=>0,
            'sale_price_paise'=>18000, 'is_active'=>true,
            'created_at'=>now(), 'updated_at'=>now(),
        ]);
        return [$vendorUser, $business, $branch, $product];
    }

    private function customer(): User
    {
        $customer = User::factory()->create();
        $type = AccountType::firstOrCreate(['code'=>'customer'], ['name'=>'Customer','is_active'=>true]);
        $customer->userAccountTypes()->create([
            'account_type_id'=>$type->id, 'status'=>'active', 'activated_at'=>now(),
        ]);
        return $customer;
    }

    public function test_customer_can_order_and_only_view_own_orders(): void
    {
        [, $business, $branch, $product] = $this->setupStudio();
        $customer = $this->customer();
        $this->actingAs($customer)->post(route('sublimation.orders.place'), [
            'product_id'=>$product, 'customer_name'=>'Buyer One', 'customer_mobile'=>'9999999999',
            'quantity'=>2, 'fulfillment_method'=>'delivery',
            'delivery_address'=>'Sample Road, Jaipur', 'design_instructions'=>'Print my name',
        ])->assertRedirect(route('customer.sublimation.orders'));
        $order = DB::table('sublimation_orders')->where('customer_user_id', $customer->id)->firstOrFail();
        $this->assertSame(36000, (int) $order->total_sale_paise);
        $this->assertSame('design_pending', $order->status);
        $this->assertSame((int) $business, (int) $order->business_id);
        $this->assertSame((int) $branch, (int) $order->branch_id);
        $other = $this->customer();
        $this->actingAs($other)->get(route('customer.sublimation.orders'))
            ->assertOk()->assertDontSee($order->order_no);
    }

    public function test_delivery_requires_dispatch_before_marking_complete(): void
    {
        [$vendorUser, , , $product] = $this->setupStudio();
        $customer = $this->customer();
        $this->actingAs($customer)->post(route('sublimation.orders.place'), [
            'product_id'=>$product, 'customer_name'=>'Buyer Two', 'customer_mobile'=>'9999999999',
            'quantity'=>1, 'fulfillment_method'=>'delivery', 'delivery_address'=>'Sample Road, Jaipur',
        ])->assertRedirect(route('customer.sublimation.orders'));
        $order = DB::table('sublimation_orders')->where('customer_user_id', $customer->id)->firstOrFail();
        $route = fn () => route('vendor.sublimation.orders.status', ['business'=>$order->business_id, 'order'=>$order->id]);
        $this->actingAs($vendorUser)->post($route(), ['status'=>'approval_pending'])->assertSessionHasNoErrors();
        $this->post($route(), ['status'=>'approved','approval_note'=>'Confirmed by phone'])->assertSessionHasNoErrors();
        $this->post($route(), ['status'=>'in_production'])->assertSessionHasNoErrors();
        $this->post($route(), ['status'=>'ready'])->assertSessionHasNoErrors();
        $this->post($route(), ['status'=>'delivered','delivery_confirmation_note'=>'Received by buyer'])
            ->assertSessionHasErrors('status');
        $this->post($route(), ['status'=>'dispatched','delivery_carrier'=>'Self Delivery'])
            ->assertSessionHasNoErrors();
        $this->post($route(), ['status'=>'delivered','delivery_confirmation_note'=>'Received by buyer'])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('sublimation_orders', ['id'=>$order->id, 'status'=>'delivered']);
    }
    public function test_individual_product_options_and_vendor_order_list(): void
    {
        [$vendor, $business, $branch, $product] = $this->setupStudio();
        DB::table('sublimation_products')->where('id', $product)->update([
            'option_choices' => json_encode(['size' => ['M', 'L'], 'color' => ['White', 'Black']]),
        ]);
        $customer = $this->customer();
        $this->actingAs($customer)->get(route('sublimation.products.show', $product))
            ->assertOk()->assertSee('Choose size')->assertSee('Choose color');
        $this->post(route('sublimation.orders.place'), [
            'product_id'=>$product, 'customer_name'=>'Gift Buyer', 'customer_mobile'=>'9999999999',
            'quantity'=>2, 'size'=>'XL', 'color'=>'White', 'fulfillment_method'=>'pickup',
        ])->assertSessionHasErrors('size');
        $this->post(route('sublimation.orders.place'), [
            'product_id'=>$product, 'customer_name'=>'Gift Buyer', 'customer_mobile'=>'9999999999',
            'quantity'=>2, 'size'=>'L', 'color'=>'White', 'fulfillment_method'=>'pickup',
        ])->assertRedirect(route('customer.sublimation.orders'));
        $order = DB::table('sublimation_orders')->where('customer_user_id', $customer->id)->firstOrFail();
        $this->assertSame(['size'=>'L','color'=>'White'], json_decode($order->personalization_options, true));
        $this->actingAs($vendor)->get(route('vendor.sublimation.products.index', $business))->assertOk();
        $this->get(route('vendor.sublimation.orders.index', [
            'business'=>$business, 'branch'=>$branch, 'status'=>'design_pending',
        ]))->assertOk()->assertSee($order->order_no);
        $this->get(route('vendor.sublimation.orders.show', ['business'=>$business, 'order'=>$order->id]))
            ->assertOk()->assertSee('White')->assertSee('Order History');
    }

    public function test_order_details_are_private_to_customer_and_vendor(): void
    {
        [$vendor, $business, , $product] = $this->setupStudio();
        $customer = $this->customer();
        $this->actingAs($customer)->post(route('sublimation.orders.place'), [
            'product_id'=>$product, 'customer_name'=>'Buyer A', 'customer_mobile'=>'9999999999',
            'quantity'=>1, 'fulfillment_method'=>'pickup',
        ]);
        $order = DB::table('sublimation_orders')->where('customer_user_id', $customer->id)->firstOrFail();
        $this->get(route('customer.sublimation.orders.show', $order->id))->assertOk();
        $otherCustomer = $this->customer();
        $this->actingAs($otherCustomer)->get(route('customer.sublimation.orders.show', $order->id))
            ->assertNotFound();
        [$otherVendor, $otherBusiness] = $this->setupStudio();
        $this->actingAs($otherVendor)->get(route('vendor.sublimation.orders.show', [
            'business'=>$otherBusiness, 'order'=>$order->id,
        ]))->assertNotFound();
        $this->actingAs($vendor)->get(route('vendor.sublimation.orders.show', [
            'business'=>$business, 'order'=>$order->id,
        ]))->assertOk();
    }

}
