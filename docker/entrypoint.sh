#!/bin/sh
# Render invokes this script on every successful image deployment/start.
# ONLY pending migrations: never fresh/refresh/reset/rollback/seed.
set -eu

if [ "${APP_ENV:-}" != "production" ]; then
  echo 'TIDONG STARTUP BLOCKED: APP_ENV must be production.' >&2
  exit 1
fi
case "${APP_DEBUG:-}" in
  false|0|off) ;;
  *) echo 'TIDONG STARTUP BLOCKED: APP_DEBUG must be false.' >&2; exit 1 ;;
esac
if [ -z "${APP_KEY:-}" ]; then
  echo 'TIDONG STARTUP BLOCKED: APP_KEY is missing.' >&2
  exit 1
fi
if [ "${DB_CONNECTION:-}" != "pgsql" ]; then
  echo 'TIDONG STARTUP BLOCKED: DB_CONNECTION=pgsql required for Supabase.' >&2
  exit 1
fi
if [ "${DB_SCHEMA:-}" != "public" ]; then
  echo 'TIDONG STARTUP BLOCKED: DB_SCHEMA must be public.' >&2
  exit 1
fi
case "${DB_SSLMODE:-}" in
  require|verify-full) ;;
  *) echo 'TIDONG STARTUP BLOCKED: DB_SSLMODE must require encrypted connection.' >&2; exit 1 ;;
esac
if [ "${SESSION_SECURE_COOKIE:-}" != "true" ]; then
  echo 'TIDONG STARTUP BLOCKED: SESSION_SECURE_COOKIE must be true.' >&2
  exit 1
fi
if [ "${PORT:-10000}" != "10000" ]; then
  echo 'TIDONG STARTUP BLOCKED: Render port must be 10000.' >&2
  exit 1
fi

echo 'TIDONG: Running ONLY pending Laravel migrations (migrate --force).'
if php artisan migrate --force --no-interaction; then
  echo 'TIDONG: Migration step completed. Starting Apache.'
else
  echo 'TIDONG: Migration failed. Apache not started; see Laravel DB error above.' >&2
  exit 1
fi
exec apache2-foreground
