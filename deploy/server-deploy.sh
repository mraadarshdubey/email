#!/usr/bin/env bash
# Deploys Sendpeak on the GCP server next to self-hosted Supabase.
# Idempotent: safe to re-run. Reads the Postgres password from Supabase's own
# .env so the secret never leaves the server.
set -euo pipefail

APP_DIR=/home/adars/sendpeak-app
SUPABASE_ENV=/home/kaxon/supabase/docker/.env
COMPOSE="sudo docker compose -f deploy/docker-compose.server.yml --project-directory ."

cd "$APP_DIR"

echo "==> Reading DB password from Supabase env"
DB_PASS=$(sudo grep -E '^POSTGRES_PASSWORD=' "$SUPABASE_ENV" | head -1 | cut -d= -f2- | tr -d '"')
if [ -z "${DB_PASS}" ]; then echo "ERROR: could not read POSTGRES_PASSWORD"; exit 1; fi

# Preserve an existing APP_KEY across re-runs; generate one the first time.
if [ -f .env ] && grep -q '^APP_KEY=base64:' .env; then
  APP_KEY=$(grep '^APP_KEY=' .env | head -1 | cut -d= -f2-)
else
  APP_KEY="base64:$(openssl rand -base64 32)"
fi

echo "==> Writing .env"
cat > .env <<EOF
APP_NAME=Sendpeak
APP_ENV=production
APP_KEY=${APP_KEY}
APP_DEBUG=false
APP_URL=http://34.47.244.178:8080

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=sendpeak
DB_USERNAME=postgres
DB_PASSWORD=${DB_PASS}

QUEUE_CONNECTION=redis
CACHE_DRIVER=redis
SESSION_DRIVER=file
REDIS_HOST=redis
REDIS_PORT=6379

BULK_MAX_CAMPAIGN_SIZE=100000
BULK_DISPATCH_CHUNK=500
BULK_RATE_PER_MINUTE=700

MAIL_MAILER=log
MAIL_FROM_ADDRESS=hello@sendpeak.in
MAIL_FROM_NAME=Sendpeak
EOF

echo "==> Ensuring writable storage dirs"
mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache

echo "==> Building image"
$COMPOSE build

echo "==> Migrating + seeding database"
$COMPOSE run --rm web php artisan migrate --force --seed
$COMPOSE run --rm web php artisan package:discover --ansi || true

echo "==> Starting web + 3 workers"
$COMPOSE up -d
$COMPOSE up -d --scale worker=3

echo "==> Status"
$COMPOSE ps
echo "==> Deploy complete. Admin UI: http://34.47.244.178:8080"
