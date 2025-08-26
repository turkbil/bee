#!/bin/bash

# 🚀 Laravel 500 Tenant System - Universal Startup Script
# All-in-One System Launcher with Multiple Modes

set -e

# Color codes for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

print_header() {
    echo -e "${BLUE}=== $1 ===${NC}"
}

print_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_menu() {
    echo -e "${CYAN}$1${NC}"
}

# ASCII Art Header
echo -e "${PURPLE}"
cat << "EOF"
   ███╗   ██╗██╗   ██╗██████╗ ██╗   ██╗██╗     ██╗      █████╗ ██╗  ██╗
   ████╗  ██║██║   ██║██╔══██╗██║   ██║██║     ██║     ██╔══██╗██║  ██║
   ██╔██╗ ██║██║   ██║██████╔╝██║   ██║██║     ██║     ███████║███████║
   ██║╚██╗██║██║   ██║██╔══██╗██║   ██║██║     ██║     ██╔══██║██╔══██║
   ██║ ╚████║╚██████╔╝██║  ██║╚██████╔╝███████╗███████╗██║  ██║██║  ██║
   ╚═╝  ╚═══╝ ╚═════╝ ╚═╝  ╚═╝ ╚═════╝ ╚══════╝╚══════╝╚═╝  ╚═╝╚═╝  ╚═╝
                                                             
      🏢 TENANT SYSTEM - UNIVERSAL LAUNCHER 🚀           
EOF
echo -e "${NC}"

print_header "Sistem Modu Seçimi"

# Menu Options
echo ""
print_menu "🚀 Başlatma Modları:"
echo -e "${CYAN}1)${NC} 🔧 ${YELLOW}Development${NC} - Günlük geliştirme (sabah başlarken)"
echo -e "${CYAN}2)${NC} 🔄 ${YELLOW}Clear & Migrate${NC} - Migration/seeder sonrası temizlik"
echo -e "${CYAN}3)${NC} 🐳 ${YELLOW}Full Production${NC} - Production test + monitoring"
echo -e "${CYAN}4)${NC} 🩺 ${YELLOW}Health Check${NC} - Sorun çıktığında otomatik tamir"
echo -e "${CYAN}5)${NC} 🛑 ${YELLOW}Durdur${NC} - İş bitince tüm servisleri kapat"
echo ""

read -p "$(echo -e ${CYAN}Mod seçin [1-5]:${NC} )" mode

