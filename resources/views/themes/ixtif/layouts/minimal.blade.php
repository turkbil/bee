<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>

    {{-- Tailwind CSS - Tenant-Aware --}}
    <link rel="stylesheet" href="{{ tenant_css() }}">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="{{ asset('assets/libs/fontawesome-pro@7.1.0/css/all.css') }}">

    {{-- Livewire Styles --}}
    @livewireStyles
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <main class="min-h-screen">
        {{ $slot }}
    </main>

    {{-- Livewire Scripts --}}
    @livewireScripts
</body>
</html>
