#!/bin/bash

# TURKBIL BEE MODÜL TEST RUNNER
# Bu script tüm modül testlerini sistematik olarak çalıştırır

echo "=================================================="
echo "  TURKBIL BEE MODÜL TESTLERİ BAŞLATIYOR"
echo "=================================================="

# Renk kodları
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Test ortamını hazırla
echo -e "${BLUE}Test ortamı hazırlanıyor...${NC}"
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Test veritabanını hazırla
echo -e "${BLUE}Test veritabanı hazırlanıyor...${NC}"
php artisan migrate:fresh --env=testing --force
php artisan db:seed --env=testing --force

# Modül listesi
modules=("AI" "Portfolio" "UserManagement" "Page" "Announcement" "WidgetManagement" "SettingManagement" "ModuleManagement" "TenantManagement" "ThemeManagement" "Studio")

# Test sonuçları
declare -A test_results
total_tests=0
passed_tests=0
failed_tests=0

echo -e "${YELLOW}=================================================="
echo -e "  MODÜL TESTLERİ ÇALIŞTIRILIYOR"
echo -e "==================================================${NC}"

# Her modül için testleri çalıştır
for module in "${modules[@]}"; do
    echo -e "\n${BLUE}📦 $module Modülü Testleri Çalıştırılıyor...${NC}"
    echo -e "${BLUE}============================================${NC}"
    
    # Modül test dosyalarını kontrol et
    test_path="Modules/$module/tests"
    if [ -d "$test_path" ]; then
        # PHPUnit ile modül testlerini çalıştır
        result=$(php artisan test --testsuite="$module" --stop-on-failure 2>&1)
        exit_code=$?
        
        if [ $exit_code -eq 0 ]; then
            echo -e "${GREEN}✅ $module modülü testleri BAŞARILI${NC}"
            test_results[$module]="PASSED"
            
            # Test sayılarını parse et
            test_count=$(echo "$result" | grep -o '[0-9]\+ tests' | head -1 | grep -o '[0-9]\+')
            if [ ! -z "$test_count" ]; then
                total_tests=$((total_tests + test_count))
                passed_tests=$((passed_tests + test_count))
            fi
        else
            echo -e "${RED}❌ $module modülü testleri BAŞARISIZ${NC}"
            test_results[$module]="FAILED"
            failed_tests=$((failed_tests + 1))
            
            # Hata detaylarını göster
            echo -e "${RED}Hata Detayları:${NC}"
            echo "$result" | tail -20
        fi
    else
        echo -e "${YELLOW}⚠️  $module modülü için test dosyası bulunamadı${NC}"
        test_results[$module]="NO_TESTS"
    fi
    
    sleep 1
done

echo -e "\n${YELLOW}=================================================="
echo -e "  FEATURE TESTLERİ ÇALIŞTIRILIYOR"
echo -e "==================================================${NC}"

# Feature testlerini çalıştır
echo -e "${BLUE}🧪 Genel Feature Testleri Çalıştırılıyor...${NC}"
feature_result=$(php artisan test tests/Feature --stop-on-failure 2>&1)
feature_exit_code=$?

if [ $feature_exit_code -eq 0 ]; then
    echo -e "${GREEN}✅ Feature testleri BAŞARILI${NC}"
    test_results["Feature"]="PASSED"
else
    echo -e "${RED}❌ Feature testleri BAŞARISIZ${NC}"
    test_results["Feature"]="FAILED"
    echo -e "${RED}Hata Detayları:${NC}"
    echo "$feature_result" | tail -20
fi

echo -e "\n${YELLOW}=================================================="
echo -e "  UNIT TESTLERİ ÇALIŞTIRILIYOR"
echo -e "==================================================${NC}"

# Unit testlerini çalıştır
echo -e "${BLUE}🔬 Unit Testleri Çalıştırılıyor...${NC}"
unit_result=$(php artisan test tests/Unit --stop-on-failure 2>&1)
unit_exit_code=$?

