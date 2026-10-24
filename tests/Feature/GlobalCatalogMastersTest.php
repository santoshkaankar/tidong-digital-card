<?php
namespace Tests\Feature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
class GlobalCatalogMastersTest extends TestCase {
    use RefreshDatabase;
    public function test_global_catalog_master_tables_are_created(): void {
        foreach (['brands','item_categories','units','variant_attributes','variant_values'] as $type) {
            $this->assertTrue(\Illuminate\Support\Facades\Schema::hasTable('global_catalog_'.$type));
        }
    }
    public function test_unauthenticated_user_cannot_create_global_master(): void {
        $this->post('/admin/global-masters/brands',['name'=>'Sample Brand'])->assertRedirect();
        $this->assertSame(0,DB::table('global_catalog_brands')->count());
    }
}
