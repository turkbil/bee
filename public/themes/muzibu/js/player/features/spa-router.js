/**
 * Muzibu SPA Router
 * Handles SPA navigation with auth page bypass
 *
 * Dependencies: player-core.js (for this.* context)
 */

// 🛡️ GUARD: Prevent redeclaration on SPA navigation
if (typeof MuzibuSpaRouter !== 'undefined') {
    console.log('⚠️ MuzibuSpaRouter already loaded, skipping...');
} else {

const MuzibuSpaRouter = {
    // Auth pages that should NOT use SPA navigation
    authPaths: ['/login', '/register', '/forgot-password', '/reset-password', '/verify-email', '/logout'],

    // 🚀 PREFETCH SYSTEM
    prefetchCache: new Map(), // URL → {html, timestamp}
    prefetchQueue: new Set(), // URLs being prefetched
    cacheTimeout: 30 * 60 * 1000, // 30 minutes (5 dakika → 30 dakika artırıldı)
    maxCacheSize: 50, // 🎯 MAX 50 pages (25 → 50 artırıldı)
    observer: null, // Intersection Observer for viewport prefetch

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
                console.log('⏭️ Event already prevented (preview mode), skipping SPA navigation');
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

            // Check if external link (different domain)
            if (href.startsWith('http') || href.startsWith('//')) {
                try {
                    const linkUrl = new URL(href, window.location.origin);
                    if (linkUrl.origin !== window.location.origin) {
                        return; // External link, let it navigate normally
                    }
                } catch (e) {
                    return; // Invalid URL, let it navigate normally
                }
            }

            // AUTH PAGES BYPASS: Bu sayfalar farklı layout kullanıyor
            const urlPath = href.startsWith('http') ? new URL(href).pathname : href.split('?')[0];
            if (this.isAuthPath(urlPath)) {
                console.log('🔐 Auth page detected, bypassing SPA:', href);
                return; // Full page navigation for auth pages
            }

            // Internal link - use SPA navigation
            console.log('🚀 SPA Navigation:', href);
            e.preventDefault();
            // 🔧 FIX: Call loadPage directly (spread operator loses 'this' context)
            MuzibuSpaRouter.navigateTo.call(this, href);
        });

        // 🔥 VIEWPORT PREFETCH: DISABLED (gereksiz network trafiği)
        // this.initViewportPrefetch();

        // ⚡ HOVER PREFETCH: DISABLED (kullanıcı isteği)
        // this.initHoverPrefetch();

        console.log('🚀 SPA Router initialized (Prefetch Disabled)');
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
                console.log(`✅ Already cached (${source}):`, url);
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
        console.log(`⚡ Prefetching (${source}):`, url);

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
                    console.log(`🗑️ LRU eviction: Removed oldest cache entry (${oldestKey})`);
                }

                this.prefetchCache.set(url, {
                    html: html,
                    timestamp: Date.now()
                });
                console.log(`✅ Prefetched (${source}):`, url);
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
     * Navigate to URL using SPA
     */
    async navigateTo(url) {
        // ⚡ SMART LOADING: Only show overlay if loading takes > 200ms
        console.log('🔄 navigateTo() called');

        history.pushState({ url: url }, '', url);
        // 🔧 FIX: Use MuzibuSpaRouter.loadPage with correct context
        await MuzibuSpaRouter.loadPage.call(this, url, true);
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
        console.log('🔵 loadPage() START:', url);
        const loadStartTime = Date.now();
        const minLoadingTime = 0; // ⚡ PERFORMANCE: No minimum delay (instant loading!)
        const maxLoadingTime = 10000; // ⏱️ 10 second timeout
        const loadingThreshold = 0; // 🔄 INSTANT: Show loading immediately (user feedback)

        // 🔄 INSTANT LOADING OVERLAY: Show immediately for user feedback
        let loadingTimeout = setTimeout(() => {
            this.isLoading = true;
        }, loadingThreshold);

        try {
            console.log('🔵 loadPage() TRY block entered');

            let html;
            let fetchPromise;

            // 🚀 CHECK CACHE FIRST (instant navigation!)
            const cached = this.prefetchCache.get(url);
            if (cached) {
                const age = Date.now() - cached.timestamp;
                if (age < this.cacheTimeout) {
                    console.log('⚡ Using cached page (instant!):', url);
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

            // ⏱️ TIMEOUT: Race between fetch and timeout
            const timeoutPromise = new Promise((_, reject) => {
                setTimeout(() => reject(new Error('Page load timeout')), maxLoadingTime);
            });

            html = await Promise.race([fetchPromise, timeoutPromise]);

            // Parse HTML and extract main content
            console.log('🔵 loadPage() Parsing HTML...');
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newContent = doc.querySelector('main');

            console.log('🔵 loadPage() Main element found:', !!newContent);

            if (newContent) {
                console.log('🔵 loadPage() Entering main content update...');
                const currentMain = document.querySelector('main');
                if (currentMain) {
                    // 🛡️ SECURITY: Clone content and remove all script tags to prevent duplicate execution
                    // This is the industry-standard approach used by Google, Facebook, etc.
                    const clonedContent = newContent.cloneNode(true);

                    // Remove ALL script tags from cloned content
                    clonedContent.querySelectorAll('script').forEach(script => script.remove());

                    // ⚡ PERFORMANCE: No artificial delay - instant loading!
                    // (minLoadingTime = 0, so no waiting)

                    // Safely replace content using modern DOM API (prevents script execution)
                    currentMain.replaceChildren(...clonedContent.childNodes);

                    window.scrollTo({ top: 0, behavior: 'smooth' });

                    const newTitle = doc.querySelector('title');
                    if (newTitle) {
                        document.title = newTitle.textContent;
                    }

                    // 🎯 UPDATE RIGHT SIDEBAR: Handle sidebar visibility for music vs non-music pages
                    const newAside = doc.querySelector('aside.muzibu-right-sidebar');
                    const currentAside = document.querySelector('aside.muzibu-right-sidebar');
                    const mainGrid = document.querySelector('#main-app-grid');

                    console.log('🔍 SPA: Sidebar check - New:', !!newAside, 'Current:', !!currentAside);

                    if (newAside) {
                        // New page HAS sidebar
                        const clonedAside = newAside.cloneNode(true);
                        clonedAside.querySelectorAll('script').forEach(script => script.remove());

                        if (currentAside) {
                            // Replace existing sidebar
                            console.log('✅ SPA: Replacing existing sidebar');
                            currentAside.replaceWith(clonedAside);
                        } else {
                            // Insert new sidebar (before player)
                            console.log('➕ SPA: Adding new sidebar');
                            const player = document.querySelector('.muzibu-player');
                            if (player && mainGrid) {
                                mainGrid.insertBefore(clonedAside, player);
                            } else {
                                console.error('❌ SPA: Player not found, cannot insert sidebar');
                            }
                        }
                    } else {
                        // New page has NO sidebar - remove it
                        if (currentAside) {
                            console.log('🗑️ SPA: Removing sidebar');
                            currentAside.remove();
                        } else {
                            console.log('ℹ️ SPA: No sidebar to remove');
                        }
                    }

                    // 🎯 UPDATE GRID LAYOUT: Sync grid classes from new page
                    const newGrid = doc.querySelector('#main-app-grid');
                    if (newGrid && mainGrid) {
                        // Extract grid-cols classes from new page
                        const newClasses = newGrid.className;
                        const currentClasses = mainGrid.className;

                        console.log('🔍 SPA: Grid classes - Old:', currentClasses);
                        console.log('🔍 SPA: Grid classes - New:', newClasses);

                        // Copy all classes from new grid
                        mainGrid.className = newClasses;

                        console.log('✅ SPA: Grid classes updated');
                    }

                    this.currentPath = url;
                    console.log('✅ Page loaded:', url);

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

            console.log('✅ loadPage complete, setting isLoading = false');

            // ⚡ Cancel loading timeout if still pending
            clearTimeout(loadingTimeout);
            this.isLoading = false;

            console.log('🔍 Final isLoading state:', this.isLoading);
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
