/**
 * 🚀 Muzibu SPA Router
 * Alpine.js tabanlı Single Page Application routing sistemi
 *
 * Özellikler:
 * - Sayfa yenilenmeden navigasyon
 * - Browser history API desteği (geri/ileri butonları)
 * - Loading state yönetimi
 * - Skeleton animasyonları
 * - Meta tag güncelleme (SEO)
 * - Cache stratejisi
 */

window.muzibuRouter = {
    // Router state
    currentRoute: '/',
    currentPageData: null,
    currentPageHtml: '',
    isLoading: false,
    contentLoaded: true,

    // Cache (basit in-memory cache)
    // ⚠️ Cache DEVRE DIŞI: Admin'den yapılan değişikliklerin anında yansıması için
    cache: {},
    cacheTimeout: 0, // Cache kapalı (eski: 5 * 60 * 1000)

    /**
     * Router'ı başlat
     */
    init() {
        console.log('🚀 Muzibu SPA Router initialized');

        // Popstate event (geri/ileri butonları)
        window.addEventListener('popstate', (e) => {
            console.log('⬅️ Browser back/forward:', window.location.pathname);
            this.navigateTo(window.location.pathname, false); // pushState yapmadan
        });

        // İlk sayfa yükleme
        this.currentRoute = window.location.pathname;
    },

    /**
     * Sayfa navigasyonu (ana fonksiyon)
     *
     * @param {string} url - Gidilecek URL (/playlists, /albums/rock vb.)
     * @param {boolean} pushState - History API'ye push yapılsın mı? (default: true)
     */
    async navigateTo(url, pushState = true) {
        console.log('🔄 Navigating to:', url);

        // 🏠 Ana sayfa için full page reload (SPA değil, Laravel blade render)
        if (url === '/' || url === '') {
            if (pushState) {
                window.location.href = '/';
            }
            return;
        }

        // Loading başlat
        this.isLoading = true;
        this.contentLoaded = false;

        // Alpine store güncelle (eğer varsa)
        if (window.Alpine && window.Alpine.store('player')) {
            window.Alpine.store('player').isLoading = true;
        }

        try {
            // Cache kontrol
            const cached = this.getFromCache(url);
            if (cached) {
                console.log('💾 Using cached data for:', url);
                this.renderPage(cached, url, pushState);
                return;
            }

            // API'dan veri çek
            const response = await fetch('/api' + url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();

            // Cache'e kaydet
            this.saveToCache(url, data);

            // Sayfayı render et
            this.renderPage(data, url, pushState);

        } catch (error) {
            console.error('❌ Navigation error:', error);

            // Hata durumunda fallback: Normal link gibi davran
            if (pushState) {
                window.location.href = url;
            } else {
                this.showError('Sayfa yüklenirken hata oluştu. Lütfen tekrar deneyin.');
            }
        } finally {
            // Loading bitir
            this.isLoading = false;

            if (window.Alpine && window.Alpine.store('player')) {
                window.Alpine.store('player').isLoading = false;
            }
        }
    },

    /**
     * Sayfa render işlemi
     */
    renderPage(data, url, pushState) {
        // HTML içeriği güncelle
        this.currentPageHtml = data.html;
        this.currentPageData = data;
        this.currentRoute = url;

        // DOM'a enjekte et
        const mainContent = document.querySelector('#mainContent .spa-content-wrapper');
        if (mainContent) {
            mainContent.innerHTML = data.html;

            // 🎯 Alpine.js re-initialize (yeni eklenen element'ler için)
            if (window.Alpine) {
                // Alpine v3+ için nextTick kullanarak DOM'un hazır olmasını bekle
                setTimeout(() => {
                    try {
                        // Alpine.initTree yerine destroyTree + initTree kombinasyonu
                        window.Alpine.destroyTree(mainContent);
                        window.Alpine.initTree(mainContent);
                        console.log('✨ Alpine re-initialized for new content');
                    } catch (e) {
                        console.warn('Alpine init error:', e);
                        // Fallback: Tüm x-data elementleri için manuel init
                        mainContent.querySelectorAll('[x-data]').forEach(el => {
                            if (!el.__x) {
                                window.Alpine.initTree(el);
                            }
                        });
                    }
                }, 50);
            }
        } else {
            console.warn('⚠️ .spa-content-wrapper bulunamadı!');
        }

        // Meta tags güncelle
        if (data.meta) {
            this.updateMetaTags(data.meta);
        }

        // Browser history güncelle
        if (pushState) {
            window.history.pushState({ url }, '', url);
        }

        // Dispatch route-changed event (sidebar kontrolü için)
        window.dispatchEvent(new CustomEvent('route-changed', {
            detail: { path: url }
        }));

        // Scroll to top
        const scrollContainer = document.querySelector('#mainContent');
        if (scrollContainer) {
            scrollContainer.scrollTop = 0;
        }

        // Content loaded animasyonu
        setTimeout(() => {
            this.contentLoaded = true;
        }, 50);

        console.log('✅ Page rendered:', url);
    },

    /**
     * Meta tag güncelleme
     */
    updateMetaTags(meta) {
        // Title
        if (meta.title) {
            document.title = meta.title;
        }

        // Description
        if (meta.description) {
            let descMeta = document.querySelector('meta[name="description"]');
            if (!descMeta) {
                descMeta = document.createElement('meta');
                descMeta.setAttribute('name', 'description');
                document.head.appendChild(descMeta);
            }
            descMeta.setAttribute('content', meta.description);
        }

        // Open Graph tags
        if (meta.title) {
            let ogTitle = document.querySelector('meta[property="og:title"]');
            if (!ogTitle) {
                ogTitle = document.createElement('meta');
                ogTitle.setAttribute('property', 'og:title');
                document.head.appendChild(ogTitle);
            }
            ogTitle.setAttribute('content', meta.title);
        }
    },

    /**
     * Cache işlemleri
     */
    getFromCache(url) {
        const cached = this.cache[url];
        if (!cached) return null;

        const now = Date.now();
        if (now - cached.timestamp > this.cacheTimeout) {
            delete this.cache[url];
            return null;
        }

        return cached.data;
    },

    saveToCache(url, data) {
        this.cache[url] = {
            data: data,
            timestamp: Date.now()
        };
    },

    clearCache() {
        this.cache = {};
        console.log('🗑️ Router cache cleared');
    },

    /**
     * Hata mesajı göster
     */
    showError(message) {
        if (window.Alpine && window.Alpine.store('toast')) {
            window.Alpine.store('toast').show(message, 'error');
        } else {
            alert(message);
        }
    }
};

// Router'ı otomatik başlat
document.addEventListener('DOMContentLoaded', () => {
    if (window.muzibuRouter) {
        window.muzibuRouter.init();
    }
});
