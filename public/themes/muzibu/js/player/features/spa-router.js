/**
 * Muzibu SPA Router
 * Handles SPA navigation with auth page bypass
 *
 * Dependencies: player-core.js (for this.* context)
 */

const MuzibuSpaRouter = {
    // Auth pages that should NOT use SPA navigation
    authPaths: ['/login', '/register', '/forgot-password', '/reset-password', '/verify-email', '/logout'],

    // 🚀 PREFETCH SYSTEM
    prefetchCache: new Map(), // URL → {html, timestamp}
    prefetchQueue: new Set(), // URLs being prefetched
    cacheTimeout: 5 * 60 * 1000, // 5 minutes
    maxCacheSize: 25, // 🎯 MAX 25 pages - Daha az LRU eviction
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
                this.loadPage(e.state.url, false);
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
            this.navigateTo(href);
        });

        // 🔥 VIEWPORT PREFETCH: Intersection Observer
        this.initViewportPrefetch();

        // ⚡ HOVER PREFETCH: Mouse enter
        this.initHoverPrefetch();

        console.log('🚀 SPA Router initialized (with Viewport + Hover Prefetch)');
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
            const href = link.getAttribute('href');
            if (href && this.shouldPrefetch(href)) {
                this.observer.observe(link);
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
        history.pushState({ url: url }, '', url);
        await this.loadPage(url, true);
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
        try {
            this.isLoading = true;

            let html;

            // 🚀 CHECK CACHE FIRST (instant navigation!)
            const cached = this.prefetchCache.get(url);
            if (cached) {
                const age = Date.now() - cached.timestamp;
                if (age < this.cacheTimeout) {
                    console.log('⚡ Using cached page (instant!):', url);
                    html = cached.html;
                } else {
                    // Cache expired, fetch fresh
                    this.prefetchCache.delete(url);
                    html = await this.fetchPage(url);
                }
            } else {
                // Not cached, fetch now
                html = await this.fetchPage(url);
            }

            // Parse HTML and extract main content
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newContent = doc.querySelector('main');

            if (newContent) {
                const currentMain = document.querySelector('main');
                if (currentMain) {
                    // 🛡️ SECURITY: Clone content and remove all script tags to prevent duplicate execution
                    // This is the industry-standard approach used by Google, Facebook, etc.
                    const clonedContent = newContent.cloneNode(true);

                    // Remove ALL script tags from cloned content
                    clonedContent.querySelectorAll('script').forEach(script => script.remove());

                    // Safely replace content using modern DOM API (prevents script execution)
                    currentMain.replaceChildren(...clonedContent.childNodes);

                    window.scrollTo({ top: 0, behavior: 'smooth' });

                    const newTitle = doc.querySelector('title');
                    if (newTitle) {
                        document.title = newTitle.textContent;
                    }

                    this.currentPath = url;
                    console.log('Page loaded:', url);

                    // 🔥 RE-OBSERVE NEW LINKS for viewport prefetch
                    setTimeout(() => this.observeLinks(), 100);
                }
            } else {
                // Main content not found = farklı layout (auth pages gibi)
                // Full page reload yap, sonsuz döngüye girme!
                console.warn('Main content not found, falling back to full page reload:', url);
                this.isLoading = false;
                window.location.href = url;
                return;
            }

            this.isLoading = false;
        } catch (error) {
            console.error('Failed to load page:', error);
            this.showToast('Sayfa yüklenemedi', 'error');
            this.isLoading = false;

            // Fallback to full page reload on error
            window.location.href = url;
        }
    }
};

// Export for use in player-core.js
window.MuzibuSpaRouter = MuzibuSpaRouter;
