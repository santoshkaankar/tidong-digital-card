TIDONG — VENDOR MAIN SIDEBAR + DEDICATED BUSINESS SIDEBARS (V1)
Prepared against: source review + Sublimation Studio V1.1 installed source.

KYA NAYA HAI
- Vendor Main Dashboard me persistent responsive dark Sidebar.
- My Businesses me "Open Business Dashboard" link.
- Har Vendor-owned Business ke liye alag dedicated Workspace:
      /vendor/businesses/{business-id}/workspace
- Business switcher me kewal login Vendor ke apne Businesses.
- Restaurant aur Cafe (approved) me branch-specific Counter POS, Menu Library,
  KDS, Table QR aur QR requests ke REAL routes.
- Sublimation / Custom Printing me existing Studio ke Sections:
  Add Product, Pricing, Custom Orders, Job Cards, Design & Production.
- Retail, Wholesale, Distributor, C&F, Manufacturing, Processing, Taxi,
  Marriage Planner aur anya types ke business-specific MENU DEFINITIONS.
  Inke jo backend modules abhi nahin bane hain, woh "Planned" hain,
  unke broken ya imaginary links nahin banaye gaye.
- Branch selector; incorrect/foreign branch id -> 404.
- Doosre Vendor ka Business kholne par -> 404. Platform staff blocked by existing middleware.
- Business approval aur Employee Permissions purane backend ke anusar.

IMPORTANT
- Ye NAVIGATION package hai. Vendor Employee delegation / internal change
  approvals abhi implemented NAHIN hain. Is package se koi new staff rights nahin milte.
- No new migrations. No registration changes. No LIVE deployment. No pricing changes.
- Existing Pending Businesses do not get POS ordering enabled.
- Existing routes/pos.php, routes/kds.php, routes/web.php are not modified.

FILES (all paths relative to C:\xampp\htdocs\digital)
Modified:
  routes/vendor.php
  resources/views/vendor/dashboard.blade.php
  resources/views/vendor/sublimation/index.blade.php
  app/Http/Controllers/Vendor/SublimationStudioController.php
New:
  app/Http/Controllers/Vendor/BusinessWorkspaceController.php
  config/tidong_business_navigation.php
  resources/views/vendor/business-workspace.blade.php
  resources/views/vendor/partials/sidebar.blade.php
  tests/Feature/VendorBusinessSidebarTest.php

SAFE INSTALL SEQUENCE (do NOT jump to LIVE)
1. Make source backup if no fresh backup exists:
   Compress-Archive -Path app,routes,resources,config,tests -DestinationPath ..\digital_before_business_sidebars.zip -Force
2. Review the ZIP contents:
   tar -tf ..\tidong-vendor-business-sidebars-v1.zip
3. Extract to LOCAL project root ONLY:
   Expand-Archive ..\tidong-vendor-business-sidebars-v1.zip . -Force
4. Refresh view/config caches (NO database change):
   php artisan view:clear
   php artisan config:clear
5. Confirm testing connection is digital_test_db:
   php artisan config:show database.connections.mysql.database --env=testing
   Must say digital_test_db. STOP if not correct.
6. Test navigation & overall Laravel:
   php artisan test --filter=VendorBusinessSidebarTest
   php artisan test
7. Test LOCAL browser using Vendor login:
   http://localhost/digital/public/vendor
   On any Business click Open Business Dashboard.
   Sublimation Business -> Open Sublimation Studio.

ROLLBACK: Restore above source backup. No migration in this package.
No force push, GitHub commit, Render deployment or Supabase modification is part of the ZIP.
