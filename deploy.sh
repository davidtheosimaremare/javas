#!/bin/bash
set -e

echo "--- Memulai Deployment ke JBB ---"

# 1. Masuk mode maintenance
php artisan down || true

# 2. Tarik codingan terbaru
git pull origin main

# 3. Install dependency PHP (PENTING: Pakai composer.phar)
php composer.phar install --no-dev --optimize-autoloader

# 4. Update Database
php artisan migrate --force
# php artisan db:seed --force  <-- Nyalakan ini kalau butuh seeder

# 5. Build Frontend Inertia
npm install
npm run build

# 6. Bersihkan Cache
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Website Online lagi
php artisan up

echo "--- Deployment Selesai! ---"