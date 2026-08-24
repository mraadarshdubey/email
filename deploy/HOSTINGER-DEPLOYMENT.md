# Deploying Sendpeak to Hostinger Shared Hosting (File Manager / FTP, no SSH)

Two ready-to-upload zips were built for you: `kaxon_app.zip` (the Laravel
app + vendor/) and `public_html.zip` (the web-facing files). This guide
assumes shared hosting where the document root is fixed to `public_html`
and there's no terminal access — everything below uses hPanel's File
Manager and a one-time browser-triggered setup script instead of SSH.

## 1. Check your PHP version

hPanel → **Websites** → select your site → **PHP Configuration** → set
PHP to **8.1, 8.2, or 8.3** (this app was built/tested against 8.3; avoid
anything older than 8.1 or newer than 8.4).

## 2. Create the MySQL database

hPanel → **Databases** → **MySQL Databases** → create a database and a
user, and note down: database name, username, password, host (usually
`localhost`).

## 3. Upload the app code (outside the web root)

In File Manager, go to your domain's root (one level **above**
`public_html` — usually `/home/USERNAME/domains/yourdomain.com/`).

1. Upload `kaxon_app.zip` there and extract it. You should end up with a
   `kaxon_app/` folder as a **sibling** of `public_html/`, not inside it —
   this keeps your `.env`, `app/`, `vendor/`, etc. off the public internet.

## 4. Upload the public files (into public_html)

1. Open `public_html/`. If Hostinger's default placeholder files
   (`index.html`, etc.) are there, delete them.
2. Upload `public_html.zip` and extract its contents **directly into**
   `public_html/` (not into a subfolder). This gives you `index.php`
   (already patched to point at `../kaxon_app`), `favicon.ico`,
   `robots.txt`, `sample-contacts.csv`, and `remote-setup.php`.

## 5. Configure `.env`

Edit `kaxon_app/.env` (File Manager → right-click → Edit) and fill in:

- `APP_URL` — your real domain, e.g. `https://yourdomain.com`
- `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` — from step 2
- `MAIL_*` — only needed for system notices; per-account SMTP is
  configured later in the app's UI, not here
- `GOOGLE_CLIENT_ID` / `GOOGLE_CLIENT_SECRET` — from step 7 below

`APP_KEY` is already pre-generated — don't touch it. A reference copy of
this file lives at `deploy/.env.hostinger.example` in the repo.

## 6. Run the database migrations (no SSH needed)

Visit this URL once in your browser (replace the domain):

```
https://yourdomain.com/remote-setup.php?token=__TOKEN__&action=migrate
```

You should see `Migrating: ...` / `Migrated: ...` lines for each table.

Optional — seed the default admin account (`admin@email.com` /
`12345678`, **change the password immediately after logging in**):

```
https://yourdomain.com/remote-setup.php?token=__TOKEN__&action=seed-admin
```

**Then delete `public_html/remote-setup.php` via File Manager.** It has
no auth beyond the token and shouldn't stay on a live server.

## 7. Set up Google Sign-In

1. Go to the [Google Cloud Console](https://console.cloud.google.com/) →
   create/select a project → **APIs & Services** → **OAuth consent
   screen** → configure it (External, add your email as a test user if
   still in testing mode).
2. **APIs & Services** → **Credentials** → **Create Credentials** →
   **OAuth client ID** → Application type **Web application**.
3. Under **Authorized redirect URIs**, add exactly:
   `https://yourdomain.com/auth/google/callback`
4. Copy the generated **Client ID** and **Client Secret** into
   `kaxon_app/.env` as `GOOGLE_CLIENT_ID` / `GOOGLE_CLIENT_SECRET`.

**Important:** Google sign-in only authenticates **existing** accounts
(matched by email) — it will never silently create a new account, since
this app has self-registration disabled by design. To let someone log in
with Google, first create their user (via the admin seeder, a manual DB
row, or your own admin-only user-creation flow) with the same email as
their Google account.

## 8. File permissions

Most Hostinger accounts default to sane permissions on upload, but if you
hit a 500 error, check via File Manager → right-click → Permissions:

- Folders: `755`
- Files: `644`
- `kaxon_app/storage/` and `kaxon_app/bootstrap/cache/` (recursively):
  `775`

## 9. Verify

- Visit `https://yourdomain.com/login` — should show the login card with
  the "Sign in with Google" button.
- Log in with the seeded admin (or Google, once step 7 is done).
- **Change the admin password immediately** (Profile → Security).
- Send one test email and confirm open/click tracking registers.

## Notes specific to shared hosting

- `QUEUE_CONNECTION=sync` is kept in the template — shared hosting can't
  run a persistent `queue:work` process without SSH, so emails send
  inline. Fine for moderate volumes; large campaigns will just take
  longer per request.
- Automation (Rules/Sequences/RSS/Follow-ups) tick opportunistically on
  page load rather than a real cron, since there's no `crontab -e` here.
  If your Hostinger plan's hPanel has a **Cron Jobs** section, you can
  point one at
  `https://yourdomain.com/remote-setup.php?token=__TOKEN__&action=...`
  style URL for more reliable scheduling — ask if you want this wired up.
- No Node/npm needed on the server — this app's pages use CDN-hosted
  CSS/JS, not a Vite build.

## Redeploying after future code changes

Re-run the packaging step locally (ask Claude, or re-run
`deploy/build-hostinger-package.sh` if present), then re-upload just the
changed files, or re-extract fresh zips over the existing folders. Always
re-upload `.env` from the server (don't overwrite it with the local
template) so you don't lose your production secrets.