if [ $unit_exit_code -eq 0 ]; then
    echo -e "${GREEN}✅ Unit testleri BAŞARILI${NC}"
    test_results["Unit"]="PASSED"
else
    echo -e "${RED}❌ Unit testleri BAŞARISIZ${NC}"
    test_results["Unit"]="FAILED"
    echo -e "${RED}Hata Detayları:${NC}"
    echo "$unit_result" | tail -20
fi

# Browser testleri (Dusk) - opsiyonel
echo -e "\n${YELLOW}=================================================="
echo -e "  BROWSER TESTLERİ (DUSK) - OPSİYONEL"
echo -e "==================================================${NC}"

read -p "Browser testlerini (Dusk) çalıştırmak istiyor musunuz? (y/n): " run_dusk

if [ "$run_dusk" = "y" ] || [ "$run_dusk" = "Y" ]; then
    echo -e "${BLUE}🌐 Browser Testleri Çalıştırılıyor...${NC}"
    dusk_result=$(php artisan dusk 2>&1)
    dusk_exit_code=$?
    
    if [ $dusk_exit_code -eq 0 ]; then
        echo -e "${GREEN}✅ Browser testleri BAŞARILI${NC}"
        test_results["Dusk"]="PASSED"
    else
        echo -e "${RED}❌ Browser testleri BAŞARISIZ${NC}"
        test_results["Dusk"]="FAILED"
        echo -e "${RED}Hata Detayları:${NC}"
        echo "$dusk_result" | tail -20
    fi
else
    echo -e "${YELLOW}⏭️  Browser testleri atlandı${NC}"
    test_results["Dusk"]="SKIPPED"
fi

# Test kapsamı raporu oluştur
echo -e "\n${YELLOW}=================================================="
echo -e "  TEST KAPSAMI RAPORU OLUŞTURULUYOR"
echo -e "==================================================${NC}"

echo -e "${BLUE}📊 Test Kapsamı Raporu Oluşturuluyor...${NC}"
coverage_result=$(php artisan test --coverage --min=70 2>&1)
coverage_exit_code=$?

if [ $coverage_exit_code -eq 0 ]; then
    echo -e "${GREEN}✅ Test kapsamı yeterli (>%70)${NC}"
else
    echo -e "${YELLOW}⚠️  Test kapsamı yetersiz (<%70)${NC}"
fi

# Sonuç raporu
echo -e "\n${YELLOW}=================================================="
echo -e "  SONUÇ RAPORU"
echo -e "==================================================${NC}"

echo -e "${BLUE}📋 Test Sonuçları:${NC}"
echo -e "---------------------------------------------------"

for key in "${!test_results[@]}"; do
    result="${test_results[$key]}"
    case $result in
        "PASSED")
            echo -e "${GREEN}✅ $key: BAŞARILI${NC}"
            ;;
        "FAILED")
            echo -e "${RED}❌ $key: BAŞARISIZ${NC}"
            ;;
        "NO_TESTS")
            echo -e "${YELLOW}⚠️  $key: TEST YOK${NC}"
            ;;
        "SKIPPED")
            echo -e "${YELLOW}⏭️  $key: ATLANDI${NC}"
            ;;
    esac
done

echo -e "\n${BLUE}📊 İstatistikler:${NC}"
echo -e "---------------------------------------------------"
echo -e "Toplam Test Edilen Modül: ${#modules[@]}"
echo -e "Başarılı Testler: $passed_tests"
echo -e "Başarısız Testler: $failed_tests"

if [ $failed_tests -eq 0 ]; then
    echo -e "\n${GREEN}🎉 TÜM TESTLER BAŞARILI! ${NC}"
    echo -e "${GREEN}Proje test edilmeye hazır durumda.${NC}"
    exit 0
else
    echo -e "\n${RED}⚠️  BAZI TESTLER BAŞARISIZ! ${NC}"
    echo -e "${RED}Lütfen başarısız testleri kontrol edin ve düzeltin.${NC}"
    exit 1
fi