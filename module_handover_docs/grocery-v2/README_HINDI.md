# TIDONG Grocery — Online Store + Vendor Operations V2.0

**19 October 2026 | Grocery module ONLY | Master integration review required**

यह ZIP मौजूदा `C:\xampp\htdocs\digital` Laravel application में Grocery जोड़ने/पुराने Grocery V1 को **safe upgrade** करने के लिए है। अलग Laravel Project नहीं। Website/UI English, प्रारम्भिक `lang/en/grocery.php` translation keys future i18n के लिए; शेष UI strings का full localization future release में होगा। निर्देश हिंदी में। यह V1 की सभी सुविधाएँ साथ रखता है और V2 की Customer Shopping जोड़ता है।

## 1. V2 में वास्तव में क्या काम करता है

### Customer Web Experience
- `/grocery`: Approved + active grocery stores की खोज (name/city), branch चयन, 24/page pagination।
- `/grocery/{business}/{branch}`: Mobile-friendly product cards, responsive layout, category chips, product/brand search, featured items, sorting, MRP + online sale prices (fallback to offline), product details, stock-verified listings।
- Session cart: quantity edit/remove/clear, 40 unique product limit, cross-store cart mixing blocked, estimated GST and payable breakdown।
- **Authorized Customer Only** checkout: pickup या **delivery arrangement request** (NOT actual dispatch), contact/address validation, order number, idempotent request token।
- Tax/price snapshot, atomic stock allocation, FEFO lots, no expired lots, current prices revalidated at submit; original order items retained।
- Customer "My Orders": status timeline, order summary, pending-order cancellation with exact stock restoration।
- Payments **not collected**. `pay_at_handover` is a request label, never proof of money received.

### Vendor Grocery Workspace
- Grocery V1: products, barcode/SKU, pricing, HSN/GST, batch/expiry receipt, stock ledger, reversal-preserving counter sales, reports, branch feature controls।
- Multi-item Counter POS UI: up to 30 distinct items; optional keyboard-style barcode scanner (no scale/printer driver).
- Product storefront fields: brand, description, optional trusted HTTPS image URL, featured flag.
- Grocery Online Order Desk: list/detail, approve/confirm → packing → ready → completed, reject pending/confirmed with exact stock restoration, append-only status events.
- Single vendor-owner access; **Employee delegation is NOT added** without Master Permission Review.

### Super Admin
- `Grocery Module`, `Products`, `Pricing`, `Inventory`, `Counter Billing`, `Reports`, `Customer Online Store`, `Customer Order Checkout / Processing`: **8 individually controlled flags**.
- New V2 `online_store` and `online_orders` flags start **OFF**. Grocery V1 flags preserve previous state when upgrading.
- Admin OFF overrides Vendor branch preference; backend does not trust hidden navigation links.

## 2. Safety Boundaries

- **One existing Laravel application.** Nothing runs migrations, artisan commands, live deployment, payments, backups removal or `git push` automatically.
- Doesn't change Restaurant, Wallet, Delivery, Affiliate or Sublimation logic. Two shared master routes `web.php`, `vendor.php`, one `admin.php`, navigation/sidebar/workspace, customer dashboard are patched only if expected anchors match.
- Installer refuses to overwrite unknown changes in existing Grocery V1 files. For matching original V1 only, the 3 changed files (feature labels, vendor Grocery Blade screen, vendor Grocery route file) upgrade with local backup.
- All recorded sales, online orders, stock movements, status events, sale snapshots remain preserved. Cancellation/rejection posts compensating stock entries; no row deletes.
- All new customer orders require **already authenticated authorized Customer account**; public registration is NOT opened here. Shop browsing and session cart do not require login.
- All new online checkout needs approved vendor/business/active branch/accepting orders plus flags; no public store checkout when OFF.
- Local MySQL / PostgreSQL-portable Laravel schema and query patterns; **production compatibility unverified without actual DB tests**.

## 3. File-only Integration, first DRY RUN

**आप Coding नहीं जानते, इसलिए बिना Master Chat से Review के नीचे `--apply` या DB commands न चलाएँ।** पहले `C:\xampp\htdocs\digital` का source + DB backup, latest pending patches और previous Grocery V1 status Master Chat में confirm करवाएँ।

1. इस ZIP को `C:\xampp\htdocs\grocery-v2-review` में Extract करें। उसमें सीधे `tools\install_grocery.py` होना चाहिए।
2. Windows CMD (PowerShell नहीं) में:

```bat
cd /d C:\xampp\htdocs\grocery-v2-review
py tools\install_grocery.py --project "C:\xampp\htdocs\digital" --check
```

3. Dry-run list Master Integration Chat में साझा करें। अगर `BLOCKED` लिखा आए तो **files copy/overwrite न करें**। अगर Master Team approve करे, **केवल files integrate** करने का command:

