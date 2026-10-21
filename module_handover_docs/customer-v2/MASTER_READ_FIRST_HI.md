# TIDONG Customer V2 — App-style Home + Safe Business Directory
20 October 2026 | **MASTER REVIEW PATCH ONLY** | No installation performed.

## IMPORTANT: dependency
This ZIP is **V2 delta relative to `TIDONG_Customer_Module_V1_MASTER_REVIEW_2026-10-20.zip`**, NOT a complete standalone customer module. Master Chat must first verify and integrate V1 safely. This patch has 14 files: existing V1 customer files are changed in place, and new files are added. **Do not blindly overwrite current live code or merge another module's pending changes.** If V1 is not yet installed, review the V1 package and then apply the V2 changes in sequence.

## Features actually delivered
- Responsive app-style customer Home, desktop sidebar + mobile bottom navigation, search-ready Explore and My Orders information screen; English UI wrapped in `__()` for future localization.
- Original **red symbol + black `Tidong®` official logo** included unchanged in `public/images/brand/tidong-official-logo.jpg` (source: user Library official logo). No fake branding generated.
- Service icon tiles: Sublimation, Restaurant, Grocery, Delivery, Wallet, Billing. Grocery/Delivery/Wallet/Billing tiles clearly show **Not available yet**, not usable integrations.
- Sublimation / Restaurant business directory is READ ONLY and lists a business only if **vendor approved, business approved + accepting orders, and at least one active branch accepting orders**. A paused/rejected/unapproved store is not displayed. Does not leak vendor email, phone, internal orders, branch secrets or staff data. SQL lookups use Eloquent parameter binding, constrained business types and pagination.
- Search limited to 70 characters; unauthorized/disabled route guards reused from V1. No payment, cart, checkout, invoice, voice-order, QR-order or live-order connection is claimed.
- My Orders page explicitly says that POS, guest QR and studio orders **are not linked to the current user yet**. Nothing is inferred using mobile numbers or names.
- V1 profile, address book, preferences, password security and audit stay **unchanged**. Customer account identity remains distinct from business tenant.
- CSS is a standalone small public asset, no npm/build change; no new dependencies or migrations.

## Files that modify V1 (diff carefully)
- `routes/customer.php`
- `app/Http/Controllers/Customer/CustomerDashboardController.php`
- `resources/views/customer/layout.blade.php`
- `resources/views/customer/dashboard.blade.php`

## New files
- `app/Http/Controllers/Customer/CustomerExploreController.php`
- `app/Http/Controllers/Customer/CustomerOrdersController.php`
- `app/Services/Customer/CustomerMarketplace.php`
- `config/tidong_customer_services.php`
- `resources/views/customer/explore.blade.php`
- `resources/views/customer/directory.blade.php`
- `resources/views/customer/orders.blade.php`
- `public/css/tidong-customer-v2.css`
- `public/images/brand/tidong-official-logo.jpg`
- `tests/Feature/CustomerMarketplaceV2Test.php`

## Installation only in Master Integration Chat
1. Confirm V1 is installed AND all its Customer tests pass in isolation, or integrate V1 first after reviewing its migration safely.
2. Backup project and `digital_test_db`, and review the current versions of the four V1-modified files. Diff/merge; never overwrite unseen new changes from other chats. No ZIP copy into main project before master review.
3. Extract new files to matching paths; V2 has **no new migration**. Do not run `migrate:fresh`, `db:wipe`, or production deployment.
4. Verify actual Laravel test env configured only for pre-migrated `digital_test_db`. Run **only after master confirms**: `php artisan test --filter=CustomerMarketplaceV2Test`. These 7 tests use rollback transactions and guard against `digital_db`/cloud targeting. They do NOT recreate tables. Running V1/full suite requires separate safe-db confirmation because V1 uses RefreshDatabase; do not run destructively on user data.
5. Browser QA: customer home `/customer`, `/customer/explore`, `/customer/explore/sublimation`, `/customer/explore/restaurant`, `/customer/orders`; mobile widths 320, 375, 768px, desktop 1280px; verify logo and navigation.
6. Negative QA: guest -> login; inactive customer 403; module disabled 403; pending vendor/business or closed branch **invisible**; unknown services return 404; input validation; no orders linked by matching names/phones.
7. Do not touch `digital_db`, Supabase, GitHub main/Render until explicit user authorization and verified test results.

## Future integration boundaries
- Sublimation vendor studio is an INTERNAL job system, not customer checkout. Connect it only after dedicated customer order identity, verified tenancy, business/branch policy and vendor approval workflow are designed and reviewed.
- Restaurant POS/QR guest orders likewise cannot be shown as customer's without a secure binding; never search orders by phone/name to guess ownership.
- Grocery/Delivery/Wallet/Billing teams own their module APIs; customer consumes explicit reviewed contracts later. Every service must obey central admin module on/off and vendor/branch permissions; config allow-list is only a temporary customer directory gate, not a substitute for future central admin controls.
- No guaranteed delivery speed, order tracking, offers, tax amount or availability claims are made. Production module activation remains vendor-owned and admin-review-controlled.

## Validation that WAS performed
- `php -l` on all 11 PHP/Blade files in package: syntax passes (PHP parser cannot validate Blade template directives).
- ZIP sha256 manifest created; file list verified; no secrets/environment/database dumps.
- **Laravel feature tests authored (7), NOT executed** here because source attachment lacks a runnable framework, vendor deps, artisan and isolated database. End-to-end app and test success cannot be claimed until Master runs them.
