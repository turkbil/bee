@php
    $themeService = app('App\Services\ThemeService');
    $activeTheme = $themeService->getActiveTheme();
    $themeName = $activeTheme->folder_name ?? 'blank';
@endphp

@include("themes.{$themeName}.layouts.header")

<!-- Main Content -->
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Dashboard Header -->
    @isset($header)
    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 mb-6 transition-colors duration-300">
        {{ $header }}
    </div>
    @endisset
    
    <!-- Dashboard Content -->
    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 transition-colors duration-300">
        {{ $slot ?? '' }}
    </div>
</main>

@include("themes.{$themeName}.layouts.footer")