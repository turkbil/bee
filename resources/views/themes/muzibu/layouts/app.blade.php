@php
    // 🔐 Şifre Koruması - DEVRE DIŞI
    // $constructionPassword = 'nn';
    // $cookieName = 'mzb_auth_' . tenant('id');
    // $cookieValue = md5($constructionPassword . 'salt2024');
    // $isAuthenticated = isset($_COOKIE[$cookieName]) && $_COOKIE[$cookieName] === $cookieValue;
    $isAuthenticated = true; // ✅ ŞİFRE KORUMASINI KALDIRDIK
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

    {{-- Device Limit Session Flash --}}
    @if (session('device_limit_exceeded'))
        <meta name="device-limit-exceeded" content="true">
        <meta name="device-limit" content="{{ session('device_limit', 1) }}">
        <meta name="active-device-count" content="{{ session('active_device_count', 2) }}">
    @endif

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

    {{-- ⚡ instant.page v5.2.0 - Ultra Fast Prefetch (hover + 50ms delay) --}}
    <script src="//instant.page/5.2.0" type="module" data-intensity="hover" data-delay="50"></script>

    @livewireStyles

    {{-- Custom Styles --}}
    <link rel="stylesheet" href="{{ versioned_asset('themes/muzibu/css/muzibu-layout.css') }}">
    <link rel="stylesheet" href="{{ versioned_asset('themes/muzibu/css/muzibu-custom.css') }}">

    @yield('styles')

    <style>
        /* Grid Layout Fix - Prevent Shrinking */
        html, body {
            width: 100vw;
            max-width: 100vw;
            overflow-x: hidden;
            position: relative;
        }

        /* Main Grid Container */
        #main-app-grid {
            width: 100vw;
            max-width: 100vw;
            min-width: 100vw;
        }

        /* Prevent content shrinking on mobile menu open */
        @media (max-width: 1023px) {
            #main-app-grid {
                position: fixed;
                left: 0;
                right: 0;
            }
        }
    </style>
