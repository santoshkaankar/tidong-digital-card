# TIDONG Live Core Candidate — REVIEW ONLY (2026-10-21)

This is a sanitized reconstruction for staging from the uploaded TIDONG_MASTER_SECURE_REVIEW_CLEAN.zip plus newest TIDONG_ORDER_OPENING_SOURCE_20261021.zip, with Docker configuration from Secure Docker V1 and the installed schema guard V1.

**Not approved for direct push to GitHub main or immediate Render live deployment.** The reconstructed candidate has not been run end-to-end, and it is not byte-identical to the newest local digital directory after Outlet Opening V1 was installed. Outlet Opening V1 is intentionally excluded.

Verified: 264 PHP files passed PHP 8.4 static lint. Config selects DB_SCHEMA via PostgreSQL search_path. Startup guard requires production+pgsql+tidong_v2 and no automatic migration. Throttling middleware present on login.

Not verified: Docker production image, Vite asset build (npm registry unreachable in review environment), Composer install, actual Render config, production schema creation/migration, production mail/assets/login/media smoke tests. The PHP version linted was 8.4; Docker targets 8.3. Earlier 134 tests were run on a previous local source, not this candidate.

Before first live: (1) create and migrate new `tidong_v2` schema with a deliberate, reviewed, explicitly authorized procedure; (2) verify Render env including APP_KEY, APP_URL, DB credentials/SSL, DB_SCHEMA, SESSION_SECURE_COOKIE, cache and session; (3) build/test Docker container with these files; (4) integrate through a reviewed Git branch, not auto-push; (5) verify Live routes and authorized login, avoid enabling incomplete ordering/payment modules; (6) retain verified backup and rollback plan.

This ZIP contains no .env, secrets, vendors, database dump, npm packages, or live data. It does not update any user's project automatically.
