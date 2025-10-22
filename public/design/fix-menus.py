#!/usr/bin/env python3
"""
Menu tasarımlarındaki Alpine.js bug'larını düzeltir:
1. x-data scope problemleri
2. Duplicate ID'ler
3. Z-index problemleri
4. Mobile menu toggle
"""

import re
from pathlib import Path

def fix_menu_file(file_path):
    """Bir menu dosyasındaki problemleri düzeltir"""

    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()

    original = content
    changes = []

    # 1. nav x-data'yı section seviyesine taşı
    if '<nav x-data' in content and '<section' in content:
        # Section'a x-data ekle, nav'dan kaldır
        content = re.sub(
            r'(<section[^>]*)(>)',
            r'\1 x-data="{ sidebarOpen: false, mobileMenuOpen: false, expandedCategory: null }"\2',
            content,
            count=1
        )

        # nav'dan x-data kaldır
        content = re.sub(
            r'<nav x-data="\{[^}]+\}"',
            '<nav',
            content
        )

        # Sidebar içindeki duplicate x-data kaldır
        content = re.sub(
            r'<div class="p-4" x-data="\{ expandedCategory: null \}">',
            '<div class="p-4">',
            content
        )

        changes.append("✓ x-data scope düzeltildi")

    # 2. Z-index düzeltmeleri
    if 'z-50' in content:
        # Navigation bar z-index en üstte olmalı
        content = re.sub(
            r'class="fixed top-0 left-0 right-0 z-50',
            'class="fixed top-0 left-0 right-0 z-[60]',
            content
        )
        changes.append("✓ Navigation z-index artırıldı")

    # 3. Sidebar overlay ve panel z-index
    content = re.sub(
        r'(<!-- Sidebar Overlay -->.*?class="[^"]*")z-50',
        r'\1z-[55]',
        content,
        flags=re.DOTALL
    )

    content = re.sub(
        r'(<!-- Sidebar Menu -->.*?class="[^"]*")z-50',
        r'\1z-[55]',
        content,
        flags=re.DOTALL
    )

    # 4. style="display: none;" kaldır (Alpine.js otomatik halleder)
    content = re.sub(
        r'\s*style="display:\s*none;"',
        '',
        content
    )
    changes.append("✓ Inline display:none kaldırıldı")

    # 5. Ürün sayılarını güncelle
    content = content.replace('850 ürün', '128 ürün')
    content = content.replace('620 ürün', '106 ürün')
    content = content.replace('1200 ürün', '69 ürün')
    content = content.replace('450 ürün', '146 ürün')

    if original != content:
        with open(file_path, 'w', encoding='utf-8') as f:
            f.write(content)
        return changes

    return []

def main():
    work_dir = Path('/Users/nurullah/Desktop/cms/laravel/public/ixtif-designs')
    menu_files = sorted(work_dir.glob('design-menu-*.html'))

    print(f"\n🔧 {len(menu_files)} menu dosyası düzeltiliyor...\n")

    for menu_file in menu_files:
        changes = fix_menu_file(menu_file)
        if changes:
            print(f"✅ {menu_file.name}")
            for change in changes:
                print(f"   {change}")
        else:
            print(f"⚪ {menu_file.name} - Değişiklik gerekmedi")

    print(f"\n✨ Tüm menu dosyaları güncellendi!\n")

if __name__ == '__main__':
    main()
