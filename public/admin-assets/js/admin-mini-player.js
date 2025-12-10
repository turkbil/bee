/**
 * Admin Mini Player - Howler.js + HLS.js Integration
 * Hızlı şarkı preview için Alpine.js component
 */

document.addEventListener('alpine:init', () => {
    Alpine.data('adminMiniPlayer', () => ({
        // State
        isPlaying: false,
        isLoading: false,
        isVisible: false,
        currentSong: null,
        progress: 0,
        duration: 0,
        currentTime: 0,
        volume: 0.8,

        // Player instances
        sound: null,           // Howler instance for MP3
        hls: null,             // HLS.js instance
        audioElement: null,    // HTML5 Audio for HLS
        progressInterval: null,

        // Fallback support
        fallbackUrl: null,     // Original MP3 URL for fallback
        hlsRetryCount: 0,      // HLS retry counter
        maxHlsRetries: 3,      // Maximum HLS retries before fallback

        // Initialize
        init() {
            console.log('🎵 [ADMIN PLAYER] Alpine component initialized');

            // Listen for play events from song list
            window.addEventListener('play-song', (e) => {
                console.log('🎵 [ADMIN PLAYER] play-song event received:', e.detail);
                this.playSong(e.detail);
            });

            // Livewire event support
            if (typeof Livewire !== 'undefined') {
                Livewire.on('play-song', (data) => {
                    this.playSong(data[0] || data);
                });
            }

        },

        // Play a song
        async playSong(songData) {
            console.log('🎵 [ADMIN PLAYER] playSong() called, songData:', songData);

            // Stop current playback
            this.stop();

            this.currentSong = songData;
            this.isLoading = true;
            this.isVisible = true;
            this.hlsRetryCount = 0;

            // Determine URLs
            let hlsUrl = null;

            // Build tenant-aware direct URL from file_path and tenant_id
            let directUrl = songData.file_url || songData.url;

            if (!directUrl && songData.file_path && songData.tenant_id) {
                directUrl = `/storage/tenant${songData.tenant_id}/muzibu/songs/${songData.file_path}`;
                console.log('🎵 [ADMIN PLAYER] Built tenant-aware URL:', directUrl);
            }

            // HLS URL oluştur - song_id bazlı (lazy conversion destekli)
            const baseUrl = window.location.origin;

            // Öncelik: song_id > hls_hash > hls_url'den çıkar
            if (songData.song_id || songData.id) {
                // Song ID varsa direkt kullan (lazy conversion destekli)
                const songId = songData.song_id || songData.id;
                hlsUrl = `${baseUrl}/stream/play/${songId}/playlist.m3u8`;
            } else if (songData.hls_hash) {
                // Hash doğrudan verilmişse (eski format)
                hlsUrl = `${baseUrl}/stream/play/${songData.hls_hash}/playlist.m3u8`;
            } else if (songData.hls_url) {
                // hls_url verilmişse, hash'i çıkar ve stream URL'e çevir
                const hashMatch = songData.hls_url.match(/hls\/([^\/]+)\//);
                if (hashMatch && hashMatch[1]) {
                    hlsUrl = `${baseUrl}/stream/play/${hashMatch[1]}/playlist.m3u8`;
                } else {
                    hlsUrl = songData.hls_url; // Fallback: original URL
                }
            } else if (songData.url && songData.url.includes('.m3u8')) {
                // Legacy URL formatı
                const hashMatch = songData.url.match(/hls\/([^\/]+)\//);
                if (hashMatch && hashMatch[1]) {
                    hlsUrl = `${baseUrl}/stream/play/${hashMatch[1]}/playlist.m3u8`;
                } else {
                    hlsUrl = songData.url;
                }
            }

            // Store fallback URL
            this.fallbackUrl = directUrl;

            console.log('🎵 [ADMIN PLAYER] URLs determined:', {
                hlsUrl: hlsUrl,
                directUrl: directUrl,
                is_hls: songData.is_hls
            });

            if (!hlsUrl && !directUrl) {
                console.error('❌ [ADMIN PLAYER] No audio URL provided');
                this.isLoading = false;
                return;
            }

            // Try HLS first if available
            if (hlsUrl && songData.is_hls !== false) {
                console.log('🎵 [ADMIN PLAYER] Attempting HLS playback:', hlsUrl);
                await this.playHLS(hlsUrl);
            } else if (directUrl) {
                console.log('🎵 [ADMIN PLAYER] Attempting direct playback:', directUrl);
                this.playDirect(directUrl);
            }
        },

        // Play HLS stream with encryption support
        async playHLS(url) {
            console.log('🎵 [ADMIN PLAYER] playHLS() called with:', url);

            if (!Hls.isSupported()) {
                console.log('⚠️ [ADMIN PLAYER] HLS not supported, falling back to direct');
                // Fallback for Safari (native HLS support)
                this.playDirect(url);
                return;
            }

            console.log('✅ [ADMIN PLAYER] HLS is supported, creating player');

            // Create video element for HLS (Audio element causes chunk loading issues)
            this.audioElement = document.createElement('video');
            this.audioElement.volume = this.volume;
            this.audioElement.style.display = 'none'; // Hidden video for audio playback
            document.body.appendChild(this.audioElement);

            // HLS config - disable worker to avoid CSP issues in admin panel
            this.hls = new Hls({
                enableWorker: false
            });

            this.hls.loadSource(url);
            this.hls.attachMedia(this.audioElement);

            // Start playing when manifest is parsed - faster start
            this.hls.on(Hls.Events.MANIFEST_PARSED, () => {
                console.log('✅ [ADMIN PLAYER] HLS manifest parsed, starting playback');
                this.audioElement.play()
                    .then(() => {
                        console.log('✅ [ADMIN PLAYER] HLS playback started successfully');
                        this.isPlaying = true;
                        this.isLoading = false;
                        this.startProgressUpdate();
                    })
                    .catch(err => {
                        if (err.name !== 'AbortError') {
                            console.error('❌ [ADMIN PLAYER] HLS play error:', err);
                        }
                        this.isLoading = false;
                    });
            });

            this.hls.on(Hls.Events.ERROR, (event, data) => {
                console.error('❌ [ADMIN PLAYER] HLS Error:', data.type, data.details, data);
                if (data.fatal) {
                    console.error('❌ [ADMIN PLAYER] Fatal HLS error, retry count:', this.hlsRetryCount);
                    this.hlsRetryCount++;

                    // Try recovery first
                    if (this.hlsRetryCount < this.maxHlsRetries) {
                        if (data.type === Hls.ErrorTypes.NETWORK_ERROR) {
                            this.hls.startLoad();
                        } else if (data.type === Hls.ErrorTypes.MEDIA_ERROR) {
                            this.hls.recoverMediaError();
                        } else {
                            // Unknown error - go to fallback
                            this.hlsRetryCount = this.maxHlsRetries;
                        }
                    }

                    // Fallback to direct playback after max retries
                    if (this.hlsRetryCount >= this.maxHlsRetries) {
                        if (this.hls) {
                            this.hls.destroy();
                            this.hls = null;
                        }
                        if (this.audioElement) {
                            this.audioElement.pause();
                            this.audioElement.src = '';
                            if (this.audioElement.parentNode) {
                                this.audioElement.parentNode.removeChild(this.audioElement);
                            }
                            this.audioElement = null;
                        }

                        if (this.fallbackUrl) {
                            this.playDirect(this.fallbackUrl);
                        } else {
                            this.isLoading = false;
                        }
                    }
                }
            });

            // Audio element events
            this.audioElement.onended = () => {
                this.isPlaying = false;
                this.progress = 0;
                this.currentTime = 0;
                this.stopProgressUpdate();
            };

            this.audioElement.onpause = () => {
                this.isPlaying = false;
            };

            this.audioElement.onplay = () => {
                this.isPlaying = true;
            };

            this.audioElement.ondurationchange = () => {
                this.duration = this.audioElement.duration || 0;
            };
        },

        // Play direct MP3/audio file
        playDirect(url) {
            console.log('🎵 [ADMIN PLAYER] playDirect() called with:', url);

            this.sound = new Howl({
                src: [url],
                html5: true,
                volume: this.volume,
                onplay: () => {
                    console.log('✅ [ADMIN PLAYER] Howler playback started');
                    this.isPlaying = true;
                    this.isLoading = false;
                    this.duration = this.sound.duration();
                    this.startProgressUpdate();
                },
                onpause: () => {
                    console.log('⏸️ [ADMIN PLAYER] Howler paused');
                    this.isPlaying = false;
                },
                onend: () => {
                    console.log('⏹️ [ADMIN PLAYER] Howler playback ended');
                    this.isPlaying = false;
                    this.progress = 0;
                    this.currentTime = 0;
                    this.stopProgressUpdate();
                },
                onload: () => {
                    console.log('✅ [ADMIN PLAYER] Howler loaded, duration:', this.sound.duration());
                    this.duration = this.sound.duration();
                },
                onloaderror: (id, error) => {
                    console.error('❌ [ADMIN PLAYER] Howler load error:', error);
                    this.isLoading = false;
                }
            });

            this.sound.play();
        },

        // Toggle play/pause
        togglePlay() {
            // HLS playback
            if (this.audioElement) {
                if (this.isPlaying) {
                    this.audioElement.pause();
                } else {
                    this.audioElement.play();
                }
                return;
            }

            // Howler playback
            if (!this.sound) return;

            if (this.isPlaying) {
                this.sound.pause();
            } else {
                this.sound.play();
            }
        },

        // Stop playback
        stop() {
            // Stop Howler
            if (this.sound) {
                this.sound.stop();
                this.sound.unload();
                this.sound = null;
            }

            // Stop HLS
            if (this.hls) {
                this.hls.destroy();
                this.hls = null;
            }

            // Stop audio/video element
            if (this.audioElement) {
                this.audioElement.pause();
                this.audioElement.src = '';
                // Remove video element from DOM
                if (this.audioElement.parentNode) {
                    this.audioElement.parentNode.removeChild(this.audioElement);
                }
                this.audioElement = null;
            }

            this.stopProgressUpdate();
            this.isPlaying = false;
            this.progress = 0;
            this.currentTime = 0;
        },

        // Close player
        close() {
            this.stop();
            this.isVisible = false;
            this.currentSong = null;
        },

        // Seek to position
        seek(event) {
            const rect = event.target.getBoundingClientRect();
            const percent = (event.clientX - rect.left) / rect.width;
            const seekTime = this.duration * percent;

            // HLS seek
            if (this.audioElement) {
                this.audioElement.currentTime = seekTime;
                this.currentTime = seekTime;
                this.progress = percent * 100;
                return;
            }

            // Howler seek
            if (!this.sound) return;

            this.sound.seek(seekTime);
            this.currentTime = seekTime;
            this.progress = percent * 100;
        },

        // Update progress
        startProgressUpdate() {
            this.stopProgressUpdate();

            this.progressInterval = setInterval(() => {
                // HLS progress
                if (this.audioElement && this.isPlaying) {
                    this.currentTime = this.audioElement.currentTime || 0;
                    this.duration = this.audioElement.duration || 0;

                    if (this.duration > 0) {
                        this.progress = (this.currentTime / this.duration) * 100;
                    }
                    return;
                }

                // Howler progress
                if (this.sound && this.isPlaying) {
                    this.currentTime = this.sound.seek() || 0;
                    this.duration = this.sound.duration() || 0;

                    if (this.duration > 0) {
                        this.progress = (this.currentTime / this.duration) * 100;
                    }
                }
            }, 100);
        },

        stopProgressUpdate() {
            if (this.progressInterval) {
                clearInterval(this.progressInterval);
                this.progressInterval = null;
            }
        },

        // Format time helper
        formatTime(seconds) {
            if (!seconds || isNaN(seconds)) return '0:00';

            const mins = Math.floor(seconds / 60);
            const secs = Math.floor(seconds % 60);
            return `${mins}:${secs.toString().padStart(2, '0')}`;
        },

        // Set volume
        setVolume(value) {
            this.volume = value;

            // HLS volume
            if (this.audioElement) {
                this.audioElement.volume = value;
            }

            // Howler volume
            if (this.sound) {
                this.sound.volume(value);
            }
        }
    }));
});

// Global helper function for easy access
window.playAdminSong = function(songData) {
    console.log('🎵 [ADMIN PLAYER] playAdminSong called with:', songData);
    window.dispatchEvent(new CustomEvent('play-song', { detail: songData }));
    console.log('🎵 [ADMIN PLAYER] play-song event dispatched');
};
