<!DOCTYPE html>
@php
    $currentLocale = app()->getLocale();
    $isRtl = in_array($currentLocale, ['ar', 'he', 'fa', 'ur']) ? 'rtl' : 'ltr';
@endphp
<html lang="{{ $currentLocale }}"
      dir="{{ $isRtl }}"
      style="background-color:#ffffff"
      x-data="{
        darkMode: localStorage.getItem('darkMode') || 'auto',
        get effectiveMode() {
            if (this.darkMode === 'auto') {
                const hour = new Date().getHours();
                return (hour >= 6 && hour < 18) ? 'light' : 'dark';
            }
            return this.darkMode;
        }
      }"
      x-init="
        $watch('darkMode', val => {
            localStorage.setItem('darkMode', val);
            document.documentElement.setAttribute('data-theme-mode', val);
        });
        setInterval(() => { if (darkMode === 'auto') { $el.classList.toggle('dark', effectiveMode === 'dark'); } }, 60000);
      "
      :class="{ 'dark': effectiveMode === 'dark' }">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- ⚡ CRITICAL: Theme Flash Fix - MUST BE FIRST! Prevents white flash --}}
    <script>
    (function(){
        const mode = localStorage.getItem('darkMode') || 'auto';
        const html = document.documentElement;

        // Set theme mode attribute for icon visibility
        html.setAttribute('data-theme-mode', mode);

        // Determine if dark mode should be active
        let isDark = false;
        if (mode === 'dark') {
            isDark = true;
        } else if (mode === 'auto') {
            const hour = new Date().getHours();
            isDark = (hour < 6 || hour >= 18);
        }

        // Apply dark class and inline background color INSTANTLY
        if (isDark) {
            html.classList.add('dark');
            html.style.backgroundColor = '#111827'; // gray-900 (Tailwind dark mode bg)
        } else {
            html.style.backgroundColor = '#ffffff'; // white
        }
    })();
    </script>

    {{-- 🔇 CONSOLE FILTER - Suppress tracking/marketing noise --}}
    <script>
    (function(){const p=[/yandex/i,/attestation/i,/topics/i,/googletagmanager/i,/facebook/i,/ERR_BLOCKED_BY_CLIENT/i];const s=m=>!m?false:p.some(x=>x.test(m));const e=console.error;console.error=function(){const m=Array.from(arguments).join(' ');if(!s(m))e.apply(console,arguments);};const w=console.warn;console.warn=function(){const m=Array.from(arguments).join(' ');if(!s(m))w.apply(console,arguments);};const l=console.log;console.log=function(){const m=Array.from(arguments).join(' ');if(!s(m))l.apply(console,arguments);};})();
    </script>

    {{-- ✅ Alpine.js is included in Livewire - DO NOT load separately to avoid conflicts --}}

{{-- Global SEO Meta Tags - Tek Satır --}}
<x-seo-meta />

{{-- Favicon - Tenant-aware dynamic route --}}
    <link rel="icon" type="image/x-icon" href="/favicon.ico">

    {{-- PWA Manifest (2025 Best Practice) --}}
    <link rel="manifest" href="{{ route('manifest') }}">

    {{-- Apple Touch Icon (iOS/Safari) - Uses favicon as fallback --}}
    <link rel="apple-touch-icon" href="/favicon.ico">

    {{-- Theme Color for Mobile Browser Bar (Tenant-aware) --}}
    @php
        $themeColor = setting('site_theme_color') ?: '#000000';
        $themeColorLight = setting('site_theme_color_light') ?: '#ffffff';
        $themeColorDark = setting('site_theme_color_dark') ?: '#1a202c';
    @endphp
    <meta name="theme-color" content="{{ $themeColor }}">
    <meta name="theme-color" media="(prefers-color-scheme: light)" content="{{ $themeColorLight }}">
    <meta name="theme-color" media="(prefers-color-scheme: dark)" content="{{ $themeColorDark }}">

    {{-- Performance: DNS Prefetch & Preconnect --}}
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="//www.googletagmanager.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>

    {{-- Performance: Preload Critical Fonts (FontAwesome) --}}
    <link rel="preload" href="{{ asset('assets/libs/fontawesome-pro@7.1.0/webfonts/fa-solid-900.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('assets/libs/fontawesome-pro@7.1.0/webfonts/fa-light-300.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('assets/libs/fontawesome-pro@7.1.0/webfonts/fa-regular-400.woff2') }}" as="font" type="font/woff2" crossorigin>

    {{-- Performance: Preload Critical CSS --}}
    {{-- Tailwind CSS - Tenant-Aware (tenant-2.css veya fallback app.css) --}}
    <link rel="stylesheet" href="{{ tenant_css() }}" media="all">

    {{-- Font Awesome Pro - ⚠️ DO NOT REMOVE - Defer for performance --}}
    <link rel="stylesheet" href="{{ asset('assets/libs/fontawesome-pro@7.1.0/css/all.css') }}" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="{{ asset('assets/libs/fontawesome-pro@7.1.0/css/all.css') }}"></noscript>

    {{-- Google Fonts - Roboto All Weights - ⚠️ DO NOT REMOVE - display=swap prevents FOIT --}}
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;200;300;400;500;600;700;800;900&display=swap">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet"></noscript>

    {{-- iXtif Theme Styles - Bundle if available, fallback to individual files --}}
    {{-- Deferred loading with preload for non-critical CSS --}}
    @if(file_exists(public_path('css/ixtif-bundle.min.css')))
        {{-- Performance Optimized: 4 CSS files bundled --}}
        <link rel="preload" href="{{ asset('css/ixtif-bundle.min.css') }}" as="style">
        <link rel="stylesheet" href="{{ asset('css/ixtif-bundle.min.css') }}" media="print" onload="this.media='all'">
        <noscript><link rel="stylesheet" href="{{ asset('css/ixtif-bundle.min.css') }}"></noscript>
    @else
        {{-- Fallback: Individual CSS files with defer --}}
        <link rel="stylesheet" href="{{ asset('css/ixtif-theme.css') }}?v={{ now()->timestamp }}" media="print" onload="this.media='all'">
        <link rel="stylesheet" href="{{ asset('css/custom-gradients.css') }}?v=8.0.1" media="print" onload="this.media='all'">
        <link rel="stylesheet" href="{{ asset('css/core-system.css') }}?v=1.0.1" media="print" onload="this.media='all'">
        <noscript>
            <link rel="stylesheet" href="{{ asset('css/ixtif-theme.css') }}">
            <link rel="stylesheet" href="{{ asset('css/custom-gradients.css') }}">
            <link rel="stylesheet" href="{{ asset('css/core-system.css') }}">
        </noscript>
    @endif

    {{-- Back to Top Button Styles - Non-critical, defer --}}
    <link rel="stylesheet" href="{{ asset('css/back-to-top.css') }}" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="{{ asset('css/back-to-top.css') }}"></noscript>

    {{-- Livewire Styles --}}
    @livewireStyles

    {{-- ❌ REMOVED: Duplicate console filter - Now at top of <head> --}}
    {{-- ❌ REMOVED: Google Analytics & Yandex - Now auto-loaded via marketing.auto-platforms component --}}

    {{-- Dynamic Content Areas --}}
    {{-- 🤖 Universal Schema Auto-Render (Dynamic for ALL modules) --}}
    {{-- SKIP if Controller already shared metaTags with schemas (prevents duplicates) --}}
    @php
        $sharedMetaTags = view()->getShared()['metaTags'] ?? null;
        $hasControllerSchemas = $sharedMetaTags && isset($sharedMetaTags['schemas']) && !empty($sharedMetaTags['schemas']);
    @endphp
    @if(!$hasControllerSchemas && isset($item) && is_object($item) && method_exists($item, 'getUniversalSchemas'))
        {!! \App\Services\SEOService::getAllSchemas($item) !!}
    @endif
    @stack('head')
    @stack('styles')

    {{-- Header Cache Buttons & Icon Visibility Style --}}
    <style>
        /* Cache buttons loading animation */
        button[onclick*="clearSystemCache"] .loading-spinner,
        button[onclick*="clearAIConversation"] .loading-spinner {
            display: none;
        }
        button[onclick*="clearSystemCache"].loading .loading-icon,
        button[onclick*="clearAIConversation"].loading .loading-icon {
            display: none;
        }
        button[onclick*="clearSystemCache"].loading .loading-spinner,
        button[onclick*="clearAIConversation"].loading .loading-spinner {
            display: inline-block !important;
        }

        /* Theme mode icon visibility - Initial state (before Alpine.js loads) */
        .theme-icon-light,
        .theme-icon-dark,
        .theme-icon-auto {
            display: none;
        }

        /* Default: show auto icon (most common case) */
        .theme-icon-auto {
            display: inline-block;
        }

        /* JS will set html attribute instantly */
        html[data-theme-mode="light"] .theme-icon-light { display: inline-block; }
        html[data-theme-mode="light"] .theme-icon-dark,
        html[data-theme-mode="light"] .theme-icon-auto { display: none; }

        html[data-theme-mode="dark"] .theme-icon-dark { display: inline-block; }
        html[data-theme-mode="dark"] .theme-icon-light,
        html[data-theme-mode="dark"] .theme-icon-auto { display: none; }

        html[data-theme-mode="auto"] .theme-icon-auto { display: inline-block; }
        html[data-theme-mode="auto"] .theme-icon-light,
        html[data-theme-mode="auto"] .theme-icon-dark { display: none; }

        /* Search icon visibility (will be controlled by Alpine.js) */
        .search-icon-default {
            display: inline-block;
        }
        .search-icon-close {
            display: none;
        }

        /* Skeleton Shimmer Animation - Global */
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        .skeleton-shimmer {
            animation: shimmer 1.5s infinite;
        }
    </style>

    {{-- AI Chat CSS - Load in head for styling --}}
    <link rel="stylesheet" href="/assets/css/ai-chat.css?v=<?php echo time(); ?>">
    {{-- AI Chat JS moved to footer.blade.php AFTER Alpine.js/Livewire --}}

    {{-- 🎯 Marketing Platforms Auto-Loader (GTM, GA4, Facebook, Yandex, LinkedIn, TikTok, Clarity) --}}
    <x-marketing.auto-platforms />
