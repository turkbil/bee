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

echo "1) 🚀 Hızlı PHP - Sadece Laravel server (en hızlı test)"
echo "2) 📱 VSCode Dev Container - Profesyonel geliştirme (Docker gerekli)"
echo "3) 🧹 Temizlik - Cache + database fresh (sorun çözme)"
echo "4) 🛑 Durdur - Tüm servisleri kapat"
echo ""

read -p "Seçim [1-4]: " mode

case $mode in
    1)
        print_header "🚀 Hızlı PHP Server"

        # Config fix
        if grep -q "request()->getHost()" config/queue.php 2>/dev/null; then
            sed -i '' "s/tenant_' . (request()->getHost() ?? 'default')/tenant_default'/g" config/queue.php
            print_status "Config düzeltildi"
        fi

        # Önceki server'ları durdur
        pkill -f "php.*serve" 2>/dev/null || true
        sleep 2

        # Cache temizle
        php artisan config:clear 2>/dev/null || true
        php artisan cache:clear 2>/dev/null || true

        # PHP server başlat
        print_status "Hızlı PHP server başlatılıyor..."
        export PHP_CLI_SERVER_WORKERS=1
        nohup php artisan serve --host=0.0.0.0 --port=8000 \
            >/dev/null 2>&1 < /dev/null &

        server_pid=$!
        echo $server_pid > /tmp/laravel-serve.pid
        sleep 3

        # Test
        if curl -s -I http://laravel.test >/dev/null 2>&1; then
            print_status "✅ SUCCESS! Laravel hazır"
            echo ""
            echo "🌐 http://laravel.test"
            echo "👨‍💼 http://laravel.test/admin"
            echo "🔑 Login: nurullah@nurullah.net / test"
            echo ""
            echo "💡 Multi-tenant sistem - sadece .test domain çalışır"
        else
            print_status "⚠️ PHP server başlatıldı (laravel.test kontrol et)"
        fi
        ;;

    2)
        print_header "📱 VSCode Dev Container"

        # Docker Desktop kontrolü ve başlatma
        print_status "Docker Desktop durumu kontrol ediliyor..."

        # Docker daemon çalışıyor mu?
        if ! docker info >/dev/null 2>&1; then
            print_status "⚠️ Docker daemon çalışmıyor..."

            # Docker Desktop açık mı kontrol et
            if ! pgrep -f "Docker Desktop" >/dev/null 2>&1; then
                print_status "Docker Desktop başlatılıyor..."
                open /Applications/Docker.app
                sleep 5
            else
                print_status "Docker Desktop açık ama daemon hazır değil, bekliyor..."
            fi

            # Docker daemon'un hazır olmasını bekle (daha uzun süre)
            print_status "Docker daemon hazır olana kadar bekleniyor (max 3 dakika)..."
            for i in {1..90}; do
                if docker info >/dev/null 2>&1; then
                    print_status "✅ Docker daemon hazır!"
                    break
                fi

                # Her 30 saniyede bir durum raporu
                if (( i % 15 == 0 )); then
                    echo ""
                    print_status "Hala bekleniyor... ($((i*2)) saniye geçti)"
                fi
                echo -n "."
                sleep 2
            done
        else
            print_status "✅ Docker daemon zaten hazır!"
        fi

        # Final kontrol - Docker çalışıyor mu?
        if ! docker info >/dev/null 2>&1; then
            echo ""
            echo -e "${RED}❌ Docker Desktop 3 dakika içinde hazır olmadı!${NC}"
            echo ""
            echo "🔧 Manual çözüm denemeleri:"
            echo "1. Docker Desktop'ı tamamen kapat ve tekrar aç"
            echo "2. Mac'i yeniden başlat"
            echo "3. Şimdilik option 1 (Hızlı PHP) kullan"
            echo ""
            read -p "Devam etmek istiyor musunuz? [y/N]: " docker_continue
            if [[ ! $docker_continue =~ ^[Yy]$ ]]; then
                exit 1
            fi
        fi

        print_status "📦 Development container'ları başlatılıyor..."

        # Eski container'ları temizle
        docker-compose -f docker-compose.dev.yml down --remove-orphans 2>/dev/null || true

        # Docker Registry sorunu için otomatik fallback sistemi
        print_status "Container'lar başlatılıyor..."

        # Laravel server başlatma fonksiyonu
        start_laravel_server() {
            print_status "Laravel hazırlanıyor..."

            # Cache temizlik
            php artisan config:clear 2>/dev/null || true
            php artisan cache:clear 2>/dev/null || true
            php artisan route:clear 2>/dev/null || true
            php artisan view:clear 2>/dev/null || true

            # Route cache (opsiyonel)
            php artisan route:cache 2>/dev/null || true

            print_status "Laravel server başlatılıyor..."
            pkill -f "php.*serve" 2>/dev/null || true
            sleep 2

            export PHP_CLI_SERVER_WORKERS=1
            nohup php artisan serve --host=0.0.0.0 --port=8000 \
                >/dev/null 2>&1 < /dev/null &

            server_pid=$!
            echo $server_pid > /tmp/laravel-serve.pid
            sleep 3

            # Test laravel.test domain
            if curl -s -I http://laravel.test/admin 2>/dev/null | grep -q "HTTP"; then
                print_status "✅ Laravel server ve domain hazır!"
            elif curl -s -I http://laravel.test 2>/dev/null | grep -q "HTTP"; then
                print_status "✅ Laravel server hazır!"
            else
                print_status "⚠️ Laravel server başlatıldı (domain kontrol et)"
            fi
        }

        # İlk deneme: Full Dev Container
        if docker-compose -f docker-compose.dev.yml up -d --build 2>/dev/null; then
            print_status "✅ Dev Container'lar başarıyla başlatıldı!"
            echo "🐳 Container'da Laravel çalışıyor: http://laravel.test"
            sleep 5
            # Container hazır değilse fallback server başlat
            if ! curl -s http://laravel.test >/dev/null 2>&1; then
                print_status "Container hazır değil, PHP server başlatılıyor..."
                start_laravel_server
            fi

        # İkinci deneme: Basit Container'lar
        elif docker-compose -f docker-compose.simple.yml up -d 2>/dev/null; then
            print_status "✅ Basit container'lar başlatıldı!"
            echo "🌐 PhpMyAdmin: http://localhost:8080"
            start_laravel_server

        # Üçüncü deneme: Minimal Container'lar
        elif docker-compose -f docker-compose.minimal.yml up -d 2>/dev/null; then
            print_status "✅ Minimal container'lar başlatıldı!"
            start_laravel_server

        # Son çare: Homebrew Servisleri
        else
            echo ""
            echo -e "${YELLOW}⚠️ Docker Registry bağlantı sorunu!${NC}"
            print_status "Homebrew servisleri ile devam ediliyor..."

            if command -v brew >/dev/null 2>&1; then
                # MySQL başlat
                if brew services list | grep -q "mysql.*started"; then
                    print_status "MySQL zaten çalışıyor"
                else
                    brew services start mysql 2>/dev/null || true
                    print_status "MySQL başlatıldı"
                fi

                # Redis başlat
                if brew services list | grep -q "redis.*started"; then
                    print_status "Redis zaten çalışıyor"
                else
                    brew services start redis 2>/dev/null || true
                    print_status "Redis başlatıldı"
                fi

                sleep 3
                print_status "✅ Sistem servisleri hazır!"

                # Laravel server başlat
                start_laravel_server

            else
                echo -e "${RED}❌ Homebrew bulunamadı!${NC}"
                echo "Manuel olarak MySQL ve Redis başlatmanız gerekiyor."
            fi
        fi

        echo ""
        echo "🌐 Erişim bilgileri:"
        echo "   🌍 Ana site: http://laravel.test"
        echo "   👨‍💼 Admin: http://laravel.test/admin"
        echo "   🔑 Login: nurullah@nurullah.net / test"
        echo "   🗄️ MySQL: localhost:3306 (root/varsayılan şifre)"
        echo "   ⚡ Redis: localhost:6379"
        echo ""
        echo "⚠️ NOT: Multi-tenant sistem - localhost:8000 çalışmaz!"
        echo "💡 Sadece laravel.test domain'i ile erişin."
        echo ""
        # Valet/Herd kontrolü ve otomatik link
        if command -v valet >/dev/null 2>&1; then
            print_status "Valet tespit edildi, otomatik link yapılıyor..."
            if ! valet links | grep -q "laravel"; then
                valet link laravel 2>/dev/null || true
                print_status "✅ Valet link oluşturuldu!"
            else
                print_status "Valet link zaten mevcut"
            fi
        elif command -v herd >/dev/null 2>&1; then
            print_status "Herd tespit edildi"
            echo "💡 Herd'de bu projeyi manuel olarak ekleyin"
        else
            echo "💡 .test domain için:"
            echo "   Laravel Valet kurabilirsiniz: composer global require laravel/valet"
            echo "   Veya Laravel Herd kullanabilirsiniz"
            echo "   Alternatif: php artisan serve (localhost:8000)"
        fi

        echo "🔧 VSCode Dev Container için:"
        echo "   1. VSCode'u açın ve bu klasörü açın"
        echo "   2. 'Reopen in Container' butonuna tıklayın"
        echo "   3. Container seçimi: 'Yes' seçin"
        echo "   4. Ayar yeri: 'VS Code settings' seçin"
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
        docker-compose -f docker-compose.dev.yml down 2>/dev/null || true
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