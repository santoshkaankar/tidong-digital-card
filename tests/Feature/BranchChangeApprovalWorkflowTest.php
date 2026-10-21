<?php
namespace Tests\Feature;

use App\Models\Identity\AccountType;
use App\Models\Authorization\Role;
use App\Models\User;
use App\Models\Vendor\BranchChangeRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class BranchChangeApprovalWorkflowTest extends TestCase
{
    protected function setUp(): void { parent::setUp(); DB::beginTransaction(); }
    protected function tearDown(): void { if (DB::transactionLevel() > 0) DB::rollBack(); parent::tearDown(); }

    private function makeVendor(): array
    {
        $user = User::factory()->create();
        $vendorAccount = AccountType::firstOrCreate(['code' => 'vendor'], ['name' => 'Vendor', 'is_active' => true]);
        $user->userAccountTypes()->create(['account_type_id' => $vendorAccount->id, 'status' => 'active', 'activated_at' => now()]);
        $vendor = DB::table('vendors')->insertGetId([
            'user_id' => $user->id, 'vendor_code' => 'VND-'.Str::upper(Str::random(14)),
            'display_name' => 'Workflow Vendor', 'status' => 'approved', 'created_at' => now(), 'updated_at' => now(),
        ]);
        $business = DB::table('businesses')->insertGetId([
            'vendor_id' => $vendor, 'business_type' => 'restaurant', 'name' => 'Test Restaurant',
            'slug' => 'test-'.Str::lower((string)Str::ulid()), 'status' => 'approved',
            'is_accepting_orders' => false, 'created_at' => now(), 'updated_at' => now(),
        ]);
        $branch = DB::table('branches')->insertGetId([
            'business_id' => $business, 'name' => 'Existing Branch', 'branch_code' => 'BR-'.Str::upper(Str::random(14)),
            'country_code' => 'IN', 'status' => 'active', 'is_accepting_orders' => false,
            'state' => 'RAJASTHAN', 'city' => 'Kota', 'phone' => '1234567890',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        return [$user,$vendor,$business,$branch];
    }
    private function admin(): User
    {
        $user = User::factory()->create();
        $type = AccountType::firstOrCreate(['code' => 'platform_admin'], ['name' => 'Platform Admin', 'is_active' => true]);
        $user->userAccountTypes()->create(['account_type_id' => $type->id,'status'=>'active','activated_at'=>now()]);
        $role = Role::firstOrCreate(['code'=>'super_admin'], ['name'=>'Super Admin','is_system'=>true]);
        $user->roleAssignments()->create(['role_id'=>$role->id,'scope_type'=>'platform','scope_id'=>null,'is_active'=>true,'starts_at'=>now()->subMinute()]);
        return $user;
    }
    private function branchRequest(int $branch): array
    {
        return ['name' => 'Edited Branch', 'phone' => '9876543210', 'city' => 'Kota', 'address_line' => 'New Address'];
    }
    public function test_guest_cannot_edit_branch(): void
    {
        $this->get(route('vendor.branch.edit', 1))->assertRedirect(route('login'));
    }
    public function test_vendor_request_does_not_change_live_branch_until_admin_approval(): void
    {
        [$owner,,, $branch] = $this->makeVendor();
        $this->actingAs($owner)->post(route('vendor.branch.changes.store', $branch), $this->branchRequest($branch))->assertSessionHasNoErrors();
        $this->assertDatabaseHas('branches', ['id'=>$branch,'name'=>'Existing Branch','phone'=>'1234567890']);
        $entry = BranchChangeRequest::where('branch_id',$branch)->firstOrFail();
        $this->assertSame('pending',$entry->status);
        $this->actingAs($this->admin())->post(route('admin.branch-changes.approve', $entry->id))->assertRedirect();
        $this->assertDatabaseHas('branches', ['id'=>$branch,'name'=>'Edited Branch','phone'=>'9876543210']);
        $this->assertDatabaseHas('branch_change_requests', ['id'=>$entry->id,'status'=>'approved']);
        $this->assertDatabaseHas('audit_logs', ['entity_type'=>'branch_change_request','entity_id'=>$entry->id,'action'=>'branch.change.approved']);
    }
    public function test_reject_keeps_previous_values_and_reason(): void
    {
        [$owner,,,$branch]=$this->makeVendor();
        $this->actingAs($owner)->post(route('vendor.branch.changes.store',$branch),$this->branchRequest($branch))->assertSessionHasNoErrors();
        $entry=BranchChangeRequest::where('branch_id',$branch)->firstOrFail();
        $this->actingAs($this->admin())->post(route('admin.branch-changes.reject',$entry->id), ['reason'=>'Incorrect branch documents'])->assertRedirect();
        $this->assertDatabaseHas('branches', ['id'=>$branch,'name'=>'Existing Branch']);
        $this->assertDatabaseHas('branch_change_requests', ['id'=>$entry->id,'status'=>'rejected','review_reason'=>'Incorrect branch documents']);
    }
    public function test_duplicate_pending_request_and_repeat_approval_are_blocked(): void
    {
        [$owner,,,$branch]=$this->makeVendor();
        $this->actingAs($owner)->post(route('vendor.branch.changes.store',$branch),$this->branchRequest($branch))->assertSessionHasNoErrors();
        $this->post(route('vendor.branch.changes.store',$branch),$this->branchRequest($branch))->assertSessionHasErrors('branch');
        $entry=BranchChangeRequest::where('branch_id',$branch)->firstOrFail();
        $this->actingAs($this->admin())->post(route('admin.branch-changes.approve',$entry->id))->assertRedirect();
        $this->post(route('admin.branch-changes.approve',$entry->id))->assertSessionHasErrors('status');
    }
    public function test_vendor_cannot_request_other_vendor_branch_change(): void
    {
        [, , , $otherBranch] = $this->makeVendor();
        [$owner] = $this->makeVendor();
        $this->actingAs($owner)->post(route('vendor.branch.changes.store',$otherBranch),$this->branchRequest($otherBranch))->assertNotFound();
    }
    public function test_new_gst_number_remains_pending_until_admin_approves(): void
    {
        [$owner,,,$branch]=$this->makeVendor();
        $this->actingAs($owner)->post(route('vendor.branch.licenses.store',$branch),[
            'license_type'=>'gstin','license_number'=>'08ABCDE1234F1Z5','registered_name'=>'Test Enterprise',
        ])->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('branch_licenses',['branch_id'=>$branch,'license_number'=>'08ABCDE1234F1Z5']);
        $entry=BranchChangeRequest::where('branch_id',$branch)->firstOrFail();
        $this->assertSame('license_create',$entry->change_type);
        $this->actingAs($this->admin())->post(route('admin.branch-changes.approve',$entry->id))->assertRedirect();
        $this->assertDatabaseHas('branch_licenses',['branch_id'=>$branch,'license_number'=>'08ABCDE1234F1Z5']);
    }
    public function test_license_edit_requires_separate_approval(): void
    {
        [$owner,,,$branch]=$this->makeVendor();
        $license=DB::table('branch_licenses')->insertGetId([
            'branch_id'=>$branch,'license_type'=>'fssai','license_number'=>'12345678901234',
            'registered_name'=>'Original Name','created_at'=>now(),'updated_at'=>now(),
        ]);
        $this->actingAs($owner)->put(route('vendor.branch.licenses.update',[$branch,$license]),[
            'registered_name'=>'Proposed Name',
        ])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('branch_licenses',['id'=>$license,'registered_name'=>'Original Name']);
        $entry=BranchChangeRequest::where('branch_id',$branch)->firstOrFail();
        $this->actingAs($this->admin())->post(route('admin.branch-changes.approve',$entry->id))->assertRedirect();
        $this->assertDatabaseHas('branch_licenses',['id'=>$license,'registered_name'=>'Proposed Name']);
    }
}
