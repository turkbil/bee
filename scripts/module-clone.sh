#!/bin/bash

# ============================================================================
# 🚀 Laravel Modül Klonlama Script'i
# ============================================================================
# Kullanım: ./scripts/module-clone.sh [kaynak_modül] [hedef_modül]
# Örnek: ./scripts/module-clone.sh Page Product
# ============================================================================

set -e

# Renkler
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Fonksiyonlar
print_header() {
    echo -e "${PURPLE}========================================${NC}"
    echo -e "${PURPLE}🚀 Laravel Modül Klonlama Script'i${NC}"
    echo -e "${PURPLE}========================================${NC}"
}

print_step() {
    echo -e "${CYAN}📋 $1${NC}"
}

print_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

# String manipulation fonksiyonları
to_lower() {
    echo "$1" | tr '[:upper:]' '[:lower:]'
}

to_upper() {
    echo "$1" | tr '[:lower:]' '[:upper:]'
}

to_title() {
    echo "$1" | sed 's/.*/\L&/; s/[a-z]*/\u&/g'
}

to_camel() {
    echo "$1" | sed 's/\([a-z]\)\([A-Z]\)/\1\2/g'
}

to_snake() {
    echo "$1" | sed 's/\([a-z0-9]\)\([A-Z]\)/\1_\2/g' | tr '[:upper:]' '[:lower:]'
}

to_kebab() {
    echo "$1" | sed 's/\([a-z0-9]\)\([A-Z]\)/\1-\2/g' | tr '[:upper:]' '[:lower:]'
}

to_plural() {
    local word="$1"
    local last_char="${word: -1}"
    local last_two="${word: -2}"

    # Basit çoğul kuralları
    if [[ "$last_char" == "y" ]]; then
        echo "${word%?}ies"
    elif [[ "$last_char" == "s" ]] || [[ "$last_char" == "x" ]] || [[ "$last_char" == "z" ]] || [[ "$last_two" == "ch" ]] || [[ "$last_two" == "sh" ]]; then
        echo "${word}es"
    elif [[ "$last_char" == "f" ]]; then
        echo "${word%?}ves"
    elif [[ "$last_two" == "fe" ]]; then
        echo "${word%??}ves"
    else
        echo "${word}s"
    fi
}

to_singular() {
    local word="$1"
    local last_char="${word: -1}"
    local last_three="${word: -3}"
    local last_two="${word: -2}"

    # Basit tekil kuralları
    if [[ "$last_three" == "ies" ]]; then
        echo "${word%???}y"
    elif [[ "$last_three" == "ves" ]]; then
        echo "${word%???}f"
    elif [[ "$last_two" == "es" ]]; then
        echo "${word%??}"
    elif [[ "$last_char" == "s" ]] && [[ "$last_two" != "ss" ]]; then
        echo "${word%?}"
    else
        echo "$word"
    fi
}

