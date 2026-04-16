#!/bin/sh
set -e

echo "STARTING APP"
echo "PORT=$PORT"

# Clear & optimize
php artisan optimize:clear
php artisan config:cache
php artisan route:cache

# DB
php artisan migrate --force
php artisan db:seed --force

# Storage link
php artisan storage:link || true

# Fix permissions
chmod -R 775 storage bootstrap/cache

echo "STARTING SERVER"
exec php -S 0.0.0.0:${PORT} -t public/