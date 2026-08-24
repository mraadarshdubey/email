#!/bin/sh
F=/var/lib/docker/volumes/supabase_caddy_config/_data/caddy/autosave.json
if [ -f "$F" ]; then
  echo "AUTOSAVE-EXISTS"
  grep -c staging "$F" 2>/dev/null && echo "staging-hits-above"
else
  echo "NO-AUTOSAVE"
fi
