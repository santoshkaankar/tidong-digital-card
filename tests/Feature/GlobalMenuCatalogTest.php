<?php

namespace Tests\Feature;

use App\Models\Authorization\Role;
use App\Models\Identity\AccountType;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class GlobalMenuCatalogTest extends TestCase
{
    protected function setUp(): void { parent::setUp(); DB::beginTransaction(); }
    protected function tearDown(): void { if (DB::transactionLevel()>0) DB::rollBack(); parent::tearDown(); }

    private function admin(): User
    {
        $u=User::factory()->create();
        $type=AccountType::firstOrCreate(['code'=>'platform_admin'],['name'=>'Platform Admin','is_active'=>true]);
        $u->userAccountTypes()->create(['account_type_id'=>$type->id,'status'=>'active','activated_at'=>now()]);
        $role=Role::firstOrCreate(['code'=>'super_admin'],['name'=>'Super Admin','is_system'=>true]);
        $u->roleAssignments()->create(['role_id'=>$role->id,'scope_type'=>'platform','scope_id'=>null,'is_active'=>true,'starts_at'=>now()->subMinute()]);
        return $u;
    }

    public function test_global_catalog_is_not_public(): void
    {
        $this->get(route('admin.global-menu.index'))->assertRedirect(route('login'));
    }
    public function test_only_super_admin_creates_atomic_global_items(): void
    {
        $name='Global Test Dish '.(string) \Illuminate\Support\Str::ulid();
        $this->actingAs($this->admin())->post(route('admin.global-menu.store'), ['name'=>$name,'category'=>'South Indian'])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('global_menu_items',['name'=>$name]);
        $this->post(route('admin.global-menu.store'), ['name'=>$name,'category'=>'South Indian'])->assertSessionHasErrors('name');
        $this->post(route('admin.global-menu.store'), ['name'=>'Special Thali','category'=>'South Indian'])->assertSessionHasErrors('name');
        $this->assertDatabaseMissing('global_menu_items',['name'=>'Special Thali']);
    }
    public function test_global_seed_is_safe_to_run_twice(): void
    {
        $seeder=new \Database\Seeders\GlobalMenuCatalogSeeder;
        $seeder->run();$once=DB::table('global_menu_items')->count();
        $seeder->run();
        $this->assertSame($once,DB::table('global_menu_items')->count());
        $this->assertDatabaseMissing('global_menu_items',['name'=>'Thali']);
    }
}
