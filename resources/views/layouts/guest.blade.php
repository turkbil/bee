@php
    // Tenant temasını al
    $themeName = tenant()->theme ?? 'simple';

    // Header/footer path'leri
    $headerPath = "themes.{$themeName}.layouts.header";
    $footerPath = "themes.{$themeName}.layouts.footer";

    // Tema header var mı?
    $hasThemeHeader = view()->exists($headerPath);

    // Tema header partial mı (DOCTYPE yok)?
    $needsWrapper = false;
    if ($hasThemeHeader) {
        $headerFile = resource_path("views/" . str_replace('.', '/', $headerPath) . ".blade.php");
        $needsWrapper = !str_contains(file_get_contents($headerFile), '<!DOCTYPE');
    }
@endphp

@if($hasThemeHeader && $needsWrapper)
{{-- Partial header themes (like t-3) - wrap with HTML structure --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        if (localStorage.getItem('darkMode') === 'true' ||
            (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
    <x-seo-meta />
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#fff7ed', 100: '#ffedd5', 200: '#fed7aa', 300: '#fdba74',
                            400: '#fb923c', 500: '#f97316', 600: '#ea580c', 700: '#c2410c',
                            800: '#9a3412', 900: '#7c2d12',
                        }
                    }
                }
            }
        }
    </script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="{{ asset('assets/libs/fontawesome-pro@7.1.0/css/all.min.css') }}" crossorigin="anonymous">
    <style>[x-cloak] { display: none !important; }</style>
    @stack('styles')
</head>
<body x-data="{ darkMode: localStorage.getItem('darkMode') === 'true', mobileMenu: false }"
      x-init="$watch('darkMode', val => { localStorage.setItem('darkMode', val); document.documentElement.classList.toggle('dark', val); })"
      :class="{ 'dark': darkMode }"
      class="bg-white dark:bg-slate-900 text-gray-900 dark:text-white transition-colors duration-300">
    @include($headerPath)
    <main class="py-12 px-4">
        <div class="max-w-md mx-auto">
            {{ $slot }}
        </div>
    </main>
    @include($footerPath)
    @stack('scripts')
</body>
</html>

@elseif($hasThemeHeader)
{{-- Full page themes (like simple) - use their structure --}}
@include($headerPath)
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{ $slot }}
</main>
@include($footerPath)

@else
{{-- FALLBACK: No theme or theme not found - standalone inline auth --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ setting('site_title') ?: config('app.name') }}</title>
    <link rel="stylesheet" href="{{ tenant_css() }}">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="min-h-screen bg-gray-100 flex items-center justify-center p-4"
      x-data="{ darkMode: localStorage.getItem('darkMode') === 'dark' }"
      :class="{ 'dark bg-gray-900': darkMode }">
    <div class="w-full max-w-md">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
            {{ $slot }}
        </div>
        <p class="text-center text-gray-500 text-sm mt-6">
            &copy; {{ date('Y') }} {{ setting('site_title') ?: config('app.name') }}
        </p>
    </div>
</body>
</html>
@endif
