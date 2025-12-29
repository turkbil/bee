/**
 * Muzibu Player - Core Module
 * Main Alpine.js component for music player
 *
 * Dependencies:
 * - safeStorage (from core/safe-storage.js)
 * - muzibuFavorites (from features/favorites.js)
 * - muzibuAuth (from features/auth.js)
 * - muzibuKeyboard (from features/keyboard.js)
 * - MuzibuApi (from features/api.js)
 * - MuzibuSession (from features/session.js)
 * - MuzibuSpaRouter (from features/spa-router.js)
 */

// 🔍 SERVER DEBUG LOG - Kritik bilgileri server'a gönder
function serverLog(action, data = {}) {
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        fetch('/api/muzibu/debug-log', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken || ''
            },
            body: JSON.stringify({ action, ...data, timestamp: new Date().toISOString() })
        }).catch(() => {}); // Sessizce başarısız ol
    } catch (e) {}
}

// 🔍 SCRIPT LOAD LOG - Script yüklendiğini server'a bildir
document.addEventListener('DOMContentLoaded', function() {
    const isMobileSafari = /iPhone|iPad|iPod/.test(navigator.userAgent) && !window.MSStream;
    serverLog('scriptLoaded', {
        version: 'v28dec-0455',
        userAgent: navigator.userAgent.substring(0, 100),
        isMobileSafari: isMobileSafari,
        crossfadeDisabled: isMobileSafari // true = crossfade off
    });
});

