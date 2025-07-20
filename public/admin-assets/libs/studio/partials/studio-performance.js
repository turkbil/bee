/**
 * Studio Editor - Performance Utilities
 * Performance optimizasyonu araçları
 */

window.StudioPerformance = (function() {
    
    // Performance metrics
    const metrics = {
        domQueries: 0,
        renderTime: 0,
        memoryUsage: 0,
        eventListeners: 0
    };
    
    // Cache sistemi
    const cache = new Map();
    const domCache = new Map();
    
    /**
     * Optimized DOM Query with caching
     * @param {string} selector - CSS selector
     * @param {Element} context - Context element
     * @param {boolean} useCache - Cache kullan
     * @returns {Element|NodeList} Elements
     */
    function optimizedQuery(selector, context = document, useCache = true) {
        metrics.domQueries++;
        
        if (useCache) {
            const cacheKey = `${selector}_${context === document ? 'document' : context.id || 'element'}`;
            
            if (domCache.has(cacheKey)) {
                return domCache.get(cacheKey);
            }
            
            const result = context.querySelectorAll(selector);
            domCache.set(cacheKey, result);
            
            // Cache'i 30 saniye sonra temizle
            setTimeout(() => {
                domCache.delete(cacheKey);
            }, 30000);
            
            return result;
        }
        
        return context.querySelectorAll(selector);
    }
    
    /**
     * Optimized single element query
     * @param {string} selector - CSS selector
     * @param {Element} context - Context element
     * @param {boolean} useCache - Cache kullan
     * @returns {Element} Element
     */
    function optimizedQueryOne(selector, context = document, useCache = true) {
        metrics.domQueries++;
        
        if (useCache) {
            const cacheKey = `${selector}_single_${context === document ? 'document' : context.id || 'element'}`;
            
            if (domCache.has(cacheKey)) {
                return domCache.get(cacheKey);
            }
            
            const result = context.querySelector(selector);
            domCache.set(cacheKey, result);
            
            // Cache'i 30 saniye sonra temizle
            setTimeout(() => {
                domCache.delete(cacheKey);
            }, 30000);
            
            return result;
        }
        
        return context.querySelector(selector);
    }
    
    /**
     * Throttle function
     * @param {Function} func - Function to throttle
     * @param {number} limit - Limit in ms
     * @returns {Function} Throttled function
     */
    function throttle(func, limit) {
        let inThrottle;
        return function(...args) {
            if (!inThrottle) {
                func.apply(this, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }
    
    /**
     * Debounce function
     * @param {Function} func - Function to debounce
     * @param {number} wait - Wait time in ms
     * @returns {Function} Debounced function
     */
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    /**
     * RequestAnimationFrame wrapper
     * @param {Function} callback - Callback function
     * @returns {number} RAF id
     */
    function optimizedRender(callback) {
        return requestAnimationFrame(() => {
            const start = performance.now();
            callback();
            const end = performance.now();
            metrics.renderTime += (end - start);
        });
    }
    
    /**
     * Batch DOM operations
     * @param {Function[]} operations - Array of operations
     */
    function batchDOMOperations(operations) {
        optimizedRender(() => {
            operations.forEach(operation => {
                try {
                    operation();
                } catch (error) {
                    console.error('Batch DOM operation error:', error);
                }
            });
        });
    }
    
    /**
     * Virtual scrolling implementation
     * @param {Element} container - Container element
     * @param {Array} items - Items array
     * @param {Function} renderItem - Item render function
     * @param {number} itemHeight - Item height
     */
    function virtualScroll(container, items, renderItem, itemHeight = 50) {
        const containerHeight = container.clientHeight;
        const viewportItemCount = Math.ceil(containerHeight / itemHeight);
        const totalHeight = items.length * itemHeight;
        
        // Scrollable area
        const scrollArea = document.createElement('div');
        scrollArea.style.height = `${totalHeight}px`;
        scrollArea.style.position = 'relative';
        
        // Viewport
        const viewport = document.createElement('div');
        viewport.style.height = `${containerHeight}px`;
        viewport.style.overflow = 'auto';
        viewport.style.position = 'relative';
        
        viewport.appendChild(scrollArea);
        
        let startIndex = 0;
        
        const renderVisibleItems = throttle(() => {
            const scrollTop = viewport.scrollTop;
            startIndex = Math.floor(scrollTop / itemHeight);
            const endIndex = Math.min(startIndex + viewportItemCount + 1, items.length);
            
            // Clear previous items
            scrollArea.innerHTML = '';
            
            // Render visible items
            for (let i = startIndex; i < endIndex; i++) {
                const item = items[i];
                const itemElement = renderItem(item, i);
                itemElement.style.position = 'absolute';
                itemElement.style.top = `${i * itemHeight}px`;
                itemElement.style.height = `${itemHeight}px`;
                scrollArea.appendChild(itemElement);
            }
        }, 16); // 60fps
        
        viewport.addEventListener('scroll', renderVisibleItems);
        
        // Initial render
        renderVisibleItems();
        
        container.innerHTML = '';
        container.appendChild(viewport);
        
        return {
            refresh: renderVisibleItems,
            updateItems: (newItems) => {
                items = newItems;
                scrollArea.style.height = `${items.length * itemHeight}px`;
                renderVisibleItems();
            }
        };
    }
    
    /**
     * Lazy loading implementation
     * @param {Element} element - Element to lazy load
     * @param {Function} loader - Loader function
     * @param {Object} options - Options
     */
    function lazyLoad(element, loader, options = {}) {
        const defaults = {
            threshold: 0.1,
            rootMargin: '50px'
        };
        
        const config = { ...defaults, ...options };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    loader(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, config);
        
        observer.observe(element);
        
        return observer;
    }
    
    /**
     * Performance monitoring
     */
    function startPerformanceMonitoring() {
        if (window.StudioMemoryManager) {
            window.StudioMemoryManager.setInterval(() => {
                const memoryInfo = window.StudioMemoryManager.monitorMemoryUsage();
                if (memoryInfo) {
                    metrics.memoryUsage = memoryInfo.used;
                }
                
                console.log('Studio Performance Metrics:', {
                    domQueries: metrics.domQueries,
                    renderTime: `${metrics.renderTime.toFixed(2)}ms`,
                    memoryUsage: `${metrics.memoryUsage}MB`,
                    cacheSize: domCache.size
                });
                
                // Reset metrics
                metrics.domQueries = 0;
                metrics.renderTime = 0;
            }, 30000, 'performance-monitor');
        }
    }
    
    /**
     * Cache management
     */
    function clearDOMCache() {
        domCache.clear();
        console.log('DOM cache cleared');
    }
    
    function clearCache() {
        cache.clear();
        domCache.clear();
        console.log('All caches cleared');
    }
    
    /**
     * Image lazy loading
     * @param {string} selector - Image selector
     */
    function setupImageLazyLoading(selector = 'img[data-src]') {
        const images = document.querySelectorAll(selector);
        
        images.forEach(img => {
            lazyLoad(img, (element) => {
                const src = element.dataset.src;
                if (src) {
                    element.src = src;
                    element.removeAttribute('data-src');
                    element.classList.add('loaded');
                }
            });
        });
    }
    
    /**
     * Widget performance optimization
     * @param {string} widgetId - Widget ID
     */
    function optimizeWidget(widgetId) {
        const namespace = `widget-${widgetId}`;
        
        // Widget container'ı bul
        const container = optimizedQueryOne(`[data-tenant-widget-id="${widgetId}"]`);
        if (!container) return;
        
        // Widget içindeki image'ları lazy load et
        const images = container.querySelectorAll('img:not([src])');
        images.forEach(img => {
            if (img.dataset.src) {
                lazyLoad(img, (element) => {
                    element.src = element.dataset.src;
                    element.removeAttribute('data-src');
                });
            }
        });
        
        // Widget içindeki event listener'ları optimize et
        const clickableElements = container.querySelectorAll('[onclick], [data-click]');
        clickableElements.forEach(element => {
            const handler = element.onclick || element.dataset.click;
            if (handler) {
                element.removeAttribute('onclick');
                element.removeAttribute('data-click');
                
                if (window.StudioMemoryManager) {
                    window.StudioMemoryManager.addEventListener(element, 'click', 
                        typeof handler === 'function' ? handler : new Function(handler), 
                        namespace
                    );
                }
            }
        });
    }
    
    // Performance monitoring'i başlat
    startPerformanceMonitoring();
    
    // Public API
    return {
        // Query optimization
        query: optimizedQuery,
        queryOne: optimizedQueryOne,
        
        // Function utilities
        throttle: throttle,
        debounce: debounce,
        
        // Render optimization
        render: optimizedRender,
        batchDOM: batchDOMOperations,
        
        // Virtual scrolling
        virtualScroll: virtualScroll,
        
        // Lazy loading
        lazyLoad: lazyLoad,
        setupImageLazyLoading: setupImageLazyLoading,
        
        // Cache management
        clearDOMCache: clearDOMCache,
        clearCache: clearCache,
        
        // Widget optimization
        optimizeWidget: optimizeWidget,
        
        // Metrics
        getMetrics: () => ({ ...metrics }),
        
        // Cache access
        cache: cache,
        domCache: domCache
    };
})();