# Ana fonksiyon
main() {
    print_header

    # Parametreleri kontrol et
    if [ $# -ne 2 ]; then
        print_error "Kullanım: $0 [kaynak_modül] [hedef_modül]"
        echo "Örnek: $0 Page Product"
        exit 1
    fi

    local source_module="$1"
    local target_module="$2"

    # Modüller klasöründe olduğumuzu kontrol et
    if [ ! -d "Modules" ]; then
        print_error "Bu script'i Laravel root klasöründe çalıştırın!"
        exit 1
    fi

    # Kaynak modülün varlığını kontrol et
    if [ ! -d "Modules/$source_module" ]; then
        print_error "Kaynak modül bulunamadı: Modules/$source_module"
        exit 1
    fi

    # Hedef modülün zaten var olup olmadığını kontrol et
    if [ -d "Modules/$target_module" ]; then
        print_warning "Hedef modül zaten mevcut: Modules/$target_module"
        read -p "Üzerine yazmak istiyor musunuz? (y/N): " -n 1 -r
        echo
        if [[ ! $REPLY =~ ^[Yy]$ ]]; then
            print_error "İşlem iptal edildi."
            exit 1
        fi
        rm -rf "Modules/$target_module"
    fi

    print_step "Modül klonlanıyor: $source_module → $target_module"

    # String varyasyonlarını hazırla
    local source_lower=$(to_lower "$source_module")
    local source_upper=$(to_upper "$source_module")
    local source_title=$(to_title "$source_module")
    local source_snake=$(to_snake "$source_module")
    local source_kebab=$(to_kebab "$source_module")
    local source_plural=$(to_plural "$source_lower")
    local source_plural_title=$(to_title "$source_plural")

    local target_lower=$(to_lower "$target_module")
    local target_upper=$(to_upper "$target_module")
    local target_title=$(to_title "$target_module")
    local target_snake=$(to_snake "$target_module")
    local target_kebab=$(to_kebab "$target_module")
    local target_plural=$(to_plural "$target_lower")
    local target_plural_title=$(to_title "$target_plural")

    print_step "String dönüşümler hazırlandı:"
    echo "  📝 Kaynak: $source_module → Hedef: $target_module"
    echo "  📝 Snake: $source_snake → $target_snake"
    echo "  📝 Kebab: $source_kebab → $target_kebab"
    echo "  📝 Çoğul: $source_plural → $target_plural"

    # Modülü kopyala
    print_step "Dosyalar kopyalanıyor..."
    cp -r "Modules/$source_module" "Modules/$target_module"
    print_success "Modül kopyalandı"

    # Dosya ve klasör isimlerini değiştir
    print_step "Dosya ve klasör isimleri değiştiriliyor..."

    # Klasör isimlerini değiştir (en derin seviyeden başla)
    find "Modules/$target_module" -depth -type d -name "*$source_module*" | while read dir; do
        new_dir=$(echo "$dir" | sed "s/$source_module/$target_module/g")
        if [ "$dir" != "$new_dir" ]; then
            mv "$dir" "$new_dir"
            print_success "Klasör: $(basename "$dir") → $(basename "$new_dir")"
        fi
    done

    # Dosya isimlerini değiştir
    find "Modules/$target_module" -type f -name "*$source_module*" | while read file; do
        new_file=$(echo "$file" | sed "s/$source_module/$target_module/g")
        if [ "$file" != "$new_file" ]; then
            mv "$file" "$new_file"
            print_success "Dosya: $(basename "$file") → $(basename "$new_file")"
        fi
    done

    # Dosya içeriklerini değiştir
    print_step "Dosya içerikleri güncelleniyor..."

    # PHP, Blade, JSON, JS dosyalarında değişiklik yap
    find "Modules/$target_module" -type f \( -name "*.php" -o -name "*.blade.php" -o -name "*.json" -o -name "*.js" -o -name "*.vue" \) | while read file; do
        # Backup oluştur
        cp "$file" "$file.bak"

        # String replacements
        sed -i.tmp \
            -e "s/$source_module/$target_module/g" \
            -e "s/$source_lower/$target_lower/g" \
            -e "s/$source_upper/$target_upper/g" \
            -e "s/$source_title/$target_title/g" \
            -e "s/$source_snake/$target_snake/g" \
            -e "s/$source_kebab/$target_kebab/g" \
            -e "s/$source_plural/$target_plural/g" \
            -e "s/$source_plural_title/$target_plural_title/g" \
            "$file"

        # Backup ve temp dosyalarını temizle
        rm -f "$file.bak" "$file.tmp"
    done

    print_success "Dosya içerikleri güncellendi"

    # Composer autoload'u yenile
    print_step "Composer autoload yenileniyor..."
    composer dump-autoload > /dev/null 2>&1
    print_success "Composer autoload yenilendi"

    # Özet
    print_header
    print_success "🎉 Modül başarıyla klonlandı!"
    echo ""
    echo -e "${CYAN}📁 Yeni Modül:${NC} Modules/$target_module"
    echo -e "${CYAN}📋 Sonraki Adımlar:${NC}"
    echo "  1️⃣  Migration dosyalarını kontrol edin"
    echo "  2️⃣  Route'ları kontrol edin"
    echo "  3️⃣  Seeder'ları güncelleyin"
    echo "  4️⃣  Lang dosyalarını düzenleyin"
    echo "  5️⃣  Test edin: php artisan migrate:fresh --seed"
    echo ""
    print_warning "⚠️  Lang dosyaları ve özel konfigürasyonlar manuel kontrol gerektirebilir!"

    return 0
}

# Script'i çalıştır
main "$@"