</head>

<body class="font-sans antialiased min-h-screen bg-white dark:bg-gray-900 transition-all duration-500 flex flex-col"
      :class="{ 'dark-mode-active': darkMode === 'dark' }"
      data-instant-allow-query-string
      data-instant-intensity="65"
      data-instant-mousedown-only>

    {{-- 🎯 GTM Body Snippet (No-Script Fallback) --}}
    <x-marketing.gtm-body />

    <header id="main-header" x-data="{
        sidebarOpen: false,
        mobileMenuOpen: false,
        expandedCategory: null,
        activeMegaMenu: null,
        searchOpen: false,
        activeCategory: 'first',
        scrolled: false,
        init() {
            // Scroll listener for hiding topbar
            window.addEventListener('scroll', () => {
                this.scrolled = window.scrollY > 10;
            });

            // Search icon toggle
            this.$watch('searchOpen', value => {
                const defaultIcon = document.querySelector('.search-icon-default');
                const closeIcon = document.querySelector('.search-icon-close');
                if (defaultIcon && closeIcon) {
                    if (value) {
                        defaultIcon.classList.add('hidden');
                        closeIcon.classList.remove('hidden');
                    } else {
                        defaultIcon.classList.remove('hidden');
                        closeIcon.classList.add('hidden');
                    }
                }
            });
        }
    }"
    class="sticky top-0 left-0 right-0 z-50 transition-all duration-300"
    :class="{ 'header-scrolled': scrolled }"
    @search-toggle.window="searchOpen = $event.detail; if (!searchOpen) { activeMegaMenu = null; }"
    @keydown.escape.window="searchOpen = false; activeMegaMenu = null; mobileMenuOpen = false"
    @close-megamenu.window="activeMegaMenu = null"
    @close-other-menus.window="searchOpen = false; mobileMenuOpen = false; activeMegaMenu = null">

        {{-- Top Info Bar - CSS ile yukarı kayacak --}}
        <div id="top-bar"
             class="overflow-hidden bg-gray-100 dark:bg-[#0a0f1a]"
             @mouseenter="activeMegaMenu = null">
            <div class="container mx-auto px-4 sm:px-4 md:px-2">
                <div class="flex items-center justify-between text-sm py-3">
                    <div class="flex items-center gap-4 sm:gap-6 text-gray-700 dark:text-gray-300">
                        @php
                            $contactPhone = setting('contact_phone_1');
                            $contactWhatsapp = setting('contact_whatsapp_1');
                        @endphp

                        {{-- Telefon (Tıklanabilir) --}}
                        @if($contactPhone)
                            <a href="tel:{{ str_replace(' ', '', $contactPhone) }}" class="hover:text-blue-600 dark:hover:text-white transition flex items-center gap-2 text-sm font-medium">
                                <i class="fa-solid fa-phone"></i>
                                <span>{{ $contactPhone }}</span>
                            </a>
                        @endif

                        {{-- WhatsApp (Tıklanabilir) --}}
                        @if($contactWhatsapp)
                            <a href="{{ whatsapp_link() }}" target="_blank" class="hover:text-green-600 dark:hover:text-green-400 transition flex items-center gap-2 text-sm font-medium">
                                <i class="fa-brands fa-whatsapp text-base"></i>
                                <span>{{ $contactWhatsapp }}</span>
                            </a>
                        @endif
                    </div>
                    <div class="flex items-center gap-3 sm:gap-4">
                        {{-- İletişim - xs/sm'de gizli, md+'da görünür --}}
                        <a href="{{ href('Page', 'show', 'iletisim') }}" class="hidden md:inline-block text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-white transition text-xs sm:text-sm font-medium">
                            <i class="fa-solid fa-envelope mr-1"></i>
                            İletişim
                        </a>

                        {{-- Site Dil Değiştirici --}}
                        @php
                            use App\Helpers\CanonicalHelper;

                            use Illuminate\Database\Eloquent\Model as EloquentModel;

                            $currentModel = isset($item) ? $item : null;
                            $moduleAction = 'show';
                            if (isset($items) && !isset($item)) $moduleAction = 'index';
                            if (isset($category)) { $currentModel = $category; $moduleAction = 'category'; }

                            if ($currentModel && !($currentModel instanceof EloquentModel)) {
                                // Only pass Eloquent models into CanonicalHelper
                                $currentModel = null;
                            }

                            $currentLang = app()->getLocale();

                            $isAuthPage = request()->is('login') || request()->is('register') ||
                                         request()->is('logout') || request()->is('password/*') ||
                                         request()->is('forgot-password') || request()->is('reset-password');

                            if ($isAuthPage) {
                                $languageSwitcherLinks = [];
                                $activeLanguages = \App\Services\TenantLanguageProvider::getActiveLanguages();
                                $defaultLocale = get_tenant_default_locale();
                                $currentPath = ltrim(request()->path(), '/');

                                foreach ($activeLanguages as $lang) {
                                    $targetUrl = $lang['code'] === $defaultLocale
                                        ? url('/' . $currentPath)
                                        : url('/' . $lang['code'] . '/' . $currentPath);

                                    $url = route('language.switch', ['locale' => $lang['code']]) . '?return=' . urlencode($targetUrl);

                                    $languageSwitcherLinks[$lang['code']] = [
                                        'url' => $url,
                                        'name' => $lang['native_name'] ?? $lang['name'],
                                        'active' => $lang['code'] === $currentLang
                                    ];
                                }
                            } else {
                                $languageSwitcherLinks = CanonicalHelper::getLanguageSwitcherLinks($currentModel ?? null, $moduleAction);
                            }
                        @endphp

                        @if(count($languageSwitcherLinks) > 1)
                        <div class="language-switcher-header relative" x-data="{ open: false }">
                            @php
                                $currentLangData = null;
                                try {
                                    $langModel = \Modules\LanguageManagement\app\Models\TenantLanguage::where('code', $currentLang)
                                        ->where('is_active', 1)
                                        ->first();
                                    if ($langModel) {
                                        $currentLangData = [
                                            'flag' => $langModel->flag_icon ?? '🌐',
                                            'name' => $langModel->native_name ?? $langModel->name ?? strtoupper($currentLang)
                                        ];
                                    }
                                } catch (\Exception $e) {}

                                if (!$currentLangData) {
                                    $currentLangData = [
                                        'flag' => '🌐',
                                        'name' => strtoupper($currentLang)
                                    ];
                                }
                            @endphp

                            <button @click="open = !open"
                                    class="flex items-center gap-1 text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-white transition text-xs sm:text-sm">
                                <span class="text-base">{{ $currentLangData['flag'] }}</span>
                                <span class="hidden sm:inline">{{ $currentLangData['name'] }}</span>
                            </button>

                            <div x-show="open"
                                 @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="dropdown-content absolute top-full mt-2 w-44 bg-white dark:bg-slate-900 rounded-lg border border-gray-200 dark:border-gray-700 py-1 z-20"
                                 x-cloak>

                                @if(count($languageSwitcherLinks) > 0)
                                    @php
                                        $languageData = [];
                                        try {
                                            $langs = \Modules\LanguageManagement\app\Models\TenantLanguage::where('is_active', 1)
                                                ->get()
                                                ->keyBy('code');
                                            foreach ($langs as $code => $lang) {
                                                $languageData[$code] = [
                                                    'flag' => $lang->flag_icon ?? '🌐',
                                                    'name' => $lang->native_name ?? $lang->name ?? strtoupper($code)
                                                ];
                                            }
                                        } catch (\Exception $e) {}
                                    @endphp

                                    @foreach($languageSwitcherLinks as $locale => $link)
                                        <a href="{{ $link['url'] }}"
                                           class="w-full flex items-center px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 border-b border-gray-200 dark:border-gray-700 last:border-b-0 {{ $link['active'] ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : '' }}">
                                            <span class="mr-2 text-base">{{ $languageData[$locale]['flag'] ?? '🌐' }}</span>
                                            <span class="flex-1 text-left">{{ $languageData[$locale]['name'] ?? $link['name'] }}</span>
                                            @if($link['active'])
                                                <i class="fa-solid fa-check text-xs"></i>
                                            @endif
                                        </a>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Menu Bar - Sticky olarak kalacak --}}
        <nav id="main-nav" class="relative bg-white/95 dark:bg-slate-900/90 backdrop-blur-lg">
            <div class="container mx-auto px-4 sm:px-4 md:px-2">
                <div id="nav-container" class="flex items-center justify-between transition-all duration-300"
                     :class="scrolled ? 'min-h-[80px]' : ''">
                    {{-- Logo - Sabit Genişlik Container --}}
                    <div class="flex items-center gap-3 transition-all duration-300"
                         :class="scrolled ? 'py-3' : 'py-4'"
                         style="width: 200px;"
                         @mouseenter="activeMegaMenu = null">
                        <a href="{{ url('/') }}" class="flex items-center gap-3 justify-start w-full">
                            @php
                                // LogoService kullan - daha temiz ve bakımı kolay
                                $logoService = app(\App\Services\LogoService::class);
                                $logos = $logoService->getLogos();

                                $logoUrl = $logos['light_logo_url'] ?? null;
                                $logoDarkUrl = $logos['dark_logo_url'] ?? null;
                                $fallbackMode = $logos['fallback_mode'] ?? 'title_only';
                                $siteTitle = $logos['site_title'] ?? setting('site_title');
                                $siteSlogan = setting('site_slogan');
                            @endphp
                            @if($fallbackMode === 'both')
                                {{-- Her iki logo da var - Dark mode'da otomatik değiş --}}
                                <img src="{{ $logoUrl }}"
                                     alt="{{ $siteTitle }}"
                                     class="dark:hidden object-contain h-10 w-auto"
                                     title="{{ $siteTitle }}"
                                     width="120"
                                     height="40">
                                <img src="{{ $logoDarkUrl }}"
                                     alt="{{ $siteTitle }}"
                                     class="hidden dark:block object-contain h-10 w-auto"
                                     title="{{ $siteTitle }}"
                                     width="120"
                                     height="40">
                            @elseif($fallbackMode === 'light_only' || $logoUrl)
                                {{-- Sadece light logo var - Dark mode'da CSS ile beyaz yap --}}
                                <img src="{{ $logoUrl }}"
                                     alt="{{ $siteTitle }}"
                                     class="block object-contain h-10 w-auto logo-adaptive"
                                     title="{{ $siteTitle }}"
                                     width="120"
                                     height="40">
                            @elseif($fallbackMode === 'dark_only' || $logoDarkUrl)
                                {{-- Sadece dark logo var - Her modda göster --}}
                                <img src="{{ $logoDarkUrl }}"
                                     alt="{{ $siteTitle }}"
                                     class="block object-contain h-10 w-auto"
                                     title="{{ $siteTitle }}"
                                     width="120"
                                     height="40">
                            @else
                                {{-- Logo yok - Site title text göster --}}
                                <div class="flex items-center gap-2">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 via-purple-600 to-pink-500 rounded-xl flex items-center justify-center">
                                        <i class="fa-solid fa-forklift text-white text-xl"></i>
                                    </div>
                                    <div>
                                        <span class="text-xl font-black text-gray-900 dark:text-white relative inline-block">
                                            <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-purple-600 dark:from-blue-400 dark:to-purple-400">{{ $siteTitle }}</span>
                                        </span>
                                        <p class="text-[10px] text-gray-500 dark:text-gray-400 font-semibold">{{ $siteSlogan }}</p>
                                    </div>
                                </div>
                            @endif
                        </a>
                    </div>

                    {{-- Main Navigation (Desktop) --}}
                    <div class="hidden lg:flex items-center gap-6">
                        {{-- Ürünler (Mega Menu + Tabs) --}}
                        <div class="relative mega-menu-item py-2"
                             @mouseenter="activeMegaMenu = 'products'">
                            <a href="{{ route('shop.index') }}"
                               class="flex items-center gap-2 font-semibold transition group py-4 text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400"
                               :class="activeMegaMenu === 'products' ? 'text-blue-600 dark:text-blue-400' : ''">
                                <i :class="activeMegaMenu === 'products' ? 'fa-solid' : 'fa-light'" class="fa-box-open transition-all duration-300"></i>
                                <span>Ürünler</span>
                                <i class="fa-solid fa-chevron-down text-xs transition-transform"
                                   :class="{ 'rotate-180': activeMegaMenu === 'products' }"></i>
                            </a>

                        </div>

                        {{-- Hizmetler (Ortalı Dropdown) --}}
                        <div class="relative mega-menu-item py-2"
                             @mouseenter="activeMegaMenu = 'hizmetler'"
                             @mouseleave="activeMegaMenu = null">
                            <a href="/hizmetler"
                               class="flex items-center gap-2 font-semibold transition py-4 text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400"
                               :class="activeMegaMenu === 'hizmetler' ? 'text-blue-600 dark:text-blue-400' : ''">
                                <i :class="activeMegaMenu === 'hizmetler' ? 'fa-solid' : 'fa-light'" class="fa-screwdriver-wrench transition-all duration-300"></i>
                                <span>Hizmetler</span>
                                <i class="fa-solid fa-chevron-down text-xs transition-transform"
                                   :class="{ 'rotate-180': activeMegaMenu === 'hizmetler' }"></i>
                            </a>

                            {{-- Basit Dropdown (Temiz ve Kompakt) --}}
                            <div x-show="activeMegaMenu === 'hizmetler'"
                                 @mouseenter="activeMegaMenu = 'hizmetler'"
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 -translate-y-3"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-200"
                                 x-transition:leave-start="opacity-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 -translate-y-3"
                                 class="absolute top-full left-1/2 -translate-x-1/2 -mt-2 w-72 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-xl z-50 overflow-hidden"
                                 x-cloak>
                                <ul class="py-2">
                                    <li>
                                        <a href="/satin-alma"
                                           class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-orange-50 dark:hover:bg-orange-900/20 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">
                                            <i class="fa-solid fa-chevron-right text-xs"></i>
                                            <span>Satın Alma</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/kiralama"
                                           class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-orange-50 dark:hover:bg-orange-900/20 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">
                                            <i class="fa-solid fa-chevron-right text-xs"></i>
                                            <span>Kiralama</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/teknik-servis"
                                           class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-orange-50 dark:hover:bg-orange-900/20 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">
                                            <i class="fa-solid fa-chevron-right text-xs"></i>
                                            <span>Teknik Servis</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/yedek-parca"
                                           class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-orange-50 dark:hover:bg-orange-900/20 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">
                                            <i class="fa-solid fa-chevron-right text-xs"></i>
                                            <span>Yedek Parça</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/bakim-anlasmalari"
                                           class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-orange-50 dark:hover:bg-orange-900/20 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">
                                            <i class="fa-solid fa-chevron-right text-xs"></i>
                                            <span>Bakım Anlaşmaları</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/ikinci-el"
                                           class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-orange-50 dark:hover:bg-orange-900/20 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">
                                            <i class="fa-solid fa-chevron-right text-xs"></i>
                                            <span>İkinci El</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        {{-- Kurumsal (Hibrit Mega Menu) --}}
                        <div class="relative mega-menu-item py-2"
                             @mouseenter="activeMegaMenu = 'hakkimizda'">
                            <button class="flex items-center gap-2 font-semibold transition group py-4 text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400"
                                    :class="activeMegaMenu === 'hakkimizda' ? 'text-blue-600 dark:text-blue-400' : ''">
                                <i :class="activeMegaMenu === 'hakkimizda' ? 'fa-solid' : 'fa-light'" class="fa-building transition-all duration-300"></i>
                                <span>Kurumsal</span>
                                <i class="fa-solid fa-chevron-down text-xs transition-transform"
                                   :class="{ 'rotate-180': activeMegaMenu === 'hakkimizda' }"></i>
                            </button>

                        </div>

                        {{-- İletişim --}}
                        <div class="py-2"
                             x-data="{ hovering: false }"
                             @mouseenter="activeMegaMenu = null; hovering = true"
                             @mouseleave="hovering = false">
                            <a href="/iletisim" class="flex items-center gap-2 text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-semibold transition py-4">
                                <i :class="hovering ? 'fa-solid' : 'fa-light'" class="fa-envelope transition-all duration-300"></i>
                                <span>İletişim</span>
                            </a>
                        </div>

                        @php
                            $currentLocale = app()->getLocale();
                            $headerMenu = getDefaultMenu($currentLocale);
                        @endphp

                        {{-- Diğer Dinamik Menüler (varsa) --}}
                        @if($headerMenu && !empty($headerMenu['items']))
                            @foreach($headerMenu['items'] as $menuItem)
                                @if(!empty($menuItem['children']))
                                    <div class="relative dropdown-menu" x-data="{ open: false }">
                                        <button @click="open = !open"
                                                class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-semibold transition flex items-center gap-1 {{ $menuItem['has_active_child'] ? 'text-blue-600 dark:text-blue-400' : '' }}">
                                            @if($menuItem['icon'])
                                                <i class="{{ $menuItem['icon'] }} mr-1"></i>
                                            @endif
                                            {{ $menuItem['title'] }}
                                            <i class="fa-solid fa-chevron-down text-xs transition-transform"
                                               :class="{ 'rotate-180': open }"></i>
                                        </button>

                                        <div x-show="open"
                                             @click.away="open = false"
                                             x-transition
                                             class="dropdown-content absolute top-full mt-2 w-48 bg-white dark:bg-slate-900 rounded-lg border border-gray-200 dark:border-gray-700 py-2 z-50"
                                             x-cloak>
                                            @foreach($menuItem['children'] as $child)
                                                <a href="{{ $child['url'] }}"
                                                   {{ $child['target'] === '_blank' ? 'target="_blank"' : '' }}
                                                   class="block px-4 py-2 text-sm border-b border-gray-200 dark:border-gray-700 last:border-b-0 {{ $child['is_active'] ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-medium' : 'text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400' }}">
                                                    @if($child['icon'])
                                                        <i class="{{ $child['icon'] }} mr-2"></i>
                                                    @endif
                                                    {{ $child['title'] }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <a href="{{ $menuItem['url'] }}"
                                       {{ $menuItem['target'] === '_blank' ? 'target="_blank"' : '' }}
                                       class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-semibold transition {{ $menuItem['is_active'] ? 'text-blue-600 dark:text-blue-400' : '' }}">
                                        @if($menuItem['icon'])
                                            <i class="{{ $menuItem['icon'] }} mr-2"></i>
                                        @endif
                                        {{ $menuItem['title'] }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    </div>

                    {{-- Right Actions --}}
                    <div class="flex items-center gap-2" @mouseenter="activeMegaMenu = null">
                        {{-- 🔐 ADMIN ONLY: Cache Clear Button (Icon Only) --}}
                        @auth
                            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('root'))
                                <div x-data="{ showTooltip: false }" class="relative">
                                    <button onclick="clearSystemCache(this)"
                                            @mouseenter="showTooltip = true"
                                            @mouseleave="showTooltip = false"
                                            aria-label="[ADMIN] Sistem Önbelleğini Temizle"
                                            class="w-10 h-10 rounded-full hover:bg-red-50 dark:hover:bg-red-900/20 flex items-center justify-center text-gray-700 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 transition">
                                        <i class="fa-light fa-trash-can text-lg loading-icon"></i>
                                        <i class="fa-solid fa-spinner fa-spin text-lg loading-spinner hidden"></i>
                                    </button>
                                    {{-- Tooltip --}}
                                    <div x-show="showTooltip"
                                         x-transition:enter="transition ease-out duration-300"
                                         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                         x-transition:leave="transition ease-in duration-150"
                                         x-transition:leave-start="opacity-100 scale-100"
                                         x-transition:leave-end="opacity-0 scale-95"
                                         class="absolute top-full left-1/2 -translate-x-1/2 mt-3 px-4 py-2.5 bg-gradient-to-br from-red-600/95 to-red-700/95 dark:from-red-500/95 dark:to-red-600/95 backdrop-blur-sm text-white text-xs font-semibold rounded-xl whitespace-nowrap pointer-events-none z-50 shadow-2xl border border-white/10"
                                         x-cloak>
                                        <span>Cache Temizle</span>
                                        {{-- Tooltip Arrow --}}
                                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-px">
                                            <div class="border-[5px] border-transparent border-b-red-600/95 dark:border-b-red-500/95"></div>
                                        </div>
                                    </div>
                                </div>

                                <div x-data="{ showTooltip: false }" class="relative">
                                    <button onclick="clearAIConversation(this)"
                                            @mouseenter="showTooltip = true"
                                            @mouseleave="showTooltip = false"
                                            aria-label="[ADMIN] AI Konuşma Geçmişini Temizle"
                                            class="w-10 h-10 rounded-full hover:bg-purple-50 dark:hover:bg-purple-900/20 flex items-center justify-center text-gray-700 dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400 transition">
                                        <i class="fa-light fa-comments text-lg loading-icon"></i>
                                        <i class="fa-solid fa-spinner fa-spin text-lg loading-spinner hidden"></i>
                                    </button>
                                    {{-- Tooltip --}}
                                    <div x-show="showTooltip"
                                         x-transition:enter="transition ease-out duration-300"
                                         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                         x-transition:leave="transition ease-in duration-150"
                                         x-transition:leave-start="opacity-100 scale-100"
                                         x-transition:leave-end="opacity-0 scale-95"
                                         class="absolute top-full left-1/2 -translate-x-1/2 mt-3 px-4 py-2.5 bg-gradient-to-br from-purple-600/95 to-purple-700/95 dark:from-purple-500/95 dark:to-purple-600/95 backdrop-blur-sm text-white text-xs font-semibold rounded-xl whitespace-nowrap pointer-events-none z-50 shadow-2xl border border-white/10"
                                         x-cloak>
                                        <span>AI Chat Temizle</span>
                                        {{-- Tooltip Arrow --}}
                                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-px">
                                            <div class="border-[5px] border-transparent border-b-purple-600/95 dark:border-b-purple-500/95"></div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endauth

                        {{-- Search Button with Tooltip --}}
                        <div x-data="{ showTooltip: false }" class="relative">
                            <button @click="searchOpen = !searchOpen; activeMegaMenu = null; mobileMenuOpen = false; $dispatch('close-user-menu'); $nextTick(() => { if (searchOpen) { setTimeout(() => { const input = document.getElementById('header-search-input'); if (input) input.focus(); }, 250); } })"
                                    @mouseenter="showTooltip = true"
                                    @mouseleave="showTooltip = false"
                                    aria-label="Arama menüsünü aç/kapat"
                                    :aria-expanded="searchOpen"
                                    class="w-10 h-10 rounded-full hover:bg-blue-50 dark:hover:bg-blue-900/20 flex items-center justify-center text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition">
                                <i class="fa-light fa-magnifying-glass text-lg transition-all duration-200 search-icon-default"></i>
                                <i class="fa-light fa-times text-lg transition-all duration-200 search-icon-close hidden"></i>
                            </button>
                            {{-- Tooltip --}}
                            <div x-show="showTooltip"
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute top-full left-1/2 -translate-x-1/2 mt-3 px-4 py-2.5 bg-gradient-to-br from-blue-600/95 to-blue-700/95 dark:from-blue-500/95 dark:to-blue-600/95 backdrop-blur-sm text-white text-xs font-semibold rounded-xl whitespace-nowrap pointer-events-none z-50 shadow-2xl border border-white/10"
                                 x-cloak>
                                <span x-text="searchOpen ? 'Aramayı Kapat' : 'Ara'"></span>
                                {{-- Tooltip Arrow --}}
                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-px">
                                    <div class="border-[5px] border-transparent border-b-blue-600/95 dark:border-b-blue-500/95"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Dark/Light/Auto Mode Toggle with Tooltip --}}
                        <div x-data="{ showTooltip: false }" class="relative">
                            <button @click="
                                    if (darkMode === 'light') darkMode = 'dark';
                                    else if (darkMode === 'dark') darkMode = 'auto';
                                    else darkMode = 'light';
                                "
                                    @mouseenter="showTooltip = true"
                                    @mouseleave="showTooltip = false"
                                    :aria-label="darkMode === 'light' ? 'Karanlık moda geç' : (darkMode === 'dark' ? 'Otomatik moda geç' : 'Aydınlık moda geç')"
                                    class="w-10 h-10 rounded-full hover:bg-purple-50 dark:hover:bg-purple-900/20 flex items-center justify-center text-gray-700 dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400 transition">
                                <i class="fa-light fa-moon-stars text-lg theme-icon-light"></i>
                                <i class="fa-light fa-sun text-lg theme-icon-dark"></i>
                                <i class="fa-light fa-eclipse text-lg theme-icon-auto"></i>
                            </button>
                            {{-- Tooltip --}}
                            <div x-show="showTooltip"
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute top-full left-1/2 -translate-x-1/2 mt-3 px-4 py-2.5 bg-gradient-to-br from-purple-600/95 to-purple-700/95 dark:from-purple-500/95 dark:to-purple-600/95 backdrop-blur-sm text-white text-xs font-semibold rounded-xl whitespace-nowrap pointer-events-none z-50 shadow-2xl border border-white/10"
                                 x-cloak>
                                <span x-text="darkMode === 'light' ? 'Aydınlık Mod' : (darkMode === 'dark' ? 'Karanlık Mod' : 'Otomatik Mod')"></span>
                                {{-- Tooltip Arrow --}}
                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-px">
                                    <div class="border-[5px] border-transparent border-b-purple-600/95 dark:border-b-purple-500/95"></div>
                                </div>
                            </div>
                        </div>

                        {{-- CART WIDGET --}}
                        @livewire('cart::front.cart-widget')

                        {{-- AUTH CONTROL VIA LIVEWIRE --}}
                        @livewire('auth.header-menu')

                        {{-- Mobile Menu Button --}}
                        <div x-data="{ showTooltip: false }" class="relative lg:hidden">
                            <button @click="mobileMenuOpen = !mobileMenuOpen; searchOpen = false; activeMegaMenu = null; $dispatch('close-user-menu')"
                                    @mouseenter="showTooltip = true"
                                    @mouseleave="showTooltip = false"
                                    aria-label="Mobil menüyü aç/kapat"
                                    :aria-expanded="mobileMenuOpen"
                                    class="w-10 h-10 rounded-lg bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 flex items-center justify-center text-white transition-all duration-300">
                                <i class="fa-solid text-lg transition-all duration-300"
                                   :class="mobileMenuOpen ? 'fa-times rotate-90' : 'fa-bars'"></i>
                            </button>
                            {{-- Tooltip --}}
                            <div x-show="showTooltip && !mobileMenuOpen"
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute top-full left-1/2 -translate-x-1/2 mt-3 px-4 py-2.5 bg-gradient-to-br from-orange-600/95 to-orange-700/95 dark:from-orange-500/95 dark:to-orange-600/95 backdrop-blur-sm text-white text-xs font-semibold rounded-xl whitespace-nowrap pointer-events-none z-50 shadow-2xl border border-white/10"
                                 x-cloak>
                                <span>Menü</span>
                                {{-- Tooltip Arrow --}}
                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-px">
                                    <div class="border-[5px] border-transparent border-b-orange-600/95 dark:border-b-orange-500/95"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Dropdown Wrapper - Contains both search and mega menu stacked --}}
            <div class="absolute left-0 right-0 top-full z-20" x-cloak>
                {{-- Search Bar --}}
                <div x-show="searchOpen"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-2"
                     @transitionend.once="if (searchOpen) { $nextTick(() => { const input = document.getElementById('header-search-input'); if (input) input.focus(); }); }"
                     class="relative z-30 bg-white dark:bg-slate-900 border-t border-gray-300 dark:border-white/20 shadow-lg">
                    <div class="container mx-auto px-4 sm:px-4 md:px-0 py-4">
                        {{-- Alpine.js + API Search (No Livewire overhead) --}}
                        <div class="relative"
                             x-data="{
                            query: '',
                            keywords: [],
                            products: [],
                            total: 0,
                            isOpen: false,
                            loading: false,
                            error: null,
                            highlightIndex: -1,
                            get hasResults() {
                                return this.keywords.length > 0 || this.products.length > 0;
                            },
                            get resultCount() {
                                return this.keywords.length + this.products.length;
                            },
                            resetSuggestions() {
                                this.keywords = [];
                                this.products = [];
                                this.total = 0;
                                this.highlightIndex = -1;
                            },
                            showEmptyState() {
                                return this.query.trim().length >= 2 && !this.loading && !this.hasResults && !this.error;
                            },
                            openDropdown() {
                                const hasContent = this.hasResults || this.showEmptyState() || !!this.error;
                                this.isOpen = hasContent;
                                if (!hasContent) {
                                    this.highlightIndex = -1;
                                }
                            },
                            async search() {
                                const trimmed = this.query.trim();
                                if (trimmed.length < 2) {
                                    this.resetSuggestions();
                                    this.isOpen = false;
                                    this.error = null;
                                    return;
                                }
                                this.loading = true;
                                this.error = null;
                                try {
                                    const response = await fetch(`/api/search/suggestions?q=${encodeURIComponent(trimmed)}`, {
                                        headers: {
                                            'Accept': 'application/json'
                                        }
                                    });

                                    if (!response.ok) {
                                        throw new Error(`HTTP ${response.status}`);
                                    }

                                    const data = await response.json();

                                    if (data.success && data.data) {
                                        this.keywords = data.data.keywords || [];
                                        this.products = data.data.products || [];
                                        this.total = data.data.total || 0;
                                        this.highlightIndex = -1;
                                    } else {
                                        this.resetSuggestions();
                                    }
                                } catch (e) {
                                    console.error('Suggestions error:', e);
                                    this.resetSuggestions();
                                    this.error = 'Öneriler getirilemedi.';
                                }
                                this.loading = false;
                                this.openDropdown();
                            },
                            goToSearch() {
                                const trimmed = this.query.trim();
                                if (trimmed.length >= 1) {
                                    window.location.href = `/search?q=${encodeURIComponent(trimmed)}`;
                                }
                            },
                            moveHighlight(step) {
                                if (!this.isOpen || this.resultCount === 0) {
                                    return;
                                }

                                let next = this.highlightIndex + step;

                                if (next < 0) {
                                    next = this.resultCount - 1;
                                } else if (next >= this.resultCount) {
                                    next = 0;
                                }

                                this.highlightIndex = next;
                            },
                            isHighlighted(index, type) {
                                const offset = type === 'product' ? this.keywords.length : 0;
                                return this.highlightIndex === index + offset;
                            },
                            setHighlight(index, type) {
                                const offset = type === 'product' ? this.keywords.length : 0;
                                this.highlightIndex = index + offset;
                            },
                            clearHighlight() {
                                this.highlightIndex = -1;
                            },
                            selectHighlighted() {
                                if (this.highlightIndex < 0) {
                                    this.goToSearch();
                                    return;
                                }

                                const combined = [
                                    ...this.keywords.map(keyword => ({ ...keyword, __type: 'keyword' })),
                                    ...this.products.map(product => ({ ...product, __type: 'product' })),
                                ];

                                const item = combined[this.highlightIndex];

                                if (!item) {
                                    return;
                                }

                                if (item.__type === 'keyword') {
                                    this.selectKeyword(item);
                                } else {
                                    this.selectProduct(item);
                                }
                            },
                            selectKeyword(keyword) {
                                if (!keyword?.text) {
                                    return;
                                }
                                this.query = keyword.text;
                                this.goToSearch();
                            },
                            selectProduct(product, index = 0) {
                                if (product?.url) {
                                    window.location.href = product.url;
                                }
                            },
                            handleFocus() {
                                this.$dispatch('search-toggle', true);
                                if (this.query.trim().length >= 2) {
                                    this.openDropdown();
                                }
                            },
                            closeDropdown() {
                                this.isOpen = false;
                                this.clearHighlight();
                            }
                        }" @click.away="closeDropdown()">
                            <div class="relative">
                                <input type="search"
                                       id="header-search-input"
                                       x-ref="searchInput"
                                       x-model="query"
                                       @focus="handleFocus()"
                                       @input.debounce.300ms="search()"
                                       @keydown.enter.prevent="selectHighlighted()"
                                       @keydown.arrow-down.prevent="moveHighlight(1)"
                                       @keydown.arrow-up.prevent="moveHighlight(-1)"
                                       @keydown.escape.prevent="$dispatch('search-toggle', false); closeDropdown()"
                                       placeholder="Ürün, kategori veya marka arayın..."
                                       class="w-full bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-600 rounded-full px-6 py-3 pl-12 pr-24 focus:outline-none focus:border-blue-500 dark:focus:border-blue-400 transition text-gray-800 dark:text-white placeholder-gray-400 dark:placeholder-gray-500"
                                       autocomplete="off">
                                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-blue-500 dark:text-blue-400"></i>
                                <button @click="goToSearch()"
                                        type="button"
                                        class="absolute right-2 top-1/2 -translate-y-1/2 bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-2 rounded-full hover:from-blue-700 hover:to-purple-700 transition disabled:opacity-70 disabled:cursor-not-allowed"
                                        :disabled="loading">
                                    <i class="fa-solid fa-spinner fa-spin" x-show="loading" x-cloak></i>
                                    <span x-show="!loading" x-cloak>Ara</span>
                                </button>
                            </div>

                            {{-- Hybrid Autocomplete Dropdown --}}
                            <div x-show="isOpen"
                                 x-transition
                                 class="absolute top-full left-0 right-0 mt-2 bg-white dark:bg-gray-800 shadow-xl rounded-lg z-50 border border-gray-200 dark:border-gray-700" style="z-index:50;">

                                <template x-if="error">
                                    <div class="px-5 py-6 text-sm text-red-600 dark:text-red-400 flex items-center gap-3">
                                        <i class="fa-solid fa-circle-exclamation text-base"></i>
                                        <span x-text="error"></span>
                                    </div>
                                </template>

                                <div class="max-h-[28rem] overflow-y-auto">
                                    <div class="grid gap-8 px-4 py-4 lg:grid-cols-[minmax(0,0.9fr)_minmax(0,1.1fr)]">
                                        {{-- Keywords Section --}}
                                        <div x-show="(keywords?.length || 0) > 0" class="space-y-2 border border-gray-200 dark:border-gray-700 rounded-lg p-4 lg:p-5 bg-gray-50 dark:bg-gray-900/40">
                                            <div class="flex items-center justify-between text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                                <span><i class="fa-solid fa-fire text-orange-500 mr-1"></i> Popüler Aramalar</span>
                                                <span class="text-[10px] text-gray-400 dark:text-gray-500" x-text="`${keywords?.length || 0}`"></span>
                                            </div>
                                            <div class="space-y-1">
                                                <template x-for="(keyword, index) in keywords" :key="'k-'+index">
                                                    <a href="#"
                                                       @click.prevent="selectKeyword(keyword)"
                                                       @mouseenter="setHighlight(index, 'keyword')"
                                                       @mouseleave="clearHighlight()"
                                                       :class="[
                                                            'flex items-center justify-between gap-3 px-3 py-2 rounded-md transition group',
                                                            isHighlighted(index, 'keyword')
                                                                ? 'bg-blue-50 dark:bg-gray-800/70 text-blue-600 dark:text-blue-400'
                                                                : 'hover:bg-white dark:hover:bg-gray-800/70'
                                                        ]">
                                                        <div class="flex items-center gap-3">
                                                            <span class="w-7 h-7 rounded-full bg-white dark:bg-gray-800 flex items-center justify-center text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition">
                                                                <i class="fa-solid fa-magnifying-glass text-sm"></i>
                                                            </span>
                                                            <span class="font-medium text-sm text-gray-900 dark:text-white" x-text="keyword.text"></span>
                                                        </div>
                                                        <span x-show="keyword.count" class="text-xs text-gray-400 dark:text-gray-500" x-text="`${keyword.count} sonuç`"></span>
                                                    </a>
                                                </template>
                                            </div>
                                        </div>

                                        {{-- Products Section --}}
                                        <div x-show="(products?.length || 0) > 0" class="space-y-3">
                                            <div class="flex items-center justify-between text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                                <span><i class="fa-solid fa-box text-blue-500 mr-1"></i> Ürünler</span>
                                                <span x-show="(total || 0) > 0" class="text-[11px] font-medium text-gray-400 dark:text-gray-500" x-text="`${products?.length || 0} / ${total || 0}`"></span>
                                            </div>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                <template x-for="(product, index) in products" :key="'p-'+index">
                                                    <a href="#"
                                                       @click.prevent="selectProduct(product, index)"
                                                       @mouseenter="setHighlight(index, 'product')"
                                                       @mouseleave="clearHighlight()"
                                                       :class="[
                                                            'flex gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 transition group',
                                                            isHighlighted(index, 'product')
                                                                ? 'border-blue-400 dark:border-blue-500 shadow-md'
                                                                : 'hover:border-blue-400 dark:hover:border-blue-500 hover:shadow-md'
                                                        ]">
                                                        <div class="w-16 h-16 rounded-md bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden flex-shrink-0">
                                                            <template x-if="product.image">
                                                                <img :src="product.image"
                                                                     :alt="product.title"
                                                                     width="64"
                                                                     height="64"
                                                                     loading="lazy"
                                                                     class="w-full h-full object-cover">
                                                            </template>
                                                            <template x-if="!product.image">
                                                                <i class="fa-solid fa-cube text-gray-400 dark:text-gray-500 text-xl"></i>
                                                            </template>
                                                        </div>
                                                        <div class="flex-1 min-w-0">
                                                            <div class="font-medium text-sm text-gray-900 dark:text-white leading-snug line-clamp-2"
                                                                 x-html="product.highlighted_title || product.title"></div>
                                                            <p x-show="product.highlighted_description"
                                                               class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-2"
                                                               x-html="product.highlighted_description"></p>
                                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-2 flex items-center justify-between">
                                                                <span x-text="product.type_label"></span>
                                                                <span x-show="product.price"
                                                                      class="ml-2 font-semibold text-green-600 dark:text-green-400"
                                                                      x-text="product.price"></span>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div x-show="showEmptyState()" class="px-5 py-10 text-center text-sm text-gray-500 dark:text-gray-400" x-cloak>
                                    <i class="fa-regular fa-face-smile text-xl mb-2 block text-blue-500 dark:text-blue-400"></i>
                                    <span>Daha fazla sonuç için farklı bir anahtar kelime deneyin.</span>
                                </div>

                                {{-- View All Results --}}
                                <a :href="`/search?q=${encodeURIComponent(query || '')}`"
                                   x-show="(total || 0) > 0"
                                   class="block p-3 text-center text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 font-medium transition border-t border-gray-200 dark:border-gray-700">
                                    <i class="fa-solid fa-arrow-right mr-2"></i>
                                    <span x-text="`Tüm ${total || 0} sonucu gör`"></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Mega Menu'ler (Container Seviyesinde, Navbar'a Hizalı) --}}
                {{-- Ürünler Mega Menu --}}
                <div x-show="activeMegaMenu === 'products'"
                     @mouseleave="activeMegaMenu = null"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 -translate-y-3"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-3"
                     class="absolute left-0 right-0 top-full z-50"
                     x-cloak>
                    <div class="container mx-auto px-4 sm:px-4 md:px-2 -mt-4 pt-4">
                        @include('themes.ixtif.partials.mega-menu-products')
                    </div>
                </div>

                {{-- Hakkımızda Hibrit Mega Menu --}}
                <div x-show="activeMegaMenu === 'hakkimizda'"
                     @mouseleave="activeMegaMenu = null"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 -translate-y-3"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-3"
                     class="absolute left-0 right-0 top-full z-50"
                     x-cloak>
                    <div class="container mx-auto px-4 sm:px-4 md:px-2 -mt-4 pt-4">
                        @include('themes.ixtif.partials.mega-menu-hakkimizda')
                    </div>
                </div>

            </div>
        </nav>

        {{-- Mobile Navigation Menu --}}
        <div x-show="mobileMenuOpen"
             x-transition
             class="lg:hidden bg-white dark:bg-slate-900 border-t border-gray-200 dark:border-gray-800"
             x-cloak>
            <div class="py-2 max-h-[70vh] overflow-y-auto">
                @php
                    // Sistemden kategorileri çek
                    $allCategories = \Modules\Shop\app\Models\ShopCategory::where('is_active', 1)
                        ->where('show_in_menu', 1)
                        ->whereNull('parent_id')
                        ->orderBy('sort_order', 'asc')
                        ->get();

                    // İkonlar (fallback)
                    $categoryIcons = [
                        'forklift' => 'fa-solid fa-forklift',
                        'transpalet' => 'fa-solid fa-dolly',
                        'istif-makinesi' => 'fa-solid fa-box-open-full',
                        'reach-truck' => 'fa-solid fa-truck-ramp-box',
                        'order-picker' => 'fa-solid fa-boxes-packing',
                        'otonom-sistemler' => 'fa-solid fa-robot',
                        'yedek-parca' => 'fa-solid fa-screwdriver-wrench',
                    ];
                @endphp

                {{-- Ürünler Accordion --}}
                <div class="border-b border-gray-100 dark:border-gray-800">
                    <button @click="expandedCategory = expandedCategory === 'urunler' ? null : 'urunler'"
                            class="w-full flex items-center justify-between px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white">
                        <span class="flex items-center gap-3">
                            <i class="fa-solid fa-box-open text-blue-600 dark:text-blue-400 w-5"></i>
                            <span>Ürünler</span>
                        </span>
                        <i class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform duration-200"
                           :class="{ 'rotate-180': expandedCategory === 'urunler' }"></i>
                    </button>

                    <div x-show="expandedCategory === 'urunler'"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-150"
                         class="pb-2 px-4">
                        <div class="space-y-1 pl-8">
                            @foreach($allCategories as $cat)
                                @php
                                    $title = is_array($cat->title) ? $cat->title['tr'] : $cat->title;
                                    $slug = is_array($cat->slug) ? $cat->slug['tr'] : $cat->slug;
                                @endphp
                                <a href="/shop/kategori/{{ $slug }}"
                                   @click="mobileMenuOpen = false"
                                   class="block py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition">
                                    {{ $title }}
                                </a>
                            @endforeach
                            <a href="{{ route('shop.index') }}"
                               @click="mobileMenuOpen = false"
                               class="block py-2 text-sm font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition">
                                Tüm Ürünler →
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Hizmetler Accordion --}}
                <div class="border-b border-gray-100 dark:border-gray-800">
                    <button @click="expandedCategory = expandedCategory === 'hizmetler' ? null : 'hizmetler'"
                            class="w-full flex items-center justify-between px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white">
                        <span class="flex items-center gap-3">
                            <i class="fa-solid fa-screwdriver-wrench text-orange-600 dark:text-orange-400 w-5"></i>
                            <span>Hizmetler</span>
                        </span>
                        <i class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform duration-200"
                           :class="{ 'rotate-180': expandedCategory === 'hizmetler' }"></i>
                    </button>

                    <div x-show="expandedCategory === 'hizmetler'"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-150"
                         class="pb-2 px-4">
                        <div class="space-y-1 pl-8">
                            <a href="/satin-alma" @click="mobileMenuOpen = false"
                               class="block py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-orange-600 dark:hover:text-orange-400 transition">
                                Satın Alma
                            </a>
                            <a href="/kiralama" @click="mobileMenuOpen = false"
                               class="block py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-orange-600 dark:hover:text-orange-400 transition">
                                Kiralama
                            </a>
                            <a href="/teknik-servis" @click="mobileMenuOpen = false"
                               class="block py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-orange-600 dark:hover:text-orange-400 transition">
                                Teknik Servis
                            </a>
                            <a href="/yedek-parca" @click="mobileMenuOpen = false"
                               class="block py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-orange-600 dark:hover:text-orange-400 transition">
                                Yedek Parça
                            </a>
                            <a href="/bakim-anlasmalari" @click="mobileMenuOpen = false"
                               class="block py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-orange-600 dark:hover:text-orange-400 transition">
                                Bakım Anlaşmaları
                            </a>
                            <a href="/ikinci-el" @click="mobileMenuOpen = false"
                               class="block py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-orange-600 dark:hover:text-orange-400 transition">
                                İkinci El
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Kurumsal Accordion --}}
                <div class="border-b border-gray-100 dark:border-gray-800">
                    <button @click="expandedCategory = expandedCategory === 'kurumsal' ? null : 'kurumsal'"
                            class="w-full flex items-center justify-between px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white">
                        <span class="flex items-center gap-3">
                            <i class="fa-solid fa-building text-purple-600 dark:text-purple-400 w-5"></i>
                            <span>Kurumsal</span>
                        </span>
                        <i class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform duration-200"
                           :class="{ 'rotate-180': expandedCategory === 'kurumsal' }"></i>
                    </button>

                    <div x-show="expandedCategory === 'kurumsal'"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-150"
                         class="pb-2 px-4">
                        <div class="space-y-1 pl-8">
                            <a href="{{ href('Page', 'show', 'hakkimizda') }}" @click="mobileMenuOpen = false"
                               class="block py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-400 transition">
                                Hakkımızda
                            </a>
                            <a href="{{ href('Page', 'show', 'kariyer') }}" @click="mobileMenuOpen = false"
                               class="block py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-400 transition">
                                Kariyer
                            </a>
                            <a href="/blog" @click="mobileMenuOpen = false"
                               class="block py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-400 transition">
                                Blog
                            </a>
                        </div>
                    </div>
                </div>

                {{-- İletişim (Direkt Link) --}}
                <a href="{{ href('Page', 'show', 'iletisim') }}"
                   @click="mobileMenuOpen = false"
                   class="flex items-center gap-3 px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    <i class="fa-solid fa-envelope text-green-600 dark:text-green-400 w-5"></i>
                    <span>İletişim</span>
                </a>

                {{-- Alt Bilgi --}}
                <div class="mt-2 px-4 py-3 border-t border-gray-100 dark:border-gray-800">
                    <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                        <a href="tel:{{ str_replace(' ', '', setting('contact_phone_1')) }}" class="flex items-center gap-2 hover:text-blue-600 dark:hover:text-blue-400">
                            <i class="fa-solid fa-phone"></i>
                            <span>{{ setting('contact_phone_1') }}</span>
                        </a>
                        <a href="{{ whatsapp_link() }}" target="_blank" class="flex items-center gap-2 hover:text-green-600 dark:hover:text-green-400">
                            <i class="fa-brands fa-whatsapp"></i>
                            <span>WhatsApp</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- Dynamic Content Areas --}}
    @stack('header-content')

    {{-- Ekran Boyutu Göstergesi (Development için) --}}
    <x-screen-size-indicator />
