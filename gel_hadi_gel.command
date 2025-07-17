#!/bin/bash

# Git otomatik commit ve push script'i - MacOS için
# Çift tıklamayla çalıştırılabilir (.command uzantısı)

# Renk tanımları
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Script'in bulunduğu dizine geç
cd "$(dirname "$0")"

echo -e "${GREEN}🚀 Git otomatik commit ve push başlatılıyor...${NC}"
echo ""

# Git durumunu kontrol et
if ! git rev-parse --git-dir > /dev/null 2>&1; then
    echo -e "${RED}❌ Bu dizin bir git repository değil!${NC}"
    echo -e "${BLUE}📍 5 saniye sonra kapanacak...${NC}"
    sleep 5
    exit 1
fi

# Git status göster
echo -e "${YELLOW}📊 Git durumu:${NC}"
git status --short
echo ""

# Değişiklikleri ekle
echo -e "${YELLOW}📦 Değişiklikler ekleniyor...${NC}"
git add .

# Değişiklik var mı kontrol et
if git diff --cached --quiet; then
    echo -e "${YELLOW}ℹ️  Commit edilecek değişiklik bulunamadı${NC}"
    echo -e "${BLUE}📍 5 saniye sonra kapanacak...${NC}"
    sleep 5
    exit 0
fi

# Otomatik commit mesajı oluştur
COMMIT_MSG="Otomatik temiz yükleme - $(date '+%d.%m.%Y - %H:%M')"

echo -e "${YELLOW}💬 Commit mesajı: ${COMMIT_MSG}${NC}"
echo ""

# Commit yap
git commit -m "$COMMIT_MSG"

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Commit başarılı!${NC}"
else
    echo -e "${RED}❌ Commit başarısız!${NC}"
    echo -e "${BLUE}📍 5 saniye sonra kapanacak...${NC}"
    sleep 5
    exit 1
fi

# Push yap
echo -e "${YELLOW}📤 Push yapılıyor...${NC}"
git push

if [ $? -eq 0 ]; then
    echo -e "${GREEN}🎉 Push başarılı! Değişiklikler remote'a gönderildi.${NC}"
else
    echo -e "${RED}❌ Push başarısız!${NC}"
    echo -e "${BLUE}📍 5 saniye sonra kapanacak...${NC}"
    sleep 5
    exit 1
fi

echo ""
echo -e "${GREEN}✨ İşlem tamamlandı!${NC}"
echo -e "${BLUE}📍 3 saniye sonra kapanacak...${NC}"
sleep 3

# Terminal'i zorla kapat (onay sormadan)
osascript -e 'tell application "Terminal" to close front window' 2>/dev/null &
sleep 1
exit 0