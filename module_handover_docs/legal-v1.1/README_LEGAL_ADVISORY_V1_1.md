# TIDONG Legal & Professional Advisory — V1.1 Review Package

**Source basis:** `tidong-master-source-2026-10-19(5).zip` plus `TIDONG_LEGAL_ADVISORY_V1_2026-10-19.zip`.
**Status:** source-only candidate, not installed, not tested within a runnable Laravel application, no migrations executed.
**Use:** Give to the Master Integration Chat. Do NOT blindly overwrite newer `routes/*.php` files.

## Scope

This is a **cumulative V1.1 ZIP**: it includes all V1 files plus V1.1 additions, not an incremental patch.

New in V1.1:
- Existing authenticated, non-platform users can apply for a qualification verification with category and registration reference. Applications start `pending`; no self-approval, no public registration, no financial access.
- Super Admin can record verified or rejected decisions. Approval requires explicit declaration of independent evidence checking and a verification reference. Rejection requires a reason. Both actions create audit log records. No vendor employment or engagement decision is made by the admin.
- Verified applicant name cannot be silently changed via application endpoint. Pending/rejected applicants may reapply; verified and pending categories cannot be duplicated. Multiple independent categories allowed.
- Fixed V1 branch validation: the supplied foundation `branches` table has no `deleted_at` column, so branch ownership now uses business linkage without nonexistent-field filtering.
- Additional authentication route smoke tests (unexecuted).

## Existing V1 preserved
- Many-to-many professionals/venders and unlimited concurrent professionals per category.
- Vendor-only invitations, scope/permission whitelisting, termination; invited user alone can accept.
- Platform feature switches default OFF; disable does not erase engagements.
- Confidential data from finance, cases and documents is **not yet exposed or integrated**.

## New endpoints (private)
- `GET/POST /professional/legal-advisory/application` — applicant submission; protected by auth and professional portal flag. The professional login may be any existing *authorized* account, but platform staff are denied.
- `GET /admin/legal-qualifications` — pending applications list; Super Admin only.
- `POST /admin/legal-qualifications/{qualification}/decision` — verification review; Super Admin only.

## Migrations
1. `2026_10_19_110000_create_legal_advisory_v1_tables.php` (base V1)
2. `2026_10_19_111000_add_professional_application_details.php` (V1.1)

Both are **additive**. Never run on `digital_db` or cloud until the master integration's backup, test and approval sequence. V1.1 migration adds non-destructive nullable review columns.

## Security considerations and unresolved items
- Qualification verification is a manual attestation workflow, not automatic authenticity checking. Reviewer must actually verify with professional registry or authoritative source **outside** this module; merely checking the checkbox is not verification. A production rollout requires a professional-policy/legal review for each category and a secure evidence/document retention policy. Review provenance uses reference text, not uploaded evidence.
- `verification_status=verified` on profile is set after a verified category; if qualifications can later be revoked/suspended, add dedicated revocation logic that updates engagement access (not in V1.1).
- `legal_engagements.permissions` stores authorizations for planned future endpoints only; do not use as a bypass to existing finance permissions.
- No business/branch employee delegation; hiring currently requires the Vendor account owner. No case management or documents; no public professional discovery, advertising, referral commission, scheduling or payments.
- Feature setting endpoints remain Super Admin only; if adding admin employees later, implement a narrow `manage_feature_flags` permission, *not* vendor operation privileges.
- Some profiles may have multiple qualifications; registered category validation is by application and by verified qualification on invitation.
- The uploaded source is a **partial snapshot** and cannot establish that this package is compatible with the user's current local checkout. Code tests below must run on `digital_test_db` only.

## Master integration checklist
1. Review a source diff against **current** `C:\xampp\htdocs\digital`, particularly all three route files; merge only Legal Advisory route fragments.
2. Back up before changes. Keep feature toggles OFF during initial testing. Never enable public registration.
3. Confirm `audit_logs`, `branches`, `vendors`, `users`, and account-role relations match snapshot.
4. In an isolated test installation using `digital_test_db`, check `php artisan route:list --path=legal` and `php artisan route:list --path=professional`.
5. Run **only against an isolated test DB**: `php artisan migrate --env=testing`; `php artisan test --filter=LegalAdvisory`; then full test suite. Review logs and any failures. Never assume tests passed until output confirms.
6. Manually test applicant, administrator rejection/verification, multi-firm invitation/acceptance and forbidden cross-vendor access using dedicated test accounts.
7. Review professional regulation, data handling and production security before any module flag is activated.

## File inventory / merge strategy
- V1 complete files (see `README_LEGAL_ADVISORY_V1.md`) are retained.
- New: V1.1 migration; `Categories.php`; `ProfessionalApplicationController.php`; `LegalQualificationReviewController.php`; applicant/review Blade views; route smoke test.
- Updated: vendor controller (branch schema fix), professional portal view (application link), admin features view (review link), `routes/web.php` and `routes/admin.php` (new isolated route groups).

No DB changes, Git actions, local installation, third-party integrations, or deployment performed while preparing ZIP.
