#!/bin/bash
set -e

cd /var/www/sayarat-api

git pull origin main
composer install --no-dev --optimize-autoloader

php artisan down --render="errors::503" --retry=60

php artisan migrate --force
php artisan db:seed --class=ProductionSeeder --force

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

php artisan storage:link
php artisan queue:restart

chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

php artisan up

echo "Deployment complete"
