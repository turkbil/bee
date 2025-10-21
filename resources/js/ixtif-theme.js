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
            text.textContent = 'Başarılı!';
            button.classList.remove('bg-red-600', 'hover:bg-red-700');
            button.classList.add('bg-green-600');

            setTimeout(() => {
                // Otomatik sayfa yenileme - hard refresh ile
                window.location.reload(true);
            }, 1000);
        } else {
            throw new Error(data.message);
        }
    })
    .catch(error => {
        text.textContent = 'Hata!';
        button.classList.remove('bg-red-600', 'hover:bg-red-700');
        button.classList.add('bg-red-700');

        setTimeout(() => {
            // Reset button
            button.disabled = false;
            spinner.classList.add('hidden');
            icon.classList.remove('hidden');
            text.textContent = 'Cache';
            button.classList.remove('bg-red-700');
            button.classList.add('bg-red-600', 'hover:bg-red-700');
        }, 2000);
    });
}

/**
 * Sticky Header Scroll Handler
 * Sayfa scroll edildiğinde header'ı yukarı taşır
 */
document.addEventListener('DOMContentLoaded', function() {
    const header = document.getElementById('main-header');
    const topBar = document.getElementById('top-bar');

    if (!header || !topBar) {
        console.error('Header elements not found!');
        return;
    }

    // Top bar height'ı hesapla
    const topBarHeight = topBar.offsetHeight;
    console.log('Top bar height:', topBarHeight + 'px');

    // CSS variable olarak ekle
    document.documentElement.style.setProperty('--top-bar-height', topBarHeight + 'px');

    window.addEventListener('scroll', function() {
        const currentScroll = window.pageYOffset;

        if (currentScroll > 30) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });

    console.log('✅ Sticky header initialized');
});

/**
 * Font Ayarlarını Temizle
 * Eski font sistem ayarlarını temizler (Roboto sabit)
 */
if (localStorage.getItem('selectedFont')) {
    localStorage.removeItem('selectedFont');
    console.log('✅ Font ayarları temizlendi - Roboto sabit font olarak ayarlandı');
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
        console.log('✅ GLightbox initialized with keyboard & mouse controls');
    } else {
        console.warn('⚠️ GLightbox library not loaded yet');
    }
}

// Global olarak erişilebilir yap
window.initGLightbox = initGLightbox;
