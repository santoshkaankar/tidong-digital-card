<?php

namespace Tests\Feature;

use App\Models\Identity\AccountType;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

/** V7 negative/security regression cases; NEVER run against live DB. */
class SublimationOrderFlowV7Test extends TestCase
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

    private function newOrder(User $owner, int $business, int $branch, int $product, array $changes = []): object
    {
        $this->actingAs($owner)->post(route('vendor.sublimation.orders.store', $business),
            $this->form($branch, $product, $changes))->assertSessionHasNoErrors();
        return DB::table('sublimation_orders')->where('business_id', $business)->orderByDesc('id')->firstOrFail();
    }

    private function statusPath(int $business, int $order): string
    {
        return route('vendor.sublimation.orders.status', ['business' => $business, 'order' => $order]);
    }

    private function advanceToApproved(int $business, int $order): void
    {
        $path = $this->statusPath($business, $order);
        $this->post($path, ['status' => 'approval_pending'])->assertSessionHasNoErrors();
        $this->post($path, ['status' => 'approved', 'approval_note' => 'Customer approved draft by telephone'])
            ->assertSessionHasNoErrors();
    }

    private function customer(): User
    {
        $user = User::factory()->create();
        $type = AccountType::firstOrCreate(['code' => 'customer'], ['name' => 'Customer', 'is_active' => true]);
        $user->userAccountTypes()->create([
            'account_type_id' => $type->id, 'status' => 'active', 'activated_at' => now(),
        ]);
        return $user;
    }

    public function test_vendor_replay_returns_same_order_and_modified_token_payload_rejected(): void
    {
        [$owner, $business, $branch, $product] = $this->studio();
        $url = route('vendor.sublimation.orders.store', $business);
        $form = $this->form($branch, $product);
        $this->actingAs($owner)->post($url, $form)->assertSessionHasNoErrors();
        $first = DB::table('sublimation_orders')->where('business_id', $business)->firstOrFail();
        $this->post($url, $form)->assertRedirect(route('vendor.sublimation.orders.show', ['business' => $business, 'order' => $first->id]));
        $this->assertSame(1, DB::table('sublimation_orders')->where('business_id', $business)->count());
        $this->post($url, array_replace($form, ['quantity' => 3]))->assertSessionHasErrors('request_token');
        $this->assertSame(1, DB::table('sublimation_orders')->where('business_id', $business)->count());
    }

    public function test_cross_vendor_cannot_reuse_another_vendor_token(): void
    {
        [$owner, $business, $branch, $product] = $this->studio();
        $form = $this->form($branch, $product);
        $this->actingAs($owner)->post(route('vendor.sublimation.orders.store', $business), $form)->assertSessionHasNoErrors();
        [$other, $otherBusiness, $otherBranch, $otherProduct] = $this->studio();
        $this->actingAs($other)->post(route('vendor.sublimation.orders.store', $otherBusiness),
            $this->form($otherBranch, $otherProduct, ['request_token' => $form['request_token']]))
            ->assertSessionHasErrors('request_token');
        $this->assertSame(0, DB::table('sublimation_orders')->where('business_id', $otherBusiness)->count());
    }

    public function test_studio_intake_rejects_suspended_business_and_inactive_branch_but_retains_old_jobs(): void
    {
        [$owner, $business, $branch, $product] = $this->studio();
        $old = $this->newOrder($owner, $business, $branch, $product);
        DB::table('businesses')->where('id', $business)->update(['status' => 'suspended']);
        $this->post(route('vendor.sublimation.orders.store', $business), $this->form($branch, $product))
            ->assertSessionHasErrors('branch_id');
        $this->get(route('vendor.sublimation.orders.show', ['business'=>$business,'order'=>$old->id]))->assertOk();
        DB::table('businesses')->where('id', $business)->update(['status' => 'approved']);
        DB::table('branches')->where('id', $branch)->update(['status' => 'inactive']);
        $this->post(route('vendor.sublimation.orders.store', $business), $this->form($branch, $product))
            ->assertSessionHasErrors('branch_id');
        $this->assertSame(1, DB::table('sublimation_orders')->where('business_id', $business)->count());
    }

    public function test_offline_manual_order_can_be_accepted_while_online_order_intake_is_disabled(): void
    {
        [$owner, $business, $branch, $product] = $this->studio();
        DB::table('businesses')->where('id', $business)->update(['is_accepting_orders'=>false]);
        DB::table('branches')->where('id', $branch)->update(['is_accepting_orders'=>false]);
        $this->newOrder($owner, $business, $branch, $product);
        $this->assertSame(1, DB::table('sublimation_orders')->where('business_id', $business)->count());
    }

    public function test_delivery_phone_validation_and_legacy_address_repair_with_audit(): void
    {
        [$owner, $business, $branch, $product] = $this->studio();
        $data = ['fulfillment_method'=>'delivery', 'delivery_address'=>'Bapu Nagar, Jaipur'];
        foreach (['x', '123', 'bad-phone', '12345678901234567'] as $bad) {
            $this->actingAs($owner)->post(route('vendor.sublimation.orders.store', $business),
                $this->form($branch, $product, $data + ['customer_mobile'=>$bad]))
                ->assertSessionHasErrors('customer_mobile');
        }
        $item = $this->newOrder($owner, $business, $branch, $product, array_merge($data, ['customer_mobile'=>'+91 99999 99999']));
        DB::table('sublimation_orders')->where('id', $item->id)->update(['delivery_address'=>null]);
        $contact = route('vendor.sublimation.orders.contact', ['business'=>$business,'order'=>$item->id]);
        $this->patch($contact, ['customer_mobile'=>'9999999999', 'delivery_address'=>'   '])
            ->assertSessionHasErrors('delivery_address');
        $this->patch($contact, ['customer_mobile'=>'9999999999', 'delivery_address'=>'Jaipur 302015'])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('sublimation_orders', ['id'=>$item->id, 'delivery_address'=>'Jaipur 302015']);
        $this->assertDatabaseHas('audit_logs', ['entity_id'=>$item->id, 'action'=>'sublimation.order.contact_updated']);
    }

    public function test_cross_vendor_cannot_edit_contact_and_dispatched_contact_is_locked(): void
    {
        [$owner, $business, $branch, $product] = $this->studio();
        $item = $this->newOrder($owner, $business, $branch, $product, [
            'fulfillment_method'=>'delivery', 'customer_mobile'=>'9999999999', 'delivery_address'=>'Old Address',
        ]);
        [$other] = $this->studio();
        $contact = route('vendor.sublimation.orders.contact', ['business'=>$business,'order'=>$item->id]);
        $this->actingAs($other)->patch($contact, ['delivery_address'=>'Evil', 'customer_mobile'=>'9999999999'])->assertNotFound();
        $this->actingAs($owner);
        $this->advanceToApproved($business, $item->id);
        $path=$this->statusPath($business,$item->id);
        $this->post($path, ['status'=>'in_production'])->assertSessionHasNoErrors();
        $this->post($path, ['status'=>'ready'])->assertSessionHasNoErrors();
        $this->post($path, ['status'=>'dispatched', 'delivery_carrier'=>'Own rider'])->assertSessionHasNoErrors();
        $this->patch($contact, ['delivery_address'=>'Another Address','customer_mobile'=>'9999999999'])
            ->assertSessionHasErrors('delivery_address');
        $this->post($path, ['status'=>'cancelled','status_note'=>'Try hiding this parcel'])
            ->assertSessionHasErrors('status');
        $this->assertDatabaseHas('sublimation_orders', ['id'=>$item->id,'status'=>'dispatched','delivery_address'=>'Old Address']);
    }

    public function test_approved_design_rework_clears_active_approval_and_requires_reapproval(): void
    {
        [$owner, $business, $branch, $product] = $this->studio();
        $item = $this->newOrder($owner, $business, $branch, $product);
        $this->advanceToApproved($business, $item->id);
        $path=$this->statusPath($business,$item->id);
        $this->post($path,['status'=>'design_pending'])->assertSessionHasErrors('status_note');
        $this->post($path,['status'=>'design_pending','status_note'=>'Customer changed logo'])->assertSessionHasNoErrors();
        $changed=DB::table('sublimation_orders')->where('id',$item->id)->firstOrFail();
        $this->assertSame(2, (int)$changed->design_revision);
        $this->assertNull($changed->approval_note);
        $this->assertNull($changed->design_approved_at);
        $this->assertNull($changed->approved_revision);
        $this->post($path,['status'=>'in_production'])->assertSessionHasErrors('status');
        $this->advanceToApproved($business, $item->id);
        $approved=DB::table('sublimation_orders')->where('id',$item->id)->firstOrFail();
        $this->assertSame(2, (int)$approved->approved_revision);
        $this->assertGreaterThanOrEqual(2, DB::table('audit_logs')->where('entity_id',$item->id)
            ->where('action','sublimation.order.status_changed')->where('new_values', 'like', '%approval_note%')->count());
    }

    public function test_malformed_queue_and_status_get_inputs_dont_raise_500(): void
    {
        [$owner, $business] = $this->studio();
        $this->actingAs($owner)->get('/vendor/businesses/'.$business.'/sublimation/orders?queue[]=design&status[]=ready&q[]=bad')
            ->assertOk();
    }

    public function test_customer_request_is_idempotent_and_actor_scoped(): void
    {
        [, $business, $branch, $product] = $this->studio();
        $buyer = $this->customer();
        $token=(string) Str::uuid();
        $form=['request_token'=>$token,'product_id'=>$product,'customer_name'=>'Buyer',
            'customer_mobile'=>'9999999999','quantity'=>1,'fulfillment_method'=>'pickup'];
        $this->actingAs($buyer)->post(route('sublimation.orders.place'),$form)
            ->assertRedirect(route('customer.sublimation.orders'));
        $this->post(route('sublimation.orders.place'),$form)->assertRedirect(route('customer.sublimation.orders'));
        $this->assertSame(1, DB::table('sublimation_orders')->where('customer_user_id',$buyer->id)->count());
        $this->post(route('sublimation.orders.place'),array_replace($form,['quantity'=>2]))
            ->assertSessionHasErrors('request_token');
        $another=$this->customer();
        $this->actingAs($another)->post(route('sublimation.orders.place'),$form)
            ->assertSessionHasErrors('request_token');
        $this->assertSame(1, DB::table('sublimation_orders')->where('business_id',$business)->count());
    }

    public function test_new_studio_and_customer_posts_require_request_token(): void
    {
        [$owner,$business,$branch,$product]=$this->studio();
        $data=$this->form($branch,$product); unset($data['request_token']);
        $this->actingAs($owner)->post(route('vendor.sublimation.orders.store',$business),$data)
            ->assertSessionHasErrors('request_token');
        $buyer=$this->customer();
        $this->actingAs($buyer)->post(route('sublimation.orders.place'),[
            'product_id'=>$product, 'customer_name'=>'Buyer', 'customer_mobile'=>'9999999999',
            'quantity'=>1, 'fulfillment_method'=>'pickup',
        ])->assertSessionHasErrors('request_token');
        $this->assertSame(0, DB::table('sublimation_orders')->where('business_id',$business)->count());
    }

    public function test_pickup_skips_delivery_fields_and_forbids_dispatch(): void
    {
        [$owner,$business,$branch,$product]=$this->studio();
        $item=$this->newOrder($owner,$business,$branch,$product);
        $this->advanceToApproved($business,$item->id);
        $path=$this->statusPath($business,$item->id);
        $this->post($path,['status'=>'in_production'])->assertSessionHasNoErrors();
        $this->post($path,['status'=>'ready'])->assertSessionHasNoErrors();
        $this->post($path,['status'=>'dispatched','delivery_carrier'=>'Not allowed'])->assertSessionHasErrors('status');
        $this->post($path,['status'=>'delivered','delivery_confirmation_note'=>'Buyer collected in store'])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('sublimation_orders',['id'=>$item->id,'status'=>'delivered']);
    }

    public function test_rework_after_production_requires_reason_and_prevents_direct_resumption(): void
    {
        [$owner,$business,$branch,$product]=$this->studio();
        $item=$this->newOrder($owner,$business,$branch,$product);
        $this->advanceToApproved($business,$item->id);
        $path=$this->statusPath($business,$item->id);
        $this->post($path,['status'=>'in_production'])->assertSessionHasNoErrors();
        $this->post($path,['status'=>'design_pending'])->assertSessionHasErrors('status_note');
        $this->post($path,['status'=>'design_pending','status_note'=>'Reprint requested in production'])
            ->assertSessionHasNoErrors();
        $this->post($path,['status'=>'in_production'])->assertSessionHasErrors('status');
        $this->assertDatabaseHas('sublimation_orders', ['id'=>$item->id,'status'=>'design_pending','design_revision'=>2]);
    }

    public function test_customer_cannot_order_inactive_branch_or_product(): void
    {
        [, $business, $branch, $product]=$this->studio();
        $buyer=$this->customer();
        $form=['request_token'=>(string) Str::uuid(), 'product_id'=>$product, 'customer_name'=>'Buyer',
            'customer_mobile'=>'9999999999', 'quantity'=>1, 'fulfillment_method'=>'pickup'];
        DB::table('branches')->where('id',$branch)->update(['status'=>'inactive']);
        $this->actingAs($buyer)->post(route('sublimation.orders.place'),$form)
            ->assertSessionHasErrors('product_id');
        DB::table('branches')->where('id',$branch)->update(['status'=>'active']);
        DB::table('sublimation_products')->where('id',$product)->update(['is_active'=>false]);
        $this->post(route('sublimation.orders.place'),$form)->assertSessionHasErrors('product_id');
        $this->assertSame(0, DB::table('sublimation_orders')->where('business_id',$business)->count());
    }

    public function test_vendor_page_escapes_html_from_order_customer(): void
    {
        [$owner,$business,$branch,$product]=$this->studio();
        $item=$this->newOrder($owner,$business,$branch,$product,[
            'customer_name'=>'<script>alert(1)</script>',
        ]);
        $this->get(route('vendor.sublimation.orders.show',['business'=>$business,'order'=>$item->id]))
            ->assertOk()->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_other_vendor_cannot_submit_status_changes(): void
    {
        [$owner,$business,$branch,$product]=$this->studio();
        $item=$this->newOrder($owner,$business,$branch,$product);
        [$other]=$this->studio();
        $this->actingAs($other)->post($this->statusPath($business,$item->id),[
            'status'=>'cancelled','status_note'=>'Unauthorized cancellation',
        ])->assertNotFound();
        $this->assertDatabaseHas('sublimation_orders',['id'=>$item->id,'status'=>'design_pending']);
    }
}
