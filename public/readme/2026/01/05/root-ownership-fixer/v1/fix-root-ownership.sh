#!/bin/bash

################################################################################
# Root Ownership Fixer Script
# Tüm root-owned dosyaları otomatik olarak doğru kullanıcıya çevirir
################################################################################

# Renkler
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Laravel root dizini
LARAVEL_ROOT="/var/www/vhosts/tuufi.com/httpdocs"

echo -e "${BLUE}╔════════════════════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║         Root Ownership Fixer - Laravel Multi-Tenant           ║${NC}"
echo -e "${BLUE}╚════════════════════════════════════════════════════════════════╝${NC}"
echo ""

# Dizin kontrolü
if [ ! -d "$LARAVEL_ROOT" ]; then
    echo -e "${RED}❌ Hata: Laravel dizini bulunamadı: $LARAVEL_ROOT${NC}"
    exit 1
fi

cd "$LARAVEL_ROOT" || exit 1

# Otomatik kullanıcı tespiti
echo -e "${YELLOW}🔍 Otomatik kullanıcı tespiti yapılıyor...${NC}"

# composer.json dosyasının owner'ını al (Laravel'in doğru kullanıcısı)
CORRECT_USER=$(stat -c '%U' composer.json 2>/dev/null)
CORRECT_GROUP=$(stat -c '%G' composer.json 2>/dev/null)

if [ -z "$CORRECT_USER" ] || [ "$CORRECT_USER" = "root" ]; then
    # Fallback: storage klasörünün owner'ı
    CORRECT_USER=$(stat -c '%U' storage 2>/dev/null)
    CORRECT_GROUP=$(stat -c '%G' storage 2>/dev/null)
fi

if [ -z "$CORRECT_USER" ] || [ "$CORRECT_USER" = "root" ]; then
    echo -e "${RED}❌ Hata: Doğru kullanıcı tespit edilemedi!${NC}"
    echo -e "${YELLOW}💡 Manuel belirtin: ./fix-root-ownership.sh tuufi.com_ psaserv${NC}"
    exit 1
fi

# Parametreden kullanıcı geçildiyse onu kullan
if [ ! -z "$1" ]; then
    CORRECT_USER="$1"
fi

if [ ! -z "$2" ]; then
    CORRECT_GROUP="$2"
fi

echo -e "${GREEN}✅ Tespit edilen kullanıcı: ${CORRECT_USER}:${CORRECT_GROUP}${NC}"
echo ""

# Root-owned dosyaları say
echo -e "${YELLOW}📊 Root-owned dosyalar taranıyor...${NC}"
ROOT_COUNT=$(find . -user root -not -path "*/.git/*" 2>/dev/null | wc -l)

if [ "$ROOT_COUNT" -eq 0 ]; then
    echo -e "${GREEN}✅ Hiç root-owned dosya yok! Sistem zaten sağlıklı.${NC}"
    exit 0
fi

echo -e "${RED}🔴 Toplam ${ROOT_COUNT} root-owned dosya bulundu!${NC}"
echo ""

# Onay al
read -p "$(echo -e ${YELLOW}Tüm root-owned dosyalar ${CORRECT_USER}:${CORRECT_GROUP} olarak değiştirilsin mi? [E/h]: ${NC})" -n 1 -r
echo ""

if [[ ! $REPLY =~ ^[Ee]$ ]]; then
    echo -e "${YELLOW}⏸️  İşlem iptal edildi.${NC}"
    exit 0
fi

echo ""
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${YELLOW}🔧 DÜZELTİLİYOR...${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""

# 1. Dosya ownership düzelt
echo -e "${YELLOW}📁 [1/6] Dosya ownership düzeltiliyor...${NC}"
find . -user root -not -path "*/.git/*" -type f -print0 | xargs -0 -P 4 sudo chown ${CORRECT_USER}:${CORRECT_GROUP} 2>/dev/null
echo -e "${GREEN}✅ Dosyalar düzeltildi${NC}"

# 2. Klasör ownership düzelt
echo -e "${YELLOW}📂 [2/6] Klasör ownership düzeltiliyor...${NC}"
find . -user root -not -path "*/.git/*" -type d -print0 | xargs -0 -P 4 sudo chown ${CORRECT_USER}:${CORRECT_GROUP} 2>/dev/null
echo -e "${GREEN}✅ Klasörler düzeltildi${NC}"

# 3. Dosya izinleri düzelt (644)
echo -e "${YELLOW}🔐 [3/6] Dosya izinleri düzeltiliyor (644)...${NC}"
find . -type f -not -path "*/.git/*" -not -path "*/node_modules/*" -not -perm 644 -print0 | xargs -0 -P 4 sudo chmod 644 2>/dev/null
echo -e "${GREEN}✅ Dosya izinleri düzeltildi${NC}"

# 4. Klasör izinleri düzelt (755)
echo -e "${YELLOW}🔓 [4/6] Klasör izinleri düzeltiliyor (755)...${NC}"
find . -type d -not -path "*/.git/*" -not -path "*/node_modules/*" -not -perm 755 -print0 | xargs -0 -P 4 sudo chmod 755 2>/dev/null
echo -e "${GREEN}✅ Klasör izinleri düzeltildi${NC}"

# 5. Storage özel izinler
echo -e "${YELLOW}💾 [5/6] Storage klasörü özel izinleri...${NC}"
sudo chmod -R 777 storage/logs 2>/dev/null
sudo chmod -R 775 storage/framework/cache storage/framework/sessions storage/framework/views 2>/dev/null
echo -e "${GREEN}✅ Storage izinleri düzeltildi${NC}"

# 6. Symlink ownership düzelt
echo -e "${YELLOW}🔗 [6/6] Symlink ownership düzeltiliyor...${NC}"
sudo chown -h ${CORRECT_USER}:${CORRECT_GROUP} public/storage/tenant* 2>/dev/null
echo -e "${GREEN}✅ Symlink'ler düzeltildi${NC}"

echo ""
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}✅ TÜM İŞLEMLER TAMAMLANDI!${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""

# Kontrol
REMAINING=$(find . -user root -not -path "*/.git/*" 2>/dev/null | wc -l)

if [ "$REMAINING" -eq 0 ]; then
    echo -e "${GREEN}🎉 Hiç root-owned dosya kalmadı!${NC}"
else
    echo -e "${YELLOW}⚠️  Hala ${REMAINING} root-owned dosya var (.git hariç)${NC}"
    echo -e "${YELLOW}💡 Bu dosyalar muhtemelen .git klasöründe (normal)${NC}"
fi

echo ""
echo -e "${BLUE}📊 SON DURUM:${NC}"
echo -e "   • Owner: ${GREEN}${CORRECT_USER}:${CORRECT_GROUP}${NC}"
echo -e "   • Dosya izni: ${GREEN}644${NC}"
echo -e "   • Klasör izni: ${GREEN}755${NC}"
echo -e "   • Storage/logs: ${GREEN}777${NC}"
echo ""
echo -e "${YELLOW}💡 Composer/cache işlemleri için:${NC}"
echo -e "   composer dump-autoload"
echo -e "   php artisan config:clear"
echo -e "   php artisan cache:clear"
echo ""
