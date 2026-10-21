# Grocery V2 Release & Regression Checklist

## Completed static/offline checks (in this package creation environment)
- PHP `-l` syntax validation over all PHP source/migrations/tests.
- File-only installer simulated against original 2026-10-19 master source and against simulated source with Grocery V1 previously installed: --check, --apply, repeat --check tested. Source simulation lacked actual artisan DB runtime; no Laravel test claims.
- Additive migration reviewed to avoid destructive rollbacks and ensure FK constraints/append-only audit design.

## Pending **Laravel runtime** validation (digital_test_db ONLY)
1. Confirm separate test DB is `digital_test_db` and DB driver settings, migrations applied in order. Never run tests against local `digital_db` or Supabase.
2. Run `php artisan route:list --name=grocery` and check all named routes exist and no route collisions.
3. Run `php artisan test --env=testing --filter=Grocery`, then whole Laravel test suite. Save exact output.
4. Test Super Admin toggling for module, online_store, online_orders, pricing, inventory; prove OFF is checked at action endpoints.
5. Test vendor/customer/other vendor/branch scope, anonymous checkout redirect, unauthorized order 404, malformed cart & invalid quantities, CSRF/throttling.
6. Test concurrent ordering vs Counter Billing, insufficient stock, expired lot, zero-price, tax-inclusive and exclusive GST (intra/inter), price changed since cart, no online payment.
7. Test purchase → cancel / reject exactly once; verify stock ledger balancing, original order lines/snapshots remain, idempotent checkout form token.
8. Test delivery request address validation and manual vendor workflow; **no courier dispatch/payment integration**.
9. Browser QA: desktop and mobile, keyboard accessibility, autocomplete, English labels, images HTTPS, UX without JS, pagination & failed validation states.
10. Master review security: CSRF session cookie policy, authorization, rate-limits, registration CLOSED, privacy/GST/FSSAI applicability, real inventory & vendor consent.
11. Production migration/deploy only with explicit separate authorization, backup, PostgreSQL staging test and Render auto-deploy awareness.

## Current limitations
This sandbox has source snapshot, not a fully runnable installed Laravel checkout; therefore automated Laravel runtime / real DB MySQL/PostgreSQL tests were NOT executed here.
