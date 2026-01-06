#!/bin/bash

# =============================================================================
# DEPLOYMENT SCRIPT - MANUEL DEPLOY
# =============================================================================
# Kullanım: bash deploy.sh
# =============================================================================

set -e  # Hata olursa durdur

echo "🚀 Deployment başlıyor..."
echo ""

# 1. Git Pull
echo "📥 1/6: Git pull..."
git pull origin main

# 2. Composer Dependencies
echo "📦 2/6: Composer dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader

# 3. NPM Dependencies (eğer package.json değiştiyse)
if git diff HEAD@{1} --name-only | grep -q "package.json"; then
    echo "📦 3/6: NPM install (package.json değişti)..."
    npm install
else
    echo "⏩ 3/6: NPM install atlandı (package.json değişmedi)"
fi

# 4. Asset Build (Production)
echo "🏗️  4/6: Asset build..."
npm run prod

# 5. Database Migration (opsiyonel, dikkatli!)
# echo "🗄️  5/6: Database migration..."
# php artisan migrate --force
echo "⏩ 5/6: Migration atlandı (manuel çalıştır: php artisan migrate)"

# 6. Cache Temizleme + Optimizasyon
echo "🧹 6/6: Cache temizleme..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan responsecache:clear

echo "⚡ Cache optimizasyonu..."
php artisan config:cache
php artisan route:cache

# OPcache reset (web üzerinden)
echo "🔄 OPcache reset..."
curl -s -k https://muzibu.com.tr/opcache-reset.php > /dev/null
curl -s -k https://ixtif.com/opcache-reset.php > /dev/null

echo ""
echo "✅ Deployment tamamlandı!"
echo "🎉 Yeni kod aktif!"
