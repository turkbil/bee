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

{{-- Favicon --}}
    @php $favicon = setting('site_favicon'); @endphp
    @if($favicon && $favicon !== 'Favicon yok')
    <link rel="icon" type="image/x-icon" href="{{ cdn($favicon) }}">
    @else
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif

    {{-- PWA Manifest (2025 Best Practice) --}}
    <link rel="manifest" href="{{ route('manifest') }}">

    {{-- Apple Touch Icon (iOS/Safari) --}}
    @php $appleTouchIcon = setting('site_logo') ?? $favicon; @endphp
    @if($appleTouchIcon && $appleTouchIcon !== 'Favicon yok')
    <link rel="apple-touch-icon" href="{{ cdn($appleTouchIcon) }}">
    @endif

    {{-- Tailwind CSS - Compiled & Minified --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ now()->timestamp }}" media="all">

    {{-- Font Awesome Pro --}}
    <link rel="stylesheet" href="{{ asset('assets/libs/fontawesome-pro@7.1.0/css/all.css') }}" media="all">

    {{-- TEMPORARY: Google Fonts for Testing (Will be removed later) - EXPANDED LIST --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Roboto:wght@300;400;500;700;900&family=Open+Sans:wght@300;400;600;700;800&family=Lato:wght@300;400;700;900&family=Montserrat:wght@300;400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700;800;900&family=Nunito:wght@300;400;600;700;800;900&family=Raleway:wght@300;400;500;600;700;800;900&family=Outfit:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@300;400;500;600;700&family=Manrope:wght@300;400;500;600;700;800&family=DM+Sans:wght@300;400;500;700;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Quicksand:wght@300;400;500;600;700&family=Josefin+Sans:wght@300;400;600;700&family=Work+Sans:wght@300;400;500;600;700;800;900&family=Rubik:wght@300;400;500;600;700;800;900&family=Karla:wght@300;400;500;600;700;800&family=Ubuntu:wght@300;400;500;700&family=Mukta:wght@300;400;500;600;700;800&family=Lexend:wght@300;400;500;600;700;800;900&family=Red+Hat+Display:wght@300;400;500;600;700;800;900&family=Sora:wght@300;400;500;600;700;800&family=Epilogue:wght@300;400;500;600;700;800;900&family=Archivo:wght@300;400;500;600;700;800;900&family=Figtree:wght@300;400;500;600;700;800;900&family=Be+Vietnam+Pro:wght@300;400;500;600;700;800;900&family=Urbanist:wght@300;400;500;600;700;800;900&family=Albert+Sans:wght@300;400;500;600;700;800;900&family=Geist:wght@300;400;500;600;700;800;900&family=Onest:wght@300;400;500;600;700;800;900&family=Schibsted+Grotesk:wght@400;500;600;700;800;900&family=IBM+Plex+Sans:wght@300;400;500;600;700&family=Source+Sans+3:wght@300;400;600;700;900&family=Bricolage+Grotesque:wght@300;400;500;600;700;800&family=Cabinet+Grotesk:wght@400;500;700;900&family=General+Sans:wght@400;500;600;700&family=Satoshi:wght@300;400;500;700;900&family=Clash+Display:wght@400;500;600;700&family=Instrument+Sans:wght@400;500;600;700&family=Shantell+Sans:wght@300;400;500;600;700;800&family=Anybody:wght@300;400;500;600;700;800;900&family=Kumbh+Sans:wght@300;400;500;600;700;800;900&family=Commissioner:wght@300;400;500;600;700;800;900&family=Golos+Text:wght@400;500;600;700;800;900&family=Spline+Sans:wght@300;400;500;600;700&family=Familjen+Grotesk:wght@400;500;600;700&family=Darker+Grotesque:wght@300;400;500;600;700;800;900&family=Jost:wght@300;400;500;600;700;800;900&family=Exo+2:wght@300;400;500;600;700;800;900&family=Barlow:wght@300;400;500;600;700;800;900&family=Hind:wght@300;400;500;600;700&family=Oxygen:wght@300;400;700&family=Heebo:wght@300;400;500;600;700;800;900&family=Yantramanav:wght@300;400;500;700;900&family=Noto+Sans:wght@300;400;500;600;700;800;900&family=PT+Sans:wght@400;700&display=swap" rel="stylesheet">

    {{-- RTL/LTR Direction Support --}}
    <style>
        [dir="rtl"] {
            direction: rtl;
        }
        [dir="rtl"] .rtl\\:space-x-reverse > :not([hidden]) ~ :not([hidden]) {
            --tw-space-x-reverse: 1;
        }
        [dir="rtl"] .rtl\\:ml-auto {
            margin-left: auto;
        }
        [dir="rtl"] .rtl\\:mr-auto {
            margin-right: auto;
        }
        [dir="rtl"] .rtl\\:text-right {
            text-align: right;
        }
        [dir="rtl"] .rtl\\:text-left {
            text-align: left;
        }
        /* RTL Dropdown Fix - RTL'de sağdan sola açılır, LTR'de soldan sağa açılır */
        [dir="rtl"] .dropdown-menu .dropdown-content,
        [dir="rtl"] .language-switcher-header .dropdown-content {
            left: 0 !important;
            right: auto !important;
        }
        [dir="ltr"] .dropdown-menu .dropdown-content,
        [dir="ltr"] .language-switcher-header .dropdown-content {
            right: 0 !important;
            left: auto !important;
        }
        /* Mobile Menu Responsive Fix */
        @media (max-width: 1023px) {
            .mobile-nav-container {
                overflow-x: hidden;
                max-width: 100vw;
            }
        }

        /* Footer Logo - Adaptive (Dark mode'da beyaz filtre) */
        .logo-footer-adaptive {
            /* Light mode: normal göster */
        }
        .dark .logo-footer-adaptive {
            /* Dark mode: beyaz filtre uygula */
            filter: brightness(0) invert(1);
            opacity: 0.9;
        }
        .dark .logo-footer-adaptive:hover {
            opacity: 1;
        }

        /* Mega Menu Animation - Yukarıdan Aşağıya + Fade */
        @keyframes slideDownFade {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideUpFade {
            from {
                opacity: 1;
                transform: translateY(0);
            }
            to {
                opacity: 0;
                transform: translateY(-20px);
            }
        }

        .animate-slide-down {
            animation: slideDownFade 0.4s ease-out forwards;
        }

        .animate-slide-up {
            animation: slideUpFade 0.3s ease-in forwards;
        }

        /* Mega Menu Content Fade */
        .mega-menu-content-fade-enter {
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Global Font - Roboto (Font Awesome hariç) */
        :root {
            --global-font: 'Roboto', sans-serif;
        }

        *:not([class*="fa-"]):not(i) {
            font-family: var(--global-font) !important;
        }

        /* Smooth Scroll Behavior */
        html {
            scroll-behavior: smooth;
        }

        /* Header Sticky Animation */
        header {
            will-change: transform;
            backface-visibility: hidden;
        }

        /* Header yukarı kayar - top bar gizlenir */
        #main-header {
            transform: translateY(0);
        }
        #main-header.scrolled {
            transform: translateY(calc(var(--top-bar-height, 52px) * -1));
        }

        /* Main nav scroll */
        #main-header.scrolled #main-nav {
            /* Shadow kaldırıldı - temiz tasarım için */
        }

        /* Top Bar Animated Gradient - Dark Mode Only */
        @keyframes slideGradient {
            0%, 100% {
                background: linear-gradient(90deg, rgba(30, 41, 59, 0.9) 0%, rgba(15, 23, 42, 0.9) 50%, rgba(30, 41, 59, 0.9) 100%);
            }
            50% {
                background: linear-gradient(90deg, rgba(15, 23, 42, 0.9) 0%, rgba(30, 41, 59, 0.9) 50%, rgba(15, 23, 42, 0.9) 100%);
            }
        }

        .dark #top-bar {
            animation: slideGradient 20s ease-in-out infinite;
            background: rgba(15, 23, 42, 0.9) !important;
        }

        /* 🎬 Multi-Layer Animated Background Gradients */
        @keyframes gradient-x {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        @keyframes gradient-y {
            0%, 100% { background-position: 50% 0%; }
            50% { background-position: 50% 100%; }
        }

        @keyframes gradient-xy {
            0%, 100% { background-position: 0% 0%; }
            25% { background-position: 100% 0%; }
            50% { background-position: 100% 100%; }
            75% { background-position: 0% 100%; }
        }

        /* Dark Mode Body - Base Background */
        body.dark-mode-active {
            background: linear-gradient(135deg, rgb(15, 23, 42) 0%, rgb(2, 6, 23) 100%);
            position: relative;
        }

        /* Layer 1 - Blue → Slate Gradient (Horizontal Movement) */
        body.dark-mode-active::before {
            content: '';
            position: fixed;
            inset: 0;
            background: linear-gradient(135deg,
                rgba(37, 99, 235, 0.3) 0%,
                rgba(30, 41, 59, 0.2) 30%,
                transparent 70%,
                transparent 100%);
            background-size: 400% 400%;
            animation: gradient-x 30s ease infinite;
            pointer-events: none;
            z-index: 0;
        }

        /* Layer 2 - Slate → Blue → Slate Gradient (Diagonal Movement) */
        body.dark-mode-active::after {
            content: '';
            position: fixed;
            inset: 0;
            background: linear-gradient(225deg,
                transparent 0%,
                rgba(15, 23, 42, 0.3) 25%,
                rgba(37, 99, 235, 0.2) 50%,
                rgba(15, 23, 42, 0.25) 75%,
                transparent 100%);
            background-size: 400% 400%;
            animation: gradient-xy 60s ease infinite;
            pointer-events: none;
            z-index: 0;
        }
    </style>

    {{-- Livewire Styles --}}
    @livewireStyles

    {{-- System Cache Clear Function --}}
    <script>
        function clearSystemCache(button) {
            const spinner = button.querySelector('.loading-spinner');
            const text = button.querySelector('.button-text');
            const icon = button.querySelector('svg:first-child');

            // Loading state
            button.disabled = true;
            spinner.classList.remove('hidden');
            icon.classList.add('hidden');
            text.textContent = 'Temizleniyor...';

            fetch('/clear-cache', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    text.textContent = 'Başarılı!';
                    button.classList.remove('bg-red-600', 'hover:bg-red-700');
                    button.classList.add('bg-green-600');

                    setTimeout(() => {
                        // Otomatik sayfa yenileme - hard refresh ile
                        window.location.reload(true);
                    }, 1000);
                } else {
                    throw new Error(data.message);
                }
            })
            .catch(error => {
                text.textContent = 'Hata!';
                button.classList.remove('bg-red-600', 'hover:bg-red-700');
                button.classList.add('bg-red-700');

                setTimeout(() => {
                    // Reset button
                    button.disabled = false;
                    spinner.classList.add('hidden');
                    icon.classList.remove('hidden');
                    text.textContent = 'Cache';
                    button.classList.remove('bg-red-700');
                    button.classList.add('bg-red-600', 'hover:bg-red-700');
                }, 2000);
            });
        }

        // Background gradients removed - using fixed gradients now
        // Light: bg-gradient-to-br from-white via-slate-50 to-gray-100 (Silver Mist)
        // Dark: bg-gradient-to-br from-gray-900 via-blue-900 to-purple-900 (Blue Purple)

    </script>

    {{-- Custom Gradient Utilities - Tailwind JIT Dark Mode Fix --}}
    <link rel="stylesheet" href="{{ asset('css/custom-gradients.css') }}?v=8.0.1">

    {{-- Core System Styles - Mandatory for all themes --}}
    <link rel="stylesheet" href="{{ asset('css/core-system.css') }}?v=1.0.1">

    {{-- Dynamic Content Areas --}}
    @stack('head')
    @stack('styles')
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
    class="sticky top-0 left-0 right-0 z-50">

        {{-- Top Info Bar - Scroll'da kaybolacak --}}
        <div id="top-bar" class="bg-slate-50/90 dark:bg-slate-900/90 backdrop-blur-md border-b border-gray-200/50 dark:border-white/10">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
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

                            $currentModel = isset($item) ? $item : null;
                            $moduleAction = 'show';
                            if (isset($items) && !isset($item)) $moduleAction = 'index';
                            if (isset($category)) { $currentModel = $category; $moduleAction = 'category'; }

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
                                 class="dropdown-content absolute top-full mt-2 w-44 bg-white dark:bg-slate-900 rounded-lg border border-gray-200 dark:border-gray-700 py-1 z-50">

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
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
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
                                $fallbackMode = $logos['fallback_mode'] ?? 'none';
                            @endphp
                            @if($fallbackMode === 'both')
                                {{-- Her iki logo da var - Direkt göster --}}
                                <img src="{{ $logoUrl }}"
                                     alt="{{ $logos['site_title'] }}"
                                     class="dark:hidden object-contain h-10 w-auto">
                                <img src="{{ $logoDarkUrl }}"
                                     alt="{{ $logos['site_title'] }}"
                                     class="hidden dark:block object-contain h-10 w-auto">
                            @elseif($fallbackMode === 'light_only' || $logoUrl)
                                {{-- Sadece light logo var - Her modda göster --}}
                                <img src="{{ $logoUrl }}"
                                     alt="{{ $logos['site_title'] ?? setting('site_name') }}"
                                     class="block object-contain h-10 w-auto">
                            @elseif($fallbackMode === 'dark_only' || $logoDarkUrl)
                                {{-- Sadece dark logo var - Her modda göster --}}
                                <img src="{{ $logoDarkUrl }}"
                                     alt="{{ $logos['site_title'] ?? setting('site_name') }}"
                                     class="block object-contain h-10 w-auto">
                            @else
                                <div class="flex items-center gap-2" x-data="{ showX: true }" x-init="setInterval(() => { showX = !showX }, 3000)">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 via-purple-600 to-pink-500 rounded-xl flex items-center justify-center">
                                        <i class="fa-solid fa-forklift text-white text-xl"></i>
                                    </div>
                                    <div>
                                        <h1 class="text-xl font-black text-gray-900 dark:text-white relative inline-block">
                                            <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-purple-600 dark:from-blue-400 dark:to-purple-400">i</span><span x-show="showX" x-transition:enter="transition-all duration-300" x-transition:enter-start="opacity-0 scale-75" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition-all duration-300" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-75" class="bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-purple-600 dark:from-blue-400 dark:to-purple-400 inline-block">X</span><span x-show="!showX" x-transition:enter="transition-all duration-300" x-transition:enter-start="opacity-0 scale-75" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition-all duration-300" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-75" class="bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-purple-600 dark:from-blue-400 dark:to-purple-400 inline-block">S</span><span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-purple-600 dark:from-blue-400 dark:to-purple-400">tif</span>
                                        </h1>
                                        <p class="text-[10px] text-gray-500 dark:text-gray-400 font-semibold">Türkiye'nin İstif Pazarı</p>
                                    </div>
                                </div>
                            @endif
                        </a>
                    </div>

                    {{-- Main Navigation (Desktop) --}}
                    <div class="hidden lg:flex items-center gap-6">
                        {{-- Forklift (Mega Menu) --}}
                        <button @mouseenter="activeMegaMenu = 'forklift'"
                                class="flex items-center gap-2 text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-semibold transition group">
                            <i class="fa-solid fa-forklift text-sm"></i>
                            <span>Forklift</span>
                            <i class="fa-solid fa-chevron-down text-xs transition-transform"
                               :class="{ 'rotate-180': activeMegaMenu === 'forklift' }"></i>
                        </button>

                        {{-- Transpalet (Mega Menu) --}}
                        <button @mouseenter="activeMegaMenu = 'transpalet'"
                                class="flex items-center gap-2 text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-semibold transition group">
                            <i class="fa-solid fa-dolly text-sm"></i>
                            <span>Transpalet</span>
                            <i class="fa-solid fa-chevron-down text-xs transition-transform"
                               :class="{ 'rotate-180': activeMegaMenu === 'transpalet' }"></i>
                        </button>

                        {{-- İstif Makinesi (Mega Menu) --}}
                        <button @mouseenter="activeMegaMenu = 'istif-makinesi'"
                                class="flex items-center gap-2 text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-semibold transition group">
                            <i class="fa-solid fa-box-open-full text-sm"></i>
                            <span>İstif Makinesi</span>
                            <i class="fa-solid fa-chevron-down text-xs transition-transform"
                               :class="{ 'rotate-180': activeMegaMenu === 'istif-makinesi' }"></i>
                        </button>

                        {{-- Tüm Kategoriler (Mega Menu + Tabs) --}}
                        <button @mouseenter="activeMegaMenu = 'all-categories'"
                                class="flex items-center gap-2 text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-semibold transition group">
                            <i class="fa-solid fa-grid-2 transition-transform duration-300"
                               :class="{ 'rotate-180': activeMegaMenu === 'all-categories' }"></i>
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
                                             class="dropdown-content absolute top-full mt-2 w-48 bg-white dark:bg-slate-900 rounded-lg border border-gray-200 dark:border-gray-700 py-2 z-50">
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
                        <button @click="searchOpen = !searchOpen; activeMegaMenu = null"
                                class="w-10 h-10 rounded-full hover:bg-blue-50 dark:hover:bg-blue-900/20 flex items-center justify-center text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition">
                            <i class="fa-solid text-lg transition-all duration-200"
                               :class="searchOpen ? 'fa-search-minus' : 'fa-search-plus'"></i>
                        </button>

                        {{-- Dark/Light Mode Toggle --}}
                        <button @click="darkMode = darkMode === 'dark' ? 'light' : 'dark'"
                            class="w-10 h-10 rounded-full hover:bg-purple-50 dark:hover:bg-purple-900/20 flex items-center justify-center text-gray-700 dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400 transition">
                            <template x-if="darkMode === 'dark'">
                                <i class="fa-solid fa-sun text-lg"></i>
                            </template>
                            <template x-if="darkMode === 'light'">
                                <i class="fa-solid fa-moon text-lg"></i>
                            </template>
                        </button>

                        {{-- AUTH CONTROL VIA LIVEWIRE --}}
                        @livewire('auth.header-menu')

                        {{-- Mobile Menu Button --}}
                        <button @click="mobileMenuOpen = !mobileMenuOpen"
                                class="lg:hidden w-10 h-10 rounded-lg bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 flex items-center justify-center text-white transition-all duration-300">
                            <i class="fa-solid text-lg transition-all duration-300"
                               :class="mobileMenuOpen ? 'fa-times rotate-90' : 'fa-bars'"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Dropdown Wrapper - Contains both search and mega menu stacked --}}
            <div class="absolute left-0 right-0 top-full z-20">
                {{-- Search Bar --}}
                <div x-show="searchOpen"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-2"
                     class="relative z-40 bg-white dark:bg-slate-900 border-t border-gray-300 dark:border-white/20 shadow-lg"
                     x-cloak>
                    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-4">
                        {{-- Alpine.js + API Search (No Livewire overhead) --}}
                        <div class="relative" x-data="{
                            query: '',
                            keywords: [],
                            products: [],
                            total: 0,
                            isOpen: false,
                            loading: false,
                            async search() {
                                if (this.query.length < 2) {
                                    this.keywords = [];
                                    this.products = [];
                                    this.isOpen = false;
                                    return;
                                }
                                this.loading = true;
                                try {
                                    const response = await fetch(`/api/search/suggestions?q=${encodeURIComponent(this.query)}`);
                                    const data = await response.json();

                                    if (data.success && data.data) {
                                        this.keywords = data.data.keywords || [];
                                        this.products = data.data.products || [];
                                        this.total = data.data.total || 0;
                                        this.isOpen = (this.keywords.length > 0 || this.products.length > 0);
                                    } else {
                                        this.keywords = [];
                                        this.products = [];
                                        this.isOpen = false;
                                    }
                                } catch (e) {
                                    console.error('Suggestions error:', e);
                                    this.keywords = [];
                                    this.products = [];
                                    this.isOpen = false;
                                }
                                this.loading = false;
                            },
                            goToSearch() {
                                if (this.query.length >= 1) {
                                    window.location.href = `/search?q=${encodeURIComponent(this.query)}`;
                                }
                            }
                        }" @click.away="isOpen = false">
                            <div class="relative">
                                <input type="search"
                                       x-model="query"
                                       @input.debounce.300ms="search()"
                                       @keydown.enter="goToSearch()"
                                       placeholder="Ürün, kategori veya marka arayın..."
                                       class="w-full bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-600 rounded-full px-6 py-3 pl-12 pr-24 focus:outline-none focus:border-blue-500 dark:focus:border-blue-400 transition text-gray-800 dark:text-white placeholder-gray-400 dark:placeholder-gray-500"
                                       autocomplete="off">
                                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-blue-500 dark:text-blue-400"></i>
                                <button @click="goToSearch()"
                                        type="button"
                                        class="absolute right-2 top-1/2 -translate-y-1/2 bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-2 rounded-full hover:from-blue-700 hover:to-purple-700 transition">
                                    <i class="fa-solid fa-spinner fa-spin" x-show="loading" x-cloak></i>
                                    <span x-show="!loading">Ara</span>
                                </button>
                            </div>

                            {{-- Hybrid Autocomplete Dropdown --}}
                            <div x-show="isOpen"
                                 x-transition
                                 class="absolute top-full left-0 right-0 mt-2 bg-white dark:bg-gray-800 shadow-xl rounded-lg z-[100] border border-gray-200 dark:border-gray-700 overflow-hidden">

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
                                                    <a :href="`/search?q=${encodeURIComponent(keyword.text)}`"
                                                       class="flex items-center justify-between gap-3 px-3 py-2 rounded-md hover:bg-white dark:hover:bg-gray-800/70 transition group">
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
                                                    <a :href="product.url"
                                                       class="flex gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-blue-400 dark:hover:border-blue-500 hover:shadow-md transition group">
                                                        <div class="w-16 h-16 rounded-md bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden flex-shrink-0">
                                                            <template x-if="product.image">
                                                                <img :src="product.image"
                                                                     :alt="product.title"
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
                     class="relative z-0 bg-white dark:bg-slate-900 border-t border-gray-300 dark:border-white/20 shadow-xl"
                     x-cloak>
                    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
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
             class="lg:hidden bg-slate-50/95 dark:bg-slate-900 backdrop-blur-lg border-t border-gray-300 dark:border-white/20 mobile-nav-container">
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

    {{-- Simple Scroll Handler --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const header = document.getElementById('main-header');
            const topBar = document.getElementById('top-bar');

            if (!header || !topBar) {
                console.error('Header elements not found!');
                return;
            }

            // Top bar height'ı hesapla
            const topBarHeight = topBar.offsetHeight;
            console.log('Top bar height:', topBarHeight + 'px');

            // CSS variable olarak ekle
            document.documentElement.style.setProperty('--top-bar-height', topBarHeight + 'px');

            window.addEventListener('scroll', function() {
                const currentScroll = window.pageYOffset;

                if (currentScroll > 30) {
                    header.classList.add('scrolled');
                } else {
                    header.classList.remove('scrolled');
                }
            });

            console.log('✅ Sticky header initialized');
        });

        // Eski font ayarlarını temizle (Roboto sabit olarak kullanılıyor)
        if (localStorage.getItem('selectedFont')) {
            localStorage.removeItem('selectedFont');
            console.log('✅ Font ayarları temizlendi - Roboto sabit font olarak ayarlandı');
        }

        // 🐛 DARK MODE + ANIMATION DEBUG
        setTimeout(() => {
            const isDark = document.documentElement.classList.contains('dark');
            const bodyBg = window.getComputedStyle(document.body).backgroundColor;
            const bodyClasses = document.body.className;
            const bodyAnimation = window.getComputedStyle(document.body).animation;
            const hasDarkModeActive = document.body.classList.contains('dark-mode-active');

            console.log('🌓 DARK MODE + ANIMATION DEBUG:');
            console.log('  ├─ localStorage.darkMode:', localStorage.getItem('darkMode'));
            console.log('  ├─ HTML has .dark class:', isDark);
            console.log('  ├─ Body has .dark-mode-active:', hasDarkModeActive);
            console.log('  ├─ Body computed background:', bodyBg);
            console.log('  ├─ Body animation:', bodyAnimation);
            console.log('  ├─ Body classes:', bodyClasses);
            console.log('  └─ Expected: slideBodyGradient 20s ease-in-out infinite');

            if (isDark && !hasDarkModeActive) {
                console.error('❌ SORUN: Dark mode aktif ama .dark-mode-active class yok!');
                console.log('💡 Alpine.js :class binding çalışmıyor olabilir');
            } else if (isDark && !bodyAnimation.includes('slideBodyGradient')) {
                console.error('❌ SORUN: Class var ama animasyon çalışmıyor!');
                console.log('💡 CSS animasyonu yüklenememiş olabilir - hard refresh dene');
            } else if (isDark && hasDarkModeActive) {
                console.log('✅ Dark mode + animasyon aktif!');
            }
        }, 1000);
    </script>

    {{-- Dynamic Content Areas --}}
    @stack('header-content')
