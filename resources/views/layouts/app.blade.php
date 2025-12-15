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

<!-- Main Content -->
<main class="py-8">
    <div class="container mx-auto px-4 sm:px-4 md:px-2">
        <!-- Dashboard Header -->
        @isset($header)
        <div class="profile-header-glass bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 mb-6 transition-colors duration-300">
            {{ $header }}
        </div>
        @endisset

        <!-- Dashboard Content -->
        {{ $slot ?? '' }}
    </div>
</main>

@include($footerPath)
