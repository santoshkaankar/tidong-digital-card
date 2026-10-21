LOCAL URL FIX - XAMPP / DIGITAL

WHY: Your website uses http://localhost/digital/public, but Laravel APP_URL
was http://127.0.0.1:8000. The local web server was generating URLs
without /digital/public. Some pages also used hard-coded root paths.

This ZIP changes only 7 source files (no migrations or database writes).
Before applying: back up app/Providers, app/Http/Controllers/Admin,
resources/views/vendor-management, and resources/views/business-management.

IMPORTANT: ZIP by itself does NOT change .env. You MUST set in
C:\xampp\htdocs\digital\.env:
APP_URL=http://localhost/digital/public
Then run: php artisan config:clear

Never share or upload your .env file.

Validation: php artisan route:list --except-vendor
             php artisan view:cache
Browser: Ctrl+F5 on /digital/public/admin/vendors, then View Details,
Back, Dashboard. Vendor remains Pending until you explicitly approve.

Production: URL root override is active ONLY for APP_ENV=local.
Do not carry your localhost APP_URL to deployed hosting.
