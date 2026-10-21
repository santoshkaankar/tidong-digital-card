<?php

namespace Tests\Feature;

use App\Models\Authorization\Role;
use App\Models\Identity\AccountType;
use App\Models\User;
use App\Models\Vendor\Vendor;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BranchGpsCoordinatesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::beginTransaction();
        foreach (['customer', 'vendor'] as $code) {
            AccountType::firstOrCreate(['code' => $code], ['name' => ucfirst($code), 'is_active' => true]);
        }
        Role::firstOrCreate(['code'=>'vendor_owner'], ['name'=>'Vendor Owner','is_system'=>true]);
    }

    protected function tearDown(): void
    {
        if (DB::transactionLevel() > 0) DB::rollBack();
        parent::tearDown();
    }

    private function business(): array
    {
        $user = User::factory()->create();
        $user->userAccountTypes()->create([
            'account_type_id'=>AccountType::where('code','customer')->firstOrFail()->id,
            'status'=>'active', 'activated_at'=>now(),
        ]);
        $this->actingAs($user)->post(route('vendor.apply.store'), ['display_name'=>'Branch GPS Test Vendor']);
        $vendor=Vendor::where('user_id', $user->id)->firstOrFail();
        $vendor->update(['status'=>'approved','approved_at'=>now()]);
        $business=$vendor->businesses()->create([
            'name'=>'GPS Test Shop','business_type'=>'restaurant',
            'slug'=>'gps-shop-'.uniqid(), 'status'=>'approved', 'is_accepting_orders'=>false,
        ]);
        return [$vendor, $business];
    }

    public function test_vendor_can_save_optional_actual_outlet_coordinates(): void
    {
        [, $business] = $this->business();
        $this->post(route('vendor.branches.store', $business->id), [
            'name'=>'Outlet Test','city'=>'Kota','latitude'=>'25.2134567','longitude'=>'75.8645678',
        ])->assertRedirect(route('vendor.home'));
        $this->assertDatabaseHas('branches',[
            'business_id'=>$business->id, 'name'=>'Outlet Test',
            'latitude'=>25.2134567, 'longitude'=>75.8645678,
            'is_accepting_orders'=>false,
        ]);
    }

    public function test_vendor_cannot_save_out_of_range_or_incomplete_coordinates(): void
    {
        [, $business] = $this->business();
        $this->post(route('vendor.branches.store', $business->id), [
            'name'=>'Outside India','latitude'=>'52.12345','longitude'=>'75.86456',
        ])->assertSessionHasErrors('latitude');
        $this->post(route('vendor.branches.store', $business->id), [
            'name'=>'Missing Longitude','latitude'=>'25.12345',
        ])->assertSessionHasErrors('longitude');
        $this->assertSame(0,$business->branches()->count());
    }
}
