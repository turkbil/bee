// 🔒 Safe Storage Wrapper - Prevents "Access to storage is not allowed" errors
const safeStorage = {
    getItem(key) {
        try {
            return localStorage.getItem(key);
        } catch (e) {
            console.warn('localStorage access denied:', e.message);
            return null;
        }
    },
    setItem(key, value) {
        try {
            localStorage.setItem(key, value);
        } catch (e) {
            console.warn('localStorage access denied:', e.message);
        }
    },
    removeItem(key) {
        try {
            localStorage.removeItem(key);
        } catch (e) {
            console.warn('localStorage access denied:', e.message);
        }
    }
};

function muzibuApp() {
    // Get config from window object (set in blade template)
    const config = window.muzibuPlayerConfig || {};

    return {
        // 🎯 Modular features (spread from separate files)
        ...muzibuFavorites(),
        ...muzibuAuth(),

        // Tenant-specific translations
        lang: config.lang || {},
        frontLang: config.frontLang || {},

        isLoggedIn: config.isLoggedIn || false,
        currentUser: config.currentUser || null,
        todayPlayedCount: config.todayPlayedCount || 0,
        showAuthModal: null,
        showQueue: false,
        progressPercent: 0,
        authLoading: false,
        authError: '',
        authSuccess: '',
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
        queue: [],
        queueIndex: 0,
        isLoggingOut: false,
        currentPath: window.location.pathname,
        _initialized: false,
        isDarkMode: safeStorage.getItem('theme') === 'light' ? false : true,
        draggedIndex: null,
        dropTargetIndex: null,
        previewTimer: null, // 🎵 Guest preview timer
        fadeOutTimer: null, // 🎵 Guest preview fade-out timer
        isPreviewBlocked: false, // 🎵 Preview ended - next/previous disabled

        // Crossfade settings (using Howler.js + HLS.js)
        crossfadeEnabled: true,
        crossfadeDuration: 6000, // 6 seconds for automatic song transitions
        fadeOutDuration: 1000, // 1 second for pause/play/manual change fade
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

        // Get the currently active HLS audio element
        getActiveHlsAudio() {
            if (this.activeHlsAudioId === 'hlsAudioNext') {
                return document.getElementById('hlsAudioNext');
            }
            return this.$refs.hlsAudio;
        },

        init() {
            // Prevent double initialization
            if (this._initialized) {
                console.log('Muzibu already initialized, skipping...');
                return;
            }
            this._initialized = true;

            console.log('Muzibu initialized with Howler.js');

            // User already loaded from Laravel backend (no need for API check)
            console.log('Muzibu initialized', { isLoggedIn: this.isLoggedIn, user: this.currentUser });

            // Load featured playlists on init
            this.loadFeaturedPlaylists();

            // Show content after loading (KRITIK - Alpine.js x-show için)
            setTimeout(() => {
                this.isLoading = false;
                this.contentLoaded = true;
            }, 500);

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

                // Skip if no href, hash link, external link, or has download/target attribute
                if (!href ||
                    href.startsWith('#') ||
                    href.startsWith('http') ||
                    href.startsWith('//') ||
                    link.hasAttribute('download') ||
                    link.hasAttribute('target')) {
                    return;
                }

                // Internal link - use SPA navigation
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

        // 🎯 Favorites functions (toggleFavorite, isFavorite, isLiked) moved to features/favorites.js

        async togglePlayPause() {
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
                }
            }
        },

        async playRandomSongs() {
            try {
                this.isLoading = true;

                // Popüler şarkılardan rastgele 50 şarkı al
                const response = await fetch('/api/muzibu/songs/popular?limit=50');
                const songs = await response.json();

                if (songs.length > 0) {
                    // Shuffle songs
                    const shuffled = songs.sort(() => Math.random() - 0.5);

                    this.queue = shuffled;
                    this.queueIndex = 0;
                    await this.playSongFromQueue(0);
                    this.showToast('Rastgele çalma başladı!', 'success');
                } else {
                    this.showToast('Şarkı bulunamadı', 'error');
                }
            } catch (error) {
                console.error('Failed to play random songs:', error);
                this.showToast('Şarkılar yüklenemedi', 'error');
            } finally {
                this.isLoading = false;
            }
        },

        async previousTrack() {
            // 🔒 Preview blocked - next/previous disabled
            if (this.isPreviewBlocked) {
                this.showToast('Premium\'a geçin, sınırsız dinleyin!', 'warning');
                return;
            }

            if (this.queueIndex > 0) {
                this.queueIndex--;
                await this.playSongFromQueue(this.queueIndex);
            }
        },

        async nextTrack() {
            // 🔒 Preview blocked - next/previous disabled
            if (this.isPreviewBlocked) {
                this.showToast('Premium\'a geçin, sınırsız dinleyin!', 'warning');
                return;
            }

            if (this.queueIndex < this.queue.length - 1) {
                this.queueIndex++;
                await this.playSongFromQueue(this.queueIndex);
            } else if (this.repeatMode === 'all') {
                this.queueIndex = 0;
                await this.playSongFromQueue(this.queueIndex);
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

                    // Eğer unauthorized ise login modali göster (sessizce)
                    if (response.status === 401) {
                        this.showAuthModal = 'login';
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

            // Get next song URL and type
            try {
                const response = await fetch(`/api/muzibu/songs/${nextSong.song_id}/stream`);
                const data = await response.json();

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

            // Reset crossfade state
            this.isCrossfading = false;

            console.log('Crossfade complete, now playing:', this.currentSong?.song_title?.tr);
        },

        seekTo(e) {
            const bar = e.currentTarget;
            const rect = bar.getBoundingClientRect();
            const percent = (e.clientX - rect.left) / rect.width;
            const newTime = this.duration * percent;

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
            this.progressPercent = percent * 100;
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
                this.isLoading = true;
                const response = await fetch(`/api/muzibu/albums/${id}`);
                const album = await response.json();

                if (album.songs && album.songs.length > 0) {
                    this.queue = album.songs;
                    this.queueIndex = 0;
                    await this.playSongFromQueue(0);
                    this.showToast(`${album.album_title.tr} çalınıyor`, 'success');
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
                this.isLoading = true;
                const response = await fetch(`/api/muzibu/playlists/${id}`);
                const playlist = await response.json();

                if (playlist.songs && playlist.songs.length > 0) {
                    this.queue = playlist.songs;
                    this.queueIndex = 0;
                    await this.playSongFromQueue(0);
                    this.showToast(`${playlist.title.tr} çalınıyor`, 'success');
                }
            } catch (error) {
                console.error('Failed to play playlist:', error);
                this.showToast('Playlist yüklenemedi', 'error');
            } finally {
                this.isLoading = false;
            }
        },

        async playSong(id) {
            try {
                this.isLoading = true;

                // Get song details first
                const songResponse = await fetch(`/api/muzibu/songs/popular?limit=100`);
                const songs = await songResponse.json();

                // Find the specific song by ID
                const song = songs.find(s => s.song_id == id);

                if (song) {
                    // Create queue with just this song
                    this.queue = [song];
                    this.queueIndex = 0;
                    this.currentSong = song;

                    // Get stream URL
                    const streamResponse = await fetch(`/api/muzibu/songs/${id}/stream`);

                    // ❌ HTTP Error Check
                    if (!streamResponse.ok) {
                        const errorData = await streamResponse.json().catch(() => ({}));

                        // 404: Şarkı bulunamadı
                        if (streamResponse.status === 404) {
                            this.showToast('Şarkı bulunamadı', 'error');
                            return;
                        }

                        // 500: Sunucu hatası
                        if (streamResponse.status >= 500) {
                            this.showToast('Sunucu hatası', 'error');
                            return;
                        }

                        // Diğer hatalar
                        this.showToast(errorData.message || 'Bir hata oluştu', 'error');
                        return;
                    }

                    const streamData = await streamResponse.json();

                    // 🔍 DEBUG: Backend response'u logla
                    console.log('🎵 Stream API Response:', {
                        status: streamData.status,
                        preview_duration: streamData.preview_duration,
                        is_premium: streamData.is_premium,
                        message: streamData.message
                    });

                    // ⚠️ 3/3 KURAL DEVRE DIŞI - limit_exceeded artık kullanılmıyor
                    // Normal/Guest üyeler 30 saniye preview alacak (status: 'preview')
                    // Premium üyeler full şarkı alacak (status: 'ready')

                    // if (streamData.status === 'limit_exceeded') {
                    //     const playLimitsComponent = Alpine.$data(document.querySelector('[x-data*="playLimits"]'));
                    //     if (playLimitsComponent) {
                    //         playLimitsComponent.showLimitModal = true;
                    //     }
                    //     this.showToast('Günlük limit doldu', 'error');
                    //     return;
                    // }

                    // 🔄 Her şarkı çalmada premium status güncelle
                    if (streamData.is_premium !== undefined && this.currentUser) {
                        this.currentUser.is_premium = streamData.is_premium;
                    }

                    // 🎵 Guest preview: Pass preview_duration to enforce 30-second limit
                    await this.loadAndPlaySong(
                        streamData.stream_url,
                        streamData.stream_type,
                        streamData.preview_duration || null
                    );
                    this.showToast('Şarkı çalınıyor', 'success');
                } else {
                    this.showToast('Şarkı bulunamadı', 'error');
                }
            } catch (error) {
                console.error('Failed to play song:', error);
                this.showToast('Şarkı yüklenemedi', 'error');
            } finally {
                this.isLoading = false;
            }
        },

        async playSongFromQueue(index) {
            if (index < 0 || index >= this.queue.length) return;

            const song = this.queue[index];
            this.currentSong = song;
            this.queueIndex = index;

            // Check if song is favorited
            this.checkFavoriteStatus(song.song_id);

            try {
                const response = await fetch(`/api/muzibu/songs/${song.song_id}/stream`);

                // ❌ FIX: Generic error handling - response.ok sadece network hatalarını kontrol eder
                // Backend'den gelen status'ü JSON'dan okuyacağız
                if (!response.ok) {
                    // Generic error message (network, auth, etc.)
                    this.showToast('Şarkı yüklenemedi, lütfen tekrar deneyin', 'error');
                    return;
                }

                const data = await response.json();

                // 🔍 DEBUG: Queue song API response
                console.log('🎵 Queue Song API Response:', {
                    status: data.status,
                    preview_duration: data.preview_duration,
                    is_premium: data.is_premium
                });

                // ⚠️ 3/3 KURAL DEVRE DIŞI
                // if (data.status === 'limit_exceeded') {
                //     const playLimitsComponent = Alpine.$data(document.querySelector('[x-data*="playLimits"]'));
                //     if (playLimitsComponent) {
                //         playLimitsComponent.showLimitModal = true;
                //     }
                //     this.showToast('Günlük limit doldu', 'error');
                //     return;
                // }

                // 🔄 Her şarkı çalmada premium status güncelle
                if (data.is_premium !== undefined && this.currentUser) {
                    this.currentUser.is_premium = data.is_premium;
                }

                // Pass stream type and preview duration from API response
                const streamType = data.stream_type || 'mp3';
                const previewDuration = data.preview_duration || null; // 🎵 Guest preview
                console.log('Playing song:', data.stream_url, 'Type:', streamType, 'Preview:', previewDuration);
                await this.loadAndPlaySong(data.stream_url, streamType, previewDuration);

                // Prefetch HLS for next songs in queue (background)
                this.prefetchHlsForQueue(index);
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

        async loadAndPlaySong(url, streamType = null, previewDuration = null) {
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

            console.log('loadAndPlaySong:', { url, streamType, useHls });

            if (useHls) {
                this.isHlsStream = true;
                await this.playHlsStream(url, targetVolume);
            } else {
                this.isHlsStream = false;
                await this.playWithHowler(url, targetVolume);
            }

            // 🎵 GUEST PREVIEW: Setup preview duration limits
            if (previewDuration && previewDuration > 0) {
                // Wait a bit for audio to load and get duration
                setTimeout(() => {
                    const duration = this.duration || 180; // Fallback to 3 minutes
                    const introSkipSeconds = duration * 0.20; // Skip first 20% (intro)
                    const fadeStartSeconds = previewDuration - 3; // Start fade-out 3 seconds before end

                    console.log('🎵 Guest Preview Config:', {
                        totalDuration: duration,
                        previewDuration: previewDuration,
                        introSkipAt: introSkipSeconds.toFixed(1) + 's',
                        fadeStartAt: fadeStartSeconds + 's',
                        stopAt: previewDuration + 's'
                    });

                    // INTRO SKIP: Jump to 20% to skip intro
                    if (this.howl && this.howl.playing()) {
                        this.howl.seek(introSkipSeconds);
                        console.log(`🎵 Intro skipped: Jumped to ${introSkipSeconds.toFixed(1)}s (20% of song)`);
                    } else if (this.hls) {
                        const audio = this.getActiveHlsAudio();
                        if (audio && !audio.paused) {
                            audio.currentTime = introSkipSeconds;
                            console.log(`🎵 Intro skipped: Jumped to ${introSkipSeconds.toFixed(1)}s (20% of song)`);
                        }
                    }

                    // FADE-OUT: Start fade 3 seconds before end (27th second for 30s preview)
                    this.fadeOutTimer = setTimeout(() => {
                        console.log('🎵 Guest preview: Fade-out başladı (son 3 saniye)');
                        const targetVolume = this.isMuted ? 0 : this.volume / 100;

                        if (this.howl && this.howl.playing()) {
                            this.howl.fade(targetVolume, 0, 3000); // 3 second fade-out
                        } else if (this.hls) {
                            const audio = this.getActiveHlsAudio();
                            if (audio && !audio.paused) {
                                this.fadeAudioElement(audio, audio.volume, 0, 3000); // 3 second fade-out
                            }
                        }
                    }, fadeStartSeconds * 1000);

                    // STOP: Stop playback at preview duration end
                    this.previewTimer = setTimeout(() => {
                        console.log('🛑 Guest preview ended - stopping playback');
                        this.pause();

                        // 🔒 Block next/previous buttons
                        this.isPreviewBlocked = true;

                        // Show guest modal
                        const playLimitsComponent = Alpine.$data(document.querySelector('[x-data*="playLimits"]'));
                        if (playLimitsComponent) {
                            playLimitsComponent.showGuestModal = true;
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
                    await new Promise(resolve => {
                        const currentVolume = this.howl.volume();
                        this.howl.fade(currentVolume, 0, this.fadeOutDuration);
                        this.howl.once('fade', () => {
                            this.howl.stop();
                            this.howl.unload();
                            this.howl = null;
                            resolve();
                        });
                    });
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
                    await this.fadeAudioElement(audio, audio.volume, 0, this.fadeOutDuration);
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
        async playWithHowler(url, targetVolume) {
            const self = this;

            // Determine format from URL or default to mp3
            let format = ['mp3'];
            if (url.includes('.ogg')) format = ['ogg'];
            else if (url.includes('.wav')) format = ['wav'];
            else if (url.includes('.webm')) format = ['webm'];

            this.howl = new Howl({
                src: [url],
                format: format,
                html5: true,
                volume: 0,
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

            this.howl.play();
            this.howl.fade(0, targetVolume, this.fadeOutDuration);
            this.isPlaying = true;
        },

        // Play using HLS.js (for HLS streams)
        async playHlsStream(url, targetVolume) {
            const self = this;
            const audio = this.$refs.hlsAudio;

            if (!audio) {
                console.error('HLS audio element not found');
                return;
            }

            // Check HLS.js support
            if (Hls.isSupported()) {
                this.hls = new Hls({
                    enableWorker: true,
                    lowLatencyMode: false
                });

                this.hls.loadSource(url);
                this.hls.attachMedia(audio);

                this.hls.on(Hls.Events.MANIFEST_PARSED, function() {
                    audio.volume = 0;
                    audio.play().then(() => {
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
                        console.error('HLS play error:', e);
                        self.showToast('Çalma hatası', 'error');
                    });
                });

                this.hls.on(Hls.Events.ERROR, function(event, data) {
                    if (data.fatal) {
                        console.error('HLS fatal error:', data);

                        // HLS yüklenemezse MP3'e fallback
                        if (data.type === Hls.ErrorTypes.NETWORK_ERROR && self.currentSong) {
                            console.log('🔄 HLS failed, falling back to MP3...');
                            const mp3Url = `/api/muzibu/songs/${self.currentSong.song_id}/serve`;

                            // Cleanup HLS
                            if (self.hls) {
                                self.hls.destroy();
                                self.hls = null;
                            }

                            // Queue'ye MP3 conversion job ekle (background)
                            self.showToast('MP3 ile çalıyor, HLS hazırlanıyor...', 'info');

                            // MP3 ile çal
                            self.playWithHowler(mp3Url, targetVolume);
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

                    // 🎵 Müzik kesilmeden başarı mesajı göster
                    this.showToast('Hoş geldin, ' + data.user.name + '! 🎉', 'success');

                    // UI'ı SPA mantığıyla güncelle (Alpine.js reaktif olduğu için otomatik re-render)
                    console.log('✅ Login successful - SPA mode, no page reload!');
                    console.log('👤 User logged in:', {
                        name: data.user.name,
                        email: data.user.email,
                        is_premium: data.user.is_premium || false,
                        isLoggedIn: this.isLoggedIn
                    });
                } else {
                    this.authError = data.message || 'E-posta veya şifre hatalı';
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

                    // 🎵 Müzik kesilmeden başarı mesajı göster
                    this.showToast('Hoş geldin, ' + data.user.name + '! 🎉 Premium denemen başladı.', 'success');

                    // UI'ı SPA mantığıyla güncelle (Alpine.js reaktif olduğu için otomatik re-render)
                    console.log('✅ Register successful - SPA mode, no page reload!');
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
                    // 3 saniye sonra login modalına dön
                    setTimeout(() => {
                        this.authSuccess = '';
                        this.showAuthModal = 'login';
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

        toggleTheme() {
            this.isDarkMode = !this.isDarkMode;
            safeStorage.setItem('theme', this.isDarkMode ? 'dark' : 'light');
            this.showToast(this.isDarkMode ? 'Koyu tema aktif' : 'Açık tema aktif', 'success');
        },

        dragStart(index, event) {
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
        }
    }
}

// ✅ Make muzibuApp globally accessible for Alpine.js
window.muzibuApp = muzibuApp;

// Play Limits Component (Guest & Member daily limits)
function playLimits() {
    return {
        showGuestModal: false,
        // showLimitModal: false, // ⚠️ REMOVED - 3/3 daily limit modal removed

        init() {
            // Listen for play limit events from muzibuApp
            window.addEventListener('player:guest-limit', () => {
                this.showGuestModal = true;
            });

            // ⚠️ REMOVED - 3/3 daily limit event listener removed
            // window.addEventListener('player:daily-limit', () => {
            //     this.showLimitModal = true;
            // });
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
