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
    <link rel="stylesheet" href="{{ asset('css/app.css') }}" media="all">

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

        /* Global Font - Poppins */
        body {
            font-family: 'Poppins', sans-serif !important;
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
    </script>

    {{-- Custom Gradient Utilities - Tailwind JIT Dark Mode Fix --}}
    <link rel="stylesheet" href="{{ asset('css/custom-gradients.css') }}?v=1.0.0">

    {{-- Core System Styles - Mandatory for all themes --}}
    <link rel="stylesheet" href="{{ asset('css/core-system.css') }}?v=1.0.0">

    {{-- Dynamic Content Areas --}}
    @stack('head')
    @stack('styles')
</head>

<body class="font-sans antialiased min-h-screen bg-gray-50 text-gray-800 dark:bg-gray-900 dark:text-gray-200 transition-colors duration-300 flex flex-col">

    {{-- NEW MEGA MENU HEADER --}}
    <header x-data="{
        sidebarOpen: false,
        mobileMenuOpen: false,
        expandedCategory: null,
        activeMegaMenu: null,
        searchOpen: false,
        activeCategory: 'first'
    }">

        {{-- Top Info Bar --}}
        <div class="border-b border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between text-sm py-2">
                    <div class="flex items-center gap-4 sm:gap-6 text-gray-600 dark:text-gray-400">
                        {{-- Telefon (Tıklanabilir) --}}
                        <a href="tel:02167553555" class="hover:text-blue-600 dark:hover:text-blue-400 transition flex items-center gap-2 text-xs sm:text-sm font-semibold">
                            <i class="fa-solid fa-phone"></i>
                            <span>0216 755 3 555</span>
                        </a>

                        {{-- WhatsApp (Tıklanabilir) --}}
                        <a href="https://wa.me/905010056758" target="_blank" class="hover:text-green-600 dark:hover:text-green-400 transition flex items-center gap-2 text-xs sm:text-sm font-semibold">
                            <i class="fa-brands fa-whatsapp text-base"></i>
                            <span>0501 005 67 58</span>
                        </a>
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
                                 class="dropdown-content absolute top-full mt-2 w-44 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50">

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
                                           class="w-full flex items-center px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ $link['active'] ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : '' }}">
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

        {{-- Main Menu Bar --}}
        <nav class="bg-white dark:bg-gray-800 shadow-lg relative transition-colors duration-300 sticky top-0" style="z-index: 100;">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between py-4">
                    {{-- Logo --}}
                    <div class="flex items-center gap-3">
                        <a href="{{ url('/') }}" class="flex items-center gap-3">
                            @php
                                $siteLogo = setting('site_logo');
                                $siteLogoDark = setting('site_logo_dark');

                                // Logo URL'lerini oluştur
                                $logoUrl = null;
                                $logoDarkUrl = null;

                                if ($siteLogo && $siteLogo !== 'Logo yok') {
                                    // Eğer zaten tam URL ise olduğu gibi kullan
                                    if (preg_match('#^https?://#', $siteLogo)) {
                                        $logoUrl = $siteLogo;
                                    } else {
                                        // Storage path'i URL'e çevir
                                        $logoUrl = asset($siteLogo);
                                    }
                                }

                                if ($siteLogoDark && $siteLogoDark !== 'Logo yok') {
                                    if (preg_match('#^https?://#', $siteLogoDark)) {
                                        $logoDarkUrl = $siteLogoDark;
                                    } else {
                                        $logoDarkUrl = asset($siteLogoDark);
                                    }
                                } else {
                                    // Dark logo yoksa, beyaz logoyu kullan (iXtif için özel)
                                    $whiteLogo = 'storage/tenant2/settings/55/ixtif-Logo-kadir-turuncu-beyaz.png';
                                    if (file_exists(public_path($whiteLogo))) {
                                        $logoDarkUrl = asset($whiteLogo);
                                    }
                                }
                            @endphp
                            @if($logoUrl)
                                {{-- Light mode logo --}}
                                <img src="{{ $logoUrl }}"
                                     alt="{{ setting('site_name') ?? 'iXtif' }}"
                                     class="h-12 sm:h-14 w-auto object-contain dark:hidden"
                                     onerror="console.error('Logo load failed:', this.src); this.style.display='none';">

                                {{-- Dark mode logo --}}
                                @if($logoDarkUrl)
                                    <img src="{{ $logoDarkUrl }}"
                                         alt="{{ setting('site_name') ?? 'iXtif' }}"
                                         class="h-12 sm:h-14 w-auto object-contain hidden dark:block"
                                         onerror="console.error('Dark logo load failed:', this.src); this.style.display='none';">
                                @else
                                    {{-- Fallback: Light logo with filter in dark mode --}}
                                    <img src="{{ $logoUrl }}"
                                         alt="{{ setting('site_name') ?? 'iXtif' }}"
                                         class="h-12 sm:h-14 w-auto object-contain hidden dark:block brightness-0 invert opacity-90"
                                         onerror="console.error('Dark logo fallback failed:', this.src); this.style.display='none';">
                                @endif
                            @else
                                <div class="flex items-center gap-2">
                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 via-purple-600 to-pink-500 rounded-xl flex items-center justify-center shadow-lg">
                                        <i class="fa-solid fa-forklift text-white text-2xl"></i>
                                    </div>
                                    <div>
                                        <h1 class="text-2xl font-black bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-purple-600 dark:from-blue-400 dark:to-purple-400">iXtif</h1>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 font-semibold">Türkiye'nin İstif Pazarı</p>
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

                        {{-- Otonom (Sadece Link - Mega Menu YOK) --}}
                        <a href="{{ route('shop.index') }}?category=amr"
                           class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-semibold transition flex items-center gap-2">
                            <i class="fa-solid fa-robot text-sm"></i>
                            <span>Otonom</span>
                        </a>

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
                                             class="dropdown-content absolute top-full mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-2 z-50">
                                            @foreach($menuItem['children'] as $child)
                                                <a href="{{ $child['url'] }}"
                                                   {{ $child['target'] === '_blank' ? 'target="_blank"' : '' }}
                                                   class="block px-4 py-2 text-sm {{ $child['is_active'] ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-medium' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
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
                                class="w-10 h-10 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center justify-center text-gray-700 dark:text-gray-300 transition">
                            <i class="fa-solid text-lg transition-all duration-200"
                               :class="searchOpen ? 'fa-search-minus' : 'fa-search-plus'"></i>
                        </button>

                        {{-- Dark/Light Mode Toggle --}}
                        <button @click="darkMode = darkMode === 'dark' ? 'light' : 'dark'"
                            class="w-10 h-10 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center justify-center text-gray-700 dark:text-gray-300 transition">
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
                                class="lg:hidden w-10 h-10 rounded-full hover:bg-blue-50 dark:hover:bg-gray-700 flex items-center justify-center text-gray-600 dark:text-gray-400 transition">
                            <i class="fa-solid" :class="mobileMenuOpen ? 'fa-times' : 'fa-bars'"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Dropdown Wrapper - Contains both search and mega menu stacked --}}
            <div class="absolute left-0 right-0 top-full" style="z-index: 150;">
                {{-- Search Bar --}}
                <div x-show="searchOpen"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-2"
                     class="bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700"
                     x-cloak>
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                        <div class="relative">
                            <input type="text"
                                   placeholder="Ürün, kategori veya marka arayın..."
                                   class="w-full bg-gray-50 dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 rounded-full px-6 py-3 pl-12 focus:outline-none focus:border-blue-500 dark:focus:border-blue-400 transition text-gray-800 dark:text-gray-200">
                            <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <button class="absolute right-2 top-1/2 -translate-y-1/2 bg-blue-600 text-white px-6 py-2 rounded-full hover:bg-blue-700 transition">
                                Ara
                            </button>
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
                     class="bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700"
                     x-cloak>
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
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
             class="lg:hidden bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 mobile-nav-container">
            <div class="px-4 py-3 space-y-1 max-w-full overflow-x-hidden">
                @php
                    // Mobile için ana kategoriler
                    $mainCategories = [
                        ['name' => 'Forklift', 'slug' => 'forklift', 'icon' => 'fa-solid fa-forklift'],
                        ['name' => 'Transpalet', 'slug' => 'transpalet', 'icon' => 'fa-solid fa-dolly'],
                        ['name' => 'İstif Makinesi', 'slug' => 'istif-makinesi', 'icon' => 'fa-solid fa-box-open-full'],
                        ['name' => 'Otonom', 'slug' => 'amr', 'icon' => 'fa-solid fa-robot'],
                    ];
                @endphp

                {{-- Ana Kategoriler (Mobile) --}}
                @foreach($mainCategories as $cat)
                    <a href="{{ route('shop.index') }}?category={{ $cat['slug'] }}"
                       @click="mobileMenuOpen = false"
                       class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
                        <i class="{{ $cat['icon'] }} text-sm"></i>
                        <span>{{ $cat['name'] }}</span>
                    </a>
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
                                           class="block px-3 py-2 text-sm rounded-md {{ $child['is_active'] ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300' : 'text-gray-600 dark:text-gray-400' }}">
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
