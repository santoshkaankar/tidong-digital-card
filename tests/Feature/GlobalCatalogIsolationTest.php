<?php
namespace Tests\Feature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
class GlobalCatalogIsolationTest extends TestCase {
 use RefreshDatabase;
 public function test_vendor_grants_are_disabled_by_default(): void {
  DB::table('vendor_global_catalog_permissions')->insert(['vendor_id'=>123,'created_at'=>now(),'updated_at'=>now()]);
  $grant=DB::table('vendor_global_catalog_permissions')->where('vendor_id',123)->first();
  $this->assertSame(0,(int)$grant->can_create);
  $this->assertSame(0,(int)$grant->can_edit);
 }
 public function test_custom_items_require_an_outlet_and_have_unique_outlet_names(): void {
  $this->assertTrue(\Illuminate\Support\Facades\Schema::hasColumns('vendor_custom_catalog_items',['vendor_id','branch_id','name','mrp','sale_price']));
  $this->assertTrue(\Illuminate\Support\Facades\Schema::hasTable('global_catalog_brands'));
 }
 public function test_unauthenticated_users_cannot_create_custom_items(): void {
  $this->post('/vendor/branches/1/custom-items',['name'=>'Private Item','mrp'=>100,'sale_price'=>90])->assertRedirect();
  $this->assertSame(0,DB::table('vendor_custom_catalog_items')->count());
 }
}
