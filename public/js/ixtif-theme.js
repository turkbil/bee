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
            closeButton: true
        });
        console.log('✅ GLightbox initialized');
    } else {
        console.warn('⚠️ GLightbox library not loaded yet');
    }
}

// Global olarak erişilebilir yap
window.initGLightbox = initGLightbox;
