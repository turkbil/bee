@php
    // 🔐 Şifre Koruması
    $constructionPassword = 'nn';
    $cookieName = 'mzb_auth_' . tenant('id');
    $cookieValue = md5($constructionPassword . 'salt2024');
    $isAuthenticated = isset($_COOKIE[$cookieName]) && $_COOKIE[$cookieName] === $cookieValue;
@endphp

@if(!$isAuthenticated)
    @include('themes.muzibu.password-protection')
@else
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" x-data="muzibuApp()" x-init="init()">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Muzibu - İşletmenize Yasal ve Telifsiz Müzik')</title>

    {{-- Spotify Layout CSS --}}
    <link rel="stylesheet" href="{{ asset('themes/muzibu/css/spotify-layout.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    {{-- Player Libraries --}}
    <script src="https://cdn.jsdelivr.net/npm/hls.js@1.4.12/dist/hls.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/howler@2.2.4/dist/howler.min.js"></script>

    @livewireStyles
    @yield('styles')
</head>
<body>
    {{-- Mobile Overlay --}}
    <div class="muzibu-mobile-overlay" @click="document.getElementById('leftSidebar').classList.remove('active'); $el.classList.remove('active')"></div>

    {{-- Hidden Audio for HLS --}}
    <audio id="hlsAudio" x-ref="hlsAudio" style="display: none;"></audio>
    <audio id="hlsAudioNext" style="display: none;"></audio>

    {{-- Main App Container --}}
    <div class="muzibu-app">
        @include('themes.muzibu.components.header')
        @include('themes.muzibu.components.sidebar-left')
        @include('themes.muzibu.components.main-content')
        @include('themes.muzibu.components.sidebar-right')
        @include('themes.muzibu.components.player')
        @include('themes.muzibu.components.bottom-nav')
    </div>

    {{-- Auth Modal --}}
    {{-- @include('themes.muzibu.components.auth-modal') --}}

    {{-- Play Limits Modals --}}
    @include('themes.muzibu.components.play-limits-modals')
    @include('themes.muzibu.components.device-limit-modal')

    {{-- Session Check --}}
    @include('themes.muzibu.components.session-check')

    {{-- Player JavaScript --}}
    <script src="{{ asset('themes/muzibu/js/player/spotify-player.js') }}?v={{ time() }}"></script>

    <script>
        // Config for Alpine.js
        window.muzibuPlayerConfig = {
            lang: @json(tenant_lang('player')),
            frontLang: @json(tenant_lang('front')),
            isLoggedIn: {{ auth()->check() ? 'true' : 'false' }},
            currentUser: @json(auth()->user()),
            tenantId: {{ tenant('id') }}
        };

        // Mobile Menu Toggle
        function toggleMobileMenu() {
            const sidebar = document.getElementById('leftSidebar');
            const overlay = document.querySelector('.muzibu-mobile-overlay');
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        }
    </script>

    @livewireScripts
    @yield('scripts')
</body>
</html>
@endif
