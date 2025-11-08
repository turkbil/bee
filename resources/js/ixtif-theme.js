/**
 * iXtif Theme JavaScript
 *
 * Bu dosya iXtif teması için özel JS fonksiyonlarını içerir.
 * Header inline script'lerinden ayrıştırılmıştır.
 *
 * @version 1.0.0
 * @package ixtif
 */

/**
 * System Cache Clear Function
 * Admin kullanıcılarının cache'i temizlemesini sağlar
 */
function clearSystemCache(button) {
    const spinner = button.querySelector('.loading-spinner');
    const text = button.querySelector('.button-text');
    const icon = button.querySelector('svg:first-child');

    // Loading state
    button.disabled = true;
    spinner.classList.remove('hidden');
    icon.classList.add('hidden');
    text.textContent = 'Temizleniyor...';

    fetch('/clear-cache', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            text.textContent = 'Temizlendi!';
            button.classList.remove('bg-red-100', 'hover:bg-red-200', 'dark:bg-red-500/20', 'dark:hover:bg-red-500/30');
            button.classList.add('bg-green-100', 'dark:bg-green-500/20');

            // ⚠️ FIX: Auto-reload KALDIRILDI - Kullanıcı istediğinde manuel yeniler
            setTimeout(() => {
                // Reset button
                button.disabled = false;
                spinner.classList.add('hidden');
                icon.classList.remove('hidden');
                text.textContent = 'Cache';
                button.classList.remove('bg-green-100', 'dark:bg-green-500/20');
                button.classList.add('bg-red-100', 'dark:bg-red-500/20', 'hover:bg-red-200', 'dark:hover:bg-red-500/30');
            }, 2000);
        } else {
            throw new Error(data.message);
        }
    })
    .catch(error => {
        text.textContent = 'Hata!';
        button.classList.remove('bg-red-100', 'dark:bg-red-500/20');
        button.classList.add('bg-red-200', 'dark:bg-red-500/40');

        setTimeout(() => {
            // Reset button
            button.disabled = false;
            spinner.classList.add('hidden');
            icon.classList.remove('hidden');
            text.textContent = 'Cache';
            button.classList.remove('bg-red-200', 'dark:bg-red-500/40');
            button.classList.add('bg-red-100', 'dark:bg-red-500/20', 'hover:bg-red-200', 'dark:hover:bg-red-500/30');
        }, 2000);
    });
}

// Global olarak erişilebilir yap
window.clearSystemCache = clearSystemCache;

/**
 * AI Conversation Clear Function
 *
 * NOT: Bu fonksiyon /public/assets/js/ai-chat.js dosyasında tanımlıdır.
 * O dosyadaki fonksiyon hem localStorage hem de database'den conversation'ı siler.
 * Burada override etmiyoruz, ai-chat.js'teki fonksiyonu kullanıyoruz.
 */

/**
 * Topbar Height Calculator
 * Topbar yüksekliğini hesaplayıp CSS variable olarak set eder
 */
document.addEventListener('DOMContentLoaded', function() {
    const topBar = document.getElementById('top-bar');

    if (topBar) {
        // Topbar height'ı hesapla ve CSS variable olarak ekle
        const topBarHeight = topBar.offsetHeight;
        document.documentElement.style.setProperty('--top-bar-height', topBarHeight + 'px');

        // Resize olduğunda yeniden hesapla
        window.addEventListener('resize', function() {
            const newHeight = topBar.offsetHeight;
            document.documentElement.style.setProperty('--top-bar-height', newHeight + 'px');
        });
    }
});

/**
 * Font Ayarlarını Temizle
 * Eski font sistem ayarlarını temizler (Roboto sabit)
 */
if (localStorage.getItem('selectedFont')) {
    localStorage.removeItem('selectedFont');
}

/**
 * GLightbox Initialization
 * Lightbox galerisi için init fonksiyonu
 *
 * Klavye kontrolleri:
 * - Sol/Sağ ok tuşları: Önceki/Sonraki görsel
 * - Escape: Lightbox'ı kapat
 *
 * Mouse kontrolleri:
 * - Sağ/Sol butonlar: Önceki/Sonraki görsel (built-in)
 * - Yakınlaştırma: Scroll ile zoom (zoomable: true)
 */
function initGLightbox() {
    if (typeof GLightbox !== 'undefined') {
        const lightbox = GLightbox({
            selector: '.glightbox',
            touchNavigation: true,
            loop: true,
            autoplayVideos: false,
            zoomable: true,
            draggable: true,
            skin: 'clean',
            closeButton: true,
            keyboardNavigation: true,  // Klavye kontrolü (ok tuşları + escape)
            closeOnOutsideClick: true, // Dışarı tıklayınca kapat
            svg: {
                // İleri/geri ok iconları
                next: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>',
                prev: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>',
                close: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>'
            }
        });
    } else {
        console.warn('⚠️ GLightbox library not loaded yet');
    }
}

// Global olarak erişilebilir yap
window.initGLightbox = initGLightbox;

/**
 * Cookie Consent System - GDPR Uyumlu
 * Sadece ilk ziyarette gösterilir, sonra bir daha çıkmaz
 */
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
                // İlk ziyaret - banner göster, modal ASLA açma
                this.showCookieConsent = true;
                this.showModal = false;
                this.startAutoAcceptTimer();
            } else {
                // Daha önce kaydedilmiş - hiçbir şey gösterme
                this.preferences = JSON.parse(consent);
                this.showCookieConsent = false;
                this.showModal = false;
                this.loadTrackingScripts();
            }
        },

        openModal() {
            // Modal açılırken timer'ı durdur
            this.stopAutoAcceptTimer();

            // Preferences'ı koru - hepsi açık olmalı
            if (!this.preferences.functional) this.preferences.functional = true;
            if (!this.preferences.analytics) this.preferences.analytics = true;
            if (!this.preferences.marketing) this.preferences.marketing = true;

            this.showModal = true;
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
                // Google Analytics kodu buraya gelecek
            }

            // Marketing
            if (this.preferences.marketing) {
                // Meta Pixel, Google Ads kodu buraya gelecek
            }
        }
    }
}
