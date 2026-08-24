#!/bin/sh
# Replace the sendpeak.in site blocks in the Caddyfile with the version in
# /tmp/caddy-sendpeak.txt (which pins Let's Encrypt PRODUCTION as the CA).
set -e

CF=/home/kaxon/supabase/docker/volumes/proxy/caddy/Caddyfile

# Back up once so the original is always recoverable.
[ -f "$CF.bak" ] || cp "$CF" "$CF.bak"

# Keep everything before the first sendpeak.in block, then append the new one.
LINE=$(grep -n '^sendpeak.in {' "$CF" | head -1 | cut -d: -f1)
if [ -n "$LINE" ]; then
  head -n $((LINE - 1)) "$CF" > "$CF.new"
else
  cp "$CF" "$CF.new"
fi

cat /tmp/caddy-sendpeak.txt >> "$CF.new"
mv "$CF.new" "$CF"

echo "--- resulting sendpeak block ---"
grep -A 12 '^sendpeak.in {' "$CF"
