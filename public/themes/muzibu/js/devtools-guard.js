/**
 * 🛡️ DEVTOOLS AGRESIF KORUMA SİSTEMİ
 *
 * Amaç: F12 (DevTools) açan normal kullanıcıları logout yapar
 * Sadece admin/root kullanıcılar whitelist token ile bypass edebilir
 *
 * TENANT AWARE: Sadece Tenant 1001 (muzibu.com.tr) için aktif!
 */

(function() {
    'use strict';

    // 🔐 TENANT KONTROLÜ (Sadece Tenant 1001)
    const currentTenant = window.location.hostname;
    if (!currentTenant.includes('muzibu.com')) {
        return; // İxtif.com veya diğer tenant'larda çalışma!
    }

    const DevToolsGuard = {
        // Ayarlar
        config: {
            checkInterval: 500,        // Her 500ms kontrol et
            logoutDelay: 3000,         // 3 saniye bekle
            whitelistKey: 'devtools_whitelist_token', // LocalStorage key
            detectionThreshold: 2,     // En az 2 yöntem tetiklensin
        },

        // Durum
        state: {
            isDevToolsOpen: false,
            detectionCount: 0,
            warningShown: false,
            countdownInterval: null,
        },

        // DevTools tespit yöntemleri
        detectors: {
            // Yöntem 1: Pencere boyutu farkı (yatay)
            checkWindowSize() {
                const widthDiff = window.outerWidth - window.innerWidth;
                const heightDiff = window.outerHeight - window.innerHeight;
                return widthDiff > 160 || heightDiff > 160;
            },

            // Yöntem 2: Pencere boyutu farkı (dikey)
            checkWindowSizeVertical() {
                const threshold = 160;
                return window.outerHeight - window.innerHeight > threshold;
            },

            // Yöntem 3: Firebug check
            checkFirebug() {
                return window.Firebug && window.Firebug.chrome && window.Firebug.chrome.isInitialized;
            },

            // Yöntem 4: devtools-detect library benzeri
            checkDevtoolsDetect() {
                const threshold = 160;
                return (
                    window.outerWidth - window.innerWidth > threshold ||
                    window.outerHeight - window.innerHeight > threshold
                );
            }
        },

        // Whitelist kontrolü
        isWhitelisted() {
            const token = localStorage.getItem(this.config.whitelistKey);
            return token && token.length > 0;
        },

        // DevTools açık mı kontrol et (4 yöntem - console spam yok!)
        detectDevTools() {
            let detectedCount = 0;

            try {
                if (this.detectors.checkWindowSize()) detectedCount++;
                if (this.detectors.checkWindowSizeVertical()) detectedCount++;
                if (this.detectors.checkFirebug()) detectedCount++;
                if (this.detectors.checkDevtoolsDetect()) detectedCount++;
            } catch (e) {
                // Hata olursa false positive olmasın
                return false;
            }

            return detectedCount >= this.config.detectionThreshold;
        },

        // Uyarı modalını göster
        showWarning() {
            if (this.state.warningShown) return;
            this.state.warningShown = true;

            // Alpine.js global event dispatch
            if (window.Alpine) {
                window.dispatchEvent(new CustomEvent('devtools-detected'));
            }

            // Ekranı karart ve modal aç
            const modal = document.getElementById('devtools-warning-modal');
            if (modal) {
                modal.style.display = 'flex';
                this.startCountdown();
            }
        },

        // Geri sayım başlat
        startCountdown() {
            let seconds = 3;
            const countdownEl = document.getElementById('devtools-countdown');

            if (countdownEl) {
                countdownEl.textContent = seconds;
            }

            this.state.countdownInterval = setInterval(() => {
                seconds--;
                if (countdownEl) {
                    countdownEl.textContent = seconds;
                }

                if (seconds <= 0) {
                    clearInterval(this.state.countdownInterval);
                    this.performLogout();
                }
            }, 1000);
        },

        // Logout yap
        performLogout() {
            // Session temizle
            if (window.Alpine?.store('auth')) {
                window.Alpine.store('auth').logout();
            }

            // LocalStorage temizle (whitelist hariç admin tokenları koruyabiliriz)
            const whitelist = localStorage.getItem(this.config.whitelistKey);
            localStorage.clear();
            if (whitelist) {
                localStorage.setItem(this.config.whitelistKey, whitelist);
            }

            // Backend'e logout request (opsiyonel)
            fetch('/logout', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json'
                }
            }).catch(() => {});

            // Login sayfasına yönlendir
            setTimeout(() => {
                window.location.href = '/login?devtools=blocked';
            }, 500);
        },

        // Ana kontrol döngüsü
        mainLoop() {
            // Whitelist varsa bypass
            if (this.isWhitelisted()) {
                return;
            }

            // DevTools tespiti
            const isOpen = this.detectDevTools();

            if (isOpen && !this.state.isDevToolsOpen) {
                // İlk kez açıldı
                this.state.isDevToolsOpen = true;
                this.showWarning();
            } else if (!isOpen && this.state.isDevToolsOpen) {
                // Kapatıldı (kullanıcı 3 saniye içinde kapattıysa)
                this.state.isDevToolsOpen = false;
                // Modal'ı kapat
                const modal = document.getElementById('devtools-warning-modal');
                if (modal && !this.state.warningShown) {
                    modal.style.display = 'none';
                }
                if (this.state.countdownInterval) {
                    clearInterval(this.state.countdownInterval);
                }
            }
        },

        // Başlat
        init() {
            // Tenant kontrolü tekrar (güvenlik)
            if (!window.location.hostname.includes('muzibu.com')) {
                return;
            }

            // Ana döngü
            setInterval(() => {
                this.mainLoop();
            }, this.config.checkInterval);

            // Sayfa yüklendiğinde de kontrol et
            this.mainLoop();
        }
    };

    // DOM hazır olduğunda başlat
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            DevToolsGuard.init();
        });
    } else {
        DevToolsGuard.init();
    }

    // Global erişim (admin bypass için)
    window.DevToolsGuard = DevToolsGuard;

})();
