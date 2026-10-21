#!/bin/sh
# Fail closed on common production misconfigurations. Never migrate or seed.
set -eu

if [ "${APP_ENV:-}" != production ]; then
  echo 'STARTUP_BLOCKED: APP_ENV must be production' >&2
  exit 1
fi
case "${APP_DEBUG:-}" in
  false|0|off) ;;
  *) echo 'STARTUP_BLOCKED: APP_DEBUG must be false' >&2; exit 1 ;;
esac
if [ -z "${APP_KEY:-}" ]; then
  echo 'STARTUP_BLOCKED: APP_KEY is missing' >&2
  exit 1
fi
if [ "${DB_CONNECTION:-}" != pgsql ]; then
  echo 'STARTUP_BLOCKED: PostgreSQL configuration not confirmed' >&2
  exit 1
fi
# Fail closed if the isolated application schema is not explicitly selected.
# The legacy production data remain in public during the transition.
if [ "${DB_SCHEMA:-}" != 'tidong_v2' ]; then
  echo 'STARTUP_BLOCKED: DB_SCHEMA must be tidong_v2' >&2
  exit 1
fi
if [ "${DB_SSLMODE:-}" != require ] && [ "${DB_SSLMODE:-}" != verify-full ]; then
  echo 'STARTUP_BLOCKED: DB_SSLMODE must require encrypted connection' >&2
  exit 1
fi
if [ "${SESSION_SECURE_COOKIE:-}" != true ]; then
  echo 'STARTUP_BLOCKED: SESSION_SECURE_COOKIE must be true' >&2
  exit 1
fi
if [ "${PORT:-10000}" != 10000 ]; then
  echo 'STARTUP_BLOCKED: Render PORT must be 10000 for this image' >&2
  exit 1
fi

# No php artisan migrate/seed/optimize here: production DB remains untouched.
exec apache2-foreground
