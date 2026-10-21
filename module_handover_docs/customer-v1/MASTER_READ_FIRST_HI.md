# TIDONG Customer Module V1 — Master Review Package
Created: 20 October 2026 | Purpose: OFFLINE REVIEW / STAGING integration only.

## पहले यह पढ़ें
यह ZIP **पूरा Laravel project नहीं है**, केवल Customer module में बदली/नई फाइलें हैं।
INSTALL, MIGRATE, TEST, DEPLOY इस चैट में **कुछ भी नहीं किया गया**।
Master Integration Chat ही सभी merge और installation संभाले।

**महत्वपूर्ण सुरक्षा नियम:** Existing source changes को न मिटाएँ। ZIP की existing files को सीधा overwrite न करें; पहले current master source से **diff/merge** करें।
`digital_db` या live Supabase/Render पर tests/migrations न चलाएँ। `.env`, user data, SQL backup ZIP में नहीं हैं।

## Delivered (V1)
1. Existing authenticated `customer` dashboard + navigation preserved and expanded.
2. Customer name/email/phone profile; email change requires CURRENT password and resets email verification status.
3. Separate private address book CRUD, default address, max 10; EVERY lookup `auth user -> customerAddresses()` scoped.
4. Preferences: preferred locale (English/Hindi stored; UI translation phase pending), optional marketing opt-in default false.
5. Change password with current-password validation; password hashed by existing User model; remember token rotated.
6. Customer activity audit events (profile, address, preferences, password) stored without body/password/address contents; recent activity visible to own customer.
7. Customer module enable/disable **config** flag; registration default **OFF** for GET and POST, even if user knows the endpoint.
8. Rate limits on sensitive customer mutation routes.
9. Feature test file for registration lock, role guard, tenant isolation, default address, validation, password/profile/preferences and audit.

## Explicitly NOT delivered
- Customer order history, order tracking, cart, checkout, payments, invoice, vendor access to customer PII: EXISTING POS/STUDIO ORDERS DO NOT LINK TO CUSTOMER ACCOUNTS. Do not fabricate linkage.
- Admin dashboard toggle UI (currently environment-configurable ON/OFF only); central admin UI requires separate reviewed integration.
- OTP/email verification service, forgot password, multi-factor authentication, logout-all-active-sessions, fraud prevention, email/send notifications.
- Full translated UI (text is written with `__()` for future locale support, preference is stored only).
- Automatic account recovery and compliance/legal policies (consent/privacy and data retention require legal review).

## Files / merge conflicts
See `FILE_MANIFEST_SHA256.txt` and `CHANGE_SUMMARY.txt` for exact paths. Source used: `tidong-master-source-2026-10-19(7).zip` snapshot.
Original ZIP had Windows backslash paths; this package has NORMAL Laravel forward-slash paths. Migration destination is `database/migrations/` (not snapshot `migrations/`).

## Safe integration sequence for MASTER CHAT
1. Take verified project and **digital_test_db** backups; diff source vs uploaded snapshot (especially `routes/web.php`, `routes/customer.php`, `app/Models/User.php`, existing Auth controller and customer middleware, login/register views).
2. Review all changed files and migration. Merge existing-file changes manually where current master differs; preserve installed mods and all old routes.
3. Confirm `.env.testing` points exclusively at `digital_test_db` (or isolated in-memory SQLite). The feature test contains a safety preflight guard against refreshing wrong DB.
4. Apply the ADDITIVE migration ONLY to the explicitly authorized TEST database (do not use `migrate:fresh` on any user database). Do NOT touch main database or production without approval and backup.
5. Run `php artisan test --filter=CustomerAccountModuleTest` against test DB, then the complete existing Laravel test suite, role/access review, browser smoke tests and MySQL/Postgres compatibility checks. RUN LOG RESULTS MUST BE CONFIRMED; none are claimed here.
6. Browser check: GET `/register` -> 404 default, POST `/register` -> 404 default; existing authorized customer can login; edit profile/preferences, add/change/delete address, change password. Unassigned accounts -> 403. Check no public signup link in login UI.
7. Vendor/POS/studio and existing records must remain unaffected. Check privacy and rate limits under real framework.
8. Only after QA and explicit authorization, schedule main DB migration/deployment using reviewed deployment process. Don't push GitHub main: Render auto-deploy is connected.

## Configuration (not setting environment automatically)
`config/tidong_customer.php` includes:
- `TIDONG_CUSTOMER_PUBLIC_REGISTRATION=false` (default OFF; do not enable until approved)
- `TIDONG_CUSTOMER_MODULE_ENABLED=true` (default ON for already-authorized customer accounts; OFF denies customer area)
- `max_addresses=10` (config constant)
- Supported locale preferences `en`, `hi` — language expansion later.
Laravel config cache must be refreshed by Master Chat if environment flags change; do not change production config without review.

## Validation status
- PHP CLI syntax checks for changed PHP files: PASS (see `QA_REPORT.txt`).
- ZIP entry path & SHA256 manifest integrity: verified during packaging.
- 13 Laravel feature tests PROVIDED, but **NOT RUN** here because the uploaded master is a source snapshot without `vendor/`, `.env`, `artisan` or a test database.
- This package is **review-ready / NOT installed / NOT production-certified**.
