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
<html lang="{{ app()->getLocale() }}" x-data="muzibuApp()" x-init="init()" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Muzibu - İşletmenize Yasal ve Telifsiz Müzik')</title>

    {{-- Tailwind CSS CDN (dev mode) --}}
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio"></script>
    <script>
        // Suppress CDN production warning
        if (typeof tailwind !== 'undefined' && tailwind.config) {
            tailwind.config.corePlugins = { preflight: true };
        }

        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        muzibu: {
                            coral: '#ff7f50',
                            'coral-light': '#ff9770',
                            'coral-dark': '#ff6a3d',
                            black: '#000000',
                            dark: '#121212',
                            gray: '#181818',
                            'gray-light': '#282828',
                            'text-gray': '#b3b3b3',
                        },
                        // Legacy Spotify color aliases (backward compatibility)
                        spotify: {
                            green: '#ff7f50', // Maps to muzibu-coral
                            coral: '#ff7f50',
                            'coral-light': '#ff9770',
                            black: '#000000',
                            dark: '#121212',
                            gray: '#181818',
                            'gray-light': '#282828',
                            'text-gray': '#b3b3b3',
                        }
                    },
                    animation: {
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'float': 'float 6s ease-in-out infinite',
                        'slide-up': 'slideUp 0.3s ease-out',
                        'fade-in': 'fadeIn 0.4s ease-out',
                        'scale-in': 'scaleIn 0.2s ease-out',
                        'gradient': 'gradient 3s ease infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-10px)' },
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(10px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        },
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        scaleIn: {
                            '0%': { transform: 'scale(0.95)', opacity: '0' },
                            '100%': { transform: 'scale(1)', opacity: '1' },
                        },
                        gradient: {
                            '0%, 100%': { backgroundPosition: '0% 50%' },
                            '50%': { backgroundPosition: '100% 50%' },
                        }
                    }
                }
            }
        };
    </script>

    {{-- FontAwesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    {{-- Alpine.js provided by Livewire --}}

    {{-- Audio Libraries --}}
    <script src="https://cdn.jsdelivr.net/npm/hls.js@1.4.12/dist/hls.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/howler@2.2.4/dist/howler.min.js"></script>

    @livewireStyles

    {{-- Custom Styles --}}
    <link rel="stylesheet" href="{{ asset('themes/muzibu/css/spotify-layout.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('themes/muzibu/css/muzibu-custom.css') }}?v={{ time() }}">

    @yield('styles')
</head>
<body class="bg-black text-white">
    {{-- Hidden Audio Elements --}}
    <audio id="hlsAudio" x-ref="hlsAudio" class="hidden"></audio>
    <audio id="hlsAudioNext" class="hidden"></audio>

    {{-- Main App Grid - Responsive Sidebar Width (ONLY right sidebar width changes) --}}
    <div class="grid grid-rows-[64px_1fr_90px] 2xl:grid-cols-[220px_1fr_480px] xl:grid-cols-[220px_1fr_380px] lg:grid-cols-[220px_1fr] grid-cols-1 h-screen">
        @include('themes.muzibu.components.header')
        @include('themes.muzibu.components.sidebar-left')
        @include('themes.muzibu.components.main-content')
        @include('themes.muzibu.components.sidebar-right')
        @include('themes.muzibu.components.player')
        @include('themes.muzibu.components.queue-overlay')
        @include('themes.muzibu.components.lyrics-overlay')
        @include('themes.muzibu.components.keyboard-shortcuts-overlay')
        @include('themes.muzibu.components.bottom-nav')
    </div>

    {{-- Auth Modal - SPA-friendly (x-teleport) --}}
    @include('themes.muzibu.components.auth-modal')

    {{-- Create Playlist Modal --}}
    <x-muzibu.create-playlist-modal />

    {{-- Play Limits Modals --}}
    @include('themes.muzibu.components.play-limits-modals')
    {{-- Device limit modal devre dışı - Backend handlePostLoginDeviceLimit() otomatik hallediyor --}}
    {{-- @include('themes.muzibu.components.device-limit-modal') --}}

    {{-- Session Check --}}
    @include('themes.muzibu.components.session-check')

    {{-- 🎯 MODULAR JAVASCRIPT ARCHITECTURE --}}

    {{-- 1. Core Utilities (önce yükle - diğerleri bağımlı) --}}
    <script src="{{ asset('themes/muzibu/js/player/core/safe-storage.js') }}?v={{ time() }}"></script>

    {{-- 2. Alpine Store --}}
    <script src="{{ asset('themes/muzibu/js/muzibu-store.js') }}?v={{ time() }}"></script>

    {{-- 3. Player Features (modular - player-core bunları spread eder) --}}
    <script src="{{ asset('themes/muzibu/js/player/features/favorites.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('themes/muzibu/js/player/features/auth.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('themes/muzibu/js/player/features/keyboard.js') }}?v={{ time() }}"></script>

    {{-- 4. Player Core (en son - features'ı spread eder) --}}
    <script src="{{ asset('themes/muzibu/js/player/core/player-core.js') }}?v={{ time() }}"></script>

    {{-- 5. Utils --}}
    <script src="{{ asset('themes/muzibu/js/utils/muzibu-cache.js') }}?v={{ time() }}"></script>

    {{-- 6. UI Components --}}
    <script src="{{ asset('themes/muzibu/js/ui/muzibu-toast.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('themes/muzibu/js/ui/muzibu-theme.js') }}?v={{ time() }}"></script>

    <script>
        // 🔇 Suppress storage access errors (browser privacy/extension related)
        window.addEventListener('unhandledrejection', (event) => {
            if (event.reason?.message?.includes('Access to storage is not allowed')) {
                event.preventDefault(); // Suppress console error
            }
        });

        // Config for Alpine.js
        window.muzibuPlayerConfig = {
            lang: @json(tenant_lang('player')),
            frontLang: @json(tenant_lang('front')),
            isLoggedIn: {{ auth()->check() ? 'true' : 'false' }},
            currentUser: @if(auth()->check()) {
                id: {{ auth()->user()->id }},
                name: "{{ auth()->user()->name }}",
                email: "{{ auth()->user()->email }}",
                is_premium: {{ auth()->user()->isPremium() ? 'true' : 'false' }}
            } @else null @endif,
            todayPlayedCount: {{ auth()->check() ? auth()->user()->getTodayPlayedCount() : 0 }},
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
