<!DOCTYPE html>
@php
    $currentLocale = app()->getLocale();
    $isRtl = in_array($currentLocale, ['ar', 'he', 'fa', 'ur']) ? 'rtl' : 'ltr';
    $siteTitle = setting('site_title', setting('site_name', config('app.name')));
@endphp
<html lang="{{ $currentLocale }}" dir="{{ $isRtl }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $siteTitle }} - {{ $pageTitle ?? 'Ödeme' }}</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="/favicon.ico">

    {{-- Tailwind CSS - Tenant-Aware --}}
    <link rel="stylesheet" href="{{ tenant_css() }}">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="{{ asset('assets/libs/fontawesome-pro@7.1.0/css/all.css') }}">

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
<body class="bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    <main class="min-h-screen">
        {{ $slot }}
        @yield('content')
    </main>

    {{-- Livewire Scripts --}}
    @livewireScripts

    @stack('scripts')
</body>
</html>
