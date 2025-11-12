#!/bin/bash

# Design System - Navigation Script Auto-Injector
# Bu script tüm design-*-[0-9].html dosyalarına otomatik navigasyon ekler

DESIGN_DIR="/var/www/vhosts/tuufi.com/httpdocs/public/design"
SCRIPT_TAG='<script src="navigation-auto.js"><\/script>'

cd "$DESIGN_DIR" || exit 1

echo "🚀 Design System Navigation Auto-Injector"
echo "========================================="
echo ""

# design-*-[0-9].html pattern'ine uyan dosyaları bul
FILES=$(find . -maxdepth 1 -name "design-*-[0-9]*.html" -type f | sort)

if [ -z "$FILES" ]; then
    echo "❌ Hiç design dosyası bulunamadı!"
    exit 1
fi

TOTAL=$(echo "$FILES" | wc -l)
ADDED=0
SKIPPED=0

echo "📁 Toplam $TOTAL dosya bulundu"
echo ""

for file in $FILES; do
    filename=$(basename "$file")

    # Dosyanın zaten navigation-auto.js içerip içermediğini kontrol et
    if grep -q "navigation-auto.js" "$file"; then
        echo "⏭️  $filename (zaten var, atlanıyor)"
        ((SKIPPED++))
        continue
    fi

    # Dosyanın </body> tag'inden önce script'i ekle
    if grep -q "</body>" "$file"; then
        # Backup oluştur
        cp "$file" "$file.bak"

        # Script'i </body> tag'inden önce ekle
        sed -i "s|</body>|$SCRIPT_TAG\n\n</body>|g" "$file"

        echo "✅ $filename (eklendi)"
        ((ADDED++))
    else
        echo "⚠️  $filename (</body> tag'i bulunamadı, atlanıyor)"
        ((SKIPPED++))
    fi
done

echo ""
echo "========================================="
echo "🎉 İşlem Tamamlandı!"
echo "✅ Eklendi: $ADDED dosya"
echo "⏭️  Atlandı: $SKIPPED dosya"
echo ""
echo "💡 İpucu: Backup dosyaları (.bak) silmek için:"
echo "   rm -f $DESIGN_DIR/design-*-*.html.bak"
echo ""
