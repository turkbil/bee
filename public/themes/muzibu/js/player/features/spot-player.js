/**
 * Muzibu Spot (Anons) Player Module
 * ===================================
 * Kurumsal hesaplar için şarkı arası anons sistemi
 *
 * Özellikler:
 * - X şarkıda bir spot çalar (varsayılan: 10)
 * - 30 saniyeden uzun dinlemeler sayılır
 * - localStorage ile sayaç tutulur (performans için)
 * - Spot atlanabilir (skip button)
 * - Dinleme istatistikleri API'ye loglanır
 */

window.MuzibuSpotPlayer = (function() {
    'use strict';

    // ═══════════════════════════════════════════════════════════════════════════
    // STATE & CONFIG
    // ═══════════════════════════════════════════════════════════════════════════

    const state = {
        enabled: false,
        isPaused: false,     // Şube için durduruldu mu?
        songsBetween: 10,
        songsPlayed: 0,      // 30 saniyeden uzun dinlenen şarkı sayısı
        corporateId: null,
        branchId: null,
        currentSpot: null,
        currentPlayId: null,
        isPlaying: false,
        spotStartTime: null,
        wasSkipped: false,
        // 🚀 PRELOAD: Spot ve audio önceden yüklenir
        preloadedSpot: null,
        preloadedAudio: null,
        isPreloading: false,
    };

    const STORAGE_KEY = 'muzibu_spot_counter';
    const MIN_LISTEN_DURATION = 30; // 30 saniye dinlenince sayılır

    // ═══════════════════════════════════════════════════════════════════════════
    // INIT & SETTINGS
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Spot sistemini başlat
     * Sayfa yüklendiğinde çağrılmalı
     */
    async function init() {
        console.log('🎙️ SpotPlayer: Initializing...');

        try {
            // localStorage'dan sayacı yükle
            loadCounter();

            // API'den ayarları al
            await fetchSettings();
        } catch (e) {
            console.error('🎙️ SpotPlayer: INIT ERROR!', e);
        }

        if (state.enabled) {
            console.log(`🎙️ SpotPlayer: Enabled. Songs between: ${state.songsBetween}, Current count: ${state.songsPlayed}`);
        } else {
            console.log('🎙️ SpotPlayer: Disabled or no corporate account');
        }
    }

    /**
     * API'den spot ayarlarını al
     */
    async function fetchSettings() {
        try {
            console.log('🎙️ SpotPlayer: Fetching settings from /api/spot/settings...');
            const response = await fetch('/api/spot/settings', {
                credentials: 'include',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();
            console.log('🎙️ SpotPlayer: Settings response:', data);

            state.enabled = data.enabled === true;
            state.isPaused = data.spot_is_paused === true;
            state.songsBetween = data.songs_between || 10;
            state.corporateId = data.corporate_id || null;
            state.branchId = data.branch_id || null;

            // Ayarlar değiştiyse sayacı sıfırla
            const savedSettings = localStorage.getItem(STORAGE_KEY + '_settings');
            const currentSettings = JSON.stringify({
                corporateId: state.corporateId,
                songsBetween: state.songsBetween
            });

            if (savedSettings !== currentSettings) {
                resetCounter();
                localStorage.setItem(STORAGE_KEY + '_settings', currentSettings);
            }

        } catch (error) {
            console.error('🎙️ SpotPlayer: Failed to fetch settings', error);
            state.enabled = false;
        }
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // COUNTER MANAGEMENT (localStorage)
    // ═══════════════════════════════════════════════════════════════════════════

    function loadCounter() {
        try {
            const saved = localStorage.getItem(STORAGE_KEY);
            if (saved) {
                state.songsPlayed = parseInt(saved, 10) || 0;
            }
        } catch (e) {
            state.songsPlayed = 0;
        }
    }

    function saveCounter() {
        try {
            localStorage.setItem(STORAGE_KEY, state.songsPlayed.toString());
        } catch (e) {
            console.warn('🎙️ SpotPlayer: Failed to save counter to localStorage');
        }
    }

    function resetCounter() {
        state.songsPlayed = 0;
        saveCounter();
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // SONG TRACKING
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Şarkı dinlendiğinde çağrılır
     * Player-core'dan ended veya timeupdate ile çağrılmalı
     *
     * @param {number} duration - Dinlenen süre (saniye)
     */
    function onSongListened(duration) {
        if (!state.enabled) return false;

        // 30 saniyeden az dinlendiyse sayma
        if (duration < MIN_LISTEN_DURATION) {
            console.log(`🎙️ SpotPlayer: Song listened for ${duration}s (< ${MIN_LISTEN_DURATION}s, not counted)`);
            return false;
        }

        // Sayacı artır
        state.songsPlayed++;
        saveCounter();

        console.log(`🎙️ SpotPlayer: Song counted. Progress: ${state.songsPlayed}/${state.songsBetween}`);

        // Spot zamanı mı?
        if (state.songsPlayed >= state.songsBetween) {
            console.log('🎙️ SpotPlayer: Time for a spot!');
            return true; // Spot çalınmalı
        }

        return false;
    }

    /**
     * Spot zamanı geldi mi kontrol et
     */
    function isSpotTime() {
        return state.enabled && state.songsPlayed >= state.songsBetween;
    }

    /**
     * 🚀 Spot zamanı yaklaştı mı? (preload için)
     * Bir sonraki şarkı bitince spot çalacaksa true döner
     */
    function shouldPreloadSpot() {
        return state.enabled && (state.songsPlayed >= state.songsBetween - 1);
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // SPOT PRELOADING (Gapless için)
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * 🚀 Spot'u önceden yükle (şarkı %80'e geldiğinde çağrılır)
     */
    async function preloadSpot() {
        if (!state.enabled || state.isPreloading || state.preloadedSpot) return;
        if (!shouldPreloadSpot()) return;

        state.isPreloading = true;
        console.log('🎙️ SpotPlayer: Preloading spot...');

        try {
            const response = await fetch('/api/spot/next', {
                credentials: 'include',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (!data.success || !data.spot) {
                console.log('🎙️ SpotPlayer: No spot to preload');
                state.isPreloading = false;
                return;
            }

            state.preloadedSpot = data.spot;

            // Audio element oluştur ve preload et
            const audio = new Audio();
            audio.preload = 'auto';
            audio.src = data.spot.audio_url;

            // Yüklenene kadar bekle
            await new Promise((resolve, reject) => {
                audio.oncanplaythrough = resolve;
                audio.onerror = reject;
                setTimeout(resolve, 3000); // Max 3 saniye bekle
            });

            state.preloadedAudio = audio;
            state.isPreloading = false;
            console.log('🎙️ SpotPlayer: Spot preloaded:', data.spot.title);

        } catch (error) {
            console.error('🎙️ SpotPlayer: Preload failed', error);
            state.isPreloading = false;
            state.preloadedSpot = null;
            state.preloadedAudio = null;
        }
    }

    /**
     * 🚀 Preload'ı temizle
     * 🔧 FIX: src'yi silme! Audio player'a aktarıldıysa hala kullanılıyor olabilir
     * @param {boolean} clearSrc - true ise src'yi de sil (iptal durumunda)
     */
    function clearPreload(clearSrc = false) {
        if (state.preloadedAudio) {
            // 🛡️ Sadece iptal durumunda src sil, normal kullanımda silme!
            if (clearSrc) {
                state.preloadedAudio.src = '';
            }
            state.preloadedAudio = null;
        }
        state.preloadedSpot = null;
        state.isPreloading = false;
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // SPOT PLAYBACK
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Bir sonraki spotu getir ve çal
     * 🚀 Preload varsa onu kullanır (gapless)
     * @returns {Promise<object|null>} Spot bilgisi veya null
     */
    async function playNextSpot() {
        if (!state.enabled) return null;

        try {
            let spot;

            // 🚀 Preload varsa kullan
            if (state.preloadedSpot && state.preloadedAudio) {
                console.log('🎙️ SpotPlayer: Using preloaded spot (gapless)');
                spot = state.preloadedSpot;
                // Preloaded audio'yu player'a ver
                spot._preloadedAudio = state.preloadedAudio;
                clearPreload();
            } else {
                // Preload yoksa API'den al
                const response = await fetch('/api/spot/next', {
                    credentials: 'include',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (!data.success || !data.spot) {
                    console.log('🎙️ SpotPlayer: No spot available');
                    resetCounter();
                    return null;
                }

                spot = data.spot;
            }

            state.currentSpot = spot;
            state.wasSkipped = false;
            state.spotStartTime = Date.now();

            // Dinleme kaydı başlat
            await logPlayStart(spot.id);

            // Sayacı sıfırla
            resetCounter();

            console.log('🎙️ SpotPlayer: Playing spot:', spot.title);

            return spot;

        } catch (error) {
            console.error('🎙️ SpotPlayer: Failed to fetch next spot', error);
            return null;
        }
    }

    /**
     * Spot dinleme başladı - API'ye log
     */
    async function logPlayStart(spotId) {
        try {
            const response = await fetch('/api/spot/play-start', {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({
                    spot_id: spotId,
                    source_type: 'player',
                    source_id: null
                })
            });

            const data = await response.json();
            if (data.success) {
                state.currentPlayId = data.play_id;
            }
        } catch (error) {
            console.error('🎙️ SpotPlayer: Failed to log play start', error);
        }
    }

    /**
     * Spot dinleme bitti - API'ye log
     */
    async function logPlayEnd(wasSkipped = false) {
        if (!state.currentPlayId) return;

        const listenedDuration = state.spotStartTime
            ? Math.floor((Date.now() - state.spotStartTime) / 1000)
            : 0;

        try {
            await fetch('/api/spot/play-end', {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({
                    play_id: state.currentPlayId,
                    listened_duration: listenedDuration,
                    was_skipped: wasSkipped
                })
            });
        } catch (error) {
            console.error('🎙️ SpotPlayer: Failed to log play end', error);
        }

        // State temizle
        state.currentPlayId = null;
        state.currentSpot = null;
        state.spotStartTime = null;
        state.wasSkipped = false;
    }

    /**
     * Spotu atla (skip)
     */
    async function skipSpot() {
        console.log('🎙️ SpotPlayer: Spot skipped');
        state.wasSkipped = true;
        await logPlayEnd(true);
    }

    /**
     * Spot normal şekilde bitti
     */
    async function spotEnded() {
        console.log('🎙️ SpotPlayer: Spot ended normally');
        await logPlayEnd(false);
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // PAUSE/RESUME (Şube için)
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Şube için spot'u durdur/devam ettir
     */
    async function togglePause() {
        try {
            const response = await fetch('/api/spot/toggle-pause', {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            });

            const data = await response.json();

            if (data.success) {
                // isPaused state'i güncelle
                state.isPaused = data.is_paused;
                // Ayarları yeniden yükle (enabled durumu değişebilir)
                await fetchSettings();
                console.log('🎙️ SpotPlayer: Pause toggled. isPaused:', state.isPaused);
                return {
                    success: true,
                    isPaused: data.is_paused,
                    message: data.message
                };
            }

            return { success: false, error: data.error || 'unknown' };

        } catch (error) {
            console.error('🎙️ SpotPlayer: Failed to toggle pause', error);
            return { success: false };
        }
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // PUBLIC API
    // ═══════════════════════════════════════════════════════════════════════════

    return {
        init,
        onSongListened,
        isSpotTime,
        playNextSpot,
        skipSpot,
        spotEnded,
        togglePause,
        fetchSettings,
        resetCounter,

        // 🚀 Preload API
        preloadSpot,
        shouldPreloadSpot,
        hasPreloadedSpot: () => !!state.preloadedSpot,

        // State getters
        isEnabled: () => state.enabled,
        isPaused: () => state.isPaused,
        getSongsPlayed: () => state.songsPlayed,
        getSongsBetween: () => state.songsBetween,
        getCurrentSpot: () => state.currentSpot,
        isPlaying: () => state.currentSpot !== null,
    };

})();

console.log('🎙️ SpotPlayer: Script loaded! MuzibuSpotPlayer:', typeof window.MuzibuSpotPlayer);

// Auto-init when DOM is ready
if (document.readyState === 'loading') {
    console.log('🎙️ SpotPlayer: DOM loading, waiting for DOMContentLoaded...');
    document.addEventListener('DOMContentLoaded', () => {
        console.log('🎙️ SpotPlayer: DOMContentLoaded fired, calling init()...');
        MuzibuSpotPlayer.init();
    });
} else {
    console.log('🎙️ SpotPlayer: DOM ready, calling init() immediately...');
    MuzibuSpotPlayer.init();
}
