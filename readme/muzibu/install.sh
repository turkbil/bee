#!/bin/bash

################################################################################
# Muzibu Module - Automatic Installation Script
#
# Bu script AlmaLinux 8+ / CentOS 8+ / RHEL 8+ sistemlerde
# Muzibu modülünü otomatik kurar.
#
# Kullanım:
#   chmod +x install.sh
#   sudo ./install.sh
#
# Yazar: Claude AI
# Tarih: 2025-11-11
################################################################################

set -e  # Hata durumunda dur

# Renkler
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Log fonksiyonları
log_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

log_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Banner
echo -e "${BLUE}"
echo "╔═══════════════════════════════════════════════════════════╗"
echo "║                                                           ║"
echo "║        MUZIBU MODULE - AUTOMATIC INSTALLER                ║"
echo "║        AlmaLinux 8+ / CentOS 8+ / RHEL 8+                 ║"
echo "║                                                           ║"
echo "╚═══════════════════════════════════════════════════════════╝"
echo -e "${NC}"

# Root kontrolü
if [[ $EUID -ne 0 ]]; then
   log_error "Bu script root olarak çalıştırılmalı (sudo ./install.sh)"
   exit 1
fi

# OS kontrolü
log_info "İşletim sistemi kontrol ediliyor..."
if [ -f /etc/os-release ]; then
    . /etc/os-release
    OS_NAME=$NAME
    OS_VERSION=$VERSION_ID
    log_success "OS: $OS_NAME $OS_VERSION"
else
    log_error "/etc/os-release bulunamadı!"
    exit 1
fi

# AlmaLinux/CentOS/RHEL kontrolü
if [[ ! "$OS_NAME" =~ (AlmaLinux|CentOS|Red\ Hat) ]]; then
    log_warning "Bu script AlmaLinux/CentOS/RHEL için optimize edilmiştir."
    log_warning "Mevcut OS: $OS_NAME"
    read -p "Devam etmek istiyor musunuz? (y/n) " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        exit 1
    fi
fi

################################################################################
# 1. EPEL REPOSITORY
################################################################################
log_info "EPEL repository kontrol ediliyor..."
if ! rpm -q epel-release &> /dev/null; then
    log_info "EPEL repository kuruluyor..."
    yum install -y epel-release
    log_success "EPEL repository kuruldu"
else
    log_success "EPEL repository zaten kurulu"
fi

################################################################################
# 2. RPM FUSION REPOSITORY (FFmpeg için)
################################################################################
log_info "RPM Fusion repository kontrol ediliyor..."

if ! rpm -q rpmfusion-free-release &> /dev/null; then
    log_info "RPM Fusion Free repository kuruluyor..."
    yum install -y https://download1.rpmfusion.org/free/el/rpmfusion-free-release-8.noarch.rpm
    log_success "RPM Fusion Free kuruldu"
else
    log_success "RPM Fusion Free zaten kurulu"
fi

if ! rpm -q rpmfusion-nonfree-release &> /dev/null; then
    log_info "RPM Fusion Nonfree repository kuruluyor..."
    yum install -y https://download1.rpmfusion.org/nonfree/el/rpmfusion-nonfree-release-8.noarch.rpm
    log_success "RPM Fusion Nonfree kuruldu"
else
    log_success "RPM Fusion Nonfree zaten kurulu"
fi

################################################################################
# 3. FFMPEG KURULUMU
################################################################################
log_info "FFmpeg kontrol ediliyor..."

if ! command -v ffmpeg &> /dev/null; then
    log_info "FFmpeg kuruluyor... (Bu birkaç dakika sürebilir)"
    yum install -y ffmpeg ffmpeg-devel
    log_success "FFmpeg kuruldu"
else
    log_success "FFmpeg zaten kurulu"
fi

# FFmpeg version kontrolü
FFMPEG_VERSION=$(ffmpeg -version 2>/dev/null | head -1)
log_success "FFmpeg Version: $FFMPEG_VERSION"

################################################################################
# 4. COMPOSER PAKETLERİ (getID3)
################################################################################
log_info "Composer paketleri kontrol ediliyor..."

# Laravel root dizini bul
LARAVEL_ROOT="/var/www/vhosts/tuufi.com/httpdocs"

if [ ! -d "$LARAVEL_ROOT" ]; then
    log_error "Laravel root dizini bulunamadı: $LARAVEL_ROOT"
    exit 1
fi

cd "$LARAVEL_ROOT"

# getID3 kontrolü
if ! grep -q "james-heinrich/getid3" composer.json; then
    log_info "getID3 paketi kuruluyor..."
    composer require james-heinrich/getid3 --no-interaction
    log_success "getID3 kuruldu"
else
    log_success "getID3 zaten kurulu"
fi