case $mode in
    1)
        print_header "🔧 Development Mode (Optimize)"
        print_status "Starting development environment..."
        
        # Stop any Docker apps that might conflict
        docker-compose stop nginx-proxy app1 app2 app3 2>/dev/null || true
        
        # Start Docker support services
        print_status "Starting Docker services (MySQL, Redis, PHPMyAdmin, Redis Commander)..."
        docker-compose up -d mysql-master redis-cluster phpmyadmin redis-commander
        
        # Wait for services
        sleep 10
        
        # KÖKTEN ÇÖZÜM - Nginx ve Broken Pipe
        print_status "Applying ROOT FIXES for Nginx & Broken Pipe..."
        
        # Buffer ayarları - kernel level
        ulimit -n 65536 2>/dev/null || true
        export PHP_CLI_SERVER_WORKERS=1
        export XDEBUG_MODE=off
        
        # Nginx restart (öncekini durdur)
        brew services stop nginx 2>/dev/null || true
        sleep 2
        brew services start nginx
        sleep 3
        
        # FIX CONFIG QUEUE ERROR FIRST
        print_status "Config queue error düzeltiliyor..."
        if grep -q "request()->getHost()" /Users/nurullah/Desktop/cms/laravel/config/queue.php; then
            sed -i '' "s/tenant_' . (request()->getHost() ?? 'default')/tenant_default'/g" /Users/nurullah/Desktop/cms/laravel/config/queue.php
            print_success "Queue config düzeltildi!"
        fi
        
        # Clear cache güvenli şekilde
        php artisan config:clear 2>/dev/null || true
        sleep 1
        php artisan cache:clear 2>/dev/null || true
        sleep 1
        php artisan view:clear 2>/dev/null || true
        sleep 1
        
        # PHP Server optimize başlatma - BROKEN PIPE FIX
        print_status "Starting optimized PHP server..."
        pkill -f "php.*8000" 2>/dev/null || true
        pkill -f "php artisan serve" 2>/dev/null || true
        sleep 2
        
        # ULTIMATE BROKEN PIPE FIX
        nohup php -d output_buffering=4096 -d implicit_flush=Off -d default_socket_timeout=60 -S 0.0.0.0:8000 -t public > /dev/null 2>&1 &
        sleep 3
        
        # Queue Worker otomatik başlat - HER ZAMAN ÇALIŞMALI
        print_status "Queue Worker başlatılıyor..."
        if [ -f "./start-queue-worker.sh" ]; then
            chmod +x ./start-queue-worker.sh
            ./start-queue-worker.sh
        else
            print_warning "start-queue-worker.sh bulunamadı!"
        fi
        
        # Test bağlantı
        if curl -s http://laravel.test > /dev/null; then
            print_success "laravel.test çalışıyor!"
        else
            print_warning "Bağlantı sorunu olabilir"
        fi
        
        print_success "Development Mode Ready! (Queue Worker Active)"
        ;;
        
    2)
        print_header "🔄 Clear & Migrate"
        print_status "Starting safe clear and migrate process..."
        
        # Buffer ayarları
        ulimit -n 65536 2>/dev/null || true
        
        # Doğru sıralama ile temizleme
        php artisan app:clear-all || true
        sleep 2
        php artisan migrate:fresh --seed --force
        sleep 3
        php artisan module:clear-cache || true
        sleep 1
        php artisan responsecache:clear || true
        sleep 1
        php artisan telescope:clear || true
        sleep 1
        
        print_success "Clear & Migrate tamamlandı!"
        say "temizleme ve migrate tamamlandı"
        ;;
        
    3)
        print_header "🐳 Full Production Mode"
        print_status "Starting complete Docker production stack..."
        
        # Stop local PHP if running
        pkill -f "php.*8000" 2>/dev/null || true
        pkill -f "php artisan serve" 2>/dev/null || true
        
        # Start all Docker services
        print_status "Starting full stack: 3 app instances + MySQL + Redis + Monitoring..."
        docker-compose up -d
        
        # Wait for all services to be healthy
        print_status "Waiting for all services to be healthy..."
        sleep 30
        
        # Check status
        docker-compose ps
        
        print_success "Full Production Stack Ready!"
        print_status "Access URLs:"
        echo "🌐 http://laravel.test (Load Balanced)"
        echo "👨‍💼 http://laravel.test/admin"
        echo "🗄️ http://localhost:8080 (PHPMyAdmin)"
        echo "📊 http://localhost:8081 (Redis Commander)"
        echo "📈 http://localhost:3000 (Grafana - admin/admin123)"
        echo "🔍 http://localhost:9090 (Prometheus)"
        say "production stack hazır"
        ;;
        
    4)
        print_header "🩺 Health Check & Auto Repair + BROKEN PIPE FIX"
        print_status "Sistem durumu kontrol ediliyor..."
        
        # BROKEN PIPE FIX FIRST
        print_status "🔧 BROKEN PIPE sorunu kontrol ediliyor ve düzeltiliyor..."
        
        # Mevcut PHP server'ları kapat
        pkill -f "php.*serve" 2>/dev/null || true
        pkill -f "php.*8000" 2>/dev/null || true
        pkill -f "php.*8001" 2>/dev/null || true
        sleep 3
        
        # ULTIMATE BROKEN PIPE FIX
        print_status "Optimized PHP server başlatılıyor..."
        export PHP_CLI_SERVER_WORKERS=1
        export XDEBUG_MODE=off
        ulimit -n 65536 2>/dev/null || true
        
        # En stabil server başlatma
        nohup php -d output_buffering=4096 -d implicit_flush=Off -d default_socket_timeout=60 artisan serve --host=0.0.0.0 --port=8000 > /dev/null 2>&1 &
        sleep 5
        
        # Test connection
        if curl -s -o /dev/null -w "%{http_code}" "http://localhost:8000" | grep -q "200\|302"; then
            print_success "✅ BROKEN PIPE sorunu çözüldü! Port: 8000"
        else
            print_warning "⚠️ Server başlatıldı ancak test edilemiyor"
        fi
        
        # Health Check Functions
        check_php_server() {
            if lsof -i :8000 > /dev/null 2>&1; then
                print_success "PHP Server (8000) - ÇALIŞIYOR"
                return 0
            else
                print_error "PHP Server (8000) - KAPALI"
                return 1
            fi
        }
        
        check_redis() {
            if redis-cli ping > /dev/null 2>&1; then
                print_success "Redis - ÇALIŞIYOR"
                return 0
            else
                print_error "Redis - KAPALI"
                return 1
            fi
        }
        
        check_laravel_sites() {
            # Ana siteler - her zaman kontrol edilir
            core_sites=("laravel.test" "a.test" "b.test" "c.test")
            # Dinamik siteler - varsa kontrol edilir  
            dynamic_sites=("d.test")
            
            failed_sites=()
            
            # Core sites kontrol
            for site in "${core_sites[@]}"; do
                if curl -s -o /dev/null -w "%{http_code}" "http://$site" | grep -q "200\|302"; then
                    print_success "$site - ÇALIŞIYOR"
                else
                    print_error "$site - HATA"
                    failed_sites+=("$site")
                fi
            done
            
            # Dynamic sites kontrol (404 normal)
            for site in "${dynamic_sites[@]}"; do
                http_code=$(curl -s -o /dev/null -w "%{http_code}" "http://$site")
                if [ "$http_code" = "200" ] || [ "$http_code" = "302" ]; then
                    print_success "$site - ÇALIŞIYOR (aktif tenant)"
                elif [ "$http_code" = "404" ]; then
                    print_status "$site - Tenant henüz eklenmemiş (normal)"
                else
                    print_error "$site - HATA (kod: $http_code)"
                    failed_sites+=("$site")
                fi
            done
            
            # Sadece core siteler fail olursa hata dönsün
            core_failed=0
            for site in "${core_sites[@]}"; do
                for failed_site in "${failed_sites[@]}"; do
                    if [ "$site" = "$failed_site" ]; then
                        ((core_failed++))
                    fi
                done
            done
            
            if [ $core_failed -eq 0 ]; then
                return 0
            else
                return 1
            fi
        }
        
        check_docker_services() {
            services=("laravel-mysql-master" "laravel-redis-simple")
            failed_services=()
            
            for service in "${services[@]}"; do
                if docker ps --format "{{.Names}}" | grep -q "^$service$"; then
                    print_success "Docker: $service - ÇALIŞIYOR"
                else
                    print_error "Docker: $service - KAPALI"
                    failed_services+=("$service")
                fi
            done
            
            if [ ${#failed_services[@]} -eq 0 ]; then
                return 0
            else
                return 1
            fi
        }
        
        # Auto Repair Functions
        repair_php_server() {
            print_status "PHP Server otomatik tamir ediliyor (BROKEN PIPE FIX)..."
            pkill -f "php.*serve" 2>/dev/null || true
            pkill -f "php.*8000" 2>/dev/null || true  
            pkill -f "php.*8001" 2>/dev/null || true
            sleep 3
            
            # FIX CONFIG QUEUE ERROR FIRST
            print_status "Config queue error düzeltiliyor..."
            if grep -q "request()->getHost()" /Users/nurullah/Desktop/cms/laravel/config/queue.php; then
                sed -i '' "s/tenant_' . (request()->getHost() ?? 'default')/tenant_default'/g" /Users/nurullah/Desktop/cms/laravel/config/queue.php
                print_success "Queue config düzeltildi!"
            fi
            
            # OPTIMIZED SERVER START
            export PHP_CLI_SERVER_WORKERS=1
            export XDEBUG_MODE=off
            ulimit -n 65536 2>/dev/null || true
            nohup php -d output_buffering=4096 -d implicit_flush=Off -d default_socket_timeout=60 artisan serve --host=0.0.0.0 --port=8000 > /dev/null 2>&1 &
            sleep 5
            
            if check_php_server; then
                print_success "PHP Server başarıyla onarıldı! (Port: 8000)"
            else
                print_error "PHP Server onarılamadı!"
            fi
        }
        
        repair_redis() {
            print_status "Redis otomatik tamir ediliyor..."
            docker run -d --name laravel-redis-simple -p 6379:6379 --network laravel_laravel-network redis:7-alpine redis-server --appendonly yes > /dev/null 2>&1 || true
            sleep 3
            if check_redis; then
                print_success "Redis başarıyla onarıldı!"
            else
                print_error "Redis onarılamadı!"
            fi
        }
        
        repair_laravel() {
            print_status "Laravel cache temizleniyor..."
            php artisan config:clear > /dev/null 2>&1 || true
            php artisan cache:clear > /dev/null 2>&1 || true
            php artisan route:clear > /dev/null 2>&1 || true
            sleep 2
            print_success "Laravel cache temizlendi!"
        }
        
        # Main Health Check
        print_header "🔍 SİSTEM DURUMU RAPORU"
        echo ""
        
        # Check all systems
        php_ok=$(check_php_server && echo "true" || echo "false")
        redis_ok=$(check_redis && echo "true" || echo "false") 
        sites_ok=$(check_laravel_sites && echo "true" || echo "false")
        docker_ok=$(check_docker_services && echo "true" || echo "false")
        
        echo ""
        print_header "🔧 OTOMATİK TAMİR"
        
        # Auto repair if needed
        if [ "$php_ok" = "false" ]; then
            repair_php_server
        fi
        
        if [ "$redis_ok" = "false" ]; then
            repair_redis
        fi
        
        if [ "$sites_ok" = "false" ]; then
            repair_laravel
            sleep 3
            # Re-check sites after repair
            check_laravel_sites > /dev/null 2>&1
        fi
        
        echo ""
        print_header "📊 FINAL RAPOR"
        
        # Final check
        php_final=$(check_php_server > /dev/null 2>&1 && echo "✅" || echo "❌")
        redis_final=$(check_redis > /dev/null 2>&1 && echo "✅" || echo "❌")
        sites_final=$(check_laravel_sites > /dev/null 2>&1 && echo "✅" || echo "❌")
        docker_final=$(check_docker_services > /dev/null 2>&1 && echo "✅" || echo "❌")
        
        echo -e "${CYAN}PHP Server:${NC} $php_final | ${CYAN}Redis:${NC} $redis_final | ${CYAN}Sites:${NC} $sites_final | ${CYAN}Docker:${NC} $docker_final"
        
        # Overall status
        if [ "$php_final" = "✅" ] && [ "$redis_final" = "✅" ] && [ "$sites_final" = "✅" ] && [ "$docker_final" = "✅" ]; then
            print_success "🎉 TÜM SİSTEMLER SAĞLIKLI!"
            say "sistem sağlıklı"
        else
            print_warning "⚠️  Bazı sistemlerde sorun var"
            say "sistem sorunları var"
        fi
        
        echo ""
        print_status "Test URL'leri:"
        echo "🌐 http://laravel.test"
        echo "🏢 http://a.test | http://b.test"
        echo "🗄️ http://localhost:8080 (PHPMyAdmin)"
        ;;
        
    5)
        print_header "🛑 Tüm Servisleri Durdur"
        
        docker-compose down 2>/dev/null || true
        pkill -f "php artisan serve" 2>/dev/null || true
        pkill -f "php.*8000" 2>/dev/null || true
        pkill -f "php.*8001" 2>/dev/null || true
        brew services stop nginx 2>/dev/null || true
        
        print_success "Tüm servisler durduruldu!"
        ;;
        
    *)
        print_error "Geçersiz seçim: $mode. ./start.sh tekrar çalıştır."
        exit 1
        ;;
esac

# Final URLs
if [ "$mode" = "1" ]; then
    echo ""
    echo "🌐 http://laravel.test"
    echo "👨‍💼 http://laravel.test/admin"
    echo "🗄️ http://localhost:8080 (PHPMyAdmin)"
    echo ""
    echo "Login: nurullah@nurullah.net / test"
fi