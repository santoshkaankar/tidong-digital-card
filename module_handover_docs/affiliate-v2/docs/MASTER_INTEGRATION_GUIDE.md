# TIDONG Affiliate V2 - Master Review Candidate

**Status:** Source and static QA review only. Not live. No production database or public registration changes.

## User-approved behavior
- Every verified, registered customer automatically receives a unique free Affiliate Member ID when new customer registration is integrated.
- Registration fee amount is configurable as a draft; actual fee collection is hard-off at INR 0 today.
- Referral attribution and placement are different. A customer with no genuine sponsor has `referrer_member_id = NULL` even though a placement parent is recorded.
- A no-referral customer must be appended to the bottom of the First ID's **all-Leg-A** chain at the moment of registration. Never choose the latest member overall, Leg B, or a randomly deeper branch.
- Referrals record actual sponsor and selected Leg A/B. Referral links include the leg explicitly. When the sponsor's direct chosen position is already occupied, the request stops with a validation error. A future owner-approved spillover strategy is required before universal signup can be claimed.
- Every new membership transaction serializes on the settings singleton using `SELECT ... FOR UPDATE`. The unique `(placement_parent_member_id, placement_leg)` index is the final prevention for duplicate placement. The no-referral tail pointer makes appending O(1), including long chains.
- First ID is provisioned once by a platform Super Admin selecting an existing **email-verified customer**. Do not infer the root from unverified or old data. If affiliate V1 has members, halt and manually reconcile instead of inventing tree positions.
- Every eligible verified purchase across all approved TIDONG outlets must contribute to the customer's own unified transaction total in a **later source-integrated module**. A completed, refund-aware, cross-outlet order ledger is **NOT included in this package**; do not claim it is running.
- The original 14 stages remain dynamically editable as historical reference only, pending Indian legal, tax and accounting review. Sales-based estimated commissions remain purely illustrative: rate starts at 0 and no verified-order adapter is connected. Wallet credits, final recognition, tax deduction, payment and withdrawal are NOT enabled.

## Safe integration order for Master Chat
1. Use a backed-up, isolated local checkout (`C:\xampp\htdocs\digital`) and **only** `digital_test_db`. Compare each provided path to the latest current source. Keep full backup.
2. Add new `app/Services/Affiliate`, `app/Http/Controllers/Affiliate`, `routes/affiliate.php`, `resources/views/affiliate`, two migrations, tests, and logo. Do not overwrite changed current files without conflict review.
3. Add only one `require __DIR__.'/affiliate.php';` in current `routes/web.php`; add an `affiliate` module catalog entry installed true, default enabled false. Check the central middleware and administrator's approval before enabling.
4. Run the first V1 migration, then additive V2 migration in an isolated test DB. Audit legacy members and confirm empty tree prior to first-ID initialization. Never run `migrate:fresh` or rollback against financial/production records.
5. In the Super Admin Affiliate page, initialize the real **First ID** with the user ID of an **existing verified customer**. If V1 members already exist, do not initialize automatically; reconcile first.
6. Integration point for secure new customer registration: after successful email/identity verification and creation of the customer user plus `user_account_types` row, call `app(\App\Services\Affiliate\AffiliateMembershipService::class)->enroll($user->id, $validatedSponsorCode, $validatedLeg);` in the **same** `DB::transaction(...)` as the customer account creation. Never allow a browser to submit a role or user ID. For customers without referrals, pass `null` for both sponsor and leg.
7. Account creation and verification are not part of this ZIP because the current Master closes `/register`. Prepare customer-only verification, abuse prevention and rate limits with the Customer Module owner before enabling this integration; do not bypass the existing registration lock.
8. Run `php artisan test --filter=AffiliateModuleReviewTest` using ONLY the authorized test database, `php artisan route:list --path=affiliate`, general full suite, MySQL locking/concurrency tests, and render Blade/UI checks; a missing runtime or database means tests remain NOT RUN.
9. Request legal and CA/CS review before allowing any real commissions or fees. Do not enable money movement from this package.

## Open questions / blockers
- Referral-sponsored placement when both direct sponsor legs are full has no owner-approved overflow algorithm. Do not guess.
- True cross-module transaction-source adapter, cancellation/refund and net eligible purchase calculations remain to be designed.
- Old V1 enrolled member migration requires independent review of existing first/referrer histories.
- The selected snapshot does not contain the operational, writable, current project or an installed Composer vendor tree. No real Laravel integration test was executed here.

**All source code, UI and implementation documentation included in this ZIP are in English.**
