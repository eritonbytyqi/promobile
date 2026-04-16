#!/bin/sh
set -e

echo "STARTING APP"

php artisan optimize:clear
php artisan config:clear
php artisan cache:clear

echo "ENV DB CHECK"
printenv | grep DB_

echo "RUN MIGRATIONS"
php artisan migrate:fresh --force

php artisan serve --host=0.0.0.0 --port=${PORT:-8080}