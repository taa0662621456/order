#!/usr/bin/env bash
set -e
composer install
php bin/console doctrine:migrations:migrate --no-interaction
vendor/bin/phpunit --testdox
