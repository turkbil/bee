/**
 * Design System - Otomatik İleri/Geri Navigasyon Scripti
 *
 * Bu script, design-*.html sayfalarına otomatik olarak navigasyon ekler.
 *
 * Kullanım: Her sayfanın sonuna ekleyin:
 * <script src="navigation-auto.js"></script>
 */

(function() {
    'use strict';

    // Mevcut sayfa URL'sini al
    const currentUrl = window.location.pathname;
    const currentFile = currentUrl.split('/').pop();

    // Pattern: design-{category}-{number}.html
    const match = currentFile.match(/design-(.+)-(\d+)\.html$/);

    if (!match) {
        console.log('Bu sayfa için otomatik navigasyon desteklenmiyor.');
        return;
    }

    const [_, category, currentNumber] = match;
    const num = parseInt(currentNumber);

    // Kategori bilgileri (max sayfa sayıları)
    const categories = {
        'about': 10,
        'accordion': 6,
        'blog': 10,
        'breadcrumb': 8,
        'categories': 10,
        'category': 10,
        'chatbot-inline': 6,
        'chatbot-popup': 5,
        'contact': 18,
        'cookie-consent': 10,
        'cta': 10,
        'faq': 10,
        'features': 10,
        'footer': 10,
        'gallery': 10,
        'glass-compact': 3,
        'header': 10,
        'hero': 10,
        'lazy-loading-demo': 1,
        'menu': 10,
        'menu-FULL': 1,
        'newsletter': 10,
        'page-hero': 8,
        'partners': 10,
        'pricing': 10,
        'product': 10,
        'product-card-premium': 12,
        'product-card-luxe': 12,
        'products': 10,
        'promotion': 6,
        'promotions': 10,
        'search': 6,
        'services': 10,
        'shop-index': 10,
        'sidebar': 6,
        'stats': 10,
        'subheader': 8,
        'subheader-shop': 8,
        'subheader-shop-index': 1,
        'tabs': 6,
        'testimonials': 10
    };

    const maxPages = categories[category] || 10;

    // Önceki ve sonraki sayfa URL'lerini oluştur
    const prevNumber = num - 1;
    const nextNumber = num + 1;

    const hasPrev = prevNumber > 0;
    const hasNext = nextNumber <= maxPages;

    const prevUrl = hasPrev ? `design-${category}-${prevNumber}.html` : null;
    const nextUrl = hasNext ? `design-${category}-${nextNumber}.html` : null;

    // Kategori ana sayfası
    const categoryIndexUrl = `design-${category}.html`;

    // Navigation bar oluştur (eğer yoksa)
    let navbar = document.querySelector('.design-navigation');

    if (!navbar) {
        // Yeni navbar oluştur
        navbar = document.createElement('div');
        navbar.className = 'design-navigation fixed top-0 left-0 right-0 z-50 bg-slate-900/90 backdrop-blur-lg border-b border-white/10';
        navbar.innerHTML = `
            <div class="container mx-auto px-4 py-4">
                <div class="flex items-center justify-between">
                    <!-- Sol: Ana Sayfa + Kategori -->
                    <div class="flex items-center gap-2">
                        <a href="index.html" class="flex items-center gap-2 px-4 py-2 bg-white/10 rounded-lg hover:bg-white/20 transition-all">
                            <i class="fa-solid fa-home"></i>
                            <span class="hidden sm:inline">Ana Sayfa</span>
                        </a>
                        <a href="${categoryIndexUrl}" class="hidden md:flex items-center gap-2 px-4 py-2 bg-blue-600/20 text-blue-300 rounded-lg hover:bg-blue-600/30 transition-all">
                            <i class="fa-solid fa-folder"></i>
                            <span>${category.charAt(0).toUpperCase() + category.slice(1)}</span>
                        </a>
                    </div>

                    <!-- Orta: Sayfa Numarası -->
                    <div class="text-center">
                        <div class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-purple-400">
                            ${num} / ${maxPages}
                        </div>
                        <div class="text-xs text-gray-400 mt-1">Tasarım Numarası</div>
                    </div>

                    <!-- Sağ: Prev/Next Butonları -->
                    <div class="flex items-center gap-2">
                        ${hasPrev
                            ? `<a href="${prevUrl}" class="px-4 py-2 bg-white/10 rounded-lg hover:bg-white/20 transition-all flex items-center gap-2">
                                <i class="fa-solid fa-arrow-left"></i>
                                <span class="hidden sm:inline">Önceki</span>
                               </a>`
                            : `<div class="px-4 py-2 bg-white/5 rounded-lg opacity-50 cursor-not-allowed transition-all flex items-center gap-2">
                                <i class="fa-solid fa-arrow-left"></i>
                                <span class="hidden sm:inline">Önceki</span>
                               </div>`
                        }
                        ${hasNext
                            ? `<a href="${nextUrl}" class="px-4 py-2 bg-white/10 rounded-lg hover:bg-white/20 transition-all flex items-center gap-2">
                                <span class="hidden sm:inline">Sonraki</span>
                                <i class="fa-solid fa-arrow-right"></i>
                               </a>`
                            : `<div class="px-4 py-2 bg-white/5 rounded-lg opacity-50 cursor-not-allowed transition-all flex items-center gap-2">
                                <span class="hidden sm:inline">Sonraki</span>
                                <i class="fa-solid fa-arrow-right"></i>
                               </div>`
                        }
                    </div>
                </div>
            </div>
        `;

        // Body'nin en başına ekle
        document.body.insertBefore(navbar, document.body.firstChild);

        // Eğer pt-20 class'ı yoksa main content'e ekle
        const mainContent = document.querySelector('.pt-20') || document.body.children[1];
        if (mainContent && !mainContent.classList.contains('pt-20')) {
            mainContent.classList.add('pt-20');
        }
    } else {
        // Mevcut navbar'ı güncelle (eğer varsa)
        const pageNumber = navbar.querySelector('.text-2xl');
        if (pageNumber) {
            pageNumber.textContent = `${num} / ${maxPages}`;
        }
    }

    // Klavye kısayolları (Arrow keys ile navigasyon)
    document.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowLeft' && hasPrev) {
            window.location.href = prevUrl;
        } else if (e.key === 'ArrowRight' && hasNext) {
            window.location.href = nextUrl;
        } else if (e.key === 'Home') {
            window.location.href = 'index.html';
        } else if (e.key === 'Escape') {
            window.location.href = categoryIndexUrl;
        }
    });

    // Alt navbar (sticky bottom navigation) - opsiyonel
    const bottomNav = document.createElement('div');
    bottomNav.className = 'fixed bottom-6 right-6 flex items-center gap-3 z-40';
    bottomNav.innerHTML = `
        ${hasPrev
            ? `<a href="${prevUrl}" title="Önceki (←)" class="w-12 h-12 bg-blue-600 hover:bg-blue-700 rounded-full flex items-center justify-center text-white shadow-lg hover:shadow-2xl transition-all">
                <i class="fa-solid fa-arrow-left"></i>
               </a>`
            : ''
        }
        <a href="${categoryIndexUrl}" title="Kategori (Esc)" class="w-12 h-12 bg-purple-600 hover:bg-purple-700 rounded-full flex items-center justify-center text-white shadow-lg hover:shadow-2xl transition-all">
            <i class="fa-solid fa-folder"></i>
        </a>
        ${hasNext
            ? `<a href="${nextUrl}" title="Sonraki (→)" class="w-12 h-12 bg-blue-600 hover:bg-blue-700 rounded-full flex items-center justify-center text-white shadow-lg hover:shadow-2xl transition-all">
                <i class="fa-solid fa-arrow-right"></i>
               </a>`
            : ''
        }
    `;

    document.body.appendChild(bottomNav);

    // Console'da bilgi ver
    console.log(`%c🎨 Design Navigation Active`, 'color: #3b82f6; font-size: 16px; font-weight: bold;');
    console.log(`Category: ${category}`);
    console.log(`Current Page: ${num} / ${maxPages}`);
    console.log(`Keyboard: ← Previous | → Next | Home | Esc (Category)`);

})();
