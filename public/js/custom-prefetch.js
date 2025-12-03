/**
 * Custom Prefetch - Manual Implementation
 * Hover'da anında prefetch yapar
 */

(function() {
    'use strict';

    // Prefetch desteği var mı?
    if (!document.createElement('link').relList.supports('prefetch')) {
        return;
    }

    const prefetchedUrls = new Set();
    let prefetchDelay = 0; // 0ms = anında

    function prefetchUrl(url) {
        if (prefetchedUrls.has(url)) {
            return; // Zaten prefetch edildi
        }

        const link = document.createElement('link');
        link.rel = 'prefetch';
        link.href = url;
        link.as = 'document';
        document.head.appendChild(link);
        prefetchedUrls.add(url);
    }

    function shouldPrefetch(anchor) {
        if (!anchor || !anchor.href) return false;

        // External link kontrolü
        if (anchor.origin !== window.location.origin) return false;

        // Hash-only link
        if (anchor.pathname === window.location.pathname && anchor.search === window.location.search) return false;

        // data-no-prefetch attribute
        if (anchor.hasAttribute('data-no-prefetch')) return false;

        return true;
    }

    // DOM hazır olunca linkleri dinle
    function initPrefetch() {
        const links = document.querySelectorAll('a[href]');

        links.forEach(anchor => {
            if (!shouldPrefetch(anchor)) return;

            let timer = null;

            // Hover event
            anchor.addEventListener('mouseenter', function() {
                timer = setTimeout(() => {
                    prefetchUrl(this.href);
                }, prefetchDelay);
            }, { passive: true });

            // Mouse leave - cancel
            anchor.addEventListener('mouseleave', function() {
                if (timer) {
                    clearTimeout(timer);
                    timer = null;
                }
            }, { passive: true });

            // Mobile: touchstart
            anchor.addEventListener('touchstart', function() {
                prefetchUrl(this.href);
            }, { passive: true, once: true });
        });
    }

    // DOMContentLoaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPrefetch);
    } else {
        initPrefetch();
    }
})();
