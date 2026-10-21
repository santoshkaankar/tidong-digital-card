<?php
namespace Tests\Feature;
use App\Models\Identity\AccountType;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class RestaurantTableQrMenuTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::beginTransaction();
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
    public function test_guest_can_view_active_table_menu(): void
    {
        $s=$this->shop();
        $this->get(route('qr.menu.show',$s['token']))->assertOk()->assertSee('Masala Dosa')->assertSee('Table 5');
    }
    public function test_missing_token_is_not_discoverable(): void
    {
        $this->get(route('qr.menu.show',Str::random(48)))->assertNotFound();
    }
    public function test_disabled_table_blocks_public_menu(): void
    {
        $s=$this->shop();DB::table('restaurant_dining_tables')->where('id',$s['table'])->update(['is_active'=>false]);
        $this->get(route('qr.menu.show',$s['token']))->assertNotFound();
    }
    public function test_closed_branch_can_show_menu_but_not_accept_orders(): void
    {
        $s=$this->shop();DB::table('branches')->where('id',$s['branch'])->update(['is_accepting_orders'=>false]);
        $this->get(route('qr.menu.show',$s['token']))->assertOk()->assertSee('temporarily paused');
    }
    public function test_pending_vendor_public_menu_is_not_available(): void
    {
        $s=$this->shop(false);
        $this->get(route('qr.menu.show',$s['token']))->assertNotFound();
    }
    public function test_owner_can_view_table_qr_codes(): void
    {
        $s=$this->shop();
        $this->actingAs($s['owner'])->get(route('pos.tables.index',$s['branch']))->assertOk()->assertSee('Table 5')->assertSee('Table QR code');
    }
    public function test_owner_can_create_a_unique_table(): void
    {
        $s=$this->shop();
        $this->actingAs($s['owner'])->post(route('pos.tables.store',$s['branch']),['label'=>'Table 6'])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('restaurant_dining_tables',['branch_id'=>$s['branch'],'label'=>'Table 6']);
        $this->post(route('pos.tables.store',$s['branch']),['label'=>'Table 6'])->assertSessionHasErrors('label');
    }
    public function test_other_vendor_cannot_view_or_modify_tables(): void
    {
        $s=$this->shop();$other=$this->shop();
        $this->actingAs($other['owner'])->get(route('pos.tables.index',$s['branch']))->assertNotFound();
        $this->post(route('pos.tables.toggle',$s['table']),['active'=>0])->assertNotFound();
        $this->assertDatabaseHas('restaurant_dining_tables',['id'=>$s['table'],'is_active'=>true]);
    }
    public function test_owner_can_disable_qr_and_audit_it(): void
    {
        $s=$this->shop();
        $this->actingAs($s['owner'])->post(route('pos.tables.toggle',$s['table']),['active'=>0])->assertSessionHasNoErrors();
        $this->get(route('qr.menu.show',$s['token']))->assertNotFound();
        $this->assertDatabaseHas('audit_logs',['action'=>'restaurant.table.availability_changed','entity_id'=>$s['table']]);
    }
    public function test_printable_svg_is_valid_and_does_not_show_plain_html(): void
    {
        $s=$this->shop();$svg=\App\Services\Qr\OfflineQrSvg::make(route('qr.menu.show',$s['token']));
        $this->assertStringStartsWith('<svg',$svg);
        $this->assertStringContainsString('<path',$svg);
        $this->assertStringNotContainsString('<script',$svg);
    }
}
