#!/bin/bash
set -e

echo "--- 🚀 Memulai Auto Deploy ke JBB ---"

# Function untuk rollback jika terjadi error
rollback() {
    echo "❌ Deployment gagal! Melakukan rollback..."
    if [ -d "public/build.backup" ]; then
        rm -rf public/build
        mv public/build.backup public/build
        echo "✅ Rollback build selesai"
    fi
    php artisan up
    exit 1
}

# Set trap untuk rollback otomatis jika error
trap rollback ERR

# 1. Pre-deployment checks
echo "🔍 Checking prerequisites..."
if ! command -v composer &> /dev/null; then
    echo "⚠️  Composer not found in PATH, checking for composer.phar..."
    if [ -f "composer.phar" ]; then
        COMPOSER_CMD="php composer.phar"
    else
        echo "❌ Composer tidak ditemukan!"
        exit 1
    fi
else
    COMPOSER_CMD="composer"
fi

if ! command -v npm &> /dev/null; then
    echo "❌ NPM tidak ditemukan!"
    exit 1
fi

# 2. Pull latest code dari GitHub
echo "📥 Pulling latest code from GitHub..."
git fetch origin main
git reset --hard origin/main

# 3. Backup build lama untuk rollback
echo "💾 Backing up current build..."
if [ -d "public/build" ]; then
    rm -rf public/build.backup
    cp -r public/build public/build.backup
fi

# 4. Install Dependencies PHP
echo "📦 Installing PHP dependencies..."
$COMPOSER_CMD install --no-dev --optimize-autoloader --no-interaction

# 5. Install & Build Frontend (di background, website masih jalan)
echo "🎨 Building frontend assets..."
npm install
npm run build

# 6. Verify build berhasil
if [ ! -d "public/build" ]; then
    echo "❌ Build frontend gagal!"
    rollback
fi

# 7. Masuk Mode Maintenance (singkat, hanya untuk migrate)
echo "🔧 Entering maintenance mode..."
php artisan down --render="errors::503" --retry=60 || true

# 8. Clear cache & optimize
echo "🧹 Clearing cache..."
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 9. Migrate database
echo "🗄️  Running database migrations..."
php artisan migrate --force

# 10. Optimize untuk production
echo "⚡ Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 11. Keluar dari maintenance mode
echo "✅ Exiting maintenance mode..."
php artisan up

# 12. Cleanup backup jika semua sukses
echo "🧹 Cleaning up..."
rm -rf public/build.backup

echo "--- ✅ Deployment Selesai! Website sudah update! 🎉 ---"