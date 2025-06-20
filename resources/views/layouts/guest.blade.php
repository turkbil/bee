@php
    $themeService = app('App\Services\ThemeService');
    $activeTheme = $themeService->getActiveTheme();
    $themeName = $activeTheme->folder_name ?? 'blank';
@endphp

@include("themes.{$themeName}.layouts.header")

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{ $slot }}
</main>

@include("themes.{$themeName}.layouts.footer")

@push('scripts')
<script>
    // Auth page global scripts
    console.log('Guest layout loaded with theme:', '{{ $themeName }}');
</script>
@endpush