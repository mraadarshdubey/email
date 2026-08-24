# Production image for Sendpeak (Laravel) — nginx + php-fpm 8.3
# (8.3 required: maennchen/zipstream-php, pulled in via Excel import, needs ^8.3)
FROM webdevops/php-nginx:8.3-alpine

ENV WEB_DOCUMENT_ROOT=/app/public \
    APP_ENV=production

WORKDIR /app

# Copy application source (build context = extracted app, .env excluded)
COPY . /app

# Install PHP deps without dev tooling; scripts run lazily at runtime.
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts \
 && chown -R application:application /app/storage /app/bootstrap/cache
