<!DOCTYPE html>
@php
    $currentLocale = app()->getLocale();
    $isRtl = in_array($currentLocale, ['ar', 'he', 'fa', 'ur']) ? 'rtl' : 'ltr';
@endphp
<html lang="{{ $currentLocale }}"
      dir="{{ $isRtl }}"
      x-data="{ darkMode: localStorage.getItem('darkMode') || 'light' }"
      x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))"
      :class="{ 'dark': darkMode === 'dark' }">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Alpine.js is included in Livewire - DO NOT load separately --}}

{{-- Global SEO Meta Tags - Tek Satır --}}
<x-seo-meta />

{{-- Favicon - Tenant-aware dynamic route --}}
    <link rel="icon" type="image/x-icon" href="/favicon.ico">

    {{-- PWA Manifest (2025 Best Practice) --}}
    <link rel="manifest" href="{{ route('manifest') }}">

    {{-- Apple Touch Icon (iOS/Safari) - Uses favicon as fallback --}}
    <link rel="apple-touch-icon" href="/favicon.ico">

    {{-- Performance: DNS Prefetch & Preconnect --}}
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="//www.googletagmanager.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>

    {{-- Performance: Preload Critical CSS --}}
    {{-- Tailwind CSS - Compiled & Minified (Direct load, no preload needed) --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ now()->timestamp }}" media="all">

    {{-- Font Awesome Pro --}}
    <link rel="stylesheet" href="{{ asset('assets/libs/fontawesome-pro@7.1.0/css/all.css') }}" media="all">

    {{-- Google Fonts - Roboto All Weights --}}
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    {{-- iXtif Theme Styles - Bundle if available, fallback to individual files --}}
    @if(file_exists(public_path('css/ixtif-bundle.min.css')))
        {{-- Performance Optimized: 4 CSS files bundled --}}
        <link rel="stylesheet" href="{{ asset('css/ixtif-bundle.min.css') }}" media="all">
    @else
        {{-- Fallback: Individual CSS files --}}
        <link rel="stylesheet" href="{{ asset('css/ixtif-theme.css') }}?v={{ now()->timestamp }}" media="all">
        <link rel="stylesheet" href="{{ asset('css/custom-gradients.css') }}?v=8.0.1">
        <link rel="stylesheet" href="{{ asset('css/core-system.css') }}?v=1.0.1">
    @endif

    {{-- Livewire Styles --}}
    @livewireStyles

    {{-- Google Analytics --}}
    @php
        $googleAnalyticsCode = setting('seo_site_google_analytics_code');
    @endphp
    @if($googleAnalyticsCode)
    <!-- Google tag (gtag.js) - Performance Optimized -->
    <script defer src="https://www.googletagmanager.com/gtag/js?id={{ $googleAnalyticsCode }}"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', '{{ $googleAnalyticsCode }}');
    </script>
    @endif

    {{-- Yandex Metrica --}}
    @php
        $yandexMetricaCode = setting('seo_site_yandex_metrica');
    @endphp
    @if($yandexMetricaCode)
    <!-- Yandex.Metrica counter -->
    {!! $yandexMetricaCode !!}
    <!-- /Yandex.Metrica counter -->
    @endif

    {{-- Dynamic Content Areas --}}
    @stack('head')
    @stack('styles')

    {{-- AI Chat CSS - Load in head for styling --}}
    <link rel="stylesheet" href="/assets/css/ai-chat.css?v=<?php echo time(); ?>">
    {{-- AI Chat JS moved to footer.blade.php AFTER Alpine.js/Livewire --}}

    {{-- Livewire Styles --}}
    @livewireStyles
</head>

