/**
 * Muzibu Play Limits System
 * Tema-bağımsız dinleme limiti kontrolü
 *
 * Guest: 30sn preview
 * Member: 5 şarkı/gün (60+ saniye)
 * Premium/Trial: Sınırsız
 */

document.addEventListener('alpine:init', () => {
    Alpine.data('playLimits', () => ({
        // State
        remainingPlays: -1,           // -1 = sınırsız, 0-5 = kalan hak
        isPreviewMode: false,         // Guest preview aktif mi
        limitExceeded: false,         // Limit aşıldı mı
        showGuestModal: false,        // Guest modal
        showLimitModal: false,        // Limit modal
        progressTracker: null,        // Progress interval
        previewStartTime: null,       // Preview başlangıç
        currentSongId: null,          // Şu anki şarkı ID
        isTracking: false,            // Tracking aktif mi

        // Init
        init() {
            console.log('🎵 Play Limits System initialized');

            // Global muzibuApp erişimi için
            if (window.muzibuApp) {
                this.bindToPlayer();
            } else {
                // Eğer player daha yüklenmediyse bekle
                document.addEventListener('player:ready', () => {
                    this.bindToPlayer();
                });
            }
        },

        // Player'a bağlan
        bindToPlayer() {
            const player = window.muzibuApp || Alpine.$data(document.querySelector('[x-data*="muzibuApp"]'));

            if (!player) {
                console.warn('⚠️ Player not found, retrying...');
                setTimeout(() => this.bindToPlayer(), 1000);
                return;
            }

            console.log('✅ Play Limits bound to player');

            // Player event'lerini dinle
            this.$watch(() => player.isPlaying, (playing) => {
                if (playing && player.currentSong) {
                    this.onSongStart(player.currentSong.id, player.isLoggedIn);
                } else {
                    this.onSongStop();
                }
            });

            // Progress izle (her saniye)
            this.$watch(() => player.currentTime, (time) => {
                if (player.isPlaying) {
                    this.onProgress(time, player.isLoggedIn);
                }
            });

            // Şarkı değişimi
            this.$watch(() => player.currentSong?.id, (newId) => {
                if (newId) {
                    this.currentSongId = newId;
                    this.previewStartTime = null;
                }
            });
        },

        // Şarkı başladı
        onSongStart(songId, isLoggedIn) {
            console.log('▶️ Song started:', songId, 'Logged:', isLoggedIn);

            this.currentSongId = songId;
            this.previewStartTime = Date.now();

            if (isLoggedIn) {
                // Üye: Progress tracking başlat
                this.startProgressTracking();
            } else {
                // Guest: Preview mode
                this.isPreviewMode = true;
                console.log('👤 Guest mode: 30 second preview');
            }
        },

        // Şarkı durdu
        onSongStop() {
            this.stopProgressTracking();
            this.previewStartTime = null;
        },

        // Progress kontrolü
        onProgress(currentTime, isLoggedIn) {
            // Guest: 30sn kontrolü
            if (!isLoggedIn && this.isPreviewMode && currentTime >= 30) {
                console.log('⏱️ Guest 30s limit reached');
                this.handleGuestLimit();
            }
        },

        // Progress tracking (5sn interval)
        startProgressTracking() {
            if (this.isTracking) return;

            this.stopProgressTracking();
            this.isTracking = true;

            console.log('📊 Progress tracking started');

            this.progressTracker = setInterval(() => {
                this.sendProgressReport();
            }, 5000); // Her 5 saniye
        },

        stopProgressTracking() {
            if (this.progressTracker) {
                clearInterval(this.progressTracker);
                this.progressTracker = null;
                this.isTracking = false;
                console.log('⏹️ Progress tracking stopped');
            }
        },

        // Backend'e progress raporu gönder
        async sendProgressReport() {
            const player = window.muzibuApp || Alpine.$data(document.querySelector('[x-data*="muzibuApp"]'));

            if (!player || !player.isPlaying || !player.currentSong) {
                return;
            }

            const duration = Math.floor(player.currentTime);

            try {
                const response = await fetch(`/api/muzibu/songs/${player.currentSong.id}/track-progress`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({ duration })
                });

                const data = await response.json();

                if (data.success) {
                    this.remainingPlays = data.remaining;
                    console.log('✅ Progress reported:', duration, 's | Remaining:', data.remaining);

                    // Limit kontrol
                    if (data.remaining === 0) {
                        this.handleMemberLimit();
                    }
                }
            } catch (error) {
                console.error('❌ Progress report failed:', error);
            }
        },

        // Guest limiti aşıldı
        handleGuestLimit() {
            const player = window.muzibuApp || Alpine.$data(document.querySelector('[x-data*="muzibuApp"]'));

            if (player) {
                // Fade out ve durdur
                if (player.howl) {
                    player.howl.fade(player.volume / 100, 0, 1000);
                    setTimeout(() => {
                        player.howl.pause();
                        player.isPlaying = false;
                    }, 1000);
                } else if (player.hls) {
                    const audio = player.getActiveHlsAudio();
                    if (audio) {
                        const fadeOut = setInterval(() => {
                            if (audio.volume > 0.1) {
                                audio.volume -= 0.1;
                            } else {
                                audio.pause();
                                player.isPlaying = false;
                                clearInterval(fadeOut);
                            }
                        }, 100);
                    }
                }
            }

            // Modal göster
            this.showGuestModal = true;
            this.isPreviewMode = false;
        },

        // Member limiti aşıldı
        handleMemberLimit() {
            const player = window.muzibuApp || Alpine.$data(document.querySelector('[x-data*="muzibuApp"]'));

            if (player) {
                player.isPlaying = false;
                if (player.howl) player.howl.pause();
                if (player.hls) {
                    const audio = player.getActiveHlsAudio();
                    if (audio) audio.pause();
                }
            }

            this.limitExceeded = true;
            this.showLimitModal = true;
            this.stopProgressTracking();
        },

        // Stream öncesi limit kontrolü
        async checkBeforePlay(songId) {
            try {
                const response = await fetch(`/api/muzibu/songs/${songId}/stream`);
                const data = await response.json();

                if (data.status === 'limit_exceeded') {
                    this.limitExceeded = true;
                    this.showLimitModal = true;
                    return false;
                }

                if (data.status === 'preview') {
                    this.isPreviewMode = true;
                }

                if (data.remaining !== undefined) {
                    this.remainingPlays = data.remaining;
                }

                return true;
            } catch (error) {
                console.error('❌ Check before play failed:', error);
                return true; // Hata durumunda çal (graceful degradation)
            }
        },

        // Guest modal'dan kayıt ol
        handleGuestRegister() {
            this.showGuestModal = false;
            const player = window.muzibuApp || Alpine.$data(document.querySelector('[x-data*="muzibuApp"]'));
            if (player) {
                player.showAuthModal = 'register';
            }
        },

        // Guest modal'dan giriş yap
        handleGuestLogin() {
            this.showGuestModal = false;
            const player = window.muzibuApp || Alpine.$data(document.querySelector('[x-data*="muzibuApp"]'));
            if (player) {
                player.showAuthModal = 'login';
            }
        },

        // Cleanup
        destroy() {
            this.stopProgressTracking();
        }
    }));
});

// Global erişim için
window.playLimitsSystem = {
    checkBeforePlay: async (songId) => {
        const component = Alpine.$data(document.querySelector('[x-data*="playLimits"]'));
        if (component) {
            return await component.checkBeforePlay(songId);
        }
        return true;
    }
};

console.log('✅ Play Limits System loaded');
