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

    {{-- Performance Optimization - DNS Prefetch & Preconnect --}}
    <link rel="dns-prefetch" href="//cdn.tailwindcss.com">
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdn.tailwindcss.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>

    {{-- Preload Critical Resources (2025 Best Practice) --}}
    <link rel="preload" href="https://cdn.tailwindcss.com" as="script">
    <link rel="modulepreload" href="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js">

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

    {{-- Tailwind CSS --}}
    <script>
        // Suppress Tailwind CDN production warning
        (function() {
            const originalWarn = console.warn;
            console.warn = function(...args) {
                const msg = String(args[0] || '');
                if (msg.includes('cdn.tailwindcss.com') || msg.includes('should not be used in production')) {
                    return; // Suppress Tailwind CDN warning
                }
                originalWarn.apply(console, args);
            };
        })();
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        }
                    }
                }
            },
            corePlugins: {
                // RTL Support
                direction: false,
            }
        }
    </script>
    
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

    {{-- Core System Styles - Mandatory for all themes --}}
    <link rel="stylesheet" href="{{ asset('css/core-system.css') }}?v=1.0.0">

    {{-- Dynamic Content Areas --}}
    @stack('head')
    @stack('styles')
</head>

<body class="font-sans antialiased min-h-screen bg-gray-50 text-gray-800 dark:bg-gray-900 dark:text-gray-200 transition-colors duration-300 flex flex-col">


    <header class="bg-white shadow dark:bg-gray-800 transition-colors duration-300" x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-14 sm:h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        @php
                            $currentLocale = app()->getLocale();
                            $defaultLocale = get_tenant_default_locale();
                            $homeUrl = $currentLocale === $defaultLocale ? url('/') : url('/' . $currentLocale);
                            $siteLogo = setting('site_logo');
                            $siteTitle = setting('site_title', config('app.name'));
                        @endphp
                        <a href="{{ $homeUrl }}" class="inline-flex items-center text-lg sm:text-xl font-bold text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition-colors duration-300" title="{{ $siteTitle }}" alt="{{ $siteTitle }}">
                            @if($siteLogo && $siteLogo !== 'Logo yok')
                                <img src="{{ cdn($siteLogo) }}" alt="{{ $siteTitle }}" title="{{ $siteTitle }}" class="h-6 sm:h-8 w-auto">
                            @else
                                {{ $siteTitle }}
                            @endif
                        </a>
                    </div>
                    
                    {{-- Mobile Menu Button --}}
                    <button class="lg:hidden ml-4 p-2 rounded-md text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-300" 
                            @click="mobileMenuOpen = !mobileMenuOpen">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" :class="{ 'rotate-90': mobileMenuOpen }">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  :d="mobileMenuOpen ? 'M6 18L18 6M6 6l12 12' : 'M4 6h16M4 12h16M4 18h16'"></path>
                        </svg>
                    </button>
                    
                    {{-- Desktop Navigation --}}
                    <nav class="hidden lg:flex lg:ml-6 lg:space-x-4">
                        @php
                            $currentLocale = app()->getLocale();
                            $headerMenu = getDefaultMenu($currentLocale);
                        @endphp
                        
                        @if($headerMenu && !empty($headerMenu['items']))
                            @foreach($headerMenu['items'] as $menuItem)
                                @if(!empty($menuItem['children']))
                                    {{-- Dropdown menü --}}
                                    <div class="relative dropdown-menu" x-data="{ open: false }">
                                        <button @click="open = !open" 
                                                class="px-3 py-2 rounded-md text-sm font-medium flex items-center transition-colors duration-300 {{ $menuItem['has_active_child'] ? 'text-blue-700 bg-blue-50 hover:bg-blue-100 dark:text-blue-300 dark:bg-blue-900/30 dark:hover:bg-blue-900/40' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700' }}">
                                            @if($menuItem['icon'])
                                                <i class="{{ $menuItem['icon'] }} mr-2"></i>
                                            @endif
                                            {{ $menuItem['title'] }}
                                            <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                        </button>
                                        
                                        {{-- Dropdown içeriği --}}
                                        <div x-show="open" 
                                             @click.away="open = false"
                                             x-transition:enter="transition ease-out duration-200"
                                             x-transition:enter-start="opacity-0 scale-95"
                                             x-transition:enter-end="opacity-100 scale-100"
                                             x-transition:leave="transition ease-in duration-75"
                                             x-transition:leave-start="opacity-100 scale-100"
                                             x-transition:leave-end="opacity-0 scale-95"
                                             class="dropdown-content absolute top-full mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-2 z-50">
                                            
                                            @foreach($menuItem['children'] as $child)
                                                <a href="{{ $child['url'] }}" 
                                                   {{ $child['target'] === '_blank' ? 'target="_blank"' : '' }}
                                                   class="block px-4 py-2 text-sm transition-colors duration-200 {{ $child['is_active'] ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 font-medium' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                                                    @if($child['icon'])
                                                        <i class="{{ $child['icon'] }} mr-2"></i>
                                                    @endif
                                                    {{ $child['title'] }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    {{-- Normal menü item --}}
                                    <a href="{{ $menuItem['url'] }}" 
                                       {{ $menuItem['target'] === '_blank' ? 'target="_blank"' : '' }}
                                       class="px-3 py-2 rounded-md text-sm font-medium transition-colors duration-300 {{ $menuItem['is_active'] ? 'text-blue-700 bg-blue-50 hover:bg-blue-100 dark:text-blue-300 dark:bg-blue-900/30 dark:hover:bg-blue-900/40' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700' }}">
                                        @if($menuItem['icon'])
                                            <i class="{{ $menuItem['icon'] }} mr-2"></i>
                                        @endif
                                        {{ $menuItem['title'] }}
                                    </a>
                                @endif
                            @endforeach
                        @else
                            {{-- Fallback hardcode menu if no dynamic menu found --}}
                            <a href="{{ href('Page', 'index') }}"
                                class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700 transition-colors duration-300">Sayfalar</a>
                            <a href="{{ href('Announcement', 'index') }}"
                                class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700 transition-colors duration-300">Duyurular</a>
                            <a href="{{ href('Portfolio', 'index') }}"
                                class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700 transition-colors duration-300">Portfolyo</a>
                        @endif
                        <button onclick="clearSystemCache(this)"
                            class="inline-flex items-center px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-md transition-colors duration-200">
                            <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="button-text">Cache</span>
                            <svg class="h-4 w-4 ml-1 loading-spinner hidden animate-spin" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M4 2a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V4a2 2 0 00-2-2H4zm6 14a6 6 0 100-12 6 6 0 000 12z"></path>
                            </svg>
                        </button>
                        @auth
                        @if(Auth::user()->roles->count() > 0)
                        <a href="/admin/dashboard"
                            class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700 transition-colors duration-300">Admin
                            Paneli</a>
                        @endif
                        @endauth
                    </nav>
                </div>
                <div class="flex items-center space-x-1">
                    {{-- Dark/Light Mode Toggle --}}
                    <button @click="darkMode = darkMode === 'dark' ? 'light' : 'dark'"
                        class="flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 text-gray-700 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white transition-colors duration-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">
                        <template x-if="darkMode === 'dark'">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
                                </path>
                            </svg>
                        </template>
                        <template x-if="darkMode === 'light'">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z">
                                </path>
                            </svg>
                        </template>
                    </button>

                    {{-- Site Dil Değiştirici - DİREKT ALTERNATE LINKS KULLANAN --}}
                    @php
                        use App\Helpers\CanonicalHelper;
                        $currentModel = isset($item) ? $item : null;
                        $moduleAction = 'show';
                        if (isset($items) && !isset($item)) $moduleAction = 'index';
                        if (isset($category)) { $currentModel = $category; $moduleAction = 'category'; }
                        
                        $currentLang = app()->getLocale();
                        $languageSwitcherLinks = CanonicalHelper::getLanguageSwitcherLinks($currentModel ?? null, $moduleAction);
                    @endphp
                    
                    {{-- Tek dil varsa language switcher'ı gizle --}}
                    @if(count($languageSwitcherLinks) > 1)
                    <div class="language-switcher-header relative" x-data="{ open: false }">
                        @php
                            
                            // Mevcut dil için flag ve isim al
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
                            } catch (\Exception $e) {
                                // Fallback
                            }
                            
                            if (!$currentLangData) {
                                $currentLangData = [
                                    'flag' => '🌐',
                                    'name' => strtoupper($currentLang)
                                ];
                            }
                        @endphp
                        
                        <button @click="open = !open" 
                                class="flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 text-gray-700 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white transition-colors duration-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">
                            {{ $currentLangData['flag'] }}
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
                                    // Dil bilgilerini çek
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
                                    } catch (\Exception $e) {
                                        // Fallback
                                    }
                                @endphp
                                
                                @foreach($languageSwitcherLinks as $locale => $link)
                                    {{-- DİREKT ALTERNATE URL'LERE GİT - /language route'u kullanma! --}}
                                    <a href="{{ $link['url'] }}" 
                                       class="w-full flex items-center px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ $link['active'] ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : '' }}">
                                        <span class="mr-2 text-base">{{ $languageData[$locale]['flag'] ?? '🌐' }}</span>
                                        <span class="flex-1 text-left">{{ $languageData[$locale]['name'] ?? $link['name'] }}</span>
                                        @if($link['active'])
                                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        @endif
                                    </a>
                                @endforeach
                            @else
                                <div class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">
                                    Dil seçeneği bulunamadı
                                </div>
                            @endif
                        </div>
                    </div>
                    @endif {{-- count($languageSwitcherLinks) > 1 --}}
                    
                    {{-- AUTH CONTROL VIA LIVEWIRE - CACHE-SAFE --}}
                    @livewire('auth.header-menu')
                </div>
            </div>
            
            {{-- Mobile Navigation Menu --}}
            <div x-show="mobileMenuOpen" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="lg:hidden bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 mobile-nav-container">
                <div class="px-2 sm:px-4 py-3 space-y-1 max-w-full overflow-x-hidden">
                    @php
                        $currentLocale = app()->getLocale();
                        $headerMenu = getDefaultMenu($currentLocale);
                    @endphp
                    
                    @if($headerMenu && !empty($headerMenu['items']))
                        @foreach($headerMenu['items'] as $menuItem)
                            @if(!empty($menuItem['children']))
                                {{-- Mobile Dropdown --}}
                                <div x-data="{ submenuOpen: false }">
                                    <button @click="submenuOpen = !submenuOpen" 
                                            class="w-full flex items-center justify-between px-3 py-2 rounded-md text-sm font-medium transition-colors duration-300 {{ $menuItem['has_active_child'] ? 'text-blue-700 bg-blue-50 dark:text-blue-300 dark:bg-blue-900/30' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700' }}">
                                        <span class="flex items-center">
                                            @if($menuItem['icon'])
                                                <i class="{{ $menuItem['icon'] }} mr-2"></i>
                                            @endif
                                            {{ $menuItem['title'] }}
                                        </span>
                                        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': submenuOpen }" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    </button>
                                    <div x-show="submenuOpen" x-transition class="pl-4 space-y-1">
                                        @foreach($menuItem['children'] as $child)
                                            <a href="{{ $child['url'] }}" 
                                               {{ $child['target'] === '_blank' ? 'target="_blank"' : '' }}
                                               @click="mobileMenuOpen = false"
                                               class="block px-3 py-2 text-sm rounded-md transition-colors duration-200 {{ $child['is_active'] ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 font-medium' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}">
                                                @if($child['icon'])
                                                    <i class="{{ $child['icon'] }} mr-2"></i>
                                                @endif
                                                {{ $child['title'] }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                {{-- Mobile Normal Menu Item --}}
                                <a href="{{ $menuItem['url'] }}" 
                                   {{ $menuItem['target'] === '_blank' ? 'target="_blank"' : '' }}
                                   @click="mobileMenuOpen = false"
                                   class="block px-3 py-2 rounded-md text-sm font-medium transition-colors duration-300 {{ $menuItem['is_active'] ? 'text-blue-700 bg-blue-50 dark:text-blue-300 dark:bg-blue-900/30' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700' }}">
                                    @if($menuItem['icon'])
                                        <i class="{{ $menuItem['icon'] }} mr-2"></i>
                                    @endif
                                    {{ $menuItem['title'] }}
                                </a>
                            @endif
                        @endforeach
                    @else
                        {{-- Mobile Fallback Menu --}}
                        <a href="{{ href('Page', 'index') }}" @click="mobileMenuOpen = false"
                            class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700 transition-colors duration-300">Sayfalar</a>
                        <a href="{{ href('Announcement', 'index') }}" @click="mobileMenuOpen = false"
                            class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700 transition-colors duration-300">Duyurular</a>
                        <a href="{{ href('Portfolio', 'index') }}" @click="mobileMenuOpen = false"
                            class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700 transition-colors duration-300">Portfolyo</a>
                    @endif
                    
                    {{-- Mobile Admin Panel Link --}}
                    @auth
                    @if(Auth::user()->roles->count() > 0)
                    <a href="/admin/dashboard" @click="mobileMenuOpen = false"
                        class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700 transition-colors duration-300">Admin Paneli</a>
                    @endif
                    @endauth
                </div>
            </div>
        </div>
    </header>

    {{-- Dynamic Content Areas --}}
    @stack('header-content')