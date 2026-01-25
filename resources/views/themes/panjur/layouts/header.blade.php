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

{{-- Global SEO Meta Tags --}}
<x-seo-meta />

{{-- Favicon --}}
    @php $favicon = setting('site_favicon'); @endphp
    @if($favicon && $favicon !== 'Favicon yok')
    <link rel="icon" type="image/x-icon" href="{{ cdn($favicon) }}">
    @else
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif

    {{-- PWA Manifest --}}
    <link rel="manifest" href="{{ route('manifest') }}">

    {{-- Apple Touch Icon --}}
    @php $appleTouchIcon = setting('site_logo') ?? $favicon; @endphp
    @if($appleTouchIcon && $appleTouchIcon !== 'Favicon yok')
    <link rel="apple-touch-icon" href="{{ cdn($appleTouchIcon) }}">
    @endif

    {{-- Tailwind CSS - Tenant-Aware --}}
    <link rel="stylesheet" href="{{ tenant_css() }}" media="all">

    {{-- Font Awesome Pro --}}
    <link rel="stylesheet" href="{{ asset('assets/libs/fontawesome-pro@7.1.0/css/all.css') }}" media="all">

    {{-- Panjur Theme Custom Colors --}}
    <style>
        :root {
            --panjur-primary: #1e3a5f;
            --panjur-secondary: #f59e0b;
            --panjur-accent: #10b981;
        }

        .bg-panjur-primary { background-color: var(--panjur-primary); }
        .bg-panjur-secondary { background-color: var(--panjur-secondary); }
        .text-panjur-primary { color: var(--panjur-primary); }
        .text-panjur-secondary { color: var(--panjur-secondary); }
        .border-panjur-primary { border-color: var(--panjur-primary); }
        .border-panjur-secondary { border-color: var(--panjur-secondary); }

        /* Dark mode adaptations */
        .dark .bg-panjur-primary { background-color: #0f1f33; }

        /* Industrial gradient */
        .panjur-gradient {
            background: linear-gradient(135deg, var(--panjur-primary) 0%, #2d4a6f 100%);
        }

        /* Footer Logo - Adaptive */
        .logo-footer-adaptive { }
        .dark .logo-footer-adaptive {
            filter: brightness(0) invert(1);
            opacity: 0.9;
        }
        .dark .logo-footer-adaptive:hover { opacity: 1; }
    </style>

    {{-- Livewire Styles --}}
    @livewireStyles

    {{-- Core System Styles --}}
    <link rel="stylesheet" href="{{ asset('css/core-system.css') }}?v=1.0.0">

    {{-- Dynamic Content Areas --}}
    @stack('head')
    @stack('styles')
</head>

<body class="font-sans antialiased min-h-screen bg-gray-50 text-gray-800 dark:bg-slate-900 dark:text-gray-200 transition-colors duration-300 flex flex-col">

    <header class="sticky top-0 z-50 bg-white shadow-lg dark:bg-slate-800 border-b-4 border-panjur-secondary transition-colors duration-300" x-data="{ mobileMenuOpen: false }">
        {{-- Top Bar --}}
        <div class="bg-panjur-primary text-white py-2 hidden sm:block">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center text-sm">
                    <div class="flex items-center space-x-4">
                        <span class="flex items-center">
                            <i class="fas fa-phone-alt mr-2 text-panjur-secondary"></i>
                            {{ setting('site_phone', '+90 555 123 4567') }}
                        </span>
                        <span class="flex items-center">
                            <i class="fas fa-envelope mr-2 text-panjur-secondary"></i>
                            {{ setting('site_email', 'info@panjur.tuufi.com') }}
                        </span>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="flex items-center">
                            <i class="fas fa-map-marker-alt mr-2 text-panjur-secondary"></i>
                            {{ setting('site_address', 'Samsun, Türkiye') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Navigation --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 lg:h-20">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        @php
                            $currentLocale = app()->getLocale();
                            $defaultLocale = get_tenant_default_locale();
                            $homeUrl = $currentLocale === $defaultLocale ? url('/') : url('/' . $currentLocale);
                            $siteLogo = setting('site_logo');
                            $siteKontrastLogo = setting('site_kontrast_logo');
                            $siteTitle = setting('site_title', config('app.name'));

                            $hasLightLogo = $siteLogo && $siteLogo !== 'Logo yok';
                            $hasDarkLogo = $siteKontrastLogo && $siteKontrastLogo !== 'Logo yok';
                            $hasBothLogos = $hasLightLogo && $hasDarkLogo;

                            $tenantId = function_exists('tenant_id') ? tenant_id() : null;
                            $normalizePath = function ($path) use ($tenantId) {
                                if (!$path) return $path;
                                if ($tenantId && !str_contains($path, 'tenant'.$tenantId) && str_starts_with($path, 'storage/')) {
                                    return 'storage/tenant'.$tenantId.'/'.\Illuminate\Support\Str::after($path, 'storage/');
                                }
                                return $path;
                            };

                            $siteLogo = $normalizePath($siteLogo);
                            $siteKontrastLogo = $normalizePath($siteKontrastLogo);

                            $siteLogoUrl = $hasLightLogo ? thumbmaker($siteLogo, 'logo') : null;
                            $siteKontrastLogoUrl = $hasDarkLogo ? thumbmaker($siteKontrastLogo, 'logo') : null;
                        @endphp
                        <a href="{{ $homeUrl }}" class="inline-flex items-center text-xl sm:text-2xl font-bold text-panjur-primary dark:text-white hover:text-panjur-secondary dark:hover:text-panjur-secondary transition-colors duration-300" aria-label="{{ $siteTitle }} - Ana Sayfa">
                            @if($hasBothLogos)
                                <img src="{{ $siteLogoUrl ?? cdn($siteLogo) }}"
                                     alt="{{ $siteTitle }} Logo"
                                     width="180"
                                     height="54"
                                     class="h-10 sm:h-12 w-auto block dark:hidden"
                                     fetchpriority="high">
                                <img src="{{ $siteKontrastLogoUrl ?? cdn($siteKontrastLogo) }}"
                                     alt="{{ $siteTitle }} Logo"
                                     width="180"
                                     height="54"
                                     class="h-10 sm:h-12 w-auto hidden dark:block"
                                     fetchpriority="high">
                            @elseif($hasLightLogo)
                                <img src="{{ $siteLogoUrl ?? cdn($siteLogo) }}"
                                     alt="{{ $siteTitle }} Logo"
                                     width="180"
                                     height="54"
                                     class="h-10 sm:h-12 w-auto"
                                     fetchpriority="high">
                            @elseif($hasDarkLogo)
                                <img src="{{ $siteKontrastLogoUrl ?? cdn($siteKontrastLogo) }}"
                                     alt="{{ $siteTitle }} Logo"
                                     width="180"
                                     height="54"
                                     class="h-10 sm:h-12 w-auto"
                                     fetchpriority="high">
                            @else
                                <span class="flex items-center">
                                    <i class="fas fa-blinds mr-2 text-panjur-secondary"></i>
                                    {{ $siteTitle }}
                                </span>
                            @endif
                        </a>
                    </div>

                    {{-- Mobile Menu Button --}}
                    <button aria-label="Mobil menü"
                            class="lg:hidden ml-4 p-2 rounded-md text-gray-600 dark:text-gray-300 hover:text-panjur-primary dark:hover:text-white hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors duration-300"
                            @click="mobileMenuOpen = !mobileMenuOpen">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  :d="mobileMenuOpen ? 'M6 18L18 6M6 6l12 12' : 'M4 6h16M4 12h16M4 18h16'"></path>
                        </svg>
                    </button>

                    {{-- Desktop Navigation --}}
                    <nav class="hidden lg:flex lg:ml-10 lg:space-x-1">
                        @php
                            $currentLocale = app()->getLocale();
                            $headerMenu = getDefaultMenu($currentLocale);
                        @endphp

                        @if($headerMenu && !empty($headerMenu['items']))
                            @foreach($headerMenu['items'] as $menuItem)
                                @if(!empty($menuItem['children']))
                                    <div class="relative" x-data="{ open: false }">
                                        <button @click="open = !open"
                                                class="px-4 py-2 rounded-md text-sm font-semibold flex items-center transition-all duration-300 {{ $menuItem['has_active_child'] ? 'text-panjur-secondary bg-panjur-primary/10' : 'text-gray-700 dark:text-gray-200 hover:text-panjur-primary dark:hover:text-panjur-secondary hover:bg-gray-100 dark:hover:bg-slate-700' }}">
                                            @if($menuItem['icon'])
                                                <i class="{{ $menuItem['icon'] }} mr-2"></i>
                                            @endif
                                            {{ $menuItem['title'] }}
                                            <svg class="w-4 h-4 ml-1 transition-transform" :class="{ 'rotate-180': open }" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                        </button>

                                        <div x-show="open"
                                             @click.away="open = false"
                                             x-transition
                                             class="absolute top-full right-0 mt-2 w-56 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-gray-200 dark:border-slate-600 py-2 z-50">
                                            @foreach($menuItem['children'] as $child)
                                                <a href="{{ $child['url'] }}"
                                                   class="block px-4 py-3 text-sm transition-colors duration-200 {{ $child['is_active'] ? 'bg-panjur-secondary/10 text-panjur-secondary font-semibold' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700' }}">
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
                                       class="px-4 py-2 rounded-md text-sm font-semibold transition-all duration-300 {{ $menuItem['is_active'] ? 'text-panjur-secondary bg-panjur-primary/10' : 'text-gray-700 dark:text-gray-200 hover:text-panjur-primary dark:hover:text-panjur-secondary hover:bg-gray-100 dark:hover:bg-slate-700' }}">
                                        @if($menuItem['icon'])
                                            <i class="{{ $menuItem['icon'] }} mr-2"></i>
                                        @endif
                                        {{ $menuItem['title'] }}
                                    </a>
                                @endif
                            @endforeach
                        @else
                            <a href="{{ url('/') }}" class="px-4 py-2 rounded-md text-sm font-semibold text-gray-700 dark:text-gray-200 hover:text-panjur-primary hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors duration-300">
                                <i class="fas fa-home mr-2"></i>Ana Sayfa
                            </a>
                        @endif
                    </nav>
                </div>

                <div class="flex items-center space-x-2">
                    {{-- Dark Mode Toggle --}}
                    <button @click="darkMode = darkMode === 'dark' ? 'light' : 'dark'"
                            aria-label="Tema değiştir"
                            class="p-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                        <template x-if="darkMode === 'dark'">
                            <i class="fas fa-sun text-yellow-500"></i>
                        </template>
                        <template x-if="darkMode === 'light'">
                            <i class="fas fa-moon text-panjur-primary"></i>
                        </template>
                    </button>

                    {{-- CTA Button --}}
                    <a href="{{ url('/iletisim') }}" class="hidden sm:inline-flex items-center px-5 py-2.5 bg-panjur-secondary hover:bg-amber-600 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-300">
                        <i class="fas fa-phone-alt mr-2"></i>
                        Teklif Al
                    </a>

                    {{-- Auth Menu --}}
                    @livewire('auth.header-menu')
                </div>
            </div>

            {{-- Mobile Navigation Menu --}}
            <div x-show="mobileMenuOpen"
                 x-transition
                 class="lg:hidden bg-white dark:bg-slate-800 border-t border-gray-200 dark:border-slate-700 py-4">
                @php
                    $currentLocale = app()->getLocale();
                    $headerMenu = getDefaultMenu($currentLocale);
                @endphp

                @if($headerMenu && !empty($headerMenu['items']))
                    @foreach($headerMenu['items'] as $menuItem)
                        @if(!empty($menuItem['children']))
                            <div x-data="{ submenuOpen: false }">
                                <button @click="submenuOpen = !submenuOpen"
                                        class="w-full flex items-center justify-between px-4 py-3 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700">
                                    <span>{{ $menuItem['title'] }}</span>
                                    <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': submenuOpen }" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                                <div x-show="submenuOpen" class="pl-6 bg-gray-50 dark:bg-slate-700/50">
                                    @foreach($menuItem['children'] as $child)
                                        <a href="{{ $child['url'] }}" @click="mobileMenuOpen = false"
                                           class="block px-4 py-3 text-gray-600 dark:text-gray-300 hover:text-panjur-primary">
                                            {{ $child['title'] }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <a href="{{ $menuItem['url'] }}" @click="mobileMenuOpen = false"
                               class="block px-4 py-3 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-700">
                                {{ $menuItem['title'] }}
                            </a>
                        @endif
                    @endforeach
                @endif

                {{-- Mobile CTA --}}
                <div class="px-4 pt-4 mt-2 border-t border-gray-200 dark:border-slate-600">
                    <a href="{{ url('/iletisim') }}" class="block w-full text-center px-5 py-3 bg-panjur-secondary hover:bg-amber-600 text-white font-semibold rounded-lg transition-colors">
                        <i class="fas fa-phone-alt mr-2"></i>
                        Teklif Al
                    </a>
                </div>
            </div>
        </div>
    </header>

    @stack('header-content')
