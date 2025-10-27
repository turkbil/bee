// Cookie Consent - GDPR Uyumlu
window.cookieConsentApp = function() {
    return {
        showCookieConsent: false,
        autoAcceptTimer: null,
        timeRemaining: 12,
        preferences: { necessary: true, functional: true, analytics: true, marketing: true },
        showModal: false,

        init() {
            // LocalStorage'da kayıt var mı kontrol et
            const consent = localStorage.getItem('cookieConsent');

            if (!consent) {
                // İlk ziyaret - banner göster
                this.showCookieConsent = true;
                this.startAutoAcceptTimer();
            } else {
                // Daha önce kaydedilmiş - banner gösterme
                this.preferences = JSON.parse(consent);
                this.showCookieConsent = false;
                this.loadTrackingScripts();
            }
        },

        startAutoAcceptTimer() {
            this.timeRemaining = 12;
            if (this.autoAcceptTimer) clearInterval(this.autoAcceptTimer);

            this.autoAcceptTimer = setInterval(() => {
                this.timeRemaining--;
                if (this.timeRemaining <= 0) {
                    this.acceptAll();
                    clearInterval(this.autoAcceptTimer);
                }
            }, 1000);
        },

        stopAutoAcceptTimer() {
            if (this.autoAcceptTimer) {
                clearInterval(this.autoAcceptTimer);
                this.autoAcceptTimer = null;
            }
        },

        acceptAll() {
            this.preferences = { necessary: true, functional: true, analytics: true, marketing: true };
            localStorage.setItem('cookieConsent', JSON.stringify(this.preferences));
            this.showCookieConsent = false;
            this.showModal = false;
            this.stopAutoAcceptTimer();
            this.loadTrackingScripts();
        },

        rejectAll() {
            this.preferences = { necessary: true, functional: false, analytics: false, marketing: false };
            localStorage.setItem('cookieConsent', JSON.stringify(this.preferences));
            this.showCookieConsent = false;
            this.showModal = false;
            this.stopAutoAcceptTimer();
        },

        savePreferences() {
            localStorage.setItem('cookieConsent', JSON.stringify(this.preferences));
            this.showCookieConsent = false;
            this.showModal = false;
            this.stopAutoAcceptTimer();
            this.loadTrackingScripts();
        },

        loadTrackingScripts() {
            // Analytics
            if (this.preferences.analytics) {
                console.log('Analytics enabled');
                // Google Analytics kodu buraya gelecek
            }

            // Marketing
            if (this.preferences.marketing) {
                console.log('Marketing enabled');
                // Meta Pixel, Google Ads kodu buraya gelecek
            }
        }
    }
}
