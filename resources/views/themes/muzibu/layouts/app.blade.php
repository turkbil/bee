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

    {{-- User Auth for Frontend JS --}}
    @auth
        <meta name="user-id" content="{{ auth()->id() }}">
        <meta name="user-email" content="{{ auth()->user()->email }}">
    @endauth

    {{-- Device Limit Session Flash --}}
    @if (session('device_limit_exceeded'))
        <meta name="device-limit-exceeded" content="true">
        <meta name="device-limit" content="{{ session('device_limit', 1) }}">
        <meta name="active-device-count" content="{{ session('active_device_count', 2) }}">
    @endif

    <title>@yield('title', 'Muzibu - İşletmenize Yasal ve Telifsiz Müzik')</title>

    {{-- Tailwind CSS - Tenant Aware (tenant-1001.css) --}}
    <link rel="stylesheet" href="{{ tenant_css() }}">

    {{-- FontAwesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    {{-- Alpine.js provided by Livewire --}}

    {{-- Audio Libraries --}}
    <script src="https://cdn.jsdelivr.net/npm/hls.js@1.4.12/dist/hls.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/howler@2.2.4/dist/howler.min.js"></script>

    {{-- ⚡ instant.page DISABLED - Conflicts with SPA Router (causes double navigation) --}}
    {{-- <script src="//instant.page/5.2.0" type="module" data-intensity="hover" data-delay="50"></script> --}}

    @livewireStyles

    {{-- PWA Manifest --}}
    <link rel="manifest" href="{{ route('manifest') }}">

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

        /* Keyboard Feedback Notification */
        .keyboard-feedback {
            position: fixed;
            bottom: 100px;
            right: 20px;
            background: rgba(0, 0, 0, 0.9);
            backdrop-filter: blur(10px);
            color: white;
            padding: 12px 20px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            z-index: 9999;
            pointer-events: none;
            opacity: 0;
            transform: translateY(10px);
            transition: opacity 0.2s ease, transform 0.2s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
        }

        .keyboard-feedback.show {
            opacity: 1;
            transform: translateY(0);
        }

        @media (max-width: 640px) {
            .keyboard-feedback {
                bottom: 80px;
                right: 10px;
                padding: 10px 16px;
                font-size: 13px;
            }
        }

        /* 🔧 Alpine.js x-cloak: Hide elements until Alpine initializes */
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>
<body class="bg-black text-white overflow-hidden" @play-song.window="playSong($event.detail.songId)">
    {{-- Mobile Menu Overlay - Sidebar açıkken arka planı karartır --}}
    <div class="muzibu-mobile-overlay" onclick="toggleMobileMenu()"></div>

    {{-- Hidden Audio Elements --}}
    <audio id="hlsAudio" x-ref="hlsAudio" class="hidden"></audio>
    <audio id="hlsAudioNext" class="hidden"></audio>

    @php
        // Sağ blok gösterilecek route'lar (music pages + my-playlists)
        $showRightSidebar = in_array(Route::currentRouteName(), [
            'muzibu.home',
            'muzibu.songs.show',
            'muzibu.albums.index',
            'muzibu.albums.show',
            'muzibu.artists.index',
            'muzibu.artists.show',
            'muzibu.playlists.index',
            'muzibu.playlists.show',
            'muzibu.genres.index',
            'muzibu.genres.show',
            'muzibu.sectors.index',
            'muzibu.sectors.show',
            'muzibu.radios.index',
            'muzibu.search',
            'muzibu.favorites',
            'muzibu.my-playlists',
        ]);

        // Grid column classes - sağ blok varsa 3 kolon, yoksa 2 kolon
        $gridCols = $showRightSidebar
            ? 'xl:grid-cols-[220px_1fr_320px] 2xl:grid-cols-[220px_1fr_360px]'
            : 'xl:grid-cols-[220px_1fr] 2xl:grid-cols-[220px_1fr]';
    @endphp

    {{-- Main App Grid - Dynamic columns based on right sidebar visibility --}}
    <div
        id="main-app-grid"
        class="grid grid-rows-[56px_1fr_auto] grid-cols-1 lg:grid-cols-[220px_1fr] {{ $gridCols }} h-[100dvh] gap-0 lg:gap-3 px-0 pb-0 lg:px-3 lg:pb-3"
    >
        @include('themes.muzibu.components.header')
        @include('themes.muzibu.components.sidebar-left')
        @include('themes.muzibu.components.main-content')

        {{-- Right Sidebar - XL+ screens, music pages only --}}
        @if($showRightSidebar)
            <aside class="muzibu-right-sidebar overflow-y-auto rounded-2xl hidden xl:block">
                @include('themes.muzibu.components.sidebar-right')
            </aside>
        @endif

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

    {{-- AI Chat Widget --}}
    @include('themes.muzibu.components.ai-chat-widget')

    {{-- Context Menu System --}}
    @include('themes.muzibu.components.context-menu')
    @include('themes.muzibu.components.rating-modal')
    @include('themes.muzibu.components.playlist-select-modal')

    {{-- 🧪 DEBUG PANEL - Queue & Playback Debugger --}}
    @include('themes.muzibu.components.debug-panel')

    {{-- 🍪 COOKIE CONSENT - Design 2 (Compact Modern) --}}
    @include('themes.muzibu.components.cookie-consent')

    {{-- ⚡ CRITICAL: Livewire MUST load BEFORE Muzibu scripts (Alpine.js dependency) --}}
    @livewireScripts

    @once
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
    <script src="{{ asset('themes/muzibu/js/player/features/spa-router.js') }}?v={{ filemtime(public_path('themes/muzibu/js/player/features/spa-router.js')) }}"></script>
    <script src="{{ versioned_asset('themes/muzibu/js/player/features/debug.js') }}"></script>
    <script src="{{ versioned_asset('themes/muzibu/js/player/features/play-helpers.js') }}"></script>

    {{-- 4. Player Core (en son - features'ı spread eder) --}}
    <script src="{{ asset('themes/muzibu/js/player/core/player-core.js') }}?v={{ filemtime(public_path('themes/muzibu/js/player/core/player-core.js')) }}"></script>

    {{-- 5. Utils --}}
    <script src="{{ versioned_asset('themes/muzibu/js/utils/muzibu-cache.js') }}"></script>

    {{-- 6. UI Components --}}
    <script src="{{ versioned_asset('themes/muzibu/js/ui/muzibu-toast.js') }}"></script>
    <script src="{{ versioned_asset('themes/muzibu/js/ui/muzibu-theme.js') }}"></script>

    {{-- AI Chat --}}
    <script src="{{ versioned_asset('themes/muzibu/js/ai/tenant1001-ai-chat.js') }}"></script>

    {{-- 7. 🚀 SPA Router - MODULAR VERSION USED (loaded in line 211 as player feature) --}}
    {{-- OLD STANDALONE ROUTER REMOVED - Duplicate initialization fixed --}}
    @endonce

    <script>
        // 🔇 Suppress storage access errors (browser privacy/extension related)
        window.addEventListener('unhandledrejection', (event) => {
            if (event.reason?.message?.includes('Access to storage is not allowed')) {
                event.preventDefault(); // Suppress console error
            }
        });

        // 🌐 Global Alpine defaults (ReferenceError önleme)
        // Device & Modal
        window.showKeyboardHelp = window.showKeyboardHelp || false;
        window.showDeviceSelectionModal = window.showDeviceSelectionModal || false;
        window.showDeviceLimitWarning = window.showDeviceLimitWarning || false;
        window.showDeviceLimitModal = window.showDeviceLimitModal || false;
        window.deviceTerminateLoading = window.deviceTerminateLoading || false;
        window.activeDevices = window.activeDevices || [];
        window.deviceLimit = window.deviceLimit || 1;

        // Player state
        window.isLoading = window.isLoading || false;
        window.isSongLoading = window.isSongLoading || false;
        window.isPlaying = window.isPlaying || false;
        window.currentSong = window.currentSong || null;
        window.currentTime = window.currentTime || 0;
        window.duration = window.duration || 0;
        window.progressPercent = window.progressPercent || 0;
        window.isLiked = window.isLiked || false;

        // Playback controls
        window.shuffle = window.shuffle || false;
        window.repeatMode = window.repeatMode || 'off';

        // Volume & Audio
        window.volume = window.volume ?? 80;
        window.isMuted = window.isMuted || false;

        // Stream info
        window.currentStreamType = window.currentStreamType || 'hls';
        window.lastFallbackReason = window.lastFallbackReason || null;

        // UI panels & Debug
        window.showLyrics = window.showLyrics || false;
        window.showQueue = window.showQueue || false;
        window.showDebugInfo = window.showDebugInfo || false;

        // Auth (fallback - overwritten by config below)
        window.isLoggedIn = window.isLoggedIn || false;
        window.currentUser = window.currentUser || null;

        // Helper functions
        window.formatTime = window.formatTime || function(sec) {
            const t = Math.max(0, Math.floor(sec || 0));
            const m = Math.floor(t / 60);
            const s = (t % 60).toString().padStart(2, '0');
            return `${m}:${s}`;
        };

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

    {{-- 🎯 Livewire Navigation Hook - Alpine Re-Init --}}
    <script>
        // Livewire SPA navigation sonrası yeni content'teki Alpine component'leri init et
        document.addEventListener('livewire:navigated', () => {
            // Kısa gecikme ile Alpine'in hazır olmasını bekle
            setTimeout(() => {
                if (!window.Alpine) return;

                // Sadece initialize edilmemiş x-data element'lerini bul ve init et
                document.querySelectorAll('[x-data]').forEach(el => {
                    // Eğer element Alpine tarafından initialize edilmemişse
                    if (!el.__x) {
                        try {
                            // Manuel initialize (try-catch ile $nextTick hatasını önle)
                            window.Alpine.initTree(el);
                        } catch (e) {
                            // Hata sessizce yut ($nextTick duplicate hatası)
                            if (!e.message?.includes('redefine')) {
                                console.warn('Alpine init warning:', e.message);
                            }
                        }
                    }
                });
            }, 50);
        });

        // 🎵 Player Store Registration - ULTIMATE FIX (Proxy Pattern - auto-forward everything)
        // Strategy: Use JavaScript Proxy to auto-forward ALL properties/methods to root $data
        document.addEventListener('alpine:initialized', () => {
            const htmlEl = document.querySelector('html');

            // Wait for Alpine to fully initialize the root component
            setTimeout(() => {
                const getRootData = () => {
                    // Try multiple ways to get root data
                    if (htmlEl._x_dataStack && htmlEl._x_dataStack[0]) {
                        return htmlEl._x_dataStack[0];
                    }
                    // Fallback: Alpine might expose it differently
                    return window.Alpine?.$data?.(htmlEl);
                };

                // Create Proxy that forwards everything to root
                const playerProxy = new Proxy({}, {
                    get(target, prop) {
                        const rootData = getRootData();
                        if (!rootData) {
                            console.error('Root data not accessible');
                            return undefined;
                        }

                        const value = rootData[prop];

                        // If it's a function, bind it to root context
                        if (typeof value === 'function') {
                            return value.bind(rootData);
                        }

                        return value;
                    },
                    set(target, prop, value) {
                        const rootData = getRootData();
                        if (rootData) {
                            rootData[prop] = value;
                            return true;
                        }
                        return false;
                    }
                });

                window.Alpine.store('player', playerProxy);
                console.log('✅ Player store = Proxy to root (auto-forward all properties/methods)');
            }, 100); // Small delay to ensure Alpine root is ready
        });

        // Context menu store kontrolü
        document.addEventListener('alpine:initialized', () => {
            if (!window.Alpine.store('contextMenu')) {
                console.error('❌ Context Menu Store not found!');
            }

            // Player store da kontrol et
            if (!window.Alpine.store('player')) {
                console.error('❌ Player Store not found!');
            } else {
                console.log('✅ Player store verified');
            }
        });
    </script>

    @once
    {{-- 🎯 Context Menu Init - SPA Safe --}}
    <script src="{{ versioned_asset('themes/muzibu/js/context-menu/init.js') }}"></script>
    @endonce

    @once
    {{-- 🎯 Alpine helper: horizontalScroll (SPA route changes için global) --}}
    <script>
        document.addEventListener('alpine:init', () => {
            if (!Alpine.data('horizontalScroll')) {
                Alpine.data('horizontalScroll', () => ({
                    scrollContainer: null,
                    scrollInterval: null,
                    init() {
                        this.scrollContainer = this.$refs.scrollContainer;
                    },
                    scrollLeft() {
                        this.scrollContainer?.scrollBy({ left: -400, behavior: 'smooth' });
                    },
                    scrollRight() {
                        this.scrollContainer?.scrollBy({ left: 400, behavior: 'smooth' });
                    },
                    startAutoScroll(direction) {
                        this.scrollInterval = setInterval(() => {
                            this.scrollContainer?.scrollBy({ left: direction === 'right' ? 20 : -20 });
                        }, 50);
                    },
                    stopAutoScroll() {
                        if (this.scrollInterval) {
                            clearInterval(this.scrollInterval);
                            this.scrollInterval = null;
                        }
                    }
                }));
            }
        });
    </script>
    @endonce

    {{-- PWA Service Worker Registration --}}
    <x-pwa-registration />

    @yield('scripts')
</body>
</html>
@endif