</head>
<body class="bg-black text-white overflow-hidden" @play-song.window="playSong($event.detail.songId)">
    {{-- Mobile Menu Overlay - Sidebar açıkken arka planı karartır --}}
    <div class="muzibu-mobile-overlay" onclick="toggleMobileMenu()"></div>

    {{-- Hidden Audio Elements --}}
    <audio id="hlsAudio" x-ref="hlsAudio" class="hidden"></audio>
    <audio id="hlsAudioNext" class="hidden"></audio>

    {{-- Main App Grid - Right sidebar on XL+ screens (all pages) --}}
    <div
        id="main-app-grid"
        class="grid grid-rows-[56px_1fr_auto] grid-cols-1 lg:grid-cols-[220px_1fr] xl:grid-cols-[220px_1fr_320px] 2xl:grid-cols-[220px_1fr_360px] h-[100dvh] gap-0 lg:gap-3 px-0 pb-0 lg:px-3 lg:pb-3"
    >
        @include('themes.muzibu.components.header')
        @include('themes.muzibu.components.sidebar-left')
        @include('themes.muzibu.components.main-content')

        {{-- Right Sidebar - XL+ screens, all pages --}}
        <aside class="muzibu-right-sidebar overflow-y-auto rounded-2xl hidden xl:block">
            @include('themes.muzibu.components.sidebar-right')
        </aside>

        @include('themes.muzibu.components.player')
        @include('themes.muzibu.components.queue-overlay')
        @include('themes.muzibu.components.lyrics-overlay')
        @include('themes.muzibu.components.keyboard-shortcuts-overlay')
        @include('themes.muzibu.components.loading-overlay')
    </div>

    {{-- Auth Modal - REMOVED: Users now go to /login and /register pages directly --}}

    {{-- 🔐 NEW DEVICE LIMIT SYSTEM (User chooses what to do) --}}
    @include('themes.muzibu.components.device-limit-warning-modal')
    @include('themes.muzibu.components.device-selection-modal')

    {{-- Create Playlist Modal --}}
    <x-muzibu.create-playlist-modal />

    {{-- Play Limits Modals - DEVRE DIŞI (3 şarkı limiti kaldırıldı) --}}
    {{-- @include('themes.muzibu.components.play-limits-modals') --}}

    {{-- Session Check --}}
    @include('themes.muzibu.components.session-check')

    {{-- Context Menu System --}}
    @include('themes.muzibu.components.context-menu')
    @include('themes.muzibu.components.rating-modal')
    @include('themes.muzibu.components.playlist-select-modal')

    {{-- 🎯 MODULAR JAVASCRIPT ARCHITECTURE --}}

    {{-- 1. Core Utilities (önce yükle - diğerleri bağımlı) --}}
    <script src="{{ versioned_asset('themes/muzibu/js/player/core/safe-storage.js') }}"></script>

    {{-- 2. Alpine Store --}}
    <script src="{{ versioned_asset('themes/muzibu/js/muzibu-store.js') }}"></script>

    {{-- 3. Player Features (modular - player-core bunları spread eder) --}}
    <script src="{{ versioned_asset('themes/muzibu/js/player/features/favorites.js') }}"></script>
    <script src="{{ versioned_asset('themes/muzibu/js/player/features/auth.js') }}"></script>
    <script src="{{ versioned_asset('themes/muzibu/js/player/features/keyboard.js') }}"></script>
    <script src="{{ versioned_asset('themes/muzibu/js/player/features/api.js') }}"></script>
    <script src="{{ versioned_asset('themes/muzibu/js/player/features/session.js') }}"></script>
    <script src="{{ versioned_asset('themes/muzibu/js/player/features/spa-router.js') }}"></script>

    {{-- 4. Player Core (en son - features'ı spread eder) --}}
    <script src="{{ versioned_asset('themes/muzibu/js/player/core/player-core.js') }}"></script>

    {{-- 5. Utils --}}
    <script src="{{ versioned_asset('themes/muzibu/js/utils/muzibu-cache.js') }}"></script>

    {{-- 6. UI Components --}}
    <script src="{{ versioned_asset('themes/muzibu/js/ui/muzibu-toast.js') }}"></script>
    <script src="{{ versioned_asset('themes/muzibu/js/ui/muzibu-theme.js') }}"></script>

    {{-- 7. 🚀 SPA Router (Alpine Store'dan sonra yükle) --}}
    <script src="{{ versioned_asset('themes/muzibu/js/router/muzibu-router.js') }}"></script>

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
            currentUser: @if(auth()->check())
                @php
                    $user = auth()->user();
                    $subscription = $user->subscriptions()
                        ->whereIn('status', ['active', 'trial'])
                        ->where(function($q) {
                            $q->whereNull('current_period_end')
                              ->orWhere('current_period_end', '>', now());
                        })
                        ->first();

                    $isTrial = $subscription
                        && $subscription->has_trial
                        && $subscription->trial_ends_at
                        && $subscription->trial_ends_at->isFuture();

                    $trialEndsAt = $isTrial ? $subscription->trial_ends_at->toIso8601String() : null;
                    $subscriptionEndsAt = $subscription && $subscription->current_period_end
                        ? $subscription->current_period_end->toIso8601String()
                        : null;

                    // 🔥 Device limit (backend'den al - 3-tier hierarchy)
                    $deviceService = app(\Modules\Muzibu\App\Services\DeviceService::class);
                    $deviceLimit = $deviceService->getDeviceLimit($user);
                @endphp
                {
                id: {{ $user->id }},
                name: "{{ $user->name }}",
                email: "{{ $user->email }}",
                is_premium: {{ $user->isPremiumOrTrial() ? 'true' : 'false' }},
                is_trial: {{ $isTrial ? 'true' : 'false' }},
                trial_ends_at: {!! $trialEndsAt ? '"' . $trialEndsAt . '"' : 'null' !!},
                subscription_ends_at: {!! $subscriptionEndsAt ? '"' . $subscriptionEndsAt . '"' : 'null' !!}
            }
            @else
                null
            @endif,
            {{-- todayPlayedCount kaldırıldı - 3 şarkı limiti devre dışı --}}
            tenantId: {{ tenant('id') }},
            // 🔥 Config values (Muzibu module)
            @if(auth()->check())
                deviceLimit: {{ $deviceLimit ?? 1 }},
            @else
                deviceLimit: 1,
            @endif
            sessionPollingInterval: {{ config('muzibu.session.polling_interval', 30000) }}
        };

        // 🔐 CSRF Token Auto-Renewal (419 hatası önleme)
        if (typeof axios !== 'undefined') {
            // Axios CSRF interceptor
            axios.interceptors.response.use(
                response => response,
                async error => {
                    // CSRF token mismatch (419)
                    if (error.response?.status === 419) {
                        console.warn('🔐 CSRF token expired, refreshing...');

                        try {
                            // Yeni token al
                            await axios.get('/sanctum/csrf-cookie');

                            // Meta tag güncelle
                            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                            if (token) {
                                axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
                            }

                            // Orijinal isteği tekrar gönder
                            return axios(error.config);
                        } catch (refreshError) {
                            console.error('❌ CSRF token refresh failed:', refreshError);
                            return Promise.reject(error);
                        }
                    }
                    return Promise.reject(error);
                }
            );

            // Initial CSRF token setup
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (token) {
                axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
                axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
            }
        }

        // Mobile Menu Toggle
        function toggleMobileMenu() {
            const sidebar = document.getElementById('leftSidebar');
            const overlay = document.querySelector('.muzibu-mobile-overlay');
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        }
    </script>

    @livewireScripts

    {{-- 🎯 Livewire Navigation Hook - Alpine Re-Init --}}
    <script>
        // Livewire navigation sonrası Alpine'i re-initialize et
        document.addEventListener('livewire:navigated', () => {
            console.log('🔄 Livewire navigated - Re-initializing Alpine...');

            // Alpine.js re-init için kısa bir gecikme
            setTimeout(() => {
                if (window.Alpine) {
                    try {
                        // Yöntem 1: SPA content wrapper'daki tüm element'leri init et
                        const spaContent = document.querySelector('.spa-content-wrapper');
                        if (spaContent) {
                            window.Alpine.initTree(spaContent);
                        }

                        // Yöntem 2: Tüm yeni x-data element'leri manuel init et
                        document.querySelectorAll('[x-data]').forEach(el => {
                            if (!el.__x) {
                                window.Alpine.initTree(el);
                            }
                        });

                        // Yöntem 3: Context menu event'lerini manuel ekle (fallback) - ARTIK GEREKSİZ
                        // Native event listener yaklaşımı kullanıyoruz (init.js)
                    } catch (e) {
                        console.error('❌ Alpine re-init error:', e);
                    }
                }
            }, 100);
        });

        // İlk yüklemede context menu store'u kontrol et (sessiz)
        document.addEventListener('alpine:initialized', () => {
            if (!window.Alpine.store('contextMenu')) {
                console.error('❌ Context Menu Store not found!');
            }
        });
    </script>

    {{-- 🎯 Context Menu Init - SPA Safe --}}
    <script src="{{ versioned_asset('themes/muzibu/js/context-menu/init.js') }}"></script>

    @yield('scripts')
</body>
</html>
@endif
