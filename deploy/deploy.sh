#!/usr/bin/env bash
# Run this ON THE SERVER, from the project root, for the first deploy and
# every subsequent update (git pull first, then this script).
#
#   ssh you@server
#   cd /var/www/kaxon-mailer
#   git pull
#   bash deploy/deploy.sh

set -euo pipefail

echo "==> Installing PHP dependencies"
composer install --no-dev --optimize-autoloader --no-interaction

if [ ! -f .env ]; then
    echo "==> No .env found — copying deploy/.env.production.example (edit it before first run!)"
    cp deploy/.env.production.example .env
fi

if ! grep -q "^APP_KEY=base64:" .env; then
    echo "==> Generating APP_KEY"
    php artisan key:generate --force
fi

echo "==> Running migrations"
php artisan migrate --force

echo "==> Linking storage"
php artisan storage:link || true

echo "==> Caching config/routes/views for speed"
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Restarting queue workers so they pick up new code"
php artisan queue:restart

echo "==> Done. Checklist:"
echo "  - crontab -e   ->   * * * * * cd $(pwd) && php artisan schedule:run >> /dev/null 2>&1"
echo "  - supervisorctl reread && supervisorctl update && supervisorctl start kaxon-mailer-worker:*"
echo "  - nginx -t && systemctl reload nginx"
