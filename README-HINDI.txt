IMPORTANT - READ FIRST (English UI, Hindi instructions)

THIS REVISED STEP 5 REPLACES the previous digital-branch-licenses-step5.zip.
Do NOT install the old Step5 zip afterward: its licence screen allowed direct writes.

PRECONDITIONS: Step 1 Address Master and Step 2 Address Dropdown are installed.
Step 4 Branch GPS zip should be installed and tested BEFORE this revised Step5 package.
Step 3 Quality Report is optional and independent.

INCLUDED:
1) Branch Edit Requests: old Branch remains unchanged until Admin approves.
2) Create/edit GST/FSSAI/other licence: request-only, no direct DB changes.
3) Admin + properly permissioned staff/employee Review Queue.
4) Approve/Reject with mandatory Reject reason, before/after values, audit logs,
   one outstanding request per Branch, no duplicate approval.
5) Existing branch code stays the same; same Branch name allowed in many states.

SECURITY & LIMITATIONS:
- These workflows cover changes through the supplied Vendor UI/routes.
  Other privileged/legacy edit routes must be audited separately before production.
- Admin approval means platform review; it does NOT verify GST/FSSAI with government APIs.
- No documents upload yet. No automatic expiry reminders yet.
- Licence number changes retain the old number in the immutable request history.
- Approvals apply to profile and licences only, NOT old orders or financial transactions.
- Never run migrate:fresh, migrate:refresh, db:wipe or rollback.

INSTALL LATER, ONE STEP AT A TIME. DO NOT APPLY BEFORE CURRENT ADDRESS
DROPDOWN, GPS, AND BACKUPS ARE CHECKED.

Backups: create local code ZIP outside project and SQL export of digital_db.
Extract revised ZIP to C:\xampp\htdocs\digital after backup.
Verify php artisan route:list --except-vendor
Verify php artisan config:show database.connections.mysql.database --env=testing -> digital_test_db
Verify php artisan config:show database.default --env=testing -> mysql
Test Migration 040000 branch_licenses first ONLY in digital_test_db.
Test Migration 050000 branch_change_requests second ONLY in digital_test_db.
Test php artisan test --filter=BranchChangeApprovalWorkflowTest
Only after passing tests and another SQL backup, migrate 040000/050000
on digital_db in order. Do NOT import demo data to digital_db.

Admin review: /digital/public/admin/branch-changes
Vendor edit: Vendor Dashboard -> Request Branch Edit.
Staff Admin review: /digital/public/staff-admin/branch-changes, requires approvals.view,
approvals.approve / approvals.reject role permissions.
