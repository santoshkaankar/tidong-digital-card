<?php

namespace Tests\Feature;

use App\Models\Authorization\Role;
use App\Models\Identity\AccountType;
use App\Models\User;
use App\Models\Vendor\Vendor;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class IndiaPostBranchAddressTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::beginTransaction();
        foreach (['customer','vendor'] as $code) {
            AccountType::firstOrCreate(['code' => $code], ['name' => ucfirst($code), 'is_active' => true]);
        }
        Role::firstOrCreate(['code' => 'vendor_owner'], ['name' => 'Vendor Owner', 'is_system' => true]);
    }

    protected function tearDown(): void
    {
        if (DB::transactionLevel() > 0) DB::rollBack();
        parent::tearDown();
    }

    private function vendor(): array
    {
        $user = User::factory()->create();
        $user->userAccountTypes()->create([
            'account_type_id' => AccountType::where('code','customer')->firstOrFail()->id,
            'status' => 'active', 'activated_at' => now(),
        ]);
        $this->actingAs($user)->post(route('vendor.apply.store'), ['display_name' => 'Address Test Vendor']);
        $vendor = Vendor::where('user_id',$user->id)->firstOrFail();
        $vendor->update(['status' => 'approved', 'approved_at' => now()]);
        return [$user,$vendor];
    }

    public function test_guest_cannot_query_private_dropdown(): void
    {
        $this->get(route('vendor.address.options', ['level' => 'states']))->assertRedirect(route('login'));
    }

    public function test_vendor_dropdown_and_selected_post_office_sets_trusted_address(): void
    {
        [$user,$vendor] = $this->vendor();
        $id = DB::table('india_post_offices')->insertGetId([
            'row_hash' => sha1('BRANCH ADDRESS TEST '.uniqid()), 'pincode' => '324001',
            'post_office' => 'Kota Test H.O', 'district' => 'KOTA', 'state' => 'RAJASTHAN',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $this->get(route('vendor.address.options', ['level'=>'states']))->assertOk()->assertSee('RAJASTHAN');
        $this->get(route('vendor.address.options', ['level'=>'districts','state'=>'RAJASTHAN']))->assertOk()->assertSee('KOTA');
        $this->get(route('vendor.address.options', ['level'=>'offices','state'=>'RAJASTHAN','district'=>'KOTA']))->assertOk()->assertSee('324001');
        $business=$vendor->businesses()->create([
            'name'=>'Sample','business_type'=>'restaurant','slug'=>'address-test-'.uniqid(),
            'status'=>'approved','is_accepting_orders'=>false,
        ]);
        $this->post(route('vendor.branches.store',$business->id), [
            'name'=>'Kota Branch','india_post_office_id'=>$id,
            'state'=>'WRONG','district'=>'WRONG','pincode'=>'999999',
            'city'=>'Kota','address_line'=>'123 Sample Street',
        ])->assertRedirect(route('vendor.home'));
        $this->assertDatabaseHas('branches', [
            'business_id'=>$business->id,'india_post_office_id'=>$id,
            'pincode'=>'324001','state'=>'RAJASTHAN','district'=>'KOTA',
            'post_office'=>'Kota Test H.O','city'=>'Kota',
        ]);
    }

    public function test_nonexistent_post_office_is_rejected(): void
    {
        [$user,$vendor]=$this->vendor();
        $business=$vendor->businesses()->create([
            'name'=>'Shop','business_type'=>'retail','slug'=>'unknown-address-'.uniqid(),
            'status'=>'pending','is_accepting_orders'=>false,
        ]);
        $this->post(route('vendor.branches.store',$business->id), [
            'name'=>'Wrong','india_post_office_id'=>999999999,
        ])->assertSessionHasErrors('india_post_office_id');
        $this->assertSame(0,$business->branches()->count());
    }
}
