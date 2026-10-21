<?php

namespace Tests\Feature;

use App\Models\Authorization\Role;
use App\Models\Identity\AccountType;
use App\Models\User;
use App\Services\Platform\FeatureRegistry;
use App\Services\Platform\ModuleRegistry;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/** Requires fully migrated digital_test_db. No destructive RefreshDatabase use. */
class DeliveryModuleAuthorizationTest extends TestCase
{
    protected function setUp(): void { parent::setUp(); DB::beginTransaction(); }
    protected function tearDown(): void
    {
        if (DB::transactionLevel() > 0) DB::rollBack();
        parent::tearDown();
    }

    private function actor(string $account, string $role): User
    {
        $user = User::factory()->create(['email' => bin2hex(random_bytes(7)).'@example.test']);
        $type = AccountType::firstOrCreate(['code' => $account], ['name' => $account, 'is_active' => true]);
        $type->update(['is_active' => true]);
        $user->userAccountTypes()->create(['account_type_id' => $type->id, 'status'=>'active', 'activated_at'=>now()]);
        $roleRecord = Role::firstOrCreate(['code'=>$role], ['name'=>$role, 'is_system'=>true]);
        $user->roleAssignments()->create(['role_id'=>$roleRecord->id, 'scope_type'=>'platform',
            'scope_id'=>null,'is_active'=>true,'starts_at'=>now()->subDay()]);
        return $user;
    }

    public function test_platform_routes_are_technical_only(): void
    {
        foreach (['admin', 'staff-admin', 'employee'] as $prefix) {
            foreach (['modules.index','modules.toggle','modules.features.toggle',
                'delivery.index','employee-permissions.index'] as $suffix) {
                $this->assertTrue(Route::has($prefix.'.'.$suffix));
            }
            $this->assertFalse(Route::has($prefix.'.delivery.jobs.store'));
            $this->assertFalse(Route::has($prefix.'.delivery.partners.status'));
            $this->assertFalse(Route::has($prefix.'.delivery.jobs.cancel'));
        }
        $this->assertTrue(Route::has('vendor.delivery.jobs.store'));
        $this->assertTrue(Route::has('vendor.delivery.partners.status'));
        $this->assertTrue(Route::has('delivery.jobs.status'));
    }

    public function test_super_admin_and_admin_can_control_feature_availability(): void
    {
        $super = $this->actor('platform_admin','super_admin');
        $admin = $this->actor('admin','admin');
        foreach ([['admin',$super], ['staff-admin',$admin]] as [$prefix, $actor]) {
            $this->actingAs($actor)->get(route($prefix.'.modules.index'))->assertOk();
            $this->actingAs($actor)->post(route($prefix.'.modules.features.toggle',
                'delivery.vendor_dispatch'), ['enabled'=>0])->assertRedirect();
            $this->assertDatabaseHas('platform_features', [
                'code'=>'delivery.vendor_dispatch','is_enabled'=>false]);
        }
        $this->assertFalse(app(FeatureRegistry::class)->isEnabled('delivery.vendor_dispatch'));
    }

    public function test_platform_employee_needs_individual_grants(): void
    {
        $admin = $this->actor('admin','admin');
        $employee = $this->actor('platform_employee','admin_employee');
        $this->actingAs($employee)->get(route('employee.modules.index'))->assertForbidden();
        $this->actingAs($employee)->post(route('employee.modules.features.toggle',
            'delivery.vendor_dispatch'), ['enabled'=>0])->assertForbidden();
        $this->actingAs($admin)->post(route('staff-admin.employee-permissions.update'),
            ['user_id'=>$employee->id,'permission_code'=>'modules.view','enabled'=>1])->assertRedirect();
        $this->actingAs($employee)->get(route('employee.modules.index'))->assertOk();
        $this->actingAs($admin)->post(route('staff-admin.employee-permissions.update'),
            ['user_id'=>$employee->id,'permission_code'=>'modules.view','enabled'=>0])->assertRedirect();
        $this->actingAs($employee)->get(route('employee.modules.index'))->assertForbidden();
        $this->assertDatabaseHas('audit_logs',['action'=>'employee.capability.changed','entity_id'=>$employee->id]);
    }

    public function test_employee_cannot_grant_own_permission_or_operate_a_firm(): void
    {
        $employee = $this->actor('platform_employee','admin_employee');
        $this->actingAs($employee)->post(route('employee.employee-permissions.update'),
            ['user_id'=>$employee->id,'permission_code'=>'features.toggle','enabled'=>1])->assertForbidden();
        $this->actingAs($employee)->get(route('vendor.delivery.index'))->assertForbidden();
    }

    public function test_uninstalled_module_cannot_be_registered_and_unknown_feature_cannot_be_toggled(): void
    {
        $admin = $this->actor('admin','admin');
        $this->actingAs($admin)->post(route('staff-admin.modules.register','game'))
            ->assertSessionHasErrors('module');
        $this->actingAs($admin)->post(route('staff-admin.modules.features.toggle','unreviewed.random'),
            ['enabled'=>1])->assertSessionHasErrors('feature');
    }

    public function test_delivery_is_disabled_by_default_before_registration(): void
    {
        $registry = app(ModuleRegistry::class);
        if (DB::table('platform_modules')->where('code','delivery')->exists()) {
            $this->markTestSkipped('Delivery has existing explicitly stored module state.');
        }
        $this->assertFalse($registry->isEnabled('delivery'));
    }
}
