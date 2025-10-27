#!/bin/bash

# Working directory'yi belirle
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$SCRIPT_DIR/../.."

echo ""
echo "╔════════════════════════════════════════════════════════════════╗"
echo "║          GTM SİSTEM TAM KONTROLÜ - OTOMATIK TEST              ║"
echo "╚════════════════════════════════════════════════════════════════╝"
echo ""

ERRORS=0
WARNINGS=0

# 1. Database Kontrolü
echo "1️⃣  DATABASE KONTROLÜ"
echo "────────────────────────────────────────────────────────────────"
php -r "
require '$PROJECT_ROOT/vendor/autoload.php';
\$app = require_once '$PROJECT_ROOT/bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

tenancy()->initialize(2);
\$gtm = setting('seo_google_tag_manager_id', 'YOK');
echo '   Setting Value: ' . \$gtm . PHP_EOL;

if (\$gtm === 'YOK') {
    echo '   ❌ HATA: GTM setting bulunamadı!' . PHP_EOL;
    exit(1);
} else {
    echo '   ✅ Başarılı' . PHP_EOL;
}
"

if [ $? -ne 0 ]; then
    ERRORS=$((ERRORS + 1))
fi

echo ""

# 2. Frontend Kontrolü
echo "2️⃣  FRONTEND KONTROLÜ (https://ixtif.com)"
echo "────────────────────────────────────────────────────────────────"
COUNT=$(curl -s -k https://ixtif.com | grep -c "GTM-P8HKHCG9" 2>/dev/null || echo "0")
echo "   GTM Bulundu: $COUNT adet (beklenen: 2)"

if [ "$COUNT" -eq "2" ]; then
    echo "   ✅ Başarılı (Head + Body GTM kodları var)"
elif [ "$COUNT" -eq "0" ]; then
    echo "   ❌ HATA: GTM kodu hiç bulunamadı!"
    ERRORS=$((ERRORS + 1))
else
    echo "   ⚠️  UYARI: Sadece $COUNT adet bulundu (2 olmalı)"
    WARNINGS=$((WARNINGS + 1))
fi

echo ""

# 3. Static HTML Kontrolü
echo "3️⃣  STATIC HTML KONTROLÜ (design-hakkimizda-10.html)"
echo "────────────────────────────────────────────────────────────────"
COUNT=$(curl -s -k https://ixtif.com/design/hakkimizda-alternatifler/design-hakkimizda-10.html | grep -c "GTM-P8HKHCG9" 2>/dev/null || echo "0")
echo "   GTM Bulundu: $COUNT adet (beklenen: 2)"

if [ "$COUNT" -eq "2" ]; then
    echo "   ✅ Başarılı (Head + Body GTM kodları var)"
elif [ "$COUNT" -eq "0" ]; then
    echo "   ❌ HATA: GTM kodu hiç bulunamadı!"
    ERRORS=$((ERRORS + 1))
else
    echo "   ⚠️  UYARI: Sadece $COUNT adet bulundu (2 olmalı)"
    WARNINGS=$((WARNINGS + 1))
fi

echo ""

# 4. Admin Layout Kontrolü
echo "4️⃣  ADMIN LAYOUT KONTROLÜ (Blade Render Test)"
echo "────────────────────────────────────────────────────────────────"
php -r "
require '$PROJECT_ROOT/vendor/autoload.php';
\$app = require_once '$PROJECT_ROOT/bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

tenancy()->initialize(2);
\$gtm = setting('seo_google_tag_manager_id');
echo '   GTM ID: ' . (\$gtm ?: 'YOK') . PHP_EOL;
echo '   Blade @if Kontrolü: ' . (\$gtm ? 'GEÇER (render edilecek)' : 'BAŞARISIZ (render edilmeyecek)') . PHP_EOL;

if (\$gtm) {
    echo '   ✅ Başarılı (Admin layout GTM render edecek)' . PHP_EOL;
    exit(0);
} else {
    echo '   ❌ HATA: GTM setting yok, admin layout boş!' . PHP_EOL;
    exit(1);
}
"

if [ $? -ne 0 ]; then
    ERRORS=$((ERRORS + 1))
fi

echo ""

# 5. Local File Kontrolü
echo "5️⃣  LOCAL FILE KONTROLÜ (Static HTML Dosyaları)"
echo "────────────────────────────────────────────────────────────────"
TOTAL_FILES=0
SUCCESS_FILES=0

for file in "$PROJECT_ROOT"/public/design/hakkimizda-alternatifler/*.html; do
    if [ -f "$file" ]; then
        TOTAL_FILES=$((TOTAL_FILES + 1))
        COUNT=$(grep -c "GTM-P8HKHCG9" "$file" 2>/dev/null || echo "0")

        if [ "$COUNT" -eq "2" ]; then
            SUCCESS_FILES=$((SUCCESS_FILES + 1))
        fi
    fi
done

echo "   Toplam Dosya: $TOTAL_FILES"
echo "   GTM Var: $SUCCESS_FILES"

if [ "$TOTAL_FILES" -eq "$SUCCESS_FILES" ] && [ "$TOTAL_FILES" -gt "0" ]; then
    echo "   ✅ Başarılı (Tüm dosyalarda GTM var)"
elif [ "$TOTAL_FILES" -eq "0" ]; then
    echo "   ⚠️  UYARI: Dosya bulunamadı (klasör kontrol et)"
    WARNINGS=$((WARNINGS + 1))
else
    echo "   ❌ HATA: $((TOTAL_FILES - SUCCESS_FILES)) dosyada GTM eksik!"
    ERRORS=$((ERRORS + 1))
fi

echo ""

# 6. Layout Dosya Kontrolü
echo "6️⃣  LAYOUT DOSYALARI KONTROLÜ"
echo "────────────────────────────────────────────────────────────────"

# Admin Layout
ADMIN_LAYOUT="$PROJECT_ROOT/resources/views/admin/layout.blade.php"
if [ -f "$ADMIN_LAYOUT" ]; then
    if grep -q "setting('seo_google_tag_manager_id')" "$ADMIN_LAYOUT"; then
        echo "   ✅ Admin Layout: Dinamik (setting() kullanıyor)"
    else
        echo "   ❌ Admin Layout: Hardcoded veya GTM yok!"
        ERRORS=$((ERRORS + 1))
    fi
else
    echo "   ❌ Admin Layout: Dosya bulunamadı!"
    ERRORS=$((ERRORS + 1))
fi

# Frontend Layout
FRONTEND_LAYOUT="$PROJECT_ROOT/resources/views/themes/ixtif/layouts/header.blade.php"
if [ -f "$FRONTEND_LAYOUT" ]; then
    if grep -q "setting('seo_google_tag_manager_id')" "$FRONTEND_LAYOUT"; then
        echo "   ✅ Frontend Layout: Dinamik (setting() kullanıyor)"
    else
        echo "   ⚠️  Frontend Layout: Hardcoded olabilir"
        WARNINGS=$((WARNINGS + 1))
    fi
else
    echo "   ❌ Frontend Layout: Dosya bulunamadı!"
    ERRORS=$((ERRORS + 1))
fi

echo ""

# Sonuç
echo "╔════════════════════════════════════════════════════════════════╗"
echo "║                        SONUÇ RAPORU                            ║"
echo "╚════════════════════════════════════════════════════════════════╝"
echo ""
echo "   ❌ Kritik Hatalar: $ERRORS"
echo "   ⚠️  Uyarılar: $WARNINGS"
echo ""

if [ "$ERRORS" -eq "0" ] && [ "$WARNINGS" -eq "0" ]; then
    echo "   🎉 MÜKEMMEl! Tüm kontroller başarılı!"
    echo ""
    exit 0
elif [ "$ERRORS" -eq "0" ]; then
    echo "   ✅ BAŞARILI! ($WARNINGS uyarı var ama sistem çalışıyor)"
    echo ""
    exit 0
else
    echo "   💥 SORUN VAR! $ERRORS kritik hata düzeltilmeli!"
    echo ""
    echo "🔧 Düzeltme Adımları:"
    echo "   1. Cache temizle: php artisan view:clear && php artisan cache:clear"
    echo "   2. Setting kontrol: php artisan tinker → setting('seo_google_tag_manager_id')"
    echo "   3. Static HTML güncelle: php readme/gtm-setup/add-gtm-to-static-html.php --force"
    echo ""
    exit 1
fi