<body class="font-sans antialiased min-h-screen transition-all duration-500 flex flex-col"
      :class="{ 'dark-mode-active': darkMode === 'dark' }"
      :style="darkMode === 'dark' ? 'color: rgb(243, 244, 246);' : 'background: linear-gradient(to bottom right, rgb(255, 255, 255), rgb(248, 250, 252), rgb(243, 244, 246)); color: rgb(17, 24, 39);'">


    <header id="main-header" x-data="{
        sidebarOpen: false,
        mobileMenuOpen: false,
        expandedCategory: null,
        activeMegaMenu: null,
        searchOpen: false,
        activeCategory: 'first'
    }"
    class="sticky top-0 left-0 right-0 z-50"
    @search-toggle.window="searchOpen = $event.detail; if (!searchOpen) { activeMegaMenu = null; }"
    @keydown.escape.window="searchOpen = false; activeMegaMenu = null">

        {{-- Top Info Bar - Scroll'da kaybolacak --}}
        <div id="top-bar" class="bg-slate-50/90 dark:bg-slate-900/90 backdrop-blur-md border-b border-gray-200/50 dark:border-white/10 overflow-hidden">
            <div class="container mx-auto px-4 sm:px-4 md:px-0">
                <div class="flex items-center justify-between text-sm py-3">
                    <div class="flex items-center gap-4 sm:gap-6 text-gray-600 dark:text-gray-400">
                        @php
                            $contactPhone = setting('contact_phone_1');
                            $contactWhatsapp = setting('contact_whatsapp_1');
                        @endphp

                        {{-- Telefon (Tıklanabilir) --}}
                        @if($contactPhone)
                            <a href="tel:{{ str_replace(' ', '', $contactPhone) }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition flex items-center gap-2 text-sm font-medium">
                                <i class="fa-solid fa-phone"></i>
                                <span>{{ $contactPhone }}</span>
                            </a>
                        @endif

                        {{-- WhatsApp (Tıklanabilir) --}}
                        @if($contactWhatsapp)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contactWhatsapp) }}" target="_blank" class="hover:text-green-600 dark:hover:text-green-400 transition flex items-center gap-2 text-sm font-medium">
                                <i class="fa-brands fa-whatsapp text-base"></i>
                                <span>{{ $contactWhatsapp }}</span>
                            </a>
                        @endif
                    </div>
                    <div class="flex items-center gap-3 sm:gap-4">
                        {{-- Sık Sorulan Sorular --}}
                        <a href="#" class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition text-xs sm:text-sm font-medium">
                            <i class="fa-solid fa-circle-question mr-1"></i>
                            Sık Sorulan Sorular
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
                                    class="flex items-center gap-1 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition text-xs sm:text-sm">
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
        <nav id="main-nav" class="bg-white/95 dark:bg-slate-900/90 backdrop-blur-lg">
            <div class="container mx-auto px-4 sm:px-4 md:px-0">
                <div id="nav-container" class="flex items-center justify-between py-4">
                    {{-- Logo - Sabit Genişlik Container --}}
                    <div class="flex items-center gap-3" style="width: 200px;">
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
                                     title="{{ $siteTitle }}">
                                <img src="{{ $logoDarkUrl }}"
                                     alt="{{ $siteTitle }}"
                                     class="hidden dark:block object-contain h-10 w-auto"
                                     title="{{ $siteTitle }}">
                            @elseif($fallbackMode === 'light_only' || $logoUrl)
                                {{-- Sadece light logo var - Dark mode'da CSS ile beyaz yap --}}
                                <img src="{{ $logoUrl }}"
                                     alt="{{ $siteTitle }}"
                                     class="block object-contain h-10 w-auto logo-adaptive"
                                     title="{{ $siteTitle }}">
                            @elseif($fallbackMode === 'dark_only' || $logoDarkUrl)
                                {{-- Sadece dark logo var - Her modda göster --}}
                                <img src="{{ $logoDarkUrl }}"
                                     alt="{{ $siteTitle }}"
                                     class="block object-contain h-10 w-auto"
                                     title="{{ $siteTitle }}">
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
                        {{-- Forklift (Mega Menu) - Tıklanabilir + Hover --}}
                        <a href="/shop/kategori/forklift"
                           @mouseenter="activeMegaMenu = 'forklift'"
                           class="flex items-center gap-2 text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-semibold transition group">
                            <i :class="activeMegaMenu === 'forklift' ? 'fa-solid' : 'fa-light'" class="fa-forklift text-sm transition-all"></i>
                            <span>Forklift</span>
                            <i class="fa-solid fa-chevron-down text-xs transition-transform"
                               :class="{ 'rotate-180': activeMegaMenu === 'forklift' }"></i>
                        </a>

                        {{-- Transpalet (Mega Menu) - Tıklanabilir + Hover --}}
                        <a href="/shop/kategori/transpalet"
                           @mouseenter="activeMegaMenu = 'transpalet'"
                           class="flex items-center gap-2 text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-semibold transition group">
                            <i :class="activeMegaMenu === 'transpalet' ? 'fa-solid' : 'fa-light'" class="fa-dolly text-sm transition-all"></i>
                            <span>Transpalet</span>
                            <i class="fa-solid fa-chevron-down text-xs transition-transform"
                               :class="{ 'rotate-180': activeMegaMenu === 'transpalet' }"></i>
                        </a>

                        {{-- İstif Makinesi (Mega Menu) - Tıklanabilir + Hover - Sadece XL ve üstünde göster --}}
                        <a href="/shop/kategori/istif-makinesi"
                           @mouseenter="activeMegaMenu = 'istif-makinesi'"
                           class="hidden xl:flex items-center gap-2 text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-semibold transition group">
                            <i :class="activeMegaMenu === 'istif-makinesi' ? 'fa-solid' : 'fa-light'" class="fa-box-open-full text-sm transition-all"></i>
                            <span>İstif Makinesi</span>
                            <i class="fa-solid fa-chevron-down text-xs transition-transform"
                               :class="{ 'rotate-180': activeMegaMenu === 'istif-makinesi' }"></i>
                        </a>

                        {{-- Tüm Kategoriler (Mega Menu + Tabs) --}}
                        <button @mouseenter="activeMegaMenu = 'all-categories'"
                                class="flex items-center gap-2 text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-semibold transition group">
                            <i :class="activeMegaMenu === 'all-categories' ? 'fa-solid' : 'fa-light'" class="fa-grid-2 transition-all duration-300"></i>
                            <span>Tüm Kategoriler</span>
                            <i class="fa-solid fa-chevron-down text-xs transition-transform"
                               :class="{ 'rotate-180': activeMegaMenu === 'all-categories' }"></i>
                        </button>

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
                    <div class="flex items-center gap-2">
                        {{-- Search Button with Tooltip --}}
                        <div x-data="{ showTooltip: false }" class="relative">
                            <button @click="searchOpen = !searchOpen; activeMegaMenu = null"
                                    @mouseenter="showTooltip = true"
                                    @mouseleave="showTooltip = false"
                                    aria-label="Arama menüsünü aç/kapat"
                                    :aria-expanded="searchOpen"
                                    class="w-10 h-10 rounded-full hover:bg-blue-50 dark:hover:bg-blue-900/20 flex items-center justify-center text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition">
                                <i class="fa-light text-lg transition-all duration-200"
                                   :class="searchOpen ? 'fa-times' : 'fa-magnifying-glass'"></i>
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

                        {{-- Dark/Light Mode Toggle with Tooltip --}}
                        <div x-data="{ showTooltip: false }" class="relative">
                            <button @click="darkMode = darkMode === 'dark' ? 'light' : 'dark'"
                                    @mouseenter="showTooltip = true"
                                    @mouseleave="showTooltip = false"
                                    :aria-label="darkMode === 'dark' ? 'Aydınlık moda geç' : 'Karanlık moda geç'"
                                    class="w-10 h-10 rounded-full hover:bg-purple-50 dark:hover:bg-purple-900/20 flex items-center justify-center text-gray-700 dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400 transition">
                                <template x-if="darkMode === 'dark'">
                                    <i class="fa-regular fa-sun-bright text-lg"></i>
                                </template>
                                <template x-if="darkMode === 'light'">
                                    <i class="fa-light fa-moon text-lg"></i>
                                </template>
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
                                <span x-text="darkMode === 'dark' ? 'Aydınlık Mod' : 'Karanlık Mod'"></span>
                                {{-- Tooltip Arrow --}}
                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-px">
                                    <div class="border-[5px] border-transparent border-b-purple-600/95 dark:border-b-purple-500/95"></div>
                                </div>
                            </div>
                        </div>

                        {{-- AUTH CONTROL VIA LIVEWIRE --}}
                        @livewire('auth.header-menu')

                        {{-- Mobile Menu Button --}}
                        <div x-data="{ showTooltip: false }" class="relative lg:hidden">
                            <button @click="mobileMenuOpen = !mobileMenuOpen"
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
                     class="relative z-30 bg-white dark:bg-slate-900 border-t border-gray-300 dark:border-white/20 shadow-lg">
                    <div class="container mx-auto px-4 sm:px-4 md:px-0 py-4">
                        {{-- Alpine.js + API Search (No Livewire overhead) --}}
                        <div class="relative" x-data="{
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
                            selectProduct(product) {
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
                                 class="absolute top-full left-0 right-0 mt-2 bg-white dark:bg-gray-800 shadow-xl rounded-lg z-40 border border-gray-200 dark:border-gray-700 overflow-hidden" style="z-index:40;">

                                <template x-if="error">
                                    <div class="px-5 py-6 text-sm text-red-600 dark:text-red-400 flex items-center gap-3">
                                        <i class="fa-solid fa-circle-exclamation text-base"></i>
                                        <span x-text="error"></span>
                                    </div>
                                </template>

                                <div class="max-h-[28rem] overflow-y-auto">
                                    <div class="grid gap-6 px-4 py-4 lg:grid-cols-[minmax(0,0.9fr)_minmax(0,1.1fr)]">
                                        {{-- Keywords Section --}}
                                        <div x-show="keywords.length > 0" class="space-y-2 border border-gray-200 dark:border-gray-700 rounded-lg p-4 lg:p-5 bg-gray-50 dark:bg-gray-900/40">
                                            <div class="flex items-center justify-between text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                                <span><i class="fa-solid fa-fire text-orange-500 mr-1"></i> Popüler Aramalar</span>
                                                <span class="text-[10px] text-gray-400 dark:text-gray-500" x-text="`${keywords.length}`"></span>
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
                                        <div x-show="products.length > 0" class="space-y-3">
                                            <div class="flex items-center justify-between text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                                <span><i class="fa-solid fa-box text-blue-500 mr-1"></i> Ürünler</span>
                                                <span x-show="total > 0" class="text-[11px] font-medium text-gray-400 dark:text-gray-500" x-text="`${products.length} / ${total}`"></span>
                                            </div>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                <template x-for="(product, index) in products" :key="'p-'+index">
                                                    <a href="#"
                                                       @click.prevent="selectProduct(product)"
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
                                <a :href="`/search?q=${encodeURIComponent(query)}`"
                                   x-show="total > 0"
                                   class="block p-3 text-center text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 font-medium transition border-t border-gray-200 dark:border-gray-700">
                                    <i class="fa-solid fa-arrow-right mr-2"></i>
                                    <span x-text="`Tüm ${total} sonucu gör`"></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Mega Menu Dropdown - Always below search if both open --}}
                <div x-show="activeMegaMenu !== null"
                     @mouseleave="activeMegaMenu = null"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 -translate-y-3"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-3"
                     class="relative z-10 bg-white dark:bg-slate-900 border-t border-gray-300 dark:border-white/20 shadow-xl" style="z-index:10;">
                    <div class="container mx-auto px-4 sm:px-4 md:px-0 py-6 md:py-8">
                        {{-- Grid overlay system: all menus in same position, auto height based on visible menu --}}
                        <div style="display: grid;">
                            {{-- Forklift Mega Menu --}}
                            <div x-show="activeMegaMenu === 'forklift'"
                                 x-transition:enter="transition-opacity ease-out duration-200"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 x-transition:leave="transition-opacity ease-in duration-150"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0"
                                 style="grid-area: 1/1;"
                                 x-cloak>
                                @include('themes.ixtif.partials.mega-menu-forklift')
                            </div>

                            {{-- Transpalet Mega Menu --}}
                            <div x-show="activeMegaMenu === 'transpalet'"
                                 x-transition:enter="transition-opacity ease-out duration-200"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 x-transition:leave="transition-opacity ease-in duration-150"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0"
                                 style="grid-area: 1/1;"
                                 x-cloak>
                                @include('themes.ixtif.partials.mega-menu-transpalet')
                            </div>

                            {{-- İstif Makinesi Mega Menu --}}
                            <div x-show="activeMegaMenu === 'istif-makinesi'"
                                 x-transition:enter="transition-opacity ease-out duration-200"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 x-transition:leave="transition-opacity ease-in duration-150"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0"
                                 style="grid-area: 1/1;"
                                 x-cloak>
                                @include('themes.ixtif.partials.mega-menu-istif')
                            </div>

                            {{-- Tüm Kategoriler Mega Menu --}}
                            <div x-show="activeMegaMenu === 'all-categories'"
                                 x-transition:enter="transition-opacity ease-out duration-200"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 x-transition:leave="transition-opacity ease-in duration-150"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0"
                                 style="grid-area: 1/1;"
                                 x-cloak>
                                @include('themes.ixtif.partials.mega-menu-content')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        {{-- Mobile Navigation Menu --}}
        <div x-show="mobileMenuOpen"
             x-transition
             class="lg:hidden bg-slate-50/95 dark:bg-slate-900 backdrop-blur-lg border-t border-gray-300 dark:border-white/20 mobile-nav-container"
             x-cloak>
            <div class="px-4 py-3 space-y-1 max-w-full overflow-x-hidden">
                @php
                    // Mobile için ana kategoriler ve alt kategoriler
                    $mainCategories = [
                        [
                            'name' => 'Forklift',
                            'slug' => 'forklift',
                            'icon' => 'fa-solid fa-forklift',
                            'subcategories' => [
                                ['name' => 'Elektrikli Forklift', 'slug' => 'elektrikli-forklift'],
                                ['name' => 'Dizel Forklift', 'slug' => 'dizel-forklift'],
                                ['name' => 'LPG Forklift', 'slug' => 'lpg-forklift'],
                                ['name' => 'Reach Truck', 'slug' => 'reach-truck'],
                            ]
                        ],
                        [
                            'name' => 'Transpalet',
                            'slug' => 'transpalet',
                            'icon' => 'fa-solid fa-dolly',
                            'subcategories' => [
                                ['name' => 'Manuel Transpalet', 'slug' => 'manuel-transpalet'],
                                ['name' => 'Akülü Transpalet', 'slug' => 'akulu-transpalet'],
                                ['name' => 'Makaslı Transpalet', 'slug' => 'makasli-transpalet'],
                            ]
                        ],
                        [
                            'name' => 'İstif Makinesi',
                            'slug' => 'istif-makinesi',
                            'icon' => 'fa-solid fa-box-open-full',
                            'subcategories' => [
                                ['name' => 'Yürüyen İstif', 'slug' => 'yuruyen-istif'],
                                ['name' => 'Binekli İstif', 'slug' => 'binekli-istif'],
                                ['name' => 'Geniş Ayaklı İstif', 'slug' => 'genis-ayakli-istif'],
                            ]
                        ],
                    ];
                @endphp

                {{-- Ana Kategoriler (Mobile) - Accordion Style --}}
                @foreach($mainCategories as $cat)
                    <div x-data="{ categoryOpen: false }" class="space-y-1">
                        {{-- Ana Kategori Başlığı --}}
                        <div class="flex items-center gap-2">
                            <button @click="categoryOpen = !categoryOpen"
                                    class="flex-1 flex items-center justify-between px-3 py-2 rounded-md text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 transition">
                                <span class="flex items-center gap-2">
                                    <i class="{{ $cat['icon'] }} text-sm"></i>
                                    <span>{{ $cat['name'] }}</span>
                                </span>
                                <i class="fa-solid fa-chevron-down text-xs transition-transform duration-300"
                                   :class="{ 'rotate-180': categoryOpen }"></i>
                            </button>
                            {{-- Hepsini Gör Linki --}}
                            <a href="{{ route('shop.index') }}?category={{ $cat['slug'] }}"
                               @click="mobileMenuOpen = false"
                               class="px-2 py-2 text-xs text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-md transition">
                                Tümü
                            </a>
                        </div>

                        {{-- Alt Kategoriler --}}
                        <div x-show="categoryOpen"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-2"
                             class="pl-8 space-y-1 bg-gray-50 dark:bg-gray-900/50 rounded-md py-1">
                            @foreach($cat['subcategories'] as $sub)
                                <a href="{{ route('shop.index') }}?category={{ $sub['slug'] }}"
                                   @click="mobileMenuOpen = false"
                                   class="block px-3 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gradient-to-r hover:from-blue-50 hover:to-purple-50 dark:hover:from-blue-900/20 dark:hover:to-purple-900/20 rounded-md transition">
                                    {{ $sub['name'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                {{-- Tüm Kategoriler --}}
                <a href="{{ route('shop.index') }}"
                   @click="mobileMenuOpen = false"
                   class="block px-3 py-2 rounded-md text-sm font-medium text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-900/30 flex items-center gap-2">
                    <i class="fa-solid fa-grid-2 text-sm"></i>
                    <span>Tüm Kategoriler</span>
                </a>

                @php
                    $currentLocale = app()->getLocale();
                    $headerMenu = getDefaultMenu($currentLocale);
                @endphp

                {{-- Diğer Dinamik Menüler (varsa) --}}
                @if($headerMenu && !empty($headerMenu['items']))
                    <div class="border-t border-gray-200 dark:border-gray-700 mt-2 pt-2"></div>
                    @foreach($headerMenu['items'] as $menuItem)
                        @if(!empty($menuItem['children']))
                            <div x-data="{ submenuOpen: false }">
                                <button @click="submenuOpen = !submenuOpen"
                                        class="w-full flex items-center justify-between px-3 py-2 rounded-md text-sm font-medium {{ $menuItem['has_active_child'] ? 'text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-900/30' : 'text-gray-700 dark:text-gray-300' }}">
                                    <span class="flex items-center gap-2">
                                        @if($menuItem['icon'])
                                            <i class="{{ $menuItem['icon'] }}"></i>
                                        @endif
                                        {{ $menuItem['title'] }}
                                    </span>
                                    <i class="fa-solid fa-chevron-down text-xs transition-transform" :class="{ 'rotate-180': submenuOpen }"></i>
                                </button>
                                <div x-show="submenuOpen" x-transition class="pl-4 space-y-1">
                                    @foreach($menuItem['children'] as $child)
                                        <a href="{{ $child['url'] }}"
                                           @click="mobileMenuOpen = false"
                                           class="block px-3 py-2 text-sm rounded-md hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 {{ $child['is_active'] ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300' : 'text-gray-600 dark:text-gray-400' }}">
                                            {{ $child['title'] }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <a href="{{ $menuItem['url'] }}"
                               @click="mobileMenuOpen = false"
                               class="block px-3 py-2 rounded-md text-sm font-medium {{ $menuItem['is_active'] ? 'text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-900/30' : 'text-gray-700 dark:text-gray-300' }} flex items-center gap-2">
                                @if($menuItem['icon'])
                                    <i class="{{ $menuItem['icon'] }}"></i>
                                @endif
                                {{ $menuItem['title'] }}
                            </a>
                        @endif
                    @endforeach
                @endif
            </div>
        </div>
    </header>

    {{-- Dynamic Content Areas --}}
    @stack('header-content')

    {{-- Ekran Boyutu Göstergesi (Development için) --}}
    <x-screen-size-indicator />
