#!/bin/bash

echo "🐳 500-Tenant Laravel Docker Deployment Başlatılıyor..."

# Eski container'ları durdur ve sil
echo "📦 Eski container'ları temizliyor..."
docker compose down --volumes --remove-orphans

# Image'ları tekrar build et
echo "🏗️ Docker images yeniden build ediliyor..."
docker compose build --no-cache

# Servisleri başlat
echo "🚀 Servisleri başlatıyor..."
docker compose up -d

# Container durumlarını kontrol et
echo "📋 Container durumları:"
docker compose ps

# MySQL bağlantısı için bekle
echo "⏳ MySQL başlatması için bekleniyor..."
sleep 20

# Laravel setup komutları
echo "⚡ Laravel kurulum komutları çalıştırılıyor..."
docker compose exec app composer install --optimize-autoloader --no-dev
docker compose exec app php artisan key:generate
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache

# Database migration ve seed
echo "🗄️ Database migration ve seed işlemleri..."
docker compose exec app php artisan migrate:fresh --seed

# Storage link
echo "🔗 Storage link oluşturuluyor..."
docker compose exec app php artisan storage:link

# Tenant kurulumları
echo "🏢 Tenant kurulumları başlatılıyor..."
docker compose exec app php artisan tenants:install

echo "✅ Docker deployment tamamlandı!"
echo "🌐 Site: http://localhost"
echo "📊 PHPMyAdmin: http://localhost:8080"
echo "📈 Redis Commander: http://localhost:8081"

# Log takibi
echo "📝 Real-time log takibi için: docker compose logs -f"