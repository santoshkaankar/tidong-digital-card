<?php

namespace Tests\Feature;

use App\Models\Authorization\Role;
use App\Models\Identity\AccountType;
use App\Models\User;
use App\Services\Delivery\DeliveryIdentity;
use App\Services\Delivery\DeliveryService;
use App\Services\Platform\FeatureRegistry;
use App\Services\Platform\ModuleRegistry;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class DeliveryPlatformApprovalGateTest extends TestCase
{
    protected function setUp(): void { parent::setUp(); DB::beginTransaction(); }
    protected function tearDown(): void
    {
        if (DB::transactionLevel() > 0) DB::rollBack();
        parent::tearDown();
    }

    private function actor(string $account, string $role): User
    {
        $u = User::factory()->create(['email' => bin2hex(random_bytes(9)).'@example.test']);
        $a = AccountType::firstOrCreate(['code'=>$account], ['name'=>$account, 'is_active'=>true]);
        $a->update(['is_active'=>true]);
        $u->userAccountTypes()->create(['account_type_id'=>$a->id,'status'=>'active','activated_at'=>now()]);
        $r = Role::firstOrCreate(['code'=>$role], ['name'=>$role, 'is_system'=>true]);
        $u->roleAssignments()->create(['role_id'=>$r->id,'scope_type'=>'platform','scope_id'=>null,
            'is_active'=>true, 'starts_at'=>now()->subMinute()]);
        return $u;
    }

    private function scenario(): array
    {
        $admin = $this->actor('platform_admin','super_admin');
        $modules = app(ModuleRegistry::class);
        if (! DB::table('platform_modules')->where('code','delivery')->exists()) {
            $modules->register('delivery',(int)$admin->id);
        }
        $modules->setEnabled('delivery',true,(int)$admin->id);
        foreach (['delivery.partner_enrollment','delivery.partner_approval',
                'delivery.vendor_dispatch','delivery.partner_progress'] as $code) {
            app(FeatureRegistry::class)->setEnabled($code,true,(int)$admin->id);
        }
        $owner = $this->actor('vendor','vendor_owner');
        $vendor = DB::table('vendors')->insertGetId([
            'user_id'=>$owner->id,'vendor_code'=>'AP-'.bin2hex(random_bytes(5)),
            'display_name'=>'Approved Vendor','status'=>'approved','created_at'=>now(),'updated_at'=>now(),
        ]);
        $business = DB::table('businesses')->insertGetId([
            'vendor_id'=>$vendor,'business_type'=>'restaurant','name'=>'Approval Test Business',
            'slug'=>'app-'.bin2hex(random_bytes(5)),'status'=>'approved',
            'created_at'=>now(),'updated_at'=>now(),
        ]);
        $branch = DB::table('branches')->insertGetId([
            'business_id'=>$business,'name'=>'Delivery Branch','branch_code'=>'AP'.bin2hex(random_bytes(4)),
            'status'=>'active','created_at'=>now(),'updated_at'=>now(),
        ]);
        $rider = User::factory()->create(['email_verified_at'=>null]);
        AccountType::firstOrCreate(['code'=>'delivery'],['name'=>'Delivery','is_active'=>true]);
        Role::firstOrCreate(['code'=>'delivery_partner'],['name'=>'Delivery Partner','is_system'=>true]);
        $service=app(DeliveryService::class);
        $partner=$service->inviteToBusiness($business,(int)$rider->id,'bike',null,(int)$owner->id);
        $service->verifyForBusiness($business,$partner,(int)$owner->id);
        return compact('admin','owner','rider','service','business','branch','partner');
    }

    public function test_vendor_cannot_activate_before_platform_approval(): void
    {
        extract($this->scenario());
        try {
            $service->setRosterStatus($business,$partner,'active',(int)$owner->id);
            $this->fail('Vendor-only approval is prohibited.');
        } catch (ValidationException $e) { $this->assertArrayHasKey('partner',$e->errors()); }
        $this->assertDatabaseHas('delivery_partners',['id'=>$partner,'status'=>'pending']);
    }

    public function test_vendor_cannot_issue_platform_approval(): void
    {
        extract($this->scenario());
        $this->actingAs($owner)->post(route('admin.delivery-partner-approvals.approve',$partner))
            ->assertForbidden();
        $this->assertNull(DB::table('delivery_partners')->where('id',$partner)->value('approved_at'));
    }

    public function test_admin_requires_verified_email_and_completed_profile(): void
    {
        extract($this->scenario());
        $this->actingAs($admin)->post(route('admin.delivery-partner-approvals.approve',$partner))
            ->assertSessionHasErrors('partner');
        DB::table('users')->where('id',$rider->id)->update(['email_verified_at'=>now()]);
        $this->actingAs($admin)->post(route('admin.delivery-partner-approvals.approve',$partner))
            ->assertSessionHasErrors('partner');
        $this->assertNull(DB::table('delivery_partners')->where('id',$partner)->value('approved_by'));
    }

    public function test_vendor_still_required_after_platform_approval_and_rider_starts_offline(): void
    {
        extract($this->scenario());
        DB::table('users')->where('id',$rider->id)->update(['email_verified_at'=>now()]);
        DB::table('delivery_partners')->where('id',$partner)->update(['phone'=>'9999999999']);
        $this->actingAs($admin)->post(route('admin.delivery-partner-approvals.approve',$partner))->assertRedirect();
        $this->assertTrue(app(DeliveryIdentity::class)->isPlatformApproved((int)$rider->id));
        $this->assertFalse(app(DeliveryIdentity::class)->hasActiveRiderCredentials((int)$rider->id));
        $service->setRosterStatus($business,$partner,'active',(int)$owner->id);
        $this->assertDatabaseHas('delivery_partners',['id'=>$partner,'status'=>'active','is_available'=>false]);
        $this->assertTrue(app(DeliveryIdentity::class)->hasActiveRiderCredentials((int)$rider->id));
        $service->setAvailability((int)$rider->id,true);
        $job=$service->createJob([
            'business_id'=>$business,'branch_id'=>$branch,'partner_id'=>$partner,
            'recipient_name'=>'Receiver','recipient_phone'=>'9999999999',
            'dropoff_address'=>'Test city apartment 4',
        ],(int)$owner->id);
        $this->assertDatabaseHas('delivery_jobs',['id'=>$job,'status'=>'assigned']);
        $service->progress($job,(int)$rider->id,'accepted',true);
        $this->assertDatabaseHas('delivery_jobs',['id'=>$job,'status'=>'accepted']);
    }

    public function test_legacy_active_without_platform_audit_is_denied(): void
    {
        extract($this->scenario());
        DB::table('delivery_partners')->where('id',$partner)->update([
            'status'=>'active','approved_by'=>$owner->id,'approved_at'=>now(),
        ]);
        $this->assertFalse(app(DeliveryIdentity::class)->hasActiveRiderCredentials((int)$rider->id));
        try {
            $service->setRosterStatus($business,$partner,'active',(int)$owner->id);
            $this->fail('Forged old status must be denied.');
        } catch (ValidationException $e) { $this->assertArrayHasKey('partner',$e->errors()); }
    }
}
