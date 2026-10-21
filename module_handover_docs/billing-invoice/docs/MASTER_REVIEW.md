# TIDONG Billing — Invoice Draft Foundation V0.3 (REVIEW ONLY)

**Do not install on live/main DB.** This package is additive and does not modify existing Finance, routes, numbering V0.2, or orders. Review alongside V0.2 and current master source; do not assume that Master integration is already completed.

## Included
- Deterministic integer-paise amount calculator, with quantity in thousandths and tax rate in basis points; mathematically tests totals, not legal GST determination.
- Two additive tables for draft invoice headers and item snapshots, scoped to business and outlet, with line revision support.
- Draft `document_kind` domain: tax_invoice / bill_invoice / proforma / stock_transfer. Stored label does NOT make a proforma valid for GST stock movement.

## Must be built/verified before release
1. Authenticated, vendor-authorized draft API/service with business/outlet membership checked on server; NO endpoint or draft writer shipped here.
2. Effective-dated GST/HSN and CGST/SGST/IGST policy, place-of-supply checks, inclusive/exclusive calculations and statutory bill-of-supply cases. Do not trust user-entered rates.
3. Issuance service linking invoice and numbering allocation atomically with same DB transaction; immutable issued snapshot, authorization, fiscal lock, cancellation/reversal, audit log, idempotency; not included.
4. Customers, products, ledger and stock movement integration after schema review; tenant-scope FKs and indexes after exact master schema check.
5. MySQL tests against digital_test_db and concurrent stress/security tests; not run here.
6. No source file or database modified outside this review package.

## Review note
Draft line totals use per-line tax rounded half-up. This is not a GST compliance certification and the statutory rounding/discount rules need accountant verification. Transaction financial year must be trusted from server-side calendar.
