<?php

namespace Tests\Feature;

use App\Services\Platform\ModuleRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class VendorModuleIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_vendor_override_is_isolated_to_one_vendor_and_one_service(): void
    {
        $this->assertTrue(app(ModuleRegistry::class)->installed('sublimation'));
        $this->assertTrue(app(ModuleRegistry::class)->isEnabledForVendor('sublimation', 101));
        $this->assertTrue(app(ModuleRegistry::class)->isEnabledForVendor('sublimation', 102));

        DB::table('vendor_module_overrides')->insert([
            'vendor_id' => 101, 'module_code' => 'sublimation',
            'is_enabled' => false, 'updated_at' => now(),
        ]);

        $this->assertFalse(app(ModuleRegistry::class)->isEnabledForVendor('sublimation', 101));
        $this->assertTrue(app(ModuleRegistry::class)->isEnabledForVendor('sublimation', 102));
        $this->assertTrue(app(ModuleRegistry::class)->isEnabledForVendor('delivery', 101));
    }

    public function test_uninstalled_modules_are_not_available_to_vendors(): void
    {
        $this->assertFalse(app(ModuleRegistry::class)->isEnabledForVendor('marriage_planner', 101));
    }
}
