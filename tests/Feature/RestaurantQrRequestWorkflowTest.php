<?php
namespace Tests\Feature;
use App\Models\Identity\AccountType;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class RestaurantQrRequestWorkflowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::beginTransaction();
        // Isolate order workflow tests from shared per-IP rate limiter state.
        $this->withoutMiddleware(\Illuminate\Routing\Middleware\ThrottleRequests::class);
        AccountType::firstOrCreate(['code'=>'vendor'], ['name'=>'Vendor','is_active'=>true]);
    }
    protected function tearDown(): void
    {
        if (DB::transactionLevel()>0) DB::rollBack();
        parent::tearDown();
    }
    private function shop(bool $approved=true): array
    {
        $owner=User::factory()->create();
        $account=AccountType::where('code','vendor')->firstOrFail();
        $owner->userAccountTypes()->create(['account_type_id'=>$account->id,'status'=>'active','activated_at'=>now()]);
        $vendor=DB::table('vendors')->insertGetId([
            'user_id'=>$owner->id,'vendor_code'=>'QR-'.Str::upper(Str::random(16)),
            'display_name'=>'Test Restaurant','status'=>$approved?'approved':'pending',
            'created_at'=>now(),'updated_at'=>now(),
        ]);
        $business=DB::table('businesses')->insertGetId([
            'vendor_id'=>$vendor,'business_type'=>'restaurant','name'=>'Table Restaurant',
            'slug'=>'qr-'.Str::lower((string)Str::ulid()),
            'status'=>$approved?'approved':'pending','is_accepting_orders'=>$approved,
            'created_at'=>now(),'updated_at'=>now(),
        ]);
        $branch=DB::table('branches')->insertGetId([
            'business_id'=>$business,'name'=>'Main Floor','branch_code'=>'QR-'.Str::upper(Str::random(16)),
            'status'=>'active','is_accepting_orders'=>$approved,
            'created_at'=>now(),'updated_at'=>now(),
        ]);
        $item=DB::table('pos_menu_items')->insertGetId([
            'branch_id'=>$branch,'name'=>'Masala Dosa','price_paise'=>12500,'is_active'=>true,
            'created_at'=>now(),'updated_at'=>now(),
        ]);
        $token=Str::random(48);
        $table=DB::table('restaurant_dining_tables')->insertGetId([
            'branch_id'=>$branch,'label'=>'Table 5','qr_token'=>$token,
            'is_active'=>true,'created_at'=>now(),'updated_at'=>now(),
        ]);
        return compact('owner','vendor','business','branch','item','token','table');
    }
    private function postQr(array $s, array $data=[]): \Illuminate\Testing\TestResponse
    {
        return $this->post(route('qr.menu.submit',$s['token']),array_replace([
            'submission_key'=>(string)Str::uuid(), 'customer_name'=>'Guest Example',
            'quantities'=>[$s['item']=>2], 'confirmed'=>1,
        ],$data));
    }
    public function test_public_request_is_pending_not_kitchen_order(): void
    {
        $s=$this->shop();
        $this->postQr($s)->assertSessionHasNoErrors();
        $r=DB::table('restaurant_qr_requests')->where('branch_id',$s['branch'])->first();
        $this->assertNotNull($r);
        $this->assertSame('pending',$r->status);
        $this->assertSame(25000,(int)$r->quoted_total_paise);
        $this->assertDatabaseHas('restaurant_qr_request_items',['request_id'=>$r->id,'unit_price_paise'=>12500,'quantity'=>2]);
        $this->assertDatabaseMissing('pos_orders',['branch_id'=>$s['branch']]);
    }
    public function test_guest_must_confirm_before_submitting(): void
    {
        $s=$this->shop();$this->postQr($s,['confirmed'=>0])->assertSessionHasErrors('confirmed');
        $this->assertDatabaseMissing('restaurant_qr_requests',['branch_id'=>$s['branch']]);
    }
    public function test_guest_must_select_at_least_one_item(): void
    {
        $s=$this->shop();$this->postQr($s,['quantities'=>[$s['item']=>0]])->assertSessionHasErrors('quantities');
    }
    public function test_public_request_refuses_foreign_branch_item(): void
    {
        $s=$this->shop();$other=$this->shop();
        $this->postQr($s,['quantities'=>[$other['item']=>1]])->assertSessionHasErrors('quantities');
        $this->assertDatabaseMissing('restaurant_qr_requests',['branch_id'=>$s['branch']]);
    }
    public function test_public_request_refuses_closed_branch(): void
    {
        $s=$this->shop(); DB::table('branches')->where('id',$s['branch'])->update(['is_accepting_orders'=>false]);
        $this->postQr($s)->assertSessionHasErrors('order');
    }
    public function test_guest_duplicated_form_submits_only_once(): void
    {
        $s=$this->shop();$key=(string)Str::uuid();
        $this->postQr($s,['submission_key'=>$key])->assertSessionHasNoErrors();
        $this->postQr($s,['submission_key'=>$key])->assertSessionHasNoErrors();
        $this->assertSame(1,DB::table('restaurant_qr_requests')->where('submission_key',$key)->count());
    }
    public function test_only_owner_can_review_requests(): void
    {
        $s=$this->shop();$other=$this->shop();
        $this->get(route('pos.qr.requests',$s['branch']))->assertRedirect(route('login'));
        $this->actingAs($other['owner'])->get(route('pos.qr.requests',$s['branch']))->assertNotFound();
    }
    public function test_accepting_guest_request_creates_unpaid_kitchen_order(): void
    {
        $s=$this->shop();$this->postQr($s)->assertSessionHasNoErrors();
        $r=DB::table('restaurant_qr_requests')->where('branch_id',$s['branch'])->firstOrFail();
        $this->actingAs($s['owner'])->post(route('pos.qr.accept',$r->id))->assertSessionHasNoErrors();
        $order=DB::table('pos_orders')->where('branch_id',$s['branch'])->firstOrFail();
        $this->assertSame('qr',$order->order_origin);
        $this->assertSame((int)$s['table'],(int)$order->dining_table_id);
        $this->assertSame('received',$order->status);
        $this->assertSame('unpaid',$order->payment_status);
        $this->assertSame(25000,(int)$order->total_paise);
        $this->assertDatabaseHas('restaurant_qr_requests',['id'=>$r->id,'status'=>'accepted','accepted_order_id'=>$order->id]);
        $this->get(route('kds.branches.show',$s['branch']))->assertOk()->assertSee('Table 5');
    }
    public function test_second_acceptance_is_rejected_without_duplicate(): void
    {
        $s=$this->shop();$this->postQr($s);
        $r=DB::table('restaurant_qr_requests')->where('branch_id',$s['branch'])->firstOrFail();
        $this->actingAs($s['owner'])->post(route('pos.qr.accept',$r->id))->assertSessionHasNoErrors();
        $this->post(route('pos.qr.accept',$r->id))->assertSessionHasErrors('request');
        $this->assertSame(1,DB::table('pos_orders')->where('branch_id',$s['branch'])->count());
    }
    public function test_changed_menu_price_blocks_acceptance(): void
    {
        $s=$this->shop();$this->postQr($s);
        $r=DB::table('restaurant_qr_requests')->where('branch_id',$s['branch'])->firstOrFail();
        DB::table('pos_menu_items')->where('id',$s['item'])->update(['price_paise'=>20000]);
        $this->actingAs($s['owner'])->post(route('pos.qr.accept',$r->id))->assertSessionHasErrors('request');
        $this->assertDatabaseHas('restaurant_qr_requests',['id'=>$r->id,'status'=>'pending']);
        $this->assertDatabaseMissing('pos_orders',['branch_id'=>$s['branch']]);
    }
    public function test_other_vendor_cannot_accept_request(): void
    {
        $s=$this->shop();$other=$this->shop();$this->postQr($s);
        $r=DB::table('restaurant_qr_requests')->where('branch_id',$s['branch'])->firstOrFail();
        $this->actingAs($other['owner'])->post(route('pos.qr.accept',$r->id))->assertNotFound();
    }
    public function test_owner_can_reject_with_reason_and_keep_records(): void
    {
        $s=$this->shop();$this->postQr($s);
        $r=DB::table('restaurant_qr_requests')->where('branch_id',$s['branch'])->firstOrFail();
        $this->actingAs($s['owner'])->post(route('pos.qr.reject',$r->id),['reason'=>'Dish sold out'])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('restaurant_qr_requests',['id'=>$r->id,'status'=>'rejected','rejection_reason'=>'Dish sold out']);
        $this->assertDatabaseHas('restaurant_qr_request_items',['request_id'=>$r->id]);
        $this->assertDatabaseHas('audit_logs',['action'=>'restaurant.qr_request.rejected','entity_id'=>$r->id]);
    }
    public function test_waiter_table_order_goes_to_kitchen_without_guest_request(): void
    {
        $s=$this->shop();
        $this->actingAs($s['owner'])->post(route('pos.orders.store',$s['branch']),[
            'items'=>[['menu_item_id'=>$s['item'],'quantity'=>1]],'dining_table_id'=>$s['table'],
        ])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('pos_orders',['branch_id'=>$s['branch'],'dining_table_id'=>$s['table'],'order_origin'=>'waiter','payment_status'=>'unpaid']);
        $this->assertDatabaseMissing('restaurant_qr_requests',['branch_id'=>$s['branch']]);
    }
    public function test_waiter_cannot_attach_table_from_another_branch(): void
    {
        $s=$this->shop();$other=$this->shop();
        $this->actingAs($s['owner'])->post(route('pos.orders.store',$s['branch']),[
            'items'=>[['menu_item_id'=>$s['item'],'quantity'=>1]],'dining_table_id'=>$other['table'],
        ])->assertSessionHasErrors('dining_table_id');
        $this->assertDatabaseMissing('pos_orders',['branch_id'=>$s['branch']]);
    }
}
