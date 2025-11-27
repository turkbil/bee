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
        guestTimeChecker: null,       // Guest 30s checker
        previewStartTime: null,       // Preview başlangıç
        currentSongId: null,          // Şu anki şarkı ID
        isTracking: false,            // Tracking aktif mi

        // Init
        init() {
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
            // Custom event'leri dinle
            window.addEventListener('player:play', (e) => {
                const { songId, isLoggedIn } = e.detail;
                this.onSongStart(songId, isLoggedIn);
            });

            window.addEventListener('player:pause', () => {
                this.onSongStop();
            });

            window.addEventListener('player:stop', () => {
                this.onSongStop();
            });

            window.addEventListener('player:timeupdate', (e) => {
                const { currentTime, isLoggedIn } = e.detail;
                this.onProgress(currentTime, isLoggedIn);
            });
        },

        // Şarkı başladı
        onSongStart(songId, isLoggedIn) {
            this.currentSongId = songId;
            this.previewStartTime = Date.now();

            if (isLoggedIn) {
                // Üye: Progress tracking başlat
                this.startProgressTracking();
            } else {
                // Guest: Preview mode + manual time check
                this.isPreviewMode = true;
                this.startGuestTimeCheck();
            }
        },

        // Şarkı durdu
        onSongStop() {
            this.stopProgressTracking();
            this.stopGuestTimeCheck();
            this.previewStartTime = null;
        },

        // Guest 30 saniye kontrolü (manuel interval)
        startGuestTimeCheck() {
            this.stopGuestTimeCheck();

            this.guestTimeChecker = setInterval(() => {
                // Alpine $data ile player objesini al
                const player = Alpine.$data(document.querySelector('[x-data*="muzibuApp"]'));

                if (!player) {
                    this.stopGuestTimeCheck();
                    return;
                }

                // Howler currentTime al
                const currentTime = player.howl ? player.howl.seek() : 0;

                // İlk saniyede 0 dönebilir, skip et
                if (currentTime < 1) {
                    return;
                }

                if (currentTime >= 30) {
                    this.handleGuestLimit();
                    this.stopGuestTimeCheck();
                }
            }, 1000); // Her saniye kontrol
        },

        stopGuestTimeCheck() {
            if (this.guestTimeChecker) {
                clearInterval(this.guestTimeChecker);
                this.guestTimeChecker = null;
            }
        },

        // Progress kontrolü
        onProgress(currentTime, isLoggedIn) {
            // Guest: 30sn kontrolü
            if (!isLoggedIn && this.isPreviewMode && currentTime >= 30) {
                this.handleGuestLimit();
            }
        },

        // Progress tracking (5sn interval)
        startProgressTracking() {
            if (this.isTracking) return;

            this.stopProgressTracking();
            this.isTracking = true;

            this.progressTracker = setInterval(() => {
                this.sendProgressReport();
            }, 5000); // Her 5 saniye
        },

        stopProgressTracking() {
            if (this.progressTracker) {
                clearInterval(this.progressTracker);
                this.progressTracker = null;
                this.isTracking = false;
            }
        },

        // Backend'e progress raporu gönder
        async sendProgressReport() {
            const player = Alpine.$data(document.querySelector('[x-data*="muzibuApp"]'));

            if (!player || !player.isPlaying || !player.currentSong) {
                return;
            }

            const duration = Math.floor(player.currentTime);

            try {
                const response = await fetch(`/api/muzibu/songs/${player.currentSong.song_id}/track-progress`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ duration })
                });

                const data = await response.json();

                if (data.success) {
                    this.remainingPlays = data.remaining;

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
            // Alpine $data ile player objesini al
            const player = Alpine.$data(document.querySelector('[x-data*="muzibuApp"]'));

            if (player) {
                // Fade out ve durdur
                if (player.howl) {
                    const isPlaying = player.howl.playing();

                    if (isPlaying) {
                        const currentVolume = player.howl.volume();
                        // 3 saniye yumuşak fade-out
                        player.howl.fade(currentVolume, 0, 3000);

                        setTimeout(() => {
                            if (player.howl) {
                                player.howl.stop();
                                player.howl.unload();
                                player.howl = null;
                            }
                            player.isPlaying = false;
                        }, 3000);
                    }
                } else if (player.hls) {
                    const audio = player.getActiveHlsAudio();
                    if (audio && !audio.paused) {
                        const startVolume = audio.volume;
                        const fadeDuration = 3000; // 3 saniye
                        const fadeSteps = 30; // 30 adım = her 100ms
                        const volumeStep = startVolume / fadeSteps;
                        let currentStep = 0;

                        const fadeOut = setInterval(() => {
                            currentStep++;
                            const newVolume = Math.max(0, startVolume - (volumeStep * currentStep));
                            audio.volume = newVolume;

                            if (currentStep >= fadeSteps || newVolume <= 0) {
                                audio.pause();
                                audio.currentTime = 0;
                                player.isPlaying = false;
                                clearInterval(fadeOut);
                            }
                        }, fadeDuration / fadeSteps); // 3000/30 = 100ms
                    }
                }
            }

            // Modal göster
            this.showGuestModal = true;
            this.isPreviewMode = false;
        },

        // Member limiti aşıldı
        handleMemberLimit() {
            const player = Alpine.$data(document.querySelector('[x-data*="muzibuApp"]'));

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
            const player = Alpine.$data(document.querySelector('[x-data*="muzibuApp"]'));
            if (player) {
                player.showAuthModal = 'register';
            }
        },

        // Guest modal'dan giriş yap
        handleGuestLogin() {
            this.showGuestModal = false;
            const player = Alpine.$data(document.querySelector('[x-data*="muzibuApp"]'));
            if (player) {
                player.showAuthModal = 'login';
            }
        },

        // Cleanup
        destroy() {
            this.stopProgressTracking();
            this.stopGuestTimeCheck();
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

// Play Limits System loaded
