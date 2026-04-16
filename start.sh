#!/bin/sh
set -e

echo "STARTING APP"
echo "PORT=$PORT"

php artisan optimize:clear
php artisan migrate --force

echo "STARTING SERVER"
exec php artisan serve --host=0.0.0.0 --port=${PORT}