<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AdminNavigationLinksTest extends TestCase
{
    public function test_dashboard_and_branch_review_routes_are_registered_for_each_role(): void
    {
        foreach (['home','admin.branch-changes.index','admin.businesses.index','admin.vendors.index',
                  'staff-admin.home','staff-admin.branch-changes.index',
                  'employee.home','employee.branch-changes.index'] as $name) {
            $this->assertTrue(Route::has($name), "Missing route: {$name}");
        }
    }

    public function test_branch_review_views_have_a_dashboard_and_review_list_link(): void
    {
        foreach (['branch-changes/index.blade.php', 'branch-changes/show.blade.php'] as $file) {
            $source = file_get_contents(resource_path('views/'.$file));
            $this->assertStringContainsString('route($dashboardRoute)', $source);
            $this->assertStringContainsString('route($baseName.', $source);
            $this->assertNotSame('', Blade::compileString($source));
        }
    }
}
