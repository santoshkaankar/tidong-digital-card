TIDONG NEW HOMEPAGE PATCH - 21 OCT 2026

Updated file: resources/views/welcome.blade.php
Additional check: tests/Feature/TidongHomepageTest.php

The Laravel app, routes, migrations, data, module code and permissions are NOT changed.

INSTALL (PowerShell in C:\xampp\htdocs\digital):
1) Copy-Item .\resources\views\welcome.blade.php .\resources\views\welcome.blade.php.before_homepage_backup -Force
2) Expand-Archive -Path "C:\xampp\htdocs\tidong_homepage_patch.zip" -DestinationPath "C:\xampp\htdocs\digital" -Force
3) php artisan view:clear

Move downloaded tidong_homepage_patch.zip to C:\xampp\htdocs first, or change the ZIP path in step 2.

Review homepage at http://localhost/digital/public/ (or the same local domain you normally use).
Do not run migrations. No composer/npm installation required.

NOTE: Guest buttons point to actual login route. Authorized user dashboards use existing named Laravel routes. Modules which only return test text are shown as 'In development'. There are no public checkout/order pages created by this patch. Real behavior still depends on existing module permissions, DB setup and tests on your computer.
