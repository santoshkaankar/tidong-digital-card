PACKAGE A (FIRST): TABLE-WISE QR MENU — INSTALL BEFORE PACKAGE B.

No existing database rows are deleted or edited by this package.
Adds the restaurant_dining_tables table for distinct QR tokens for each table.
Vendor owner can create/disable printable table QRs. The public token URL shows
only the currently active menu, and does not create kitchen orders yet.

BACKUP FIRST: app, routes, resources, tests, database and SQL backup of digital_db.
DO NOT RUN migrate:fresh, db:wipe or rollback. Start by checking database config
for testing: php artisan config:show database.connections.mysql.database --env=testing
Expected digital_test_db. Also check: php artisan config:show database.default --env=testing (mysql).

For install: expand into project root after backup. Run php artisan route:list --except-vendor.
Then test migration status --env=testing and migrate only this migration on testing DB.
Use php artisan test --filter=RestaurantTableQrMenuTest. It is untested on your Windows runtime.
Only later, with SQL backup, apply this specific migration to main database.

IMPORTANT: package B adds Guest Order Requests, Manual Vendor Approval and Voice Helper.
QR token links are bearer-like links: do not publish unused/private tokens.
Localhost QR can only be opened by devices which can reach localhost on that machine.
For a mobile phone, host on a reachable LAN/domain with HTTPS and set APP_URL accordingly.
BSD-2-Clause QR library and license notices are bundled under app/Support/QrLibraries.
Do not upload .env file or database backups into the chat.
