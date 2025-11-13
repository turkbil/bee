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
<html lang="{{ $currentLocale }}" dir="{{ $isRtl }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $siteTitle }} - Ödeme</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="/favicon.ico">

    {{-- Tailwind CSS --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ now()->timestamp }}" media="all">

    {{-- Font Awesome Pro --}}
    <link rel="stylesheet" href="{{ asset('assets/libs/fontawesome-pro@7.1.0/css/all.css') }}" media="all">

    {{-- Livewire Styles --}}
    @livewireStyles

    @stack('styles')
</head>

<body class="font-sans antialiased min-h-screen bg-gray-50">
    {{-- Minimal Compact Header --}}
    <header class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3">
            <div class="flex items-center justify-between">
                {{-- Logo --}}
                @if($logos['has_light'])
                    <a href="{{ $homeUrl }}" class="flex items-center">
                        <img src="{{ $logos['light_logo_url'] }}"
                             alt="{{ $siteTitle }}"
                             class="h-8 w-auto object-contain"
                             title="{{ $siteTitle }}">
                    </a>
                @elseif($logos['has_dark'])
                    <a href="{{ $homeUrl }}" class="flex items-center">
                        <img src="{{ $logos['dark_logo_url'] }}"
                             alt="{{ $siteTitle }}"
                             class="h-8 w-auto object-contain"
                             title="{{ $siteTitle }}">
                    </a>
                @else
                    <a href="{{ $homeUrl }}" class="text-lg font-bold text-gray-900">
                        {{ $siteTitle }}
                    </a>
                @endif

                {{-- Güvenli Ödeme Badge --}}
                <div class="flex items-center gap-2 text-green-600">
                    <i class="fa-solid fa-lock text-sm"></i>
                    <span class="text-xs font-medium hidden sm:inline">Güvenli Ödeme</span>
                </div>
            </div>
        </div>
    </header>

    {{-- Main Content - No padding top --}}
    <main>
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    {{-- Minimal Footer --}}
    <footer class="bg-white border-t border-gray-200 py-3 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 text-center">
            <p class="text-xs text-gray-500">
                © {{ date('Y') }} {{ $siteTitle }}
            </p>
        </div>
    </footer>

    {{-- Livewire Scripts --}}
    @livewireScripts

    @stack('scripts')
</body>
</html>
