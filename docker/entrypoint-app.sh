#!/bin/sh
echo "=========================Entrypoint is running========================="

echo "Waiting for MySQL container to start..."
sleep 15  # Add a delay of 15 seconds (adjust as needed)

echo "composer install"
composer install

echo "composer dum autoload"
composer dump-autoload

chmod -R 777 /var/www/storage /var/www/bootstrap/cache


echo "==> Start to run migrations"
php /var/www/artisan migrate
echo "==> Complete migrations"

echo "==> Start to run seeder"
php /var/www/artisan db:seed
echo "==> Complete seeder"

echo "==> Start to clear cached data"
php /var/www/artisan config:clear
php /var/www/artisan cache:clear
php /var/www/artisan route:clear
php /var/www/artisan view:clear
php /var/www/artisan clear-compiled
echo "==> Cached cleared successfully"

if [ "${APP_ENV}" = "local" ] || [ "${APP_ENV}" = "development" ]; then
    echo "[$(date)] Running migrations..."
    php artisan migrate --force

    echo "[$(date)] Running seeders..."
    php artisan db:seed --force
fi

echo "[$(date)] Starting PHP-FPM..."

exec php-fpm
