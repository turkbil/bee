<!DOCTYPE html>
@php
    $currentLocale = app()->getLocale();
    $isRtl = in_array($currentLocale, ['ar', 'he', 'fa', 'ur']) ? 'rtl' : 'ltr';
@endphp
<html lang="{{ $currentLocale }}" dir="{{ $isRtl }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO Meta Tags --}}
    <x-seo-meta />

    {{-- Favicon --}}
    @php $favicon = setting('site_favicon'); @endphp
    @if($favicon && $favicon !== 'Favicon yok')
        <link rel="icon" type="image/x-icon" href="{{ cdn($favicon) }}">
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif

    {{-- PWA Manifest --}}
    <link rel="manifest" href="{{ route('manifest') }}">

    {{-- Tailwind CSS --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="{{ asset('assets/libs/fontawesome-pro@7.1.0/css/all.css') }}">

    {{-- Livewire Styles --}}
    @livewireStyles

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('head')
    @stack('styles')
</head>
<body>
