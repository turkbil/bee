#!/usr/bin/env python3
"""
Orijinal design-*.html dosyalarını güzel gallery grid sayfalarına çevirir.
"""

import os
import re
from pathlib import Path

# Her kategori için tasarım sayısı
DESIGN_COUNTS = {
    'design-menu': 10,
    'design-header': 10,
    'design-hero': 10,
    'design-cta': 10,
    'design-products': 10,
    'design-categories': 10,
    'design-services': 10,
    'design-about': 10,
    'design-features': 10,
    'design-stats': 10,
    'design-testimonials': 10,
    'design-partners': 10,
    'design-gallery': 10,
    'design-pricing': 10,
    'design-promotions': 10,
    'design-contact': 10,
    'design-newsletter': 10,
    'design-blog': 10,
    'design-faq': 10,
    'design-footer': 10,
    'design-chatbot-inline': 6,
    'design-chatbot-popup': 5,
    'design-accordion': 6,
    'design-breadcrumb': 8,
    'design-promotion': 6,
    'design-search': 6,
    'design-sidebar': 6,
    'design-subheader': 8,
    'design-tabs': 6
}

# Kategori bilgileri
CATEGORY_INFO = {
    'design-menu': {'name': 'Menu Tasarımları', 'icon': 'fa-bars', 'color': 'from-blue-500 to-cyan-500', 'desc': 'Modern ve interaktif menü tasarımları'},
    'design-header': {'name': 'Header Tasarımları', 'icon': 'fa-rectangle-wide', 'color': 'from-purple-500 to-pink-500', 'desc': 'Etkileyici header tasarımları'},
    'design-hero': {'name': 'Hero Sections', 'icon': 'fa-rocket-launch', 'color': 'from-orange-500 to-red-500', 'desc': 'Göz alıcı hero section tasarımları'},
    'design-cta': {'name': 'CTA Sections', 'icon': 'fa-bullhorn', 'color': 'from-green-500 to-emerald-500', 'desc': 'Dönüşüm odaklı CTA tasarımları'},
    'design-products': {'name': 'Ürün Tasarımları', 'icon': 'fa-box-open', 'color': 'from-yellow-500 to-orange-500', 'desc': 'E-ticaret ürün kartları'},
    'design-categories': {'name': 'Kategori Tasarımları', 'icon': 'fa-grid-2', 'color': 'from-teal-500 to-cyan-500', 'desc': 'Kategori gösterim tasarımları'},
    'design-services': {'name': 'Hizmet Tasarımları', 'icon': 'fa-briefcase', 'color': 'from-indigo-500 to-purple-500', 'desc': 'Hizmet tanıtım tasarımları'},
    'design-about': {'name': 'Hakkımızda Tasarımları', 'icon': 'fa-building', 'color': 'from-pink-500 to-rose-500', 'desc': 'Kurumsal tanıtım tasarımları'},
    'design-features': {'name': 'Özellik Tasarımları', 'icon': 'fa-sparkles', 'color': 'from-violet-500 to-purple-500', 'desc': 'Özellik gösterim tasarımları'},
    'design-stats': {'name': 'İstatistik Tasarımları', 'icon': 'fa-chart-line', 'color': 'from-blue-500 to-indigo-500', 'desc': 'İstatistik ve sayı gösterimleri'},
    'design-testimonials': {'name': 'Testimonial Tasarımları', 'icon': 'fa-quote-left', 'color': 'from-cyan-500 to-blue-500', 'desc': 'Müşteri yorumu tasarımları'},
    'design-partners': {'name': 'Partner Tasarımları', 'icon': 'fa-handshake', 'color': 'from-emerald-500 to-teal-500', 'desc': 'Partner/logo gösterim tasarımları'},
    'design-gallery': {'name': 'Galeri Tasarımları', 'icon': 'fa-images', 'color': 'from-fuchsia-500 to-pink-500', 'desc': 'Görsel galeri tasarımları'},
    'design-pricing': {'name': 'Fiyatlandırma Tasarımları', 'icon': 'fa-tag', 'color': 'from-lime-500 to-green-500', 'desc': 'Fiyat tablosu tasarımları'},
    'design-promotions': {'name': 'Kampanya Tasarımları', 'icon': 'fa-badge-percent', 'color': 'from-red-500 to-orange-500', 'desc': 'Promosyon ve kampanya tasarımları'},
    'design-contact': {'name': 'İletişim Tasarımları', 'icon': 'fa-envelope', 'color': 'from-sky-500 to-blue-500', 'desc': 'İletişim formu tasarımları'},
    'design-newsletter': {'name': 'Newsletter Tasarımları', 'icon': 'fa-paper-plane', 'color': 'from-purple-500 to-violet-500', 'desc': 'E-posta abonelik tasarımları'},
    'design-blog': {'name': 'Blog Tasarımları', 'icon': 'fa-newspaper', 'color': 'from-amber-500 to-yellow-500', 'desc': 'Blog ve haber tasarımları'},
    'design-faq': {'name': 'SSS Tasarımları', 'icon': 'fa-circle-question', 'color': 'from-rose-500 to-pink-500', 'desc': 'Sıkça sorulan sorular tasarımları'},
    'design-footer': {'name': 'Footer Tasarımları', 'icon': 'fa-rectangle-wide', 'color': 'from-slate-500 to-gray-500', 'desc': 'Footer tasarımları'},
    'design-chatbot-inline': {'name': 'Chatbot Inline', 'icon': 'fa-comment-dots', 'color': 'from-blue-500 to-cyan-500', 'desc': 'Inline chatbot tasarımları'},
    'design-chatbot-popup': {'name': 'Chatbot Popup', 'icon': 'fa-messages', 'color': 'from-purple-500 to-pink-500', 'desc': 'Popup chatbot tasarımları'},
    'design-accordion': {'name': 'Accordion Tasarımları', 'icon': 'fa-bars-staggered', 'color': 'from-blue-500 to-purple-500', 'desc': 'Accordion ve collapse tasarımları'},
    'design-breadcrumb': {'name': 'Breadcrumb Tasarımları', 'icon': 'fa-ellipsis', 'color': 'from-gray-500 to-slate-500', 'desc': 'Breadcrumb navigasyon tasarımları'},
    'design-promotion': {'name': 'Promosyon Tasarımları', 'icon': 'fa-gift', 'color': 'from-red-500 to-pink-500', 'desc': 'Promosyon banner tasarımları'},
    'design-search': {'name': 'Arama Tasarımları', 'icon': 'fa-magnifying-glass', 'color': 'from-cyan-500 to-blue-500', 'desc': 'Arama kutusu tasarımları'},
    'design-sidebar': {'name': 'Sidebar Tasarımları', 'icon': 'fa-sidebar', 'color': 'from-indigo-500 to-purple-500', 'desc': 'Yan menu tasarımları'},
    'design-subheader': {'name': 'Subheader Tasarımları', 'icon': 'fa-heading', 'color': 'from-teal-500 to-cyan-500', 'desc': 'Alt başlık tasarımları'},
    'design-tabs': {'name': 'Tab Tasarımları', 'icon': 'fa-table-columns', 'color': 'from-purple-500 to-pink-500', 'desc': 'Sekme tasarımları'}
}

