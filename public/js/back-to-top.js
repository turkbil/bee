/**
 * Back to Top Button - Smart & Smooth
 *
 * Özellikler:
 * - Scroll > 300px sonra görünür
 * - Smooth scroll to top
 * - Keyboard support (Home tuşu)
 * - Performance optimized (requestAnimationFrame)
 *
 * @version 1.0.0
 */

(function() {
    'use strict';

    // Back to top butonu oluştur
    function createBackToTopButton() {
        const button = document.createElement('button');
        button.className = 'back-to-top';
        button.setAttribute('aria-label', 'Başa Dön');
        button.setAttribute('title', 'Başa Dön (Home)');
        button.innerHTML = '<i class="fas fa-arrow-up"></i>';

        document.body.appendChild(button);
        return button;
    }

    // Smooth scroll to top
    function scrollToTop() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    // Scroll pozisyonu kontrol et (throttled)
    let ticking = false;
    function checkScrollPosition(button) {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

        if (!ticking) {
            window.requestAnimationFrame(() => {
                if (scrollTop > 300) {
                    button.classList.add('show');
                } else {
                    button.classList.remove('show');
                }
                ticking = false;
            });
            ticking = true;
        }
    }

    // Initialize
    function init() {
        const button = createBackToTopButton();

        // Click event
        button.addEventListener('click', scrollToTop);

        // Scroll event (optimized)
        window.addEventListener('scroll', () => checkScrollPosition(button), { passive: true });

        // Keyboard support (Home key)
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Home' && !e.ctrlKey && !e.shiftKey) {
                // Sadece input/textarea dışında
                if (!['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) {
                    e.preventDefault();
                    scrollToTop();
                }
            }
        });

        // İlk yüklemede kontrol et
        checkScrollPosition(button);
    }

    // DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    console.log('[Back to Top] Initialized - Position: Left Bottom');
})();
