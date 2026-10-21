BRANCH MENU PART 2 - Requires Part 1 already installed and migrated.

1) In C:\xampp\htdocs\digital, after Part 1 backup and tests:
   Expand-Archive ..\digital-branch-custom-menu-part2.zip . -Force
2) Check testing DB again before running tests:
   php artisan config:show database.connections.mysql.database --env=testing
   Must show digital_test_db, NOT digital_db.
3) TEST DB migration:
   php artisan migrate --env=testing --path=database/migrations/2026_10_19_070000_add_global_item_reference_to_pos_menu_items.php
4) Tests:
   php artisan test --filter=BranchMenuCatalogWorkflowTest
   php artisan test
5) If PASS and AFTER main database backup:
   php artisan migrate --path=database/migrations/2026_10_19_070000_add_global_item_reference_to_pos_menu_items.php
6) php artisan view:clear
7) To show vendor-uploaded photos via /storage URL, run once:
   php artisan storage:link
8) Vendor opens POS > Branch > Browse Global Items / Branch Custom Menu & Photos.

- Global Master lives in its own table; each Branch chooses its own price.
- Existing pos_menu_items are retained and default to source_kind=custom; all existing orders are unchanged.
- Each Branch can create private items and upload its own photo.
- No Thali/Combo/merge formula in this package.
- Approved Vendor and approved Business are required to ADD menu items; Branch order-taking remains OFF until explicitly enabled.
- Photos use public storage: use no secret or personal content in them.
- This package does not seed any branch prices or auto-enable ordering.
