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
        // Tenant-specific translations
        lang: config.lang || {},
        frontLang: config.frontLang || {},

        isLoggedIn: config.isLoggedIn || false,
        currentUser: config.currentUser || null,
        showAuthModal: null,
        showQueue: false,
        progressPercent: 0,
        loginForm: {
            email: safeStorage.getItem('remembered_email') || '',
            password: '',
            remember: safeStorage.getItem('remembered_email') ? true : false
        },
        registerForm: { firstName: '', lastName: '', name: '', email: '', password: '', phone: '' },
        forgotForm: { email: '' },
        showPassword: false,
        showLoginPassword: false,
        tenantId: config.tenantId || 2,
        registerValidation: {
            name: { valid: false, message: '' },
            email: { valid: false, message: '' },
            phone: { valid: false, message: '' },
            password: {
                valid: false,
                strength: 0,
                strengthText: '',
                checks: { length: false, uppercase: false, lowercase: false, number: false }
            }
        },
        loginValidation: {
            email: { valid: false, message: '' },
            password: { valid: false, message: '' }
        },
        forgotValidation: {
            email: { valid: false, message: '' }
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
        isPlaying: false,
        isLiked: false,
        shuffle: false,
        repeatMode: 'off',
        currentTime: 0,
        duration: 240,
        volume: 70,
        isMuted: false,
        currentSong: null,
        queue: [],
        queueIndex: 0,
        isLoading: false,
        isLoggingOut: false,
        currentPath: window.location.pathname,
        _initialized: false,
        isDarkMode: safeStorage.getItem('theme') === 'light' ? false : true,
        draggedIndex: null,
        dropTargetIndex: null,

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

        toggleFavorite(type, id) {
            const key = `${type}-${id}`;
            if (this.favorites.includes(key)) {
                this.favorites = this.favorites.filter(f => f !== key);
                this.showToast('Favorilerden kaldırıldı', 'info');
            } else {
                this.favorites.push(key);
                this.showToast('Favorilere eklendi', 'success');
            }
        },

        isFavorite(type, id) {
            return this.favorites.includes(`${type}-${id}`);
        },

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
            if (this.queueIndex > 0) {
                this.queueIndex--;
                await this.playSongFromQueue(this.queueIndex);
            }
        },

        async nextTrack() {
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

        cycleRepeat() {
            const modes = ['off', 'all', 'one'];
            const idx = modes.indexOf(this.repeatMode);
            this.repeatMode = modes[(idx + 1) % modes.length];
        },

        async toggleLike() {
            if (!this.currentSong) return;

            const songId = this.currentSong.song_id;
            const previousState = this.isLiked;

            // Optimistic UI update
            this.isLiked = !this.isLiked;

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
                    this.isLiked = previousState;

                    // Eğer unauthorized ise login modali göster
                    if (response.status === 401) {
                        this.showAuthModal = 'login';
                    }
                }
            } catch (error) {
                console.error('Favorite toggle error:', error);
                // Hata durumunda eski haline döndür
                this.isLiked = previousState;
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

                    // 🔐 LIMIT CHECK: Üye limit aştıysa çalma!
                    if (streamData.status === 'limit_exceeded') {
                        // Play limits component'ine bildir
                        const playLimitsComponent = Alpine.$data(document.querySelector('[x-data*="playLimits"]'));
                        if (playLimitsComponent) {
                            playLimitsComponent.limitExceeded = true;
                            playLimitsComponent.showLimitModal = true;
                            playLimitsComponent.remainingPlays = 0;
                        }

                        this.showToast('Günlük limit doldu', 'error');
                        return; // Şarkıyı çalma!
                    }

                    await this.loadAndPlaySong(streamData.stream_url);
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

                // 🔐 403/401 Check: Backend auth/limit hatası
                if (!response.ok) {
                    // Play limits component'ine bildir (modal aç!)
                    const playLimitsComponent = Alpine.$data(document.querySelector('[x-data*="playLimits"]'));
                    if (playLimitsComponent) {
                        playLimitsComponent.limitExceeded = true;
                        playLimitsComponent.showLimitModal = true;
                        playLimitsComponent.remainingPlays = 0;
                    }

                    this.showToast('Günlük limit doldu', 'error');
                    return; // Şarkıyı çalma!
                }

                const data = await response.json();

                // 🔐 LIMIT CHECK: Üye limit aştıysa çalma!
                if (data.status === 'limit_exceeded') {
                    // Play limits component'ine bildir
                    const playLimitsComponent = Alpine.$data(document.querySelector('[x-data*="playLimits"]'));
                    if (playLimitsComponent) {
                        playLimitsComponent.limitExceeded = true;
                        playLimitsComponent.showLimitModal = true;
                        playLimitsComponent.remainingPlays = 0;
                    }

                    this.showToast('Günlük limit doldu', 'error');
                    return; // Şarkıyı çalma!
                }

                // Pass stream type from API response ('hls' or 'mp3')
                const streamType = data.stream_type || 'mp3';
                console.log('Playing song:', data.stream_url, 'Type:', streamType);
                await this.loadAndPlaySong(data.stream_url, streamType);

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

        async loadAndPlaySong(url, streamType = null) {
            const self = this;
            const targetVolume = this.isMuted ? 0 : this.volume / 100;

            // Stop and fade out current playback
            await this.stopCurrentPlayback();

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

        addToQueue(type, id) {
            console.log('Adding to queue:', type, id);
            this.showToast('Kuyruğa eklendi', 'success');
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

        showToast(message, type = 'info') {
            console.log(`Toast [${type}]:`, message);
        },

        // checkAuth() removed - user data now loaded directly from Laravel backend on page load

        async handleLogin() {
            try {
                this.isLoading = true;
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
                    // Beni Hatırla - email'i kaydet veya sil
                    if (this.loginForm.remember) {
                        safeStorage.setItem('remembered_email', this.loginForm.email);
                    } else {
                        safeStorage.removeItem('remembered_email');
                    }

                    this.isLoggedIn = true;
                    this.currentUser = data.user;
                    this.showAuthModal = null;
                    this.showToast('Başarıyla giriş yapıldı!', 'success');
                    location.reload();
                } else {
                    this.showToast(data.message || 'Giriş başarısız', 'error');
                }
            } catch (error) {
                console.error('Login error:', error);
                this.showToast('Giriş hatası', 'error');
            } finally {
                this.isLoading = false;
            }
        },

        async handleRegister() {
            try {
                this.isLoading = true;
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
                    this.isLoggedIn = true;
                    this.currentUser = data.user;
                    this.showAuthModal = null;
                    this.registerForm = { firstName: '', lastName: '', name: '', email: '', password: '', phone: '' };
                    this.showToast('Hesabınız oluşturuldu! 7 günlük deneme başladı.', 'success');
                    location.reload(); // Reload to update sidebar
                } else {
                    this.showToast(data.message || 'Kayıt başarısız', 'error');
                }
            } catch (error) {
                console.error('Register error:', error);
                this.showToast('Kayıt hatası', 'error');
            } finally {
                this.isLoading = false;
            }
        },

        async handleForgotPassword() {
            try {
                this.isLoading = true;
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
                    this.showToast('Şifre sıfırlama linki e-postanıza gönderildi!', 'success');
                    this.forgotForm = { email: '' };
                    // 3 saniye sonra login modalına dön
                    setTimeout(() => {
                        this.showAuthModal = 'login';
                    }, 3000);
                } else {
                    this.showToast(data.message || 'E-posta gönderilemedi', 'error');
                }
            } catch (error) {
                console.error('Forgot password error:', error);
                this.showToast('Bir hata oluştu', 'error');
            } finally {
                this.isLoading = false;
            }
        },

        async logout() {
            // Çift tıklamayı engelle
            if (this.isLoggingOut) return;

            // Hemen UI'ı güncelle
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

                // Kısa bekle ve sayfayı yenile (cache'siz)
                setTimeout(() => {
                    window.location.reload(true);
                }, 100);
            } catch (error) {
                console.error('Logout error:', error);
                window.location.reload(true);
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

        validateName() {
            const firstName = this.registerForm.firstName.trim();
            const lastName = this.registerForm.lastName.trim();

            // Birleşik name'i güncelle (API için)
            this.registerForm.name = (firstName + ' ' + lastName).trim();

            // Validation
            if (firstName.length >= 2 && lastName.length >= 2 &&
                /^[a-zA-ZğüşöçıİĞÜŞÖÇ]+$/.test(firstName) &&
                /^[a-zA-ZğüşöçıİĞÜŞÖÇ]+$/.test(lastName)) {
                this.registerValidation.name.valid = true;
                this.registerValidation.name.message = '';
            } else {
                this.registerValidation.name.valid = false;
                this.registerValidation.name.message = 'Ad ve soyad en az 2 karakter olmalıdır';
            }
        },

        async validateEmail() {
            const email = this.registerForm.email.trim().toLowerCase();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!emailRegex.test(email)) {
                this.registerValidation.email.valid = false;
                this.registerValidation.email.message = 'Geçerli bir e-posta adresi giriniz';
                return;
            }

            if (email.includes('..')) {
                this.registerValidation.email.valid = false;
                this.registerValidation.email.message = 'E-posta adresinde ardışık nokta olamaz';
                return;
            }

            if (email.startsWith('.') || email.endsWith('.')) {
                this.registerValidation.email.valid = false;
                this.registerValidation.email.message = 'E-posta adresi nokta ile başlayamaz veya bitemez';
                return;
            }

            // Format valid, check availability via API
            try {
                const response = await fetch('/api/auth/check-email', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    },
                    body: JSON.stringify({ email })
                });

                const data = await response.json();

                if (data.exists) {
                    this.registerValidation.email.valid = false;
                    this.registerValidation.email.message = 'Bu e-posta adresi zaten kullanılıyor';
                } else {
                    this.registerValidation.email.valid = true;
                    this.registerValidation.email.message = '';
                }
            } catch (error) {
                console.error('Email check failed:', error);
                // Format is valid, just can't check availability
                this.registerValidation.email.valid = true;
                this.registerValidation.email.message = '';
            }
        },

        selectCountry(country) {
            this.phoneCountry = country;
            this.registerForm.phone = '';
            this.registerValidation.phone.valid = false;
        },

        formatPhoneNumber() {
            let phone = this.registerForm.phone.replace(/\D/g, '');

            // Turkey specific formatting
            if (this.phoneCountry.code === '+90') {
                if (phone.length > 0) {
                    if (phone.length <= 3) {
                        this.registerForm.phone = phone;
                    } else if (phone.length <= 6) {
                        this.registerForm.phone = phone.substring(0, 3) + ' ' + phone.substring(3);
                    } else if (phone.length <= 8) {
                        this.registerForm.phone = phone.substring(0, 3) + ' ' + phone.substring(3, 6) + ' ' + phone.substring(6);
                    } else {
                        this.registerForm.phone = phone.substring(0, 3) + ' ' + phone.substring(3, 6) + ' ' + phone.substring(6, 8) + ' ' + phone.substring(8, 10);
                        phone = phone.substring(0, 10);
                    }
                }

                // Validate Turkey phone
                if (phone.length === 0) {
                    this.registerValidation.phone.valid = false;
                    this.registerValidation.phone.message = 'Telefon numarası gereklidir';
                } else if (!phone.startsWith('5')) {
                    this.registerValidation.phone.valid = false;
                    this.registerValidation.phone.message = 'Cep telefonu 5 ile başlamalıdır';
                } else if (phone.length !== 10) {
                    this.registerValidation.phone.valid = false;
                    this.registerValidation.phone.message = 'Telefon numarası 10 haneli olmalıdır';
                } else if (!['50', '51', '52', '53', '54', '55', '56', '58', '59'].includes(phone.substring(0, 2))) {
                    this.registerValidation.phone.valid = false;
                    this.registerValidation.phone.message = 'Geçersiz operatör kodu';
                } else {
                    this.registerValidation.phone.valid = true;
                    this.registerValidation.phone.message = '';
                }
            } else {
                // Generic international validation
                this.registerForm.phone = phone;

                if (phone.length === 0) {
                    this.registerValidation.phone.valid = false;
                    this.registerValidation.phone.message = 'Telefon numarası gereklidir';
                } else if (phone.length < 7) {
                    this.registerValidation.phone.valid = false;
                    this.registerValidation.phone.message = 'Telefon numarası çok kısa';
                } else if (phone.length > 15) {
                    this.registerValidation.phone.valid = false;
                    this.registerValidation.phone.message = 'Telefon numarası çok uzun';
                } else {
                    this.registerValidation.phone.valid = true;
                    this.registerValidation.phone.message = '';
                }
            }
        },

        validatePassword() {
            const password = this.registerForm.password;

            // Check individual requirements
            this.registerValidation.password.checks.length = password.length >= 8;
            this.registerValidation.password.checks.uppercase = /[A-Z]/.test(password);
            this.registerValidation.password.checks.lowercase = /[a-z]/.test(password);
            this.registerValidation.password.checks.number = /[0-9]/.test(password);

            // Calculate strength
            let strength = 0;
            if (this.registerValidation.password.checks.length) strength++;
            if (this.registerValidation.password.checks.uppercase) strength++;
            if (this.registerValidation.password.checks.lowercase) strength++;
            if (this.registerValidation.password.checks.number) strength++;
            if (password.length >= 12) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++; // Special char bonus

            // Normalize to 1-4 scale
            this.registerValidation.password.strength = Math.min(4, Math.ceil(strength / 1.5));

            // Set strength text
            const strengthTexts = {
                1: 'Çok Zayıf',
                2: 'Zayıf',
                3: 'Orta',
                4: 'Güçlü'
            };
            this.registerValidation.password.strengthText = strengthTexts[this.registerValidation.password.strength] || '';

            // Password is valid if all basic checks pass
            this.registerValidation.password.valid =
                this.registerValidation.password.checks.length &&
                this.registerValidation.password.checks.uppercase &&
                this.registerValidation.password.checks.lowercase &&
                this.registerValidation.password.checks.number;
        },

        isRegisterFormValid() {
            const basicValid = this.registerValidation.name.valid &&
                             this.registerValidation.email.valid &&
                             this.registerValidation.password.valid;

            // If tenant 1001, phone is also required
            if (this.tenantId === 1001) {
                return basicValid && this.registerValidation.phone.valid;
            }

            return basicValid;
        },

        validateLoginEmail() {
            const email = this.loginForm.email.trim().toLowerCase();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!emailRegex.test(email)) {
                this.loginValidation.email.valid = false;
                this.loginValidation.email.message = 'Geçerli bir e-posta adresi giriniz';
            } else {
                this.loginValidation.email.valid = true;
                this.loginValidation.email.message = '';
            }
        },

        validateLoginPassword() {
            const password = this.loginForm.password;

            if (password.length < 1) {
                this.loginValidation.password.valid = false;
                this.loginValidation.password.message = 'Şifre gereklidir';
            } else {
                this.loginValidation.password.valid = true;
                this.loginValidation.password.message = '';
            }
        },

        validateForgotEmail() {
            const email = this.forgotForm.email.trim().toLowerCase();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!emailRegex.test(email)) {
                this.forgotValidation.email.valid = false;
                this.forgotValidation.email.message = 'Geçerli bir e-posta adresi giriniz';
            } else {
                this.forgotValidation.email.valid = true;
                this.forgotValidation.email.message = '';
            }
        }
    }
}
