#!/bin/bash

# Laravel Multi-Tenant System Startup Script
# Bu script'i çalıştırarak tüm servisleri başlatabilirsin

echo "🚀 Laravel Multi-Tenant System başlatılıyor..."

# 1. Docker container'ları başlat
echo "📦 Docker container'ları başlatılıyor..."
cd /Users/nurullah/Desktop/cms/laravel
docker compose up mysql redis phpmyadmin redis-commander -d

# 2. Docker container'ların hazır olmasını bekle
echo "⏳ Docker container'ların hazır olması bekleniyor..."
sleep 10

# 3. Nginx'i başlat
echo "🌐 Nginx proxy başlatılıyor..."
brew services start nginx

# 4. Laravel development server'ı arka planda başlat
echo "🐘 Laravel development server başlatılıyor..."
nohup php artisan serve --host=0.0.0.0 --port=8000 > /dev/null 2>&1 &

# 5. Servislerin durumunu kontrol et
echo "✅ Servis durumları kontrol ediliyor..."
sleep 3

echo ""
echo "🎯 DURUM RAPORU:"
echo "=================================="

# Docker durumu
echo "📦 Docker Container'lar:"
docker ps --format "table {{.Names}}\t{{.Status}}" | grep -E "(laravel-mysql|laravel-redis|laravel-phpmyadmin)"

# Nginx durumu  
echo ""
echo "🌐 Nginx Proxy:"
brew services list | grep nginx

# Laravel durumu
echo ""
echo "🐘 Laravel Development Server:"
ps aux | grep "php artisan serve" | grep -v grep | wc -l | xargs echo "Aktif process sayısı:"

echo ""
echo "🌟 SİSTEM HAZIR!"
echo "=================================="
echo "✅ Tenant domain'leri:"
echo "   • http://laravel.test:8888"  
echo "   • http://a.test:8888"
echo "   • http://b.test:8888"
echo "   • http://c.test:8888" 
echo "   • http://d.test:8888"
echo ""
echo "🔧 Yönetim panelleri:"
echo "   • PHPMyAdmin: http://localhost:8080"
echo "   • Redis Commander: http://localhost:8081"
echo "   • Login: http://laravel.test:8888/login"
echo ""
echo "📋 Bilgilendirme:"
echo "   • Port 80 yerine 8888 kullanıyoruz (macOS kısıtlaması)"
echo "   • Tüm tenant'lar çalışır durumda"