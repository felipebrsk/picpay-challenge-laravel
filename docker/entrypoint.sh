#!/usr/bin/env bash

if [[ ! -f .env ]]
then
    cp .env.example .env
fi
composer install
php artisan migrate
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
