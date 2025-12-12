{{-- Tenant 2 (İxtif.com) - Endüstriyel Ekipman Mega Menu --}}
{{-- ⚠️ DO NOT REMOVE - Tenant-specific mega menu for ixtif.com --}}

@php
// 🛡️ Admin sayfalarında mega-menu render etme
if (request()->is('admin/*')) {
    return;
}

// 🛡️ Tenant context yoksa render etme
if (!function_exists('tenant') || !tenant()) {
    return;
}

// 🛡️ Sadece Tenant 2 için (ixtif.com)
if (tenant()->id !== 2) {
    return;
}
@endphp

{{-- Mega Menu Container - Content loaded via AJAX on first hover --}}
<div id="mega-menu-products"
     class="hidden group-hover:block absolute top-full left-0 w-full bg-white dark:bg-gray-800 shadow-2xl border-t border-gray-200 dark:border-gray-700 z-50"
     data-loaded="false"
     data-api-url="{{ url('/api/v1/shops/mega-menu') }}">

    {{-- Loading State --}}
    <div id="mega-menu-loading" class="flex items-center justify-center py-12">
        <div class="text-center">
            <i class="fas fa-spinner fa-spin text-3xl text-blue-500 mb-3"></i>
            <p class="text-gray-600 dark:text-gray-400">Ürünler yükleniyor...</p>
        </div>
    </div>

    {{-- Content Container (filled by JavaScript) --}}
    <div id="mega-menu-content" class="hidden"></div>
</div>

{{-- Lazy Loading Script - ⚠️ DO NOT REMOVE --}}
<script>
(function() {
    'use strict';

    const menuContainer = document.getElementById('mega-menu-products');
    const loadingDiv = document.getElementById('mega-menu-loading');
    const contentDiv = document.getElementById('mega-menu-content');

    if (!menuContainer) return;

    let isLoaded = false;
    let isLoading = false;

    // Load menu data on first hover
    const parentLink = document.querySelector('a[href*="/shop"]')?.closest('li');

    if (parentLink) {
        parentLink.addEventListener('mouseenter', async function() {
            if (isLoaded || isLoading) return;

            isLoading = true;
            const apiUrl = menuContainer.dataset.apiUrl;

            try {
                const response = await fetch(apiUrl);
                const data = await response.json();

                if (data.success && data.data) {
                    renderMegaMenu(data.data);
                    isLoaded = true;
                    menuContainer.dataset.loaded = 'true';
                    loadingDiv.classList.add('hidden');
                    contentDiv.classList.remove('hidden');
                }
            } catch (error) {
                console.error('Mega menu load error:', error);
                loadingDiv.innerHTML = '<p class="text-red-500 text-center py-6">Menü yüklenemedi</p>';
            } finally {
                isLoading = false;
            }
        }, { once: false });
    }

    function renderMegaMenu(categories) {
        // Tenant 2 (İxtif) - Endüstriyel Ekipman Render
        let html = '<div class="container mx-auto px-4 py-8"><div class="grid grid-cols-5 gap-6">';

        categories.forEach((cat, index) => {
            html += `<div class="category-tab">
                <h3 class="font-bold mb-4 flex items-center gap-2">
                    <i class="${cat.icon || 'fa-solid fa-box'}"></i>
                    ${cat.title}
                </h3>`;

            if (cat.type === 'products' && cat.products) {
                cat.products.forEach(product => {
                    html += `
                        <div class="product-card mb-3 p-3 border rounded hover:shadow-md transition">
                            ${product.image ? `<img src="${product.image}" alt="${product.title}" loading="lazy" class="w-full h-24 object-cover rounded mb-2">` : ''}
                            <h4 class="text-sm font-medium line-clamp-2">${product.title}</h4>
                            ${product.price ? `<p class="text-xs text-blue-600 font-semibold mt-1">${product.price} ${product.currency}</p>` : ''}
                        </div>
                    `;
                });
            } else if (cat.type === 'subcategories' && cat.subcategories) {
                cat.subcategories.forEach(sub => {
                    html += `<div class="mb-2">
                        <a href="/urunler/${sub.slug}" class="text-sm hover:text-blue-600 transition">
                            ${sub.title}
                        </a>
                    </div>`;
                });
            }

            html += '</div>';
        });

        html += '</div></div>';
        contentDiv.innerHTML = html;
    }
})();
</script>
