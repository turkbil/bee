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

/**
 * 🎯 STICKY SYSTEM V4 - Header Topbar + TOC Bar + Sidebar
 *
 * 1. Topbar: Scroll > 50px → Kaybolur
 * 2. TOC: Hero'dan sonra sticky → TOC'un ALTI FAQ BAŞINA değince DURUR (kaybolmaz)
 * 3. Sidebar: TOC sticky olunca başlar (TOC altında 16px) → Sidebar'ın ALTI FAQ BAŞINA değince DURUR (kaybolmaz)
 */
(function() {
    'use strict';

    const HEADER_HEIGHT = 80;
    const TOC_SIDEBAR_GAP = 16; // TOC ile sidebar arası boşluk

    const elements = {
        topbar: document.getElementById('top-bar'),
        tocBar: document.getElementById('toc-bar'),
        heroSection: document.getElementById('hero-section'),
        contactSection: document.getElementById('contact'),
        sidebar: document.getElementById('sticky-sidebar'),
        faqSection: document.getElementById('faq'),
        trustSignalsSection: document.getElementById('trust-signals')
    };

    let topbarVisible = true;
    let tocVisible = true;
    let tocSticky = false;
    let sidebarSticky = false;
    let sidebarVisible = true;
    let ticking = false;
    let tocOriginalOffset = 0;
    let contactOffset = 0;
    let faqStartOffset = 0;
    let trustSignalsOffset = 0;

    // Sidebar width/left cache (sürekli hesaplamayı önle)
    let cachedSidebarWidth = 0;
    let cachedSidebarLeft = 0;

    function init() {
        if (!elements.topbar) return;

        // TOC bar için başlangıç offsetini hesapla
        if (elements.tocBar && elements.heroSection) {
            tocOriginalOffset = elements.heroSection.offsetHeight;
        }

        // Contact section offset
        if (elements.contactSection) {
            contactOffset = elements.contactSection.offsetTop;
        }

        // FAQ section start offset (FAQ'nın BAŞLADIĞI yer)
        if (elements.faqSection) {
            faqStartOffset = elements.faqSection.offsetTop;
        }

        // Trust Signals section offset (TOC ve Sidebar burada durmalı)
        if (elements.trustSignalsSection) {
            trustSignalsOffset = elements.trustSignalsSection.offsetTop;
        }

        // Scroll listener
        window.addEventListener('scroll', onScroll, { passive: true });
        window.addEventListener('resize', calculateOffsets, { passive: true });
    }

    function calculateOffsets() {
        if (elements.heroSection) {
            tocOriginalOffset = elements.heroSection.offsetHeight;
        }
        if (elements.contactSection) {
            contactOffset = elements.contactSection.offsetTop;
        }
        if (elements.faqSection) {
            faqStartOffset = elements.faqSection.offsetTop;
        }
        if (elements.trustSignalsSection) {
            trustSignalsOffset = elements.trustSignalsSection.offsetTop;
        }

        // Sidebar cache'i güncelle (resize'da)
        if (sidebarSticky && elements.sidebar) {
            const sidebarParent = elements.sidebar.parentElement;
            const parentRect = sidebarParent.getBoundingClientRect();
            cachedSidebarWidth = parentRect.width;
            cachedSidebarLeft = parentRect.left;
        }
    }

    function onScroll() {
        if (!ticking) {
            window.requestAnimationFrame(() => {
                const scrollY = window.pageYOffset;
                updateTopbar(scrollY);
                updateTOC(scrollY);
                updateSidebar(scrollY);
                ticking = false;
            });
            ticking = true;
        }
    }

    function updateTopbar(scrollY) {
        if (!elements.topbar) return;

        const shouldHide = scrollY > 50;

        if (shouldHide && topbarVisible) {
            // Efektsiz kaydır
            elements.topbar.style.transform = 'translateY(-100%)';
            topbarVisible = false;
        } else if (!shouldHide && !topbarVisible) {
            // Efektsiz getir
            elements.topbar.style.transform = 'translateY(0)';
            topbarVisible = true;
        }
    }

    function updateTOC(scrollY) {
        if (!elements.tocBar) return;

        const shouldStick = scrollY >= tocOriginalOffset;

        // TOC'un ALTININ FAQ BAŞINA değip değmediğini kontrol et
        const tocHeight = elements.tocBar.offsetHeight;
        const tocBottom = scrollY + HEADER_HEIGHT + tocHeight;
        const shouldStop = tocBottom >= faqStartOffset;

        // Sticky/Static/Stop pozisyon kontrolü
        if (!shouldStick) {
            // Normal mode - sticky değil
            elements.tocBar.style.position = 'static';
            elements.tocBar.style.top = 'auto';
            elements.tocBar.style.transform = 'translateY(0)';
            tocVisible = true;
            tocSticky = false;
        } else if (shouldStop) {
            // Stop mode - FAQ BAŞINDA dur (fixed kalsın ama top sabit)
            const stoppedTop = faqStartOffset - scrollY - tocHeight;
            elements.tocBar.style.position = 'fixed';
            elements.tocBar.style.top = stoppedTop + 'px';
            elements.tocBar.style.left = '0';
            elements.tocBar.style.right = '0';
            elements.tocBar.style.transform = 'translateY(0)';
            tocVisible = true;
            tocSticky = true;
        } else {
            // Sticky mode - takip et
            elements.tocBar.style.position = 'fixed';
            elements.tocBar.style.top = HEADER_HEIGHT + 'px';
            elements.tocBar.style.left = '0';
            elements.tocBar.style.right = '0';
            elements.tocBar.style.transform = 'translateY(0)';
            tocVisible = true;
            tocSticky = true;
        }
    }

    function updateSidebar(scrollY) {
        if (!elements.sidebar || !elements.tocBar) return;

        // TOC height hesapla
        const tocHeight = tocSticky ? elements.tocBar.offsetHeight : 0;

        // Sidebar sticky top position (TOC altında boşluk)
        const stickyTop = HEADER_HEIGHT + tocHeight + TOC_SIDEBAR_GAP;

        // Sidebar sticky başlamalı mı? (TOC sticky ise)
        const shouldStick = tocSticky;

        // Sidebar'ın ALTININ FAQ BAŞINA değip değmediğini kontrol et
        const sidebarHeight = elements.sidebar.offsetHeight;
        const sidebarBottom = scrollY + stickyTop + sidebarHeight;
        const shouldStop = shouldStick && (sidebarBottom >= faqStartOffset);

        if (!shouldStick) {
            // Normal mode - TOC henüz sticky değil
            elements.sidebar.style.position = 'static';
            elements.sidebar.style.top = 'auto';
            elements.sidebar.style.width = 'auto';
            elements.sidebar.style.left = 'auto';
            elements.sidebar.style.opacity = '1';
            elements.sidebar.style.visibility = 'visible';
            sidebarSticky = false;
            sidebarVisible = true;
            // Cache'i sıfırla
            cachedSidebarWidth = 0;
            cachedSidebarLeft = 0;
        } else {
            // Sticky mode - cache width/left (sadece ilk sticky'de)
            if (!sidebarSticky || cachedSidebarWidth === 0) {
                const sidebarParent = elements.sidebar.parentElement;
                const parentRect = sidebarParent.getBoundingClientRect();
                cachedSidebarWidth = parentRect.width;
                cachedSidebarLeft = parentRect.left;
            }

            if (shouldStop) {
                // Stop mode - FAQ BAŞINDA dur (fixed kalsın ama top sabit)
                const stoppedTop = faqStartOffset - scrollY - sidebarHeight;
                elements.sidebar.style.position = 'fixed';
                elements.sidebar.style.top = stoppedTop + 'px';
                elements.sidebar.style.width = cachedSidebarWidth + 'px';
                elements.sidebar.style.left = cachedSidebarLeft + 'px';
                elements.sidebar.style.opacity = '1';
                elements.sidebar.style.visibility = 'visible';
                sidebarVisible = true;
            } else {
                // Sticky mode - TOC altında takip et
                elements.sidebar.style.position = 'fixed';
                elements.sidebar.style.top = stickyTop + 'px';
                elements.sidebar.style.width = cachedSidebarWidth + 'px';
                elements.sidebar.style.left = cachedSidebarLeft + 'px';
                elements.sidebar.style.opacity = '1';
                elements.sidebar.style.visibility = 'visible';
                sidebarVisible = true;
            }

            sidebarSticky = true;
        }
    }

    // Start
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
