#!/bin/bash

# ============================================================================
# 🚀 LARAVEL MODÜL KOPYALAMA SİSTEMİ
# ============================================================================
# Kullanım: ./module.sh
# ============================================================================

set -e

# Renkler
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
WHITE='\033[1;37m'
NC='\033[0m' # No Color

# Ana header
print_header() {
    clear
    echo -e "${PURPLE}╔══════════════════════════════════════════════════════════════╗${NC}"
    echo -e "${PURPLE}║                  🚀 LARAVEL MODÜL SİSTEMİ                   ║${NC}"
    echo -e "${PURPLE}║                     Hızlı Modül Oluşturma                    ║${NC}"
    echo -e "${PURPLE}╚══════════════════════════════════════════════════════════════╝${NC}"
    echo ""
}

# Seçenek yazdırma
print_option() {
    echo -e "${CYAN}  [$1]${NC} $2"
}

# Başarı mesajı
print_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

# Hata mesajı
print_error() {
    echo -e "${RED}❌ $1${NC}"
}

# Uyarı mesajı
print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

# Bilgi mesajı
print_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
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


# Modül klonlama işlemi
clone_module() {
    local source_module="$1"
    local target_module="$2"

    echo ""
    print_info "Modül klonlanıyor: $source_module → $target_module"

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

    echo ""
    print_info "String dönüşümler:"
    echo "  📝 Modül: $source_module → $target_module"
    echo "  📝 Snake: $source_snake → $target_snake"
    echo "  📝 Kebab: $source_kebab → $target_kebab"
    echo "  📝 Çoğul: $source_plural → $target_plural"
    echo ""

    # Hedef varsa sil
    if [[ -d "Modules/$target_module" ]]; then
        rm -rf "Modules/$target_module"
    fi

    # Modülü kopyala
    print_info "Dosyalar kopyalanıyor..."
    cp -r "Modules/$source_module" "Modules/$target_module"
    print_success "Modül kopyalandı"

    # Dosya ve klasör isimlerini değiştir
    print_info "Dosya ve klasör isimleri değiştiriliyor..."

    # Klasör isimlerini değiştir (en derin seviyeden başla)
    find "Modules/$target_module" -depth -type d -name "*$source_module*" | while read dir; do
        new_dir=$(echo "$dir" | sed "s/$source_module/$target_module/g")
        if [ "$dir" != "$new_dir" ]; then
            mv "$dir" "$new_dir"
        fi
    done

    # Dosya isimlerini değiştir - Büyük harf
    find "Modules/$target_module" -type f -name "*$source_module*" | while read file; do
        new_file=$(echo "$file" | sed "s/$source_module/$target_module/g")
        if [ "$file" != "$new_file" ]; then
            mv "$file" "$new_file"
        fi
    done

    # Dosya isimlerini değiştir - Küçük harf
    find "Modules/$target_module" -type f -name "*$source_lower*" | while read file; do
        new_file=$(echo "$file" | sed "s/$source_lower/$target_lower/g")
        if [ "$file" != "$new_file" ]; then
            mv "$file" "$new_file"
        fi
    done

    print_success "Dosya ve klasör isimleri güncellendi"

    # Dosya içeriklerini değiştir
    print_info "Dosya içerikleri güncelleniyor..."

    find "Modules/$target_module" -type f \( -name "*.php" -o -name "*.blade.php" -o -name "*.json" -o -name "*.js" -o -name "*.vue" -o -name "*.xml" -o -name "*.sh" -o -name "*.md" \) | while read file; do
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
        rm -f "$file.tmp"
    done

    print_success "Dosya içerikleri güncellendi"

    # Laravel modül komutları atlanıyor (zaten klonladık)
    print_info "Laravel modül komutları atlandı..."

    # Composer autoload'u yenile
    print_info "Composer autoload yenileniyor..."
    composer dump-autoload > /dev/null 2>&1
    print_success "Composer autoload yenilendi"

    echo ""
    print_success "🎉 Modül başarıyla oluşturuldu!"
    echo ""
    echo -e "${CYAN}📁 Yeni Modül Konumu:${NC} Modules/$target_module"
    echo ""
    echo -e "${WHITE}📋 SONRAKI ADIMLAR:${NC}"
    echo -e "${CYAN}  1️⃣  Migration dosyalarını kontrol edin${NC}"
    echo -e "${CYAN}  2️⃣  Route'ları kontrol edin${NC}"
    echo -e "${CYAN}  3️⃣  Seeder'ları güncelleyin${NC}"
    echo -e "${CYAN}  4️⃣  Lang dosyalarını düzenleyin${NC}"
    echo -e "${CYAN}  5️⃣  Test edin: ${WHITE}php artisan migrate:fresh --seed${NC}"
    echo ""
    print_warning "Lang dosyaları ve özel konfigürasyonlar manuel kontrol gerektirebilir!"
}

