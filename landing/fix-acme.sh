#!/bin/sh
# Force Caddy to re-issue sendpeak.in certs from Let's Encrypt PRODUCTION
# (previous attempts used the staging CA, whose certs browsers do not trust).
set -e

echo "--- current acme dirs ---"
ls /var/lib/docker/volumes/supabase_caddy_data/_data/caddy/certificates/ 2>/dev/null || echo "none"

echo "--- removing staging certs + acme accounts ---"
rm -rf /var/lib/docker/volumes/supabase_caddy_data/_data/caddy/certificates/acme-staging-v02.api.letsencrypt.org-directory
rm -rf /var/lib/docker/volumes/supabase_caddy_data/_data/caddy/acme/acme-staging-v02.api.letsencrypt.org-directory

echo "--- restarting caddy ---"
docker restart supabase-caddy >/dev/null

echo "RESTARTED"
