#!/bin/sh
set -e

echo "==> Running migrations..."
php artisan migrate --force

echo "==> Seeding superadmin..."
php artisan db:seed --class=DatabaseSeeder --force

echo "==> Caching config & routes..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Starting supervisord..."
exec /usr/bin/supervisord -c /etc/supervisord.conf
