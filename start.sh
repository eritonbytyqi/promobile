#!/bin/sh
set -e

echo "STARTING APP"
echo "PORT=$PORT"

php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan migrate --force

echo "RUNNING SEED"
php artisan db:seed --force

php artisan storage:link || true
chmod -R 775 storage bootstrap/cache

echo "STARTING SERVER"
exec php -S 0.0.0.0:${PORT} -t public/
