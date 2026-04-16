#!/bin/sh
set -e

echo "STARTING APP"

php artisan optimize:clear
php artisan config:clear
php artisan cache:clear

echo "ENV DB CHECK"
printenv | grep DB_

echo "MIGRATION STATUS BEFORE"
php artisan migrate:status || true

echo "RUN FRESH MIGRATIONS"
php artisan migrate:fresh --force

echo "MIGRATION STATUS AFTER"
php artisan migrate:status || true

php artisan serve --host=0.0.0.0 --port=${PORT:-8080}