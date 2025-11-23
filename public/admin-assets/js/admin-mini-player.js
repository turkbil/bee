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
            // Listen for play events from song list
            window.addEventListener('play-song', (e) => {
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
            // Stop current playback
            this.stop();

            this.currentSong = songData;
            this.isLoading = true;
            this.isVisible = true;
            this.hlsRetryCount = 0;

            // Determine URLs
            let hlsUrl = null;
            const directUrl = songData.file_url || songData.url;

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

            if (!hlsUrl && !directUrl) {
                console.error('No audio URL provided');
                this.isLoading = false;
                return;
            }

            // Try HLS first if available
            if (hlsUrl && songData.is_hls !== false) {
                await this.playHLS(hlsUrl);
            } else if (directUrl) {
                this.playDirect(directUrl);
            }
        },

        // Play HLS stream with encryption support
        async playHLS(url) {
            if (!Hls.isSupported()) {
                // Fallback for Safari (native HLS support)
                this.playDirect(url);
                return;
            }

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
                this.audioElement.play()
                    .then(() => {
                        this.isPlaying = true;
                        this.isLoading = false;
                        this.startProgressUpdate();
                    })
                    .catch(err => {
                        if (err.name !== 'AbortError') {
                            console.error('HLS play error:', err);
                        }
                        this.isLoading = false;
                    });
            });

            this.hls.on(Hls.Events.ERROR, (event, data) => {
                if (data.fatal) {
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
            this.sound = new Howl({
                src: [url],
                html5: true,
                volume: this.volume,
                onplay: () => {
                    this.isPlaying = true;
                    this.isLoading = false;
                    this.duration = this.sound.duration();
                    this.startProgressUpdate();
                },
                onpause: () => {
                    this.isPlaying = false;
                },
                onend: () => {
                    this.isPlaying = false;
                    this.progress = 0;
                    this.currentTime = 0;
                    this.stopProgressUpdate();
                },
                onload: () => {
                    this.duration = this.sound.duration();
                },
                onloaderror: (id, error) => {
                    console.error('Audio load error:', error);
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
    window.dispatchEvent(new CustomEvent('play-song', { detail: songData }));
};
