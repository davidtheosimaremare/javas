#!/bin/bash
set -e

echo "--- Memulai Deployment ke JBB ---"

# 1. Masuk mode maintenance (agar user tidak error saat proses)
php artisan down || true

# 2. Tarik codingan terbaru
git pull origin main

# 3. Install dependency PHP
composer install --no-dev --optimize-autoloader

# 4. Update Database (Migrasi struktur tabel)
php artisan migrate --force

# 5. Build Frontend Inertia (PENTING: agar update tampilan masuk)
npm install
npm run build

# 6. Bersihkan Cache Laravel
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Website Online lagi
php artisan up

echo "--- Deployment Selesai! ---"