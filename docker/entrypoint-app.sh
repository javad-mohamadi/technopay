#!/bin/sh
set -e

echo "========================= Entrypoint is running ========================="

echo "Waiting for MySQL container to start..."
sleep 15

echo "==> Running composer install"
composer install --no-interaction --prefer-dist --optimize-autoloader

echo "==> Dumping autoload"
composer dump-autoload

echo "==> Setting correct permissions"

mkdir -p /var/www/storage /var/www/bootstrap/cache

chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

find /var/www/storage -type d -exec chmod 775 {} \;
find /var/www/storage -type f -exec chmod 664 {} \;
find /var/www/bootstrap/cache -type d -exec chmod 775 {} \;
find /var/www/bootstrap/cache -type f -exec chmod 664 {} \;

echo "==> Checking Passport keys..."

if [ ! -f /var/www/storage/oauth-private.key ] || [ ! -f /var/www/storage/oauth-public.key ]; then
    echo "==> Passport keys not found. Generating new keys..."
    php /var/www/artisan passport:keys --force || true
else
    echo "==> Passport keys already exist."
fi

if [ -f /var/www/storage/oauth-private.key ]; then
    chmod 600 /var/www/storage/oauth-private.key
    chown www-data:www-data /var/www/storage/oauth-private.key
fi

if [ -f /var/www/storage/oauth-public.key ]; then
    chmod 600 /var/www/storage/oauth-public.key
    chown www-data:www-data /var/www/storage/oauth-public.key
fi

if [ "${APP_ENV}" = "local" ] || [ "${APP_ENV}" = "development" ]; then
    echo "[$(date)] Running migrations..."
    php /var/www/artisan migrate --force || true

    echo "[$(date)] Running seeders..."
    php /var/www/artisan db:seed --force || true
fi

echo "==> Clearing Laravel caches"
php /var/www/artisan config:clear || true
php /var/www/artisan cache:clear || true
php /var/www/artisan route:clear || true
php /var/www/artisan view:clear || true
php /var/www/artisan clear-compiled || true

echo "==> Caches cleared successfully"

echo "[$(date)] Starting PHP-FPM as www-data..."
exec php-fpm
