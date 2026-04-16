#!/bin/sh
set -e

echo "STARTING APP"

php artisan optimize:clear
php artisan config:clear
php artisan cache:clear

php artisan migrate --force

echo "STARTING SERVER"

php -S 0.0.0.0:$PORT -t public