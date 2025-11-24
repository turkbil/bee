@php
    $themeService = app('App\Services\ThemeService');
    $activeTheme = $themeService->getActiveTheme();
    $themeName = $activeTheme->folder_name ?? 'ixtif';
@endphp

@include("themes.{$themeName}.layouts.header")

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

@include("themes.{$themeName}.layouts.footer")