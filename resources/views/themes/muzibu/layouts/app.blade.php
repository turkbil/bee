@php
    // 🔐 Şifre Koruması - DEVRE DIŞI
    // $constructionPassword = 'nn';
    // $cookieName = 'mzb_auth_' . tenant('id');
    // $cookieValue = md5($constructionPassword . 'salt2024');
    // $isAuthenticated = isset($_COOKIE[$cookieName]) && $_COOKIE[$cookieName] === $cookieValue;
    $isAuthenticated = true; // ✅ ŞİFRE KORUMASINI KALDIRDIK
@endphp

@if(!$isAuthenticated)
    @include('themes.muzibu.password-protection')
@else
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" x-data="muzibuApp()" x-init="init()" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- 🔇 CONSOLE FILTER - Suppress tracking/marketing noise --}}
    <script>
    (function(){const p=[/yandex/i,/attestation/i,/topics/i,/googletagmanager/i,/facebook/i,/ERR_BLOCKED_BY_CLIENT/i];const s=m=>!m?false:p.some(x=>x.test(m));const e=console.error;console.error=function(){const m=Array.from(arguments).join(' ');if(!s(m))e.apply(console,arguments);};const w=console.warn;console.warn=function(){const m=Array.from(arguments).join(' ');if(!s(m))w.apply(console,arguments);};const l=console.log;console.log=function(){const m=Array.from(arguments).join(' ');if(!s(m))l.apply(console,arguments);};})();
    </script>

    {{-- User Auth for Frontend JS --}}
    @auth
        <meta name="user-id" content="{{ auth()->id() }}">
        <meta name="user-email" content="{{ auth()->user()->email }}">
    @endauth

    {{-- Device Limit Session Flash --}}
    @if (session('device_limit_exceeded'))
        <meta name="device-limit-exceeded" content="true">
        <meta name="device-limit" content="{{ session('device_limit', 1) }}">
        <meta name="active-device-count" content="{{ session('active_device_count', 2) }}">
    @endif

    {{-- Global SEO Meta Tags - SeoManagement Module (title dahil) --}}
    <x-seo-meta />

    {{-- Performance: DNS Prefetch & Preconnect --}}
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    {{-- Performance: Preload Critical Fonts (FontAwesome) --}}
    <link rel="preload" href="{{ asset('assets/libs/fontawesome-pro@7.1.0/webfonts/fa-solid-900.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('assets/libs/fontawesome-pro@7.1.0/webfonts/fa-light-300.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('assets/libs/fontawesome-pro@7.1.0/webfonts/fa-regular-400.woff2') }}" as="font" type="font/woff2" crossorigin>

    {{-- Tailwind CSS - Tenant Aware (tenant-1001.css) --}}
    <link rel="stylesheet" href="{{ tenant_css() }}">

    {{-- FontAwesome Pro 7.1.0 (Local) --}}
    <link rel="stylesheet" href="{{ asset('assets/libs/fontawesome-pro@7.1.0/css/all.css') }}">

    {{-- Alpine.js provided by Livewire --}}

    {{-- Audio Libraries --}}
    <script src="https://cdn.jsdelivr.net/npm/hls.js@1.4.12/dist/hls.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/howler@2.2.4/dist/howler.min.js"></script>

    {{-- SortableJS for Queue Drag & Drop (Mobile + Desktop) --}}
    <script src="{{ asset('admin-assets/libs/sortable/sortable.min.js') }}"></script>

    {{-- ⚡ instant.page DISABLED - Conflicts with SPA Router (causes double navigation) --}}
    {{-- <script src="//instant.page/5.2.0" type="module" data-intensity="hover" data-delay="50"></script> --}}

    @livewireStyles

    {{-- Favicon - Settings'den çek, yoksa /favicon.ico fallback --}}
    @php
        $faviconUrl = setting('site_favicon') ?: '/favicon.ico';
    @endphp
    <link rel="icon" type="image/x-icon" href="{{ $faviconUrl }}">
    <link rel="apple-touch-icon" href="{{ $faviconUrl }}">

    {{-- PWA Manifest (2025 Best Practice) --}}
    <link rel="manifest" href="{{ route('manifest') }}">

    {{-- Theme Color for Mobile Browser Bar (Tenant-aware) --}}
    @php
        $themeColor = setting('site_theme_color') ?: '#000000';
        $themeColorLight = setting('site_theme_color_light') ?: '#ffffff';
        $themeColorDark = setting('site_theme_color_dark') ?: '#1a202c';
    @endphp
    <meta name="theme-color" content="{{ $themeColor }}">
    <meta name="theme-color" media="(prefers-color-scheme: light)" content="{{ $themeColorLight }}">
    <meta name="theme-color" media="(prefers-color-scheme: dark)" content="{{ $themeColorDark }}">

    {{-- Custom Styles --}}
    <link rel="stylesheet" href="{{ versioned_asset('themes/muzibu/css/muzibu-layout.css') }}">
    <link rel="stylesheet" href="{{ versioned_asset('themes/muzibu/css/muzibu-custom.css') }}">
    <script src="{{ versioned_asset('themes/muzibu/js/player/core/player-core.js') }}"></script>
    <script src="{{ versioned_asset('themes/muzibu/js/player/features/play-helpers.js') }}"></script>

    {{-- 🌍 Global Lang Strings for JS (Blade'den çekilir) --}}
    <script>
        window.muzibuLang = {
            queue: {
                added_to_queue: "{{ trans('muzibu::front.player.added_to_queue') }}",
                added_to_queue_next: "{{ trans('muzibu::front.player.added_to_queue_next') }}",
                added_with_duplicates: "{{ trans('muzibu::front.player.added_with_duplicates_removed') }}",
                added_next_with_duplicates: "{{ trans('muzibu::front.player.added_next_with_duplicates_removed') }}",
                song_not_found: "{{ trans('muzibu::front.player.song_not_found_to_add') }}",
                queue_error: "{{ trans('muzibu::front.player.queue_add_error') }}"
            }
        };
    </script>

    {{-- 🎯 Alpine Apps - External File (dashboardApp, corporatePanel, playlistEditor, etc.) --}}
    <script src="{{ versioned_asset('themes/muzibu/js/alpine-apps.js') }}"></script>

    {{-- 🤖 Universal Schema Auto-Render (Dynamic for ALL modules) --}}
    {{-- SKIP if Controller already shared metaTags with schemas (prevents duplicates) --}}
    @php
        $sharedMetaTags = view()->getShared()['metaTags'] ?? null;
        $hasControllerSchemas = $sharedMetaTags && isset($sharedMetaTags['schemas']) && !empty($sharedMetaTags['schemas']);
    @endphp
    @if(!$hasControllerSchemas && isset($item) && is_object($item) && method_exists($item, 'getAllSchemas'))
        {!! \App\Services\SEOService::getAllSchemas($item) !!}
    @endif

    {{-- 🎯 Marketing Platforms Auto-Loader (GTM, GA4, Facebook, Yandex, LinkedIn, TikTok, Clarity) --}}
    <x-marketing.auto-platforms />

    @yield('styles')

</head>
<body class="bg-black text-white overflow-hidden"
      @play-song.window="playSong($event.detail.songId)"
      @play-all-preview.window="
        if ($store.sidebar.previewInfo?.type === 'Playlist') {
            window.playPlaylist ? window.playPlaylist($store.sidebar.previewInfo.id) : $store.player.playPlaylist($store.sidebar.previewInfo.id);
        } else if ($store.sidebar.previewInfo?.type === 'Album') {
            window.playAlbum ? window.playAlbum($store.sidebar.previewInfo.id) : $store.player.playAlbum($store.sidebar.previewInfo.id);
        } else if ($store.sidebar.previewInfo?.type === 'Genre') {
            window.playGenres ? window.playGenres($store.sidebar.previewInfo.id) : $store.player.playGenre($store.sidebar.previewInfo.id);
        } else if ($store.sidebar.previewInfo?.type === 'Sector') {
            window.playSector ? window.playSector($store.sidebar.previewInfo.id) : $store.player.playSector($store.sidebar.previewInfo.id);
        } else if ($store.sidebar.previewInfo?.type === 'Radio') {
            window.playRadio ? window.playRadio($store.sidebar.previewInfo.id) : $store.player.playRadio($store.sidebar.previewInfo.id);
        }
      "
      @play-all-entity.window="
        if ($store.sidebar.entityInfo?.type === 'Playlist') {
            window.playPlaylist ? window.playPlaylist($store.sidebar.entityInfo.id) : $store.player.playPlaylist($store.sidebar.entityInfo.id);
        } else if ($store.sidebar.entityInfo?.type === 'Album') {
            window.playAlbum ? window.playAlbum($store.sidebar.entityInfo.id) : $store.player.playAlbum($store.sidebar.entityInfo.id);
        } else if ($store.sidebar.entityInfo?.type === 'Genre') {
            window.playGenres ? window.playGenres($store.sidebar.entityInfo.id) : $store.player.playGenre($store.sidebar.entityInfo.id);
        } else if ($store.sidebar.entityInfo?.type === 'Sector') {
            window.playSector ? window.playSector($store.sidebar.entityInfo.id) : $store.player.playSector($store.sidebar.entityInfo.id);
        } else if ($store.sidebar.entityInfo?.type === 'Radio') {
            window.playRadio ? window.playRadio($store.sidebar.entityInfo.id) : $store.player.playRadio($store.sidebar.entityInfo.id);
        }
      "
      @play-all-songs.window="
        if ($event.detail.playlistId) {
            window.playPlaylist ? window.playPlaylist($event.detail.playlistId) : $store.player.playPlaylist($event.detail.playlistId);
        } else if ($event.detail.albumId) {
            window.playAlbum ? window.playAlbum($event.detail.albumId) : $store.player.playAlbum($event.detail.albumId);
        } else if ($event.detail.genreId) {
            window.playGenre ? window.playGenre($event.detail.genreId) : $store.player.playGenre($event.detail.genreId);
        }
      "
      @play-all-playlists.window="
        if ($event.detail.sectorId) {
            window.playSector ? window.playSector($event.detail.sectorId) : $store.player.playSector($event.detail.sectorId);
        }
      ">
    {{-- 🎯 GTM Body Snippet (No-Script Fallback) --}}
    <x-marketing.gtm-body />

    {{-- Hidden Audio Elements --}}
    <audio id="hlsAudio" x-ref="hlsAudio" class="hidden"></audio>
    <audio id="hlsAudioNext" class="hidden"></audio>

    @php
        // 🚀 HYBRID: PHP initial value + Alpine SPA updates
        // Sağ sidebar gösterilecek route'lar (music pages - dashboard HARİÇ)
        // Mobilde (<768px) GİZLİ, Tablet+ (768px+) GÖRÜNÜR
        $showRightSidebar = in_array(Route::currentRouteName(), [
            // Dashboard sayfasında sağ sidebar KAPALI
            'muzibu.home',
            'muzibu.songs.index',
            'muzibu.songs.show',
            'muzibu.albums.index',
            'muzibu.albums.show',
            'muzibu.artists.index',
            'muzibu.artists.show',
            'muzibu.playlists.index',
            'muzibu.playlists.show',
            'muzibu.genres.index',
            'muzibu.genres.show',
            'muzibu.sectors.index',
            'muzibu.sectors.show',
            'muzibu.radios.index',
            'muzibu.search',
            'muzibu.favorites',
            'muzibu.my-playlists',
            'muzibu.corporate-playlists',
            'muzibu.listening-history',
        ]);

        // Grid class'ları - PHP initial, Alpine SPA override
        // ⚡ SAĞ SIDEBAR MD+ (768px+) ekranlarda görünür (sadece mobilde GİZLİ)
        $gridColsWithSidebar = 'md:grid-cols-[1fr_280px] lg:grid-cols-[220px_1fr_280px] xl:grid-cols-[220px_1fr_320px] 2xl:grid-cols-[220px_1fr_360px]';
        $gridColsNoSidebar = 'lg:grid-cols-[220px_1fr] xl:grid-cols-[220px_1fr] 2xl:grid-cols-[220px_1fr]';
        $initialGridCols = $showRightSidebar ? $gridColsWithSidebar : $gridColsNoSidebar;
    @endphp

    {{-- Main App Grid - Hybrid: PHP initial + Alpine SPA updates --}}
    {{-- md (768px+): gap, padding, sağ sidebar başlar --}}
    {{-- lg (1024px+): sol sidebar da görünür --}}
    <div
        id="main-app-grid"
        class="grid grid-rows-[56px_1fr_auto] grid-cols-1 {{ $initialGridCols }} h-[100dvh] w-full gap-0 md:gap-3 px-0 pb-0 pt-0 md:px-3 md:pt-3"
        x-bind:class="$store.sidebar?.rightSidebarVisible
            ? '{{ $gridColsWithSidebar }}'
            : '{{ $gridColsNoSidebar }}'"
    >
        @include('themes.muzibu.components.header')
        @include('themes.muzibu.components.sidebar-left')

        {{-- Mobile Menu Overlay - Grid içinde (sidebar ile aynı stacking context) --}}
        <div class="muzibu-mobile-overlay" onclick="toggleMobileMenu()"></div>

        {{-- MAIN CONTENT --}}
        <main class="muzibu-main row-start-2 relative overflow-hidden">
            <div class="overflow-y-auto h-full relative">
                {{-- V3: Turuncu → Kırmızı → Bordo - Yatay Animasyonlu + Dark Altta --}}
                <div class="absolute top-0 left-0 right-0 h-[250px] rounded-t-lg pointer-events-none overflow-hidden">
                    {{-- Animated layer (Soldan sağa renk kayması) --}}
                    <div class="absolute top-0 left-0 w-[200%] h-full animate-gradient-horizontal"></div>
                    {{-- Dark overlay (Altta sabit) --}}
                    <div class="absolute top-0 left-0 right-0 bottom-0 bg-gradient-to-b from-transparent via-black/50 to-[#121212]"></div>
                </div>

                {{-- Content (Gradient ile birlikte scroll yapar) --}}
                <div class="relative z-10">
                    {{-- 🚀 SPA Loading Skeleton --}}
                    <div x-show="isLoading" x-cloak class="spa-loading-skeleton">
                        @include('themes.muzibu.partials.loading-skeleton')
                    </div>

                    {{-- 🚀 SPA Content Wrapper --}}
                    <div class="spa-content-wrapper" id="spaContent">
                        @yield('content')
                        {{ $slot ?? '' }}
                    </div>
                </div>
            </div>
        </main>

        {{-- Right Sidebar - MD+ screens (768px+), Hybrid: PHP initial + Alpine SPA --}}
        {{-- SADECE MOBİLDE GİZLİ (<768px), TABLET VE DESKTOP'TA GÖSTER (768px+) --}}
        {{-- 768px+: Sağ sidebar görünür, 1024px+: Her iki sidebar da görünür --}}
        <aside
            class="muzibu-right-sidebar row-start-2 overflow-y-auto rounded-2xl {{ $showRightSidebar ? 'hidden md:block' : 'hidden' }}"
            x-bind:class="$store.sidebar?.rightSidebarVisible ? 'md:block' : 'hidden'"
        >
            @include('themes.muzibu.components.sidebar-right')
        </aside>

        @include('themes.muzibu.components.player')
        @include('themes.muzibu.components.queue-overlay')
        @include('themes.muzibu.components.lyrics-overlay')
        @include('themes.muzibu.components.keyboard-shortcuts-overlay')
        @include('themes.muzibu.components.loading-overlay')
    </div>

    {{-- Auth Modal - REMOVED: Users now go to /login and /register pages directly --}}

    {{-- 🔐 NEW DEVICE LIMIT SYSTEM (User chooses what to do) --}}
    @include('themes.muzibu.components.device-limit-warning-modal')
    @include('themes.muzibu.components.device-selection-modal')

    {{-- Create Playlist Modal - Using theme version at bottom of page (global) --}}

    {{-- Play Limits Modals - DEVRE DIŞI (3 şarkı limiti kaldırıldı) --}}
    {{-- @include('themes.muzibu.components.play-limits-modals') --}}

    {{-- Session Check --}}
    @include('themes.muzibu.components.session-check')

    {{-- AI Chat Widget - Sadece root kullanıcılar için --}}
    @auth
        @if(auth()->user()->hasRole('root'))
            @include('themes.muzibu.components.ai-chat-widget')
        @endif
    @endauth

    {{-- Context Menu System --}}
    @include('themes.muzibu.components.context-menu')
    @include('themes.muzibu.components.rating-modal')
    @include('themes.muzibu.components.playlist-select-modal')
    @include('themes.muzibu.components.confirm-modal')

    {{-- 🍪 COOKIE CONSENT - Design 2 (Compact Modern) --}}
    @include('themes.muzibu.components.cookie-consent')

    {{-- ⚡ CRITICAL: Livewire MUST load BEFORE Muzibu scripts (Alpine.js dependency) --}}
    @livewireScripts

    @once
    {{-- 🎯 MODULAR JAVASCRIPT ARCHITECTURE --}}

    {{-- 1. Core Utilities (önce yükle - diğerleri bağımlı) --}}
    <script src="{{ versioned_asset('themes/muzibu/js/player/core/safe-storage.js') }}"></script>

    {{-- 2. Alpine Store --}}
    <script src="{{ versioned_asset('themes/muzibu/js/muzibu-store.js') }}"></script>

    {{-- 3. Player Features (modular - player-core bunları spread eder) --}}
    <script src="{{ versioned_asset('themes/muzibu/js/player/features/favorites.js') }}"></script>
    <script src="{{ versioned_asset('themes/muzibu/js/player/features/auth.js') }}"></script>
    {{-- ❌ REMOVED: keyboard.js (klavye kısayolları kaldırıldı) --}}
    <script src="{{ versioned_asset('themes/muzibu/js/player/features/api.js') }}"></script>
    <script src="{{ versioned_asset('themes/muzibu/js/player/features/session.js') }}"></script>
    <script src="{{ versioned_asset('themes/muzibu/js/player/features/spot-player.js') }}"></script>
    <script src="{{ asset('themes/muzibu/js/player/features/spa-router.js') }}?v={{ filemtime(public_path('themes/muzibu/js/player/features/spa-router.js')) }}"></script>
    {{-- ❌ REMOVED: play-helpers.js (already loaded in HEAD) --}}
    <script src="{{ versioned_asset('themes/muzibu/js/global-helpers.js') }}"></script>

    {{-- Context Menu System (Hybrid Approach) --}}
    <script src="{{ versioned_asset('themes/muzibu/js/context-menus/menu-builder.js') }}"></script>

    {{-- Context Menu - Handlers --}}
    <script src="{{ versioned_asset('themes/muzibu/js/context-menus/handlers/play-handler.js') }}"></script>
    <script src="{{ versioned_asset('themes/muzibu/js/context-menus/handlers/queue-handler.js') }}"></script>
    <script src="{{ versioned_asset('themes/muzibu/js/context-menus/handlers/favorite-handler.js') }}"></script>
    <script src="{{ versioned_asset('themes/muzibu/js/context-menus/handlers/rating-handler.js') }}"></script>
    <script src="{{ versioned_asset('themes/muzibu/js/context-menus/handlers/playlist-handler.js') }}"></script>

    {{-- Context Menu - Actions (per content type) --}}
    <script src="{{ versioned_asset('themes/muzibu/js/context-menus/actions/song-actions.js') }}"></script>
    <script src="{{ versioned_asset('themes/muzibu/js/context-menus/actions/album-actions.js') }}"></script>
    <script src="{{ versioned_asset('themes/muzibu/js/context-menus/actions/playlist-actions.js') }}"></script>
    <script src="{{ versioned_asset('themes/muzibu/js/context-menus/actions/genre-actions.js') }}"></script>
    <script src="{{ versioned_asset('themes/muzibu/js/context-menus/actions/sector-actions.js') }}"></script>
    <script src="{{ versioned_asset('themes/muzibu/js/context-menus/actions/radio-actions.js') }}"></script>
    <script src="{{ versioned_asset('themes/muzibu/js/context-menus/actions/artist-actions.js') }}"></script>

    {{-- Context Menu - Utils --}}
    <script src="{{ versioned_asset('themes/muzibu/js/context-menus/utils/action-executor.js') }}"></script>

    {{-- 4. Player Core - MOVED TO HEAD for early initialization --}}

    {{-- 5. Utils --}}
    <script src="{{ versioned_asset('themes/muzibu/js/utils/muzibu-cache.js') }}"></script>

    {{-- 6. UI Components --}}
    <script src="{{ versioned_asset('themes/muzibu/js/ui/muzibu-toast.js') }}"></script>
    <script src="{{ versioned_asset('themes/muzibu/js/ui/muzibu-theme.js') }}"></script>

    {{-- AI Chat --}}
    <script src="{{ versioned_asset('themes/muzibu/js/ai/tenant1001-ai-chat.js') }}"></script>

    {{-- Corporate Spots Manager (SPA Compatible) --}}
    <script src="{{ versioned_asset('themes/muzibu/js/corporate-spots.js') }}"></script>

    {{-- 7. 🚀 SPA Router - MODULAR VERSION USED (loaded in line 211 as player feature) --}}
    {{-- OLD STANDALONE ROUTER REMOVED - Duplicate initialization fixed --}}
    @endonce

    <script>
        // 🔇 Suppress storage access errors (browser privacy/extension related)
        window.addEventListener('unhandledrejection', (event) => {
            if (event.reason?.message?.includes('Access to storage is not allowed')) {
                event.preventDefault(); // Suppress console error
            }
        });

        // 🌐 Global Alpine defaults (ReferenceError önleme)
        // Device & Modal
        window.showKeyboardHelp = window.showKeyboardHelp || false;
        window.showDeviceSelectionModal = window.showDeviceSelectionModal || false;
        window.showDeviceLimitWarning = window.showDeviceLimitWarning || false;
        window.showDeviceLimitModal = window.showDeviceLimitModal || false;
        window.deviceTerminateLoading = window.deviceTerminateLoading || false;
        window.activeDevices = window.activeDevices || [];
        window.deviceLimit = window.deviceLimit || 1;

        // Player state
        window.isLoading = window.isLoading || false;
        window.isSongLoading = window.isSongLoading || false;
        window.isPlaying = window.isPlaying || false;
        window.currentSong = window.currentSong || null;
        window.currentTime = window.currentTime || 0;
        window.duration = window.duration || 0;
        window.progressPercent = window.progressPercent || 0;
        window.isLiked = window.isLiked || false;

        // Playback controls
        window.shuffle = window.shuffle || false;
        window.repeatMode = window.repeatMode || 'off';

        // Volume & Audio
        window.volume = window.volume ?? 80;
        window.isMuted = window.isMuted || false;

        // Stream info
        window.currentStreamType = window.currentStreamType || 'hls';
        window.lastFallbackReason = window.lastFallbackReason || null;

        // UI panels & Debug
        window.showLyrics = window.showLyrics || false;
        window.showQueue = window.showQueue || false;
        window.showDebugInfo = window.showDebugInfo || false;

        // Auth (fallback - overwritten by config below)
        window.isLoggedIn = window.isLoggedIn || false;
        window.currentUser = window.currentUser || null;

        // Helper functions
        window.formatTime = window.formatTime || function(sec) {
            const t = Math.max(0, Math.floor(sec || 0));
            const m = Math.floor(t / 60);
            const s = (t % 60).toString().padStart(2, '0');
            return `${m}:${s}`;
        };

        // Config for Alpine.js
        window.muzibuPlayerConfig = {
            lang: @json(tenant_lang('player')),
            frontLang: @json(tenant_lang('front')),
            isLoggedIn: {{ auth()->check() ? 'true' : 'false' }},
            currentUser: @if(auth()->check())
                @php
                    $user = auth()->user();

                    // 🔴 TEK KAYNAK: users.subscription_expires_at
                    $subscriptionExpiresAt = $user->subscription_expires_at;
                    $subscriptionEndsAt = $subscriptionExpiresAt?->toIso8601String();

                    // 🔥 Device limit (backend'den al - 3-tier hierarchy)
                    $deviceService = app(\Modules\Muzibu\App\Services\DeviceService::class);
                    $deviceLimit = $deviceService->getDeviceLimit($user);
                @endphp
                {
                id: {{ $user->id }},
                name: "{{ $user->name }}",
                email: "{{ $user->email }}",
                is_premium: {{ $user->isPremium() ? 'true' : 'false' }},
                is_root: {{ $user->hasRole('root') ? 'true' : 'false' }},
                subscription_ends_at: {!! $subscriptionEndsAt ? '"' . $subscriptionEndsAt . '"' : 'null' !!}
            }
            @else
                null
            @endif,
            {{-- todayPlayedCount kaldırıldı - 3 şarkı limiti devre dışı --}}
            tenantId: {{ tenant('id') }},
            // 🔥 Config values (Muzibu module)
            @if(auth()->check())
                deviceLimit: {{ $deviceLimit ?? 1 }},
            @else
                deviceLimit: 1,
            @endif
            sessionPollingInterval: {{ config('muzibu.session.polling_interval', 30000) }},
            crossfadeDuration: {{ config('muzibu.player.crossfade_duration', 4000) }}
        };

        // 🔐 CSRF Token Auto-Renewal (419 hatası önleme)
        if (typeof axios !== 'undefined') {
            // Axios CSRF interceptor
            axios.interceptors.response.use(
                response => response,
                async error => {
                    // CSRF token mismatch (419)
                    if (error.response?.status === 419) {
                        console.warn('🔐 CSRF token expired, refreshing...');

                        try {
                            // Yeni token al
                            await axios.get('/sanctum/csrf-cookie');

                            // Meta tag güncelle
                            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                            if (token) {
                                axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
                            }

                            // Orijinal isteği tekrar gönder
                            return axios(error.config);
                        } catch (refreshError) {
                            console.error('❌ CSRF token refresh failed:', refreshError);
                            return Promise.reject(error);
                        }
                    }
                    return Promise.reject(error);
                }
            );

            // Initial CSRF token setup
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (token) {
                axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
                axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
            }
        }

        // Mobile Menu Toggle - Enhanced for Mobile & Tablet
        function toggleMobileMenu() {
            const sidebar = document.getElementById('leftSidebar');
            const overlay = document.querySelector('.muzibu-mobile-overlay');
            const hamburger = document.getElementById('hamburgerIcon');
            const isOpen = sidebar.classList.contains('active');

            if (isOpen) {
                // Close
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
                if (hamburger) hamburger.classList.remove('active');
                document.body.style.overflow = '';
            } else {
                // Open
                sidebar.classList.add('active');
                overlay.classList.add('active');
                if (hamburger) hamburger.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeMobileMenu() {
            const sidebar = document.getElementById('leftSidebar');
            const overlay = document.querySelector('.muzibu-mobile-overlay');
            const hamburger = document.getElementById('hamburgerIcon');
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
            if (hamburger) hamburger.classList.remove('active');
            document.body.style.overflow = '';
        }

        // ESC key to close mobile menu
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const sidebar = document.getElementById('leftSidebar');
                if (sidebar && sidebar.classList.contains('active')) {
                    closeMobileMenu();
                }
            }
        });

        // Close mobile menu when clicking on nav links (SPA friendly)
        document.addEventListener('click', (e) => {
            const link = e.target.closest('#leftSidebar a[href]');
            if (link && window.innerWidth < 1024) {
                closeMobileMenu();
            }
        });

        // Close mobile menu on resize to desktop (fixes overlay stuck bug)
        let resizeTimeout;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => {
                if (window.innerWidth >= 1024) {
                    closeMobileMenu();
                }
            }, 100);
        });


        // 🌍 CONTEXT MENU TYPE TÜRKÇELEŞTIRME
        window.getContextTypeLabel = function(type) {
            const labels = {
                'song': 'Şarkı',
                'album': 'Albüm',
                'playlist': 'Playlist',
                'my-playlist': 'Playlistim',
                'genre': 'Tür',
                'sector': 'Sektör',
                'radio': 'Radyo',
                'artist': 'Sanatçı'
            };
            return labels[type] || type;
        };

        // 🔐 SAĞ TUŞ KORUMASI - Sadece root kullanıcı boşluk alanlarda sağ tuşa ulaşabilir
        document.addEventListener('contextmenu', (e) => {
            // Root kullanıcı mı kontrol et
            const isRoot = window.muzibuPlayerConfig?.currentUser?.is_root ?? false;

            // Root kullanıcı ise → Her yerde sağ tuş açık
            if (isRoot) {
                return;
            }

            // Root değil → Context menu'ye sahip elementleri kontrol et
            const allowedSelectors = [
                '.song-card',
                '.album-card',
                '.playlist-card',
                '.artist-card',
                '.genre-card',
                '.sector-card',
                '.radio-card',
                '.song-row',
                '.song-list-item',
                '.song-detail-row',
                '.song-simple-row',
                '.song-history-row',
                '.my-playlist-card',
                '.playlist-quick-card',
                '.genre-quick-card'
            ];

            // Tıklanan element veya parent'ları allowed mi?
            let element = e.target;
            let isAllowed = false;

            // Parent'lara doğru tara (max 5 level)
            for (let i = 0; i < 5 && element && element !== document.body; i++) {
                if (allowedSelectors.some(selector => element.matches?.(selector))) {
                    isAllowed = true;
                    break;
                }
                element = element.parentElement;
            }

            // İzinli değilse → Sağ tuşu engelle
            if (!isAllowed) {
                e.preventDefault();
            }
        }, true); // Capture phase - önce bu çalışır
    </script>

    {{-- 🎯 Livewire Navigation Hook - Alpine Re-Init --}}
    <script>
        // ✅ FIX: Prevent Alpine.js multiple initialization ($nextTick redefine error)
        document.addEventListener('livewire:navigated', () => {
            if (!window.Alpine) return;

            // 🎯 Use Alpine's built-in mutateDom to safely initialize new components
            // This prevents magic property redefinition errors
            setTimeout(() => {
                window.Alpine.mutateDom(() => {
                    // Only initialize uninitialized elements
                    document.querySelectorAll('[x-data]:not([data-alpine-initialized])').forEach(el => {
                        try {
                            window.Alpine.initTree(el);
                            el.setAttribute('data-alpine-initialized', 'true');
                        } catch (e) {
                            // Silently ignore already initialized elements
                            if (!e.message?.includes('redefine') && !e.message?.includes('already')) {
                                console.warn('Alpine init warning:', e.message);
                            }
                        }
                    });
                });
            }, 50);
        });

        // 🎵 Player Store Registration - ULTIMATE FIX (Proxy Pattern - auto-forward everything)
        // Strategy: Use JavaScript Proxy to auto-forward ALL properties/methods to root $data
        document.addEventListener('alpine:initialized', () => {
            const htmlEl = document.querySelector('html');

            // Wait for Alpine to fully initialize the root component
            setTimeout(() => {
                const getRootData = () => {
                    // Try multiple ways to get root data
                    if (htmlEl._x_dataStack && htmlEl._x_dataStack[0]) {
                        return htmlEl._x_dataStack[0];
                    }
                    // Fallback: Alpine might expose it differently
                    return window.Alpine?.$data?.(htmlEl);
                };

                // Create Proxy that forwards everything to root
                const playerProxy = new Proxy({}, {
                    get(target, prop) {
                        const rootData = getRootData();
                        if (!rootData) {
                            console.error('❌ Root data not accessible, prop:', prop);
                            return undefined;
                        }

                        const value = rootData[prop];

                        // Debug log for missing methods
                        if (value === undefined && (prop === 'playPlaylist' || prop === 'playAlbum' || prop === 'playGenre')) {
                            console.error(`❌ Method ${prop} not found in root data. Available methods:`, Object.keys(rootData).filter(k => typeof rootData[k] === 'function'));
                        }

                        // If it's a function, bind it to root context
                        if (typeof value === 'function') {
                            return value.bind(rootData);
                        }

                        return value;
                    },
                    set(target, prop, value) {
                        const rootData = getRootData();
                        if (rootData) {
                            rootData[prop] = value;
                            return true;
                        }
                        return false;
                    }
                });

                window.Alpine.store('player', playerProxy);
            }, 100); // Small delay to ensure Alpine root is ready
        });

        // Context menu store kontrolü
        document.addEventListener('alpine:initialized', () => {
            if (!window.Alpine.store('contextMenu')) {
                console.error('❌ Context Menu Store not found!');
            }

            // Player store da kontrol et
            if (!window.Alpine.store('player')) {
                console.error('❌ Player Store not found!');
            }
        });
    </script>

    @once
    {{-- 🎯 Context Menu Init - SPA Safe --}}
    <script src="{{ versioned_asset('themes/muzibu/js/context-menu/init.js') }}"></script>
    @endonce

    @once
    {{-- 🎯 Alpine helper: horizontalScroll (SPA route changes için global) --}}
    <script>
        document.addEventListener('alpine:init', () => {
            if (!Alpine.data('horizontalScroll')) {
                Alpine.data('horizontalScroll', () => ({
                    scrollContainer: null,
                    scrollInterval: null,
                    init() {
                        this.scrollContainer = this.$refs.scrollContainer;
                    },
                    scrollLeft() {
                        this.scrollContainer?.scrollBy({ left: -400, behavior: 'smooth' });
                    },
                    scrollRight() {
                        this.scrollContainer?.scrollBy({ left: 400, behavior: 'smooth' });
                    },
                    startAutoScroll(direction) {
                        this.scrollInterval = setInterval(() => {
                            this.scrollContainer?.scrollBy({ left: direction === 'right' ? 20 : -20 });
                        }, 50);
                    },
                    stopAutoScroll() {
                        if (this.scrollInterval) {
                            clearInterval(this.scrollInterval);
                            this.scrollInterval = null;
                        }
                    }
                }));
            }
        });
    </script>
    @endonce

    {{-- PWA Service Worker Registration --}}
    <x-pwa-registration />

    {{-- Create Playlist Modal (Global - SPA Compatible) --}}
    @include('themes.muzibu.components.create-playlist-modal')

    {{-- Create Playlist Modal Alpine.js Component (SPA Safe) --}}
    <script>
    document.addEventListener('alpine:init', () => {
        if (!Alpine.data('createPlaylistModal')) {
            Alpine.data('createPlaylistModal', () => ({
                open: false,
                loading: false,
                title: '',
                description: '',
                isPublic: true,

                openModal() {
                    this.open = true;
                    this.title = '';
                    this.description = '';
                    this.isPublic = true;
                },

                closeModal() {
                    this.open = false;
                },

                async createPlaylist() {
                    if (!this.title.trim()) return;

                    this.loading = true;

                    try {
                        const response = await fetch('/api/muzibu/playlists/quick-create', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                title: this.title,
                                description: this.description,
                                is_public: this.isPublic,
                                song_ids: []
                            })
                        });

                        const data = await response.json();

                        if (data.success || data.playlist) {
                            const newPlaylist = data.playlist || data.data;

                            if (window.$store?.toast) {
                                window.$store.toast.show('Playlist oluşturuldu!', 'success');
                            }
                            this.closeModal();

                            // 🎯 SPA: Dispatch event for playlist-created
                            window.dispatchEvent(new CustomEvent('playlist-created', {
                                detail: { playlist: newPlaylist }
                            }));

                            // 🎯 Check if playlistModal had pending context (song/album adding flow)
                            const pendingContext = window._playlistModalPendingContext;
                            if (pendingContext && pendingContext.contentType) {
                                // Clear pending context
                                window._playlistModalPendingContext = null;

                                // Reopen playlistModal with the same content
                                const playlistModal = Alpine.store('playlistModal');
                                if (playlistModal) {
                                    setTimeout(() => {
                                        if (pendingContext.contentType === 'song') {
                                            playlistModal.showForSong(pendingContext.contentId, pendingContext.contentData);
                                        } else if (pendingContext.contentType === 'album') {
                                            playlistModal.showForAlbum(pendingContext.contentId, pendingContext.contentData);
                                        }
                                    }, 300);
                                }
                            } else {
                                // No pending context - check if on my-playlists page
                                const currentPath = window.location.pathname;
                                if (currentPath.includes('my-playlists')) {
                                    setTimeout(() => window.location.reload(), 500);
                                }
                            }
                        } else {
                            throw new Error(data.message || 'Bir hata oluştu');
                        }
                    } catch (error) {
                        if (window.$store?.toast) {
                            window.$store.toast.show(error.message || 'Bir hata oluştu', 'error');
                        } else {
                            alert(error.message || 'Bir hata oluştu');
                        }
                    } finally {
                        this.loading = false;
                    }
                }
            }));
        }
    });
    </script>

    @yield('scripts')
    @stack('scripts')
</body>
</html>
@endif
