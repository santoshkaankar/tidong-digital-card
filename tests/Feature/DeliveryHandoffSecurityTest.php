<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Authorization\Role;
use App\Models\Identity\AccountType;
use App\Services\Delivery\DeliveryService;
use App\Services\Platform\ModuleRegistry;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

/** Requires migrated isolated digital_test_db; never resets production data. */
class DeliveryHandoffSecurityTest extends TestCase
{
    use \Tests\Support\ApprovesDeliveryPartnerForTest;
    protected function setUp(): void { parent::setUp(); DB::beginTransaction(); }
    protected function tearDown(): void
    {
        if (DB::transactionLevel() > 0) DB::rollBack();
        parent::tearDown();
    }
    private function identity(string $account, string $role): User
    {
        $user = User::factory()->create(['email' => bin2hex(random_bytes(8)).'@example.test']);
        $type = AccountType::firstOrCreate(['code'=>$account], ['name'=>$account,'is_active'=>true]);
        $type->update(['is_active'=>true]);
        $user->userAccountTypes()->create(['account_type_id'=>$type->id, 'status'=>'active','activated_at'=>now()]);
        $r = Role::firstOrCreate(['code'=>$role], ['name'=>$role,'is_system'=>true]);
        $user->roleAssignments()->create(['role_id'=>$r->id, 'scope_type'=>'platform',
            'scope_id'=>null, 'is_active'=>true, 'starts_at'=>now()->subMinute()]);
        return $user;
    }
    private function job(): array
    {
        $admin = $this->identity('platform_admin','super_admin');
        $registry = app(ModuleRegistry::class);
        if (!DB::table('platform_modules')->where('code','delivery')->exists()) {
            $registry->register('delivery', (int) $admin->id);
        }
        $registry->setEnabled('delivery', true, (int)$admin->id);
        $vendor = $this->identity('vendor','vendor_owner');
        $uid = DB::table('vendors')->insertGetId([
            'user_id'=>$vendor->id,'vendor_code'=>'PIN-'.bin2hex(random_bytes(6)),
            'display_name'=>'PIN Safety Vendor','status'=>'approved',
            'created_at'=>now(),'updated_at'=>now(),
        ]);
        $business = DB::table('businesses')->insertGetId([
            'vendor_id'=>$uid, 'business_type'=>'restaurant','name'=>'Safety Test',
            'slug'=>'pin-'.bin2hex(random_bytes(6)), 'status'=>'approved',
            'created_at'=>now(),'updated_at'=>now(),
        ]);
        $branch = DB::table('branches')->insertGetId([
            'business_id'=>$business,'name'=>'Safety Branch', 'branch_code'=>'PIN'.bin2hex(random_bytes(4)),
            'status'=>'active', 'created_at'=>now(),'updated_at'=>now(),
        ]);
        $rider = User::factory()->create();
        AccountType::firstOrCreate(['code'=>'delivery'], ['name'=>'Delivery','is_active'=>true]);
        Role::firstOrCreate(['code'=>'delivery_partner'], ['name'=>'Delivery Partner','is_system'=>true]);
        $delivery = app(DeliveryService::class);
        $partnerId = $delivery->inviteToBusiness($business,(int)$rider->id,'bike',null,(int)$vendor->id);
        $delivery->verifyForBusiness($business,$partnerId,(int)$vendor->id);
        $this->approvePlatformDeliveryForTest($partnerId);
        $delivery->setRosterStatus($business,$partnerId,'active',(int)$vendor->id);
        app(\App\Services\Platform\FeatureRegistry::class)->setEnabled('delivery.partner_progress', true, (int)$admin->id);
        $delivery->setAvailability((int)$rider->id, true);
        $jobId = $delivery->createJob([
            'business_id'=>$business,'branch_id'=>$branch,'partner_id'=>$partnerId,
            'recipient_name'=>'Recipient','recipient_phone'=>'9999999999',
            'dropoff_address'=>'Safe test address in Kota, India',
        ], (int)$vendor->id);
        $pin = $delivery->takeHandoffPin();
        $delivery->progress($jobId,(int)$rider->id,'accepted',true);
        $delivery->progress($jobId,(int)$rider->id,'picked_up',true);
        return [$delivery,$vendor,$rider,$jobId,$pin];
    }
    public function test_five_wrong_pins_persist_and_block_even_correct_pin(): void
    {
        [$delivery,$vendor,$rider,$jobId,$pin] = $this->job();
        $wrong = $pin === '000000' ? '111111' : '000000';
        for ($n=1; $n<=5; $n++) {
            try {
                $delivery->progress($jobId,(int)$rider->id,'delivered',true,
                    ['handoff_pin'=>$wrong,'recipient_name'=>'Recipient']);
                $this->fail('Invalid PIN should fail.');
            } catch (ValidationException $e) {
                $this->assertTrue(true);
            }
        }
        $job = DB::table('delivery_jobs')->where('id',$jobId)->first();
        $this->assertSame(5,(int)$job->handoff_failed_attempts);
        $this->assertNotNull($job->handoff_locked_until);
        $this->assertDatabaseHas('delivery_jobs',['id'=>$jobId,'status'=>'picked_up']);
        $this->expectException(ValidationException::class);
        $delivery->progress($jobId,(int)$rider->id,'delivered',true,
            ['handoff_pin'=>$pin,'recipient_name'=>'Recipient']);
    }
    public function test_vendor_reissue_invalidates_old_pin_and_resets_lock(): void
    {
        [$delivery,$vendor,$rider,$jobId,$pin] = $this->job();
        $delivery->reissuePin($jobId,(int)$vendor->id);
        $newPin = $delivery->takeHandoffPin();
        $this->assertMatchesRegularExpression('/^[0-9]{6}$/',$newPin);
        $this->assertNotSame($pin,$newPin); // One in a million collision possible: reissue must avoid same PIN.
        try {
            $delivery->progress($jobId,(int)$rider->id,'delivered',true,
                ['handoff_pin'=>$pin,'recipient_name'=>'Recipient']);
            $this->fail('Old PIN must not work.');
        } catch (ValidationException $expected) { $this->assertTrue(true); }
        $delivery->progress($jobId,(int)$rider->id,'delivered',true,
            ['handoff_pin'=>$newPin,'recipient_name'=>'Recipient']);
        $this->assertDatabaseHas('delivery_jobs',['id'=>$jobId,'status'=>'delivered','handoff_failed_attempts'=>0]);
    }
    public function test_platform_admin_cannot_reissue_firm_pin(): void
    {
        [$delivery,$vendor,$rider,$jobId,$pin] = $this->job();
        $admin = $this->identity('platform_admin','super_admin');
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $delivery->reissuePin($jobId,(int)$admin->id);
    }
}
