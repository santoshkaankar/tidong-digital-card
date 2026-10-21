GLOBAL MENU PART 1 - Safe installation for C:\xampp\htdocs\digital

1) Backup (PowerShell in project root):
   Compress-Archive app,routes,resources,tests,database,public ..\digital_before_global_menu_backup.zip -Force
2) Extract this ZIP in project root, after backup:
   Expand-Archive ..\digital-global-menu-master-part1.zip . -Force
3) Confirm testing database before running tests:
   php artisan config:show database.connections.mysql.database --env=testing
   Must show digital_test_db. STOP if any other name.
4) TEST DB migration first:
   php artisan migrate --env=testing --path=database/migrations/2026_10_19_060000_create_global_menu_catalog.php
5) Tests: php artisan test --filter=GlobalMenuCatalogTest
6) If PASS, SQL backup main digital_db with phpMyAdmin.
7) Main DB migration:
   php artisan migrate --path=database/migrations/2026_10_19_060000_create_global_menu_catalog.php
8) OPTIONAL! Seed predesigned example global foods ONLY after approval:
   php artisan db:seed --class=GlobalMenuCatalogSeeder
   Seed is insert-only; existing IDs/names/prices are not overwritten.
9) Super Admin view: http://localhost/digital/public/admin/global-menu

Important: global catalog stores no price; photos are illustrations, not proof of recipe, no Thali/Combo.
This package does NOT change existing orders or POS item rows. No destructive migrations.
Part 2 will add branch reference to this global master and branch-local private products.
