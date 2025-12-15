<!DOCTYPE html>
@php
    $currentLocale = app()->getLocale();
    $isRtl = in_array($currentLocale, ['ar', 'he', 'fa', 'ur']) ? 'rtl' : 'ltr';
    $defaultLocale = get_tenant_default_locale();
    $homeUrl = $currentLocale === $defaultLocale ? url('/') : url('/' . $currentLocale);

    // Logo bilgilerini LogoService'den al
    $logoService = app(\App\Services\LogoService::class);
    $logos = $logoService->getLogos();

    // Settings'den site bilgileri
    $siteTitle = setting('site_title', setting('site_name', config('app.name')));
@endphp
<html lang="{{ $currentLocale }}" dir="{{ $isRtl }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" :class="{ 'dark': darkMode }">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $siteTitle }} - Ödeme</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="/favicon.ico">

    {{-- Tailwind CSS - Tenant-Aware --}}
    <link rel="stylesheet" href="{{ tenant_css() }}" media="all">

    {{-- Font Awesome Pro --}}
    <link rel="stylesheet" href="{{ asset('assets/libs/fontawesome-pro@7.1.0/css/all.css') }}" media="all">

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Livewire Styles --}}
    @livewireStyles

    {{-- Dark Mode Initial State --}}
    <script>
        if (localStorage.getItem('darkMode') === 'true' ||
            (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>

    @stack('styles')
</head>

<body class="font-sans antialiased min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    {{-- Minimal Compact Header --}}
    <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3">
            <div class="flex items-center justify-between">
                {{-- Logo --}}
                @if($logos['has_light'] && $logos['has_dark'])
                    <a href="{{ $homeUrl }}" class="flex items-center">
                        {{-- Light mode logo --}}
                        <img src="{{ $logos['light_logo_url'] }}"
                             alt="{{ $siteTitle }}"
                             class="h-8 w-auto object-contain dark:hidden"
                             title="{{ $siteTitle }}"
                             width="120"
                             height="32">
                        {{-- Dark mode logo --}}
                        <img src="{{ $logos['dark_logo_url'] }}"
                             alt="{{ $siteTitle }}"
                             class="h-8 w-auto object-contain hidden dark:block"
                             title="{{ $siteTitle }}"
                             width="120"
                             height="32">
                    </a>
                @elseif($logos['has_light'])
                    <a href="{{ $homeUrl }}" class="flex items-center">
                        <img src="{{ $logos['light_logo_url'] }}"
                             alt="{{ $siteTitle }}"
                             class="h-8 w-auto object-contain"
                             title="{{ $siteTitle }}"
                             width="120"
                             height="32">
                    </a>
                @elseif($logos['has_dark'])
                    <a href="{{ $homeUrl }}" class="flex items-center">
                        <img src="{{ $logos['dark_logo_url'] }}"
                             alt="{{ $siteTitle }}"
                             class="h-8 w-auto object-contain"
                             title="{{ $siteTitle }}"
                             width="120"
                             height="32">
                    </a>
                @else
                    <a href="{{ $homeUrl }}" class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ $siteTitle }}
                    </a>
                @endif

                <div class="flex items-center gap-4">
                    {{-- Dark Mode Toggle --}}
                    <button @click="darkMode = !darkMode"
                            class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                            title="Tema Değiştir">
                        <i class="fa-solid fa-sun text-lg" x-show="darkMode"></i>
                        <i class="fa-solid fa-moon text-lg" x-show="!darkMode"></i>
                    </button>

                    {{-- Güvenli Ödeme Badge --}}
                    <div class="flex items-center gap-2 text-green-600 dark:text-green-400">
                        <i class="fa-solid fa-lock text-sm"></i>
                        <span class="text-xs font-medium hidden sm:inline">Güvenli Ödeme</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="flex-1">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    {{-- Minimal Footer --}}
    <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 py-3 mt-auto transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 text-center">
            <p class="text-xs text-gray-500 dark:text-gray-400">
                © {{ date('Y') }} {{ $siteTitle }}
            </p>
        </div>
    </footer>

    {{-- Livewire Scripts --}}
    @livewireScripts

    @stack('scripts')
</body>
</html>