```bat
py tools\install_grocery.py --project "C:\xampp\htdocs\digital" --apply
```

- `--apply` पुराने shared/upgrade files का backup `.grocery_install_backups\<timestamp>` में रखता है; additive new files कॉपी करता है। Repeat dry-run zero-change होता है।
- **Application migrations अपने आप नहीं चलतीं**।
- ZIP में `.env`, passwords, private credentials, customer data या DB backups नहीं।

## 4. DATABASE MIGRATIONS — only after Master approval

`digital_test_db` के लिए `.env.testing` connection के exact database name की पुष्टि जरूरी है। **Main `digital_db`, Render, Supabase पर टेस्ट मत चलाइए**। Installer दोनों Grocery migration files रखेगा:

```
database/migrations/2026_10_20_000000_create_grocery_module_tables.php
database/migrations/2026_10_20_010000_add_grocery_online_store_and_orders.php
```

यदि पहले V1 migration **उसी test DB** में लग चुकी है, तो उसे फिर से run नहीं करना चाहिए। Laravel migration repository इसे पहचानती है। नई additive V2 migration customer orders, status events, allocations, product display metadata और V2 OFF switches बनाती है। Rollback intentionally blocked due financial/stock retention.

**Only after checking `.env.testing` points to `digital_test_db` and Master Chat specifically approves:**

```bat
cd /d C:\xampp\htdocs\digital
php artisan migrate:status --env=testing
php artisan migrate --env=testing --path=database/migrations/2026_10_20_000000_create_grocery_module_tables.php
php artisan migrate --env=testing --path=database/migrations/2026_10_20_010000_add_grocery_online_store_and_orders.php
php artisan test --env=testing --filter=Grocery
```

फिर पूरे Laravel test suite का result verify करें। बिना approval `migrate:fresh`, `db:wipe`, `rollback`, live push / deploy **न करें**। V2 integration and testing must be reviewed before even local MAIN database migrations.

## 5. First use (after installation + test/approvals)

1. Admin → **Grocery Feature Controls**: needed main, catalogue, pricing, inventory switches allow करें; online store and online orders को public testing से पहले separately approve करें। Default OFF रखना सुरक्षित है।
2. Vendor → Create Grocery business and at least one branch; platform onboarding approval and accepting-orders status required. Verify Vendor branch state.
3. Vendor Grocery Workspace: product SKU/barcode/category/unit/HSN/GST/MRP/sale, receive fresh stock lot, activate product, optionally add brand/image URL.
4. Admin ON after security/UX test: `/grocery` से store select, product browse, basket, already authorized customer login, pickup/assisted delivery request.
5. Vendor Grocery → **Review Online Orders**: confirm/pick/ready/complete. Reject earlier pending/confirmed → reserved stock restored with append-only movement.
6. Customer `/grocery/my-orders`: verify pending/confirmed/status and cancel only pending.
7. Test Admin OFF at runtime and cross-business/branch permissions. Check all order snapshots and movements. Then Master integration approval.

## 6. Known Limitations / Production Release Gates

**This is a substantial Grocery V2 functional implementation, not a claim of a certified/finished live international-commerce platform.** Without additional Master-approved integrations, these are NOT implemented: real payment gateway, wallet/affiliate settlement, final GST-compliant invoice/tax rules, online refunds, automated courier dispatch/live tracking, delivery fee/slot confirmation, tax registration/FSSAI compliance review, vendor employee POS delegation, discount coupons/loyalty, purchase order/supplier accounting, paid/unpaid ledger settlement, push/SMS/email notifications, product photo uploads, physical weighing scale/printer integrations, advanced SEO/international translation, shipping geography restrictions, personalized ranking or real-time address verification. Delivery request may be accepted only manually; it does not create a Delivery-module job.

If `online_orders` flag is OFF, Vendor processing pauses as well. Customer pending-order cancellation still works to avoid blocking a refund-less, unpaid stock reservation. Turning OFF `online_store` blocks new shopping/checkout but does not erase historical online orders or halt Vendor processing if `online_orders` remains enabled.

External HTTPS product photos are sourced in buyer browsers only, not downloaded server-side. Vendor should use trusted/licensed image hosts and privacy-compliant image policy. Do not use placeholders implying stock/data ownership. Sensitive customer names/phone/address should be reviewed under platform retention/privacy policy before production.

## 7. Integration report for Master Chat

Read `docs/CHANGED_FILES.md`, `docs/ROUTES_PERMISSIONS.md`, `docs/TEST_PLAN.md`, plus `SHA256SUMS.txt`. Send dry-run and Laravel test results to Master Chat. No live deploy from this module chat.
