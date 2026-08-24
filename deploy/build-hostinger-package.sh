#!/usr/bin/env bash
# Builds kaxon_app.zip + public_html.zip for Hostinger shared-hosting
# deployment. Run from the project root. Requires composer + rsync + zip.
#
#   bash deploy/build-hostinger-package.sh /path/to/output-dir YOUR_SETUP_TOKEN

set -euo pipefail

OUT="${1:?Usage: build-hostinger-package.sh <output-dir> <setup-token>}"
TOKEN="${2:?Usage: build-hostinger-package.sh <output-dir> <setup-token>}"

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
STAGE="$OUT/hostinger_package"

rm -rf "$STAGE"
mkdir -p "$STAGE/kaxon_app" "$STAGE/public_html"
cd "$ROOT"

composer install --no-dev --optimize-autoloader --no-interaction

rsync -a \
  --exclude='.git' --exclude='.claude' --exclude='node_modules' --exclude='public' \
  --exclude='tests' --exclude='deploy' --exclude='.env' \
  --exclude='.env.example' --exclude='.env.backup' --exclude='.env.production' \
  --exclude='database/database.sqlite' --exclude='.phpunit.cache' \
  --exclude='.phpunit.result.cache' --exclude='storage/logs/*.log' \
  --exclude='.DS_Store' --exclude='*.zip' \
  ./ "$STAGE/kaxon_app/"

find "$STAGE/kaxon_app/storage/framework/sessions" -type f ! -name '.gitignore' -delete
find "$STAGE/kaxon_app/storage/framework/views" -type f ! -name '.gitignore' -delete
rm -rf "$STAGE/kaxon_app/storage/framework/cache/data"
mkdir -p "$STAGE/kaxon_app/storage/framework/cache/data"

rsync -a --exclude='build' --exclude='hot' public/ "$STAGE/public_html/"
cp deploy/hostinger-index.php "$STAGE/public_html/index.php"
sed "s/REPLACE_WITH_YOUR_TOKEN/$TOKEN/" deploy/remote-setup.php > "$STAGE/public_html/remote-setup.php"
cp deploy/.env.hostinger.example "$STAGE/kaxon_app/.env"

KEY=$(php artisan key:generate --show)
sed -i '' "s|^APP_KEY=|APP_KEY=$KEY|" "$STAGE/kaxon_app/.env"

cd "$STAGE"
(cd kaxon_app && zip -rq ../kaxon_app.zip . -x '.DS_Store')
(cd public_html && zip -rq ../public_html.zip . -x '.DS_Store')

echo "Built: $STAGE/kaxon_app.zip and $STAGE/public_html.zip"
echo "Setup URL will be: https://yourdomain.com/remote-setup.php?token=$TOKEN&action=migrate"
