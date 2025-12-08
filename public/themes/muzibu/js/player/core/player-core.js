/**
 * Muzibu Player - Core Module
 * Main Alpine.js component for music player
 *
 * Dependencies:
 * - safeStorage (from core/safe-storage.js)
 * - muzibuFavorites (from features/favorites.js)
 * - muzibuAuth (from features/auth.js)
 * - muzibuKeyboard (from features/keyboard.js)
 */

function muzibuApp() {
    // Get config from window object (set in blade template)
    const config = window.muzibuPlayerConfig || {};

    return {
        // 🎯 Modular features (spread from separate files)
        ...muzibuFavorites(),
        ...muzibuAuth(),
        ...muzibuKeyboard(),

        // Tenant-specific translations
        lang: config.lang || {},
        frontLang: config.frontLang || {},

        isLoggedIn: config.isLoggedIn || false,
        currentUser: config.currentUser || null,
        todayPlayedCount: config.todayPlayedCount || 0,
        showAuthModal: null,
        showQueue: false,
        showLyrics: false,
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

        // Loading & UI states (KRITIK - bunlar eksikti!)
        isLoading: true,
        contentLoaded: false,
        searchQuery: '',
        searchResults: [],
        searchOpen: false,
        mobileMenuOpen: false,

        // Player states
        isPlaying: false,
        shuffle: false,
        repeatMode: 'off',
        currentTime: 0,
        duration: 240,
        volume: parseInt(safeStorage.getItem('volume')) || 100, // Load from localStorage, default 100
        isMuted: false,
        currentSong: null,
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
        previewTimer: null, // 🎵 Guest preview timer
        fadeOutTimer: null, // 🎵 Guest preview fade-out timer
        isPreviewBlocked: false, // 🎵 Preview ended - next/previous disabled
        playTracked: false, // 🎵 Track if current song play has been recorded
        playTrackedAt: 60, // 🎵 Track play after 60 seconds
        sessionPollInterval: null, // 🔐 Device limit polling interval
        showDeviceLimitModal: false, // 🔐 Show device limit exceeded modal

        // Crossfade settings (using Howler.js + HLS.js)
        crossfadeEnabled: true,
        crossfadeDuration: 1500, // 1.5 seconds for automatic song transitions (was 5s - too slow!)
        fadeOutDuration: 800, // 0.8 seconds for pause/play/manual change fade (was 5s - too slow!)
        isCrossfading: false,
        howl: null, // Current Howler instance (for MP3)
        howlNext: null, // Next song Howler instance for crossfade
        hls: null, // Current HLS.js instance
        hlsNext: null, // Next HLS.js instance for crossfade
        isHlsStream: false, // Whether current stream is HLS
        activeHlsAudioId: 'hlsAudio', // Which HLS audio element is active ('hlsAudio' or 'hlsAudioNext')
        progressInterval: null, // Interval for updating progress
        _fadeAnimation: null, // For requestAnimationFrame fade

        // Computed: Current stream type
        get currentStreamType() {
            return this.isHlsStream ? 'hls' : 'mp3';
        },

        /**
         * 🎨 GET COVER URL: Smart cover URL resolver
         * Handles both media_id (number) and full URL formats
         * @param {string|number} cover - media_id or full URL
         * @param {number} width - thumbnail width
         * @param {number} height - thumbnail height
         */
        getCoverUrl(cover, width = 56, height = 56) {
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

        // Get the currently active HLS audio element
        getActiveHlsAudio() {
            if (this.activeHlsAudioId === 'hlsAudioNext') {
                return document.getElementById('hlsAudioNext');
            }
            return this.$refs.hlsAudio;
        },

        init() {
            // ✅ Prevent double initialization (component-level, not window-level)
            if (this._initialized) {
                console.log('⚠️ Already initialized, skipping...');
                return;
            }
            this._initialized = true;

            console.log('Muzibu initialized with Howler.js');

            // User already loaded from Laravel backend (no need for API check)
            console.log('Muzibu initialized', { isLoggedIn: this.isLoggedIn, user: this.currentUser });

            // Load featured playlists on init
            this.loadFeaturedPlaylists();

            // 🎯 PRELOAD: Load last played song in PAUSE mode (instant playback)
            this.preloadLastPlayedSong();

            // Initialize keyboard shortcuts
            this.initKeyboard();

            // Show content after loading (KRITIK - Alpine.js x-show için)
            setTimeout(() => {
                this.isLoading = false;
                this.contentLoaded = true;
            }, 500);

            // 🎯 QUEUE CHECKER: Monitor queue and auto-refill (PHASE 4)
            this.startQueueMonitor();

            // 💾 FULL STATE RESTORATION: Tarayıcı kapansa bile kaldığı yerden devam et
            this.loadQueueState();

            // 🎵 BACKGROUND PLAYBACK: Tarayıcı minimize olsa bile çalsın
            this.enableBackgroundPlayback();

            // 💾 AUTO-SAVE: State değişikliklerini otomatik kaydet
            this.setupAutoSave();

            // 🔐 SESSION POLLING: Device limit kontrolü (sadece login olunca başlar)
            if (this.isLoggedIn) {
                this.startSessionPolling();
            }

            // 🔐 DEVICE LIMIT WARNING: Check localStorage flag after logout
            try {
                const deviceLimitWarning = localStorage.getItem('device_limit_warning');
                if (deviceLimitWarning === 'true') {
                    console.log('🔐 Showing device limit warning modal after logout');
                    this.showDeviceLimitWarning = true;
                    // Fetch device limit from API (async, modal will show with default until loaded)
                    this.fetchDeviceLimitInfo();
                    localStorage.removeItem('device_limit_warning');
                }
            } catch (e) {
                console.warn('localStorage not available:', e.message);
            }

            // 🔐 DEVICE LIMIT: Check meta tag for session flash (2. cihaz login sonrası)
            const deviceLimitMeta = document.querySelector('meta[name="device-limit-exceeded"]');
            if (deviceLimitMeta && deviceLimitMeta.content === 'true') {
                console.log('🔐 Device limit exceeded on login - showing warning modal');
                this.showDeviceLimitWarning = true;
                this.deviceLimit = parseInt(document.querySelector('meta[name="device-limit"]')?.content || '1');
                this.activeDeviceCount = parseInt(document.querySelector('meta[name="active-device-count"]')?.content || '2');
            }

            // SPA Navigation: Handle browser back/forward
            window.addEventListener('popstate', (e) => {
                if (e.state && e.state.url) {
                    this.loadPage(e.state.url, false);
                }
            });

            // SPA Navigation: Intercept all internal links
            document.addEventListener('click', (e) => {
                const link = e.target.closest('a');
                if (!link) return;

                const href = link.getAttribute('href');

                // Skip if no href, hash link, or has download/target attribute
                if (!href ||
                    href.startsWith('#') ||
                    link.hasAttribute('download') ||
                    link.hasAttribute('target')) {
                    return;
                }

                // Check if external link (different domain)
                if (href.startsWith('http') || href.startsWith('//')) {
                    try {
                        const linkUrl = new URL(href, window.location.origin);
                        // If same domain, use SPA navigation
                        if (linkUrl.origin !== window.location.origin) {
                            return; // External link, let it navigate normally
                        }
                    } catch (e) {
                        return; // Invalid URL, let it navigate normally
                    }
                }

                // Internal link - use SPA navigation
                console.log('🚀 SPA Navigation:', href);
                e.preventDefault();
                this.navigateTo(href);
            });
        },

        async loadFeaturedPlaylists() {
            try {
                const response = await fetch('/api/muzibu/playlists/featured');
                const playlists = await response.json();
                console.log('Featured playlists loaded:', playlists.length);
            } catch (error) {
                console.error('Failed to load playlists:', error);
            }
        },

        // 🎯 PRELOAD: Load last played song in PAUSE mode for instant playback
        async preloadLastPlayedSong() {
            try {
                const response = await fetch('/api/muzibu/songs/last-played');

                // Silently skip if endpoint not found
                if (!response.ok) {
                    return;
                }

                const data = await response.json();

                if (!data.last_played) {
                    return;
                }

                const song = data.last_played;
                console.log('🎵 Preloading last played song:', song.song_title);

                // Add to queue (single song)
                this.queue = [song];
                this.queueIndex = 0;
                this.currentSong = song;

                // Load song stream URL
                const streamResponse = await fetch(`/api/muzibu/songs/${song.song_id}/stream`);
                const streamData = await streamResponse.json();

                // Load audio in PAUSE mode
                if (streamData.stream_url) {
                    const useHls = streamData.stream_type === 'hls';

                    // Load but DON'T play
                    if (useHls) {
                        this.isHlsStream = true;
                        await this.playHlsStream(streamData.stream_url, 0, true); // autoplay: false
                    } else {
                        this.isHlsStream = false;
                        await this.playWithHowler(streamData.stream_url, 0, true); // autoplay: false
                    }

                    this.isPlaying = false; // Ensure paused
                    console.log('✅ Last played song preloaded (PAUSED, ready to play)');
                }

            } catch (error) {
                // Silently ignore errors (endpoint may not exist yet)
            }
        },

        // 🎯 Favorites functions (toggleFavorite, isFavorite, isLiked) moved to features/favorites.js

        async togglePlayPause() {
            console.log('🎵 togglePlayPause called', { queue: this.queue.length, currentSong: this.currentSong, isPlaying: this.isPlaying, howl: !!this.howl, hls: !!this.hls });

            // 🔒 PREVIEW BLOCKED: Play button disabled after preview ends
            if (this.isPreviewBlocked) {
                console.log('🔒 Play blocked: Preview ended, upgrading required');
                this.showToast('Premium\'a geçin, sınırsız dinleyin!', 'warning');

                // Show modal
                const playLimitsElement = document.querySelector('[x-data*="playLimits"]');
                if (playLimitsElement) {
                    const playLimitsComponent = Alpine.$data(playLimitsElement);
                    if (playLimitsComponent) {
                        playLimitsComponent.showGuestModal = true;
                    }
                }
                return;
            }

            // Eğer queue boşsa, rastgele şarkılar yükle
            if (this.queue.length === 0 || !this.currentSong) {
                await this.playRandomSongs();
                return;
            }

            const targetVolume = this.isMuted ? 0 : this.volume / 100;

            if (this.isPlaying) {
                // Fade out then pause
                if (this.howl) {
                    const currentVolume = this.howl.volume();
                    this.howl.fade(currentVolume, 0, this.fadeOutDuration);
                    this.howl.once('fade', () => {
                        this.howl.pause();
                        this.isPlaying = false;
                        window.dispatchEvent(new CustomEvent('player:pause'));
                    });
                } else if (this.hls) {
                    const audio = this.getActiveHlsAudio();
                    if (audio) {
                        await this.fadeAudioElement(audio, audio.volume, 0, this.fadeOutDuration);
                        audio.pause();
                        this.isPlaying = false;
                        window.dispatchEvent(new CustomEvent('player:pause'));
                    }
                }
            } else {
                // Fade in then play
                if (this.howl) {
                    this.howl.volume(0);
                    this.howl.play();
                    this.howl.fade(0, targetVolume, this.fadeOutDuration);
                    this.isPlaying = true;
                } else if (this.hls) {
                    const audio = this.getActiveHlsAudio();
                    if (audio) {
                        audio.volume = 0;
                        await audio.play();
                        this.fadeAudioElement(audio, 0, targetVolume, this.fadeOutDuration);
                        this.isPlaying = true;
                    }
                } else if (this.currentSong) {
                    // 🎵 No audio source loaded yet - load and play current song
                    console.log('🎵 No audio source, loading current song:', this.currentSong.song_id);
                    await this.playSongFromQueue(this.queueIndex);
                }
            }
        },

        async playRandomSongs() {
            try {
                this.isLoading = true;

                // 🎵 AUTO-START: Queue boşsa Genre'den başla (infinite loop garantisi)
                console.log('🎵 Auto-starting music from Genre (infinite loop)...');

                // ✅ Alpine store check (Livewire navigate sonrası store undefined olabilir)
                const muzibuStore = Alpine.store('muzibu');
                if (!muzibuStore) {
                    console.error('❌ Alpine.store("muzibu") not available yet - Using fallback');
                    await this.fallbackToPopularSongs();
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
                        await this.playSongFromQueue(0);
                        this.showToast(`🎵 ${firstGenre.title?.tr || firstGenre.title} çalıyor`, 'success');
                    } else {
                        // Fallback: Popular songs
                        await this.fallbackToPopularSongs();
                    }
                } else {
                    // Fallback: Popular songs
                    await this.fallbackToPopularSongs();
                }

                this.isLoading = false;
            } catch (error) {
                console.error('Failed to start auto-play:', error);
                // Fallback: Popular songs
                await this.fallbackToPopularSongs();
                this.isLoading = false;
            }
        },

        /**
         * 🔄 Fallback: Genre bulunamazsa popular songs
         */
        async fallbackToPopularSongs() {
            try {
                const response = await fetch('/api/muzibu/songs/popular?limit=50');
                const songs = await response.json();

                if (songs.length > 0) {
                    // Shuffle songs
                    const shuffled = songs.sort(() => Math.random() - 0.5);

                    this.queue = shuffled;
                    this.queueIndex = 0;
                    await this.playSongFromQueue(0);
                    this.showToast('Popüler şarkılar çalıyor!', 'success');
                } else {
                    this.showToast('Şarkı bulunamadı', 'error');
                }
            } catch (error) {
                console.error('Failed to play fallback songs:', error);
                this.showToast('Şarkılar yüklenemedi', 'error');
            } finally {
                this.isLoading = false;
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

                const state = {
                    queue: this.queue,
                    queueIndex: this.queueIndex,
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
                    console.log('💾 Full state saved:', {
                        queue: state.queue.length,
                        index: state.queueIndex,
                        song: state.currentSong?.song_title?.tr || state.currentSong?.song_title,
                        time: Math.floor(state.currentTime),
                        volume: state.volume,
                        playing: state.isPlaying
                    });
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
                    console.log('💾 No saved state found - Fresh start');
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

                console.log('💾 Full state restored:', {
                    queue: this.queue.length,
                    index: this.queueIndex,
                    song: this.currentSong?.song_title?.tr || this.currentSong?.song_title,
                    volume: this.volume,
                    wasPlaying: state.isPlaying
                });

                // 🎵 AUTO-RESUME: Tarayıcı kapansa bile kaldığı yerden devam et
                // ⚠️ Autoplay Policy: Kullanıcı etkileşimi olmadan play() yapılamaz
                // Çözüm: Şarkıyı yükle, PAUSE modunda beklet, kullanıcı play'e basınca devam
                if (this.currentSong && this.queue.length > 0) {
                    const wasPlaying = state.isPlaying;
                    const savedTime = state.currentTime || 0;

                    console.log('🎵 State restored, waiting for user interaction...', { wasPlaying, savedTime });

                    // Şarkıyı yükle ama autoplay=false ile
                    setTimeout(async () => {
                        try {
                            // Queue'dan şarkıyı yükle (autoplay=false)
                            await this.playSongFromQueue(this.queueIndex, false);

                            // Kaldığı pozisyona git
                            if (savedTime > 0) {
                                setTimeout(() => {
                                    try {
                                        this.seekTo(savedTime);
                                        console.log(`⏩ Positioned at ${Math.floor(savedTime)}s (paused)`);
                                    } catch (e) {
                                        console.warn('⚠️ seekTo failed during restore:', e);
                                    }
                                }, 1000);
                            }

                            // Eğer önceden çalıyorduysa, kullanıcıya bilgi ver
                            if (wasPlaying) {
                                console.log('ℹ️ Song loaded in pause mode. Click play to resume.');
                            }

                            // 🛡️ Re-enable auto-save after song is loaded
                            setTimeout(() => {
                                this._isRestoringState = false;
                                console.log('✅ State restoration complete, auto-save re-enabled');
                            }, 500);
                        } catch (e) {
                            console.warn('⚠️ Auto-load failed:', e);
                            this._isRestoringState = false;
                        }
                    }, 1500);
                } else {
                    // No song to load, just re-enable auto-save
                    this._isRestoringState = false;
                }

            } catch (error) {
                console.error('❌ Failed to load state:', error);
                this._isRestoringState = false;
            }
        },

        async previousTrack() {
            // 🔒 Preview blocked - next/previous disabled
            if (this.isPreviewBlocked) {
                this.showToast('Premium\'a geçin, sınırsız dinleyin!', 'warning');
                return;
            }

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
            // 🔒 Preview blocked - next/previous disabled
            if (this.isPreviewBlocked) {
                this.showToast('Premium\'a geçin, sınırsız dinleyin!', 'warning');
                return;
            }

            // 🚨 INSTANT PLAY: Cancel crossfade (manual track change)
            this.isCrossfading = false;

            // ⚡ INSTANT STOP: Stop current track immediately before loading next
            await this.stopCurrentPlayback();

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
                    console.log('💼 B2B mode: Queue restarted');
                }
            } else {
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

                    this.showToast('Karışık çalma aktif', 'success');
                }
            } else {
                this.showToast('Karışık çalma kapalı', 'info');
                // Note: We don't restore original order since we don't track it
                // Shuffle off just means next songs will play in current order
            }
        },

        cycleRepeat() {
            const modes = ['off', 'all', 'one'];
            const idx = modes.indexOf(this.repeatMode);
            this.repeatMode = modes[(idx + 1) % modes.length];
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
            if (this.isCrossfading) return;

            // Check if any player is active (Howler OR HLS)
            const hasActiveHowler = this.howl && this.howl.playing();
            const audio = this.$refs.hlsAudio;
            const hasActiveHls = this.hls && audio && !audio.paused;

            if (!hasActiveHowler && !hasActiveHls) return;

            const nextIndex = this.getNextSongIndex();
            if (nextIndex === -1) return;

            const nextSong = this.queue[nextIndex];
            if (!nextSong) return;

            this.isCrossfading = true;
            console.log('Starting crossfade...');

            const self = this;
            const targetVolume = this.isMuted ? 0 : this.volume / 100;

            // Get next song URL and type - USE CACHE FIRST!
            try {
                let data;

                // 🚀 CHECK CACHE FIRST - instant crossfade if cached!
                const cached = this.getCachedStream(nextSong.song_id);
                if (cached) {
                    console.log('⚡ Crossfade using cached stream:', nextSong.song_id);
                    data = cached;
                } else {
                    // Fetch from API if not cached
                    console.log('⏳ Crossfade fetching from API:', nextSong.song_id);
                    const response = await fetch(`/api/muzibu/songs/${nextSong.song_id}/stream`);
                    data = await response.json();
                }

                if (!data.stream_url) {
                    this.isCrossfading = false;
                    return;
                }

                const nextStreamType = data.stream_type || 'mp3';
                const nextIsHls = nextStreamType === 'hls';

                console.log('Next song type:', nextStreamType, 'URL:', data.stream_url);

                // Create next player based on stream type
                if (nextIsHls) {
                    // Create HLS player for next song
                    await this.createNextHlsPlayer(data.stream_url, targetVolume);
                } else {
                    // Create Howler for next song (MP3)
                    this.createNextHowlerPlayer(data.stream_url, targetVolume);
                }

                // Fade out current player (Howler or HLS)
                if (hasActiveHowler) {
                    this.howl.fade(targetVolume, 0, this.crossfadeDuration);
                } else if (hasActiveHls) {
                    this.fadeAudioElement(audio, audio.volume, 0, this.crossfadeDuration);
                }

                // After crossfade duration, complete the transition
                setTimeout(() => {
                    this.completeCrossfade(nextIndex, nextIsHls);
                }, this.crossfadeDuration);

            } catch (error) {
                console.error('Crossfade error:', error);
                this.isCrossfading = false;
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

            this.howlNext = new Howl({
                src: [url],
                format: format,
                html5: true,
                volume: 0,
                onplay: function() {
                    // Fade in next song
                    self.howlNext.fade(0, targetVolume, self.crossfadeDuration);
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

            // Create a second audio element for crossfade
            let nextAudio = document.getElementById('hlsAudioNext');
            if (!nextAudio) {
                nextAudio = document.createElement('audio');
                nextAudio.id = 'hlsAudioNext';
                nextAudio.style.display = 'none';
                document.body.appendChild(nextAudio);
            }

            return new Promise((resolve, reject) => {
                if (Hls.isSupported()) {
                    this.hlsNext = new Hls({
                        enableWorker: true,
                        lowLatencyMode: false
                    });

                    this.hlsNext.loadSource(url);
                    this.hlsNext.attachMedia(nextAudio);

                    this.hlsNext.on(Hls.Events.MANIFEST_PARSED, function() {
                        nextAudio.volume = 0;
                        nextAudio.play().then(() => {
                            // Fade in next HLS stream
                            self.fadeAudioElement(nextAudio, 0, targetVolume, self.crossfadeDuration);
                            resolve();
                        }).catch(e => {
                            console.error('HLS crossfade play error:', e);
                            reject(e);
                        });
                    });

                    this.hlsNext.on(Hls.Events.ERROR, function(event, data) {
                        if (data.fatal) {
                            console.error('HLS crossfade fatal error:', data);
                            reject(data);
                        }
                    });
                } else if (nextAudio.canPlayType('application/vnd.apple.mpegurl')) {
                    // Native HLS support (Safari)
                    nextAudio.src = url;
                    nextAudio.volume = 0;
                    nextAudio.play().then(() => {
                        self.fadeAudioElement(nextAudio, 0, targetVolume, self.crossfadeDuration);
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
                const oldAudio = this.$refs.hlsAudio;
                if (oldAudio) {
                    oldAudio.pause();
                    oldAudio.src = '';
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

                // Mark hlsAudioNext as the active audio element
                this.activeHlsAudioId = 'hlsAudioNext';

                // Get reference to the next audio element (now becomes main)
                const nextAudio = document.getElementById('hlsAudioNext');
                if (nextAudio) {
                    this.duration = nextAudio.duration || 0;

                    // Set up ended handler for the new audio
                    const self = this;
                    nextAudio.onended = function() {
                        if (!self.isCrossfading) {
                            self.onTrackEnded();
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

            // Reset crossfade state
            this.isCrossfading = false;

            console.log('Crossfade complete, now playing:', this.currentSong?.song_title?.tr);
        },

        seekTo(e) {
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
                console.warn('⚠️ seekTo called with invalid argument:', e);
                return;
            }

            // 🔒 PREVIEW LIMIT: Guest kullanıcılar sadece 30 saniye dinleyebilir
            if (this.previewDuration && this.previewDuration > 0) {
                const maxSeekTime = this.previewDuration; // 30 saniye

                if (newTime > maxSeekTime) {
                    // 30 saniye sonrasına gitmeye çalışıyor - Blokla!
                    console.log(`🔒 Seek blocked: Tried to seek to ${newTime.toFixed(1)}s but max is ${maxSeekTime}s`);
                    this.showToast('Premium\'a geçin, tüm şarkıyı dinleyin!', 'warning');

                    // Modal göster
                    const playLimitsElement = document.querySelector('[x-data*="playLimits"]');
                    if (playLimitsElement) {
                        const playLimitsComponent = Alpine.$data(playLimitsElement);
                        if (playLimitsComponent) {
                            playLimitsComponent.showGuestModal = true;
                        }
                    }

                    return; // Seek işlemini iptal et
                }
            }

            if (this.howl && this.duration) {
                this.howl.seek(newTime);
            }
            if (this.hls) {
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
            // Dispatch stop event (track ended naturally)
            window.dispatchEvent(new CustomEvent('player:stop'));

            if (this.repeatMode === 'one') {
                // Repeat current song
                if (this.howl) {
                    this.howl.seek(0);
                    this.howl.play();
                }
                if (this.hls) {
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

        async playAlbum(id) {
            try {
                // 🚀 INSTANT FEEDBACK: Show loading state immediately
                this.isLoading = true;
                this.showToast('Yükleniyor...', 'info');

                const response = await fetch(`/api/muzibu/albums/${id}`);
                const album = await response.json();

                if (album.songs && album.songs.length > 0) {
                    // 🧹 Clean queue from null/undefined songs
                    this.queue = this.cleanQueue(album.songs);

                    if (this.queue.length === 0) {
                        this.showToast('Albümde çalınabilir şarkı bulunamadı', 'error');
                        return;
                    }

                    // 🎯 Preload first song in queue
                    this.preloadFirstInQueue();

                    this.queueIndex = 0;
                    await this.playSongFromQueue(0);

                    // Safe album title extraction
                    const albumTitle = album.album_title?.tr || album.album_title?.en || album.album_title || 'Albüm';
                    this.showToast(`${albumTitle} çalınıyor`, 'success');
                }
            } catch (error) {
                console.error('Failed to play album:', error);
                this.showToast('Albüm yüklenemedi', 'error');
            } finally {
                this.isLoading = false;
            }
        },

        async playPlaylist(id) {
            try {
                // 🚀 INSTANT FEEDBACK: Show loading state immediately
                this.isLoading = true;
                this.showToast('Yükleniyor...', 'info');

                const response = await fetch(`/api/muzibu/playlists/${id}`);
                const playlist = await response.json();

                if (playlist.songs && playlist.songs.length > 0) {
                    // 🧹 Clean queue from null/undefined songs
                    this.queue = this.cleanQueue(playlist.songs);

                    if (this.queue.length === 0) {
                        this.showToast('Playlist\'te çalınabilir şarkı bulunamadı', 'error');
                        return;
                    }

                    // 🎯 Preload first song in queue
                    this.preloadFirstInQueue();

                    this.queueIndex = 0;
                    await this.playSongFromQueue(0);

                    // Safe playlist title extraction
                    const playlistTitle = playlist.title?.tr || playlist.title?.en || playlist.title || 'Playlist';
                    this.showToast(`${playlistTitle} çalınıyor`, 'success');
                }
            } catch (error) {
                console.error('Failed to play playlist:', error);
                this.showToast('Playlist yüklenemedi', 'error');
            } finally {
                this.isLoading = false;
            }
        },

        async playGenre(id) {
            try {
                // 🚀 INSTANT FEEDBACK: Show loading state immediately
                this.isLoading = true;
                this.showToast('Yükleniyor...', 'info');

                const response = await fetch(`/api/muzibu/genres/${id}/songs`);
                const data = await response.json();

                if (data.songs && data.songs.length > 0) {
                    this.queue = this.cleanQueue(data.songs);

                    if (this.queue.length === 0) {
                        this.showToast('Tür\'de çalınabilir şarkı bulunamadı', 'error');
                        return;
                    }

                    this.queueIndex = 0;
                    await this.playSongFromQueue(0);

                    const genreTitle = data.genre?.title?.tr || data.genre?.title?.en || data.genre?.title || 'Tür';
                    this.showToast(`${genreTitle} çalınıyor`, 'success');
                }
            } catch (error) {
                console.error('Failed to play genre:', error);
                this.showToast('Tür yüklenemedi', 'error');
            } finally {
                this.isLoading = false;
            }
        },

        async playSector(id) {
            try {
                // 🚀 INSTANT FEEDBACK: Show loading state immediately
                this.isLoading = true;
                this.showToast('Yükleniyor...', 'info');

                const response = await fetch(`/api/muzibu/sectors/${id}/songs`);
                const data = await response.json();

                if (data.songs && data.songs.length > 0) {
                    this.queue = this.cleanQueue(data.songs);

                    if (this.queue.length === 0) {
                        this.showToast('Sektör\'de çalınabilir şarkı bulunamadı', 'error');
                        return;
                    }

                    this.queueIndex = 0;
                    await this.playSongFromQueue(0);

                    const sectorTitle = data.sector?.title?.tr || data.sector?.title?.en || data.sector?.title || 'Sektör';
                    this.showToast(`${sectorTitle} çalınıyor`, 'success');
                }
            } catch (error) {
                console.error('Failed to play sector:', error);
                this.showToast('Sektör yüklenemedi', 'error');
            } finally {
                this.isLoading = false;
            }
        },

        async playSong(id) {
            try {
                // 🚨 INSTANT PLAY: Cancel crossfade (manual song change)
                this.isCrossfading = false;

                // Stop current playback FIRST before loading new song
                await this.stopCurrentPlayback();

                this.isLoading = true;

                // 🚀 OPTIMIZED: Get stream URL directly (includes song info)
                const streamResponse = await fetch(`/api/muzibu/songs/${id}/stream`);

                // ❌ HTTP Error Check
                if (!streamResponse.ok) {
                    const errorData = await streamResponse.json().catch(() => ({}));
                    if (streamResponse.status === 404) {
                        this.showToast('Şarkı bulunamadı', 'error');
                    } else if (streamResponse.status >= 500) {
                        this.showToast('Sunucu hatası', 'error');
                    } else {
                        this.showToast(errorData.message || 'Bir hata oluştu', 'error');
                    }
                    return;
                }

                const streamData = await streamResponse.json();

                // 🎵 Build song object from stream API response
                const song = {
                    song_id: id,
                    song_title: streamData.song?.title || 'Bilinmeyen Şarkı',
                    duration: streamData.song?.duration || '0:00',
                    album_cover: null
                };

                // 🎯 COVER: Extract from stream API
                if (streamData.song?.cover_url) {
                    const coverMatch = streamData.song.cover_url.match(/\/thumb\/(\d+)\//);
                    song.album_cover = coverMatch ? coverMatch[1] : streamData.song.cover_url;
                }

                // Create queue with just this song
                this.queue = [song];
                this.queueIndex = 0;
                this.currentSong = song;
                this.playTracked = false;

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

                // 🎵 Play immediately
                await this.loadAndPlaySong(
                    streamData.stream_url,
                    streamData.stream_type,
                    streamData.preview_duration || null
                );
                this.showToast('Şarkı çalınıyor', 'success');
            } catch (error) {
                console.error('Failed to play song:', error);
                this.showToast('Şarkı yüklenemedi', 'error');
            } finally {
                this.isLoading = false;
            }
        },

        async playSongFromQueue(index, autoplay = true) {
            if (index < 0 || index >= this.queue.length) return;

            const song = this.queue[index];
            this.currentSong = song;
            this.queueIndex = index;
            this.playTracked = false;
            this.isPreviewBlocked = false;

            // Check if song is favorited (background, don't wait)
            this.checkFavoriteStatus(song.song_id);

            // Store autoplay preference for loadAndPlaySong
            this._autoplayNext = autoplay;

            try {
                let data;

                // 🚀 CHECK CACHE FIRST - instant playback if cached!
                const cached = this.getCachedStream(song.song_id);
                if (cached) {
                    console.log('⚡ Using cached stream URL:', song.song_id);
                    data = cached;
                } else {
                    // Fetch from API if not cached
                    const response = await fetch(`/api/muzibu/songs/${song.song_id}/stream`);

                    if (!response.ok) {
                        this.showToast(`Şarkı yüklenemedi, sonrakine geçiliyor...`, 'warning');
                        if (this.queueIndex < this.queue.length - 1) {
                            await this.nextTrack();
                        } else {
                            this.isPlaying = false;
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
                const shouldAutoplay = this._autoplayNext !== false;
                await this.loadAndPlaySong(
                    data.stream_url,
                    data.stream_type || 'mp3',
                    data.preview_duration || null,
                    shouldAutoplay
                );
                // Reset autoplay flag
                this._autoplayNext = true;

                // 🚀 Preload next songs in background (don't wait)
                this.preloadNextThreeSongs();
            } catch (error) {
                console.error('Failed to load song:', error);
                this.showToast('Şarkı yüklenemedi', 'error');
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
                                console.log(`HLS prefetch started for: ${song.song_title?.tr || song.song_id}`);
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
            const self = this;
            const targetVolume = this.isMuted ? 0 : this.volume / 100;

            // Stop and fade out current playback
            await this.stopCurrentPlayback();

            // 🎵 GUEST PREVIEW: Clear existing preview timers and unblock
            if (this.previewTimer) {
                clearTimeout(this.previewTimer);
                this.previewTimer = null;
            }
            if (this.fadeOutTimer) {
                clearTimeout(this.fadeOutTimer);
                this.fadeOutTimer = null;
            }
            // Unblock next/previous when starting a new song
            this.isPreviewBlocked = false;

            // 🎯 Reset intro skip flag for new song
            this.introSkipped = false;

            // Clear progress interval
            if (this.progressInterval) {
                clearInterval(this.progressInterval);
            }

            // 🎯 Store preview duration in instance for Howler callback access
            this.previewDuration = previewDuration;

            // Use stream type from API if provided, otherwise detect from URL
            let useHls = false;
            if (streamType) {
                // 🔒 GUEST PREVIEW: Force MP3 for preview mode (HLS has encryption issues)
                if (previewDuration && previewDuration > 0) {
                    useHls = false; // Force MP3 fallback immediately
                    console.log('🔒 Guest preview mode: Forcing MP3 (skipping HLS)');
                } else {
                    useHls = streamType === 'hls';
                }
            } else {
                // Fallback: detect from URL
                const isDirectAudio = url.match(/\.(mp3|ogg|wav|webm|aac|m4a)(\?|$)/i);
                const isHlsUrl = url.includes('.m3u8') || url.includes('m3u8') || url.includes('/hls/');
                useHls = isHlsUrl || !isDirectAudio;
            }

            // Use passed autoplay parameter
            console.log('🔍 Autoplay mode:', autoplay);

            if (useHls) {
                this.isHlsStream = true;
                await this.playHlsStream(url, targetVolume, autoplay);
            } else {
                this.isHlsStream = false;
                // 🔒 GUEST PREVIEW: Use fallback URL if available (faster MP3 loading)
                const playUrl = (previewDuration && previewDuration > 0 && this.currentFallbackUrl)
                    ? this.currentFallbackUrl
                    : url;
                console.log('🎵 Playing URL:', playUrl, 'Autoplay:', autoplay);
                await this.playWithHowler(playUrl, targetVolume, autoplay);
            }

            // 🎵 GUEST PREVIEW: Setup preview duration limits
            if (previewDuration && previewDuration > 0) {
                // Wait a bit for audio to load and get duration
                setTimeout(() => {
                    const duration = this.duration || 180; // Fallback to 3 minutes

                    // 🎯 PREVIEW CALCULATION: Play from 0s to previewDuration (30s)
                    const fadeStartSeconds = previewDuration - 5; // Start fade 5s before end (25s)

                    console.log('🎵 Guest Preview Config:', {
                        totalDuration: duration,
                        startFrom: '0s',
                        playDuration: previewDuration + 's',
                        fadeStartAt: fadeStartSeconds + 's',
                        stopAt: previewDuration + 's'
                    });

                    // FADE-OUT: Start fade 5 seconds before end (from intro skip point)
                    this.fadeOutTimer = setTimeout(() => {
                        console.log('🎵 Guest preview: Fade-out başladı (son 5 saniye)');
                        const targetVolume = this.isMuted ? 0 : this.volume / 100;

                        if (this.howl && this.howl.playing()) {
                            this.howl.fade(targetVolume, 0, 5000); // 5 second fade-out
                        } else if (this.hls) {
                            const audio = this.getActiveHlsAudio();
                            if (audio && !audio.paused) {
                                this.fadeAudioElement(audio, audio.volume, 0, 5000); // 5 second fade-out
                            }
                        }
                    }, fadeStartSeconds * 1000);

                    // STOP: Stop playback after preview duration (from intro skip point)
                    this.previewTimer = setTimeout(() => {
                        console.log('🛑 Guest preview ended - stopping playback');

                        // Pause playback (Howler or HLS)
                        if (this.howl) {
                            this.howl.pause();
                        } else if (this.hls) {
                            const audio = this.getActiveHlsAudio();
                            if (audio) audio.pause();
                        }
                        this.isPlaying = false;

                        // 🔒 Block next/previous buttons
                        this.isPreviewBlocked = true;

                        // Show guest modal
                        const playLimitsElement = document.querySelector('[x-data*="playLimits"]');
                        if (playLimitsElement) {
                            const playLimitsComponent = Alpine.$data(playLimitsElement);
                            if (playLimitsComponent) {
                                playLimitsComponent.showGuestModal = true;
                            }
                        }

                        this.showToast('Premium\'a geçin, sınırsız dinleyin!', 'info');
                    }, previewDuration * 1000);

                }, 500); // Wait 500ms for audio to load
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

            // Stop HLS if playing (check both audio elements)
            if (this.hls) {
                const audio = this.getActiveHlsAudio();
                if (audio && !audio.paused) {
                    wasStopped = true;
                    // 🚀 INSTANT STOP: No fade, immediate pause
                    audio.pause();
                }
                this.hls.destroy();
                this.hls = null;
            }

            // Also clean up hlsAudioNext if exists
            const nextAudio = document.getElementById('hlsAudioNext');
            if (nextAudio) {
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
            console.log('🎵 playWithHowler called with URL:', url);
            console.log('🔍 URL type:', typeof url);
            console.log('🔍 URL length:', url?.length);
            console.log('🔍 Autoplay:', autoplay);

            // Determine format from URL or default to mp3
            let format = ['mp3'];
            if (url.includes('.ogg')) format = ['ogg'];
            else if (url.includes('.wav')) format = ['wav'];
            else if (url.includes('.webm')) format = ['webm'];

            console.log('🎵 Creating Howl with src:', [url]);

            this.howl = new Howl({
                src: [url],
                format: format,
                html5: true,
                volume: 0,
                autoplay: autoplay,
                onload: function() {
                    self.duration = self.howl.duration();
                    console.log('Howler loaded, duration:', self.duration);
                },
onplay: function() {
                    self.isPlaying = true;
                    self.startProgressTracking('howler');

                    // Dispatch event for play-limits
                    window.dispatchEvent(new CustomEvent('player:play', {
                        detail: {
                            songId: self.currentSong?.song_id,
                            isLoggedIn: self.isLoggedIn
                        }
                    }));
                },
                onend: function() {
                    if (!self.isCrossfading) {
                        self.onTrackEnded();
                    }
                },
                onloaderror: function(id, error) {
                    console.error('Howler load error:', error);
                    console.error('🔍 Howler ID:', id);
                    console.error('🔍 Howler._src:', self.howl?._src);
                    console.error('🔍 Howler.src():', self.howl?.src());
                    console.error('❌ MP3 playback failed, cannot fallback (already in fallback mode)');
                    self.showToast('Şarkı yüklenemedi', 'error');
                    self.isPlaying = false;

                    // Bir sonraki şarkıya geç
                    setTimeout(() => {
                        self.nextTrack();
                    }, 1500);
                },
                onplayerror: function(id, error) {
                    console.error('Howler play error:', error);
                    self.showToast('Çalma hatası', 'error');
                    self.isPlaying = false;
                }
            });

            if (autoplay) {
                this.howl.play();
                this.howl.fade(0, targetVolume, this.fadeOutDuration);
                this.isPlaying = true;
            } else {
                // Preload mode: loaded but paused
                this.isPlaying = false;
                console.log('Howler loaded (PAUSED, ready to play)');
            }
        },

        // Play using HLS.js (for HLS streams)
        async playHlsStream(url, targetVolume, autoplay = true) {
            const self = this;
            const audio = this.$refs.hlsAudio;

            if (!audio) {
                console.error('HLS audio element not found');
                return;
            }

            console.log('🔍 HLS Autoplay:', autoplay);

            // 🛡️ Flag to prevent play() after error/fallback
            let hlsAborted = false;
            let hlsPlayStarted = false;

            // 🔥 HLS TIMEOUT FALLBACK: 6 saniye icinde calmaya baslamazsa MP3'e dus
            const hlsTimeoutMs = 6000;
            const hlsTimeoutId = setTimeout(() => {
                if (!hlsPlayStarted && !hlsAborted && autoplay) {
                    console.warn('⏰ HLS timeout - MP3 fallback tetikleniyor...');
                    hlsAborted = true;
                    self.triggerMp3Fallback(audio, targetVolume, 'timeout');
                }
            }, hlsTimeoutMs);

            // Helper: HLS timeout'u temizle ve basariyi logla
            const markHlsSuccess = () => {
                hlsPlayStarted = true;
                clearTimeout(hlsTimeoutId);
            };

            // Check HLS.js support
            if (Hls.isSupported()) {
                // Store original chunk URLs with tokens from playlist
                const chunkUrlsWithTokens = {};

                this.hls = new Hls({
                    enableWorker: true,
                    lowLatencyMode: false,
                    // 🔑 KEY LOADING POLICY - Prevent keyLoadError with retries
                    keyLoadPolicy: {
                        default: {
                            maxTimeToFirstByteMs: 8000,  // 8 second timeout for first byte
                            maxLoadTimeMs: 15000,        // 15 second total timeout
                            timeoutRetry: {
                                maxNumRetry: 3,          // 3 timeout retries
                                retryDelayMs: 1000,      // 1 second delay
                                maxRetryDelayMs: 4000    // Max 4 seconds
                            },
                            errorRetry: {
                                maxNumRetry: 5,          // 5 error retries (critical for key)
                                retryDelayMs: 500,       // 500ms initial delay
                                maxRetryDelayMs: 3000,   // Max 3 seconds
                                backoff: 'exponential'   // Exponential backoff
                            }
                        }
                    },
                    // 🎵 FRAGMENT LOADING POLICY
                    fragLoadPolicy: {
                        default: {
                            maxTimeToFirstByteMs: 6000,
                            maxLoadTimeMs: 20000,
                            timeoutRetry: {
                                maxNumRetry: 2,
                                retryDelayMs: 1000,
                                maxRetryDelayMs: 4000
                            },
                            errorRetry: {
                                maxNumRetry: 3,
                                retryDelayMs: 500,
                                maxRetryDelayMs: 3000
                            }
                        }
                    },
                    // Custom XHR setup to preserve query strings (tokens) for chunks only
                    xhrSetup: function(xhr, url) {
                        // 🔑 For encryption key requests - ensure proper handling
                        if (url.includes('/key') || url.includes('/key/')) {
                            xhr.withCredentials = false;
                            return;
                        }

                        // HLS.js strips query strings from chunks, we restore them here
                        // Extract chunk filename from URL
                        const chunkMatch = url.match(/chunk_\d+\.ts/);
                        if (chunkMatch && chunkUrlsWithTokens[chunkMatch[0]]) {
                            // Replace with stored URL that has token
                            xhr.open('GET', chunkUrlsWithTokens[chunkMatch[0]], true);
                            return;
                        }
                    }
                });

                // Intercept playlist loading to extract chunk URLs with tokens
                this.hls.on(Hls.Events.LEVEL_LOADED, function(event, data) {
                    if (data.details && data.details.fragments) {
                        data.details.fragments.forEach(function(fragment) {
                            if (fragment.url) {
                                const chunkMatch = fragment.url.match(/chunk_\d+\.ts/);
                                if (chunkMatch) {
                                    chunkUrlsWithTokens[chunkMatch[0]] = fragment.url;
                                }
                            }
                        });
                    }
                });

                this.hls.loadSource(url);
                this.hls.attachMedia(audio);

                // 🔑 Track key loading for debugging
                this.hls.on(Hls.Events.KEY_LOADING, function(event, data) {
                    console.log('🔑 Key loading started:', data.frag?.decryptdata?.uri);
                });

                this.hls.on(Hls.Events.KEY_LOADED, function(event, data) {
                    console.log('✅ Key loaded successfully for song:', self.currentSong?.song_id);
                });

                // 🔑 Non-fatal error handling with retry info
                this.hls.on(Hls.Events.ERROR, function(event, data) {
                    if (!data.fatal && data.details === 'keyLoadError') {
                        console.warn('⚠️ Key load retry:', {
                            song: self.currentSong?.song_id,
                            retry: data.frag?.loader?.stats?.retry || 0
                        });
                    }
                });

                this.hls.on(Hls.Events.MANIFEST_PARSED, function() {
                    // 🛡️ Check if HLS was aborted (error occurred before manifest parsed)
                    if (hlsAborted) {
                        console.log('⚠️ HLS aborted, skipping play()');
                        return;
                    }

                    audio.volume = 0;

                    if (autoplay) {
                        audio.play().then(() => {
                            // 🛡️ Double-check: HLS might have been aborted during play promise
                            if (hlsAborted) {
                                console.log('⚠️ HLS aborted during play(), stopping');
                                audio.pause();
                                return;
                            }

                            // ✅ HLS basariyla caldi - timeout'u temizle
                            markHlsSuccess();
                            console.log('✅ HLS basariyla basladi');

                            self.isPlaying = true;
                            self.fadeAudioElement(audio, 0, targetVolume, self.fadeOutDuration);
                            self.startProgressTracking('hls');

                            // Dispatch event for play-limits (HLS)
                            window.dispatchEvent(new CustomEvent('player:play', {
                                detail: {
                                    songId: self.currentSong?.song_id,
                                    isLoggedIn: self.isLoggedIn
                                }
                            }));
                        }).catch(e => {
                            // 🛡️ AbortError is expected when fallback kicks in - don't show error toast
                            if (e.name === 'AbortError') {
                                console.log('⚠️ HLS play aborted (expected during fallback)');
                            } else {
                                console.error('HLS play error:', e);
                                self.showToast('Çalma hatası', 'error');
                            }
                        });
                    } else {
                        // Preload mode: load but don't play
                        markHlsSuccess(); // Preload da basarili sayilir
                        self.duration = audio.duration || 0;
                        self.isPlaying = false;
                        console.log('HLS loaded (PAUSED, ready to play)');
                    }
                });

                this.hls.on(Hls.Events.ERROR, function(event, data) {
                    if (data.fatal) {
                        console.error('HLS fatal error:', data);

                        // 🛡️ Set abort flag FIRST to prevent MANIFEST_PARSED from calling play()
                        hlsAborted = true;
                        clearTimeout(hlsTimeoutId); // Timeout'u temizle

                        // HLS yüklenemezse MP3'e fallback (SIGNED URL)
                        // Sadece NETWORK_ERROR degil, TUM fatal error'larda fallback yap
                        if (self.currentSong && self.currentFallbackUrl) {
                            console.log('🔄 HLS failed, falling back to signed MP3...');
                            console.log('🔍 currentFallbackUrl:', self.currentFallbackUrl);
                            console.log('🔍 currentFallbackUrl type:', typeof self.currentFallbackUrl);

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
                            self.showToast('MP3 ile çalıyor, HLS hazırlanıyor...', 'info');

                            // MP3 ile çal (signed URL)
                            console.log('🔍 About to call playWithHowler with:', self.currentFallbackUrl);
                            self.playWithHowler(self.currentFallbackUrl, targetVolume);
                        } else {
                            self.showToast('Şarkı yüklenemedi', 'error');
                            self.isPlaying = false;
                        }
                    }
                });

                // Handle track end
                audio.onended = function() {
                    if (!self.isCrossfading) {
                        self.onTrackEnded();
                    }
                };

                // Get duration when available
                audio.onloadedmetadata = function() {
                    self.duration = audio.duration;
                    console.log('HLS loaded, duration:', self.duration);
                };
            } else if (audio.canPlayType('application/vnd.apple.mpegurl')) {
                // Native HLS support (Safari)
                audio.src = url;
                audio.volume = 0;
                audio.play().then(() => {
                    self.isPlaying = true;
                    self.fadeAudioElement(audio, 0, targetVolume, self.fadeOutDuration);
                    self.startProgressTracking('hls');

                    // Dispatch event for play-limits (Safari native HLS)
                    window.dispatchEvent(new CustomEvent('player:play', {
                        detail: {
                            songId: self.currentSong?.song_id,
                            isLoggedIn: self.isLoggedIn
                        }
                    }));
                });
            } else {
                console.error('HLS not supported');
                this.showToast('HLS desteklenmiyor', 'error');
            }
        },

        // 🔥 HLS Timeout/Error icin MP3 Fallback Helper
        triggerMp3Fallback(audio, targetVolume, reason = 'unknown') {
            console.log('🔄 MP3 fallback tetiklendi, sebep:', reason);

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
                this.showToast('HLS yuklenemedi, MP3 ile caliniyor...', 'info');
                this.isHlsStream = false;
                this.playWithHowler(this.currentFallbackUrl, targetVolume);
            } else {
                this.showToast('Sarki yuklenemedi', 'error');
                this.isPlaying = false;
            }
        },

        // Fade audio element volume using requestAnimationFrame
        fadeAudioElement(audio, fromVolume, toVolume, duration) {
            return new Promise(resolve => {
                if (this._fadeAnimation) cancelAnimationFrame(this._fadeAnimation);

                const startTime = performance.now();
                const volumeDiff = toVolume - fromVolume;

                const animate = (currentTime) => {
                    const elapsed = currentTime - startTime;
                    const progress = Math.min(elapsed / duration, 1);

                    audio.volume = fromVolume + (volumeDiff * progress);

                    if (progress < 1) {
                        this._fadeAnimation = requestAnimationFrame(animate);
                    } else {
                        audio.volume = toVolume;
                        resolve();
                    }
                };

                this._fadeAnimation = requestAnimationFrame(animate);
            });
        },

        // Start progress tracking for either Howler or HLS
        startProgressTracking(type) {
            const self = this;

            this.progressInterval = setInterval(() => {
                let currentTime = 0;
                let isCurrentlyPlaying = false;

                if (type === 'howler' && this.howl) {
                    currentTime = this.howl.seek();
                    isCurrentlyPlaying = this.howl.playing();
                } else if (type === 'hls') {
                    const audio = this.$refs.hlsAudio;
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

                    // 🎵 Track play after 60 seconds (analytics)
                    if (!self.playTracked && currentTime >= self.playTrackedAt && self.currentSong && self.isLoggedIn) {
                        self.playTracked = true;
                        self.trackSongPlay(self.currentSong.id);
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

                        console.log('Page loaded:', url);
                    }
                } else {
                    console.error('Main content not found in response');
                }

                this.isLoading = false;
            } catch (error) {
                console.error('Failed to load page:', error);
                this.showToast('Sayfa yüklenemedi', 'error');
                this.isLoading = false;

                // Fallback to full page reload on error
                window.location.href = url;
            }
        },

        shareContent(type, id) {
            console.log('Sharing:', type, id);
            this.showToast('Paylaşım linki kopyalandı', 'success');
        },

        // 🎵 Track song play (analytics) - Called after 60 seconds of playback
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
                            artist_name: data.song.artist?.name || 'Bilinmeyen Sanatçı',
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
                            artist_name: song.artist?.name || data.album.artist?.name || 'Bilinmeyen Sanatçı',
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
                            artist_name: song.artist?.name || 'Bilinmeyen Sanatçı',
                            album_name: song.album?.title || '',
                            album_cover: song.album?.cover_image || '/placeholder-album.jpg',
                            duration: song.duration || 0
                        }));
                    }
                }

                if (songs.length > 0) {
                    // Add songs to queue
                    this.queue.push(...songs);

                    const message = songs.length === 1
                        ? 'Şarkı kuyruğa eklendi'
                        : `${songs.length} şarkı kuyruğa eklendi`;

                    this.showToast(message, 'success');
                } else {
                    this.showToast('Şarkı bulunamadı', 'error');
                }
            } catch (error) {
                console.error('Add to queue error:', error);
                this.showToast('Kuyruğa eklenirken hata oluştu', 'error');
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

            this.showToast('Şarkı kuyruktan kaldırıldı', 'info');
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

            this.showToast('Çalma listesi temizlendi', 'info');
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
            console.log('Going to artist:', id);
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

        // checkAuth() removed - user data now loaded directly from Laravel backend on page load

        async handleLogin() {
            // Form boşluk kontrolü
            if (!this.loginForm.email || !this.loginForm.password) {
                this.authError = 'Lütfen tüm alanları doldurun';
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
                        console.log('🔐 CSRF token refreshed after login');
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

                    // 🔓 PREMIUM: Reset preview block for logged-in users
                    this.isPreviewBlocked = false;
                    console.log('🔓 Preview block removed - User logged in');

                    // 🔄 RELOAD CURRENT SONG: Clear preview timers and reload without preview limit
                    if (this.previewTimer) {
                        clearTimeout(this.previewTimer);
                        this.previewTimer = null;
                    }
                    if (this.fadeOutTimer) {
                        clearTimeout(this.fadeOutTimer);
                        this.fadeOutTimer = null;
                    }

                    // If there's a current song playing with preview, reload it without preview
                    if (this.currentSong && this.currentSong.song_id) {
                        console.log('🔄 Reloading current song without preview restrictions...');
                        const currentTime = this.currentTime || 0;
                        const wasPlaying = this.isPlaying;

                        // Reload song from API (will get full access now)
                        fetch(`/api/muzibu/songs/${this.currentSong.song_id}/stream`)
                            .then(res => res.json())
                            .then(async data => {
                                if (data.stream_url) {
                                    // Stop current playback
                                    await this.stopCurrentPlayback();

                                    // Load without preview (data.preview_duration will be null for premium)
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

                                    console.log('✅ Song reloaded with full access');
                                }
                            })
                            .catch(err => console.error('Failed to reload song:', err));
                    }

                    // 🎵 Başarı mesajı göster
                    this.showToast('Hoş geldin, ' + data.user.name + '! 🎉', 'success');

                    console.log('✅ Login successful - reloading page for proper session...');
                    console.log('👤 User logged in:', {
                        name: data.user.name,
                        email: data.user.email,
                        is_premium: data.user.is_premium || false
                    });

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
                        this.authError = data.message || 'E-posta veya şifre hatalı';
                    }
                }
            } catch (error) {
                console.error('Login error:', error);
                this.authError = 'Bir hata oluştu, lütfen tekrar deneyin';
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
                this.authError = 'Lütfen tüm alanları doğru şekilde doldurun';
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
                        console.log('🔐 CSRF token refreshed after register');
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

                    // 🔓 PREMIUM: Reset preview block for new users
                    this.isPreviewBlocked = false;
                    console.log('🔓 Preview block removed - New user registered with trial');

                    // 🔄 RELOAD CURRENT SONG: Clear preview timers and reload without preview limit
                    if (this.previewTimer) {
                        clearTimeout(this.previewTimer);
                        this.previewTimer = null;
                    }
                    if (this.fadeOutTimer) {
                        clearTimeout(this.fadeOutTimer);
                        this.fadeOutTimer = null;
                    }

                    // If there's a current song playing with preview, reload it without preview
                    if (this.currentSong && this.currentSong.song_id) {
                        console.log('🔄 Reloading current song without preview restrictions...');
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

                                    console.log('✅ Song reloaded with full trial access');
                                }
                            })
                            .catch(err => console.error('Failed to reload song:', err));
                    }

                    // 🎵 Başarı mesajı göster
                    this.showToast('Hoş geldin, ' + data.user.name + '! 🎉 Premium denemen başladı.', 'success');

                    console.log('✅ Register successful - reloading page for proper session...');

                    // 🔄 SESSION FIX: Sayfa yenileme ile session cookie'lerin düzgün set edilmesini garantile
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    this.authError = data.message || 'Kayıt başarısız, lütfen bilgilerinizi kontrol edin';
                }
            } catch (error) {
                console.error('Register error:', error);
                this.authError = 'Bir hata oluştu, lütfen tekrar deneyin';
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
                    this.authSuccess = 'Şifre sıfırlama linki e-postanıza gönderildi! ✉️';
                    this.forgotForm = { email: '' };
                    // 3 saniye sonra login sayfasına yönlendir
                    setTimeout(() => {
                        window.location.href = '/login';
                    }, 3000);
                } else {
                    this.authError = data.message || 'E-posta gönderilemedi';
                }
            } catch (error) {
                console.error('Forgot password error:', error);
                this.authError = 'Bir hata oluştu, lütfen tekrar deneyin';
            } finally {
                this.authLoading = false;
            }
        },

        async logout() {
            // Çift tıklamayı engelle
            if (this.isLoggingOut) return;

            console.log('🚪 Logging out user...');

            // Hemen UI'ı güncelle (SPA-friendly)
            this.isLoggingOut = true;
            this.isLoggedIn = false;
            this.currentUser = null;
            this.showAuthModal = null;

            try {
                // Logout isteğini BEKLE
                const response = await fetch('/logout', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });

                console.log('✅ Logout successful - SPA mode maintained!');
                console.log('👤 User logged out:', {
                    isLoggedIn: this.isLoggedIn,
                    currentUser: this.currentUser
                });

                // Toast mesajı
                this.showToast('Başarıyla çıkış yaptınız! 👋', 'success');

                // SPA-friendly: Sayfayı YENILEME, sadece state temizle
                this.isLoggingOut = false;

                // 🔐 CSRF token yenile (logout sonrası session regenerate edilir)
                if (response.ok) {
                    const data = await response.json();
                    if (data.csrf_token) {
                        document.querySelector('meta[name="csrf-token"]').setAttribute('content', data.csrf_token);
                        console.log('🔐 CSRF token refreshed after logout');
                    }
                }
            } catch (error) {
                console.error('❌ Logout error:', error);
                this.isLoggingOut = false;
                this.showToast('Çıkış yapılırken hata oluştu', 'error');
            }
        },

        // 🧹 Clean queue: Remove null/undefined songs
        cleanQueue(songs) {
            if (!Array.isArray(songs)) return [];
            return songs.filter(song => song !== null && song !== undefined && typeof song === 'object');
        },

        toggleTheme() {
            this.isDarkMode = !this.isDarkMode;
            safeStorage.setItem('theme', this.isDarkMode ? 'dark' : 'light');
            this.showToast(this.isDarkMode ? 'Koyu tema aktif' : 'Açık tema aktif', 'success');
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
            this.showToast('Sıra güncellendi', 'success');
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
            // Backward compatibility: Still works as before (preloads first song)
            await this.preloadNextThreeSongs();
        },

        /**
         * 🚀 AGGRESSIVE PRELOAD: İlk 3 şarkıyı preload et (0ms transition)
         */
        async preloadNextThreeSongs() {
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
                console.log('🔍 No songs to preload (queue too short)');
                return;
            }

            console.log(`🚀 Aggressive Preload: Loading next ${songsToPreload.length} songs...`);

            // Paralel preload (3 şarkıyı aynı anda yükle)
            const preloadPromises = songsToPreload.map(song =>
                this.preloadSongOnHover(song.song_id)
            );

            // Tüm preload'lar tamamlanana kadar bekle (ama hata olsa bile devam et)
            await Promise.allSettled(preloadPromises);

            console.log(`✅ Aggressive Preload completed: ${songsToPreload.length} songs ready`);
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

            // Already cached?
            if (this.streamUrlCache.has(songId)) {
                return;
            }

            try {
                // 🚀 Fetch stream URL and cache it
                const response = await fetch(`/api/muzibu/songs/${songId}/stream`);
                if (!response.ok) return;

                const data = await response.json();

                // Cache the stream data for instant playback later
                this.streamUrlCache.set(songId, {
                    stream_url: data.stream_url,
                    stream_type: data.stream_type,
                    fallback_url: data.fallback_url,
                    preview_duration: data.preview_duration,
                    cached_at: Date.now()
                });

                // 🎯 Preload HLS playlist (triggers browser cache)
                if (data.stream_type === 'hls' && data.stream_url) {
                    fetch(data.stream_url).catch(() => {}); // Fire and forget
                }

                this.preloadedSongs.add(songId);
                console.log('✅ Preloaded & cached:', songId);

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

            // Cache valid for 5 minutes
            if (Date.now() - cached.cached_at > 300000) {
                this.streamUrlCache.delete(songId);
                return null;
            }

            return cached;
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

            console.log('✅ Queue Monitor started (checks every 10s)');
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
                    console.log(`🔍 Queue Check: ${queueLength} songs remaining (queueIndex: ${this.queueIndex}/${this.queue.length})`);
                }

                // Eğer 3 veya daha az şarkı kaldıysa refill et
                if (queueLength <= 3) {
                    // Context var mı kontrol et
                    const context = Alpine.store('muzibu')?.getPlayContext();

                    if (!context) {
                        // Sadece ilk kez uyar (boş queue spam yapmasın)
                        if (this.queue.length > 0) {
                            console.warn('⚠️ No play context - cannot auto-refill queue');
                        }
                        return;
                    }

                    console.warn('⚠️ Queue running low! Auto-refilling...');

                    // Mevcut offset'i hesapla (kaç şarkı çalındı)
                    const currentOffset = context.offset || 0;

                    // Alpine store'dan refillQueue çağır
                    const newSongs = await Alpine.store('muzibu').refillQueue(currentOffset, 15);

                    if (newSongs && newSongs.length > 0) {
                        // Queue'ya ekle (mevcut queue'nun sonuna)
                        this.queue = [...this.queue, ...newSongs];
                        console.log(`✅ Auto-refilled: ${newSongs.length} songs added (Total queue: ${this.queue.length})`);

                        // İlk şarkıyı preload et
                        this.preloadFirstInQueue();
                    } else {
                        console.warn('⚠️ Auto-refill returned empty - queue might end soon!');

                        // Context Transition: Eğer queue boşsa Genre'ye geç
                        if (context.type !== 'genre') {
                            console.log('🔄 Queue empty - attempting context transition to genre...');
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
                        console.log('📱 Page hidden - Background playback active');
                        // Müzik çalmaya devam etsin (hiçbir şey yapma, otomatik devam eder)
                    } else {
                        console.log('👀 Page visible - Welcome back!');
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

                console.log('✅ Background playback enabled (works when minimized)');

            } catch (error) {
                console.error('❌ Background playback error:', error);
            }
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

            // Volume değiştiğinde kaydet
            this.$watch('volume', () => {
                this.saveQueueState();
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

            console.log('✅ Auto-save enabled (state saved on every change)');
        },

        /**
         * 🔐 SESSION POLLING: Start polling for session validity (device limit check)
         * Polls /api/auth/check-session every 30 seconds
         */
        startSessionPolling() {
            // Clear any existing interval
            if (this.sessionPollInterval) {
                clearInterval(this.sessionPollInterval);
            }

            // 🔧 LOGIN SONRASI: Session DB'ye kaydedilmesi için 2 saniye bekle
            // Race condition önleme: Backend registerSession() işlemi tamamlansın
            setTimeout(() => {
                this.checkSessionValidity();
            }, 2000);

            // Poll every 30 seconds
            this.sessionPollInterval = setInterval(() => {
                this.checkSessionValidity();
            }, 30000); // 30 seconds

            console.log('🔐 Session polling started (30s interval, initial check in 2s)');
        },

        /**
         * 🔐 CHECK SESSION: Verify session is still valid
         * Backend checks if session exists in DB (device limit enforcement)
         */
        async checkSessionValidity() {
            try {
                const response = await fetch('/api/auth/check-session', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });

                const data = await response.json();

                // Session invalid - user was logged out
                if (!data.valid) {
                    console.warn('⚠️ Session invalid:', data.reason);

                    // Stop polling
                    if (this.sessionPollInterval) {
                        clearInterval(this.sessionPollInterval);
                        this.sessionPollInterval = null;
                    }

                    // Handle based on reason
                    if (data.reason === 'device_limit_exceeded') {
                        // 🚨 DEVICE LIMIT EXCEEDED: Başka cihazdan giriş yapıldı
                        // Session DB'den silinmiş - modal göster ve bilgilendir
                        console.log('🚨 Device limit exceeded - session was terminated from another device');
                        this.handleDeviceLimitExceeded();
                    } else {
                        // Silent logout (session expired or not authenticated)
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
         * 🔐 DEVICE LIMIT EXCEEDED: Force logout immediately
         * Başka cihazdan giriş yapıldı - direkt çıkış yap
         */
        handleDeviceLimitExceeded() {
            console.log('🔐 Device limit exceeded - forcing logout');

            // Stop playback immediately
            this.pause();

            // Clear player state
            this.clearState();

            // Force logout FIRST
            this.forceLogout();

            // THEN show modal after page reload (localStorage flag)
            localStorage.setItem('device_limit_warning', 'true');
        },

        /**
         * 🔐 SILENT LOGOUT: Logout without modal (session expired)
         */
        handleSilentLogout() {
            console.log('🔐 Session expired - silent logout');
            this.forceLogout();
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
         */
        async fetchActiveDevices() {
            try {
                const response = await fetch('/api/auth/me', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error('Failed to fetch active devices');
                }

                const data = await response.json();

                // Update device limit from API (dynamic from subscription/settings)
                if (data.authenticated && data.user) {
                    this.deviceLimit = data.user.device_limit || 1;
                    console.log('🔐 Device limit updated from API:', this.deviceLimit);
                }

                // Note: /api/auth/me doesn't return active_devices yet
                // We need to modify it or create a new endpoint
                // For now, use empty array
                this.activeDevices = data.active_devices || [];

                console.log('🔐 Active devices fetched:', this.activeDevices.length);
            } catch (error) {
                console.error('Failed to fetch active devices:', error);
                this.activeDevices = [];
            }
        },

        /**
         * 🔐 TERMINATE SELECTED DEVICES: Terminate multiple device sessions (checkbox seçimleri)
         */
        async terminateSelectedDevices() {
            if (this.selectedDeviceIds.length === 0) {
                alert('Lütfen en az bir cihaz seçin');
                return;
            }

            this.deviceTerminateLoading = true;

            try {
                // Her seçili cihaz için terminate isteği gönder
                const promises = this.selectedDeviceIds.map(sessionId => {
                    return fetch('/api/auth/terminate-device', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ session_id: sessionId })
                    }).then(res => res.json());
                });

                const results = await Promise.all(promises);
                const allSuccess = results.every(data => data.success);

                if (allSuccess) {
                    console.log('✅ Selected devices terminated successfully');
                    this.showToast(`${this.selectedDeviceIds.length} cihaz çıkış yaptırıldı`, 'success');

                    // Close modals and refresh
                    this.showDeviceSelectionModal = false;
                    this.showDeviceLimitWarning = false;
                    this.selectedDeviceIds = [];

                    // Refresh device list or reload page
                    window.location.reload();
                } else {
                    alert('Bazı cihazlar çıkış yaptırılamadı');
                }
            } catch (error) {
                console.error('Device termination failed:', error);
                alert('Bir hata oluştu, lütfen tekrar deneyin');
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
                alert('Çıkarılacak başka cihaz yok');
                return;
            }

            if (!confirm(`${otherDevices.length} cihazın tümünü çıkarmak istediğinize emin misiniz?`)) {
                return;
            }

            this.deviceTerminateLoading = true;

            try {
                // Tüm diğer cihazlar için terminate isteği gönder
                const promises = otherDevices.map(device => {
                    return fetch('/api/auth/terminate-device', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ session_id: device.session_id })
                    }).then(res => res.json());
                });

                const results = await Promise.all(promises);
                const allSuccess = results.every(data => data.success);

                if (allSuccess) {
                    console.log('✅ All other devices terminated successfully');
                    this.showToast(`${otherDevices.length} cihaz çıkış yaptırıldı`, 'success');

                    // Close modals and refresh
                    this.showDeviceSelectionModal = false;
                    this.showDeviceLimitWarning = false;
                    this.selectedDeviceIds = [];

                    // Refresh device list or reload page
                    window.location.reload();
                } else {
                    alert('Bazı cihazlar çıkış yaptırılamadı');
                }
            } catch (error) {
                console.error('Device termination failed:', error);
                alert('Bir hata oluştu, lütfen tekrar deneyin');
            } finally {
                this.deviceTerminateLoading = false;
            }
        },

        /**
         * 🔐 LOGOUT FROM THIS DEVICE: User chooses to logout from current device
         */
        logoutFromThisDevice() {
            console.log('🔐 User chose to logout from this device');
            this.showDeviceLimitWarning = false;
            this.forceLogout();
        },

        /**
         * 🔐 SHOW DEVICE SELECTION: User chooses to terminate another device
         */
        showDeviceSelection() {
            console.log('🔐 User chose to select which device to terminate');
            this.showDeviceLimitWarning = false;
            this.showDeviceSelectionModal = true;
        }
    }
}

// ✅ Make muzibuApp globally accessible for Alpine.js
window.muzibuApp = muzibuApp;

// Play Limits Component (Guest & Member daily limits)
function playLimits() {
    return {
        showGuestModal: false,

        init() {
            // Listen for play limit events from muzibuApp
            window.addEventListener('player:guest-limit', () => {
                this.showGuestModal = true;
            });
        },

        handleGuestRegister() {
            this.showGuestModal = false;
            window.location.href = '/register';
        },

        handleGuestLogin() {
            this.showGuestModal = false;
            window.location.href = '/login';
        }
    }
}

// ✅ Make playLimits globally accessible for Alpine.js
window.playLimits = playLimits;
window.playLimits = playLimits;

// Cache bust: 1765140096
// Cache bust: 1765142226
