# Deploying Sendpeak Mailer to a VPS

Everything here is prepared and tested locally. Once you give me SSH access
(host, user, key) I'll run this end-to-end — or you can follow it yourself.

## 1. Server prerequisites (Ubuntu/Debian assumed)

```bash
sudo apt update
sudo apt install -y nginx mysql-server php8.2-fpm php8.2-cli php8.2-mysql \
  php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-bcmath \
  php8.2-gd php8.2-sqlite3 unzip git supervisor certbot python3-certbot-nginx
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

No Node/npm needed — every page uses CDN-hosted CSS/JS (Bootstrap, Quill,
DataTables, SweetAlert2), there's no `@vite` build step in this app.

## 2. Database

```sql
CREATE DATABASE kaxon_mailer CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'kaxon_mailer'@'localhost' IDENTIFIED BY 'CHOOSE-A-STRONG-PASSWORD';
GRANT ALL PRIVILEGES ON kaxon_mailer.* TO 'kaxon_mailer'@'localhost';
FLUSH PRIVILEGES;
```

## 3. Get the code onto the server

```bash
sudo mkdir -p /var/www/kaxon-mailer
sudo chown $USER:$USER /var/www/kaxon-mailer
git clone https://github.com/hellokiransheth/Bulk-Email-Sender.git /var/www/kaxon-mailer
cd /var/www/kaxon-mailer
git checkout feature/tracking-and-automation   # or main, once merged
```

## 4. Configure and deploy

```bash
cp deploy/.env.production.example .env
nano .env   # fill in APP_URL, DB_PASSWORD, MAIL_* (or leave MAIL_* blank —
            # the app's SMTP is per-account, set from the UI, not .env)
bash deploy/deploy.sh
```

`deploy.sh` installs Composer deps, generates the app key if missing, runs
migrations, links storage, caches config/routes/views, and restarts queue
workers. Re-run it (after `git pull`) for every future update.

## 5. Web server (Nginx + PHP-FPM)

```bash
sudo cp deploy/nginx.conf.example /etc/nginx/sites-available/kaxon-mailer
sudo nano /etc/nginx/sites-available/kaxon-mailer   # set your real domain + PHP-FPM socket
sudo ln -s /etc/nginx/sites-available/kaxon-mailer /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
sudo certbot --nginx -d CHANGE-ME.sendpeak.in   # free SSL, auto-renews
```

## 6. Queue worker (Supervisor) — required for bulk sends to not block requests

```bash
sudo cp deploy/supervisor-queue-worker.conf.example /etc/supervisor/conf.d/kaxon-mailer-worker.conf
sudo nano /etc/supervisor/conf.d/kaxon-mailer-worker.conf   # fix the path if not /var/www/kaxon-mailer
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start kaxon-mailer-worker:*
```

## 7. Cron (real automation scheduling — Rules/Sequences/RSS/Follow-ups)

```bash
crontab -e
```

Add:
```
* * * * * cd /var/www/kaxon-mailer && php artisan schedule:run >> /dev/null 2>&1
```

This replaces the "opportunistic" page-load-triggered automation with a
real, reliable once-a-minute tick (`automations:tick`, registered in
`app/Console/Kernel.php`).

## 8. File permissions

```bash
sudo chown -R www-data:www-data /var/www/kaxon-mailer
sudo find /var/www/kaxon-mailer -type d -exec chmod 755 {} \;
sudo find /var/www/kaxon-mailer -type f -exec chmod 644 {} \;
sudo chmod -R 775 /var/www/kaxon-mailer/storage /var/www/kaxon-mailer/bootstrap/cache
```

## 9. First login

Visit `https://CHANGE-ME.sendpeak.in`. If the seeded admin doesn't exist yet:

```bash
php artisan db:seed --class=AdminSeeder --force
```

Default login is `admin@email.com` / `12345678` — **change this password
immediately** from Profile → Security after first login.

## 10. Post-launch checklist

- [ ] `APP_DEBUG=false` in `.env` (already set in the production template — just don't flip it back)
- [ ] Real domain + SSL working (padlock in browser)
- [ ] `TRUSTED_PROXIES` set to your actual reverse proxy IP if there is one (Cloudflare, load balancer) — leave `*` only if Nginx faces the internet directly
- [ ] Add at least one working SMTP account under Email Accounts and hit **Test**
- [ ] `supervisorctl status` shows the worker running
- [ ] `crontab -l` shows the schedule:run entry
- [ ] Send one real test email end-to-end and confirm open/click tracking registers
