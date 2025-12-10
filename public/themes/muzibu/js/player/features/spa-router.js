/**
 * Muzibu SPA Router
 * Handles SPA navigation with auth page bypass
 *
 * Dependencies: player-core.js (for this.* context)
 */

const MuzibuSpaRouter = {
    // Auth pages that should NOT use SPA navigation
    authPaths: ['/login', '/register', '/forgot-password', '/reset-password', '/verify-email', '/logout'],

    /**
     * Initialize SPA navigation handlers
     */
    initSpaNavigation() {
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

        console.log('🚀 SPA Router initialized');
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
     * Load page content via AJAX
     */
    async loadPage(url, addToHistory = true) {
        try {
            this.isLoading = true;

            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const html = await response.text();

            // Parse HTML and extract main content
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newContent = doc.querySelector('main');

            if (newContent) {
                const currentMain = document.querySelector('main');
                if (currentMain) {
                    currentMain.innerHTML = newContent.innerHTML;
                    window.scrollTo({ top: 0, behavior: 'smooth' });

                    const newTitle = doc.querySelector('title');
                    if (newTitle) {
                        document.title = newTitle.textContent;
                    }

                    this.currentPath = url;
                    console.log('Page loaded:', url);
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
