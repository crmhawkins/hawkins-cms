#!/bin/sh

echo "==> Running migrations..."
php artisan migrate --force || echo "[WARN] migrate failed, continuing..."

echo "==> Seeding..."
php artisan db:seed --class=DatabaseSeeder --force || echo "[WARN] seed failed, continuing..."
php artisan db:seed --class=DemoContentSeeder --force || echo "[WARN] DemoContentSeeder failed, continuing..."

echo "==> Storage link..."
php artisan storage:link || true

echo "==> Clearing old caches..."
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

echo "==> Caching config & routes..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "==> Starting supervisord..."
exec /usr/bin/supervisord -c /etc/supervisord.conf
