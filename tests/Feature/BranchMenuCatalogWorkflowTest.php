<?php

namespace Tests\Feature;

use App\Models\Identity\AccountType;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class BranchMenuCatalogWorkflowTest extends TestCase
{
    protected function setUp(): void { parent::setUp(); DB::beginTransaction(); }
    protected function tearDown(): void { if(DB::transactionLevel()>0) DB::rollBack(); parent::tearDown(); }
    private function shop(bool $approved=true): array
    {
        $user=User::factory()->create();
        $account=AccountType::firstOrCreate(['code'=>'vendor'],['name'=>'Vendor','is_active'=>true]);
        $user->userAccountTypes()->create(['account_type_id'=>$account->id,'status'=>'active','activated_at'=>now()]);
        $vendor=DB::table('vendors')->insertGetId(['user_id'=>$user->id,'vendor_code'=>'ML-'.Str::random(18),'display_name'=>'Menu Shop','status'=>'approved','created_at'=>now(),'updated_at'=>now()]);
        $business=DB::table('businesses')->insertGetId(['vendor_id'=>$vendor,'business_type'=>'restaurant','name'=>'Menu Cafe','slug'=>'menu-'.Str::lower((string)Str::ulid()),'status'=>$approved?'approved':'pending','is_accepting_orders'=>false,'created_at'=>now(),'updated_at'=>now()]);
        $branch=DB::table('branches')->insertGetId(['business_id'=>$business,'name'=>'Main Outlet','branch_code'=>'MB-'.Str::random(18),'status'=>'active','is_accepting_orders'=>false,'created_at'=>now(),'updated_at'=>now()]);
        return [$user,$branch,$business];
    }
    private function global(): int
    {
        return DB::table('global_menu_items')->insertGetId(['slug'=>'idli-'.Str::lower((string)Str::ulid()),'name'=>'Idli','category'=>'South Indian','image_path'=>'/images/global-menu/idli-sambar.webp','is_active'=>true,'created_at'=>now(),'updated_at'=>now()]);
    }
    public function test_guest_cannot_browse_private_branch_menu(): void
    {
        $this->get(route('pos.menu-library.index',1))->assertRedirect(route('login'));
    }
    public function test_vendor_can_attach_global_at_own_price_and_not_twice(): void
    {
        [$owner,$branch]=$this->shop();$global=$this->global();
        $this->actingAs($owner)->post(route('pos.menu-library.global',$branch),['global_menu_item_id'=>$global,'price'=>'125.50'])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('pos_menu_items',['branch_id'=>$branch,'global_menu_item_id'=>$global,'source_kind'=>'global','price_paise'=>12550]);
        $this->post(route('pos.menu-library.global',$branch),['global_menu_item_id'=>$global,'price'=>'99.00'])->assertSessionHasErrors('global_menu_item_id');
        $this->assertSame(1,DB::table('pos_menu_items')->where('branch_id',$branch)->count());
    }
    public function test_pending_business_cannot_attach_global_or_create_custom(): void
    {
        [$owner,$branch]=$this->shop(false);$global=$this->global();
        $this->actingAs($owner)->post(route('pos.menu-library.global',$branch),['global_menu_item_id'=>$global,'price'=>'100.00'])->assertSessionHasErrors('business');
        $this->post(route('pos.menu-library.custom',$branch),['name'=>'Special Tea','price'=>'30.00'])->assertSessionHasErrors('business');
        $this->assertDatabaseMissing('pos_menu_items',['branch_id'=>$branch]);
    }
    public function test_private_item_is_isolated_to_branch_and_other_vendor_cannot_edit_it(): void
    {
        [$owner,$branch]=$this->shop();
        $this->actingAs($owner)->post(route('pos.menu-library.custom',$branch),['name'=>'Owner Special','price'=>'199.75'])->assertSessionHasNoErrors();
        $item=DB::table('pos_menu_items')->where('branch_id',$branch)->firstOrFail();
        $this->assertSame('custom',$item->source_kind);
        $this->assertNull($item->global_menu_item_id);
        [$other,$otherBranch]=$this->shop();
        $this->actingAs($other)->get(route('pos.menu-library.index',$branch))->assertNotFound();
        $this->get(route('pos.menu-library.index',$otherBranch))->assertOk()->assertDontSee('Owner Special');
        $this->post(route('pos.menu-library.custom',$branch),['name'=>'Intrusion','price'=>'1.00'])->assertNotFound();
        $this->assertDatabaseMissing('pos_menu_items',['branch_id'=>$otherBranch,'name'=>'Owner Special']);
    }
    public function test_global_item_cannot_be_attached_if_disabled(): void
    {
        [$owner,$branch]=$this->shop();$global=$this->global();
        DB::table('global_menu_items')->where('id',$global)->update(['is_active'=>false]);
        $this->actingAs($owner)->post(route('pos.menu-library.global',$branch),['global_menu_item_id'=>$global,'price'=>'50.00'])->assertSessionHasErrors('global_menu_item_id');
    }
    public function test_same_global_item_can_have_different_prices_in_two_branches(): void
    {
        [$owner,$branch]=$this->shop();$global=$this->global();
        $otherBranch=DB::table('branches')->insertGetId([
            'business_id'=>(int)DB::table('branches')->where('id',$branch)->value('business_id'),
            'name'=>'Second Outlet','branch_code'=>'MB-'.Str::random(18),
            'status'=>'active','is_accepting_orders'=>false,'created_at'=>now(),'updated_at'=>now(),
        ]);
        $this->actingAs($owner)->post(route('pos.menu-library.global',$branch),['global_menu_item_id'=>$global,'price'=>'80.00'])->assertSessionHasNoErrors();
        $this->post(route('pos.menu-library.global',$otherBranch),['global_menu_item_id'=>$global,'price'=>'95.00'])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('pos_menu_items',['branch_id'=>$branch,'global_menu_item_id'=>$global,'price_paise'=>8000]);
        $this->assertDatabaseHas('pos_menu_items',['branch_id'=>$otherBranch,'global_menu_item_id'=>$global,'price_paise'=>9500]);
    }

    public function test_new_thali_is_not_stored_as_atomic_item(): void
    {
        [$owner,$branch]=$this->shop();
        $this->actingAs($owner)->post(route('pos.menu-library.custom',$branch),['name'=>'Special Thali','price'=>'199.75'])->assertSessionHasErrors('name');
        $this->post(route('pos.menu.store',$branch),['name'=>'Special Thali','price'=>'199.75'])->assertSessionHasErrors('name');
        $this->assertDatabaseMissing('pos_menu_items',['name'=>'Special Thali','branch_id'=>$branch]);
    }

    public function test_new_custom_item_does_not_turn_on_branch_orders(): void
    {
        [$owner,$branch]=$this->shop();
        $this->actingAs($owner)->post(route('pos.menu-library.custom',$branch),['name'=>'Special Chai','price'=>'49.00'])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('branches',['id'=>$branch,'is_accepting_orders'=>false]);
        $this->assertDatabaseHas('audit_logs',['action'=>'pos.menu.branch_custom_created']);
    }
}
