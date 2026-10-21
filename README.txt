DIGITAL NAVIGATION FIX - PART 2 (Staff Admin / Employee / Vendor)
=================================================================
CHANGED FILES (5 views, 1 test):
- resources/views/staff-admin/dashboard.blade.php
- resources/views/employee/dashboard.blade.php
- resources/views/vendor/dashboard.blade.php
- resources/views/vendor/branch-edit.blade.php
- resources/views/vendor/branch-licenses.blade.php
- tests/Feature/RoleDashboardNavigationTest.php

RESULT:
Staff Admin/Employee dashboard links show only when the signed-in user has matching permission.
Vendor Dashboard gets POS and KDS shortcuts for approved Vendors.
Vendor's Branch Edit and GST/FSSAI/Licence pages can switch to each other or go to Vendor Dashboard.
This package DOES NOT grant new permissions or allow platform staff to access vendor POS.
No routes, controllers, migrations, or database changes.

INSTALL AFTER Part 1, and only after backing up current files.
From PS C:\xampp\htdocs\digital>:
Expand-Archive ..\digital-role-dashboard-navigation-part2.zip . -Force
php artisan view:clear
php artisan test --filter=RoleDashboardNavigationTest

Open /staff-admin, /employee and /vendor with matching test accounts.
Run the full suite 'php artisan test' only after reconfirming --env=testing mysql/digital_test_db.
All UI labels are English for current project requirements.
