PACKAGE B (SECOND): QR ORDER REQUESTS, OPTIONAL VOICE INPUT, TABLE-WISE WAITER ORDER.

DEPENDENCY: FIRST INSTALL AND TEST PACKAGE A. The A migration (restaurant_dining_tables)
MUST exist on the target database BEFORE B migration runs.

Users open their table QR. Menu prices come from server. Voice selection is OPTIONAL and depends
on browser mic permissions and SpeechRecognition support. No claim of universal Hindi accuracy:
the browser may use a cloud speech service, and the customer MUST review selection.
Guest sends a PENDING REQUEST (no direct kitchen order, no payment). Vendor can accept or
reject; accepting creates a POS Order with order_origin=qr, preserves request & audit logs.
Menu changes between request/accept are blocked, not silently repriced.
Vendor-owner POS screen can select a Table when creating a Waiter Order.
Kitchen KDS displays table label and source (QR, waiter, counter).

SECURITY: Guest submit uses CSRF, request throttle 6/min/IP, order caps and UUID dedupe.
Real-world production needs anti-abuse monitoring and HTTPS before public deployment.
This is an initial POS module for vendor owner only, not employee roles, payments, delivery or wallet.

NO destructive migrations. DO NOT apply to live digital_db before code backup, SQL backup,
checking testing config and successful tests against digital_test_db.
Suggested: php artisan config:show database.connections.mysql.database --env=testing
Expected digital_test_db. Then verify database.default --env=testing (mysql).
Inspect migrate:status --env=testing; migrate new file only using --env=testing --path=...
Run php artisan test --filter=RestaurantQrRequestWorkflowTest (after packaging test class).
Only then apply B migration to main with SQL backup, and verify browser/phone workflow.

To test QR on a phone, http://localhost/digital/public links point to the PHONE itself,
so use a proper LAN-accessible host or domain, HTTPS (recommended) and update APP_URL.
