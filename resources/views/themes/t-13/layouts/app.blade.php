<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <x-seo-meta />
    <link rel="manifest" href="{{ route('manifest') }}">

    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Alpine.js --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- FontAwesome --}}
    <link rel="stylesheet" href="{{ asset('assets/libs/fontawesome-pro@7.1.0/css/all.min.css') }}">

    <style>[x-cloak] { display: none !important; }</style>
    @stack('styles')
</head>
<body class="bg-white text-gray-900">
    @include('themes.t-13.layouts.header')

    <main>
        @yield('content')
        @yield('module_content')
    </main>

    @include('themes.t-13.layouts.footer')

    @stack('scripts')
    <x-pwa-registration />
</body>
</html>