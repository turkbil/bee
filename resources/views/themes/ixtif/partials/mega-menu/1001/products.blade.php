{{-- Tenant 1001 (Muzibu.com.tr) - Müzik Platformu Mega Menu --}}
{{-- ⚠️ DO NOT REMOVE - Tenant-specific mega menu for muzibu.com.tr --}}

@php
// 🛡️ Admin sayfalarında mega-menu render etme
if (request()->is('admin/*')) {
    return;
}

// 🛡️ Tenant context yoksa render etme
if (!function_exists('tenant') || !tenant()) {
    return;
}

// 🛡️ Sadece Tenant 1001 için (muzibu.com.tr)
if (tenant()->id !== 1001) {
    return;
}
@endphp

{{-- Mega Menu Container - Content loaded via AJAX on first hover --}}
<div id="mega-menu-music"
     class="hidden group-hover:block absolute top-full left-0 w-full bg-white dark:bg-gray-800 shadow-2xl border-t border-gray-200 dark:border-gray-700 z-50"
     data-loaded="false"
     data-api-url="{{ url('/api/v1/music/mega-menu') }}">

    {{-- Loading State --}}
    <div id="mega-menu-loading" class="flex items-center justify-center py-12">
        <div class="text-center">
            <i class="fas fa-spinner fa-spin text-3xl text-purple-500 mb-3"></i>
            <p class="text-gray-600 dark:text-gray-400">Müzik kategorileri yükleniyor...</p>
        </div>
    </div>

    {{-- Content Container (filled by JavaScript) --}}
    <div id="mega-menu-content" class="hidden"></div>
</div>

{{-- Lazy Loading Script - ⚠️ DO NOT REMOVE --}}
<script>
(function() {
    'use strict';

    const menuContainer = document.getElementById('mega-menu-music');
    const loadingDiv = document.getElementById('mega-menu-loading');
    const contentDiv = document.getElementById('mega-menu-content');

    if (!menuContainer) return;

    let isLoaded = false;
    let isLoading = false;

    // Load menu data on first hover (müzik kategorisi için)
    const parentLink = document.querySelector('a[href*="muzik"], a[href*="sarkilar"]')?.closest('li');

    if (parentLink) {
        parentLink.addEventListener('mouseenter', async function() {
            if (isLoaded || isLoading) return;

            isLoading = true;
            const apiUrl = menuContainer.dataset.apiUrl;

            try {
                const response = await fetch(apiUrl);
                const data = await response.json();

                if (data.success && data.data) {
                    renderMusicMegaMenu(data.data);
                    isLoaded = true;
                    menuContainer.dataset.loaded = 'true';
                    loadingDiv.classList.add('hidden');
                    contentDiv.classList.remove('hidden');
                }
            } catch (error) {
                console.error('Music mega menu load error:', error);
                loadingDiv.innerHTML = '<p class="text-red-500 text-center py-6">Menü yüklenemedi</p>';
            } finally {
                isLoading = false;
            }
        }, { once: false });
    }

    function renderMusicMegaMenu(data) {
        // Tenant 1001 (Muzibu) - Müzik Kategorileri Render
        let html = '<div class="container mx-auto px-4 py-8"><div class="grid grid-cols-4 gap-6">';

        // Örnek: Genres, Artists, Albums, Playlists gibi kategoriler
        if (data.genres) {
            html += `<div class="music-category">
                <h3 class="font-bold mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-music"></i>
                    Türler
                </h3>`;
            data.genres.forEach(genre => {
                html += `<div class="mb-2">
                    <a href="/muzik/tur/${genre.slug}" class="text-sm hover:text-purple-600 transition">
                        ${genre.name}
                    </a>
                </div>`;
            });
            html += '</div>';
        }

        if (data.artists) {
            html += `<div class="music-category">
                <h3 class="font-bold mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-microphone"></i>
                    Sanatçılar
                </h3>`;
            data.artists.slice(0, 8).forEach(artist => {
                html += `<div class="mb-2">
                    <a href="/sanatci/${artist.slug}" class="text-sm hover:text-purple-600 transition">
                        ${artist.name}
                    </a>
                </div>`;
            });
            html += '</div>';
        }

        html += '</div></div>';
        contentDiv.innerHTML = html;
    }
})();
</script>