function muzibuApp() {
    // Get config from window object (set in blade template)
    const config = window.muzibuPlayerConfig || {};

    return {
        // 🎯 Modular features (spread from separate files)
        ...muzibuFavorites(),
        ...muzibuAuth(),
        ...muzibuKeyboard(),
        ...(window.MuzibuApi || {}),
        ...(window.MuzibuSession || {}),
        ...(window.MuzibuSpaRouter || {}),
        ...(window.debugFeature || {}), // 🧪 Debug feature (showDebugInfo, showDebugPanel)

        // Tenant-specific translations
        lang: config.lang || {},
        frontLang: config.frontLang || {},

        isLoggedIn: config.isLoggedIn || false,
        currentUser: config.currentUser || null,
        todayPlayedCount: config.todayPlayedCount || 0,
        showAuthModal: null,
        showQueue: false,
        showLyrics: false,
        showMobileMenu: false, // 📱 Mobile 3-dots context menu
        showKeyboardHelp: false, // 🎹 Keyboard shortcuts overlay
        progressPercent: 0,
        authLoading: false,
        authError: '',
        authSuccess: '',

        // 🔐 Device Selection Modal State
        showDeviceSelectionModal: false, // Device seçim modalı
        showDeviceLimitWarning: false, // Device limit uyarı modalı (polling için)
        activeDevices: [], // Aktif cihaz listesi
        deviceLimit: 1, // Kullanıcı cihaz limiti
        selectedDeviceIds: [], // Seçilen cihazların session ID'leri (çoklu seçim için array)
        deviceTerminateLoading: false, // Device terminate loading state
        deviceLimitExceeded: false, // 🛑 Device limit aşıldı mı? (playback engelle)
        sessionCheckFailCount: 0, // Session check başarısız deneme sayısı (login sonrası)
        loginForm: {
            email: safeStorage.getItem('remembered_email') || '',
            password: '',
            remember: safeStorage.getItem('remembered_email') ? true : false
        },
        registerForm: {
            name: '',
            email: '',
            phone: '',
            password: '',
            password_confirmation: ''
        },
        forgotForm: { email: '' },
        showPassword: false,
        showLoginPassword: false,
        tenantId: config.tenantId || 2,

        // Modern validation state (real-time blur validation)
        validation: {
            name: { valid: false, checked: false, message: '' },
            email: { valid: false, checked: false, message: '' },
            phone: { valid: false, checked: false, message: '' },
            password: { valid: false, checked: false, message: '' },
            password_confirmation: { valid: false, checked: false, message: '' }
        },
        phoneCountry: {
            code: '+90',
            flag: '🇹🇷',
            name: 'Türkiye',
            placeholder: '5__ ___ __ __',
            format: 'XXX XXX XX XX'
        },
        phoneCountries: [
            { code: '+90', flag: '🇹🇷', name: 'Türkiye', placeholder: '5__ ___ __ __', format: 'XXX XXX XX XX' },
            { code: '+1', flag: '🇺🇸', name: 'Amerika', placeholder: '(___) ___-____', format: '(XXX) XXX-XXXX' },
            { code: '+44', flag: '🇬🇧', name: 'İngiltere', placeholder: '____ ______', format: 'XXXX XXXXXX' },
            { code: '+49', flag: '🇩🇪', name: 'Almanya', placeholder: '___ ________', format: 'XXX XXXXXXXX' },
            { code: '+33', flag: '🇫🇷', name: 'Fransa', placeholder: '_ __ __ __ __', format: 'X XX XX XX XX' },
            { code: '+39', flag: '🇮🇹', name: 'İtalya', placeholder: '___ _______', format: 'XXX XXXXXXX' },
            { code: '+34', flag: '🇪🇸', name: 'İspanya', placeholder: '___ __ __ __', format: 'XXX XX XX XX' },
            { code: '+31', flag: '🇳🇱', name: 'Hollanda', placeholder: '_ ________', format: 'X XXXXXXXX' },
            { code: '+32', flag: '🇧🇪', name: 'Belçika', placeholder: '___ __ __ __', format: 'XXX XX XX XX' },
            { code: '+41', flag: '🇨🇭', name: 'İsviçre', placeholder: '__ ___ __ __', format: 'XX XXX XX XX' },
            { code: '+43', flag: '🇦🇹', name: 'Avusturya', placeholder: '___ ________', format: 'XXX XXXXXXXX' },
            { code: '+7', flag: '🇷🇺', name: 'Rusya', placeholder: '(___) ___-__-__', format: '(XXX) XXX-XX-XX' },
            { code: '+86', flag: '🇨🇳', name: 'Çin', placeholder: '___ ____ ____', format: 'XXX XXXX XXXX' },
            { code: '+81', flag: '🇯🇵', name: 'Japonya', placeholder: '__-____-____', format: 'XX-XXXX-XXXX' },
            { code: '+82', flag: '🇰🇷', name: 'Güney Kore', placeholder: '__-____-____', format: 'XX-XXXX-XXXX' },
            { code: '+971', flag: '🇦🇪', name: 'BAE', placeholder: '__ ___ ____', format: 'XX XXX XXXX' },
            { code: '+966', flag: '🇸🇦', name: 'Suudi Arabistan', placeholder: '__ ___ ____', format: 'XX XXX XXXX' }
        ],
        favorites: [],

        // Loading & UI states - ⚡ PERFORMANCE: Start with false (no initial loading overlay)
        isLoading: false, // Only show when actually loading (SPA navigation)
        isSongLoading: false, // Şarkı yüklenirken spinner
        contentLoaded: true, // Content ready by default
        searchQuery: '',
        searchResults: [],
        searchOpen: false,
        mobileMenuOpen: false,

        // Player states
        isPlaying: false,
        isToggling: false, // 🚫 Debounce flag for togglePlayPause
        shuffle: false,
        repeatMode: 'off',
        currentTime: 0,
        duration: 240,
        volume: parseInt(safeStorage.getItem('volume')) || 100, // Load from localStorage, default 100
        isMuted: false,
        currentSong: null,
        currentContext: null, // 🎯 Play context (playlist/album/genre/sector - for sidebar preview)
        currentFallbackUrl: null, // 🔐 MP3 fallback URL (signed)
        queue: [],
        queueIndex: 0,
        b2bMode: safeStorage.getItem('b2b_mode') === 'true', // 💾 B2B mode: infinite loop
        isLoggingOut: false,
        currentPath: window.location.pathname,
        _initialized: false,
        isDarkMode: safeStorage.getItem('theme') === 'light' ? false : true,
        draggedIndex: null,
        dropTargetIndex: null,
        playTracked: false, // 🎵 Track if current song play has been recorded
        playTrackedAt: 30, // 🎵 Track play after 30 seconds (hit +1, play log)
        sessionPollInterval: null, // 🔐 Device limit polling interval
        showDeviceLimitModal: false, // 🔐 Show device limit exceeded modal

        // Crossfade settings (using Howler.js + HLS.js)
        // 🍎 Mobile Safari'de crossfade çalışmıyor - devre dışı
        crossfadeEnabled: !(/iPhone|iPad|iPod/.test(navigator.userAgent) && !window.MSStream), // Desktop: true, Mobile Safari: false
        crossfadeDuration: window.muzibuPlayerConfig?.crossfadeDuration || 5000, // Config'den al, varsayılan 5 saniye
        fadeOutDuration: 0, // 🚀 INSTANT: No fade, immediate volume changes
        isCrossfading: false,
        crossfadeTimeoutId: null, // 🔧 Crossfade completion timeout (iptal edilebilir)
        crossfadeNextIndex: -1, // 🔧 Crossfade sırasında yeni şarkının index'i
        howl: null, // Current Howler instance (for MP3)
        howlNext: null, // Next song Howler instance for crossfade
        hls: null, // Current HLS.js instance
        hlsNext: null, // Next HLS.js instance for crossfade
        isHlsStream: false, // Whether current stream is HLS
        lastFallbackReason: null, // 🧪 TEST: Why MP3 fallback was triggered
        activeHlsAudioId: 'hlsAudio', // Which HLS audio element is active ('hlsAudio' or 'hlsAudioNext')
        progressInterval: null, // Interval for updating progress
        _fadeAnimation: null, // For requestAnimationFrame fade

        // 🚀 PRELOAD NEXT SONG: HLS instance ile gerçek preload
        _preloadedNext: null, // { songId, hls, audioId, ready } - Preloaded next song info
        _preloadNextInProgress: false, // Preload işlemi devam ediyor mu

        // Computed: Current stream type
        get currentStreamType() {
            if (!this.currentSong) return null;
            return this.isHlsStream ? 'hls' : 'mp3';
        },

        /**
         * 🎨 GET COVER URL: Smart cover URL resolver
         * Handles both media_id (number) and full URL formats
         * @param {string|number} cover - media_id or full URL
         * @param {number} width - thumbnail width
         * @param {number} height - thumbnail height
         */
        getCoverUrl(cover, width = 120, height = 120) {
            if (!cover) return null;

            // If it's a full URL (starts with http), use it directly
            if (typeof cover === 'string' && (cover.startsWith('http') || cover.startsWith('//'))) {
                // If it's already a thumbmaker URL, just return it
                if (cover.includes('thumbmaker')) {
                    return cover;
                }
                // For other URLs, return as-is
                return cover;
            }

            // If it's a media_id (number or numeric string), use thumb endpoint
            if (cover && !isNaN(cover)) {
                return `${window.location.origin}/thumb/${cover}/${width}/${height}`;
            }

            // Fallback: return as-is
            return cover;
        },

        /**
         * 🎨 UPDATE PLAYER COLORS: Şarkıya göre gradient renkleri güncelle
         * color_hash formatı: "hue1,hue2,hue3" (örn: "45,85,125")
         * Fallback: Şarkı başlığından client-side hesaplama
         */
        updatePlayerColors() {
            try {
                if (!this.currentSong) {
                    return;
                }

                let hues = [30, 350, 320]; // Varsayılan (turuncu-kırmızı-pembe)
                let source = 'default';

                // 1. Önce DB'den gelen color_hash'i dene
                if (this.currentSong.color_hash) {
                    const parsed = this.currentSong.color_hash.split(',').map(h => parseInt(h.trim(), 10));
                    if (parsed.length === 3 && parsed.every(h => !isNaN(h))) {
                        hues = parsed;
                        source = 'db';
                    }
                }

                // 2. Yoksa şarkı başlığından client-side hesapla (fallback)
                if (source === 'default') {
                    const title = this.currentSong.song_title?.tr || this.currentSong.song_title?.en ||
                                  this.currentSong.song_title || this.currentSong.title || '';
                    if (title) {
                        hues = this.generateColorHashFromTitle(title);
                        source = 'client';
                    }
                }

                // 🔄 color_hues'u currentSong'a ekle (Alpine reaktivite)
                this.currentSong.color_hues = hues;

                // CSS değişkenlerini güncelle (border gradient için)
                document.documentElement.style.setProperty('--player-hue1', hues[0]);
                document.documentElement.style.setProperty('--player-hue2', hues[1]);
                document.documentElement.style.setProperty('--player-hue3', hues[2]);

            } catch (error) {
                console.error('❌ updatePlayerColors error:', error);
            }
        },

        /**
         * 🎨 Client-side color hash hesaplama (DB'de yoksa fallback)
         * PHP'deki generateColorHash() ile aynı algoritma
         */
        generateColorHashFromTitle(title) {
            const normalizedTitle = title.toLowerCase().trim();
            let hash = 0;
            for (let i = 0; i < normalizedTitle.length; i++) {
                const char = normalizedTitle.charCodeAt(i);
                hash = ((hash << 5) - hash) + char;
                hash = hash & 0xFFFFFFFF; // 32-bit integer
            }
            hash = Math.abs(hash);
            const hue1 = hash % 360;
            const hue2 = (hue1 + 40) % 360;
            const hue3 = (hue1 + 80) % 360;
            return [hue1, hue2, hue3];
        },

        // Get the currently active HLS audio element
        getActiveHlsAudio() {
            if (this.activeHlsAudioId === 'hlsAudioNext') {
                return document.getElementById('hlsAudioNext');
            }
            return document.getElementById('hlsAudio');
        },

        /**
         * 🔐 AUTHENTICATED FETCH: Tüm API çağrılarında 401 kontrolü yapar
         * 401 alırsa kullanıcıyı logout eder veya guest'e mesaj gösterir
         */
        async authenticatedFetch(url, options = {}) {
            const ignoreAuthError = options.ignoreAuthError || false;
            const fetchOptions = {
                credentials: 'include',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
                ...options
            };

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (token) {
                fetchOptions.headers['X-CSRF-TOKEN'] = token;
            }

            const response = await fetch(url, fetchOptions);

            // 🔴 401/419 Unauthorized = Guest user VEYA session terminated/CSRF expired
            if (response.status === 401 || response.status === 419) {
                // Preload vs: auth hatasını sessizce yut (logout tetikleme)
                if (ignoreAuthError) {
                    return null;
                }

                // Tekrar deneme döngüsünü engelle
                if (this._handlingAuthError) {
                    return null;
                }
                this._handlingAuthError = true;

                // 419 veya CSRF expired ise bir kez token yenile ve tekrar dene
                try {
                    const data = await response.json().catch(() => ({}));
                    const isGuest = data.status === 'unauthorized' && data.redirect;

                    if (isGuest) {
                        this.showAuthRequiredModal(data.message || this.frontLang?.auth?.login_required || 'Login required to listen');
                        this._handlingAuthError = false;
                        return null;
                    }

                    if (response.status === 419 || data.error === 'csrf_token_mismatch') {
                        try {
                            const html = await fetch('/', { headers: { 'X-Requested-With': 'XMLHttpRequest' } }).then(r => r.text());
                            const doc = new DOMParser().parseFromString(html, 'text/html');
                            const newToken = doc.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                            if (newToken) {
                                document.querySelector('meta[name="csrf-token"]')?.setAttribute('content', newToken);
                                fetchOptions.headers['X-CSRF-TOKEN'] = newToken;
                                const retry = await fetch(url, fetchOptions);
                                this._handlingAuthError = false;
                                if (retry.ok) {
                                    return retry;
                                }
                            }
                        } catch (_) {}
                    }

                    // 🔐 SESSION TERMINATED: Başka cihazdan giriş yapıldı
                    if (data.force_logout || data.error === 'session_terminated') {
                        this.handleSessionTerminated({ message: data.message, reason: data.reason || null });
                        return null;
                    }
                } catch (e) {
                    // JSON parse hatası veya diğer durum
                }

                // Genel fallback: logout mesajı
                this.handleSessionTerminated({ message: this.frontLang?.messages?.session_terminated || 'Oturumunuz sona erdi, lütfen tekrar giriş yapın.', reason: null });
                return null;
            }

            this._handlingAuthError = false;
            return response;
        },

        init() {
            // ✅ Prevent double initialization (component-level, not window-level)
            if (this._initialized) {
                return;
            }
            this._initialized = true;


            // User already loaded from Laravel backend (no need for API check)

            // ⏱️ DELAYED: Load featured playlists after 300ms (avoid rate limiting)
            setTimeout(() => {
                this.loadFeaturedPlaylists();
            }, 300);

            // Initialize keyboard shortcuts
            this.initKeyboard();

            // ⚡ PERFORMANCE: Show content immediately (no delay!)
            this.isLoading = false;
            this.contentLoaded = true;

            // 🎯 QUEUE CHECKER: Monitor queue and auto-refill (PHASE 4)
            this.startQueueMonitor();

            // ⏱️ SUBSCRIPTION COUNTDOWN: Premium/Trial bitiş süresini takip et
            this.startSubscriptionCountdown();

            // 🎵 BACKGROUND PLAYBACK: Tarayıcı minimize olsa bile çalsın
            this.enableBackgroundPlayback();

            // 🔄 FRESH START: Sayfa yenilenince state temizle (no restore, no auto-save)
            this.clearPlayerState();

            // 🚀 INSTANT QUEUE: Sayfa açılır açılmaz queue yükle (no delay!)
            this.loadInitialQueue();

            // 🔐 SESSION POLLING: Device limit kontrolü (sadece login olunca başlar)
            if (this.isLoggedIn) {
                this.startSessionPolling();
            }

            // 🔐 DEVICE LIMIT WARNING: Check localStorage flag after logout
            // Bu flag sadece başka cihazdan çıkarıldığında (session polling) set edilir
            try {
                const deviceLimitWarning = localStorage.getItem('device_limit_warning');
                if (deviceLimitWarning === 'true') {
                    this.showDeviceLimitWarning = true;
                    localStorage.removeItem('device_limit_warning');
                }
            } catch (e) {
                console.warn('localStorage not available:', e.message);
            }

            // 🔐 DEVICE LIMIT: Check meta tag for session flash (login sonrası limit aşıldıysa)
            // Bu durumda SELECTION MODAL göster (kullanıcı seçim yapsın)
            const deviceLimitMeta = document.querySelector('meta[name="device-limit-exceeded"]');
            if (deviceLimitMeta && deviceLimitMeta.content === 'true') {

                // 🔧 FIX: Selection modal göster, warning modal DEĞİL!
                // Önce cihaz listesini çek (device limit de API'den gelir - 3-tier hierarchy)
                // Backend: 1) User->device_limit 2) SubscriptionPlan->device_limit 3) Setting('auth_device_limit')
                this.fetchActiveDevices().then(() => {
                    // 🔥 FIX: Sadece başka cihaz varsa modal göster
                    // Eğer sadece mevcut cihaz varsa (is_current=true), modal göstermenin anlamı yok
                    const terminableDevices = this.activeDevices.filter(d => !d.is_current);

                    if (terminableDevices.length > 0) {
                        this.showDeviceSelectionModal = true;
                    } else {
                        // Device limit exceeded ama çıkış yapılacak başka cihaz yok
                        // Bu durumda LIFO zaten en eski session'ı silmiş olmalı
                        this.deviceLimitExceeded = false; // Flag'i temizle
                    }
                });
            }

            // 🔐 DEVICE LIMIT: Her sayfa yüklemesinde kontrol et (login olmuş kullanıcılar için)
            // Meta tag yoksa bile, API'den cihaz sayısı ve limiti al, limit aşılmışsa modal göster
            // ⏱️ DELAYED: 600ms sonra kontrol et (avoid rate limiting)
            if (this.isLoggedIn && !deviceLimitMeta) {
                setTimeout(() => {
                    this.checkDeviceLimitOnPageLoad();
                }, 600);
            }

            // 🚀 SPA NAVIGATION: Initialize MuzibuSpaRouter (with prefetch!)
            if (this.initSpaNavigation) {
                this.initSpaNavigation();
            }

            // 🚀 PRELOAD LAST PLAYED: Sayfa yüklenince son şarkıyı hazırla (instant play için)
            // ⏱️ 500ms delay: Sayfa render'ı tamamlansın, sonra preload başlasın
            if (this.isLoggedIn && (this.currentUser?.is_premium || this.currentUser?.is_trial)) {
                setTimeout(() => {
                    this.preloadLastPlayedSong();
                }, 500);
            }
        },

        async loadFeaturedPlaylists() {
            try {
                const response = await fetch('/api/muzibu/playlists/featured');
                const playlists = await response.json();
            } catch (error) {
                console.error('Failed to load playlists:', error);
            }
        },

        // 🎯 PRELOAD: Cache last played song URL for instant playback
        // NOT: HLS instance oluşturmuyoruz (startLoad sorunları önlemek için)
        // Sadece URL cache'liyoruz, play basınca playSongFromQueue yeni HLS oluşturur
        async preloadLastPlayedSong() {
            // 🚫 Skip if not premium (prevent 402 spam)
            if (!this.isLoggedIn || (!this.currentUser?.is_premium && !this.currentUser?.is_trial)) {
                return;
            }

            try {
                let song = null;

                // 1️⃣ Try last-played first
                const response = await fetch('/api/muzibu/songs/last-played');
                if (response.ok) {
                    const data = await response.json();
                    if (data.last_played) {
                        song = data.last_played;
                    }
                }

                // 2️⃣ Fallback: Queue'daki ilk şarkıyı kullan (last-played yoksa)
                if (!song && this.queue && this.queue.length > 0) {
                    song = this.queue[0];
                }

                // 3️⃣ Son çare: Hiç şarkı yoksa çık
                if (!song) {
                    return;
                }

                // Add to queue
                this.queue = [song];
                this.queueIndex = 0;
                this.currentSong = song;

                // Load song stream URL (🔐 401 kontrolü ile)
                const streamResponse = await this.authenticatedFetch(`/api/muzibu/songs/${song.song_id}/stream`);
                if (!streamResponse) return;

                if (!streamResponse.ok) {
                    return;
                }

                const streamData = await streamResponse.json();

                // 🚀 URL'i cache'le (HLS instance oluşturmadan)
                // Play basınca playSongFromQueue bu cache'i kullanarak yeni HLS oluşturur
                if (!this.streamUrlCache) {
                    this.streamUrlCache = new Map();
                }
                this.streamUrlCache.set(song.song_id, {
                    stream_url: streamData.stream_url,
                    stream_type: streamData.stream_type,
                    fallback_url: streamData.fallback_url,
                    preview_duration: streamData.preview_duration,
                    song: streamData.song,
                    cached_at: Date.now()
                });

                // Duration'ı set et (varsa)
                if (streamData.song?.duration_seconds) {
                    this.duration = streamData.song.duration_seconds;
                } else if (song.duration_seconds) {
                    this.duration = song.duration_seconds;
                }

                // 🎨 Merge API song data (color_hash dahil) ve renkleri güncelle
                if (streamData.song) {
                    this.currentSong = { ...this.currentSong, ...streamData.song };
                }
                this.updatePlayerColors();

                this.isPlaying = false;
                this.isSongLoading = false;

            } catch (error) {
                console.error('preloadLastPlayedSong error:', error);
            }
        },

        // 🎯 Favorites functions (toggleFavorite, isFavorite, isLiked) moved to features/favorites.js

        async togglePlayPause() {
            // 🚫 Debounce: İşlem devam ederken tekrar çağrılmasını engelle
            if (this.isToggling) {
                return;
            }
            this.isToggling = true;

            try {
                // 🚫 FRONTEND PREMIUM CHECK: Play yapmadan önce kontrol et
                if (!this.isPlaying) {
                    // Guest kullanıcı → Direkt /register
                    if (!this.isLoggedIn) {
                        this.showToast(this.frontLang?.auth?.login_required || 'Login required to listen', 'warning');
                        setTimeout(() => {
                            window.location.href = '/login';
                        }, 800);
                        return;
                    }

                    // Premium/Trial olmayan üye → Direkt /subscription/plans
                    const isPremiumOrTrial = this.currentUser?.is_premium || this.currentUser?.is_trial;
                    if (!isPremiumOrTrial) {
                        this.showToast(this.frontLang?.auth?.premium_required || 'Premium membership required', 'warning');
                        setTimeout(() => {
                            window.location.href = '/subscription/plans';
                        }, 800);
                        return;
                    }
                }

                // Eğer queue boşsa, rastgele şarkılar yükle
                if (this.queue.length === 0 || !this.currentSong) {
                    await this.playRandomSongs();
                    return;
                }

                const targetVolume = this.isMuted ? 0 : this.volume / 100;

                if (this.isPlaying || this.isSongLoading) {
                    // 🚀 INSTANT PAUSE: No fade
                    // 🔧 FIX: Loading sırasında da durdur
                    this.isSongLoading = false;

                    // 🔧 FIX: Crossfade sırasında pause yapılırsa, önce crossfade'i tamamla
                    // Böylece yeni şarkı aktif olur ve play'e basınca yeni şarkı devam eder
                    if (this.isCrossfading && (this.howlNext || this.hlsNext)) {
                        // 🔧 FIX: Crossfade timeout'unu iptal et (5sn sonra tekrar tetiklenmesini önle)
                        if (this.crossfadeTimeoutId) {
                            clearTimeout(this.crossfadeTimeoutId);
                            this.crossfadeTimeoutId = null;
                        }

                        // 🔧 FIX: Doğru index'i kullan (crossfadeNextIndex, getNextSongIndex değil!)
                        const nextIndex = this.crossfadeNextIndex >= 0 ? this.crossfadeNextIndex : (this.queueIndex + 1);
                        const nextIsHls = this.hlsNext !== null;

                        // Crossfade'i tamamla (yeni şarkı aktif olsun)
                        this.completeCrossfade(nextIndex, nextIsHls);
                        this.crossfadeNextIndex = -1; // Reset
                    }

                    // Şimdi normal pause yap
                    if (this.howl) {
                        this.howl.pause();
                    }

                    if (this.hls) {
                        const audio = this.getActiveHlsAudio();
                        if (audio) {
                            audio.pause();
                        }
                    }

                    // 🔧 FIX: Her zaman TÜM audio element'leri durdur (crossfade durumlarında gerekli)
                    const hlsAudio = document.getElementById('hlsAudio');
                    const hlsAudioNext = document.getElementById('hlsAudioNext');
                    if (hlsAudio) {
                        try { hlsAudio.pause(); } catch(e) {}
                        // 🔧 FIX: Event listener'ları temizle (otomatik başlamayı önle)
                        hlsAudio.ontimeupdate = null;
                        hlsAudio.onended = null;
                    }
                    if (hlsAudioNext) {
                        try { hlsAudioNext.pause(); } catch(e) {}
                        hlsAudioNext.ontimeupdate = null;
                        hlsAudioNext.onended = null;
                    }

                    // 🔧 FIX: Progress interval'i temizle (crossfade tetiklenmesini önle)
                    if (this.progressInterval) {
                        clearInterval(this.progressInterval);
                        this.progressInterval = null;
                    }

                    // State'i sıfırla
                    this.isPlaying = false;
                    this.isCrossfading = false;
                    window.dispatchEvent(new CustomEvent('player:pause'));
                } else {
                    // 🚀 INSTANT PLAY: No fade, direct volume
                    if (this.howl) {
                        this.howl.volume(targetVolume);
                        this.howl.play();
                        this.isPlaying = true;
                        // 🔧 FIX: Start progress tracking if not already started
                        if (!this.progressInterval) {
                            this.startProgressTracking('howler');
                        }
                        window.dispatchEvent(new CustomEvent('player:play', {
                            detail: {
                                songId: this.currentSong?.song_id,
                                isLoggedIn: this.isLoggedIn
                            }
                        }));
                    } else if (this.hls) {
                        const audio = this.getActiveHlsAudio();
                        if (audio) {
                            // 🎵 Resume playback - startLoad() gerekli değil
                            // HLS zaten normal buffer ile çalışıyor (playSongFromQueue oluşturdu)
                            audio.volume = targetVolume;
                            try {
                                await audio.play();
                            } catch (playError) {
                                // Silently catch play() interruptions (race condition)
                                if (playError.name !== 'AbortError') {
                                    console.warn('Play failed:', playError);
                                }
                            }
                            this.isPlaying = true;
                            // 🔧 FIX: Start progress tracking if not already started
                            if (!this.progressInterval) {
                                this.startProgressTracking('hls');
                            }
                            window.dispatchEvent(new CustomEvent('player:play', {
                                detail: {
                                    songId: this.currentSong?.song_id,
                                    isLoggedIn: this.isLoggedIn
                                }
                            }));
                        }
                    } else if (this.isHlsStream && this.currentSong) {
                        // 🍎 Safari Native HLS: this.hls = null ama audio element var
                        // isHlsStream true ise Safari native HLS aktif demektir
                        const audio = this.getActiveHlsAudio();
                        if (audio && audio.src) {
                            audio.volume = targetVolume;
                            try {
                                await audio.play();
                            } catch (playError) {
                                if (playError.name !== 'AbortError') {
                                    console.warn('Safari native play failed:', playError);
                                }
                            }
                            this.isPlaying = true;
                            if (!this.progressInterval) {
                                this.startProgressTracking('hls');
                            }
                            window.dispatchEvent(new CustomEvent('player:play', {
                                detail: {
                                    songId: this.currentSong?.song_id,
                                    isLoggedIn: this.isLoggedIn
                                }
                            }));
                        } else {
                            // Audio element yoksa veya src boşsa yeniden yükle
                            await this.playSongFromQueue(this.queueIndex);
                        }
                    } else if (this.currentSong) {
                        // 🎵 No audio source loaded yet - load and play current song
                        await this.playSongFromQueue(this.queueIndex);
                    }
                }
            } catch (error) {
                console.error('togglePlayPause error:', error);
            } finally {
                // ✅ Reset debounce flag after 300ms
                setTimeout(() => {
                    this.isToggling = false;
                }, 300);
            }
        },

        async playRandomSongs(autoPlay = true) {
            // 🚫 CRITICAL: Premium kontrolü (auto-play engelle)
            if (!this.isLoggedIn) {
                this.showToast(this.frontLang?.auth?.login_required || 'Login required to listen', 'warning');
                setTimeout(() => {
                    window.location.href = '/register';
                }, 800);
                return;
            }

            const isPremiumOrTrial = this.currentUser?.is_premium || this.currentUser?.is_trial;
            if (!isPremiumOrTrial) {
                this.showToast(this.frontLang?.auth?.premium_required || 'Premium membership required', 'warning');
                setTimeout(() => {
                    window.location.href = '/subscription/plans';
                }, 800);
                return;
            }

            try {
                // 🎵 AUTO-START: Queue boşsa Genre'den başla (infinite loop garantisi)

                // ✅ Alpine store check (Livewire navigate sonrası store undefined olabilir)
                const muzibuStore = Alpine.store('muzibu');
                if (!muzibuStore) {
                    console.error('❌ Alpine.store("muzibu") not available yet - Using fallback');
                    await this.fallbackToPopularSongs(autoPlay);
                    return;
                }

                // En popüler genre'yi bul ve oradan başlat
                const genresResponse = await fetch('/api/muzibu/genres');
                const genres = await genresResponse.json();

                if (genres && genres.length > 0) {
                    // İlk genre'yi al (veya rastgele)
                    const firstGenre = genres[0];

                    // Genre context'i ayarla
                    muzibuStore.setPlayContext({
                        type: 'genre',
                        id: firstGenre.genre_id,
                        offset: 0,
                        source: 'auto_start'
                    });

                    // Genre'den şarkıları yükle
                    const songs = await muzibuStore.refillQueue(0, 15);

                    if (songs && songs.length > 0) {
                        this.queue = songs;
                        this.queueIndex = 0;

                        if (autoPlay) {
                            await this.playSongFromQueue(0);
                            const genreTitle = firstGenre.title?.tr || firstGenre.title;
                            this.showToast(`🎵 ${(this.frontLang?.messages?.now_playing || ':title is playing').replace(':title', genreTitle)}`, 'success');
                        } else {
                            // Sadece yükle, çalma (space tuşu için hazır olsun)
                            await this.playSongFromQueue(0, false);
                        }
                    } else {
                        // Fallback: Popular songs
                        await this.fallbackToPopularSongs(autoPlay);
                    }
                } else {
                    // Fallback: Popular songs
                    await this.fallbackToPopularSongs(autoPlay);
                }
            } catch (error) {
                console.error('Failed to start auto-play:', error);
                // Fallback: Popular songs
                await this.fallbackToPopularSongs(autoPlay);
            }
        },

        /**
         * 🔄 Fallback: Genre bulunamazsa popular songs
         */
        async fallbackToPopularSongs(autoPlay = true) {
            try {
                const response = await fetch('/api/muzibu/songs/popular?limit=50');
                const songs = await response.json();

                if (songs.length > 0) {
                    // Shuffle songs
                    const shuffled = songs.sort(() => Math.random() - 0.5);

                    this.queue = shuffled;
                    this.queueIndex = 0;

                    if (autoPlay) {
                        await this.playSongFromQueue(0);
                        this.showToast(this.frontLang?.messages?.popular_songs_playing || 'Popular songs playing!', 'success');
                    } else {
                        // Sadece yükle, çalma
                        await this.playSongFromQueue(0, false);
                    }
                } else {
                    this.showToast(this.frontLang?.messages?.song_not_found || 'Song not found', 'error');
                }
            } catch (error) {
                console.error('Failed to play fallback songs:', error);
                this.showToast(this.frontLang?.messages?.songs_loading_failed || 'Failed to load songs', 'error');
            }
        },

        // 💾 FULL STATE BACKUP: Save complete player state to localStorage
        saveQueueState() {
            // 🛡️ Don't save during state restoration (prevents queue corruption)
            if (this._isRestoringState) {
                return;
            }

            try {
                // ✅ Alpine store check
                const muzibuStore = Alpine.store('muzibu');

                // 🧹 MINIMAL QUEUE SAVE: Sadece current + sonraki 20 şarkıyı kaydet
                // Eski çalınan şarkıları kaydetmeye gerek yok (DB'den yüklenecek)
                const minimalQueue = this.queue.slice(
                    Math.max(0, this.queueIndex - 2), // 2 önceki (geri gitmek için)
                    this.queueIndex + 20 // 20 sonraki
                );
                const adjustedQueueIndex = Math.min(this.queueIndex, 2);

                const state = {
                    queue: minimalQueue, // Minimal queue (max 22 şarkı)
                    queueIndex: adjustedQueueIndex,
                    currentSong: this.currentSong,
                    currentTime: this.currentTime,
                    shuffle: this.shuffle,
                    repeatMode: this.repeatMode,
                    volume: this.volume,
                    isPlaying: this.isPlaying,
                    playContext: muzibuStore?.getPlayContext() || null
                };

                // ✅ localStorage access check (cross-origin/iframe hatası önleme)
                try {
                    safeStorage.setItem('muzibu_full_state', JSON.stringify(state));
                } catch (storageError) {
                    // localStorage access denied (cross-origin, iframe, private mode)
                    console.warn('⚠️ localStorage access denied:', storageError.message);
                }
            } catch (error) {
                console.error('❌ Failed to save state:', error);
            }
        },

        // 💾 FULL STATE RESTORATION: Load complete player state from localStorage
        async loadQueueState() {
            try {
                // 🛡️ Prevent auto-save during state restoration
                this._isRestoringState = true;

                // ✅ localStorage access check
                let saved;
                try {
                    saved = safeStorage.getItem('muzibu_full_state');
                } catch (storageError) {
                    console.warn('⚠️ localStorage access denied:', storageError.message);
                    this._isRestoringState = false;
                    return;
                }

                if (!saved) {
                    this._isRestoringState = false;
                    return;
                }

                const state = JSON.parse(saved);

                // Restore queue and settings
                this.queue = state.queue || [];
                this.queueIndex = state.queueIndex || 0;
                this.currentSong = state.currentSong || null;
                this.shuffle = state.shuffle || false;
                this.repeatMode = state.repeatMode || 'off';
                this.volume = state.volume || 1.0;

                // ✅ Restore play context (Alpine store check)
                const muzibuStore = Alpine.store('muzibu');
                if (state.playContext && muzibuStore) {
                    muzibuStore.updatePlayContext(state.playContext);
                }

                // 🎵 AUTO-RESUME: Tarayıcı kapansa bile kaldığı yerden devam et
                // ⚠️ Autoplay Policy: Kullanıcı etkileşimi olmadan play() yapılamaz
                // Çözüm: Şarkıyı yükle, PAUSE modunda beklet, kullanıcı play'e basınca devam
                if (this.currentSong && this.queue.length > 0) {
                    const wasPlaying = state.isPlaying;
                    const savedTime = state.currentTime || 0;


                    // 🔥 FIX: Stream isteği ATMA! Sadece UI'ı güncelle.
                    // Kullanıcı play butonuna basınca şarkı yüklenecek.
                    // Bu şekilde login sonrası race condition olmaz.

                    // UI'da şarkı bilgisini göster (stream isteği yok)
                    this.currentTime = savedTime;
                    this.isPlaying = false; // Pause modunda bekle

                    // 🛡️ Re-enable auto-save
                    setTimeout(() => {
                        this._isRestoringState = false;
                    }, 500);

                    if (wasPlaying) {
                    }
                } else {
                    // No song to load, just re-enable auto-save
                    this._isRestoringState = false;
                }

            } catch (error) {
                console.error('❌ Failed to load state:', error);
                this._isRestoringState = false;
            }
        },

        /**
         * 🚀 INSTANT QUEUE: Sayfa açılır açılmaz queue yükle
         * Backend'den son dinlenen şarkı + genre şarkıları alır
         */
        async loadInitialQueue() {
            try {
                const response = await fetch('/api/muzibu/queue/initial', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin'
                });

                if (!response.ok) {
                    console.warn('⚠️ Initial queue fetch failed:', response.status);
                    return;
                }

                const data = await response.json();

                if (data.success && data.songs && data.songs.length > 0) {
                    // Queue'ya şarkıları ekle
                    this.queue = data.songs;
                    this.queueIndex = 0;
                    this.currentSong = data.songs[0];

                    // 🎨 Update player gradient colors (initial queue load)
                    this.updatePlayerColors();

                    // Context'i güncelle (genre/popular)
                    if (data.context) {
                        const muzibuStore = Alpine.store('muzibu');
                        if (muzibuStore) {
                            muzibuStore.updatePlayContext(data.context);
                        }
                    }
                }
            } catch (error) {
                console.error('❌ Initial queue error:', error);
            }
        },

        async previousTrack() {
            if (this.queueIndex > 0) {
                // 🚨 INSTANT PLAY: Cancel crossfade (manual track change)
                this.isCrossfading = false;

                // ⚡ INSTANT STOP: Stop current track immediately before loading next
                await this.stopCurrentPlayback();

                this.queueIndex--;
                await this.playSongFromQueue(this.queueIndex);

                // 🎯 Preload first song in queue (after track change)
                this.preloadFirstInQueue();
            }
        },

        async nextTrack() {
            // 🚨 INSTANT PLAY: Cancel crossfade (manual track change)
            this.isCrossfading = false;

            // ⚡ INSTANT STOP: Stop current track immediately before loading next
            await this.stopCurrentPlayback();

            // 🔍 SERVER LOG
            serverLog('nextTrack', {
                queueIndex: this.queueIndex,
                queueLength: this.queue?.length,
                hasNext: this.queueIndex < this.queue.length - 1,
                repeatMode: this.repeatMode,
                currentSongId: this.currentSong?.song_id,
                currentSongAlbumId: this.currentSong?.album_id,
                currentSongGenreId: this.currentSong?.genre_id
            });

            if (this.queueIndex < this.queue.length - 1) {
                this.queueIndex++;
                await this.playSongFromQueue(this.queueIndex);

                // 🎯 Preload first song in queue (after track change)
                this.preloadFirstInQueue();
            } else if (this.repeatMode === 'all' || this.b2bMode) {
                // 💾 B2B mode: infinite loop (auto-restart)
                this.queueIndex = 0;
                await this.playSongFromQueue(this.queueIndex);
                if (this.b2bMode) {
                }
            } else {
                // 🔄 AUTO-REFILL: Queue bitti, yeni şarkılar çekmeyi dene
                if (this.currentUser?.is_root) {
                    this.showToast('🔄 Queue bitti, refill deneniyor...', 'warning');
                }

                const muzibuStore = Alpine.store('muzibu') || Alpine.store('player');
                let hasContext = muzibuStore?.getPlayContext();

                // 🔧 FIX: Context yoksa, mevcut şarkıdan oluştur!
                if (!hasContext && this.currentSong) {
                    let albumId = this.currentSong.album_id;
                    let genreId = this.currentSong.genre_id;

                    // 🍎 FIX: album_id/genre_id yoksa API'den çek!
                    if (!albumId && !genreId && this.currentSong.song_id) {
                        serverLog('fetchingSongDetails', { songId: this.currentSong.song_id });
                        try {
                            const response = await fetch(`/api/muzibu/songs/${this.currentSong.song_id}`);
                            if (response.ok) {
                                const songData = await response.json();
                                if (songData.song) {
                                    albumId = songData.song.album_id;
                                    genreId = songData.song.genre_id;
                                    // Şarkıya da ekle (gelecek için)
                                    this.currentSong.album_id = albumId;
                                    this.currentSong.genre_id = genreId;
                                    serverLog('songDetailsFetched', { albumId, genreId });
                                }
                            }
                        } catch (e) {
                            serverLog('songDetailsFetchError', { error: e.message });
                        }
                    }

                    // 🔍 SERVER LOG
                    serverLog('autoCreateContext', {
                        albumId: albumId,
                        genreId: genreId,
                        currentSong: this.currentSong
                    });

                    if (albumId) {
                        const contextObj = { type: 'album', id: albumId, name: 'Album', offset: 0, source: 'auto_fallback' };
                        if (muzibuStore) muzibuStore.playContext = contextObj;
                        try { localStorage.setItem('muzibu_play_context', JSON.stringify(contextObj)); } catch(e) {}
                        hasContext = contextObj;
                        serverLog('contextCreated', { context: contextObj });
                    } else if (genreId) {
                        const contextObj = { type: 'genre', id: genreId, name: 'Genre', offset: 0, source: 'auto_fallback' };
                        if (muzibuStore) muzibuStore.playContext = contextObj;
                        try { localStorage.setItem('muzibu_play_context', JSON.stringify(contextObj)); } catch(e) {}
                        hasContext = contextObj;
                        serverLog('contextCreated', { context: contextObj });
                    } else {
                        serverLog('noContextData', { message: 'currentSong has no album_id or genre_id' });
                    }
                }

                // 🔍 SERVER LOG
                serverLog('refillAttempt', {
                    hasContext: !!hasContext,
                    contextType: hasContext?.type,
                    contextId: hasContext?.id,
                    hasMuzibuStore: !!muzibuStore,
                    hasRefillQueue: typeof muzibuStore?.refillQueue === 'function'
                });

                if (muzibuStore && typeof muzibuStore.refillQueue === 'function' && hasContext) {
                    try {
                        const newSongs = await muzibuStore.refillQueue(0, 15);

                        // 🔍 SERVER LOG
                        serverLog('refillResult', {
                            newSongsCount: newSongs?.length || 0,
                            firstSongId: newSongs?.[0]?.song_id,
                            firstSongTitle: newSongs?.[0]?.title
                        });

                        if (this.currentUser?.is_root) {
                            this.showToast(`🔄 Refill: ${newSongs?.length || 0} şarkı`, 'info');
                        }

                        if (newSongs && newSongs.length > 0) {
                            // Mevcut şarkıyı filtrele
                            const currentSongId = this.currentSong?.song_id;
                            const uniqueSongs = newSongs.filter(s => s.song_id !== currentSongId);

                            // 🔍 SERVER LOG
                            serverLog('refillFiltered', {
                                originalCount: newSongs.length,
                                filteredCount: uniqueSongs.length,
                                filteredSongId: currentSongId
                            });

                            if (uniqueSongs.length > 0) {
                                this.queue = uniqueSongs;
                                this.queueIndex = 0;

                                // 🔍 SERVER LOG
                                serverLog('refillPlaying', {
                                    newQueueLength: uniqueSongs.length,
                                    playingSongId: uniqueSongs[0]?.song_id,
                                    playingSongTitle: uniqueSongs[0]?.title
                                });

                                await this.playSongFromQueue(0);
                                return;
                            }
                        }
                    } catch (error) {
                        console.error('❌ Auto-refill failed:', error);
                        // 🔍 SERVER LOG
                        serverLog('refillError', { error: error.message });
                        if (this.currentUser?.is_root) {
                            this.showToast(`❌ Refill hata: ${error.message}`, 'error');
                        }
                    }
                } else {
                    // 🔍 SERVER LOG
                    serverLog('refillSkipped', { reason: 'no store or function or context' });
                    if (this.currentUser?.is_root) {
                        this.showToast(`⚠️ Context yok, refill yapılamıyor`, 'warning');
                    }
                }

                // Refill başarısız, dur
                this.isPlaying = false;
            }
        },

        // Fisher-Yates Shuffle Algorithm
        shuffleArray(array) {
            const arr = [...array]; // Create a copy
            for (let i = arr.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [arr[i], arr[j]] = [arr[j], arr[i]]; // Swap
            }
            return arr;
        },

        toggleShuffle() {
            this.shuffle = !this.shuffle;

            if (this.shuffle) {
                // Shuffle the queue
                if (this.queue.length > 0) {
                    // Save current song
                    const currentSong = this.queue[this.queueIndex];

                    // Remove current song from queue
                    const remainingSongs = this.queue.filter((_, index) => index !== this.queueIndex);

                    // Shuffle remaining songs
                    const shuffled = this.shuffleArray(remainingSongs);

                    // Rebuild queue: current song first, then shuffled
                    this.queue = [currentSong, ...shuffled];
                    this.queueIndex = 0;

                    this.showToast(this.frontLang?.player?.shuffle_on || 'Shuffle on', 'success');
                }
            } else {
                this.showToast(this.frontLang?.player?.shuffle_off || 'Shuffle off', 'info');
                // Note: We don't restore original order since we don't track it
                // Shuffle off just means next songs will play in current order
            }
        },

        cycleRepeat() {
            const modes = ['off', 'all', 'one'];
            const idx = modes.indexOf(this.repeatMode);
            this.repeatMode = modes[(idx + 1) % modes.length];

            // 🔔 Toast notification
            const messages = {
                'off': this.frontLang?.player?.repeat_off || 'Tekrarlama kapalı',
                'all': this.frontLang?.player?.repeat_all || 'Tümünü tekrarla',
                'one': this.frontLang?.player?.repeat_one || 'Tek şarkıyı tekrarla'
            };
            const types = {
                'off': 'info',
                'all': 'success',
                'one': 'success'
            };
            this.showToast(messages[this.repeatMode], types[this.repeatMode]);
        },

        async toggleLike(songId = null) {
            // Eğer songId verilmemişse, mevcut şarkı için çalış (player bar için)
            if (!songId) {
                if (!this.currentSong) return;
                songId = this.currentSong.song_id;
            }

            const favoriteKey = `song-${songId}`;
            const previousFavorites = [...this.favorites];

            // Optimistic UI update
            const isCurrentlyLiked = this.favorites.includes(favoriteKey);
            if (isCurrentlyLiked) {
                this.favorites = this.favorites.filter(f => f !== favoriteKey);
            } else {
                this.favorites.push(favoriteKey);
            }

            // Eğer mevcut şarkıysa, isLiked state'ini de güncelle
            if (this.currentSong && this.currentSong.song_id === songId) {
                this.isLiked = !isCurrentlyLiked;
            }

            try {
                const response = await fetch('/api/favorites/toggle', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        model_class: 'Modules\\Muzibu\\App\\Models\\Song',
                        model_id: songId
                    })
                });

                const data = await response.json();

                if (!data.success) {
                    // Başarısız ise eski haline döndür
                    this.favorites = previousFavorites;
                    if (this.currentSong && this.currentSong.song_id === songId) {
                        this.isLiked = isCurrentlyLiked;
                    }

                    // Eğer unauthorized ise login sayfasına yönlendir
                    if (response.status === 401) {
                        window.location.href = '/login';
                    } else {
                        // 401 dışındaki hataları logla
                        console.warn('Favorite action failed:', response.status);
                    }
                }
            } catch (error) {
                // Network veya diğer kritik hatalar
                if (!error.message?.includes('401')) {
                    console.error('Favorite toggle error:', error);
                }
                // Hata durumunda eski haline döndür
                this.favorites = previousFavorites;
                if (this.currentSong && this.currentSong.song_id === songId) {
                    this.isLiked = isCurrentlyLiked;
                }
            }
        },

        toggleMute() {
            this.isMuted = !this.isMuted;
            if (this.howl) {
                this.howl.mute(this.isMuted);
            }
            if (this.hls) {
                const audio = this.getActiveHlsAudio();
                if (audio) {
                    audio.muted = this.isMuted;
                }
            }
        },

        // Progress tracking is handled by Howler.js in loadAndPlaySong()

        // Get index of next song (considering repeat and shuffle)
        getNextSongIndex() {
            if (this.repeatMode === 'one') {
                return this.queueIndex; // Same song
            }

            if (this.queueIndex < this.queue.length - 1) {
                return this.queueIndex + 1;
            } else if (this.repeatMode === 'all') {
                return 0; // Loop back
            }

            return -1; // No next song
        },

        // Start crossfade transition (using Howler.js)
        async startCrossfade() {
            // 🛡️ CRITICAL: Kullanıcı pause/stop yaptıysa crossfade başlatma!
            if (!this.isPlaying) return;

            if (this.isCrossfading) return;

            // Check if any player is active (Howler OR HLS)
            const hasActiveHowler = this.howl && this.howl.playing();
            const audio = this.getActiveHlsAudio(); // Use helper to get correct audio element
            const hasActiveHls = this.hls && audio && !audio.paused;

            if (!hasActiveHowler && !hasActiveHls) return;

            const nextIndex = this.getNextSongIndex();
            if (nextIndex === -1) return;

            const nextSong = this.queue[nextIndex];
            if (!nextSong) return;

            // 🧹 Preload varsa temizle (crossfade kendi HLS'ini oluşturacak)
            // Ama URL cache'de kalır, crossfade onu kullanır
            if (this._preloadedNext) {
                this._cleanupPreloadedNext();
                this._preloadNextInProgress = false;
            }

            this.isCrossfading = true;

            const self = this;
            const targetVolume = this.isMuted ? 0 : this.volume / 100;

            // 🔥 FIX: Save current audio volume BEFORE creating next player
            // (createNextHlsPlayer might reuse the same audio element!)
            const currentAudioVolume = hasActiveHls ? audio.volume : null;

            // Get next song URL and type - USE CACHE FIRST!
            try {
                let data;

                // 🚀 CHECK CACHE FIRST - instant crossfade if cached!
                const cached = this.getCachedStream(nextSong.song_id);
                if (cached) {
                    data = cached;
                } else {
                    // Fetch from API if not cached (🔐 401 kontrolü ile)
                    const response = await this.authenticatedFetch(`/api/muzibu/songs/${nextSong.song_id}/stream`);
                    if (!response) {
                        this.isCrossfading = false;
                        return; // 401 aldıysa logout olacak
                    }
                    data = await response.json();
                }

                if (!data.stream_url) {
                    this.isCrossfading = false;
                    return;
                }

                const nextStreamType = data.stream_type || 'mp3';
                const nextIsHls = nextStreamType === 'hls';


                // Create next player based on stream type
                if (nextIsHls) {
                    // Create HLS player for next song
                    await this.createNextHlsPlayer(data.stream_url, targetVolume);
                } else {
                    // Create Howler for next song (MP3)
                    this.createNextHowlerPlayer(data.stream_url, targetVolume);
                }

                // 🔥 FIX: Update UI immediately for smooth progress bar transition
                // Instead of waiting 7 seconds, show new song info RIGHT NOW
                // This prevents progress bar jumping and provides better UX
                this.queueIndex = nextIndex;
                this.currentSong = this.queue[nextIndex];
                this.currentTime = 0;
                this.progressPercent = 0;
                this.playTracked = false;

                // 🎨 Update player gradient colors for crossfade
                this.updatePlayerColors();

                // 🔥 CRITICAL: Stop old progress tracking and start tracking NEXT player
                // Old interval tracks old song, but we're showing new song info now!
                if (this.progressInterval) {
                    clearInterval(this.progressInterval);
                    this.progressInterval = null;
                }

                // Get duration and start tracking NEXT player
                if (nextIsHls) {
                    const nextAudio = document.getElementById(this.nextHlsAudioId);
                    if (nextAudio && nextAudio.duration) {
                        this.duration = nextAudio.duration;
                    }
                    // Track next HLS audio during crossfade
                    this.startProgressTrackingWithElement(nextAudio);
                } else if (this.howlNext) {
                    this.duration = this.howlNext.duration() || 0;
                    // Track next Howler during crossfade
                    const self = this;
                    this.progressInterval = setInterval(() => {
                        if (this.howlNext && this.howlNext.playing() && this.duration > 0) {
                            this.currentTime = this.howlNext.seek();
                            this.progressPercent = (this.currentTime / this.duration) * 100;
                        }
                    }, 100);
                }

                // 🔊 BACKGROUND TAB FIX: Background'daysa eski player'ı da fade etme, direkt stop
                const isBackgroundTab = document.hidden;

                // Fade out current player (Howler or HLS)
                if (hasActiveHowler) {
                    if (!isBackgroundTab) {
                        this.howl.fade(targetVolume, 0, this.crossfadeDuration);
                    } else {
                        // Background: Direkt volume 0 yap, fade yok
                        this.howl.volume(0);
                    }
                } else if (hasActiveHls) {
                    if (!isBackgroundTab) {
                        // 🔥 FIX: Use saved volume instead of audio.volume
                        // (audio.volume might be 0 if createNextHlsPlayer reused the same element!)
                        this.fadeAudioElement(audio, currentAudioVolume, 0, this.crossfadeDuration);
                    } else {
                        // Background: Direkt volume 0 yap, fade yok
                        audio.volume = 0;
                    }
                }

                // 🔧 FIX: nextIndex'i sakla (pause sırasında doğru şarkıya geçmek için)
                this.crossfadeNextIndex = nextIndex;

                // After crossfade duration, complete the transition
                // 🔧 FIX: Timeout'u kaydet (pause sırasında iptal edebilmek için)
                this.crossfadeTimeoutId = setTimeout(() => {
                    this.crossfadeTimeoutId = null;
                    this.completeCrossfade(nextIndex, nextIsHls);
                }, this.crossfadeDuration);

            } catch (error) {
                // Silent: Crossfade failed (browser power save, background tab, etc.)
                // Smart Crossfade: Just cleanup next player, let current song finish naturally

                // Cleanup crossfade state
                this.isCrossfading = false;
                this.crossfadeNextIndex = -1;

                // Cleanup failed next player ONLY (don't touch current player!)
                if (this.hlsNext) {
                    try { this.hlsNext.destroy(); } catch (e) {}
                    this.hlsNext = null;
                }
                if (this.howlNext) {
                    try { this.howlNext.unload(); } catch (e) {}
                    this.howlNext = null;
                }

                // Cleanup next audio element
                const nextAudioEl = document.getElementById(this.nextHlsAudioId);
                if (nextAudioEl) {
                    try {
                        nextAudioEl.pause();
                        nextAudioEl.src = '';
                    } catch (e) {}
                }

                // DON'T stop current player - let it finish naturally!
                // The onended event will trigger playNextSong() when current song ends
            }
        },

        // Create next Howler player for crossfade
        createNextHowlerPlayer(url, targetVolume) {
            const self = this;

            // Determine format from URL
            let format = ['mp3'];
            if (url.includes('.ogg')) format = ['ogg'];
            else if (url.includes('.wav')) format = ['wav'];
            else if (url.includes('.webm')) format = ['webm'];

            // 🔊 BACKGROUND TAB FIX: Background'daysa fade skip, direkt volume set
            const isBackgroundTab = document.hidden;

            this.howlNext = new Howl({
                src: [url],
                format: format,
                html5: true,
                volume: isBackgroundTab ? targetVolume : 0,
                onplay: function() {
                    // 🔊 BACKGROUND TAB: Fade skip
                    if (!isBackgroundTab) {
                        self.howlNext.fade(0, targetVolume, self.crossfadeDuration);
                    }
                },
                onloaderror: function(id, error) {
                    console.error('Howler load error (crossfade):', error);
                }
            });

            // Start playing next
            this.howlNext.play();
        },

        // Create next HLS player for crossfade
        async createNextHlsPlayer(url, targetVolume) {
            const self = this;

            // 🔊 BACKGROUND TAB FIX: Background'daysa fade skip, direkt volume set
            const isBackgroundTab = document.hidden;

            // 🔥 FIX: Use the INACTIVE audio element for crossfade
            // If hlsAudio is active, use hlsAudioNext. If hlsAudioNext is active, use hlsAudio.
            const currentAudioId = this.activeHlsAudioId || 'hlsAudio';
            const nextAudioId = currentAudioId === 'hlsAudio' ? 'hlsAudioNext' : 'hlsAudio';

            // Create or get the inactive audio element
            let nextAudio = document.getElementById(nextAudioId);
            if (!nextAudio) {
                nextAudio = document.createElement('audio');
                nextAudio.id = nextAudioId;
                nextAudio.style.display = 'none';
                document.body.appendChild(nextAudio);
            }

            // Store next audio ID for completeCrossfade
            this.nextHlsAudioId = nextAudioId;

            return new Promise((resolve, reject) => {
                if (Hls.isSupported()) {
                    this.hlsNext = new Hls({
                        enableWorker: false, // 🔧 FIX: Disable worker to avoid internal exceptions
                        lowLatencyMode: false,
                        xhrSetup: function(xhr, url) {
                            xhr.withCredentials = false; // 🔑 CRITICAL: Disable credentials for CORS
                        }
                    });

                    // 🔧 FIX: Normalize URL to match current page origin (www vs non-www)
                    let normalizedUrl = url;
                    if (url.startsWith('http')) {
                        const currentOrigin = window.location.origin;
                        const urlObj = new URL(url);
                        normalizedUrl = currentOrigin + urlObj.pathname + urlObj.search + urlObj.hash;
                    }

                    this.hlsNext.loadSource(normalizedUrl);
                    this.hlsNext.attachMedia(nextAudio);

                    this.hlsNext.on(Hls.Events.MANIFEST_PARSED, function() {
                        nextAudio.volume = isBackgroundTab ? targetVolume : 0;
                        nextAudio.play().then(() => {
                            // 🔊 BACKGROUND TAB: Fade skip
                            if (!isBackgroundTab) {
                                self.fadeAudioElement(nextAudio, 0, targetVolume, self.crossfadeDuration);
                            }
                            resolve();
                        }).catch(e => {
                            // Silent: Browser power save or background tab - handled by smart crossfade fallback
                            reject(e);
                        });
                    });

                    this.hlsNext.on(Hls.Events.ERROR, async function(event, data) {
                        const respCode = data?.response?.code || data?.response?.status || null;

                        // 🔧 FIX: Non-fatal 401/403 - URL yenile
                        if (!data.fatal && (respCode === 401 || respCode === 403)) {
                            console.warn('🔄 Crossfade 401/403 - URL yenileniyor...');
                            await self.refreshHlsUrlForCurrentSong(false);
                            return; // Retry devam etsin
                        }

                        if (data.fatal) {
                            // 🔧 FIX: Fatal 401/403 - MP3 fallback dene
                            if (respCode === 401 || respCode === 403) {
                                console.warn('🔒 Crossfade HLS denied, skipping crossfade');
                                reject(new Error('HLS_AUTH_ERROR'));
                                return;
                            }
                            console.error('HLS crossfade fatal error:', data);
                            reject(data);
                        }
                    });
                } else if (nextAudio.canPlayType('application/vnd.apple.mpegurl')) {
                    // Native HLS support (Safari)
                    nextAudio.src = url;
                    nextAudio.volume = isBackgroundTab ? targetVolume : 0;
                    nextAudio.play().then(() => {
                        // 🔊 BACKGROUND TAB: Fade skip
                        if (!isBackgroundTab) {
                            self.fadeAudioElement(nextAudio, 0, targetVolume, self.crossfadeDuration);
                        }
                        resolve();
                    }).catch(reject);
                } else {
                    console.error('HLS not supported for crossfade');
                    reject(new Error('HLS not supported'));
                }
            });
        },

        // Complete the crossfade transition
        completeCrossfade(nextIndex, nextIsHls = false) {
            // Stop and unload old Howler
            if (this.howl) {
                this.howl.stop();
                this.howl.unload();
                this.howl = null;
            }

            // Stop and unload old HLS
            if (this.hls) {
                // 🔥 FIX: Get the CURRENT active audio element (not always hlsAudio!)
                const currentAudioId = this.activeHlsAudioId || 'hlsAudio';
                const oldAudio = document.getElementById(currentAudioId);

                if (oldAudio) {
                    oldAudio.pause();
                    oldAudio.src = '';
                    oldAudio.load(); // Reset audio element
                }
                this.hls.destroy();
                this.hls = null;
            }

            // Clear old progress interval
            if (this.progressInterval) {
                clearInterval(this.progressInterval);
            }

            // Swap next player to current based on type
            if (nextIsHls) {
                // HLS crossfade - swap hlsNext to hls
                this.hls = this.hlsNext;
                this.hlsNext = null;
                this.isHlsStream = true;

                // 🔥 FIX: Use nextHlsAudioId (set in createNextHlsPlayer)
                this.activeHlsAudioId = this.nextHlsAudioId;

                // Get reference to the next audio element (now becomes main)
                const nextAudio = document.getElementById(this.nextHlsAudioId);
                if (nextAudio) {
                    this.duration = nextAudio.duration || 0;

                    // Set up ended handler for the new audio
                    const self = this;
                    nextAudio.onended = function() {
                        if (!self.isCrossfading) {
                            self.onTrackEnded();
                        }
                    };

                    // 🎵 CROSSFADE TRIGGER: timeupdate event for crossfaded HLS
                    nextAudio.ontimeupdate = function() {
                        if (!self.duration || self.duration <= 0) return;
                        if (self.isCrossfading) return;

                        const timeRemaining = self.duration - nextAudio.currentTime;
                        if (self.crossfadeEnabled && timeRemaining <= (self.crossfadeDuration / 1000) && timeRemaining > 0) {
                            self.startCrossfade();
                        }
                    };
                }

                // Start progress tracking with next audio element
                this.startProgressTrackingWithElement(nextAudio);

            } else {
                // MP3 crossfade - swap howlNext to howl
                this.howl = this.howlNext;
                this.howlNext = null;
                this.isHlsStream = false;

                // Get duration and start tracking
                if (this.howl) {
                    this.duration = this.howl.duration();
                }
                this.startProgressTracking('howler');
            }

            // Update queue index and current song
            this.queueIndex = nextIndex;
            this.currentSong = this.queue[nextIndex];
            this.playTracked = false; // 🎵 Reset play tracking for new song

            // 🎨 Update player gradient colors after crossfade completion
            this.updatePlayerColors();

            // Reset crossfade state
            this.isCrossfading = false;


            // 🚀 PRELOAD: Crossfade bitti, bir sonraki şarkıyı cache'e yükle
            this.preloadNextSong();
        },

        seekTo(e) {
            // 🛡️ Guard: null/undefined kontrolü - sessizce çık
            if (e === null || e === undefined) {
                return;
            }

            let newTime;

            // 🔧 Support both event (click on progress bar) and direct time value
            if (typeof e === 'number') {
                // Direct time value (from state restore)
                newTime = e;
            } else if (e && e.currentTarget) {
                // Click event on progress bar
                const bar = e.currentTarget;
                const rect = bar.getBoundingClientRect();
                const percent = (e.clientX - rect.left) / rect.width;
                newTime = this.duration * percent;
            } else {
                // Bilinmeyen argüman tipi - sessizce çık
                return;
            }

            // 1️⃣ Howler.js (MP3)
            if (this.howl && this.duration) {
                this.howl.seek(newTime);
            }

            // 2️⃣ HLS.js (PC)
            if (this.hls) {
                const audio = this.getActiveHlsAudio();
                if (audio && this.duration) {
                    audio.currentTime = newTime;
                }
            }

            // 3️⃣ Safari Native HLS (Mobile) - this.hls yok ama audio element var
            if (!this.howl && !this.hls) {
                const audio = this.getActiveHlsAudio();
                if (audio && this.duration) {
                    audio.currentTime = newTime;
                }
            }

            this.currentTime = newTime;
            // 🔥 FIX: percent sadece click event'de tanımlı, duration'dan hesapla
            this.progressPercent = this.duration > 0 ? (newTime / this.duration) * 100 : 0;
        },

        setVolume(e) {
            const bar = e.currentTarget;
            const rect = bar.getBoundingClientRect();
            const percent = (e.clientX - rect.left) / rect.width;
            this.volume = Math.max(0, Math.min(100, percent * 100));

            const volumeValue = this.volume / 100;

            if (this.howl) {
                this.howl.volume(volumeValue);
            }
            if (this.hls) {
                const audio = this.getActiveHlsAudio();
                if (audio) {
                    audio.volume = volumeValue;
                }
            }

            if (this.isMuted && this.volume > 0) {
                this.isMuted = false;
                if (this.howl) {
                    this.howl.mute(false);
                }
                if (this.hls) {
                    const audio = this.getActiveHlsAudio();
                    if (audio) {
                        audio.muted = false;
                    }
                }
            }

            // Save volume to localStorage
            safeStorage.setItem('volume', Math.round(this.volume));
        },

        // Metadata is handled by Howler.js onload callback

        onTrackEnded() {
            // 🛡️ CRITICAL: Kullanıcı pause/stop yaptıysa, otomatik devam ETME!
            // Sadece isPlaying = true iken sonraki şarkıya geç
            if (!this.isPlaying) {
                serverLog('onTrackEndedBlocked', { reason: 'isPlaying is false (user paused)' });
                return;
            }

            // 🍎 FIX: Debounce - 1 saniye içinde tekrar çağrılmasını engelle
            const now = Date.now();
            if (this._lastTrackEndedTime && (now - this._lastTrackEndedTime) < 1000) {
                serverLog('onTrackEndedDebounced', { timeSinceLast: now - this._lastTrackEndedTime });
                return;
            }
            this._lastTrackEndedTime = now;

            // Dispatch stop event (track ended naturally)
            window.dispatchEvent(new CustomEvent('player:stop'));

            // 🔍 SERVER LOG
            serverLog('onTrackEnded', {
                repeatMode: this.repeatMode,
                currentSongId: this.currentSong?.song_id,
                queueLength: this.queue?.length,
                queueIndex: this.queueIndex
            });

            if (this.repeatMode === 'one') {
                // Repeat current song
                if (this.howl) {
                    this.howl.seek(0);
                    this.howl.play();
                } else if (this.hls) {
                    // HLS.js (PC)
                    const audio = this.getActiveHlsAudio();
                    if (audio) {
                        audio.currentTime = 0;
                        audio.play();
                    }
                } else {
                    // Safari Native HLS (Mobile)
                    const audio = this.getActiveHlsAudio();
                    if (audio) {
                        audio.currentTime = 0;
                        audio.play();
                    }
                }
            } else {
                this.nextTrack();
            }
        },

        formatTime(sec) {
            if (!sec || isNaN(sec)) return '0:00';
            const m = Math.floor(sec / 60);
            const s = Math.floor(sec % 60);
            return `${m}:${s.toString().padStart(2, '0')}`;
        },

        /**
         * 🍎 Update MediaSession metadata for iOS Control Center / Lock Screen
         * Shows song title, artist, album art in system media controls
         */
        updateMediaSession() {
            if (!('mediaSession' in navigator)) {
                serverLog('mediaSessionNotSupported', {});
                return;
            }

            const song = this.currentSong;
            if (!song) {
                serverLog('mediaSessionNoSong', {});
                return;
            }

            try {
                // Get cover URL
                const coverUrl = song.cover_url || song.album_cover || '';
                const songTitle = song.song_title || song.title || 'Unknown';
                const artistName = song.artist_title || song.artist_name || '';
                const albumName = song.album_title || song.album_name || '';

                // 🔍 SERVER LOG
                serverLog('mediaSessionUpdate', {
                    songTitle: songTitle,
                    artistName: artistName,
                    albumName: albumName,
                    hasCover: !!coverUrl
                });

                navigator.mediaSession.metadata = new MediaMetadata({
                    title: songTitle,
                    artist: artistName,
                    album: albumName,
                    artwork: coverUrl ? [
                        { src: coverUrl, sizes: '96x96', type: 'image/webp' },
                        { src: coverUrl, sizes: '128x128', type: 'image/webp' },
                        { src: coverUrl, sizes: '192x192', type: 'image/webp' },
                        { src: coverUrl, sizes: '256x256', type: 'image/webp' },
                        { src: coverUrl, sizes: '384x384', type: 'image/webp' },
                        { src: coverUrl, sizes: '512x512', type: 'image/webp' }
                    ] : []
                });

                // Set up action handlers
                const self = this;
                navigator.mediaSession.setActionHandler('play', () => self.togglePlayPause());
                navigator.mediaSession.setActionHandler('pause', () => self.togglePlayPause());
                navigator.mediaSession.setActionHandler('previoustrack', () => self.prevTrack());
                navigator.mediaSession.setActionHandler('nexttrack', () => self.nextTrack());
                navigator.mediaSession.setActionHandler('seekbackward', (details) => {
                    const skipTime = details.seekOffset || 10;
                    self.seekRelative(-skipTime);
                });
                navigator.mediaSession.setActionHandler('seekforward', (details) => {
                    const skipTime = details.seekOffset || 10;
                    self.seekRelative(skipTime);
                });
            } catch (e) {
                console.warn('MediaSession error:', e);
            }
        },

        /**
         * Seek relative to current position (for MediaSession)
         */
        seekRelative(seconds) {
            const audio = this.getActiveHlsAudio();
            if (audio && audio.duration) {
                const newTime = Math.max(0, Math.min(audio.duration, audio.currentTime + seconds));
                audio.currentTime = newTime;
                this.currentTime = newTime;
            }
        },

        /**
         * Set play context (for sidebar preview mode AND queue refill)
         * 🔧 CRITICAL: Updates BOTH component state AND Alpine.store('player')
         * @param {Object} context - { type, id, name, offset }
         */
        setPlayContext(context) {
            const validTypes = ['genre', 'album', 'playlist', 'user_playlist', 'sector', 'radio', 'popular', 'recent', 'favorites', 'artist', 'search'];
            if (!validTypes.includes(context.type)) {
                console.warn('⚠️ Invalid context type:', context.type);
                return;
            }

            const contextObj = {
                type: context.type || 'playlist',
                id: context.id,
                name: context.name,
                offset: context.offset || 0,
                source: context.source || 'sidebar'
            };

            // 1️⃣ Update component state
            this.playContext = contextObj;
            this.currentContext = contextObj;

            // 2️⃣ 🔧 CRITICAL FIX: Update Alpine.store('player') - this is what refillQueue uses!
            const store = Alpine.store('player');
            if (store) {
                store.playContext = contextObj;
            }

            // 3️⃣ Save to localStorage (Safari Private Mode safe)
            try {
                localStorage.setItem('muzibu_play_context', JSON.stringify(contextObj));
            } catch (e) {
                // Safari Private Mode - silently ignore
            }
        },

        /**
         * Get play context (for queue refill)
         * @returns {Object|null} Context object or null
         */
        getPlayContext() {
            // 1️⃣ Check component state
            if (this.playContext) {
                return this.playContext;
            }

            // 2️⃣ Check Alpine store
            const store = Alpine.store('player');
            if (store?.playContext) {
                return store.playContext;
            }

            // 3️⃣ Try localStorage
            try {
                const stored = localStorage.getItem('muzibu_play_context');
                if (stored) {
                    this.playContext = JSON.parse(stored);
                    return this.playContext;
                }
            } catch (e) {
                // Safari Private Mode - silently ignore
            }

            return null;
        },

        async playAlbum(id) {
            // 🚫 PREMIUM CHECK
            if (!this.isLoggedIn) {
                this.showToast(this.frontLang?.auth?.login_required || 'Login required to listen', 'warning');
                return;
            }

            const isPremiumOrTrial = this.currentUser?.is_premium || this.currentUser?.is_trial;
            if (!isPremiumOrTrial) {
                this.showToast(this.frontLang?.auth?.premium_required || 'Premium membership required', 'warning');
                return;
            }

            try {
                const response = await fetch(`/api/muzibu/albums/${id}`);
                const album = await response.json();

                if (album.songs && album.songs.length > 0) {
                    // 🧹 Clean queue from null/undefined songs
                    this.queue = this.cleanQueue(album.songs);

                    if (this.queue.length === 0) {
                        this.showToast(this.frontLang?.messages?.album_no_playable_songs || 'No playable songs in this album', 'error');
                        return;
                    }

                    // 🎯 Preload first song in queue
                    this.preloadFirstInQueue();

                    this.queueIndex = 0;
                    await this.playSongFromQueue(0);

                    // Safe album title extraction
                    const albumTitle = album.album_title?.tr || album.album_title?.en || album.album_title || this.frontLang?.general?.album || 'Album';
                    this.showToast((this.frontLang?.messages?.now_playing || ':title is playing').replace(':title', albumTitle), 'success');
                }
            } catch (error) {
                console.error('Failed to play album:', error);
                this.showToast(this.frontLang?.messages?.album_loading_failed || 'Failed to load album', 'error');
            }
        },

        async playPlaylist(id) {
            // 🚫 PREMIUM CHECK
            if (!this.isLoggedIn) {
                this.showToast(this.frontLang?.auth?.login_required || 'Login required to listen', 'warning');
                return;
            }

            const isPremiumOrTrial = this.currentUser?.is_premium || this.currentUser?.is_trial;
            if (!isPremiumOrTrial) {
                this.showToast(this.frontLang?.auth?.premium_required || 'Premium membership required', 'warning');
                return;
            }

            try {
                const response = await fetch(`/api/muzibu/playlists/${id}`);
                const playlist = await response.json();

                if (playlist.songs && playlist.songs.length > 0) {
                    // 🧹 Clean queue from null/undefined songs
                    this.queue = this.cleanQueue(playlist.songs);

                    if (this.queue.length === 0) {
                        this.showToast(this.frontLang?.messages?.playlist_no_playable_songs || 'No playable songs in this playlist', 'error');
                        return;
                    }

                    // 🎯 Preload first song in queue
                    this.preloadFirstInQueue();

                    this.queueIndex = 0;
                    await this.playSongFromQueue(0);

                    // Safe playlist title extraction
                    const playlistTitle = playlist.title?.tr || playlist.title?.en || playlist.title || this.frontLang?.general?.playlist || 'Playlist';
                    this.showToast((this.frontLang?.messages?.now_playing || ':title is playing').replace(':title', playlistTitle), 'success');
                }
            } catch (error) {
                console.error('Failed to play playlist:', error);
                this.showToast(this.frontLang?.messages?.playlist_loading_failed || 'Failed to load playlist', 'error');
            }
        },

        async playGenre(id) {
            // 🚫 PREMIUM CHECK
            if (!this.isLoggedIn) {
                this.showToast(this.frontLang?.auth?.login_required || 'Login required to listen', 'warning');
                return;
            }

            const isPremiumOrTrial = this.currentUser?.is_premium || this.currentUser?.is_trial;
            if (!isPremiumOrTrial) {
                this.showToast(this.frontLang?.auth?.premium_required || 'Premium membership required', 'warning');
                return;
            }

            try {
                const response = await fetch(`/api/muzibu/genres/${id}/songs`);
                const data = await response.json();

                if (data.songs && data.songs.length > 0) {
                    this.queue = this.cleanQueue(data.songs);

                    if (this.queue.length === 0) {
                        this.showToast(this.frontLang?.messages?.genre_no_playable_songs || 'No playable songs in this genre', 'error');
                        return;
                    }

                    this.queueIndex = 0;
                    await this.playSongFromQueue(0);

                    const genreTitle = data.genre?.title?.tr || data.genre?.title?.en || data.genre?.title || this.frontLang?.general?.genre || 'Genre';
                    this.showToast((this.frontLang?.messages?.now_playing || ':title is playing').replace(':title', genreTitle), 'success');
                }
            } catch (error) {
                console.error('Failed to play genre:', error);
                this.showToast(this.frontLang?.messages?.genre_loading_failed || 'Failed to load genre', 'error');
            }
        },

        async playSector(id) {
            // 🚫 PREMIUM CHECK
            if (!this.isLoggedIn) {
                this.showToast(this.frontLang?.auth?.login_required || 'Login required to listen', 'warning');
                return;
            }

            const isPremiumOrTrial = this.currentUser?.is_premium || this.currentUser?.is_trial;
            if (!isPremiumOrTrial) {
                this.showToast(this.frontLang?.auth?.premium_required || 'Premium membership required', 'warning');
                return;
            }

            try {
                const response = await fetch(`/api/muzibu/sectors/${id}/songs`);
                const data = await response.json();

                if (data.songs && data.songs.length > 0) {
                    this.queue = this.cleanQueue(data.songs);

                    if (this.queue.length === 0) {
                        this.showToast(this.frontLang?.messages?.sector_no_playable_songs || 'No playable songs in this sector', 'error');
                        return;
                    }

                    this.queueIndex = 0;
                    await this.playSongFromQueue(0);

                    const sectorTitle = data.sector?.title?.tr || data.sector?.title?.en || data.sector?.title || this.frontLang?.general?.sector || 'Sector';
                    this.showToast((this.frontLang?.messages?.now_playing || ':title is playing').replace(':title', sectorTitle), 'success');
                }
            } catch (error) {
                console.error('Failed to play sector:', error);
                this.showToast(this.frontLang?.messages?.sector_loading_failed || 'Failed to load sector', 'error');
            }
        },

        async playRadio(id) {
            // 🚫 PREMIUM CHECK
            if (!this.isLoggedIn) {
                this.showToast(this.frontLang?.auth?.login_required || 'Login required to listen', 'warning');
                return;
            }

            const isPremiumOrTrial = this.currentUser?.is_premium || this.currentUser?.is_trial;
            if (!isPremiumOrTrial) {
                this.showToast(this.frontLang?.auth?.premium_required || 'Premium membership required', 'warning');
                return;
            }

            try {
                // 📻 RADIO: No loading overlay - Direct playback
                // Fetch radio songs in background
                const response = await fetch(`/api/muzibu/radios/${id}/songs`);
                const data = await response.json();

                if (data.songs && data.songs.length > 0) {
                    // Shuffle songs for radio experience
                    const shuffledSongs = this.shuffleArray([...data.songs]);
                    this.queue = this.cleanQueue(shuffledSongs);

                    if (this.queue.length === 0) {
                        this.showToast(this.frontLang?.messages?.radio_no_playable_songs || 'No playable songs in this radio', 'error');
                        return;
                    }

                    this.queueIndex = 0;
                    await this.playSongFromQueue(0);

                    const radioTitle = data.radio?.title?.tr || data.radio?.title?.en || data.radio?.title || this.frontLang?.general?.radio || 'Radio';
                    this.showToast(`📻 ${(this.frontLang?.messages?.now_playing || ':title is playing').replace(':title', radioTitle)}`, 'success');
                } else {
                    this.showToast(this.frontLang?.messages?.radio_no_playable_songs || 'No playable songs in this radio', 'error');
                }
            } catch (error) {
                console.error('Failed to play radio:', error);
                this.showToast(this.frontLang?.messages?.radio_loading_failed || 'Failed to load radio', 'error');
            }
        },

        // Helper: Shuffle array (Fisher-Yates)
        shuffleArray(array) {
            for (let i = array.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [array[i], array[j]] = [array[j], array[i]];
            }
            return array;
        },

        async playSong(id) {
            // 🔍 SERVER LOG: playSong başlangıcı
            serverLog('playSongStart', { songId: id, isLoggedIn: this.isLoggedIn, isPremium: this.currentUser?.is_premium, isTrial: this.currentUser?.is_trial });

            try {
                // 🔄 Loading state başlat
                this.isSongLoading = true;

                // 🚫 FRONTEND PREMIUM CHECK: Şarkı çalmaya çalışmadan önce kontrol et
                // Guest kullanıcı → Toast mesajı göster
                if (!this.isLoggedIn) {
                    this.isSongLoading = false;
                    serverLog('playSongBlocked', { reason: 'not_logged_in' });
                    this.showToast(this.frontLang?.auth?.login_required || 'Login required to listen', 'warning');
                    return;
                }

                // Premium/Trial olmayan üye → Toast mesajı göster
                const isPremiumOrTrial = this.currentUser?.is_premium || this.currentUser?.is_trial;
                if (!isPremiumOrTrial) {
                    this.isSongLoading = false;
                    serverLog('playSongBlocked', { reason: 'not_premium' });
                    this.showToast(this.frontLang?.auth?.premium_required || 'Premium membership required', 'warning');
                    return;
                }

                // 🚨 INSTANT PLAY: Cancel crossfade (manual song change)
                this.isCrossfading = false;

                // 🚀 PRELOAD CHECK: Eğer aynı şarkı zaten yüklüyse, tekrar fetch etme!
                if (this.currentSong?.song_id === id && this.hls) {
                    const audio = this.getActiveHlsAudio();
                    if (audio) {
                        this.hls.startLoad(); // Resume loading if stopped
                        try {
                            await audio.play();
                            this.isPlaying = true;
                            this.isSongLoading = false;
                            if (!this.progressInterval) {
                                this.startProgressTracking('hls');
                            }
                            window.dispatchEvent(new CustomEvent('player:play', {
                                detail: { songId: id, isLoggedIn: this.isLoggedIn }
                            }));
                            return;
                        } catch (e) {
                            console.warn('Preloaded play failed, will re-fetch:', e);
                        }
                    }
                }

                // Stop current playback FIRST before loading new song
                await this.stopCurrentPlayback();

                // 🚀 OPTIMIZED: Get stream URL directly (includes song info)
                const streamResponse = await fetch(`/api/muzibu/songs/${id}/stream`);

                // ❌ HTTP Error Check
                if (!streamResponse.ok) {
                    const errorData = await streamResponse.json().catch(() => ({}));

                    // 🚫 GUEST: Kayıt olmadan dinleyemez (401)
                    if (streamResponse.status === 401) {
                        this.showToast(errorData.message || this.frontLang?.auth?.login_required || 'Login required to listen', 'warning');
                        return;
                    }

                    // 💎 SUBSCRIPTION: Premium gerekli (402)
                    if (streamResponse.status === 402) {
                        this.showToast(errorData.message || this.frontLang?.auth?.premium_required || 'Premium membership required', 'warning');
                        return;
                    }

                    // 🔐 DEVICE LIMIT CHECK: Stream API'den gelen device limit hatası
                    if (streamResponse.status === 403 && errorData.error === 'device_limit_exceeded') {
                        this.deviceLimit = errorData.device_limit || 1;
                        this.activeDevices = []; // Modal açılınca fetchActiveDevices çağrılacak
                        this.showDeviceSelectionModal = true;
                        this.fetchActiveDevices(); // Cihaz listesini getir
                        return;
                    }

                    if (streamResponse.status === 404) {
                        this.showToast(this.frontLang?.messages?.song_not_found || 'Song not found', 'error');
                    } else if (streamResponse.status >= 500) {
                        this.showToast(this.frontLang?.messages?.server_error || 'Server error', 'error');
                    } else {
                        this.showToast(errorData.message || this.frontLang?.messages?.generic_error || 'An error occurred', 'error');
                    }
                    return;
                }

                const streamData = await streamResponse.json();

                // 🎵 Build song object from stream API response
                const song = {
                    song_id: id,
                    song_title: streamData.song?.title || this.frontLang?.general?.song || 'Unknown Song',
                    duration: streamData.song?.duration || '0:00',
                    album_cover: null
                };

                // 🎯 COVER: Extract from stream API
                if (streamData.song?.cover_url) {
                    const coverMatch = streamData.song.cover_url.match(/\/thumb\/(\d+)\//);
                    song.album_cover = coverMatch ? coverMatch[1] : streamData.song.cover_url;
                }

                // 🔧 FIX: Merge API song data BEFORE adding to queue
                // This ensures queue items have album_id/genre_id for auto-context
                const fullSong = streamData.song ? { ...song, ...streamData.song } : song;

                // Create queue with FULL song data (includes album_id, genre_id)
                this.queue = [fullSong];
                this.queueIndex = 0;
                this.currentSong = fullSong;
                this.playTracked = false;

                // 🎨 Update player gradient colors based on song's color_hash
                this.updatePlayerColors();

                // 🍎 Update iOS Control Center / Lock Screen metadata
                this.updateMediaSession();

                // 🔄 Her şarkı çalmada premium status ve subscription bilgilerini güncelle
                if (this.currentUser) {
                    if (streamData.is_premium !== undefined) {
                        this.currentUser.is_premium = streamData.is_premium;
                    }
                    if (streamData.is_trial !== undefined) {
                        this.currentUser.is_trial = streamData.is_trial;
                    }
                    if (streamData.trial_ends_at !== undefined) {
                        this.currentUser.trial_ends_at = streamData.trial_ends_at;
                    }
                    if (streamData.subscription_ends_at !== undefined) {
                        this.currentUser.subscription_ends_at = streamData.subscription_ends_at;
                    }
                }

                // 🎯 AUTO-CONTEXT: Set context automatically if not already set
                // User wants infinite loop system to work from ANYWHERE (homepage, search, random, etc.)
                const muzibuStore = Alpine.store('muzibu') || Alpine.store('player');

                const currentContext = muzibuStore?.getPlayContext();

                // 🔧 FIX: Hem API'den gelen hem de parametredeki song'u kontrol et (fallback)
                const albumId = streamData.song?.album_id || song?.album_id;
                const genreId = streamData.song?.genre_id || song?.genre_id;
                const albumName = streamData.song?.album_name || song?.album_name || 'Album';
                const genreName = streamData.song?.genre_name || song?.genre_name || 'Genre';

                // 🔍 SERVER LOG: playSong başladığında API verisini logla
                serverLog('playSong', {
                    songId: id,
                    albumId: albumId,
                    genreId: genreId,
                    hasContext: !!currentContext,
                    streamDataSong: streamData.song ? {
                        album_id: streamData.song.album_id,
                        genre_id: streamData.song.genre_id,
                        title: streamData.song.title
                    } : null
                });

                // 🔍 MOBILE DEBUG: Toast ile debug (sadece root kullanıcılar)
                if (this.currentUser?.is_root) {
                    this.showToast(`🔍 store:${muzibuStore ? 'OK' : 'YOK!'} album:${albumId || '-'} genre:${genreId || '-'} ctx:${currentContext ? 'var' : 'yok'}`, 'info');
                }

                if (!currentContext && (albumId || genreId)) {

                    // Priority: Album → Genre
                    // If song has album_id, set context to album (will transition to genre when album ends)
                    // If no album, set context to genre directly (infinite loop)
                    if (albumId) {
                        muzibuStore.setPlayContext({
                            type: 'album',
                            id: albumId,
                            name: albumName,
                            offset: 0,
                            source: 'auto_detect'
                        });
                    } else if (genreId) {
                        muzibuStore.setPlayContext({
                            type: 'genre',
                            id: genreId,
                            name: genreName,
                            offset: 0,
                            source: 'auto_detect'
                        });
                    }
                }

                // 🔥 INSTANT QUEUE REFILL: Context var ise (detail page veya auto-detect), queue'yu doldur!
                // Kullanıcı playlist/album/genre'den şarkı tıkladığında diğer şarkılar anında gelsin
                const finalContext = muzibuStore?.getPlayContext();
                if (finalContext) {
                    try {
                        const nextSongs = await muzibuStore.refillQueue(1, 15); // offset=1 (mevcut şarkıdan sonraki)

                        // 🔍 MOBILE DEBUG: Queue sonucu (sadece root)
                        if (this.currentUser?.is_root) {
                            this.showToast(`🎵 Queue: ${nextSongs?.length || 0} şarkı geldi`, 'info');
                        }

                        if (nextSongs && nextSongs.length > 0) {
                            // 🛡️ DUPLICATE FILTER: Mevcut şarkı ile aynı olanları filtrele
                            const currentSongId = song.song_id;
                            const uniqueNextSongs = nextSongs.filter(s => s.song_id !== currentSongId);

                            // Queue'ya ekle (mevcut şarkı zaten 0. index'te)
                            this.queue = [song, ...uniqueNextSongs];
                            this.queueIndex = 0;
                        } else {
                            // 🔧 FIX: Queue her durumda set edilmeli!
                            this.queue = [song];
                            this.queueIndex = 0;
                        }
                    } catch (error) {
                        console.error('❌ INSTANT QUEUE REFILL hatası:', error);
                        // 🔍 MOBILE DEBUG: Hata (sadece root)
                        if (this.currentUser?.is_root) {
                            this.showToast(`❌ Queue hata: ${error.message || 'bilinmeyen'}`, 'error');
                        }
                        // 🔧 FIX: Hata olsa bile queue set edilmeli!
                        this.queue = [song];
                        this.queueIndex = 0;
                    }
                } else {
                    // 🔍 MOBILE DEBUG: Context yok (sadece root)
                    if (this.currentUser?.is_root) {
                        this.showToast('⚠️ Context yok, tek şarkı çalacak', 'warning');
                    }
                    // 🔧 FIX: Context yoksa bile queue set edilmeli!
                    this.queue = [song];
                    this.queueIndex = 0;
                }

                // 🎵 Play immediately
                await this.loadAndPlaySong(
                    streamData.stream_url,
                    streamData.stream_type,
                    streamData.preview_duration || null
                );
                this.showToast(this.frontLang?.messages?.song_playing || 'Song is playing', 'success');
            } catch (error) {
                console.error('Failed to play song:', error);
                this.isSongLoading = false;
                this.showToast(this.frontLang?.messages?.song_loading_failed || 'Song failed to load', 'error');
            }
        },

        async playSongFromQueue(index, autoplay = true) {
            if (index < 0 || index >= this.queue.length) return;

            // 🛑 Device limit exceeded - don't try to play anything
            if (this.deviceLimitExceeded) {
                return;
            }

            // 🔄 Loading state başlat (validation'dan sonra)
            this.isSongLoading = true;

            const song = this.queue[index];

            // 🚫 Failed song kontrolü - blacklist'teyse atla
            if (this.isFailedSong(song.song_id)) {
                console.warn('⏭️ Şarkı blacklist\'te, atlanıyor:', song.song_id);
                this.showToast(this.frontLang?.messages?.song_unavailable || 'Bu şarkı şu an çalınamıyor', 'warning');
                // Sonraki şarkıya geç
                if (index < this.queue.length - 1) {
                    await this.playSongFromQueue(index + 1, autoplay);
                }
                return;
            }
            this.currentSong = song;
            this.queueIndex = index;
            this.playTracked = false;
            this._nextSongPreloaded = false; // 🔄 Reset preload flag for new song
            this._firstFragLoaded = false; // 🔄 Reset first fragment flag for new song
            this._safariTrackEndTriggered = false; // 🍎 Reset Safari track end fallback flag

            // 🔍 SERVER LOG: currentSong set edildi
            serverLog('currentSongSet', {
                song_id: song.song_id,
                song_title: song.song_title,
                title: song.title,
                artist_title: song.artist_title,
                album_title: song.album_title,
                album_cover: song.album_cover ? 'VAR' : 'YOK'
            });

            // 🎨 Update player gradient colors
            this.updatePlayerColors();

            // 🍎 Update iOS Control Center / Lock Screen metadata
            this.updateMediaSession();

            // 🎯 RECENTLY PLAYED: Şarkıyı exclude listesine ekle (tekrar gelmemesi için)
            const playerStore = Alpine.store('player') || Alpine.store('muzibu');
            if (playerStore && playerStore.addToRecentlyPlayed) {
                playerStore.addToRecentlyPlayed(song.song_id);
            }

            // Check if song is favorited (background, don't wait)
            this.checkFavoriteStatus(song.song_id);

            // 🔧 FIX: Local variable kullan (race condition önleme)
            // Instance variable yerine closure ile autoplay değerini koru
            const shouldAutoplayLocal = autoplay;

            // 🚀 INSTANT PLAY: Preloaded HLS instance'ı doğrudan kullan
            // HLS.js preload (hls != null) VEYA Safari native preload (isSafariNative = true)
            if (this._preloadedNext && this._preloadedNext.songId === song.song_id && this._preloadedNext.ready && (this._preloadedNext.hls || this._preloadedNext.isSafariNative)) {
                const preloaded = this._preloadedNext;
                const preloadedHls = preloaded.hls;
                const preloadedAudioId = preloaded.audioId;
                const preloadedAudio = document.getElementById(preloadedAudioId);

                if (preloadedAudio) {
                    // Mevcut playback'i durdur (eski HLS/Howler) - preloaded audio'ya dokunma!
                    if (this.hls && this.hls !== preloadedHls) {
                        try {
                            const oldAudioId = this.activeHlsAudioId || 'hlsAudio';
                            const oldAudio = document.getElementById(oldAudioId);
                            if (oldAudio) {
                                oldAudio.pause();
                            }
                            this.hls.destroy();
                        } catch (e) {}
                        this.hls = null;
                    }
                    if (this.howl) {
                        try {
                            this.howl.stop();
                            this.howl.unload();
                        } catch (e) {}
                        this.howl = null;
                    }

                    // Progress tracking durdur
                    if (this.progressInterval) {
                        clearInterval(this.progressInterval);
                        this.progressInterval = null;
                    }

                    // 🎯 Duration'ı set et
                    if (preloaded.streamData?.song?.duration_seconds) {
                        this.duration = preloaded.streamData.song.duration_seconds;
                    } else if (song.duration_seconds) {
                        this.duration = song.duration_seconds;
                    } else if (song.duration) {
                        this.duration = song.duration;
                    }

                    // 🔄 State güncelle
                    this.hls = preloadedHls; // Safari'de null olacak
                    this.activeHlsAudioId = preloadedAudioId;
                    this.isHlsStream = true;
                    this._lastHlsUrl = preloaded.streamUrl;
                    this.currentFallbackUrl = preloaded.streamData?.fallback_url || null;

                    // 🎨 Merge streamData.song bilgilerini currentSong'a (color_hash dahil)
                    if (preloaded.streamData?.song) {
                        this.currentSong = { ...this.currentSong, ...preloaded.streamData.song };
                    }

                    // 🎨 Update player gradient colors (preloaded path)
                    this.updatePlayerColors();

                    // 🔊 Volume ayarla
                    const targetVolume = this.isMuted ? 0 : this.volume / 100;
                    preloadedAudio.volume = targetVolume;

                    const self = this;

                    // 🍎 Safari Native vs HLS.js path
                    if (preloaded.isSafariNative) {
                        // 🍎 SAFARI NATIVE: Audio element zaten src set, sadece play
                        // Duration from audio element
                        preloadedAudio.onloadedmetadata = function() {
                            if (self.currentSong?.duration && self.currentSong.duration > 0) {
                                self.duration = self.currentSong.duration;
                            } else if (preloadedAudio.duration && isFinite(preloadedAudio.duration)) {
                                self.duration = preloadedAudio.duration;
                            }
                        };

                        // Crossfade trigger
                        preloadedAudio.ontimeupdate = function() {
                            if (!self.duration || self.duration <= 0) return;
                            if (self.isCrossfading) return;
                            const timeRemaining = self.duration - preloadedAudio.currentTime;
                            if (self.crossfadeEnabled && timeRemaining <= (self.crossfadeDuration / 1000) && timeRemaining > 0) {
                                self.startCrossfade();
                            }
                        };

                        // Ended event
                        preloadedAudio.onended = function() {
                            if (!self.isCrossfading) {
                                if (self.crossfadeEnabled && self.getNextSongIndex() !== -1) {
                                    self.startCrossfade();
                                } else {
                                    self.onTrackEnded();
                                }
                            }
                        };
                    } else {
                        // 🖥️ HLS.js path
                        // 🚀 Yüklemeye devam et (preload'da stopLoad() yapılmıştı)
                        preloadedHls.startLoad(-1);

                        // Duration için LEVEL_LOADED
                        preloadedHls.on(Hls.Events.LEVEL_LOADED, function(event, data) {
                            if (data.details && data.details.totalduration) {
                                self.duration = data.details.totalduration;
                            }
                        });

                        // Şarkı bitişi için BUFFER_EOS
                        preloadedHls.on(Hls.Events.BUFFER_EOS, function() {
                            if (!self.isCrossfading) {
                                setTimeout(() => {
                                    const audio = self.getActiveHlsAudio();
                                    if (audio && audio.paused && !self.isCrossfading) {
                                        if (self.crossfadeEnabled && self.getNextSongIndex() !== -1) {
                                            self.startCrossfade();
                                        } else {
                                            self.onTrackEnded();
                                        }
                                    }
                                }, 300);
                            }
                        });

                        // Audio ended event
                        preloadedAudio.onended = function() {
                            if (!self.isCrossfading) {
                                if (self.crossfadeEnabled && self.getNextSongIndex() !== -1) {
                                    self.startCrossfade();
                                } else {
                                    self.onTrackEnded();
                                }
                            }
                        };
                    }

                    // ▶️ Çalmaya başla
                    if (shouldAutoplayLocal) {
                        try {
                            await preloadedAudio.play();
                            this.isPlaying = true;
                            this.isSongLoading = false;
                            this.startProgressTracking('hls');

                            // Event dispatch
                            window.dispatchEvent(new CustomEvent('player:play', {
                                detail: { songId: song.song_id, isLoggedIn: this.isLoggedIn }
                            }));

                            // 🎨 Update player gradient colors for preloaded song
                            this.updatePlayerColors();
                        } catch (e) {
                            console.warn('Preloaded play failed:', e);
                            this.isPlaying = false;
                            this.isSongLoading = false;
                        }
                    }

                    // 🧹 Preload state temizle (instance artık ana player'da)
                    this._preloadedNext = null;
                    this._preloadNextInProgress = false;
                    this._nextSongPreloaded = false;
                    this._safariTrackEndTriggered = false; // 🍎 Reset Safari fallback
                    this._hlsRetryCount = 0;

                    return;
                }
            }

            // 🧹 CLEANUP: Preload kullanılmadıysa (hazır değil veya farklı şarkı) temizle
            // Bu sayede yeni preload başlayabilir
            if (this._preloadedNext || this._preloadNextInProgress) {
                this._cleanupPreloadedNext();
                this._preloadNextInProgress = false;
            }

            try {
                let data;

                // 🚀 CHECK CACHE FIRST - instant playback if cached!
                const cached = this.getCachedStream(song.song_id);
                if (cached) {
                    data = cached;
                } else {
                    // Fetch from API if not cached (🔐 401 kontrolü ile)
                    const response = await this.authenticatedFetch(`/api/muzibu/songs/${song.song_id}/stream`);

                    // 🔴 401 = authenticatedFetch null döndü, logout yapıldı
                    if (!response) {
                        return;
                    }

                    if (!response.ok) {
                        const errorData = await response.json().catch(() => ({}));

                        // 🚫 GUEST REDIRECT: Kayıt olmadan dinleyemez (401)
                        if (response.status === 401 && errorData.redirect) {
                            this.showToast(errorData.message || this.frontLang?.auth?.login_required || 'Login required to listen', 'warning');
                            setTimeout(() => {
                                window.location.href = errorData.redirect;
                            }, 1000);
                            return;
                        }

                        // 💎 SUBSCRIPTION REDIRECT: Premium gerekli (402)
                        if (response.status === 402 && errorData.redirect) {
                            this.showToast(errorData.message || this.frontLang?.auth?.premium_required || 'Premium membership required', 'warning');
                            setTimeout(() => {
                                window.location.href = errorData.redirect;
                            }, 1000);
                            return;
                        }

                        // 🛑 403 = Device limit exceeded OR Session terminated
                        if (response.status === 403) {
                            // 🔐 Session terminated - another device logged in (LIFO)
                            // 🔥 FIX: Sonsuz döngü önleme - zaten handle ediliyorsa tekrar çağırma
                            if (errorData.error === 'session_terminated') {
                                if (!this._sessionTerminatedHandling) {
                                    this.handleSessionTerminated(errorData.message);
                                } else {
                                }
                                return;
                            }

                            if (errorData.error === 'device_limit_exceeded' || errorData.show_device_modal) {
                                this.handleDeviceLimitExceeded();
                                return; // Don't try next track!
                            }
                        }

                        // Other errors - try next track (but only if not device limited AND not session terminated)
                        if (!this.deviceLimitExceeded && !this._sessionTerminatedHandling) {
                            console.error('Song stream failed:', {
                                status: response.status,
                                statusText: response.statusText,
                                error: errorData,
                                songId: song.song_id
                            });
                            this.showToast(this.frontLang?.messages?.song_loading_failed_next || 'Song failed to load, skipping to next...', 'warning');
                            if (this.queueIndex < this.queue.length - 1) {
                                await this.nextTrack();
                            } else {
                                this.isPlaying = false;
                            }
                        }
                        return;
                    }

                    data = await response.json();
                }

                // Update premium status ve subscription bilgileri
                if (this.currentUser) {
                    if (data.is_premium !== undefined) {
                        this.currentUser.is_premium = data.is_premium;
                    }
                    if (data.is_trial !== undefined) {
                        this.currentUser.is_trial = data.is_trial;
                    }
                    if (data.trial_ends_at !== undefined) {
                        this.currentUser.trial_ends_at = data.trial_ends_at;
                    }
                    if (data.subscription_ends_at !== undefined) {
                        this.currentUser.subscription_ends_at = data.subscription_ends_at;
                    }
                }

                // Save fallback URL
                this.currentFallbackUrl = data.fallback_url || null;

                // 🎵 Load and optionally play
                // 🔧 FIX: shouldAutoplayLocal kullan (race condition önleme)
                await this.loadAndPlaySong(
                    data.stream_url,
                    data.stream_type || 'mp3',
                    data.preview_duration || null,
                    shouldAutoplayLocal
                );

                // ⏱️ HLS URL refresh: expires param'ına göre dinamik zamanlama
                if (data.stream_type === 'hls' && data.stream_url) {
                    const expiresParam = Number(new URL(data.stream_url).searchParams.get('expires'));
                    const nowMs = Date.now();
                    const ttlMs = expiresParam ? Math.max(60000, (expiresParam * 1000) - nowMs) : 300000; // en az 60s
                    const marginMs = Math.max(120000, Math.floor(ttlMs * 0.5)); // %50 veya min 120s önce yenile (güvenli margin)
                    const delayMs = Math.max(30000, ttlMs - marginMs);

                    setTimeout(() => {
                        this.refreshHlsUrlForCurrentSong(true);
                    }, delayMs);
                }
                // 🔧 FIX: _autoplayNext artık kullanılmıyor (local variable kullanıyoruz)

                // 🚫 REMOVED: Başlangıçta preload yapmıyoruz, %80'de yapılacak
                // this.preloadNextThreeSongs();
            } catch (error) {
                console.error('Failed to load song:', error);
                this.showToast(this.frontLang?.messages?.song_loading_failed || 'Song failed to load', 'error');
            }
        },

        // Prefetch HLS conversion for upcoming songs in queue
        prefetchHlsForQueue(currentIndex) {
            // Prefetch next 3 songs (or remaining songs if less)
            const prefetchCount = 3;
            const startIndex = currentIndex + 1;
            const endIndex = Math.min(startIndex + prefetchCount, this.queue.length);

            for (let i = startIndex; i < endIndex; i++) {
                const song = this.queue[i];
                if (song && song.song_id) {
                    // Fire and forget - just trigger the API to start HLS conversion
                    fetch(`/api/muzibu/songs/${song.song_id}/stream`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.hls_converting) {
                            }
                        })
                        .catch(() => {}); // Ignore errors for prefetch
                }
            }
        },

        async checkFavoriteStatus(songId) {
            // Reset to false while checking
            this.isLiked = false;

            // Only check if user is logged in
            if (!this.isLoggedIn) return;

            try {
                const response = await fetch(`/api/favorites/check?model_class=Modules\\Muzibu\\App\\Models\\Song&model_id=${songId}`, {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    this.isLiked = data.is_favorited || false;
                }
            } catch (error) {
                console.error('Failed to check favorite status:', error);
            }
        },

        async loadAndPlaySong(url, streamType = null, previewDuration = null, autoplay = true) {
            // Note: previewDuration parameter is deprecated and not used (preview mode removed)
            const self = this;
            const targetVolume = this.isMuted ? 0 : this.volume / 100;

            // HLS retry state reset
            this._hlsRetryCount = 0;
            this._lastHlsUrl = url;
            this._refreshedHlsUrl = null;
            this._refreshedFallbackUrl = null;

            // Stop and fade out current playback
            await this.stopCurrentPlayback();
            // 🎯 Reset intro skip flag for new song
            this.introSkipped = false;

            // Clear progress interval
            if (this.progressInterval) {
                clearInterval(this.progressInterval);
            }

            // Use stream type from API if provided, otherwise detect from URL
            let useHls = false;
            if (streamType) {
                useHls = streamType === 'hls';
            } else {
                // Fallback: detect from URL
                const isDirectAudio = url.match(/\.(mp3|ogg|wav|webm|aac|m4a)(\?|$)/i);
                const isHlsUrl = url.includes('.m3u8') || url.includes('m3u8') || url.includes('/hls/');
                useHls = isHlsUrl || !isDirectAudio;
            }

            // Use passed autoplay parameter

            if (useHls) {
                this.isHlsStream = true;
                await this.playHlsStream(url, targetVolume, autoplay);
            } else {
                this.isHlsStream = false;
                await this.playWithHowler(url, targetVolume, autoplay);
            }
        },

        // Stop current playback with fade out
        async stopCurrentPlayback() {
            const targetVolume = this.volume / 100;
            let wasStopped = false;

            // Stop Howler if playing
            if (this.howl) {
                if (this.howl.playing()) {
                    wasStopped = true;
                    // 🚀 INSTANT STOP: No fade, immediate stop
                    this.howl.stop();
                    this.howl.unload();
                    this.howl = null;
                } else {
                    this.howl.unload();
                    this.howl = null;
                }
            }

            // 🔧 FIX: Also stop howlNext (crossfade için oluşturulan)
            if (this.howlNext) {
                try {
                    this.howlNext.stop();
                    this.howlNext.unload();
                } catch (e) {}
                this.howlNext = null;
            }

            // Stop HLS if playing (check both audio elements)
            if (this.hls) {
                const audio = this.getActiveHlsAudio();
                if (audio && !audio.paused) {
                    wasStopped = true;
                    // 🚀 INSTANT STOP: No fade, immediate pause
                    audio.pause();
                }
                // 🔧 FIX: Clear instance ID BEFORE destroy to ignore pending error events
                this._currentHlsInstanceId = null;
                this.hls.destroy();
                this.hls = null;
            }

            // 🔧 FIX: Also destroy hlsNext (crossfade için oluşturulan)
            if (this.hlsNext) {
                try {
                    this.hlsNext.destroy();
                } catch (e) {}
                this.hlsNext = null;
            }

            // Also clean up hlsAudioNext if exists
            // 🚀 PRELOAD PROTECTION: Preloaded song hlsAudioNext kullanıyorsa temizleme!
            const nextAudio = document.getElementById('hlsAudioNext');
            if (nextAudio && !(this._preloadedNext && this._preloadedNext.audioId === 'hlsAudioNext')) {
                nextAudio.pause();
                nextAudio.src = '';
            }

            // Reset active HLS audio to default
            this.activeHlsAudioId = 'hlsAudio';

            // Dispatch stop event if something was actually stopped
            if (wasStopped) {
                window.dispatchEvent(new CustomEvent('player:stop'));
            }
        },

        // Play using Howler.js (for MP3, etc.)
        async playWithHowler(url, targetVolume, autoplay = true) {
            const self = this;

            // 🔍 DEBUG: Log exactly what URL we're about to pass to Howler

            // 🧹 CLEANUP: Eski Howl instance'ını temizle (Audio pool exhausted önleme)
            if (this.howl) {
                try {
                    this.howl.stop();
                    this.howl.unload();
                } catch (e) {
                    console.warn('⚠️ Howl cleanup warning:', e);
                }
                this.howl = null;
            }

            // Determine format from URL or default to mp3
            let format = ['mp3'];
            if (url.includes('.ogg')) format = ['ogg'];
            else if (url.includes('.wav')) format = ['wav'];
            else if (url.includes('.webm')) format = ['webm'];


            this.howl = new Howl({
                src: [url],
                format: format,
                html5: true,
                volume: targetVolume, // 🚀 INSTANT: Start with target volume, no fade
                autoplay: autoplay,
                onload: function() {
                    self.duration = self.howl.duration();
                },
onplay: function() {
                    self.isPlaying = true;
                    self.isSongLoading = false; // 🔄 Loading tamamlandı
                    self.startProgressTracking('howler');

                    // Dispatch event for play-limits
                    window.dispatchEvent(new CustomEvent('player:play', {
                        detail: {
                            songId: self.currentSong?.song_id,
                            isLoggedIn: self.isLoggedIn
                        }
                    }));

                    // 🚀 PRELOAD: Bir sonraki şarkıyı cache'e yükle (instant crossfade için)
                    self.preloadNextSong();
                },
                onend: function() {
                    if (!self.isCrossfading) {
                        // 🔥 Son şans: Crossfade başlatılamamışsa ve enabled ise, başlat!
                        if (self.crossfadeEnabled && self.getNextSongIndex() !== -1) {
                            self.startCrossfade();
                        } else {
                            self.onTrackEnded();
                        }
                    }
                },
                onloaderror: function(id, error) {
                    console.error('Howler load error:', error);
                    console.error('🔍 Howler ID:', id);
                    console.error('🔍 Howler._src:', self.howl?._src);
                    console.error('❌ MP3 playback failed, cannot fallback (already in fallback mode)');
                    self.showToast(self.frontLang?.messages?.song_loading_failed || 'Song failed to load', 'error');
                    self.isPlaying = false;
                    self.isSongLoading = false; // 🔄 Loading hatası

                    // Bir sonraki şarkıya geç
                    setTimeout(() => {
                        self.nextTrack();
                    }, 1500);
                },
                onplayerror: function(id, error) {
                    console.error('Howler play error:', error);
                    self.showToast(self.frontLang?.messages?.playback_error || 'Playback error', 'error');
                    self.isPlaying = false;
                    self.isSongLoading = false; // 🔄 Loading hatası
                }
            });

            if (autoplay) {
                this.howl.play();
                // 🚀 INSTANT: No fade, volume already set in Howl config
                this.isPlaying = true;
            } else {
                // Preload mode: loaded but paused
                this.isPlaying = false;
            }
        },

        // Play using HLS.js (for HLS streams)
        async playHlsStream(url, targetVolume, autoplay = true, isRetry = false, startPosition = 0) {
            const self = this;
            const audio = document.getElementById('hlsAudio');

            if (!audio) {
                console.error('HLS audio element not found');
                return;
            }


            this._lastHlsUrl = url;

            // 🛡️ Flag to prevent play() after error/fallback
            let hlsAborted = false;
            let hlsPlayStarted = false;

            // 🔥 HLS TIMEOUT FALLBACK: DISABLED - User requested removal
            // const hlsTimeoutMs = 45000;
            const hlsTimeoutId = null; // Timeout disabled

            // Helper: HLS timeout'u temizle ve basariyi logla
            const markHlsSuccess = () => {
                hlsPlayStarted = true;
                clearTimeout(hlsTimeoutId);
                self.lastFallbackReason = null; // 🧪 TEST: Clear fallback reason on success
            };

            // Check HLS.js support
            if (Hls.isSupported()) {
                // 🔧 FIX: Store reference to THIS specific HLS instance
                // Used to ignore stale error events from destroyed instances
                const hlsInstanceId = Date.now();

                // 🚀 PRELOAD MODE: Minimal buffer kullan (sadece ilk segment için)
                const isPreloadMode = !autoplay;
                const bufferLength = isPreloadMode ? 1 : 90; // Preload: sadece 1 saniye buffer istek
                const bufferSize = isPreloadMode ? 5 * 1000 * 1000 : 120 * 1000 * 1000;

                this.hls = new Hls({
                    enableWorker: false, // 🔧 FIX: Disable worker to avoid internal exceptions
                    lowLatencyMode: false,
                    maxBufferLength: bufferLength, // Preload: 1sn, Normal: 90sn
                    maxMaxBufferLength: isPreloadMode ? 5 : 180, // Preload: max 5sn, Normal: max 180sn
                    maxBufferSize: bufferSize, // Preload: 5MB, Normal: 120MB
                    backBufferLength: isPreloadMode ? 0 : 30,
                    // 🔑 KEY LOADING POLICY - Prevent keyLoadError with aggressive retries
                    keyLoadPolicy: {
                        default: {
                            maxTimeToFirstByteMs: 30000,  // 30 second timeout for first byte (increased from 15s for stability)
                            maxLoadTimeMs: 60000,         // 60 second total timeout (increased from 30s for stability)
                            timeoutRetry: {
                                maxNumRetry: 8,           // 8 timeout retries (increased from 6)
                                retryDelayMs: 1000,       // 1 second delay
                                maxRetryDelayMs: 5000     // Max 5 seconds
                            },
                            errorRetry: {
                                maxNumRetry: 10,          // 10 error retries (increased from 8)
                                retryDelayMs: 500,        // 500ms initial delay
                                maxRetryDelayMs: 5000,    // Max 5 seconds (increased from 4s)
                                backoff: 'exponential'    // Exponential backoff
                            }
                        }
                    },
                    // 🎵 FRAGMENT LOADING POLICY
                    fragLoadPolicy: {
                        default: {
                            maxTimeToFirstByteMs: 10000,  // 10 second timeout (increased from 6s for stability)
                            maxLoadTimeMs: 30000,         // 30 second timeout (increased from 20s for stability)
                            timeoutRetry: {
                                maxNumRetry: 4,           // 4 retries (increased from 2)
                                retryDelayMs: 1000,
                                maxRetryDelayMs: 5000     // Max 5 seconds (increased from 4s)
                            },
                            errorRetry: {
                                maxNumRetry: 5,           // 5 retries (increased from 3)
                                retryDelayMs: 500,
                                maxRetryDelayMs: 3000
                            }
                        }
                    },
            // 🔧 XHR SETUP - Disable credentials for CORS compatibility
            // Key endpoint uses Access-Control-Allow-Origin: * (wildcard)
            // Wildcard + credentials is invalid per CORS spec
            // Fix: Set withCredentials=false for all HLS requests
            xhrSetup: function(xhr, url) {
                xhr.withCredentials = false; // 🔑 CRITICAL: Disable credentials for CORS
                // Frag/key debug için
                xhr.addEventListener('error', () => {
                    console.warn('HLS XHR error', { url });
                });
                xhr.addEventListener('timeout', () => {
                    console.warn('HLS XHR timeout', { url });
                });
            }
        });

                // 🔧 FIX: Tag this instance with unique ID for stale event detection
                this.hls._instanceId = hlsInstanceId;
                this._currentHlsInstanceId = hlsInstanceId;

                // 🔧 FIX: Match playlist URL origin with current page origin (www vs non-www)
                // Problem: User visits www.muzibu.com.tr but playlist URL is muzibu.com.tr
                // HLS.js resolves relative key URLs from playlist base → cross-origin!
                // Solution: Force playlist URL to use same origin as current page
                let normalizedUrl = url;
                if (url.startsWith('http')) {
                    const currentOrigin = window.location.origin;
                    const urlObj = new URL(url);
                    normalizedUrl = currentOrigin + urlObj.pathname + urlObj.search + urlObj.hash;
                }

                // 🔥 CACHE BYPASS: Add timestamp to playlist URL to force fresh fetch
                const cacheBustedUrl = normalizedUrl.includes('?')
                    ? normalizedUrl + '&v=' + Date.now()
                    : normalizedUrl + '?v=' + Date.now();

                this.hls.loadSource(cacheBustedUrl);
                this.hls.attachMedia(audio);
                this.hls.startLoad(startPosition > 0 ? startPosition : -1);

                // 🔑 Error handling (only log fatal errors)
                this.hls.on(Hls.Events.ERROR, function(event, data) {
                    // Only log fatal errors for debugging
                    if (data.fatal) {
                        console.error('HLS Fatal Error:', data.type, data.details);
                    }
                });

                // 🎯 DURATION FIX: HLS manifest'ten doğru duration'ı al
                this.hls.on(Hls.Events.LEVEL_LOADED, function(event, data) {
                    if (data.details && data.details.totalduration) {
                        const hlsDuration = data.details.totalduration;
                        // DB duration ile karşılaştır, HLS daha güvenilir
                        const dbDuration = self.currentSong?.duration || 0;

                        // HLS duration'ı kullan (daha doğru)
                        if (hlsDuration > 0) {
                            self.duration = hlsDuration;
                        }
                    }
                });

                this.hls.on(Hls.Events.MANIFEST_PARSED, function() {
                    // 🛡️ Check if HLS was aborted (error occurred before manifest parsed)
                    if (hlsAborted) {
                        return;
                    }

                    // 🎯 DURATION: Önce DB'deki duration'ı kullan (HLS LEVEL_LOADED'da override edilecek)
                    if (self.currentSong?.duration && self.currentSong.duration > 0) {
                        self.duration = self.currentSong.duration;
                    }

                    audio.volume = targetVolume; // 🚀 INSTANT: Start with target volume, no fade

                    if (autoplay) {
                        audio.play().then(() => {
                            // 🛡️ Double-check: HLS might have been aborted during play promise
                            if (hlsAborted) {
                                audio.pause();
                                return;
                            }

                            // Seek to previous position if retry
                            if (startPosition > 0 && !isNaN(startPosition)) {
                                try {
                                    audio.currentTime = startPosition;
                                } catch (_) {}
                            }

                            // ✅ HLS basariyla caldi - timeout'u temizle
                            markHlsSuccess();

                            self.isPlaying = true;
                            self.isSongLoading = false; // 🔄 Loading tamamlandı
                            // 🚀 INSTANT: No fade, volume already set
                            self.startProgressTracking('hls');

                            // 🚫 REMOVED: Başlangıçta preload yok, %80'de yapılacak
                            // self.preloadNextSong();

                            // Dispatch event for play-limits (HLS)
                            window.dispatchEvent(new CustomEvent('player:play', {
                                detail: {
                                    songId: self.currentSong?.song_id,
                                    isLoggedIn: self.isLoggedIn
                                }
                            }));
                        }).catch(e => {
                            // 🛡️ Expected errors - don't show toast
                            if (e.name === 'AbortError') {
                                // Fallback tetiklendi, normal
                                self.isSongLoading = false; // 🔄 Loading hatası
                            } else if (e.name === 'NotAllowedError') {
                                // Autoplay policy - preload mode'da normal
                                // Kullanıcı play basınca çalacak
                                self.isSongLoading = false; // 🔄 Loading tamamlandı (beklemede)
                            } else {
                                // Beklenmeyen hata
                                console.error('HLS play error:', e);
                                self.showToast(self.frontLang?.messages?.playback_error || 'Playback error', 'error');
                                self.isSongLoading = false; // 🔄 Loading hatası
                            }
                        });
                    } else {
                        // Preload mode: load but don't play
                        // 🚀 İlk segment'i buffer'la (instant play için)
                        // 🎯 DURATION FIX: DB duration'ı kullan, audio.duration güvenilmez
                        self.duration = self.currentSong?.duration || audio.duration || 0;
                        self.isPlaying = false;
                        // isSongLoading = true kalacak, FRAG_BUFFERED'da false olacak
                    }
                });

                // 🚀 PRELOAD FIRST SEGMENT: İlk .ts dosyası yüklenince dur (bandwidth tasarrufu)
                this.hls.on(Hls.Events.FRAG_BUFFERED, function(event, data) {
                    // Sadece ilk fragment için tetikle (bir kez)
                    if (!autoplay && !self._firstFragLoaded) {
                        self._firstFragLoaded = true;
                        markHlsSuccess();
                        self.isSongLoading = false;

                        // 🛑 STOP LOADING: İlk segment yüklendi, geri kalanı durdur
                        // Play basınca startLoad() ile devam edecek
                        self.hls.stopLoad();
                    }
                });

                // 🎯 BUFFER_EOS: Şarkı gerçekten bittiğinde tetiklenir (ended event güvenilmez olabilir)
                this.hls.on(Hls.Events.BUFFER_EOS, function() {
                    // Zaten crossfade veya track geçişi yapılıyorsa tekrar yapma
                    if (self.isCrossfading) {
                        return;
                    }

                    // Biraz bekle (audio element ended event'i tetikleyebilir)
                    setTimeout(() => {
                        // Hala çalmıyorsa ve crossfade yapılmadıysa, şarkıyı bitir
                        const audio = self.getActiveHlsAudio?.();
                        if (audio && audio.paused && !self.isCrossfading) {
                            if (self.crossfadeEnabled && self.getNextSongIndex() !== -1) {
                                self.startCrossfade();
                            } else {
                                self.onTrackEnded();
                            }
                        }
                    }, 300);
                });

                this.hls.on(Hls.Events.ERROR, async function(event, data) {
                    // 🔧 FIX: Ignore stale error events from destroyed HLS instances
                    // When user presses N (next track), old HLS is destroyed but pending
                    // requests can still trigger error events. Check if this event is from
                    // the currently active HLS instance.
                    if (hlsInstanceId !== self._currentHlsInstanceId) {
                        console.warn('⚠️ Ignoring stale HLS error from destroyed instance:', {
                            staleInstanceId: hlsInstanceId,
                            currentInstanceId: self._currentHlsInstanceId,
                            errorDetails: data.details
                        });
                        return; // Ignore this error - it's from an old instance
                    }

                    // 🔧 FIX: Non-fatal 401/403 fragment hatalarında hemen URL yenile
                    // HLS.js retry yapmadan önce yeni signed URL al
                    const respCode = data?.response?.code || data?.response?.status || null;
                    if (!data.fatal && (respCode === 401 || respCode === 403) && data.details === 'fragLoadError') {
                        if (!self._fragRefreshInProgress) {
                            self._fragRefreshInProgress = true;
                            console.warn('🔄 Fragment 401/403 - Yeni HLS URL alınıyor...');
                            try {
                                const currentPos = self.getActiveHlsAudio?.()?.currentTime || 0;
                                await self.refreshHlsUrlForCurrentSong(true);
                            } catch (e) {
                                console.warn('⚠️ Fragment URL refresh failed:', e);
                            }
                            // Cooldown: 5 saniye içinde tekrar deneme
                            setTimeout(() => { self._fragRefreshInProgress = false; }, 5000);
                        }
                        return; // HLS.js kendi retry'ına devam etsin
                    }

                    if (data.fatal) {
                        // 🔍 DETAILED ERROR LOGGING
                        console.error('🔴 HLS FATAL ERROR:', {
                            song_id: self.currentSong?.song_id || 'Unknown',
                            song: self.currentSong?.song_title || self.currentSong?.title || 'Unknown',
                            artist: self.currentSong?.artist_title || self.currentSong?.artist?.title || 'Unknown',
                            hls_path: self.currentSong?.hls_path || 'Unknown',
                            errorType: data.type,
                            errorDetails: data.details,
                            errorFatal: data.fatal,
                            errorReason: data.reason,
                            url: data.url,
                            response: data.response,
                            position_sec: Math.round((self.getActiveHlsAudio?.()?.currentTime || 0))
                        });

                        const respCode = data?.response?.code || data?.response?.status || null;
                        if (respCode === 401 || respCode === 403) {
                            // Eğer URL expired ise logout yerine yeni imza ile dene
                            try {
                                const urlObj = new URL(self._lastHlsUrl || data.url || '');
                                const expiresParam = Number(urlObj.searchParams.get('expires')) || 0;
                                const nowSec = Math.floor(Date.now() / 1000);
                                if (expiresParam > 0 && expiresParam < nowSec) {
                                    console.warn('🔁 HLS 401/403 but URL expired, retrying with new signature');
                                    const currentPos = self.getActiveHlsAudio?.()?.currentTime || 0;
                                    const retriedExpired = await self.retryHlsWithNewUrl(targetVolume, autoplay, 'expired_signature', currentPos);
                                    if (retriedExpired) {
                                        return;
                                    }
                                }
                            } catch (_) {}

                            // 🔧 FIX: HLS 401/403 = Token sorunu, Session sorunu DEĞİL!
                            // Hemen logout yapma, önce MP3 fallback dene
                            console.warn('🔒 HLS denied (401/403) - Token sorunu, MP3 fallback deneniyor');

                            // MP3 fallback varsa dene (signed URL ile)
                            if (self.currentSong && self.currentFallbackUrl) {
                                self.triggerMp3Fallback(audio, targetVolume, '401_token_error');
                                return;
                            }

                            // Fallback yoksa sonraki şarkıya geç (logout YAPMA!)
                            console.warn('⚠️ HLS 401 ve MP3 fallback yok, sonraki şarkıya geçiliyor');
                            if (!self.deviceLimitExceeded && !self._sessionTerminatedHandling) {
                                self.nextTrack();
                            } else {
                                self.isPlaying = false;
                            }
                            return;
                        }

                // 🛡️ Set abort flag FIRST to prevent MANIFEST_PARSED from calling play()
                hlsAborted = true;
                clearTimeout(hlsTimeoutId); // Timeout'u temizle

                        // Önce yeni imzalı HLS URL ile yeniden dene (tek sefer)
                        const retried = await self.retryHlsWithNewUrl(targetVolume, autoplay, data.details || 'fatal');
                        if (retried) {
                            return;
                        }

                        // HLS yüklenemezse MP3'e fallback (SIGNED URL)
                        if (self.currentSong && self.currentFallbackUrl) {
                            // 🛑 Stop HLS audio element first (prevent AbortError)
                            if (audio) {
                                audio.pause();
                                audio.src = '';
                                audio.load();
                            }

                            // Cleanup HLS
                            if (self.hls) {
                                self.hls.destroy();
                                self.hls = null;
                            }

                            // 🔐 Use signed fallback URL from API response
                            // Toast kaldırıldı - HLS başarısız olursa sessizce MP3'e geç
                            console.warn('⚠️ HLS fallback to MP3:', {
                                details: data.details,
                                reason: data.reason,
                                url: data.url,
                                code: data?.response?.code || data?.response?.status,
                                frag: data?.frag?.sn
                            });

                            // MP3 ile çal (signed URL) - autoplay parametresini aktar!
                            self.playWithHowler(self.currentFallbackUrl, targetVolume, autoplay);
                        } else {
                            console.error('❌ HLS failed and no fallback URL available:', {
                                songId: self.currentSong?.song_id,
                                hlsError: data.details,
                                hasFallbackUrl: !!self.currentFallbackUrl
                            });
                            self.showToast(self.frontLang?.messages?.song_loading_failed_next || 'Şarkı yüklenemedi, sonrakiye geçiliyor', 'warning');
                            if (!self.deviceLimitExceeded && !self._sessionTerminatedHandling) {
                                self.nextTrack();
                            } else {
                                self.isPlaying = false;
                            }
                        }
                    }
                });

                // 🎵 CROSSFADE TRIGGER: timeupdate event (NOT throttled like setInterval!)
                // Bu event page hidden olsa bile düzgün çalışır
                audio.ontimeupdate = function() {
                    if (!self.duration || self.duration <= 0) return;

                    const currentTime = audio.currentTime;
                    const timeRemaining = self.duration - currentTime;
                    const progressPercent = (currentTime / self.duration) * 100;

                    // 🚀 INSTANT PRELOAD: Şarkı başladığında hemen sonraki şarkıyı yükle
                    if (!self._nextSongPreloaded && currentTime >= 2) {
                        self._nextSongPreloaded = true;
                        self.preloadNextSong();
                    }

                    if (self.isCrossfading) return;

                    // Son 1.5 saniyede crossfade başlat
                    if (self.crossfadeEnabled && timeRemaining <= (self.crossfadeDuration / 1000) && timeRemaining > 0) {
                        self.startCrossfade();
                    }
                };

                // Handle track end
                audio.onended = function() {
                    if (!self.isCrossfading) {
                        // 🔥 Son şans: Crossfade başlatılamamışsa ve enabled ise, başlat!
                        if (self.crossfadeEnabled && self.getNextSongIndex() !== -1) {
                            self.startCrossfade();
                        } else {
                            self.onTrackEnded();
                        }
                    }
                };

                // Get duration when available
                // 🎯 DURATION FIX: DB/HLS duration'ı öncelikli kullan, audio.duration güvenilmez olabilir
                audio.onloadedmetadata = function() {
                    // Eğer zaten valid duration varsa (LEVEL_LOADED'dan), override etme
                    if (self.duration && self.duration > 0 && self.duration < 7200) {
                        return;
                    }
                    // DB'deki duration'ı kullan
                    if (self.currentSong?.duration && self.currentSong.duration > 0) {
                        self.duration = self.currentSong.duration;
                    } else if (audio.duration && isFinite(audio.duration)) {
                        // Son çare: audio element'ten al
                        self.duration = audio.duration;
                    }
                };
            } else if (audio.canPlayType('application/vnd.apple.mpegurl')) {
                // Native HLS support (Safari)
                audio.src = url;
                audio.volume = targetVolume; // 🚀 INSTANT: Start with target volume, no fade

                // 🎵 DURATION FIX (Safari): loadedmetadata event ile duration al
                audio.onloadedmetadata = function() {
                    // Eğer zaten valid duration varsa, override etme
                    if (self.duration && self.duration > 0 && self.duration < 7200) {
                        return;
                    }
                    // DB'deki duration'ı kullan
                    if (self.currentSong?.duration && self.currentSong.duration > 0) {
                        self.duration = self.currentSong.duration;
                    } else if (audio.duration && isFinite(audio.duration)) {
                        // Son çare: audio element'ten al
                        self.duration = audio.duration;
                    }
                };

                // 🎵 CROSSFADE TRIGGER: timeupdate event for Safari
                audio.ontimeupdate = function() {
                    if (!self.duration || self.duration <= 0) return;

                    const currentTime = audio.currentTime;
                    const timeRemaining = self.duration - currentTime;
                    const progressPercent = (currentTime / self.duration) * 100;

                    // Update UI
                    self.currentTime = currentTime;
                    self.progressPercent = progressPercent;

                    // 🔍 DEBUG: Son 5 saniyede her saniye log (root user için toast)
                    if (timeRemaining <= 5 && timeRemaining > 0) {
                        const rounded = Math.floor(timeRemaining);
                        if (!self._lastDebugSecond || self._lastDebugSecond !== rounded) {
                            self._lastDebugSecond = rounded;
                            if (self.currentUser?.is_root && rounded <= 3) {
                                self.showToast(`⏱️ Kalan: ${rounded}s`, 'info');
                            }
                        }
                    }

                    // 🚀 INSTANT PRELOAD: Şarkı başladığında hemen sonraki şarkıyı yükle
                    if (!self._nextSongPreloaded && currentTime >= 2) {
                        self._nextSongPreloaded = true;
                        self.preloadNextSong();
                    }

                    if (self.isCrossfading) return;

                    // Crossfade başlat
                    if (self.crossfadeEnabled && timeRemaining <= (self.crossfadeDuration / 1000) && timeRemaining > 0) {
                        self.startCrossfade();
                    }

                    // 🔍 SERVER LOG: Son 3 saniyede durumu logla
                    if (timeRemaining <= 3 && timeRemaining > 0 && !self._lastLoggedSecond) {
                        self._lastLoggedSecond = Math.floor(timeRemaining);
                        serverLog('safariTimeUpdate', {
                            timeRemaining: timeRemaining.toFixed(2),
                            duration: self.duration,
                            currentTime: currentTime.toFixed(2),
                            safariTrackEndTriggered: self._safariTrackEndTriggered,
                            isCrossfading: self.isCrossfading
                        });
                    }
                    if (timeRemaining > 3) {
                        self._lastLoggedSecond = null;
                    }

                    // 🍎 SAFARI FALLBACK: onended event tetiklenmezse, son 0.5 saniyede track'i bitir
                    // 0.3'ten 0.5'e çıkarıldı - Safari'de daha erken tetiklensin
                    if (!self._safariTrackEndTriggered && timeRemaining <= 0.5 && timeRemaining >= 0) {
                        self._safariTrackEndTriggered = true;
                        serverLog('safariTrackEndFallback', { timeRemaining: timeRemaining.toFixed(2) });
                        if (self.currentUser?.is_root) {
                            self.showToast('🍎 Track end fallback!', 'success');
                        }
                        // 🔍 SERVER LOG: Branch info
                        serverLog('safariTrackEndBranch', {
                            isCrossfading: self.isCrossfading,
                            crossfadeEnabled: self.crossfadeEnabled,
                            nextSongIndex: self.getNextSongIndex()
                        });

                        if (!self.isCrossfading) {
                            if (self.crossfadeEnabled && self.getNextSongIndex() !== -1) {
                                serverLog('callingStartCrossfade', {});
                                self.startCrossfade();
                            } else {
                                serverLog('callingOnTrackEnded', {});
                                self.onTrackEnded();
                            }
                        } else {
                            serverLog('blockedByIsCrossfading', {});
                        }
                    }
                };

                // Safari onended fallback
                // 🍎 FIX: timeupdate fallback zaten tetiklendiyse, tekrar tetikleme!
                audio.onended = function() {
                    if (self._safariTrackEndTriggered) {
                        serverLog('onendedBlocked', { reason: 'already triggered by timeupdate' });
                        return; // timeupdate fallback zaten çağrıldı
                    }
                    serverLog('onendedFired', { isCrossfading: self.isCrossfading });
                    if (!self.isCrossfading) {
                        if (self.crossfadeEnabled && self.getNextSongIndex() !== -1) {
                            self.startCrossfade();
                        } else {
                            self.onTrackEnded();
                        }
                    }
                };

                audio.play().then(() => {
                    self.isPlaying = true;
                    self.isSongLoading = false; // 🔄 Loading tamamlandı (Safari)
                    // 🚀 INSTANT: No fade, volume already set
                    self.startProgressTracking('hls');

                    // 🚫 REMOVED: Başlangıçta preload yok, %80'de yapılacak
                    // self.preloadNextSong();

                    // Dispatch event for play-limits (Safari native HLS)
                    window.dispatchEvent(new CustomEvent('player:play', {
                        detail: {
                            songId: self.currentSong?.song_id,
                            isLoggedIn: self.isLoggedIn
                        }
                    }));
                }).catch(e => {
                    // 🛡️ Safari play errors
                    if (e.name === 'NotAllowedError') {
                        // Autoplay policy - kullanıcı etkileşimi gerekli
                        console.warn('Safari autoplay blocked, waiting for user interaction');
                        self.isSongLoading = false;
                        self.isPlaying = false;
                    } else if (e.name === 'AbortError') {
                        // Normal durum - geçiş sırasında olabilir
                        self.isSongLoading = false;
                    } else {
                        console.error('Safari HLS play error:', e);
                        self.showToast(self.frontLang?.messages?.playback_error || 'Playback error', 'error');
                        self.isSongLoading = false;
                        self.isPlaying = false;
                    }
                });
            } else {
                console.error('HLS not supported');
                this.showToast(this.frontLang?.messages?.hls_not_supported || 'HLS not supported', 'error');
            }
        },

        // 🔁 HLS retry: yeni imzalı URL ile tek sefer yeniden dene
        async retryHlsWithNewUrl(targetVolume, autoplay = true, reason = 'retry', startPosition = 0) {
            if (!this.currentSong) return false;

            this._hlsRetryCount = this._hlsRetryCount || 0;
            if (this._hlsRetryCount >= 1) {
                return false; // Tek sefer dene
            }
            this._hlsRetryCount += 1;

            let newUrl = this._refreshedHlsUrl || null;
            let newFallback = this._refreshedFallbackUrl || this.currentFallbackUrl;

            try {
                if (!newUrl) {
                    const response = await this.authenticatedFetch(`/api/muzibu/songs/${this.currentSong.song_id}/stream`);
                    if (!response || !response.ok) {
                        console.warn('HLS retry fetch failed', { status: response?.status });
                        return false;
                    }

                    const data = await response.json();
                    if (data.stream_type !== 'hls' || !data.stream_url) {
                        return false;
                    }

                    newUrl = data.stream_url;
                    newFallback = data.fallback_url || newFallback;

                    // Cache güncelle
                    if (!this.streamUrlCache) {
                        this.streamUrlCache = new Map();
                    }
                    this.streamUrlCache.set(this.currentSong.song_id, {
                        stream_url: data.stream_url,
                        stream_type: data.stream_type,
                        fallback_url: data.fallback_url,
                        preview_duration: data.preview_duration,
                        cached_at: Date.now()
                    });
                }

                this.currentFallbackUrl = newFallback || this.currentFallbackUrl;
                console.warn('🔁 HLS retry with new signed URL', { reason, attempt: this._hlsRetryCount });

                // Eski instance'ı temizle
                const currentAudio = this.getActiveHlsAudio?.() || document.getElementById('hlsAudio');
                if (currentAudio) {
                    if (!startPosition || startPosition <= 0) {
                        startPosition = currentAudio.currentTime || 0;
                    }
                    currentAudio.pause();
                    currentAudio.src = '';
                    currentAudio.load();
                }
                if (this.hls) {
                    try { this.hls.destroy(); } catch (e) {}
                    this.hls = null;
                }

                await this.playHlsStream(newUrl, targetVolume, autoplay, true, startPosition);
                return true;
            } catch (e) {
                console.warn('HLS retry failed', e);
                return false;
            }
        },

        // 🔥 HLS Timeout/Error icin MP3 Fallback Helper
        triggerMp3Fallback(audio, targetVolume, reason = 'unknown') {
            this.lastFallbackReason = reason; // 🧪 TEST: Track fallback reason

            // HLS audio element'i temizle
            if (audio) {
                audio.pause();
                audio.src = '';
                audio.load();
            }

            // HLS instance'i temizle
            if (this.hls) {
                this.hls.destroy();
                this.hls = null;
            }

            // Fallback URL varsa MP3 ile cal
            if (this.currentFallbackUrl) {
                this.showToast(this.frontLang?.messages?.hls_fallback || 'HLS failed, playing with MP3...', 'info');
                this.isHlsStream = false;
                this.playWithHowler(this.currentFallbackUrl, targetVolume);
            } else {
                this.showToast(this.frontLang?.messages?.song_loading_failed || 'Song failed to load', 'error');
                this.isPlaying = false;
            }
        },

        // Fade audio element volume using requestAnimationFrame
        fadeAudioElement(audio, fromVolume, toVolume, duration) {
            return new Promise(resolve => {
                // 🔥 FIX: Store animation frame PER audio element (not global)
                // This allows multiple audio elements to fade simultaneously during crossfade
                if (audio._fadeAnimation) {
                    cancelAnimationFrame(audio._fadeAnimation);
                }

                const startTime = performance.now();
                const volumeDiff = toVolume - fromVolume;

                const animate = (currentTime) => {
                    const elapsed = currentTime - startTime;
                    const progress = Math.min(elapsed / duration, 1);

                    // 🔒 CLAMP: Ensure volume stays within valid range [0, 1]
                    audio.volume = Math.max(0, Math.min(1, fromVolume + (volumeDiff * progress)));

                    if (progress < 1) {
                        audio._fadeAnimation = requestAnimationFrame(animate);
                    } else {
                        // 🔒 CLAMP: Ensure final volume is valid
                        audio.volume = Math.max(0, Math.min(1, toVolume));
                        audio._fadeAnimation = null;
                        resolve();
                    }
                };

                audio._fadeAnimation = requestAnimationFrame(animate);
            });
        },

        // Start progress tracking for either Howler or HLS
        startProgressTracking(type) {
            const self = this;

            // 🔧 FIX: Önce mevcut interval'i temizle (çakışma önleme)
            if (this.progressInterval) {
                clearInterval(this.progressInterval);
                this.progressInterval = null;
            }

            this.progressInterval = setInterval(() => {
                let currentTime = 0;
                let isCurrentlyPlaying = false;

                if (type === 'howler' && this.howl) {
                    currentTime = this.howl.seek();
                    isCurrentlyPlaying = this.howl.playing();
                } else if (type === 'hls') {
                    // 🔥 FIX: Use getActiveHlsAudio() instead of $refs (supports crossfade with dual audio elements)
                    const audio = this.getActiveHlsAudio();
                    if (audio) {
                        currentTime = audio.currentTime;
                        isCurrentlyPlaying = !audio.paused;
                    }
                }

                if (isCurrentlyPlaying && this.duration > 0) {
                    this.currentTime = currentTime;
                    this.progressPercent = (currentTime / this.duration) * 100;

                    // Dispatch time update event for play-limits (every second, not every 100ms)
                    if (Math.floor(currentTime) !== self._lastDispatchedSecond) {
                        self._lastDispatchedSecond = Math.floor(currentTime);
                        window.dispatchEvent(new CustomEvent('player:timeupdate', {
                            detail: {
                                currentTime: Math.floor(currentTime),
                                isLoggedIn: self.isLoggedIn
                            }
                        }));
                    }

                    // 🎵 Track play after 30 seconds (analytics: hit +1, play log with IP)
                    if (!self.playTracked && currentTime >= self.playTrackedAt && self.currentSong && self.isLoggedIn) {
                        self.playTracked = true;
                        self.trackSongPlay(self.currentSong.song_id);
                    }

                    // Check for crossfade at end of song
                    const timeRemaining = this.duration - currentTime;
                    if (this.crossfadeEnabled && timeRemaining <= (this.crossfadeDuration / 1000) && timeRemaining > 0 && !this.isCrossfading) {
                        this.startCrossfade();
                    }
                }
            }, 100);
        },

        // Start progress tracking with a specific audio element (for HLS crossfade)
        startProgressTrackingWithElement(audioElement) {
            const self = this;

            if (!audioElement) return;

            // 🔧 FIX: Önce mevcut interval'i temizle (çakışma önleme)
            if (this.progressInterval) {
                clearInterval(this.progressInterval);
                this.progressInterval = null;
            }

            this.progressInterval = setInterval(() => {
                if (!audioElement.paused && this.duration > 0) {
                    this.currentTime = audioElement.currentTime;
                    this.progressPercent = (audioElement.currentTime / this.duration) * 100;

                    // Check for crossfade at end of song
                    const timeRemaining = this.duration - this.currentTime;
                    if (this.crossfadeEnabled && timeRemaining <= (this.crossfadeDuration / 1000) && timeRemaining > 0 && !this.isCrossfading) {
                        this.startCrossfade();
                    }
                }
            }, 100);
        },

        // SPA Navigation: Navigate to URL
        async navigateTo(url) {
            history.pushState({ url: url }, '', url);
            await this.loadPage(url, true);
        },

        // SPA Navigation: Load page content
        async loadPage(url, addToHistory = true) {
            try {
                // Show loading indicator
                this.isLoading = true;

                // Fetch page content
                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }

                const html = await response.text();

                // Parse HTML and extract main content
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContent = doc.querySelector('main');

                if (newContent) {
                    // Replace main content
                    const currentMain = document.querySelector('main');
                    if (currentMain) {
                        currentMain.innerHTML = newContent.innerHTML;

                        // Scroll to top
                        window.scrollTo({ top: 0, behavior: 'smooth' });

                        // Update page title
                        const newTitle = doc.querySelector('title');
                        if (newTitle) {
                            document.title = newTitle.textContent;
                        }

                        // Update current path for active link tracking
                        this.currentPath = url;

                    }
                } else {
                    // 🔥 Main content not found = farklı layout (auth pages gibi)
                    // Full page reload yap, sonsuz döngüye girme!
                    console.warn('Main content not found, falling back to full page reload:', url);
                    this.isLoading = false;
                    window.location.href = url;
                    return;
                }

                this.isLoading = false;
            } catch (error) {
                console.error('Failed to load page:', error);
                this.showToast(this.frontLang?.messages?.page_loading_failed || 'Page loading failed', 'error');
                this.isLoading = false;

                // Fallback to full page reload on error
                window.location.href = url;
            }
        },

        shareContent(type, id) {
            this.showToast(this.frontLang?.messages?.share_link_copied || 'Share link copied', 'success');
        },

        // 🎵 Track song play (analytics) - Called after 30 seconds of playback
        // Increments songs.play_count (+1) and logs to muzibu_song_plays (with IP address)
        async trackSongPlay(songId) {
            if (!this.isLoggedIn || !songId) return;

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                const response = await fetch(`/api/muzibu/songs/${songId}/track-progress`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        progress: this.currentTime
                    })
                });

                if (!response.ok) {
                    console.warn('Track progress failed:', response.status);
                } else {
                    // ✅ Increment today's play count on successful track
                    this.todayPlayedCount++;
                }
            } catch (error) {
                console.error('Track play error:', error);
            }
        },

        async addToQueue(type, id) {
            try {
                let songs = [];

                if (type === 'song') {
                    // Single song - fetch details
                    const response = await fetch(`/api/muzibu/songs/${id}/stream`);
                    const data = await response.json();

                    if (data.song) {
                        songs = [{
                            song_id: data.song.id,
                            title: data.song.title,
                            artist_name: data.song.artist?.name || this.frontLang?.general?.artist || 'Unknown Artist',
                            album_name: data.song.album?.title || '',
                            album_cover: data.song.album?.cover_image || '/placeholder-album.jpg',
                            duration: data.song.duration || 0
                        }];
                    }
                } else if (type === 'album') {
                    // Album - fetch all songs
                    const response = await fetch(`/api/muzibu/albums/${id}`);
                    const data = await response.json();

                    if (data.album && data.album.songs) {
                        songs = data.album.songs.map(song => ({
                            song_id: song.id,
                            title: song.title,
                            artist_name: song.artist?.name || data.album.artist?.name || this.frontLang?.general?.artist || 'Unknown Artist',
                            album_name: data.album.title,
                            album_cover: data.album.cover_image || '/placeholder-album.jpg',
                            duration: song.duration || 0
                        }));
                    }
                } else if (type === 'playlist') {
                    // Playlist - fetch all songs
                    const response = await fetch(`/api/muzibu/playlists/${id}`);
                    const data = await response.json();

                    if (data.playlist && data.playlist.songs) {
                        songs = data.playlist.songs.map(song => ({
                            song_id: song.id,
                            title: song.title,
                            artist_name: song.artist?.name || this.frontLang?.general?.artist || 'Unknown Artist',
                            album_name: song.album?.title || '',
                            album_cover: song.album?.cover_image || '/placeholder-album.jpg',
                            duration: song.duration || 0
                        }));
                    }
                }

                if (songs.length > 0) {
                    // Add songs to queue
                    this.queue.push(...songs);

                    const message = this.frontLang?.messages?.song_added_to_queue || 'Song added to queue';
                    this.showToast(message, 'success');
                } else {
                    this.showToast(this.frontLang?.messages?.song_not_found || 'Song not found', 'error');
                }
            } catch (error) {
                console.error('Add to queue error:', error);
                this.showToast(this.frontLang?.messages?.queue_error || 'Error adding to queue', 'error');
            }
        },

        removeFromQueue(index) {
            if (index < 0 || index >= this.queue.length) return;

            // If removing current song, stop playback
            if (index === this.queueIndex) {
                this.isPlaying = false;
                if (this.howl) {
                    this.howl.stop();
                }
            }

            // Remove song from queue
            this.queue.splice(index, 1);

            // Adjust queue index if needed
            if (index < this.queueIndex) {
                this.queueIndex--;
            } else if (index === this.queueIndex && this.queue.length > 0) {
                // If removed current song, play next one
                if (this.queueIndex >= this.queue.length) {
                    this.queueIndex = this.queue.length - 1;
                }
                this.playSongFromQueue(this.queueIndex);
            }

            this.showToast(this.frontLang?.messages?.song_removed_from_queue || 'Song removed from queue', 'info');
        },

        clearQueue() {
            // Stop playback
            if (this.howl) {
                this.howl.stop();
            }

            // Clear queue
            this.queue = [];
            this.queueIndex = 0;
            this.currentSong = null;
            this.isPlaying = false;

            this.showToast(this.frontLang?.messages?.queue_cleared || 'Queue cleared', 'info');
        },

        playFromQueue(index) {
            if (index < 0 || index >= this.queue.length) {
                console.error('Invalid queue index:', index);
                return;
            }

            this.queueIndex = index;
            this.playSongFromQueue(index);
        },

        goToArtist(id) {
        },

        // ✅ MODULARIZED: Delegates to Alpine toast store
        showToast(message, type = 'info') {
            const toastStore = Alpine.store('toast');
            if (toastStore && toastStore.show) {
                toastStore.show(message, type);
            } else {
                console.warn('Toast store not available:', message);
            }
        },

        /**
         * 🚫 GUEST USER MODAL: Giriş yapmadan dinleyemez - kullanıcıya bildir
         */
        showAuthRequiredModal(message) {
            // Player'ı durdur (HLS veya Howl)
            if (this.hls) {
                const audio = this.getActiveHlsAudio();
                if (audio) {
                    audio.pause();
                }
            } else if (this.howl) {
                this.howl.pause();
            }
            this.isPlaying = false;

            // Toast göster
            this.showToast(message, 'warning');

            // 2 saniye sonra login sayfasına yönlendir
            setTimeout(() => {
                // Kullanıcı zaten login sayfasındaysa tekrar yönlendirme
                if (window.location.pathname !== '/login' && window.location.pathname !== '/register') {
                    window.location.href = '/login';
                }
            }, 2000);
        },

        // checkAuth() removed - user data now loaded directly from Laravel backend on page load

        async handleLogin() {
            // Form boşluk kontrolü
            if (!this.loginForm.email || !this.loginForm.password) {
                this.authError = this.frontLang?.messages?.generic_error || 'Please fill in all fields';
                return;
            }

            try {
                this.authLoading = true;
                this.authError = '';
                this.authSuccess = '';

                const response = await fetch('/api/auth/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(this.loginForm)
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    // 🔐 CSRF Token Refresh (Laravel session regenerate sonrası yeni token al)
                    if (data.csrf_token) {
                        document.querySelector('meta[name="csrf-token"]').setAttribute('content', data.csrf_token);
                    }

                    // Beni Hatırla - email'i kaydet veya sil
                    if (this.loginForm.remember) {
                        safeStorage.setItem('remembered_email', this.loginForm.email);
                    } else {
                        safeStorage.removeItem('remembered_email');
                    }

                    // SPA-friendly state update (location.reload() YOK - müzik kesintisiz!)
                    this.isLoggedIn = true;
                    this.currentUser = data.user;
                    this.showAuthModal = null;
                    this.loginForm.password = ''; // Şifreyi temizle

                    // 🛑 STREAM API ÇAĞIRMA! Session cookie henüz set edilmedi.
                    // window.location.reload() ile sayfa yenilenecek,
                    // yeni session cookie'ler orada yüklenecek.

                    // 🎵 Başarı mesajı göster
                    const welcomeMsg = (this.frontLang?.user?.welcome_back_name || 'Welcome, :name!').replace(':name', data.user.name);
                    this.showToast(welcomeMsg + ' 🎉', 'success');


                    // 🔄 SESSION FIX: Sayfa yenileme ile session cookie'lerin düzgün set edilmesini garantile
                    // SPA mode session yönetimi sorunlu - Laravel session regenerate sonrası
                    // yeni cookie'ler browser'a düzgün gelmeyebiliyor.
                    // 1 saniye bekle (toast görünsün) sonra yenile
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    // 🔐 DEVICE LIMIT EXCEEDED: Show device selection modal
                    if (data.device_limit_exceeded) {
                        this.showDeviceSelectionModal = true;
                        this.activeDevices = data.active_devices || [];
                        this.deviceLimit = data.device_limit || 1;
                        this.showAuthModal = null; // Close login modal
                    } else {
                        this.authError = data.message || this.frontLang?.messages?.generic_error || 'Invalid email or password';
                    }
                }
            } catch (error) {
                console.error('Login error:', error);
                this.authError = this.frontLang?.messages?.generic_error || 'An error occurred, please try again';
            } finally {
                this.authLoading = false;
            }
        },

        // 🎯 Modern Real-time Validation Functions
        validateName() {
            const name = this.registerForm.name.trim();
            this.validation.name.checked = true;

            if (name.length === 0) {
                this.validation.name.valid = false;
                this.validation.name.message = 'Ad soyad gereklidir';
            } else if (name.length < 3) {
                this.validation.name.valid = false;
                this.validation.name.message = 'En az 3 karakter olmalıdır';
            } else if (!/^[a-zA-ZğüşıöçĞÜŞİÖÇ\s]+$/.test(name)) {
                this.validation.name.valid = false;
                this.validation.name.message = 'Sadece harf kullanılabilir';
            } else {
                this.validation.name.valid = true;
                this.validation.name.message = '';
            }
        },

        validateEmail() {
            const email = this.registerForm.email.trim();
            this.validation.email.checked = true;

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (email.length === 0) {
                this.validation.email.valid = false;
                this.validation.email.message = 'E-posta adresi gereklidir';
            } else if (!emailRegex.test(email)) {
                this.validation.email.valid = false;
                this.validation.email.message = 'Geçerli bir e-posta adresi girin';
            } else {
                this.validation.email.valid = true;
                this.validation.email.message = '';
            }
        },

        validatePhone() {
            const phone = this.registerForm.phone.trim();
            this.validation.phone.checked = true;

            if (phone.length === 0) {
                this.validation.phone.valid = false;
                this.validation.phone.message = 'Telefon numarası gereklidir';
            } else if (phone.length < 10) {
                this.validation.phone.valid = false;
                this.validation.phone.message = 'En az 10 haneli olmalıdır';
            } else if (!/^5[0-9]{9}$/.test(phone)) {
                this.validation.phone.valid = false;
                this.validation.phone.message = '5 ile başlamalı ve 10 haneli olmalıdır';
            } else {
                this.validation.phone.valid = true;
                this.validation.phone.message = '';
            }
        },

        validatePassword() {
            const password = this.registerForm.password;
            this.validation.password.checked = true;

            if (password.length === 0) {
                this.validation.password.valid = false;
                this.validation.password.message = 'Şifre gereklidir';
            } else if (password.length < 8) {
                this.validation.password.valid = false;
                this.validation.password.message = 'En az 8 karakter olmalıdır';
            } else if (!/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/.test(password)) {
                this.validation.password.valid = false;
                this.validation.password.message = 'Büyük harf, küçük harf ve rakam içermelidir';
            } else {
                this.validation.password.valid = true;
                this.validation.password.message = '';
            }
        },

        validatePasswordConfirmation() {
            const password = this.registerForm.password;
            const confirmation = this.registerForm.password_confirmation;
            this.validation.password_confirmation.checked = true;

            if (confirmation.length === 0) {
                this.validation.password_confirmation.valid = false;
                this.validation.password_confirmation.message = 'Şifre tekrarı gereklidir';
            } else if (password !== confirmation) {
                this.validation.password_confirmation.valid = false;
                this.validation.password_confirmation.message = 'Şifreler eşleşmiyor';
            } else {
                this.validation.password_confirmation.valid = true;
                this.validation.password_confirmation.message = '';
            }
        },

        async handleRegister() {
            // Tüm validationları kontrol et
            this.validateName();
            this.validateEmail();
            this.validatePhone();
            this.validatePassword();
            this.validatePasswordConfirmation();

            // Tüm fieldler valid mi kontrol et
            const allValid = Object.values(this.validation).every(field => field.valid);

            if (!allValid) {
                this.authError = this.frontLang?.messages?.generic_error || 'Please fill in all fields correctly';
                return;
            }

            try {
                this.authLoading = true;
                this.authError = '';

                const response = await fetch('/api/auth/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(this.registerForm)
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    // 🔐 CSRF Token Refresh (Laravel session regenerate sonrası yeni token al)
                    if (data.csrf_token) {
                        document.querySelector('meta[name="csrf-token"]').setAttribute('content', data.csrf_token);
                    }

                    // SPA-friendly state update (location.reload() YOK - müzik kesintisiz!)
                    this.isLoggedIn = true;
                    this.currentUser = data.user;
                    this.showAuthModal = null;
                    this.registerForm = { name: '', email: '', phone: '', password: '', password_confirmation: '' };
                    // Reset validation
                    this.validation = {
                        name: { valid: false, checked: false, message: '' },
                        email: { valid: false, checked: false, message: '' },
                        phone: { valid: false, checked: false, message: '' },
                        password: { valid: false, checked: false, message: '' },
                        password_confirmation: { valid: false, checked: false, message: '' }
                    };

                    // If there's a current song playing, reload it without preview
                    if (this.currentSong && this.currentSong.song_id) {
                        const currentTime = this.currentTime || 0;
                        const wasPlaying = this.isPlaying;

                        // Reload song from API (will get full access now)
                        fetch(`/api/muzibu/songs/${this.currentSong.song_id}/stream`)
                            .then(res => res.json())
                            .then(async data => {
                                if (data.stream_url) {
                                    // Stop current playback
                                    await this.stopCurrentPlayback();

                                    // Load without preview (data.preview_duration will be null for trial users)
                                    await this.loadAndPlaySong(
                                        data.stream_url,
                                        data.stream_type || 'mp3',
                                        data.preview_duration || null,
                                        false // Don't autoplay, let user resume
                                    );

                                    // Restore position
                                    if (currentTime > 0) {
                                        this.seekTo(null, currentTime);
                                    }

                                    // Resume if was playing
                                    if (wasPlaying) {
                                        this.togglePlayPause();
                                    }

                                }
                            })
                            .catch(err => console.error('Failed to reload song:', err));
                    }

                    // 🎵 Başarı mesajı göster
                    const welcomePremiumMsg = (this.frontLang?.user?.welcome_premium || 'Welcome, :name! Your premium trial has started.').replace(':name', data.user.name);
                    this.showToast(welcomePremiumMsg + ' 🎉', 'success');


                    // 🔄 SESSION FIX: Sayfa yenileme ile session cookie'lerin düzgün set edilmesini garantile
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    this.authError = data.message || this.frontLang?.messages?.generic_error || 'Registration failed';
                }
            } catch (error) {
                console.error('Register error:', error);
                this.authError = this.frontLang?.messages?.generic_error || 'An error occurred, please try again';
            } finally {
                this.authLoading = false;
            }
        },

        async handleForgotPassword() {
            try {
                this.authLoading = true;
                this.authError = '';
                this.authSuccess = '';

                const response = await fetch('/api/auth/forgot-password', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(this.forgotForm)
                });

                const data = await response.json();

                if (response.ok) {
                    this.authSuccess = this.frontLang?.user?.reset_password || 'Password reset link has been sent to your email! ✉️';
                    this.forgotForm = { email: '' };
                    // 3 saniye sonra login sayfasına yönlendir
                    setTimeout(() => {
                        window.location.href = '/login';
                    }, 3000);
                } else {
                    this.authError = data.message || this.frontLang?.messages?.generic_error || 'Email could not be sent';
                }
            } catch (error) {
                console.error('Forgot password error:', error);
                this.authError = this.frontLang?.messages?.generic_error || 'An error occurred, please try again';
            } finally {
                this.authLoading = false;
            }
        },

        async logout() {
            // Çift tıklamayı engelle
            if (this.isLoggingOut) return;


            // Hemen UI'ı güncelle
            this.isLoggingOut = true;

            // State temizle (logout öncesi)
            this.isLoggedIn = false;
            this.currentUser = null;
            // NOT: Player state'i (queue, currentSong) silmiyoruz - kullanıcı tekrar giriş yapınca devam edebilsin

            // Session polling durdur
            if (this.sessionPollInterval) {
                clearInterval(this.sessionPollInterval);
                this.sessionPollInterval = null;
            }

            // 🔐 FORM-BASED LOGOUT: CSRF token ile hidden form oluştur ve submit et
            // Bu yöntem CSRF mismatch sorununu çözer
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/logout';
            form.style.display = 'none';

            // CSRF token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = document.querySelector('meta[name="csrf-token"]')?.content || '';
            form.appendChild(csrfInput);

            // Form'u body'e ekle ve submit et
            document.body.appendChild(form);
            form.submit();
        },

        // 🧹 Clean queue: Remove null/undefined songs
        cleanQueue(songs) {
            if (!Array.isArray(songs)) return [];
            return songs.filter(song => song !== null && song !== undefined && typeof song === 'object');
        },

        toggleTheme() {
            this.isDarkMode = !this.isDarkMode;
            safeStorage.setItem('theme', this.isDarkMode ? 'dark' : 'light');
            const darkModeMsg = this.frontLang?.user?.dark_mode_on || 'Dark mode on';
            const lightModeMsg = this.frontLang?.user?.light_mode_on || 'Light mode on';
            this.showToast(this.isDarkMode ? darkModeMsg : lightModeMsg, 'success');
        },

        dragStart(event, index) {
            // Guard: Ensure event and dataTransfer exist
            if (!event || !event.dataTransfer) {
                console.warn('dragStart: Invalid event or dataTransfer');
                return;
            }
            this.draggedIndex = index;
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/html', event.target);
        },

        dragOver(index) {
            if (this.draggedIndex !== null && this.draggedIndex !== index) {
                this.dropTargetIndex = index;
            }
        },

        drop(dropIndex) {
            if (this.draggedIndex === null || this.draggedIndex === dropIndex) {
                this.draggedIndex = null;
                this.dropTargetIndex = null;
                return;
            }

            // Guard: Ensure valid indices and songs exist
            if (!this.queue[this.draggedIndex] || !this.queue[dropIndex]) {
                console.warn('drop: Invalid queue indices or undefined songs');
                this.draggedIndex = null;
                this.dropTargetIndex = null;
                return;
            }

            // Reorder queue
            const draggedSong = this.queue[this.draggedIndex];
            const newQueue = [...this.queue];

            // Remove dragged item
            newQueue.splice(this.draggedIndex, 1);

            // Insert at drop position
            newQueue.splice(dropIndex, 0, draggedSong);

            // Update queueIndex if needed
            if (this.queueIndex === this.draggedIndex) {
                // Currently playing song was moved
                this.queueIndex = dropIndex;
            } else if (this.draggedIndex < this.queueIndex && dropIndex >= this.queueIndex) {
                // Moved from before to after current
                this.queueIndex--;
            } else if (this.draggedIndex > this.queueIndex && dropIndex <= this.queueIndex) {
                // Moved from after to before current
                this.queueIndex++;
            }

            this.queue = newQueue;
            this.draggedIndex = null;
            this.dropTargetIndex = null;
            this.showToast(this.frontLang?.messages?.queue_updated || 'Queue updated', 'success');
        },

        dragEnd() {
            this.draggedIndex = null;
            this.dropTargetIndex = null;
        },

        // ✅ MODULARIZED: Moved to muzibu-cache.js
        async clearCache() {
            const cacheModule = muzibuCache();
            await cacheModule.clearAll();
        },

        // ═══════════════════════════════════════════════════════════════════
        // 🚀 PRELOAD & QUEUE FUNCTIONS (Fixed: Moved from playLimits to muzibuApp)
        // ═══════════════════════════════════════════════════════════════════

        /**
         * 🚀 PRELOAD FIRST IN QUEUE: Backward compatibility wrapper
         */
        async preloadFirstInQueue() {
            // 🔄 OPTIMIZED: Sadece 1 şarkı preload et (3 değil)
            await this.preloadNextSong();
        },

        /**
         * 🚀 AGGRESSIVE PRELOAD: İlk 3 şarkıyı preload et (0ms transition)
         */
        async preloadNextThreeSongs() {
            // 🚫 Skip if not premium (prevent 402 spam)
            if (!this.isLoggedIn || (!this.currentUser?.is_premium && !this.currentUser?.is_trial)) {
                return;
            }

            // Queue kontrolü
            if (!this.queue || this.queue.length <= 1) return;

            // Mevcut queueIndex'ten sonraki 3 şarkıyı al
            const currentIndex = this.queueIndex || 0;
            const songsToPreload = [];

            // İlk 3 şarkıyı topla (mevcut şarkıdan sonra)
            for (let i = 1; i <= 3; i++) {
                const nextIndex = currentIndex + i;
                if (nextIndex < this.queue.length) {
                    const song = this.queue[nextIndex];
                    if (song && song.song_id) {
                        songsToPreload.push(song);
                    }
                }
            }

            // Boş liste kontrolü
            if (songsToPreload.length === 0) {
                return;
            }


            // Paralel preload (3 şarkıyı aynı anda yükle)
            const preloadPromises = songsToPreload.map(song =>
                this.preloadSongOnHover(song.song_id)
            );

            // Tüm preload'lar tamamlanana kadar bekle (ama hata olsa bile devam et)
            await Promise.allSettled(preloadPromises);

        },

        /**
         * 🚀 AGGRESSIVE PRELOAD: Stream URL'lerini cache'le + HLS playlist preload
         * @param {number} songId - Ön yüklenecek şarkı ID
         */
        async preloadSongOnHover(songId) {
            // Initialize cache if not exists
            if (!this.streamUrlCache) {
                this.streamUrlCache = new Map();
            }
            if (!this.preloadedSongs) {
                this.preloadedSongs = new Set();
            }

            // 🔧 FIX: Cache'de varsa expire kontrolü yap
            const cached = this.streamUrlCache.get(songId);
            if (cached) {
                // URL'deki expires parametresini kontrol et
                try {
                    const urlObj = new URL(cached.stream_url);
                    const expiresParam = Number(urlObj.searchParams.get('expires')) || 0;
                    const nowSec = Math.floor(Date.now() / 1000);
                    const marginSec = 120; // 2 dakika margin

                    // Henüz expire olmamışsa (margin ile) cache'i kullan
                    if (expiresParam > 0 && expiresParam > (nowSec + marginSec)) {
                        return; // Cache hala geçerli
                    }

                    // Expired veya expire olmak üzere - cache'i temizle, yeni URL al
                    this.streamUrlCache.delete(songId);
                } catch (e) {
                    // URL parse hatası - cache'i temizle
                    this.streamUrlCache.delete(songId);
                }
            }

            try {
                // 🚀 Fetch stream URL and cache it (🔐 401 kontrolü ile)
                const response = await this.authenticatedFetch(`/api/muzibu/songs/${songId}/stream`, { ignoreAuthError: true });
                if (!response || !response.ok) return;

                const data = await response.json();

                // Cache the stream data for instant playback later
                this.streamUrlCache.set(songId, {
                    stream_url: data.stream_url,
                    stream_type: data.stream_type,
                    fallback_url: data.fallback_url,
                    preview_duration: data.preview_duration,
                    cached_at: Date.now()
                });

                // 🎯 Preload HLS playlist (triggers browser cache) - 401 kontrolü ile!
                if (data.stream_type === 'hls' && data.stream_url) {
                    try {
                        const hlsResponse = await fetch(data.stream_url);
                        if (hlsResponse.status === 401 || hlsResponse.status === 403) {
                            // Token geçersiz - şarkıyı blacklist'e al
                            console.warn('⚠️ Preload HLS 401 - şarkı blacklist\'e ekleniyor:', songId);
                            this.streamUrlCache.delete(songId);
                            this.addToFailedSongs(songId);

                            // Queue'dan çıkar
                            const indexToRemove = this.queue.findIndex(s => s.song_id === songId);
                            if (indexToRemove > -1 && indexToRemove !== this.queueIndex) {
                                this.queue.splice(indexToRemove, 1);
                            }
                            return;
                        }
                    } catch (e) {
                        // Network hatası - sessizce geç
                    }
                }

                this.preloadedSongs.add(songId);

            } catch (error) {
                // Silently ignore preload errors
            }
        },

        /**
         * 🚀 GET CACHED STREAM: Return cached stream URL if available
         */
        getCachedStream(songId) {
            if (!this.streamUrlCache) return null;

            const cached = this.streamUrlCache.get(songId);
            if (!cached) return null;

            // 🔧 FIX: URL'deki expires parametresine bak (daha güvenilir)
            try {
                const urlObj = new URL(cached.stream_url);
                const expiresParam = Number(urlObj.searchParams.get('expires')) || 0;
                const nowSec = Math.floor(Date.now() / 1000);
                const marginSec = 60; // 1 dakika margin (çalma başlamadan önce)

                if (expiresParam > 0 && expiresParam <= (nowSec + marginSec)) {
                    // URL expired veya expire olmak üzere
                    this.streamUrlCache.delete(songId);
                    return null;
                }
            } catch (e) {
                // URL parse hatası - fallback to timestamp check
                if (Date.now() - cached.cached_at > 240000) {
                    this.streamUrlCache.delete(songId);
                    return null;
                }
            }

            return cached;
        },

        /**
         * 🚫 FAILED SONGS: Çalınamayan şarkıları blacklist'e al (5 dakika)
         */
        addToFailedSongs(songId) {
            if (!this._failedSongs) {
                this._failedSongs = new Map();
            }
            // 5 dakika sonra otomatik temizlenecek
            this._failedSongs.set(songId, Date.now() + 300000);
        },

        isFailedSong(songId) {
            if (!this._failedSongs) return false;
            const expiry = this._failedSongs.get(songId);
            if (!expiry) return false;

            // Süre dolmuşsa listeden çıkar
            if (Date.now() > expiry) {
                this._failedSongs.delete(songId);
                return false;
            }
            return true;
        },

        clearFailedSongs() {
            if (this._failedSongs) {
                this._failedSongs.clear();
            }
        },

        /**
         * 🚀 PRELOAD NEXT SONG: Sonraki şarkının ilk HLS segment'ini yükle (instant geçiş için)
         * Şarkı çalarken 10 saniye sonra çağrılır, next basınca anında geçiş sağlar
         */
        async preloadNextSong() {
            // Zaten preload işlemi devam ediyorsa çık
            if (this._preloadNextInProgress) {
                return;
            }

            const nextIndex = this.getNextSongIndex();
            if (nextIndex === -1) return; // Sonraki şarkı yok

            const nextSong = this.queue[nextIndex];
            if (!nextSong) return;

            // Zaten bu şarkı preload edilmişse çık
            if (this._preloadedNext && this._preloadedNext.songId === nextSong.song_id && this._preloadedNext.ready) {
                return;
            }

            // Önceki preload'u temizle (farklı şarkıysa)
            this._cleanupPreloadedNext();

            this._preloadNextInProgress = true;
            const self = this;

            try {
                // 1️⃣ Stream URL'i al
                const response = await this.authenticatedFetch(`/api/muzibu/songs/${nextSong.song_id}/stream`, { ignoreAuthError: true });
                if (!response) {
                    this._preloadNextInProgress = false;
                    return;
                }

                if (!response.ok) {
                    this._preloadNextInProgress = false;
                    return;
                }

                const data = await response.json();

                // URL Cache'e yaz (backup için)
                if (!this.streamUrlCache) {
                    this.streamUrlCache = new Map();
                }
                this.streamUrlCache.set(nextSong.song_id, {
                    stream_url: data.stream_url,
                    stream_type: data.stream_type,
                    fallback_url: data.fallback_url,
                    preview_duration: data.preview_duration,
                    cached_at: Date.now()
                });

                // 2️⃣ HLS ise gerçek preload yap (ilk segment)
                if (data.stream_type === 'hls' && data.stream_url && typeof Hls !== 'undefined' && Hls.isSupported()) {
                    // 🔄 Aktif OLMAYAN audio element'i kullan (çakışma önleme)
                    // Eğer hlsAudioNext aktifse → hlsAudio kullan, tersi de geçerli
                    const audioId = this.activeHlsAudioId === 'hlsAudioNext' ? 'hlsAudio' : 'hlsAudioNext';
                    let nextAudio = document.getElementById(audioId);
                    if (!nextAudio) {
                        nextAudio = document.createElement('audio');
                        nextAudio.id = audioId;
                        nextAudio.crossOrigin = 'anonymous';
                        nextAudio.preload = 'auto';
                        document.body.appendChild(nextAudio);
                    } else {
                        // 🧹 Mevcut audio'yu temizle (çakışma önleme)
                        try {
                            nextAudio.pause();
                            nextAudio.src = '';
                            nextAudio.load();
                        } catch (e) {}
                    }

                    // Yeni HLS instance oluştur (sadece İLK SEGMENT için düşük buffer)
                    // Segment süresi ~10sn, maxBufferLength: 8 ile sadece 1 segment yüklenir
                    const hlsPreload = new Hls({
                        enableWorker: false,
                        lowLatencyMode: false,
                        maxBufferLength: 8,   // 8 saniye - sadece ilk segment (10sn) yüklenecek
                        maxMaxBufferLength: 10,
                        maxBufferSize: 10 * 1000 * 1000,
                        backBufferLength: 0,
                        startLevel: -1,
                        abrEwmaDefaultEstimate: 500000
                    });

                    // State'i kaydet
                    this._preloadedNext = {
                        songId: nextSong.song_id,
                        song: nextSong,
                        hls: hlsPreload,
                        audioId: audioId,
                        streamUrl: data.stream_url,
                        streamData: data,
                        ready: false
                    };

                    hlsPreload.loadSource(data.stream_url);
                    hlsPreload.attachMedia(nextAudio);

                    // İlk segment yüklenince hazır işaretle ve DURDUR
                    hlsPreload.on(Hls.Events.FRAG_BUFFERED, function(event, fragData) {
                        if (self._preloadedNext && self._preloadedNext.songId === nextSong.song_id && !self._preloadedNext.ready) {
                            self._preloadedNext.ready = true;
                            self._preloadNextInProgress = false;

                            // 🛑 İlk segment yüklendi, DURDUR (bandwidth tasarrufu)
                            // startLoad() ile devam ettirilecek
                            try {
                                hlsPreload.stopLoad();
                            } catch (e) {
                                console.warn('stopLoad error:', e);
                            }
                        }
                    });

                    // Hata durumu
                    hlsPreload.on(Hls.Events.ERROR, function(event, errData) {
                        if (errData.fatal) {
                            console.warn('⚠️ Preload HLS error:', errData.details);
                            self._cleanupPreloadedNext();
                            self._preloadNextInProgress = false;
                        }
                    });

                } else if (data.stream_type === 'hls' && data.stream_url) {
                    // 🍎 SAFARI NATIVE HLS PRELOAD
                    // Safari doesn't support HLS.js, use native <audio> element
                    const audioId = this.activeHlsAudioId === 'hlsAudioNext' ? 'hlsAudio' : 'hlsAudioNext';
                    let nextAudio = document.getElementById(audioId);
                    if (!nextAudio) {
                        nextAudio = document.createElement('audio');
                        nextAudio.id = audioId;
                        nextAudio.crossOrigin = 'anonymous';
                        nextAudio.preload = 'auto';
                        document.body.appendChild(nextAudio);
                    } else {
                        try {
                            nextAudio.pause();
                            nextAudio.src = '';
                            nextAudio.load();
                        } catch (e) {}
                    }

                    // State'i kaydet (Safari için hls = null)
                    this._preloadedNext = {
                        songId: nextSong.song_id,
                        song: nextSong,
                        hls: null, // Safari native, no HLS.js instance
                        audioId: audioId,
                        streamUrl: data.stream_url,
                        streamData: data,
                        ready: false,
                        isSafariNative: true
                    };

                    // Safari native HLS: Set src and let browser preload
                    nextAudio.src = data.stream_url;
                    nextAudio.volume = 0; // Silent preload

                    // Safari loadeddata event = first segment ready
                    nextAudio.onloadeddata = () => {
                        if (self._preloadedNext && self._preloadedNext.songId === nextSong.song_id && !self._preloadedNext.ready) {
                            self._preloadedNext.ready = true;
                            self._preloadNextInProgress = false;
                            // 🛑 Pause to stop further buffering (save bandwidth)
                            try {
                                nextAudio.pause();
                            } catch (e) {}
                        }
                    };

                    nextAudio.onerror = () => {
                        // Safari preload hatası - sessizce devam et (network/stream sorunu olabilir)
                        self._preloadNextInProgress = false;
                    };

                } else {
                    // MP3 veya diğer durumlar: sadece URL cache'le
                    this._preloadNextInProgress = false;
                }

            } catch (error) {
                console.error('Preload error:', error);
                this._preloadNextInProgress = false;
            }
        },

        /**
         * 🧹 Preloaded next song'u temizle
         */
        _cleanupPreloadedNext() {
            if (this._preloadedNext) {
                // 🧹 HLS instance'ı destroy et
                if (this._preloadedNext.hls) {
                    try {
                        this._preloadedNext.hls.destroy();
                    } catch (e) {}
                }

                // 🧹 Audio element'i temizle (MediaSource bağlantısını kes)
                if (this._preloadedNext.audioId) {
                    const audio = document.getElementById(this._preloadedNext.audioId);
                    if (audio) {
                        try {
                            audio.pause();
                            audio.removeAttribute('src');
                            audio.load(); // MediaSource'u sıfırlar
                        } catch (e) {}
                    }
                }

                this._preloadedNext = null;
            }
        },

        async refreshHlsUrlForCurrentSong(applyToActive = false) {
            if (!this.currentSong) return;

            try {
                const response = await this.authenticatedFetch(`/api/muzibu/songs/${this.currentSong.song_id}/stream`);
                if (!response || !response.ok) return;

                const data = await response.json();
                if (data.stream_type === 'hls' && data.stream_url) {
                    this.currentFallbackUrl = data.fallback_url || this.currentFallbackUrl;
                    this._refreshedHlsUrl = data.stream_url;
                    this._refreshedFallbackUrl = data.fallback_url || null;
                    // HLS yürürken URL update etmek riskli; yeni URL'yi cache et
                    if (!this.streamUrlCache) {
                        this.streamUrlCache = new Map();
                    }
                    this.streamUrlCache.set(this.currentSong.song_id, {
                        stream_url: data.stream_url,
                        stream_type: data.stream_type,
                        fallback_url: data.fallback_url,
                        preview_duration: data.preview_duration,
                        cached_at: Date.now()
                    });

                    // İstek geldiyse aktif player'a anlık swap et (hatasız devam için)
                    // 🎯 FIX: Sadece şarkı çalıyorsa swap yap! Durdurulmuşsa dokunma!
                    if (applyToActive && this.isPlaying && this.isHlsStream && this.hls && this.getActiveHlsAudio()) {
                        try {
                            const audio = this.getActiveHlsAudio();
                            // Double-check: audio gerçekten çalıyor mu?
                            if (audio && !audio.paused) {
                                const startPos = audio?.currentTime || 0;
                                this.hls.stopLoad();
                                this.hls.loadSource(data.stream_url);
                                this.hls.startLoad(startPos);
                            }
                        } catch (e) {
                            console.warn('HLS live swap failed, will use cached URL on retry:', e);
                        }
                    }
                }
            } catch (error) {
                console.warn('HLS refresh failed:', error);
            }
        },

        /**
         * 🎯 QUEUE MONITOR: setInterval ile queue durumunu kontrol et
         * Her 10 saniyede queue kontrol edilir, 3 şarkıya düşerse otomatik refill
         */
        startQueueMonitor() {
            // Mevcut interval varsa temizle
            if (this.queueMonitorInterval) {
                clearInterval(this.queueMonitorInterval);
            }

            // Her 10 saniyede kontrol et
            this.queueMonitorInterval = setInterval(() => {
                this.checkAndRefillQueue();
            }, 10000); // 10 saniye
        },

        /**
         * 🔄 QUEUE REFILL CHECKER: Queue 3 şarkıya düştüyse otomatik refill
         */
        async checkAndRefillQueue() {
            try {
                // Queue kontrolü
                const queueLength = this.queue.length - this.queueIndex;

                // Sadece queue varsa log yaz (boş queue spam yapmasın)
                if (this.queue.length > 0) {
                }

                // Eğer 3 veya daha az şarkı kaldıysa refill et
                if (queueLength <= 3) {
                    // Context var mı kontrol et
                    const context = Alpine.store('muzibu')?.getPlayContext();

                    if (!context) {
                        // Sadece ilk kez uyar (spam yapmasın)
                        if (!this._noContextWarningShown && this.queue.length > 0) {
                            // Console logs removed - no context is normal on initial load
                            this._noContextWarningShown = true;
                        }
                        return;
                    }

                    // Context varsa flag'i resetle (yeni session için)
                    this._noContextWarningShown = false;

                    // Auto-refilling queue (silent operation)

                    // Mevcut offset'i hesapla (kaç şarkı çalındı)
                    const currentOffset = context.offset || 0;

                    // Alpine store'dan refillQueue çağır
                    const newSongs = await Alpine.store('muzibu').refillQueue(currentOffset, 15);

                    if (newSongs && newSongs.length > 0) {
                        // 🧹 QUEUE CLEANUP: Eski çalınan şarkıları sil (memory optimization)
                        // currentIndex'ten önce sadece 5 şarkı tut (geri gitmek için)
                        const keepPreviousSongs = 5;
                        const cleanupStartIndex = Math.max(0, this.queueIndex - keepPreviousSongs);

                        if (cleanupStartIndex > 0) {
                            // Eski şarkıları sil
                            const removedCount = cleanupStartIndex;
                            this.queue = this.queue.slice(cleanupStartIndex);
                            this.queueIndex = this.queueIndex - cleanupStartIndex;
                        }

                        // Queue'ya yeni şarkıları ekle
                        this.queue = [...this.queue, ...newSongs];

                        // İlk şarkıyı preload et
                        this.preloadFirstInQueue();
                    } else {
                        console.warn('⚠️ Auto-refill returned empty - queue might end soon!');

                        // Context Transition: Eğer queue boşsa Genre'ye geç
                        if (context.type !== 'genre') {
                            // TODO: Context transition logic (Phase 4 - Priority 4)
                        }
                    }
                }
            } catch (error) {
                console.error('❌ Queue check error:', error);
            }
        },

        /**
         * 🎵 BACKGROUND PLAYBACK: Tarayıcı minimize olsa bile müzik çalsın
         * Page Visibility API kullanarak arka planda çalmaya devam et
         */
        enableBackgroundPlayback() {
            try {
                // Page Visibility API - Tarayıcı minimize/hidden olunca bile çalmaya devam et
                document.addEventListener('visibilitychange', () => {
                    if (document.hidden) {
                        // Müzik çalmaya devam etsin (hiçbir şey yapma, otomatik devam eder)
                    } else {
                        // Sayfa görünür olunca sync yap
                        this.syncPlayerState();
                    }
                });

                // Audio tag'ine background playback özelliği ekle
                if (this.audio) {
                    // Modern browsers için background playback hints
                    this.audio.setAttribute('playsinline', '');
                    this.audio.setAttribute('webkit-playsinline', '');
                }

            } catch (error) {
                console.error('❌ Background playback error:', error);
            }
        },

        /**
         * ⏱️ SUBSCRIPTION COUNTDOWN: Premium/Trial bitiş süresini takip et
         * Süre bitince: Şarkıyı durdur + Cache temizle + Abonelik sayfasına yönlendir
         */
        startSubscriptionCountdown() {
            // Sadece login olan kullanıcılar için
            if (!this.isLoggedIn || !this.currentUser) {
                return;
            }

            // Trial veya subscription bitiş tarihini al (hangisi daha yakınsa)
            const trialEnd = this.currentUser.trial_ends_at ? new Date(this.currentUser.trial_ends_at) : null;
            const subscriptionEnd = this.currentUser.subscription_ends_at ? new Date(this.currentUser.subscription_ends_at) : null;

            let expiresAt = null;
            if (trialEnd && subscriptionEnd) {
                // İkisi de varsa, hangisi daha yakınsa onu kullan
                expiresAt = trialEnd < subscriptionEnd ? trialEnd : subscriptionEnd;
            } else if (trialEnd) {
                expiresAt = trialEnd;
            } else if (subscriptionEnd) {
                expiresAt = subscriptionEnd;
            }

            // Bitiş tarihi yoksa countdown başlatma
            if (!expiresAt) {
                return;
            }

            // Her saniye kontrol et
            const countdownInterval = setInterval(() => {
                const now = new Date();
                const timeLeft = expiresAt - now;

                // Süre doldu
                if (timeLeft <= 0) {
                    clearInterval(countdownInterval);
                    console.warn('⏰ Subscription expired! Stopping playback and redirecting...');

                    // 1. Şarkıyı durdur
                    if (this.isPlaying) {
                        if (this.howl) {
                            this.howl.pause();
                        } else if (this.hls) {
                            const audio = this.getActiveHlsAudio();
                            if (audio) audio.pause();
                        }
                        this.isPlaying = false;
                        window.dispatchEvent(new CustomEvent('player:pause'));
                    }

                    // 2. Toast göster
                    this.showToast(this.frontLang?.messages?.subscription_expired || 'Your subscription has expired. Redirecting to subscription page...', 'warning');

                    // 3. 2 saniye bekle, sonra cache temizle ve redirect
                    setTimeout(() => {
                        // Hard reload (cache temizle)
                        window.location.href = '/subscription/plans';
                    }, 2000);
                }

                // Subscription time check (silent)
            }, 1000); // Her saniye kontrol
        },

        /**
         * 🔄 Player state sync (sayfa visible olunca)
         */
        syncPlayerState() {
            // UI'ı güncelle
            if (this.audio) {
                this.isPlaying = !this.audio.paused;
                this.currentTime = this.audio.currentTime || 0;
            }
        },

        /**
         * 💾 AUTO-SAVE: State değişikliklerini izle ve otomatik kaydet
         * $watch ile queue, song, volume, shuffle, repeat değişikliklerini takip et
         */
        setupAutoSave() {
            // Queue değiştiğinde kaydet
            this.$watch('queue', () => {
                this.saveQueueState();
            });

            // Queue index değiştiğinde kaydet
            this.$watch('queueIndex', () => {
                this.saveQueueState();
            });

            // Şarkı değiştiğinde kaydet
            this.$watch('currentSong', () => {
                this.saveQueueState();
            });

            // Playing/pause durumu değiştiğinde kaydet
            this.$watch('isPlaying', () => {
                this.saveQueueState();
            });

            // Volume değiştiğinde kaydet VE gerçek audio volume'u güncelle
            this.$watch('volume', (newVolume) => {
                this.saveQueueState();

                // 🔊 FIX: Gerçek audio volume'u güncelle (MAX butonu, klavye vs için)
                const volumeValue = newVolume / 100;

                if (this.howl) {
                    this.howl.volume(this.isMuted ? 0 : volumeValue);
                }
                if (this.hls) {
                    const audio = this.getActiveHlsAudio();
                    if (audio) {
                        audio.volume = this.isMuted ? 0 : volumeValue;
                    }
                }

                // localStorage'a kaydet
                safeStorage.setItem('volume', Math.round(newVolume));
            });

            // Shuffle değiştiğinde kaydet
            this.$watch('shuffle', () => {
                this.saveQueueState();
            });

            // Repeat mode değiştiğinde kaydet
            this.$watch('repeatMode', () => {
                this.saveQueueState();
            });

            // 🕒 Her 5 saniyede bir currentTime'ı kaydet (progress tracking)
            setInterval(() => {
                if (this.isPlaying && this.currentSong) {
                    this.saveQueueState();
                }
            }, 5000);
        },

        /**
         * 🔄 CLEAR PLAYER STATE: Sayfa yenilenince localStorage temizle
         * Her yenilemede temiz başlangıç (no restore)
         */
        clearPlayerState() {
            try {
                // Clear all player-related localStorage keys
                safeStorage.removeItem('queue_state');
                safeStorage.removeItem('player_state');
                safeStorage.removeItem('last_played_song');
                safeStorage.removeItem('current_time');
                safeStorage.removeItem('queue_index');

                // Reset player state to default
                this.queue = [];
                this.queueIndex = 0;
                this.currentSong = null;
                this.currentTime = 0;
                this.duration = 0;
                this.isPlaying = false;
                this.progressPercent = 0;

            } catch (error) {
                console.warn('⚠️ Failed to clear player state:', error);
            }
        },

        /**
         * 🔐 SESSION POLLING: Start polling for session validity (device limit check)
         * Polls /api/auth/check-session every 30 seconds
         *
         * 🔴 GEÇİCİ DEVRE DIŞI - DeviceService kapalı (2025-12-26)
         */
        startSessionPolling() {
            // 🔴 GEÇİCİ: Polling tamamen devre dışı
            return;

            // Clear any existing interval
            if (this.sessionPollInterval) {
                clearInterval(this.sessionPollInterval);
            }

            // 🔧 LOGIN SONRASI: Session DB'ye kaydedilmesi için 2 saniye bekle
            // Race condition önleme: Backend registerSession() işlemi tamamlansın
            setTimeout(() => {
                this.checkSessionValidity();
            }, 2000);

            // 🔧 PERFORMANS AYARI:
            // TEST: 5 saniye (5000ms) - hızlı geri bildirim
            // CANLI: 5 dakika (300000ms) - 10.000 kullanıcıda 33 req/s
            // @see https://ixtif.com/readme/2025/12/10/muzibu-session-auth-system/
            const SESSION_POLL_INTERVAL = 5000; // 🧪 TEST: 5 saniye

            this.sessionPollInterval = setInterval(() => {
                this.checkSessionValidity();
            }, SESSION_POLL_INTERVAL);
        },

        /**
         * 🔐 STOP SESSION POLLING: Clear the polling interval
         */
        stopSessionPolling() {
            if (this.sessionPollInterval) {
                clearInterval(this.sessionPollInterval);
                this.sessionPollInterval = null;
            }
        },

        /**
         * 🔐 CHECK SESSION: Verify session is still valid
         * Backend checks if session exists in DB (device limit enforcement)
         */
        async checkSessionValidity() {
            try {
                // 🔥 FIX: Sanctum stateful authentication için Referer header ZORUNLU!
                // EnsureFrontendRequestsAreStateful middleware Referer/Origin header'a bakıyor
                const response = await fetch('/api/auth/check-session', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Referer': window.location.origin + '/'  // Sanctum stateful için ZORUNLU
                    },
                    credentials: 'same-origin',
                    referrerPolicy: 'strict-origin-when-cross-origin'  // Browser'ın Referer göndermesini sağla
                });

                // 🔥 FIX: 429 Too Many Requests durumunda logout YAPMA!
                // Rate limit hatası session invalid demek DEĞİL
                if (response.status === 429) {
                    console.warn('⚠️ Rate limit hit on session check, will retry later');
                    return; // Hiçbir şey yapma, polling devam edecek
                }

                // 🔥 FIX: Network hatası veya server error durumunda logout YAPMA
                if (!response.ok) {
                    // 401/419 = oturum yok → zorunlu logout
                    if (response.status === 401 || response.status === 419) {
                        this.handleSessionTerminated(this.frontLang?.messages?.session_terminated || 'Oturumunuz sonlandırıldı.');
                        return;
                    }

                    console.warn('⚠️ Session check HTTP error:', response.status);
                    return; // Hiçbir şey yapma
                }

                const data = await response.json();

                // Session invalid - user was logged out
                if (!data.valid) {
                    console.warn('⚠️ Session invalid:', data.reason);

                    // Stop polling
                    if (this.sessionPollInterval) {
                        clearInterval(this.sessionPollInterval);
                        this.sessionPollInterval = null;
                    }

                    // 🔥 Kritik: Oturum düştüğünde çalmayı ANINDA durdur
                    try {
                        if (this.hls) {
                            const audio = this.getActiveHlsAudio();
                            if (audio) {
                                audio.pause();
                                audio.src = '';
                            }
                            this.hls.stopLoad?.();
                            this.hls.destroy?.();
                            this.hls = null;
                        }
                        if (this.howl) {
                            this.howl.stop();
                            this.howl.unload();
                            this.howl = null;
                        }
                        this.isPlaying = false;
                        window.dispatchEvent(new CustomEvent('player:pause'));
                    } catch (stopErr) {
                        console.warn('⚠️ Failed to stop playback on invalid session:', stopErr);
                    }

                    // Handle based on reason
                    if (data.reason === 'device_limit_exceeded') {
                        // 🚨 DEVICE LIMIT EXCEEDED: Limit aşık - modal göster
                        this.handleDeviceLimitExceeded();
                    } else if (data.reason === 'session_terminated') {
                        // 🔐 SESSION TERMINATED: Başka cihazdan giriş yapıldı (LIFO)
                        // 🔥 FIX: Sonsuz döngü önleme
                        if (!this._sessionTerminatedHandling) {
                            this.handleSessionTerminated(data.message);
                        }
                    } else if (data.reason === 'not_authenticated') {
                        // 🔥 Sayfa renderda auth vardı ama API'de yok
                        // Bu NORMAL durum olabilir: İlk sayfa yüklemesi sırasında session henüz sync olmamış

                        // Sadece flag güncelle, agresif logout YAPMA
                        // Session sync sorunu genelde kendiliğinden düzelir
                        this.isLoggedIn = false;

                        // Polling'i durdur (gereksiz istek atmaya gerek yok)
                        this.stopSessionPolling();
                    } else {
                        // Silent logout (session expired veya diğer nedenler)
                        this.handleSilentLogout();
                    }
                } else {
                    // ✅ Session valid - reset fail counter
                    this.sessionCheckFailCount = 0;
                }
            } catch (error) {
                console.error('Session check failed:', error);
                // Don't logout on network error - keep trying
            }
        },

        /**
         * 🔐 DEVICE LIMIT EXCEEDED: Show modal to select which device to terminate
         * Limit aşıldı - kullanıcı hangi cihazı çıkaracağını seçsin
         */
        handleDeviceLimitExceeded() {

            // 🛑 Set device limit flag to prevent further playback attempts
            this.deviceLimitExceeded = true;

            // Stop playback immediately (use stopCurrentPlayback instead of pause)
            this.stopCurrentPlayback();
            this.isPlaying = false;

            // 🔥 FIX: Önce cihaz listesini çek, sonra başka cihaz varsa modal göster
            this.fetchActiveDevices().then(() => {
                const terminableDevices = this.activeDevices.filter(d => !d.is_current);

                if (terminableDevices.length > 0) {
                    this.showDeviceSelectionModal = true;
                } else {
                    // Sadece mevcut cihaz var, modal yerine logout seçeneği sun
                    this.showToast(this.frontLang?.messages?.device_limit_reached || 'Device limit reached. You can log out and log back in from this device to listen to music.', 'warning', 8000);
                    this.deviceLimitExceeded = false; // Playback'i durdurmaya devam et ama modal gösterme
                }
            });
        },

        /**
         * 🔐 SILENT LOGOUT: Logout without modal (session expired)
         */
        handleSilentLogout() {
            this.forceLogout();
        },

        /**
         * 🔐 SESSION TERMINATED: Başka cihazdan giriş yapıldı
         * HEMEN logout yap ve login'e yönlendir - modal yok, bekleme yok!
         */
        handleSessionTerminated(messageOrObj) {
            // 🔥 Sonsuz döngü önleme
            if (this._sessionTerminatedHandling) {
                return;
            }
            this._sessionTerminatedHandling = true;


            // 🛑 HER ŞEYİ DURDUR
            try {
                this.stopCurrentPlayback();
                this.isPlaying = false;
                this.isLoggedIn = false;
                this.stopSessionPolling();
                this.clearAllBrowserStorage();
                this.streamUrlCache = new Map();
                this.preloadedSongs = new Set();
            } catch(e) {}

            let reason = null;
            let displayMessage = null;
            if (typeof messageOrObj === 'object' && messageOrObj !== null) {
                reason = messageOrObj.reason || null;
                displayMessage = messageOrObj.message || null;
            } else {
                displayMessage = messageOrObj;
            }

            const reasonMessages = {
                device_limit: 'Başka bir cihazdan giriş yapıldı. Bu oturum kapatıldı.',
                lifo: 'Başka bir cihazdan giriş yapıldı. Bu oturum kapatıldı.',
                lifo_new_device: 'Başka bir cihazdan giriş yapıldı. Bu oturum kapatıldı.',
                expired_signature: 'Oturum süresi doldu. Lütfen tekrar giriş yapın.',
                session_missing: 'Oturum bulunamadı. Lütfen tekrar giriş yapın.',
                csrf: 'Güvenlik doğrulaması yenilendi. Lütfen tekrar giriş yapın.'
            };

            if (!displayMessage && reason && reasonMessages[reason]) {
                displayMessage = reasonMessages[reason];
            }

            const fallbackMessage = this.frontLang?.messages?.session_terminated || 'Oturumunuz sonlandırıldı. Lütfen tekrar giriş yapın.';
            const finalMessage = displayMessage || fallbackMessage;
            this.showSessionTerminatedModal(finalMessage);

            // 🔥 HARD REDIRECT (logout fetch yok, 419 döngüsü engelle)
            setTimeout(() => {
                const query = new URLSearchParams({
                    session_terminated: 1,
                    reason: reason || '',
                    msg: finalMessage
                });
                window.location.href = '/login?' + query.toString();
            }, 300);
        },

        /**
         * 🔥 SESSION TERMINATED MODAL
         * Kullanıcıya bilgi veren modal - Butona basınca TAM ÇIKIŞ yapar
         */
        showSessionTerminatedModal(message) {
            const defaultMessage = 'Başka bir cihazdan giriş yapıldı. Bu oturum sonlandırıldı.';
            const displayMessage = message || defaultMessage;

            // Mevcut modal varsa kaldır
            const existingModal = document.getElementById('session-terminated-modal');
            if (existingModal) {
                existingModal.remove();
            }

            // Modal HTML
            const modalHtml = `
                <div id="session-terminated-modal" class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/80 backdrop-blur-sm">
                    <div class="bg-slate-900 border border-slate-700 rounded-2xl p-8 max-w-md mx-4 shadow-2xl">
                        <div class="text-center">
                            <!-- Icon -->
                            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-orange-500/20 flex items-center justify-center">
                                <svg class="w-8 h-8 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>

                            <!-- Title -->
                            <h3 class="text-xl font-bold text-white mb-2">Oturum Sonlandırıldı</h3>

                            <!-- Message -->
                            <p class="text-slate-300 mb-6">${displayMessage}</p>

                            <!-- Button -->
                            <button
                                id="session-terminated-btn"
                                class="w-full px-6 py-3 bg-gradient-to-r from-orange-500 to-red-500 text-white font-semibold rounded-xl hover:from-orange-600 hover:to-red-600 transition-all duration-200"
                            >
                                Tamam
                            </button>
                        </div>
                    </div>
                </div>
            `;

            // Modal'ı body'ye ekle
            document.body.insertAdjacentHTML('beforeend', modalHtml);

            // 🔥 Butona tıklanınca TAM ÇIKIŞ yap
            document.getElementById('session-terminated-btn').addEventListener('click', () => {
                this.performFullLogout();
            });
        },

        /**
         * 🔥 TAM ÇIKIŞ - Form POST ile logout yap (en güvenilir yöntem)
         */
        async performFullLogout() {
            const btn = document.getElementById('session-terminated-btn');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="animate-pulse">Çıkış yapılıyor...</span>';
            }


            // 1. Browser storage temizle
            this.clearAllBrowserStorage();

            // 2. Cache API temizle
            this.clearCacheAPI();

            // 3. Form POST ile logout - Bu en güvenilir yöntem
            // Laravel'in standart logout route'u cookie'leri otomatik siler
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/logout';
            form.style.display = 'none';

            // CSRF token ekle
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ||
                              document.querySelector('input[name="_token"]')?.value || '';

            const tokenInput = document.createElement('input');
            tokenInput.type = 'hidden';
            tokenInput.name = '_token';
            tokenInput.value = csrfToken;
            form.appendChild(tokenInput);

            // Redirect URL ekle (logout sonrası nereye gidecek)
            const redirectInput = document.createElement('input');
            redirectInput.type = 'hidden';
            redirectInput.name = 'redirect';
            redirectInput.value = '/login?session_terminated=1';
            form.appendChild(redirectInput);

            document.body.appendChild(form);
            form.submit();
        },

        /**
         * 🔥 TÜM COOKIE'LERİ TEMİZLE
         */
        clearAllCookies() {
            const cookies = document.cookie.split(';');

            for (let cookie of cookies) {
                const eqPos = cookie.indexOf('=');
                const name = eqPos > -1 ? cookie.substr(0, eqPos).trim() : cookie.trim();

                // Cookie'yi sil (tüm path'ler için)
                document.cookie = name + '=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/';
                document.cookie = name + '=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/;domain=' + window.location.hostname;
                document.cookie = name + '=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/;domain=.' + window.location.hostname;
            }

        },

        /**
         * 🔥 CACHE API TEMİZLE (Service Worker)
         */
        async clearCacheAPI() {
            if ('caches' in window) {
                try {
                    const cacheNames = await caches.keys();
                    await Promise.all(cacheNames.map(name => caches.delete(name)));
                } catch (e) {
                }
            }
        },

        /**
         * 🔥 BROWSER STORAGE TEMİZLE
         * LocalStorage, SessionStorage ve player state'i temizle
         */
        clearAllBrowserStorage() {

            // Player state temizle
            try {
                localStorage.removeItem('muzibu_player_state');
                localStorage.removeItem('muzibu_queue');
                localStorage.removeItem('muzibu_favorites');
                localStorage.removeItem('muzibu_play_context');
                localStorage.removeItem('muzibu_volume');
            } catch (e) {
            }

            // Session storage temizle
            try {
                sessionStorage.clear();
            } catch (e) {
            }

        },

        /**
         * 🔐 FORCE LOGOUT: Clear state and reload page
         */
        forceLogout() {
            // Clear session data
            this.isLoggedIn = false;
            this.currentUser = null;

            // Clear favorites
            this.favorites = [];

            // Reload page to clear session (player will stop automatically)
            window.location.reload();
        },

        /**
         * 🔐 FETCH DEVICE LIMIT INFO: Get device limit from backend
         */
        async fetchDeviceLimitInfo() {
            return this.fetchActiveDevices();
        },

        /**
         * 🔐 FETCH ACTIVE DEVICES: Get list of active devices from backend
         *
         * 🔴 GEÇİCİ DEVRE DIŞI - DeviceService kapalı (2025-12-26)
         */
        async fetchActiveDevices() {
            // 🔴 GEÇİCİ: Devre dışı
            return;

            try {
                // 🔧 FIX: Doğru endpoint'i kullan - /api/auth/active-devices
                const response = await fetch('/api/auth/active-devices', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });

                if (!response.ok) {
                    console.warn('🔐 Active devices fetch failed:', response.status);
                    // Fallback: /api/auth/me ile device limit al
                    await this.fetchDeviceLimitFromMe();
                    return;
                }

                const data = await response.json();

                if (data.success) {
                    this.activeDevices = data.devices || [];
                    // Device limit'i de API'den al
                    if (data.device_limit) {
                        this.deviceLimit = data.device_limit;
                    }
                } else {
                    this.activeDevices = [];
                }
            } catch (error) {
                console.error('Failed to fetch active devices:', error);
                this.activeDevices = [];
            }
        },

        /**
         * 🔐 FETCH DEVICE LIMIT FROM ME: Fallback method
         */
        async fetchDeviceLimitFromMe() {
            try {
                const response = await fetch('/api/auth/me', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.authenticated && data.user) {
                        this.deviceLimit = data.user.device_limit || 1;
                    }
                }
            } catch (error) {
                console.warn('Failed to fetch device limit:', error);
            }
        },

        /**
         * 🔐 CHECK DEVICE LIMIT ON PAGE LOAD: Her sayfa yüklemesinde limit kontrolü
         * API'den cihaz sayısı ve limiti al, limit aşılmışsa selection modal göster
         *
         * 🔴 GEÇİCİ DEVRE DIŞI - DeviceService kapalı (2025-12-26)
         */
        async checkDeviceLimitOnPageLoad() {
            // 🔴 GEÇİCİ: Devre dışı
            return;

            try {
                const response = await fetch('/api/auth/active-devices', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });

                if (!response.ok) {
                    console.warn('🔐 Device limit check failed:', response.status);
                    return;
                }

                const data = await response.json();

                if (data.success) {
                    this.activeDevices = data.devices || [];
                    this.deviceLimit = data.device_limit || 1;

                    const deviceCount = this.activeDevices.length;
                    const terminableDevices = this.activeDevices.filter(d => !d.is_current);

                    // 🔥 FIX: Limit aşıldıysa VE çıkış yapılabilecek başka cihaz varsa modal göster
                    if (deviceCount > this.deviceLimit && terminableDevices.length > 0) {
                        this.showDeviceSelectionModal = true;
                    } else if (deviceCount > this.deviceLimit) {
                        // Limit aşıldı ama sadece mevcut cihaz var - bu olmamalı, LIFO bozuk demek
                        console.warn('🔐 Device limit exceeded but no terminable devices - LIFO issue?');
                    }
                }
            } catch (error) {
                console.error('🔐 Device limit check error:', error);
            }
        },

        /**
         * 🔐 TERMINATE SELECTED DEVICES: Terminate multiple device sessions (checkbox seçimleri)
         */
        async terminateSelectedDevices() {
            if (this.selectedDeviceIds.length === 0) {
                alert(this.frontLang?.messages?.generic_error || 'Please select at least one device');
                return;
            }

            this.deviceTerminateLoading = true;

            try {
                // 🔥 FIX: Tek API call ile tüm seçili cihazları terminate et (batch)
                const response = await fetch('/api/auth/terminate-devices', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ session_ids: this.selectedDeviceIds })
                });

                const data = await response.json();

                if (data.success && data.deleted_count > 0) {
                    const loggedOutMsg = (this.frontLang?.messages?.devices_logged_out || ':count device(s) logged out').replace(':count', data.deleted_count);
                    this.showToast(loggedOutMsg, 'success');

                    // Close modals and refresh
                    this.showDeviceSelectionModal = false;
                    this.showDeviceLimitWarning = false;
                    this.selectedDeviceIds = [];

                    // 🔓 Reset device limit flag - user can play again
                    this.deviceLimitExceeded = false;

                    // 🔥 FIX: Session save için 500ms bekle, sonra reload
                    // Session cookie browser'a yazılmadan reload yapılıyordu
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                } else {
                    alert(data.message || this.frontLang?.messages?.generic_error || 'An error occurred, please try again');
                }
            } catch (error) {
                console.error('Device termination failed:', error);
                alert(this.frontLang?.messages?.generic_error || 'An error occurred, please try again');
            } finally {
                this.deviceTerminateLoading = false;
            }
        },

        /**
         * 🔐 TERMINATE ALL DEVICES: Terminate all devices except current (Tümünü Çıkar)
         */
        async terminateAllDevices() {
            const otherDevices = this.activeDevices.filter(d => !d.is_current);

            if (otherDevices.length === 0) {
                alert(this.frontLang?.messages?.generic_error || 'No other devices to log out');
                return;
            }

            this.deviceTerminateLoading = true;

            try {
                // 🔥 FIX: Tek API call ile tüm diğer cihazları terminate et (batch)
                const sessionIds = otherDevices.map(d => d.session_id);

                const response = await fetch('/api/auth/terminate-devices', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ session_ids: sessionIds })
                });

                const data = await response.json();

                if (data.success && data.deleted_count > 0) {
                    const loggedOutMsg = (this.frontLang?.messages?.devices_logged_out || ':count device(s) logged out').replace(':count', data.deleted_count);
                    this.showToast(loggedOutMsg, 'success');

                    // Close modals and refresh
                    this.showDeviceSelectionModal = false;
                    this.showDeviceLimitWarning = false;
                    this.selectedDeviceIds = [];

                    // 🔓 Reset device limit flag - user can play again
                    this.deviceLimitExceeded = false;

                    // 🔥 FIX: Session save için 500ms bekle, sonra reload
                    // Session cookie browser'a yazılmadan reload yapılıyordu
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                } else {
                    alert(data.message || this.frontLang?.messages?.generic_error || 'An error occurred, please try again');
                }
            } catch (error) {
                console.error('Device termination failed:', error);
                alert(this.frontLang?.messages?.generic_error || 'An error occurred, please try again');
            } finally {
                this.deviceTerminateLoading = false;
            }
        },

        /**
         * 🔐 LOGOUT FROM THIS DEVICE: User chooses to logout from current device
         */
        logoutFromThisDevice() {
            this.showDeviceLimitWarning = false;
            this.forceLogout();
        },

        /**
         * 🔐 SHOW DEVICE SELECTION: User chooses to terminate another device
         */
        showDeviceSelection() {
            this.showDeviceLimitWarning = false;
            this.showDeviceSelectionModal = true;
        }
    }
}

// ✅ Make muzibuApp globally accessible for Alpine.js
window.muzibuApp = muzibuApp;

// Play Limits Component (Guest & Member daily limits)
// Cache bust: 1765140096
// Cache bust: 1765142226
