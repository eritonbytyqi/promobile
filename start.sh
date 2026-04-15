#!/bin/sh

echo "STARTING APP"

php artisan optimize:clear
php artisan migrate --force
php artisan migrate:status

php artisan serve --host=0.0.0.0 --port=${PORT:-8080}