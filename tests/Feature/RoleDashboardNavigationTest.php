<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RoleDashboardNavigationTest extends TestCase
{
    public function test_dashboard_and_vendor_navigation_routes_exist(): void
    {
        foreach (['staff-admin.home','staff-admin.vendors.index','staff-admin.businesses.index',
                  'staff-admin.branch-changes.index','employee.home','employee.vendors.index',
                  'employee.businesses.index','employee.branch-changes.index','employee.finance.index',
                  'vendor.home','vendor.branch.edit','vendor.branch.licenses.index',
                  'pos.home','kds.home'] as $name) {
            $this->assertTrue(Route::has($name), "Missing route: {$name}");
        }
    }

    public function test_role_dashboard_links_are_permission_guarded_and_blade_compiles(): void
    {
        foreach (['staff-admin/dashboard.blade.php','employee/dashboard.blade.php'] as $file) {
            $source = file_get_contents(resource_path('views/'.$file));
            $this->assertStringContainsString("hasPlatformPermission('approvals.view')", $source);
            $this->assertStringContainsString("hasPlatformPermission('vendors.view')", $source);
            $this->assertNotSame('', Blade::compileString($source));
        }
        // The new Vendor Dashboard renders navigation through a shared Sidebar partial.
        // Confirm both the inclusion and the actual link, rather than expecting the link
        // to be duplicated in dashboard.blade.php.
        $vendorDashboard = file_get_contents(resource_path('views/vendor/dashboard.blade.php'));
        $this->assertStringContainsString("@include('vendor.partials.sidebar'", $vendorDashboard);
        $this->assertNotSame('', Blade::compileString($vendorDashboard));

        $vendorSidebar = file_get_contents(resource_path('views/vendor/partials/sidebar.blade.php'));
        $this->assertStringContainsString("route('vendor.home')", $vendorSidebar);
        $this->assertNotSame('', Blade::compileString($vendorSidebar));

        // Branch sub-pages still provide an explicit back-to-Vendor-Dashboard link.
        foreach (['vendor/branch-edit.blade.php', 'vendor/branch-licenses.blade.php'] as $file) {
            $source = file_get_contents(resource_path('views/'.$file));
            $this->assertStringContainsString("route('vendor.home')", $source);
            $this->assertNotSame('', Blade::compileString($source));
        }
    }
}
