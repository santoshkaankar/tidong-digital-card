# TIDONG Legal & Professional Advisory V1 — Master Integration Handover

**Status:** additive source package, NOT installed or verified by running Laravel. Prepared against `tidong-master-source-2026-10-19(5).zip`, a partial source snapshot. **No DB migrations were run.**

## What is implemented
- Vendor can invite unlimited verified profiles, including several professionals in the same category, across vendors, businesses and branches. Each invitation is independent.
- Only an existing user with a pre-existing **verified** `legal_professional_profiles` record and **verified** category can be invited. No public onboarding, no self-verification. Verification/backoffice workflow is **NOT implemented**; authenticated, reviewed administrative provisioning will be needed in V1.1. Do not manually mark unverified qualifications verified.
- Invited professional can accept only their invitation; vendor can end only engagements under own vendor.
- Whitelisted engagement permissions stored, but **no case/document endpoints exist yet**. They grant no finance/data access by themselves.
- Super Admin can flip module, hiring, and professional portal switches via protected admin endpoints, all OFF by default. Disabling the module blocks both portals (and invitation actions), never deletes records.
- Database FK constraints and ownership checks. Audit entries on invite, acceptance, termination and feature changes.
- No public listing/lead marketplace/fees/commission/GST filing/payment functionality. Sector-specific professional regulations require separate review.

## Files
- `database/migrations/2026_10_19_110000_create_legal_advisory_v1_tables.php` — four new tables.
- `app/Support/LegalAdvisory/Feature.php` — feature policy.
- `app/Http/Controllers/{Vendor/LegalAdvisoryController,LegalAdvisory/ProfessionalPortalController,Admin/LegalFeaturesController}.php` — isolated module endpoints.
- `resources/views/{vendor/legal-advisory/index,legal-advisory/professional/index,admin/legal-features/index}.blade.php` — basic English interface.
- `routes/{vendor,web,admin}.php` — **surgical changes from supplied 2026-10-19 source snapshot**; do NOT blindly replace files updated since snapshot.
- `tests/Feature/LegalAdvisoryStructureTest.php` — basic smoke assertions; not run here.

## Important pre-merge checks for Master Chat
1. Compare supplied `routes/vendor.php`, `routes/web.php`, `routes/admin.php` with actual `C:\xampp\htdocs\digital` originals. Manually merge only added Legal Advisory route groups if originals changed. Never overwrite newer work.
2. Ensure live project loads vendor routes at `/vendor` and admin routes at `/admin` (snapshot omits `bootstrap/app.php`). Professional route resolves under web prefix; verify `php artisan route:list --path=professional`.
3. Back up main DB; **first install and test on `digital_test_db` only**. Confirm migration naming does not collide, schema PostgreSQL/MySQL compatibility, and tests run with a configured test DB.
4. Review trust provisioning workflow for profiles and category verification before inviting any professional; V1 intentionally does not permit self-approved identities.
5. Verify existing middleware and login routes. Check route names via `php artisan route:list --path=vendor/legal-advisory` and `--path=admin/legal-features`.
6. `php artisan test --filter=LegalAdvisoryStructureTest` followed by full suite, only against isolated test DB. The supplied snapshot does not contain a runnable Laravel application, so test results are **not claimed**.
7. Need full end-to-end permission tests, UI integration and reviewed professional onboarding in next iteration. Do not deploy now.

## Roles and ownership
- Platform Super Admin toggles feature availability only; cannot hire on vendors' behalf with these controllers.
- Platform employee cannot access Vendor routes; professional portal rejects platform account users.
- Vendor manages invitations, permissions, termination; Professional consents by accepting.
- Default no public registration, no automatic access to finances, no record deletion.

## URLs (once module and route prefixes installed)
- Vendor: `/vendor/legal-advisory`
- Professional: `/professional/legal-advisory`
- Admin: `/admin/legal-features`

## Limitations
This is V1 foundation, not a completed regulated advisory marketplace. End-to-end tests, qualification verification UI, scoped document access, case management, notifications, contracts and payments are future phases. **A flag turning OFF hiring does not remove already accepted engagements; turning OFF the module blocks portal access.**
