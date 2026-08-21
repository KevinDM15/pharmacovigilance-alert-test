#!/bin/sh
set -e

php artisan migrate --force

if [ "$(php artisan tinker --execute="echo \App\Models\User::count();")" = "0" ]; then
    php artisan db:seed --force
fi

php artisan serve --host=0.0.0.0 --port=8000
