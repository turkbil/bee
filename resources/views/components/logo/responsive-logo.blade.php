{{--
    Responsive Logo Component

    Props:
    - $logos: Logo bilgileri array (LogoService::getLogos() çıktısı)
    - $baseClass: Temel CSS class (default: 'h-8 w-auto')
    - $priority: fetchpriority (high/low)
    - $location: Logo konumu (header/footer)
--}}

@php
    $currentLocale = app()->getLocale();
    $defaultLocale = get_tenant_default_locale();
    $homeUrl = $currentLocale === $defaultLocale ? url('/') : url('/' . $currentLocale);
    $isFooter = ($location ?? 'header') === 'footer';

    // Footer için adaptive class ekle (dark mode'da beyaz filtre)
    $adaptiveClass = $isFooter && $logos['fallback_mode'] === 'light_only' ? 'logo-footer-adaptive' : '';
@endphp

@if($logos['fallback_mode'] === 'both')
    {{-- DURUM 1: Her iki logo da var - Light/Dark mode geçişi --}}
    @if($isFooter)
        {{-- Footer: Link olmadan sadece logo --}}
        <div class="inline-flex items-center">
            <img src="{{ $logos['light_logo_url'] }}"
                 alt="{{ $logos['site_title'] }}"
                 width="160"
                 height="48"
                 class="{{ $baseClass }} block dark:hidden"
                 fetchpriority="{{ $priority }}">
            <img src="{{ $logos['dark_logo_url'] }}"
                 alt="{{ $logos['site_title'] }}"
                 width="160"
                 height="48"
                 class="{{ $baseClass }} hidden dark:block"
                 fetchpriority="{{ $priority }}">
        </div>
    @else
        {{-- Header: Link ile wrap edilmiş --}}
        <a href="{{ $homeUrl }}"
           class="inline-flex items-center"
           aria-label="{{ $logos['site_title'] }} - Ana Sayfa">
            <img src="{{ $logos['light_logo_url'] }}"
                 alt="{{ $logos['site_title'] }}"
                 width="160"
                 height="48"
                 class="{{ $baseClass }} block dark:hidden"
                 fetchpriority="{{ $priority }}">
            <img src="{{ $logos['dark_logo_url'] }}"
                 alt="{{ $logos['site_title'] }}"
                 width="160"
                 height="48"
                 class="{{ $baseClass }} hidden dark:block"
                 fetchpriority="{{ $priority }}">
        </a>
    @endif

@elseif($logos['fallback_mode'] === 'light_only')
    {{-- DURUM 2: Sadece normal logo var - Her modda göster --}}
    @if($isFooter)
        <div class="inline-flex items-center">
            <img src="{{ $logos['light_logo_url'] }}"
                 alt="{{ $logos['site_title'] }}"
                 width="160"
                 height="48"
                 class="{{ $baseClass }} {{ $adaptiveClass }}"
                 fetchpriority="{{ $priority }}">
        </div>
    @else
        <a href="{{ $homeUrl }}"
           class="inline-flex items-center"
           aria-label="{{ $logos['site_title'] }} - Ana Sayfa">
            <img src="{{ $logos['light_logo_url'] }}"
                 alt="{{ $logos['site_title'] }}"
                 width="160"
                 height="48"
                 class="{{ $baseClass }}"
                 fetchpriority="{{ $priority }}">
        </a>
    @endif

@elseif($logos['fallback_mode'] === 'dark_only')
    {{-- DURUM 3: Sadece kontrast logo var - Her modda göster --}}
    @if($isFooter)
        <div class="inline-flex items-center">
            <img src="{{ $logos['dark_logo_url'] }}"
                 alt="{{ $logos['site_title'] }}"
                 width="160"
                 height="48"
                 class="{{ $baseClass }}"
                 fetchpriority="{{ $priority }}">
        </div>
    @else
        <a href="{{ $homeUrl }}"
           class="inline-flex items-center"
           aria-label="{{ $logos['site_title'] }} - Ana Sayfa">
            <img src="{{ $logos['dark_logo_url'] }}"
                 alt="{{ $logos['site_title'] }}"
                 width="160"
                 height="48"
                 class="{{ $baseClass }}"
                 fetchpriority="{{ $priority }}">
        </a>
    @endif

@else
    {{-- DURUM 4: Hiç logo yok - Site title göster --}}
    @if($isFooter)
        <h2 class="text-lg font-bold text-gray-900 dark:text-white">
            {{ $logos['site_title'] }}
        </h2>
    @else
        <a href="{{ $homeUrl }}"
           class="inline-flex items-center text-lg sm:text-xl font-bold text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition-colors duration-300"
           aria-label="{{ $logos['site_title'] }} - Ana Sayfa">
            {{ $logos['site_title'] }}
        </a>
    @endif
@endif