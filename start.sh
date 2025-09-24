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

echo "1) 🚀 Local Development - Valet + Homebrew (varsayılan)"
echo "2) 🐳 Docker Development - Container ortamı"
echo "3) 🧹 Temizlik - Cache + database fresh"
echo "4) 🛑 Durdur - Tüm servisleri kapat"
echo ""

read -p "Seçim [1-4]: " mode

case $mode in
    1)
        print_header "🚀 Local Development"

        # Homebrew servisleri kontrol et
        print_status "Homebrew servisleri kontrol ediliyor..."

        if ! brew services list | grep -q "mysql.*started"; then
            print_status "MySQL başlatılıyor..."
            brew services start mysql 2>/dev/null || true
            sleep 3
        else
            print_status "✅ MySQL zaten çalışıyor"
        fi

        if ! brew services list | grep -q "redis.*started"; then
            print_status "Redis başlatılıyor..."
            brew services start redis 2>/dev/null || true
            sleep 2
        else
            print_status "✅ Redis zaten çalışıyor"
        fi

        # Cache temizle
        print_status "Laravel cache temizleniyor..."
        php artisan config:clear 2>/dev/null || true
        php artisan cache:clear 2>/dev/null || true

        # Valet kontrol et
        if command -v valet >/dev/null 2>&1; then
            print_status "✅ Valet tespit edildi"

            if curl -s -I http://laravel.test >/dev/null 2>&1; then
                print_status "✅ Laravel domain hazır!"
            else
                print_status "⚠️ Laravel domain erişilemiyor"
                print_status "💡 Manuel: cd $(pwd) && valet link laravel"
            fi
        else
            print_status "⚠️ Valet bulunamadı"
            print_status "💡 Valet kurulumu: composer global require laravel/valet && valet install"
        fi

        # Laravel server başlat
        print_status "Laravel server başlatılıyor..."
        pkill -f "php.*serve.*8001" 2>/dev/null || true
        pkill -f "php.*8001" 2>/dev/null || true
        sleep 3

        # Server'ı başlat ve hemen PID'yi kontrol et
        nohup php artisan serve --host=127.0.0.1 --port=8001 > storage/logs/laravel-server.log 2>&1 &
        sleep 2

        if lsof -ti:8001 >/dev/null 2>&1; then
            print_status "✅ Laravel server başlatıldı!"
        else
            print_status "⚠️ Laravel server başlatılamadı, tekrar deneniyor..."
            sleep 2
            nohup php artisan serve --host=127.0.0.1 --port=8001 > storage/logs/laravel-server.log 2>&1 &
            sleep 2
            if lsof -ti:8001 >/dev/null 2>&1; then
                print_status "✅ Laravel server başlatıldı!"
            else
                print_status "❌ Laravel server başlatılamadı!"
            fi
        fi

        # Horizon başlat
        print_status "Horizon queue sistemi başlatılıyor..."
        php artisan horizon:terminate 2>/dev/null || true
        pkill -f "php.*horizon" 2>/dev/null || true
        sleep 3

        # Horizon'u başlat ve kontrol et
        nohup php artisan horizon > storage/logs/horizon.log 2>&1 &
        sleep 3

        if pgrep -f "php.*horizon" >/dev/null 2>&1; then
            print_status "✅ Horizon başlatıldı!"
        else
            print_status "⚠️ Horizon başlatılamadı, tekrar deneniyor..."
            sleep 2
            nohup php artisan horizon > storage/logs/horizon.log 2>&1 &
            sleep 3
            if pgrep -f "php.*horizon" >/dev/null 2>&1; then
                print_status "✅ Horizon başlatıldı!"
            else
                print_status "❌ Horizon başlatılamadı!"
            fi
        fi

        echo ""
        echo "🌐 Erişim bilgileri:"
        echo "   🌍 Ana site: http://laravel.test"
        echo "   👨‍💼 Admin: http://laravel.test/admin"
        echo "   🚀 Horizon: http://laravel.test/horizon/dashboard"
        echo "   🔭 Telescope: http://laravel.test/telescope"
        echo "   🔑 Login: nurullah@nurullah.net / test"
        echo ""
        echo "🏢 Tenant Siteleri:"
        echo "   🏠 Tenant A: http://a.test"
        echo "   🏠 Tenant B: http://b.test"
        echo "   🏠 Tenant C: http://c.test"
        echo "   🏠 Tenant D: http://d.test"
        # PhpMyAdmin başlat
        print_status "PhpMyAdmin kontrol ediliyor..."
        pkill -f "php.*8090" 2>/dev/null || true
        sleep 2

        if [ -d "/Users/nurullah/Desktop/cms/phpmyadmin" ]; then
            print_status "PhpMyAdmin başlatılıyor..."
            cd /Users/nurullah/Desktop/cms/phpmyadmin
            nohup php -S localhost:8090 >/dev/null 2>&1 &
            cd - >/dev/null
            sleep 2

            if lsof -ti:8090 >/dev/null 2>&1; then
                print_status "✅ PhpMyAdmin başlatıldı!"
            else
                print_status "⚠️ PhpMyAdmin başlatılamadı!"
            fi
        else
            print_status "⚠️ PhpMyAdmin dizini bulunamadı"
        fi

        echo ""
        echo "🗄️ Veritabanı & Cache:"
        echo "   📊 PhpMyAdmin: http://pma.test (otomatik giriş)"
        echo "   🗄️ MySQL: localhost:3306 (root/şifresiz)"
        echo "   ⚡ Redis: localhost:6379"
        echo ""
        echo "💡 Hızlı local development için optimize edildi!"
        echo "💡 PhpMyAdmin config mode - otomatik root giriş yapılır"
        ;;

    2)
        print_header "🐳 Docker Development (Production Test)"

        print_status "⚠️ Bu mod production ortamına benzer container sistemini kullanır"
        print_status "Local development için Option 1 daha hızlıdır"
        echo ""
        read -p "Docker development moduna geçmek istediğinizden emin misiniz? [y/N]: " docker_confirm

        if [[ ! $docker_confirm =~ ^[Yy]$ ]]; then
            print_status "❌ İşlem iptal edildi. Option 1 ile hızlı development yapabilirsiniz."
            exit 0
        fi

        # Docker Desktop kontrolü
        print_status "Docker Desktop kontrol ediliyor..."
        if ! docker info >/dev/null 2>&1; then
            print_status "❌ Docker çalışmıyor!"
            echo "💡 Docker Desktop'ı başlatın: open /Applications/Docker.app"
            echo "💡 Alternatif: Option 1 (Local Development) kullanın"
            exit 1
        fi

        # Local servisleri durdur
        print_status "Local servisleri durduruluyor..."
        php artisan horizon:terminate 2>/dev/null || true
        pkill -f "php.*horizon" 2>/dev/null || true
        pkill -f "php.*serve" 2>/dev/null || true

        # Docker containers başlat
        print_status "🐳 Docker containers başlatılıyor..."

        # Önceki container'ları temizle
        docker-compose -f docker-compose.simple.yml down --remove-orphans 2>/dev/null || true
        docker-compose -f docker-compose.dev.yml down --remove-orphans 2>/dev/null || true

        # Simple containers başlat (MySQL + Redis + PhpMyAdmin)
        if docker-compose -f docker-compose.simple.yml --profile phpmyadmin up -d; then
            print_status "✅ Docker containers başlatıldı!"

            # Container'ların hazır olmasını bekle
            print_status "Container'lar hazırlanıyor..."
            sleep 10

            print_status "✅ Docker sistemine geçiş tamamlandı!"
            echo ""
            echo "🌐 Erişim bilgileri:"
            echo "   🌍 Ana site: http://laravel.test (Valet ile)"
            echo "   👨‍💼 Admin: http://laravel.test/admin"
            echo "   🚀 Horizon: http://laravel.test/horizon/dashboard"
            echo "   🔭 Telescope: http://laravel.test/telescope"
            echo "   🔑 Login: nurullah@nurullah.net / test"
            echo ""
            echo "🗄️ Veritabanı & Cache (Docker):"
            echo "   📊 PhpMyAdmin: http://localhost:8080"
            echo "   🗄️ MySQL: localhost:3306 (root:root123)"
            echo "   ⚡ Redis: localhost:6379"
            echo ""
            echo "💡 .env dosyası Docker ayarlarına uygun mu kontrol edin!"
            echo "💡 Local development için Option 1 kullanın"

        else
            print_status "❌ Docker containers başlatılamadı!"
            echo "💡 Option 1 (Local Development) kullanın"
            exit 1
        fi
        ;;

    3)
        print_header "🧹 Sistem Temizliği"

        print_status "⚠️ Bu işlem tüm veritabanını sıfırlayacak!"
        read -p "Devam etmek istiyor musunuz? [y/N]: " confirm

        if [[ $confirm =~ ^[Yy]$ ]]; then
            print_status "🧹 Kapsamlı temizlik başlıyor..."

            # Laravel artisan komutları
            print_status "Laravel cache'leri temizleniyor..."
            php artisan app:clear-all 2>/dev/null || {
                # app:clear-all yoksa tek tek çalıştır
                php artisan config:clear 2>/dev/null || true
                php artisan cache:clear 2>/dev/null || true
                php artisan route:clear 2>/dev/null || true
                php artisan view:clear 2>/dev/null || true
            }

            print_status "🗄️ Veritabanı yeniden oluşturuluyor..."
            php artisan migrate:fresh --seed

            print_status "📦 Modül cache'leri temizleniyor..."
            php artisan module:clear-cache 2>/dev/null || true

            print_status "⚡ Response cache temizleniyor..."
            php artisan responsecache:clear 2>/dev/null || true

            print_status "🔭 Telescope temizleniyor..."
            php artisan telescope:clear 2>/dev/null || true

            print_status "✅ Kapsamlı temizlik tamamlandı!"
            echo ""
            echo "🔄 Sistem tamamen temizlendi:"
            echo "   ✓ Tüm cache'ler"
            echo "   ✓ Database fresh + seed"
            echo "   ✓ Modül cache'leri"
            echo "   ✓ Response cache"
            echo "   ✓ Telescope data"
        else
            print_status "❌ İşlem iptal edildi."
        fi
        ;;

    4)
        print_header "🛑 Servisleri Durdur"

        # Horizon durdur
        print_status "Horizon queue sistemi durduruluyor..."
        php artisan horizon:terminate 2>/dev/null || true
        pkill -f "php.*horizon" 2>/dev/null || true

        # PID dosyasından server'ı durdur
        if [ -f /tmp/laravel-serve.pid ]; then
            server_pid=$(cat /tmp/laravel-serve.pid)
            kill $server_pid 2>/dev/null || true
            rm -f /tmp/laravel-serve.pid
            print_status "Laravel server durduruldu (PID: $server_pid)"
        fi

        # Genel temizlik
        pkill -f "php.*serve" 2>/dev/null || true
        pkill -f "php.*8090" 2>/dev/null || true
        lsof -ti:8001 | xargs kill -9 2>/dev/null || true
        lsof -ti:8090 | xargs kill -9 2>/dev/null || true

        # Docker durdur
        docker-compose -f docker-compose.simple.yml down 2>/dev/null || true
        docker-compose -f docker-compose.dev.yml down 2>/dev/null || true
        docker-compose down 2>/dev/null || true

        print_status "✅ Tüm servisler durduruldu!"
        ;;

    *)
        echo -e "${RED}Geçersiz seçim!${NC}"
        exit 1
        ;;
esac

echo ""
echo "🔍 Final Durum Kontrolü:"
if lsof -ti:8001 >/dev/null 2>&1; then
    echo "   ✅ Laravel Server (8001): Çalışıyor"
else
    echo "   ❌ Laravel Server (8001): Çalışmıyor"
fi

if pgrep -f "php.*horizon" >/dev/null 2>&1; then
    echo "   ✅ Horizon: Çalışıyor"
else
    echo "   ❌ Horizon: Çalışmıyor"
fi

if lsof -ti:8090 >/dev/null 2>&1; then
    echo "   ✅ PhpMyAdmin (8090): Çalışıyor"
else
    echo "   ❌ PhpMyAdmin (8090): Çalışmıyor"
fi

if brew services list | grep -q "mysql.*started"; then
    echo "   ✅ MySQL: Çalışıyor"
else
    echo "   ❌ MySQL: Çalışmıyor"
fi

if brew services list | grep -q "redis.*started"; then
    echo "   ✅ Redis: Çalışıyor"
else
    echo "   ❌ Redis: Çalışmıyor"
fi

echo ""
print_status "İşlem tamamlandı! 🎉"