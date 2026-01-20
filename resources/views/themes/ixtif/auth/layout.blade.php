@php
    $currentLocale = app()->getLocale();
    $isRtl = in_array($currentLocale, ['ar', 'he', 'fa', 'ur']) ? 'rtl' : 'ltr';
    $siteName = setting('site_name') ?: setting('site_title') ?: config('app.name', 'İxtif');
@endphp
<!DOCTYPE html>
<html lang="{{ $currentLocale }}"
      dir="{{ $isRtl }}"
      x-data="{ darkMode: localStorage.getItem('darkMode') || 'light' }"
      x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))"
      :class="{ 'dark': darkMode === 'dark' }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Giriş') - {{ $siteName }}</title>

    {{-- Theme Flash Fix - Prevent FOUC --}}
    <script>if(localStorage.getItem('darkMode')==='dark')document.documentElement.classList.add('dark')</script>

    {{-- Tailwind CSS - Tenant-Aware --}}
    <link rel="stylesheet" href="{{ tenant_css() }}">

    {{-- Font Awesome Pro --}}
    <link rel="stylesheet" href="{{ asset('assets/libs/fontawesome-pro@7.1.0/css/all.css') }}">

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- Alpine.js CDN (Auth sayfalarında Livewire yok) --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Roboto', -apple-system, BlinkMacSystemFont, sans-serif; }
        [x-cloak] { display: none !important; }

        /* Dark mode icon visibility (CSS-based, instant) */
        html:not(.dark) .dark-mode-icon-moon { display: inline-block !important; }
        html:not(.dark) .dark-mode-icon-sun { display: none !important; }
        html.dark .dark-mode-icon-moon { display: none !important; }
        html.dark .dark-mode-icon-sun { display: inline-block !important; }
    </style>

    {{-- Global SEO Meta Tags --}}
    <x-seo-meta />

    {{-- 🎯 Marketing Platforms Auto-Loader (GTM, GA4, Facebook, Yandex, LinkedIn, TikTok, Clarity) --}}
    <x-marketing.auto-platforms />
</head>
<body class="min-h-screen bg-gray-50 dark:bg-gray-900 flex flex-col">

    {{-- 🎯 GTM Body Snippet (No-Script Fallback) --}}
    <x-marketing.gtm-body />

    {{-- Minimal Header --}}
    <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <div class="container mx-auto py-4">
            <div class="flex items-center justify-between">
                {{-- Logo --}}
                <a href="/" class="flex items-center gap-3">
                    @php
                        $logoService = app(\App\Services\LogoService::class);
                        $logos = $logoService->getLogos();
                        $logoUrl = $logos['light_logo_url'] ?? null;
                        $logoDarkUrl = $logos['dark_logo_url'] ?? null;
                        $fallbackMode = $logos['fallback_mode'] ?? 'title_only';
                        $siteTitle = $logos['site_title'] ?? setting('site_title');
                    @endphp

                    @if($fallbackMode === 'both')
                        <img src="{{ $logoUrl }}" alt="{{ $siteTitle }}" class="dark:hidden h-10 w-auto" width="120" height="40">
                        <img src="{{ $logoDarkUrl }}" alt="{{ $siteTitle }}" class="hidden dark:block h-10 w-auto" width="120" height="40">
                    @elseif($logoUrl)
                        <img src="{{ $logoUrl }}" alt="{{ $siteTitle }}" class="h-10 w-auto logo-adaptive" width="120" height="40">
                    @else
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                            <i class="fa-solid fa-industry text-white text-lg"></i>
                        </div>
                        <span class="text-xl font-bold text-gray-900 dark:text-white">{{ $siteTitle }}</span>
                    @endif
                </a>

                {{-- Right Actions --}}
                <div class="flex items-center gap-3">
                    {{-- Dark Mode Toggle (Same as main site header) --}}
                    <button @click="darkMode = darkMode === 'dark' ? 'light' : 'dark'"
                            type="button"
                            class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 flex items-center justify-center transition-colors"
                            :aria-label="darkMode === 'dark' ? 'Aydınlık moda geç' : 'Karanlık moda geç'">
                        <i class="fa-light fa-moon text-lg text-gray-600 dark-mode-icon-moon"></i>
                        <i class="fa-regular fa-sun-bright text-lg text-yellow-500 dark-mode-icon-sun"></i>
                    </button>

                    {{-- Ana Sayfa Link --}}
                    <a href="/" class="hidden sm:flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span>Ana Sayfa</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="flex-1 flex items-center justify-center p-4 py-12">
        <div class="w-full max-w-md">
            {{-- Auth Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 border border-gray-200 dark:border-gray-700">
                @yield('content')
            </div>

            {{-- Footer Links --}}
            <div class="text-center mt-6">
                @yield('footer-links')
            </div>
        </div>
    </main>

    {{-- Minimal Footer --}}
    <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 py-6">
        <div class="container mx-auto px-4 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                &copy; {{ date('Y') }} {{ $siteName }}. Tüm hakları saklıdır.
            </p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
