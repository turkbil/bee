#!/bin/bash

# Page Modül Test Çalıştırma Script'i

echo "🧪 Page Modül Test Suite Başlatılıyor..."
echo ""

# Renkler
GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Test tipi seçimi
case "$1" in
    "unit")
        echo -e "${BLUE}📦 Unit Testler Çalıştırılıyor...${NC}"
        vendor/bin/phpunit Modules/Page/tests/Unit --testdox
        ;;
    "feature")
        echo -e "${BLUE}🎯 Feature Testler Çalıştırılıyor...${NC}"
        vendor/bin/phpunit Modules/Page/tests/Feature --testdox
        ;;
    "coverage")
        echo -e "${BLUE}📊 Coverage Raporu Oluşturuluyor...${NC}"
        vendor/bin/phpunit Modules/Page/tests --coverage-html Modules/Page/tests/coverage --testdox
        echo -e "${GREEN}✅ Coverage raporu: Modules/Page/tests/coverage/index.html${NC}"
        ;;
    "fast")
        echo -e "${BLUE}⚡ Hızlı Test (Parallel)...${NC}"
        php artisan test --parallel --filter=Page
        ;;
    *)
        echo -e "${BLUE}🚀 Tüm Testler Çalıştırılıyor...${NC}"
        vendor/bin/phpunit Modules/Page/tests --testdox
        ;;
esac

echo ""
echo -e "${GREEN}✨ Test Suite Tamamlandı!${NC}"
