@php
    // Tenant temasını al (accessor ile)
    $themeName = tenant()->theme ?? 'simple';

    // Tema layout'u var mı kontrol et
    $headerPath = "themes.{$themeName}.layouts.header";
    $footerPath = "themes.{$themeName}.layouts.footer";

    // Fallback to simple if theme doesn't exist
    if (!view()->exists($headerPath)) {
        $themeName = 'simple';
        $headerPath = 'themes.simple.layouts.header';
        $footerPath = 'themes.simple.layouts.footer';
    }
@endphp

@include($headerPath)

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{ $slot }}
</main>

@include($footerPath)