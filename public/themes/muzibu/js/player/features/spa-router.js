/**
 * Muzibu SPA Router
 * Handles SPA navigation with auth page bypass
 *
 * Dependencies: player-core.js (for this.* context)
 */

// 🛡️ GUARD: Prevent redeclaration on SPA navigation (silent)
if (typeof MuzibuSpaRouter !== 'undefined') {
    // Already loaded - skip silently
} else {

const MuzibuSpaRouter = {
    // Auth pages that should NOT use SPA navigation
    // Also includes pages with inline Alpine.js components that need full page load
    authPaths: ['/login', '/register', '/forgot-password', '/reset-password', '/verify-email', '/logout'],

    // 🔴 DYNAMIC PAGES: User-specific content - NEVER cache!
    // Bu sayfalar kullanıcıya özel içerik gösterir, cache'lenmemeli
    dynamicPaths: ['/muzibu/favorites', '/muzibu/my-playlists', '/dashboard', '/muzibu/listening-history', '/corporate', '/muzibu/corporate-playlists', '/cart', '/checkout', '/my-subscriptions', '/subscription-success'],

    // 🚀 PREFETCH SYSTEM
    prefetchCache: new Map(), // URL → {html, timestamp}
    prefetchQueue: new Set(), // URLs being prefetched
    cacheTimeout: 30 * 60 * 1000, // 30 minutes (5 dakika → 30 dakika artırıldı)
    maxCacheSize: 50, // 🎯 MAX 50 pages (25 → 50 artırıldı)
    observer: null, // Intersection Observer for viewport prefetch

    // 🛡️ CLICK PROTECTION: Prevent accidental clicks after SPA navigation
    lastNavigationTime: 0, // Timestamp of last DOM change
    clickProtectionMs: 200, // Block clicks for 200ms after navigation

    /**
     * Check if we're in click protection period (just after SPA navigation)
     * Used by playContent to prevent accidental plays
     */
    isClickProtected() {
        const elapsed = Date.now() - this.lastNavigationTime;
        return elapsed < this.clickProtectionMs;
    },

    // 🛡️ SINGLETON: Prevent multiple initializations
    isInitialized: false,
    clickHandler: null,
    popstateHandler: null,
    hoverHandler: null,

    /**
     * Initialize SPA navigation handlers (singleton - only once!)
     */
    initSpaNavigation() {
        // 🛡️ Prevent multiple initializations
        if (this.isInitialized) {
            console.warn('⚠️ SPA Router already initialized, skipping...');
            return;
        }
        this.isInitialized = true;
        // Handle browser back/forward
        window.addEventListener('popstate', (e) => {
            if (e.state && e.state.url) {
                MuzibuSpaRouter.loadPage.call(this, e.state.url, false);
            }
        });

        // Intercept all internal links
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a');
            if (!link) return;

            // 🎯 PREVIEW MODE: If event already prevented (by Alpine @click for XL+ preview), skip SPA navigation
            if (e.defaultPrevented) {
                return;
            }

            const href = link.getAttribute('href');

            // Skip if no href, hash link, or has download/target attribute
            if (!href ||
                href.startsWith('#') ||
                link.hasAttribute('download') ||
                link.hasAttribute('target')) {
                return;
            }

            // 🔥 SKIP SPA: data-spa="false" attribute bypasses SPA navigation
            if (link.getAttribute('data-spa') === 'false') {
                return; // Full page navigation
            }

            // Check if external link (different domain)
            if (href.startsWith('http') || href.startsWith('//')) {
                try {
                    const linkUrl = new URL(href, window.location.origin);
                    // 🔥 FIX: www-tolerant origin check (www.domain.com == domain.com)
                    // Laravel route() helper bazen www'suz URL üretiyor
                    const normalizeOrigin = (origin) => origin.replace('://www.', '://');
                    if (normalizeOrigin(linkUrl.origin) !== normalizeOrigin(window.location.origin)) {
                        return; // External link, let it navigate normally
                    }
                } catch (e) {
                    return; // Invalid URL, let it navigate normally
                }
            }

            // AUTH PAGES BYPASS: Bu sayfalar farklı layout kullanıyor
            const urlPath = href.startsWith('http') ? new URL(href).pathname : href.split('?')[0];
            if (this.isAuthPath(urlPath)) {
                return; // Full page navigation for auth pages
            }

            // Internal link - use SPA navigation
            e.preventDefault();
            // 🔧 FIX: Call loadPage directly (spread operator loses 'this' context)
            MuzibuSpaRouter.navigateTo.call(this, href);
        });

        // 🔥 VIEWPORT PREFETCH: DISABLED (gereksiz network trafiği)
        // this.initViewportPrefetch();

        // ⚡ HOVER PREFETCH: DISABLED (kullanıcı isteği)
        // this.initHoverPrefetch();
    },

    /**
     * 🔥 VIEWPORT PREFETCH: Prefetch links when they enter viewport
     */
    initViewportPrefetch() {
        this.observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const link = entry.target;
                    const href = link.getAttribute('href');
                    if (href && this.shouldPrefetch(href)) {
                        this.prefetch(href, 'viewport');
                    }
                }
            });
        }, {
            rootMargin: '50px' // Start prefetching 50px before link is visible
        });

        // Observe all internal links
        this.observeLinks();
    },

    /**
     * ⚡ HOVER PREFETCH: Prefetch on mouseenter (instant navigation)
     */
    initHoverPrefetch() {
        document.addEventListener('mouseenter', (e) => {
            // Check if target is an Element (not text node)
            if (!e.target || !e.target.closest) return;

            const link = e.target.closest('a');
            if (!link) return;

            const href = link.getAttribute('href');
            if (href && this.shouldPrefetch(href)) {
                this.prefetch(href, 'hover');
            }
        }, true); // Use capture to catch all links
    },

    /**
     * Observe all current links for viewport prefetch
     */
    observeLinks() {
        // Safety check: ensure observer exists
        if (!this.observer) {
            console.warn('⚠️ Intersection Observer not initialized, skipping link observation');
            return;
        }

        document.querySelectorAll('a[href]').forEach(link => {
            // 🛡️ Skip if already observed (prevent duplicate observations)
            if (link.dataset.spaObserved === 'true') {
                return;
            }

            const href = link.getAttribute('href');
            if (href && this.shouldPrefetch(href)) {
                this.observer.observe(link);
                link.dataset.spaObserved = 'true'; // Mark as observed
            }
        });
    },

    /**
     * Check if URL should be prefetched
     */
    shouldPrefetch(href) {
        // Skip if already cached or in queue
        if (this.prefetchCache.has(href) || this.prefetchQueue.has(href)) {
            return false;
        }

        // Skip hash links, downloads, external links
        if (!href ||
            href.startsWith('#') ||
            href.startsWith('mailto:') ||
            href.startsWith('tel:')) {
            return false;
        }

        // Skip external links
        if (href.startsWith('http') || href.startsWith('//')) {
            try {
                const linkUrl = new URL(href, window.location.origin);
                if (linkUrl.origin !== window.location.origin) {
                    return false;
                }
            } catch (e) {
                return false;
            }
        }

        // Skip auth pages
        const urlPath = href.startsWith('http') ? new URL(href).pathname : href.split('?')[0];
        if (this.isAuthPath(urlPath)) {
            return false;
        }

        // 🔴 SKIP DYNAMIC PAGES: User-specific content should NEVER be prefetched
        // Favoriler, playlistler, dashboard vb. her zaman taze veri çekmeli
        if (this.isDynamicPath(urlPath)) {
            return false;
        }

        return true;
    },

    /**
     * 🚀 PREFETCH: Fetch and cache page
     */
    async prefetch(url, source = 'unknown') {
        // 🛡️ VALIDATE URL: Ignore invalid/special URLs
        if (!url ||
            url.startsWith('javascript:') ||
            url.startsWith('#') ||
            url.startsWith('mailto:') ||
            url.startsWith('tel:') ||
            url === '#' ||
            url.trim() === '') {
            return; // Silently ignore
        }

        // Check cache first
        const cached = this.prefetchCache.get(url);
        if (cached) {
            const age = Date.now() - cached.timestamp;
            if (age < this.cacheTimeout) {
                return;
            }
            // Cache expired, remove it
            this.prefetchCache.delete(url);
        }

        // Check if already prefetching
        if (this.prefetchQueue.has(url)) {
            return;
        }

        this.prefetchQueue.add(url);

        try {
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                },
                priority: source === 'hover' ? 'high' : 'low' // High priority for hover
            });

            if (response.ok) {
                const html = await response.text();

                // 🎯 LRU: Remove oldest entry if cache is full
                if (this.prefetchCache.size >= this.maxCacheSize) {
                    const oldestKey = this.prefetchCache.keys().next().value;
                    this.prefetchCache.delete(oldestKey);
                }

                this.prefetchCache.set(url, {
                    html: html,
                    timestamp: Date.now()
                });
            }
        } catch (error) {
            console.warn(`⚠️ Prefetch failed (${source}):`, url, error);
        } finally {
            this.prefetchQueue.delete(url);
        }
    },

    /**
     * Check if path is an auth page
     */
    isAuthPath(path) {
        return this.authPaths.some(authPath => path === authPath || path.startsWith(authPath + '/'));
    },

    /**
     * 🔴 Check if path is a dynamic page (user-specific, should NOT be cached)
     * Bu sayfalar kullanıcıya özel içerik gösterir: favoriler, playlistler, dashboard vb.
     */
    isDynamicPath(path) {
        return this.dynamicPaths.some(dynamicPath => path === dynamicPath || path.startsWith(dynamicPath + '/') || path.startsWith(dynamicPath + '?'));
    },

    /**
     * 🧹 Clear cache for dynamic pages
     * Favori ekleme/çıkarma gibi işlemlerden sonra çağrılır
     */
    clearDynamicCache() {
        let cleared = 0;
        for (const [url] of this.prefetchCache) {
            try {
                const urlPath = new URL(url, window.location.origin).pathname;
                if (this.isDynamicPath(urlPath)) {
                    this.prefetchCache.delete(url);
                    cleared++;
                }
            } catch (e) {
                // Invalid URL, skip
            }
        }
    },

    /**
     * Navigate to URL using SPA
     */
    async navigateTo(url) {
        // 🔥 FIX: URL'yi mevcut origin'e normalize et (CORS sorunu önleme)
        // Laravel route() www'suz URL üretebilir, biz www'lu origin'deyiz
        let normalizedUrl = url;
        if (url.startsWith('http')) {
            try {
                const urlObj = new URL(url);
                const currentOrigin = window.location.origin;
                // Sadece path + query + hash'i al, origin'i mevcut ile değiştir
                normalizedUrl = currentOrigin + urlObj.pathname + urlObj.search + urlObj.hash;
            } catch (e) {
                // URL parse hatası, orijinali kullan
            }
        }

        history.pushState({ url: normalizedUrl }, '', normalizedUrl);
        // 🔧 FIX: Use MuzibuSpaRouter.loadPage with correct context
        await MuzibuSpaRouter.loadPage.call(this, normalizedUrl, true);
    },

    /**
     * Fetch page from server
     */
    async fetchPage(url) {
        const response = await fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        return await response.text();
    },

    /**
     * Load page content via AJAX (uses cache if available)
     */
    async loadPage(url, addToHistory = true) {
        const loadStartTime = Date.now();
        const minLoadingTime = 0; // ⚡ PERFORMANCE: No minimum delay (instant loading!)
        const maxLoadingTime = 10000; // ⏱️ 10 second timeout
        const loadingThreshold = 0; // 🔄 INSTANT: Show loading immediately (user feedback)

        // 🔄 INSTANT LOADING OVERLAY: Show immediately for user feedback
        let loadingTimeout = setTimeout(() => {
            this.isLoading = true;
        }, loadingThreshold);

        try {
            let html;
            let fetchPromise;

            // 🔴 DYNAMIC PAGES: Always fetch fresh (never use cache!)
            // Favoriler, playlistler, dashboard vb. kullanıcıya özel içerik
            const urlPath = new URL(url, window.location.origin).pathname;
            const isDynamic = this.isDynamicPath(urlPath);

            if (isDynamic) {
                // Remove from cache if exists (stale data)
                this.prefetchCache.delete(url);
                fetchPromise = this.fetchPage(url);
            } else {
                // 🚀 CHECK CACHE FIRST (instant navigation!)
                const cached = this.prefetchCache.get(url);
                if (cached) {
                    const age = Date.now() - cached.timestamp;
                    if (age < this.cacheTimeout) {
                        // ⚡ INSTANT: Cancel loading timeout immediately (no overlay needed!)
                        clearTimeout(loadingTimeout);
                        this.isLoading = false; // Cache hit - loading gösterme!
                        html = cached.html;
                        fetchPromise = Promise.resolve(html);
                    } else {
                        // Cache expired, fetch fresh
                        this.prefetchCache.delete(url);
                        fetchPromise = this.fetchPage(url);
                    }
                } else {
                    // Not cached, fetch now
                    fetchPromise = this.fetchPage(url);
                }
            }

            // ⏱️ TIMEOUT: Race between fetch and timeout
            const timeoutPromise = new Promise((_, reject) => {
                setTimeout(() => reject(new Error('Page load timeout')), maxLoadingTime);
            });

            html = await Promise.race([fetchPromise, timeoutPromise]);

            // Parse HTML and extract main content
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newContent = doc.querySelector('main');

            if (newContent) {
                const currentMain = document.querySelector('main');
                if (currentMain) {
                    // 🔥 FIX: Destroy Alpine.js components before replacing DOM (prevent $nextTick redefine error)
                    // Alpine.js magic properties ($nextTick, $watch, etc.) can't be redefined, so we must clean up first
                    if (window.Alpine && typeof window.Alpine.destroyTree === 'function') {
                        try {
                            // Destroy all Alpine components in current main
                            currentMain.querySelectorAll('[x-data]').forEach(el => {
                                window.Alpine.destroyTree(el);
                            });
                        } catch (e) {
                            console.warn('⚠️ Alpine.js cleanup failed:', e.message);
                        }
                    }

                    // 🛡️ SECURITY: Clone content and remove all script tags to prevent duplicate execution
                    // This is the industry-standard approach used by Google, Facebook, etc.
                    const clonedContent = newContent.cloneNode(true);

                    // Remove ALL script tags from cloned content
                    clonedContent.querySelectorAll('script').forEach(script => script.remove());

                    // ⚡ PERFORMANCE: No artificial delay - instant loading!
                    // (minLoadingTime = 0, so no waiting)

                    // Safely replace content using modern DOM API (prevents script execution)
                    currentMain.replaceChildren(...clonedContent.childNodes);

                    // 🔥 FIX: Initialize Alpine.js on new main content (for x-data, x-bind, etc.)
                    if (window.Alpine && typeof window.Alpine.initTree === 'function') {
                        try {
                            window.Alpine.initTree(currentMain);
                        } catch (e) {
                            console.warn('⚠️ Main Alpine.js init failed:', e.message);
                        }
                    }

                    // 🛡️ CLICK PROTECTION: Set navigation time to prevent accidental clicks
                    MuzibuSpaRouter.lastNavigationTime = Date.now();

                    window.scrollTo({ top: 0, behavior: 'smooth' });

                    const newTitle = doc.querySelector('title');
                    if (newTitle) {
                        document.title = newTitle.textContent;
                    }

                    // 🎯 UPDATE RIGHT SIDEBAR: Handle sidebar visibility for music vs non-music pages
                    const newAside = doc.querySelector('aside.muzibu-right-sidebar');
                    const currentAside = document.querySelector('aside.muzibu-right-sidebar');
                    const mainGrid = document.querySelector('#main-app-grid');

                    if (newAside) {
                        // New page HAS sidebar
                        const clonedAside = newAside.cloneNode(true);
                        clonedAside.querySelectorAll('script').forEach(script => script.remove());

                        if (currentAside) {
                            // 🔥 FIX: Destroy Alpine.js components in sidebar before replacing
                            if (window.Alpine && typeof window.Alpine.destroyTree === 'function') {
                                try {
                                    currentAside.querySelectorAll('[x-data]').forEach(el => {
                                        window.Alpine.destroyTree(el);
                                    });
                                } catch (e) {
                                    console.warn('⚠️ Sidebar Alpine.js cleanup failed:', e.message);
                                }
                            }

                            // Replace existing sidebar
                            currentAside.replaceWith(clonedAside);

                            // 🔥 FIX: Initialize Alpine.js on new sidebar
                            if (window.Alpine && typeof window.Alpine.initTree === 'function') {
                                try {
                                    window.Alpine.initTree(clonedAside);
                                } catch (e) {
                                    console.warn('⚠️ Sidebar Alpine.js init failed:', e.message);
                                }
                            }
                        } else {
                            // Insert new sidebar (before player)
                            const player = document.querySelector('.muzibu-player');
                            if (player && mainGrid) {
                                mainGrid.insertBefore(clonedAside, player);

                                // 🔥 FIX: Initialize Alpine.js on new sidebar
                                if (window.Alpine && typeof window.Alpine.initTree === 'function') {
                                    try {
                                        window.Alpine.initTree(clonedAside);
                                    } catch (e) {
                                        console.warn('⚠️ Sidebar Alpine.js init failed:', e.message);
                                    }
                                }
                            } else {
                                console.error('❌ SPA: Player not found, cannot insert sidebar');
                            }
                        }
                    } else {
                        // New page has NO sidebar - remove it
                        if (currentAside) {
                            // 🔥 FIX: Destroy Alpine.js components before removing sidebar
                            if (window.Alpine && typeof window.Alpine.destroyTree === 'function') {
                                try {
                                    currentAside.querySelectorAll('[x-data]').forEach(el => {
                                        window.Alpine.destroyTree(el);
                                    });
                                } catch (e) {
                                    console.warn('⚠️ Sidebar Alpine.js cleanup failed:', e.message);
                                }
                            }

                            currentAside.remove();
                        }
                    }

                    this.currentPath = url;

                    // 🏠 HOMEPAGE NAVIGATION: Reset sidebar to default state
                    const urlPath = new URL(url, window.location.origin).pathname;
                    if (urlPath === '/' || urlPath === '') {
                        // Anasayfaya dönüldü → Sidebar preview mode'dan çık
                        if (window.Alpine?.store('sidebar')) {
                            window.Alpine.store('sidebar').reset();
                        }
                    }

                    // 🚀 UPDATE RIGHT SIDEBAR VISIBILITY: Force Alpine reactivity
                    // Problem: If rightSidebarVisible doesn't change, Alpine x-bind:class won't update
                    // Solution: Toggle value to force reactivity, then set correct value
                    if (window.Alpine?.store('sidebar')) {
                        const sidebar = window.Alpine.store('sidebar');
                        const currentValue = sidebar.rightSidebarVisible;

                        // Force Alpine reactivity by toggling value
                        sidebar.rightSidebarVisible = !currentValue;

                        // Then set correct value based on route
                        sidebar.updateRightSidebarVisibility();
                    }

                    // 🔥 RE-OBSERVE NEW LINKS: DISABLED (viewport prefetch kapatıldı)
                    // setTimeout(() => this.observeLinks(), 100);
                }
            } else {
                // Main content not found = farklı layout (auth pages gibi)
                // Full page reload yap, sonsuz döngüye girme!
                console.warn('Main content not found, falling back to full page reload:', url);
                this.isLoading = false;
                window.location.href = url;
                return;
            }

            // ⚡ Cancel loading timeout if still pending
            clearTimeout(loadingTimeout);
            this.isLoading = false;
        } catch (error) {
            // ⚡ Cancel loading timeout on error
            clearTimeout(loadingTimeout);
            console.error('❌ Failed to load page:', error);

            // User-friendly error messages
            if (error.message === 'Page load timeout') {
                this.showToast('Sayfa yüklenemedi (zaman aşımı)', 'error');
            } else if (!navigator.onLine) {
                this.showToast('İnternet bağlantınızı kontrol edin', 'error');
            } else {
                this.showToast('Sayfa yüklenemedi', 'error');
            }

            this.isLoading = false;

            // Fallback to full page reload on error
            setTimeout(() => {
                window.location.href = url;
            }, 1000);
        }
    }
};

// Export for use in player-core.js
window.MuzibuSpaRouter = MuzibuSpaRouter;

} // END GUARD
