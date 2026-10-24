<?php

namespace Tests\Feature;

use Tests\TestCase;

class ProfessionalWorkTransparencyStructureTest extends TestCase
{
    public function test_professional_work_tracking_routes_have_authentication_and_tenant_guards(): void
    {
        $vendor = app('router')->getRoutes()->getByName('vendor.legal.progress');
        $this->assertNotNull($vendor);
        $this->assertContains('auth', $vendor->gatherMiddleware());
        $this->assertTrue(collect($vendor->gatherMiddleware())->contains(fn ($item) => str_contains($item, 'EnsureVendorAccount')));
        $professional = app('router')->getRoutes()->getByName('legal.professional.work-updates.store');
        $this->assertNotNull($professional);
        $this->assertContains('auth', $professional->gatherMiddleware());
    }

    public function test_professional_write_requires_active_vendor(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/LegalAdvisory/ProfessionalPortalController.php'));
        $this->assertStringContainsString("where('status', 'approved')", $controller);
        $this->assertStringContainsString('where(\'professional_id\', $profile->id)', $controller);
    }

    public function test_work_update_table_migration_exists(): void
    {
        $files = glob(database_path('migrations/*create_legal_work_updates_table.php'));
        $this->assertNotEmpty($files);
    }

    public function test_progress_dashboard_explains_evidence_is_not_verified(): void
    {
        $view = file_get_contents(resource_path('views/vendor/legal-advisory/progress.blade.php'));
        $this->assertStringContainsString('independently verified', $view);
        $this->assertStringContainsString('No work updates', $view);
    }
}