def create_gallery_page(category_key, count):
    """Bir kategori için gallery sayfası oluşturur"""

    info = CATEGORY_INFO.get(category_key, {
        'name': category_key.replace('design-', '').title(),
        'icon': 'fa-square',
        'color': 'from-gray-500 to-slate-500',
        'desc': 'Tasarım koleksiyonu'
    })

    # Tasarım kartlarını oluştur
    cards_html = ""
    for i in range(1, count + 1):
        cards_html += f'''
            <a href="{category_key}-{i}.html"
               class="group relative bg-white/5 backdrop-blur-lg rounded-3xl overflow-hidden border border-white/10 hover:border-white/30 transition-all duration-500 hover:scale-105 hover:shadow-2xl">

                <!-- Gradient Background -->
                <div class="absolute inset-0 bg-gradient-to-br {info['color']} opacity-0 group-hover:opacity-20 transition-opacity duration-500"></div>

                <!-- Preview Area (burada screenshot olacak) -->
                <div class="relative aspect-video bg-gradient-to-br {info['color']} opacity-20 flex items-center justify-center">
                    <div class="text-center">
                        <i class="fa-solid {info['icon']} text-6xl text-white/40 mb-4"></i>
                        <div class="text-2xl font-bold text-white/60">Tasarım #{i}</div>
                    </div>
                </div>

                <!-- Card Info -->
                <div class="relative p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold text-white group-hover:text-transparent group-hover:bg-clip-text group-hover:bg-gradient-to-r group-hover:{info['color']} transition-all">
                            {info['name']} #{i}
                        </h3>
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br {info['color']} flex items-center justify-center group-hover:scale-110 group-hover:rotate-12 transition-all">
                            <i class="fa-solid fa-arrow-right text-white"></i>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 text-sm text-gray-400">
                        <i class="fa-solid fa-eye"></i>
                        <span>Önizleme</span>
                    </div>
                </div>

                <!-- Hover Effect -->
                <div class="absolute -bottom-20 -right-20 w-40 h-40 bg-gradient-to-br {info['color']} rounded-full blur-3xl opacity-0 group-hover:opacity-30 transition-opacity duration-500"></div>
            </a>
'''

    # Gallery sayfası HTML
    html = f'''<!DOCTYPE html>
<html lang="tr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{info['name']} - iXtif Tasarım Kütüphanesi</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Font Awesome Pro 7 CDN -->
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.5.1/css/all.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        body {{
            font-family: 'Inter', sans-serif;
        }}
        h1, h2, h3, h4, h5, h6 {{
            font-family: 'Poppins', sans-serif;
        }}

        /* Custom scrollbar */
        ::-webkit-scrollbar {{
            width: 10px;
        }}
        ::-webkit-scrollbar-track {{
            background: #1e293b;
        }}
        ::-webkit-scrollbar-thumb {{
            background: linear-gradient(to bottom, #3b82f6, #8b5cf6);
            border-radius: 5px;
        }}
        ::-webkit-scrollbar-thumb:hover {{
            background: linear-gradient(to bottom, #2563eb, #7c3aed);
        }}
    </style>
</head>
<body class="bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 text-white min-h-screen">

    <!-- Header Navigation -->
    <div class="sticky top-0 z-50 bg-slate-900/90 backdrop-blur-lg border-b border-white/10">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <!-- Ana Sayfa -->
                <a href="index.html" class="flex items-center gap-3 px-4 py-2 bg-white/10 rounded-lg hover:bg-white/20 transition-all group">
                    <i class="fa-solid fa-home text-xl group-hover:scale-110 transition-transform"></i>
                    <span class="hidden sm:inline font-semibold">Ana Sayfa</span>
                </a>

                <!-- Kategori Başlığı -->
                <div class="text-center flex-1 mx-4">
                    <div class="inline-flex items-center gap-3 px-6 py-2 bg-gradient-to-r {info['color']} rounded-full">
                        <i class="fa-solid {info['icon']} text-2xl"></i>
                        <h1 class="text-xl md:text-2xl font-bold">{info['name']}</h1>
                    </div>
                    <p class="text-sm text-gray-400 mt-2">{info['desc']}</p>
                </div>

                <!-- Tasarım Sayısı -->
                <div class="text-center px-4 py-2 bg-white/10 rounded-lg">
                    <div class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r {info['color']}">
                        {count}
                    </div>
                    <div class="text-xs text-gray-400">Tasarım</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-12">

        <!-- Info Banner -->
        <div class="mb-12 bg-white/5 backdrop-blur-lg rounded-2xl p-6 border border-white/10">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br {info['color']} flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-info text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-2">Nasıl Kullanılır?</h3>
                    <p class="text-gray-400">
                        Aşağıdaki tasarımlardan birini seçin. Her tasarım ayrı bir sayfada açılacak ve üst kısımdaki
                        <strong class="text-white">İleri/Geri</strong> butonları ile diğer tasarımlara geçiş yapabilirsiniz.
                    </p>
                </div>
            </div>
        </div>

        <!-- Gallery Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            {cards_html}
        </div>

        <!-- Back to Home -->
        <div class="mt-12 text-center">
            <a href="index.html" class="inline-flex items-center gap-2 px-8 py-4 bg-white/10 hover:bg-white/20 rounded-full transition-all group">
                <i class="fa-solid fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                <span class="font-semibold">Ana Sayfaya Dön</span>
            </a>
        </div>

    </div>

</body>
</html>'''

    return html

def main():
    """Ana fonksiyon"""

    work_dir = '/Users/nurullah/Desktop/cms/laravel/public/ixtif-designs'
    os.chdir(work_dir)

    print(f"\n🎨 Gallery sayfaları oluşturuluyor...\n")

    created = 0
    for category_key, count in DESIGN_COUNTS.items():
        output_file = f"{category_key}.html"
        html = create_gallery_page(category_key, count)

        with open(output_file, 'w', encoding='utf-8') as f:
            f.write(html)

        print(f"✅ {output_file} → {count} tasarım kartı")
        created += 1

    print(f"\n✨ {created} gallery sayfası oluşturuldu!\n")

if __name__ == '__main__':
    main()
