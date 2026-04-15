#!/bin/sh

echo "STARTING APP"

php artisan config:clear
php artisan cache:clear
php artisan optimize:clear

echo "ENV TEST:"
printenv | grep DB_

php artisan migrate --force

php artisan serve --host=0.0.0.0 --port=${PORT:-8080}