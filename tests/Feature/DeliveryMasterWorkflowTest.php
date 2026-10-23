<?php

namespace Tests\Feature;

use App\Models\Authorization\Role;
use App\Models\Identity\AccountType;
use App\Models\User;
use App\Services\Delivery\DeliveryService;
use App\Services\Delivery\VendorDeliveryAccess;
use App\Services\Platform\ModuleRegistry;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

/** Isolated migrated digital_test_db required. No destructive database operations. */
class DeliveryMasterWorkflowTest extends TestCase
{
    use \Tests\Support\ApprovesDeliveryPartnerForTest;
    protected function setUp(): void { parent::setUp(); DB::beginTransaction(); }
    protected function tearDown(): void
    {
        if (DB::transactionLevel() > 0) DB::rollBack();
        parent::tearDown();
    }
    private function user(): User
    {
        return User::factory()->create(['email'=>bin2hex(random_bytes(7)).'@example.test']);
    }
    private function identity(User $u, string $account, string $role): void
    {
        $type = AccountType::firstOrCreate(['code'=>$account], ['name'=>$account,'is_active'=>true]);
        $type->update(['is_active'=>true]);
        $u->userAccountTypes()->create(['account_type_id'=>$type->id, 'status'=>'active', 'activated_at'=>now()]);
        $roleModel = Role::firstOrCreate(['code'=>$role], ['name'=>$role, 'is_system'=>true]);
        $u->roleAssignments()->create(['role_id'=>$roleModel->id, 'scope_type'=>'platform',
            'scope_id'=>null, 'starts_at'=>now()->subMinute(), 'is_active'=>true]);
    }
    private function admin(): User
    {
        $u=$this->user(); $this->identity($u,'platform_admin','super_admin'); return $u;
    }
    private function vendor(): User
    {
        $u=$this->user(); $this->identity($u,'vendor','vendor_owner'); return $u;
    }
    private function enabled(): void
    {
        $admin=$this->admin();$registry=app(ModuleRegistry::class);
        if (! DB::table('platform_modules')->where('code','delivery')->exists()) {
            $registry->register('delivery',(int)$admin->id);
        }
        $registry->setEnabled('delivery',true,(int)$admin->id);
    }
    private function business(User $owner): array
    {
        $now=now();
        $vendor=DB::table('vendors')->where('user_id', $owner->id)->value('id')
            ?? DB::table('vendors')->insertGetId([
            'user_id'=>$owner->id, 'vendor_code'=>'DV-'.bin2hex(random_bytes(6)),
            'display_name'=>'Test Delivery Vendor', 'status'=>'approved',
            'created_at'=>$now,'updated_at'=>$now,
        ]);
        $business=DB::table('businesses')->insertGetId([
            'vendor_id'=>$vendor, 'business_type'=>'restaurant',
            'name'=>'Test Restaurant', 'slug'=>'dv-'.bin2hex(random_bytes(6)),
            'status'=>'approved', 'created_at'=>$now,'updated_at'=>$now,
        ]);
        $branch=DB::table('branches')->insertGetId([
            'business_id'=>$business,'name'=>'Test Branch','branch_code'=>'B'.bin2hex(random_bytes(4)),
            'status'=>'active','created_at'=>$now,'updated_at'=>$now,
        ]);
        return [$business,$branch];
    }
    private function partner(DeliveryService $service, User $owner, int $business): array
    {
        $rider=$this->user();
        AccountType::firstOrCreate(['code'=>'delivery'], ['name'=>'Delivery','is_active'=>true]);
        Role::firstOrCreate(['code'=>'delivery_partner'], ['name'=>'Delivery Partner','is_system'=>true]);
        $id=$service->inviteToBusiness($business,(int)$rider->id,'bike',null,(int)$owner->id);
        $service->verifyForBusiness($business,$id,(int)$owner->id);
        $this->approvePlatformDeliveryForTest($id);
        $service->setRosterStatus($business,$id,'active',(int)$owner->id);
        $service->setAvailability((int)$rider->id,true);
        return [$rider,$id];
    }
    private function data(int $business,int $branch,int $partner): array
    {
        return ['business_id'=>$business,'branch_id'=>$branch,'partner_id'=>$partner,
            'recipient_name'=>'Recipient','recipient_phone'=>'9999999999',
            'dropoff_address'=>'A long enough delivery address in Kota',
            'delivery_fee_paise'=>1500,'partner_earning_paise'=>800];
    }
    private function expectVendorDeniedBeforePlatformApproval(DeliveryService $service, int $business,
        int $partnerId, int $ownerId): void
    {
        try {
            $service->setRosterStatus($business,$partnerId,'active',$ownerId);
            $this->fail('Vendor must not activate before Admin approval.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('partner',$e->errors());
        }
    }
    public function test_vendor_only_can_verify_activate_and_assign_partner(): void
    {
        $this->enabled();$owner=$this->vendor();[$business,$branch]=$this->business($owner);
        AccountType::updateOrCreate(['code'=>'delivery'], ['name'=>'Delivery', 'is_active'=>true]);
        Role::firstOrCreate(['code'=>'delivery_partner'], ['name'=>'Delivery Partner', 'is_system'=>true]);
        $service=app(DeliveryService::class); $rider=$this->user();
        $id=$service->inviteToBusiness($business,(int)$rider->id,'bike',null,(int)$owner->id);
        try { $service->setRosterStatus($business,$id,'active',(int)$owner->id); $this->fail(); }
        catch (ValidationException $e) { $this->assertTrue(true); }
        $service->verifyForBusiness($business,$id,(int)$owner->id);
        // Even a verified Vendor cannot promote a rider without platform approval.
        $this->expectVendorDeniedBeforePlatformApproval($service,$business,$id,(int)$owner->id);
        $this->approvePlatformDeliveryForTest($id);
        $service->setRosterStatus($business,$id,'active',(int)$owner->id);
        $service->setAvailability((int)$rider->id,true);
        $job=$service->createJob($this->data($business,$branch,$id),(int)$owner->id);
        $this->assertDatabaseHas('delivery_jobs',['id'=>$job,'business_id'=>$business]);
        $this->assertDatabaseHas('delivery_business_partners', ['business_id'=>$business, 'partner_id'=>$id,'status'=>'active']);
    }
    public function test_partner_pin_proof_required_and_events_immutable(): void
    {
        $this->enabled();$owner=$this->vendor();[$business,$branch]=$this->business($owner);
        $service=app(DeliveryService::class);[$rider,$id]=$this->partner($service,$owner,$business);
        $job=$service->createJob($this->data($business,$branch,$id),(int)$owner->id);
        $pin=$service->takeHandoffPin();$this->assertMatchesRegularExpression('/^[0-9]{6}$/',$pin);
        $this->assertNotEquals($pin,DB::table('delivery_jobs')->where('id',$job)->value('handoff_pin_hash'));
        $service->progress($job,(int)$rider->id,'accepted',true);
        $service->progress($job,(int)$rider->id,'picked_up',true);
        try { $service->progress($job,(int)$rider->id,'delivered',true,
            ['handoff_pin'=>'000000','recipient_name'=>'Receiver']); $this->fail(); }
        catch (ValidationException $expected) { $this->assertTrue(true); }
        $service->progress($job,(int)$rider->id,'delivered',true,
            ['handoff_pin'=>$pin,'recipient_name'=>'Receiver']);
        $this->assertDatabaseHas('delivery_jobs',['id'=>$job,'status'=>'delivered',
            'delivery_fee_paise'=>1500,'partner_earning_paise'=>800]);
        $this->assertSame(5, DB::table('delivery_job_events')->where('delivery_job_id',$job)->count());
    }
    public function test_platform_admin_cannot_dispatch_or_cancel_even_if_module_manager(): void
    {
        $this->enabled();$owner=$this->vendor();[$business,$branch]=$this->business($owner);
        $service=app(DeliveryService::class);[$rider,$id]=$this->partner($service,$owner,$business);
        $admin=$this->admin();
        try { $service->createJob($this->data($business,$branch,$id),(int)$admin->id); $this->fail(); }
        catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) { $this->assertSame(403,$e->getStatusCode()); }
        $job=$service->createJob($this->data($business,$branch,$id),(int)$owner->id);
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $service->progress($job,(int)$admin->id,'cancelled',false);
    }
    public function test_wrong_vendor_and_unapproved_roster_cannot_dispatch(): void
    {
        $this->enabled();$owner=$this->vendor();[$business,$branch]=$this->business($owner);
        $other=$this->vendor();$service=app(DeliveryService::class);
        [$rider,$id]=$this->partner($service,$owner,$business);
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $service->createJob($this->data($business,$branch,$id),(int)$other->id);
    }
    public function test_scoped_employee_must_have_individual_vendor_grant(): void
    {
        $this->enabled();$owner=$this->vendor();[$business,$branch]=$this->business($owner);
        $employee=$this->user(); $role=Role::firstOrCreate(['code'=>'vendor_employee'],
            ['name'=>'Vendor Employee', 'is_system'=>true]);
        DB::table('employee_assignments')->insert([
            'user_id'=>$employee->id,'role_id'=>$role->id,'business_id'=>$business,
            'branch_id'=>$branch,'assigned_by'=>$owner->id,'starts_at'=>now()->subMinute(),
            'is_active'=>true,'created_at'=>now(),'updated_at'=>now(),
        ]);
        $access=app(VendorDeliveryAccess::class);
        $this->assertFalse($access->allows((int)$employee->id,$business,$branch,'delivery.jobs.manage'));
        $access->grant((int)$owner->id,(int)$employee->id,$business,$branch,'delivery.jobs.manage',true);
        $this->assertTrue($access->allows((int)$employee->id,$business,$branch,'delivery.jobs.manage'));
        $this->assertFalse($access->allows((int)$employee->id,$business,null,'delivery.jobs.manage'));
        $access->grant((int)$owner->id,(int)$employee->id,$business,$branch,'delivery.jobs.manage',false);
        $this->assertFalse($access->allows((int)$employee->id,$business,$branch,'delivery.jobs.manage'));
    }
    public function test_admin_feature_switch_blocks_firm_dispatch_but_does_not_grant_firm_rights(): void
    {
        $this->enabled();$owner=$this->vendor();[$business,$branch]=$this->business($owner);
        $service=app(DeliveryService::class);[$rider,$partner]=$this->partner($service,$owner,$business);
        $admin=$this->admin();
        app(\App\Services\Platform\FeatureRegistry::class)->setEnabled(
            'delivery.vendor_dispatch', false, (int)$admin->id);
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $service->createJob($this->data($business,$branch,$partner),(int)$owner->id);
    }

    public function test_module_disable_blocks_dispatch_service(): void
    {
        $this->enabled(); $owner=$this->vendor();[$business,$branch]=$this->business($owner);
        $service=app(DeliveryService::class);[$rider,$id]=$this->partner($service,$owner,$business);
        app(ModuleRegistry::class)->setEnabled('delivery',false,(int)$this->admin()->id);
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $service->createJob($this->data($business,$branch,$id),(int)$owner->id);
    }
    public function test_firm_member_cannot_be_shared_to_another_business_without_approval(): void
    {
        $this->enabled(); $owner=$this->vendor();[$business,$branch]=$this->business($owner);
        [$otherBusiness,$otherBranch]=$this->business($owner);
        $service=app(DeliveryService::class);[$rider,$id]=$this->partner($service,$owner,$business);
        $this->expectException(ValidationException::class);
        $service->createJob($this->data($otherBusiness,$otherBranch,$id),(int)$owner->id);
    }
}
