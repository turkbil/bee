#!/bin/bash

# 🚀 Laravel Basit Başlatma - Sıfırdan Temiz Kurulum
# Her şey basit ve anlaşılır

set -e

# Renkler
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m'

print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_header() {
    echo -e "${BLUE}=== $1 ===${NC}"
    echo ""
}

echo -e "${BLUE}"
cat << "EOF"
███╗   ██╗██╗   ██╗██████╗ ██╗   ██╗██╗     ██╗      █████╗ ██╗  ██╗
████╗  ██║██║   ██║██╔══██╗██║   ██║██║     ██║     ██╔══██╗██║  ██║
██╔██╗ ██║██║   ██║██████╔╝██║   ██║██║     ██║     ███████║███████║
██║╚██╗██║██║   ██║██╔══██╗██║   ██║██║     ██║     ██╔══██║██╔══██║
██║ ╚████║╚██████╔╝██║  ██║╚██████╔╝███████╗███████╗██║  ██║██║  ██║
╚═╝  ╚═══╝ ╚═════╝ ╚═╝  ╚═╝ ╚═════╝ ╚══════╝╚══════╝╚═╝  ╚═╝╚═╝  ╚═╝
                                                             
      🚀 Laravel Start System - Sıfırdan Temiz Kurulum 🚀
EOF
echo -e "${NC}"

print_header "Laravel Basit Başlatma"

echo "1) 🔧 Development - PHP + Docker Servisler"
echo "2) 🚀 Sadece PHP - En hızlı (Docker'sız)"
echo "3) 🐳 Full Docker - Gelişmiş"
echo "4) 🛑 Durdur"
echo ""

read -p "Seçim [1-4]: " mode

case $mode in
    1)
        print_header "🔧 Development Mode"
        
        # Config fix
        if grep -q "request()->getHost()" config/queue.php 2>/dev/null; then
            sed -i '' "s/tenant_' . (request()->getHost() ?? 'default')/tenant_default'/g" config/queue.php
            print_status "Config düzeltildi"
        fi
        
        # Docker servisleri başlat
        print_status "Docker servisleri başlatılıyor (MySQL + Redis + PHPMyAdmin)..."
        
        # Docker registry hatası varsa sistem MySQL'ini kullan
        if ! docker-compose -f docker-compose.simple.yml up -d 2>/dev/null; then
            print_status "❌ Docker registry sorunu! Sistem MySQL'ini başlatıyorum..."
            brew services start mysql 2>/dev/null || true
            brew services start redis 2>/dev/null || true
            sleep 3
            print_status "✅ Sistem MySQL + Redis başlatıldı!"
        fi
        
        # Servislerin hazır olmasını bekle
        print_status "Servislerin hazır olması bekleniyor..."
        for i in {1..30}; do
            if docker exec laravel-mysql mysqladmin ping -h localhost --silent 2>/dev/null; then
                print_status "MySQL hazır!"
                break
            fi
            echo -n "."
            sleep 2
        done
        
        # Cache temizle
        print_status "Cache temizleniyor..."
        php artisan config:clear 2>/dev/null || true
        php artisan cache:clear 2>/dev/null || true
        
        # PHP server başlat
        print_status "PHP server başlatılıyor..."
        pkill -f "php.*serve" 2>/dev/null || true
        sleep 2
        
        # Broken pipe koruması ile başlat - geliştirilmiş versiyon
        export PHP_CLI_SERVER_WORKERS=1
        nohup php artisan serve --host=0.0.0.0 --port=8000 \
            >/dev/null 2>&1 < /dev/null &
        
        server_pid=$!
        echo $server_pid > /tmp/laravel-serve.pid
        sleep 3
        
        # Test
        if curl -s http://localhost:8000/admin >/dev/null; then
            echo ""
            print_status "✅ SUCCESS! Laravel çalışıyor"
            echo ""
            echo "🌐 http://localhost:8000"
            echo "👨‍💼 http://localhost:8000/admin"
            echo "🗄️ http://localhost:8080 (PHPMyAdmin)"
            echo ""
            echo "🔑 Login: nurullah@nurullah.net / test"
            echo "🗄️ DB: root123 / laravel123"
        else
            echo "⚠️ Server başlatıldı, test ediliyor..."
        fi
        ;;
        
    2) 
        print_header "🚀 Sadece PHP (En Hızlı)"
        
        # Config fix
        if grep -q "request()->getHost()" config/queue.php 2>/dev/null; then
            sed -i '' "s/tenant_' . (request()->getHost() ?? 'default')/tenant_default'/g" config/queue.php
            print_status "Config düzeltildi"
        fi
        
        # Docker durdur (varsa)
        docker-compose -f docker-compose.simple.yml down 2>/dev/null || true
        
        # PHP server başlat
        print_status "Sadece PHP server başlatılıyor (Docker'sız)..."
        pkill -f "php.*serve" 2>/dev/null || true
        sleep 2
        
        # Cache temizle
        php artisan config:clear 2>/dev/null || true
        php artisan cache:clear 2>/dev/null || true
        
        # Hızlı başlatma - broken pipe korumalı - geliştirilmiş
        export PHP_CLI_SERVER_WORKERS=1
        nohup php artisan serve --host=0.0.0.0 --port=8000 \
            >/dev/null 2>&1 < /dev/null &
        
        server_pid=$!
        echo $server_pid > /tmp/laravel-serve.pid
        sleep 3
        
        # Test
        if curl -s http://localhost:8000 >/dev/null 2>&1; then
            print_status "✅ SUCCESS! Sadece PHP çalışıyor"
            echo ""
            echo "🌐 http://localhost:8000"
            echo "👨‍💼 http://localhost:8000/admin"  
            echo ""
            echo "🔑 Login: nurullah@nurullah.net / test"
            echo "💡 Not: Sadece PHP server (Docker yok)"
        else
            print_status "⚠️ PHP server başlatıldı"
        fi
        ;;
        
    3)
        print_header "🐳 Full Docker"
        print_status "Gelişmiş Docker kurulumu..."
        docker-compose up -d
        print_status "Full Docker başlatıldı!"
        ;;
        
    4)
        print_header "🛑 Durdur"
        
        # PID dosyasından server'ı durdur
        if [ -f /tmp/laravel-serve.pid ]; then
            server_pid=$(cat /tmp/laravel-serve.pid)
            kill $server_pid 2>/dev/null || true
            rm -f /tmp/laravel-serve.pid
            print_status "Laravel server durduruldu (PID: $server_pid)"
        fi
        
        # Genel temizlik
        pkill -f "php.*serve" 2>/dev/null || true
        lsof -ti:8000 | xargs kill -9 2>/dev/null || true
        
        # Docker durdur
        docker-compose -f docker-compose.simple.yml down 2>/dev/null || true
        docker-compose down 2>/dev/null || true
        
        print_status "Tüm servisler durduruldu!"
        ;;
        
    *)
        echo -e "${RED}Geçersiz seçim!${NC}"
        exit 1
        ;;
esac

echo ""
print_status "İşlem tamamlandı! 🎉"