################################################################################
# 5. NPM PAKETLERİ (HLS.js)
################################################################################
log_info "NPM paketleri kontrol ediliyor..."

if ! grep -q '"hls.js"' package.json; then
    log_info "HLS.js paketi kuruluyor..."
    npm install hls.js --save
    log_success "HLS.js kuruldu"
else
    log_success "HLS.js zaten kurulu"
fi

################################################################################
# 6. STORAGE KLASÖRLERI VE İZİNLER
################################################################################
log_info "Storage klasörleri kontrol ediliyor..."

STORAGE_DIR="$LARAVEL_ROOT/storage/app/public/muzibu/songs/hls"

if [ ! -d "$STORAGE_DIR" ]; then
    log_info "Storage klasörleri oluşturuluyor..."
    mkdir -p "$STORAGE_DIR"
    log_success "Storage klasörleri oluşturuldu"
fi

# İzinleri ayarla
log_info "Storage izinleri ayarlanıyor..."
chown -R tuufi.com_:psaserv storage/app/public/muzibu/
chmod -R 755 storage/app/public/muzibu/
log_success "İzinler ayarlandı"

################################################################################
# 7. AUTOLOAD REFRESH
################################################################################
log_info "Composer autoload güncelleniyor..."
composer dump-autoload
log_success "Autoload güncellendi"

################################################################################
# 8. CACHE CLEAR
################################################################################
log_info "Laravel cache temizleniyor..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
log_success "Cache temizlendi"

################################################################################
# 9. HORIZON KONTROLÜ
################################################################################
log_info "Queue worker (Horizon) kontrol ediliyor..."

if pgrep -f "artisan horizon" > /dev/null; then
    log_success "Horizon aktif çalışıyor"
else
    log_warning "Horizon çalışmıyor! Manuel başlatın:"
    log_warning "  php artisan horizon &"
fi

################################################################################
# 10. FINAL KONTROLLER
################################################################################
echo ""
log_info "Final kontroller yapılıyor..."
echo ""

# FFmpeg test
if command -v ffmpeg &> /dev/null; then
    echo -e "${GREEN}✓${NC} FFmpeg kurulu"
else
    echo -e "${RED}✗${NC} FFmpeg kurulu DEĞİL"
fi

# getID3 test
if grep -q "james-heinrich/getid3" composer.json; then
    echo -e "${GREEN}✓${NC} getID3 kurulu"
else
    echo -e "${RED}✗${NC} getID3 kurulu DEĞİL"
fi

# HLS.js test
if grep -q '"hls.js"' package.json; then
    echo -e "${GREEN}✓${NC} HLS.js kurulu"
else
    echo -e "${RED}✗${NC} HLS.js kurulu DEĞİL"
fi

# Storage klasörü test
if [ -d "$STORAGE_DIR" ]; then
    echo -e "${GREEN}✓${NC} Storage klasörü hazır"
else
    echo -e "${RED}✗${NC} Storage klasörü hazır DEĞİL"
fi

# Horizon test
if pgrep -f "artisan horizon" > /dev/null; then
    echo -e "${GREEN}✓${NC} Horizon çalışıyor"
else
    echo -e "${YELLOW}⚠${NC} Horizon çalışmıyor (manuel başlatın)"
fi

################################################################################
# KURULUM TAMAMLANDI
################################################################################
echo ""
echo -e "${GREEN}"
echo "╔═══════════════════════════════════════════════════════════╗"
echo "║                                                           ║"
echo "║        ✓ KURULUM TAMAMLANDI!                             ║"
echo "║                                                           ║"
echo "╚═══════════════════════════════════════════════════════════╝"
echo -e "${NC}"

echo ""
log_info "Sıradaki adımlar:"
echo ""
echo "  1. Migration'ları çalıştırın:"
echo "     php artisan tenants:migrate --path=Modules/Muzibu/database/migrations/tenant/2025_11_11_020022_add_hls_fields_to_muzibu_songs_table.php"
echo ""
echo "  2. Queue worker'ı başlatın (eğer çalışmıyorsa):"
echo "     php artisan horizon &"
echo ""
echo "  3. Test song yükleyin:"
echo "     Admin Panel → Muzibu → Songs → Upload"
echo ""
echo "  4. HLS conversion test edin:"
echo "     php artisan tinker"
echo "     >>> \$song = \\Modules\\Muzibu\\App\\Models\\Song::first();"
echo "     >>> \\Modules\\Muzibu\\App\\Jobs\\ConvertToHLSJob::dispatch(\$song);"
echo ""
echo "  5. API endpoint test edin:"
echo "     curl https://yourdomain.com/api/muzibu/songs/1/stream"
echo ""
log_success "Detaylı dokümantasyon: readme/muzibu/SETUP-GUIDE.md"
echo ""

exit 0