# Ana menü
main_menu() {
    while true; do
        print_header
        echo -e "${WHITE}📋 MODÜL KOPYALAMA${NC}"
        echo ""

        print_option "1" "Portfolio Modülünü Kopyala (Kategori Sistemi)"
        print_option "2" "Announcement Modülünü Kopyala (Medya Sistemi)"
        print_option "3" "Mevcut Modülleri Listele"
        print_option "0" "Çıkış"

        echo ""
        echo -ne "${YELLOW}Seçiminiz (0-3): ${NC}"
        read choice

        case $choice in
            1)
                echo ""
                print_info "Portfolio modülü seçildi - Kategori sistemi ile"

                # Modül adını al
                while true; do
                    echo ""
                    echo -ne "${YELLOW}Yeni modül adını girin (örn: Product, News, Gallery): ${NC}"
                    read target_name

                    if [[ -z "$target_name" ]]; then
                        print_error "Modül adı boş olamaz!"
                        continue
                    fi

                    # İlk harf büyük yap, diğerleri olduğu gibi bırak
                    first_char=$(echo "${target_name:0:1}" | tr '[:lower:]' '[:upper:]')
                    rest_chars="${target_name:1}"
                    target_name="${first_char}${rest_chars}"

                    if [[ -d "Modules/$target_name" ]]; then
                        print_warning "Bu modül zaten mevcut: Modules/$target_name"
                        echo -ne "${YELLOW}Üzerine yazmak istiyor musunuz? (y/N): ${NC}"
                        read overwrite
                        if [[ $overwrite =~ ^[Yy]$ ]]; then
                            break
                        else
                            continue
                        fi
                    fi
                    break
                done

                echo ""
                clone_module "Portfolio" "$target_name"
                echo ""
                echo -ne "${YELLOW}Ana menüye dönmek için Enter'a basın...${NC}"
                read
                ;;
            2)
                echo ""
                print_info "Announcement modülü seçildi - Medya sistemi ile"

                # Modül adını al
                while true; do
                    echo ""
                    echo -ne "${YELLOW}Yeni modül adını girin (örn: Product, News, Gallery): ${NC}"
                    read target_name

                    if [[ -z "$target_name" ]]; then
                        print_error "Modül adı boş olamaz!"
                        continue
                    fi

                    # İlk harf büyük yap, diğerleri olduğu gibi bırak
                    first_char=$(echo "${target_name:0:1}" | tr '[:lower:]' '[:upper:]')
                    rest_chars="${target_name:1}"
                    target_name="${first_char}${rest_chars}"

                    if [[ -d "Modules/$target_name" ]]; then
                        print_warning "Bu modül zaten mevcut: Modules/$target_name"
                        echo -ne "${YELLOW}Üzerine yazmak istiyor musunuz? (y/N): ${NC}"
                        read overwrite
                        if [[ $overwrite =~ ^[Yy]$ ]]; then
                            break
                        else
                            continue
                        fi
                    fi
                    break
                done

                echo ""
                clone_module "Announcement" "$target_name"
                echo ""
                echo -ne "${YELLOW}Ana menüye dönmek için Enter'a basın...${NC}"
                read
                ;;
            3)
                clear
                print_header
                echo -e "${WHITE}📊 MEVCUT MODÜLLER${NC}"
                echo ""

                if [[ -d "Modules" ]]; then
                    for module in Modules/*/; do
                        if [[ -d "$module" ]]; then
                            module_name=$(basename "$module")
                            if [[ -f "$module/module.json" ]]; then
                                description=$(grep '"description"' "$module/module.json" 2>/dev/null | sed 's/.*: *"\([^"]*\)".*/\1/' || echo "Açıklama yok")
                                echo -e "${CYAN}  📦 $module_name${NC} - $description"
                            else
                                echo -e "${CYAN}  📦 $module_name${NC} - Modül bilgisi yok"
                            fi
                        fi
                    done
                else
                    print_warning "Modules klasörü bulunamadı!"
                fi

                echo ""
                echo -ne "${YELLOW}Ana menüye dönmek için Enter'a basın...${NC}"
                read
                ;;
            0)
                clear
                print_success "Görüşmek üzere! 👋"
                exit 0
                ;;
            *)
                print_error "Geçersiz seçim! Lütfen 0-3 arası bir sayı girin."
                sleep 2
                ;;
        esac
    done
}

# Başlangıç kontrolleri
startup_checks() {
    # Laravel root kontrol
    if [[ ! -f "artisan" ]]; then
        print_error "Bu script Laravel root klasöründe çalıştırılmalıdır!"
        exit 1
    fi

    # Modules klasörü kontrol
    if [[ ! -d "Modules" ]]; then
        print_error "Modules klasörü bulunamadı!"
        exit 1
    fi

    # Master pattern'lerin varlığını kontrol
    missing_patterns=()
    for pattern in "Announcement" "Portfolio"; do
        if [[ ! -d "Modules/$pattern" ]]; then
            missing_patterns+=("$pattern")
        fi
    done

    if [[ ${#missing_patterns[@]} -gt 0 ]]; then
        print_warning "Eksik master modüller: ${missing_patterns[*]}"
        echo -ne "${YELLOW}Yine de devam etmek istiyor musunuz? (y/N): ${NC}"
        read continue_anyway
        if [[ ! $continue_anyway =~ ^[Yy]$ ]]; then
            exit 1
        fi
    fi
}

# Ana script başlangıcı
main() {
    startup_checks
    main_menu
}

# Script'i çalıştır
main "$@"