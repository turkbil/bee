#!/bin/bash

echo "📝 Docker Container Logs"
echo "======================="

# Tüm servis logları
echo "🐳 Tüm servis logları için: docker compose logs -f"
echo "📦 Belirli servis için: docker compose logs -f [servis_adı]"
echo ""
echo "Mevcut servisler:"
echo "- app (Laravel uygulaması)"
echo "- mysql (MySQL veritabanı)"
echo "- redis (Redis cache)"
echo "- nginx (Web server)"
echo "- worker (Queue worker)"
echo "- scheduler (Cron scheduler)"
echo ""

# Seçenek menüsü
echo "Hangi logları görmek istiyorsunız?"
echo "1) Tüm loglar"
echo "2) Sadece Laravel app"
echo "3) Sadece MySQL"
echo "4) Sadece Redis"
echo "5) Sadece Nginx"
echo "6) Sadece Worker"
echo "7) Sadece Scheduler"
echo ""

read -p "Seçiminizi yapın (1-7): " choice

case $choice in
    1)
        echo "📋 Tüm container logları gösteriliyor..."
        docker compose logs -f
        ;;
    2)
        echo "📱 Laravel app logları gösteriliyor..."
        docker compose logs -f app
        ;;
    3)
        echo "🗄️ MySQL logları gösteriliyor..."
        docker compose logs -f mysql
        ;;
    4)
        echo "📈 Redis logları gösteriliyor..."
        docker compose logs -f redis
        ;;
    5)
        echo "🌐 Nginx logları gösteriliyor..."
        docker compose logs -f nginx
        ;;
    6)
        echo "⚡ Worker logları gösteriliyor..."
        docker compose logs -f worker
        ;;
    7)
        echo "⏰ Scheduler logları gösteriliyor..."
        docker compose logs -f scheduler
        ;;
    *)
        echo "❌ Geçersiz seçim. Tüm loglar gösteriliyor..."
        docker compose logs -f
        ;;
esac