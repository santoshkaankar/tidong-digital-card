# Grocery V2 — New, Upgraded and Shared Files

## If V1 was not installed
- All original Grocery V1 Files from this bundle are added (controllers/services/feature switches/migration/seeders/tests).
- V2 adds: customer shopping controller, vendor order desk/presentation controllers, checkout/inventory allocation service, 1 additive migration, public shopping routes, Grocery customer views and new CSS, English translation catalogue (`lang/en/grocery.php`), Grocery online tests.
- Master Laravel Shared changes are **anchored insertions**, not full overwrite:
  - `routes/web.php` → `routes/grocery_shop.php` require
  - `routes/vendor.php` → `routes/grocery_vendor.php` require (V1)
  - `routes/admin.php` → `routes/grocery_admin.php` require (V1)
  - `config/tidong_business_navigation.php` → Grocery business nav group and Online Shopping/Orders items
  - `resources/views/vendor/partials/sidebar.blade.php` → V1 Grocery group-aware links
  - `resources/views/vendor/business-workspace.blade.php` → V1 Grocery entry
  - `resources/views/layouts/admin.blade.php` → Grocery switches link
  - `resources/views/customer/dashboard.blade.php` → Grocery Shopping link when enabled

## If V1 was already installed
- All **existing Grocery V1 files preserved** except specifically three version-verified files:
  - `app/Services/Grocery/GroceryFeatures.php` → add 2 admin flags
  - `routes/grocery_vendor.php` → add orders/presentation routes
  - `resources/views/vendor/grocery/index.blade.php` → mobile-friendly order entry + multi-product POS + presentation editor
- All other V2 module files are newly added; missing shared routes are inserted once.
- Unexpected changes in the 3 V1 files → installer STOP; request manual Master merge.

## Safety
Installer runs file-only operations with preflight checks. No data migrations, no database/remote connections, no Git, no deployments, no model/schema overwrite. No `vendor` dependencies bundled. `README_HINDI.md` explains approval gates.
