# TIDONG Billing Numbering — V0.2 review-only prototype

**NOT INSTALLED, NOT PRODUCTION-READY.** This package includes two additive tables and a standalone internal numbering service. It intentionally does not modify FinancePostingService, routes, existing billing, or any live DB.

## Contract
- A separate, business+outlet-scoped `GL` counter tracks *every* voucher created for that outlet.
- Every two-letter voucher code has a separate counter in the same outlet.
- Both display `FY/Book/Serial`; defaults 50 leaves and reset each FY, editable by vendor-authorized settings after separate implementation.
- `request_key` UUID enables idempotent retries. Unique constraints protect collisions; counter rows use `lockForUpdate()`.
- Call `allocate()` only after verifying authenticated business/outlet ownership and actor permissions, within voucher creation's outer DB transaction. Never expose this raw service from a route.
- Counters must be provisioned by a separate, authorized setup action (not included), so rollout cannot auto-create scope records based on untrusted identifiers.

## Unresolved / review blockers
1. Source snapshot confirms branches table/model with business_id; V0.2 validates branch belongs to business. Vendor/employee authorization and production schema compatibility still require integration review. No foreign keys yet pending migration review.
2. Voucher creation and audit writes must be integrated *atomically* in the same transaction, with actor-scope validation and reference to allocation. This package alone does not do that.
3. Counter configuration changes (book size, FY reset) must be vendor-authorized, effective-dated and audited. V0.2 tracks used_in_book so a book size change does not rely on global serial modulo; exact policy for decreasing book size below current occupancy still needs sign-off.
4. Financial year string must be determined by a trusted accounting calendar, not blindly by request payload. Prevent historical FY issuance except controlled process.
5. V0.2 refuses the same request key under a different actor. The caller must independently authorize actor, voucher type, business and outlet; no HTTP route is provided.
6. Concurrent duplicate UUID requests can raise a unique violation after the initial read; the caller should safely retry/read the committed allocation, with verified owner.
7. Required: PHPUnit integration tests against `digital_test_db` (including MySQL concurrent writers), security negative tests, rollback/idempotency tests, migration review, and audited end-to-end finance linking.
8. Existing FinancePostingService appears not to enforce business/outlet ownership; secure that first, separately, before enabling vouchers.

No migrations or application code from this package have been executed on the user's environment.

## V0.2 change log
- Validates branch/business linkage inside allocation transaction using branches table.
- Rejects idempotency replay under a different actor.
- Tracks used_in_book independently of running serial; supports resetting/reusing the same book across FY according to vendor config.
- Added review-only static checks and example test cases below; full Laravel runtime tests NOT executed.
- Important: allocation alone is NOT a posted voucher. A transaction must create voucher, allocate numbers and audit together.
