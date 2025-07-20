/**
 * Studio Editor - Memory Management
 * Memory leak'leri önleme ve cleanup sistemi
 */

window.StudioMemoryManager = (function() {
    
    // Global cleanup registery
    const cleanupRegistry = new Map();
    const eventListeners = new WeakMap();
    const mutationObservers = new WeakMap();
    const intervalTimers = new Set();
    const timeoutTimers = new Set();
    
    /**
     * AbortController ile Event Listener Management
     */
    class EventManager {
        constructor() {
            this.controllers = new Map();
        }
        
        /**
         * Event listener ekle
         * @param {Element} element - Element
         * @param {string} event - Event tipi
         * @param {Function} handler - Handler fonksiyonu
         * @param {string} namespace - Namespace (cleanup için)
         */
        addEventListener(element, event, handler, namespace = 'default') {
            if (!this.controllers.has(namespace)) {
                this.controllers.set(namespace, new AbortController());
            }
            
            const controller = this.controllers.get(namespace);
            element.addEventListener(event, handler, {
                signal: controller.signal
            });
            
            // Cleanup registry'ye ekle
            if (!cleanupRegistry.has(namespace)) {
                cleanupRegistry.set(namespace, new Set());
            }
            cleanupRegistry.get(namespace).add(() => {
                if (this.controllers.has(namespace)) {
                    controller.abort();
                }
            });
        }
        
        /**
         * Namespace'deki tüm event listener'ları temizle
         * @param {string} namespace - Namespace
         */
        removeEventListeners(namespace) {
            if (this.controllers.has(namespace)) {
                this.controllers.get(namespace).abort();
                this.controllers.delete(namespace);
            }
        }
        
        /**
         * Tüm event listener'ları temizle
         */
        removeAllEventListeners() {
            this.controllers.forEach((controller, namespace) => {
                controller.abort();
            });
            this.controllers.clear();
        }
    }
    
    /**
     * MutationObserver Management
     */
    class ObserverManager {
        constructor() {
            this.observers = new Map();
        }
        
        /**
         * MutationObserver oluştur
         * @param {Function} callback - Callback fonksiyonu
         * @param {Element} target - Target element
         * @param {Object} options - Observer seçenekleri
         * @param {string} namespace - Namespace
         */
        createObserver(callback, target, options, namespace = 'default') {
            const observer = new MutationObserver(callback);
            observer.observe(target, options);
            
            if (!this.observers.has(namespace)) {
                this.observers.set(namespace, new Set());
            }
            this.observers.get(namespace).add(observer);
            
            // Cleanup registry'ye ekle
            if (!cleanupRegistry.has(namespace)) {
                cleanupRegistry.set(namespace, new Set());
            }
            cleanupRegistry.get(namespace).add(() => {
                observer.disconnect();
            });
            
            return observer;
        }
        
        /**
         * Namespace'deki tüm observer'ları temizle
         * @param {string} namespace - Namespace
         */
        disconnectObservers(namespace) {
            if (this.observers.has(namespace)) {
                this.observers.get(namespace).forEach(observer => {
                    observer.disconnect();
                });
                this.observers.delete(namespace);
            }
        }
        
        /**
         * Tüm observer'ları temizle
         */
        disconnectAllObservers() {
            this.observers.forEach((observerSet, namespace) => {
                observerSet.forEach(observer => {
                    observer.disconnect();
                });
            });
            this.observers.clear();
        }
    }
    
    /**
     * Timer Management
     */
    class TimerManager {
        constructor() {
            this.intervals = new Map();
            this.timeouts = new Map();
        }
        
        /**
         * Interval oluştur
         * @param {Function} callback - Callback fonksiyonu
         * @param {number} delay - Delay
         * @param {string} namespace - Namespace
         */
        setInterval(callback, delay, namespace = 'default') {
            const timerId = setInterval(callback, delay);
            
            if (!this.intervals.has(namespace)) {
                this.intervals.set(namespace, new Set());
            }
            this.intervals.get(namespace).add(timerId);
            
            // Cleanup registry'ye ekle
            if (!cleanupRegistry.has(namespace)) {
                cleanupRegistry.set(namespace, new Set());
            }
            cleanupRegistry.get(namespace).add(() => {
                clearInterval(timerId);
            });
            
            return timerId;
        }
        
        /**
         * Timeout oluştur
         * @param {Function} callback - Callback fonksiyonu
         * @param {number} delay - Delay
         * @param {string} namespace - Namespace
         */
        setTimeout(callback, delay, namespace = 'default') {
            const timerId = setTimeout(() => {
                callback();
                // Timeout completed, remove from registry
                if (this.timeouts.has(namespace)) {
                    this.timeouts.get(namespace).delete(timerId);
                }
            }, delay);
            
            if (!this.timeouts.has(namespace)) {
                this.timeouts.set(namespace, new Set());
            }
            this.timeouts.get(namespace).add(timerId);
            
            // Cleanup registry'ye ekle
            if (!cleanupRegistry.has(namespace)) {
                cleanupRegistry.set(namespace, new Set());
            }
            cleanupRegistry.get(namespace).add(() => {
                clearTimeout(timerId);
            });
            
            return timerId;
        }
        
        /**
         * Namespace'deki tüm timer'ları temizle
         * @param {string} namespace - Namespace
         */
        clearTimers(namespace) {
            if (this.intervals.has(namespace)) {
                this.intervals.get(namespace).forEach(timerId => {
                    clearInterval(timerId);
                });
                this.intervals.delete(namespace);
            }
            
            if (this.timeouts.has(namespace)) {
                this.timeouts.get(namespace).forEach(timerId => {
                    clearTimeout(timerId);
                });
                this.timeouts.delete(namespace);
            }
        }
        
        /**
         * Tüm timer'ları temizle
         */
        clearAllTimers() {
            this.intervals.forEach((intervalSet, namespace) => {
                intervalSet.forEach(timerId => {
                    clearInterval(timerId);
                });
            });
            this.intervals.clear();
            
            this.timeouts.forEach((timeoutSet, namespace) => {
                timeoutSet.forEach(timerId => {
                    clearTimeout(timerId);
                });
            });
            this.timeouts.clear();
        }
    }
    
    // Manager instance'ları
    const eventManager = new EventManager();
    const observerManager = new ObserverManager();
    const timerManager = new TimerManager();
    
    /**
     * Widget cleanup fonksiyonu
     * @param {string} widgetId - Widget ID
     */
    function cleanupWidget(widgetId) {
        const namespace = `widget-${widgetId}`;
        
        // Event listener'ları temizle
        eventManager.removeEventListeners(namespace);
        
        // Observer'ları temizle
        observerManager.disconnectObservers(namespace);
        
        // Timer'ları temizle
        timerManager.clearTimers(namespace);
        
        // Custom cleanup fonksiyonlarını çalıştır
        if (cleanupRegistry.has(namespace)) {
            cleanupRegistry.get(namespace).forEach(cleanup => {
                try {
                    cleanup();
                } catch (error) {
                    console.error(`Cleanup error for ${namespace}:`, error);
                }
            });
            cleanupRegistry.delete(namespace);
        }
        
        // Global widget cache'den temizle
        if (window._loadedWidgets) {
            window._loadedWidgets.delete(widgetId);
        }
        
        // DOM'dan widget elementlerini temizle
        const widgetElements = document.querySelectorAll(`[data-tenant-widget-id="${widgetId}"]`);
        widgetElements.forEach(element => {
            // Event listener'ları temizle
            const newElement = element.cloneNode(true);
            element.parentNode.replaceChild(newElement, element);
        });
        
        console.log(`Widget ${widgetId} memory cleanup completed`);
    }
    
    /**
     * Editor cleanup fonksiyonu
     */
    function cleanupEditor() {
        // Tüm event listener'ları temizle
        eventManager.removeAllEventListeners();
        
        // Tüm observer'ları temizle
        observerManager.disconnectAllObservers();
        
        // Tüm timer'ları temizle
        timerManager.clearAllTimers();
        
        // Tüm custom cleanup fonksiyonlarını çalıştır
        cleanupRegistry.forEach((cleanupSet, namespace) => {
            cleanupSet.forEach(cleanup => {
                try {
                    cleanup();
                } catch (error) {
                    console.error(`Cleanup error for ${namespace}:`, error);
                }
            });
        });
        cleanupRegistry.clear();
        
        // Global cache'leri temizle
        if (window._loadedWidgets) {
            window._loadedWidgets.clear();
        }
        if (window._loadedModules) {
            window._loadedModules.clear();
        }
        
        // Editor instance'ını temizle
        if (window.__STUDIO_EDITOR_INSTANCE) {
            try {
                window.__STUDIO_EDITOR_INSTANCE.destroy();
            } catch (error) {
                console.error('Editor destroy error:', error);
            }
            window.__STUDIO_EDITOR_INSTANCE = null;
        }
        
        console.log('Studio Editor memory cleanup completed');
    }
    
    /**
     * Memory usage monitor
     */
    function monitorMemoryUsage() {
        if (performance && performance.memory) {
            const memory = performance.memory;
            const usage = {
                used: Math.round(memory.usedJSHeapSize / 1024 / 1024),
                total: Math.round(memory.totalJSHeapSize / 1024 / 1024),
                limit: Math.round(memory.jsHeapSizeLimit / 1024 / 1024)
            };
            
            console.log(`Memory Usage: ${usage.used}MB / ${usage.total}MB (Limit: ${usage.limit}MB)`);
            
            // Memory warning
            if (usage.used > usage.limit * 0.8) {
                console.warn('High memory usage detected, consider cleanup');
                
                // Automatic cleanup trigger
                if (usage.used > usage.limit * 0.9) {
                    console.warn('Critical memory usage, forcing cleanup');
                    cleanupEditor();
                }
            }
            
            return usage;
        }
        return null;
    }
    
    /**
     * Page unload cleanup
     */
    window.addEventListener('beforeunload', () => {
        cleanupEditor();
    });
    
    // Public API
    return {
        // Event management
        addEventListener: eventManager.addEventListener.bind(eventManager),
        removeEventListeners: eventManager.removeEventListeners.bind(eventManager),
        
        // Observer management
        createObserver: observerManager.createObserver.bind(observerManager),
        disconnectObservers: observerManager.disconnectObservers.bind(observerManager),
        
        // Timer management
        setInterval: timerManager.setInterval.bind(timerManager),
        setTimeout: timerManager.setTimeout.bind(timerManager),
        clearTimers: timerManager.clearTimers.bind(timerManager),
        
        // Cleanup functions
        cleanupWidget: cleanupWidget,
        cleanupEditor: cleanupEditor,
        
        // Memory monitoring
        monitorMemoryUsage: monitorMemoryUsage,
        
        // Registry access
        addCleanupFunction: (namespace, cleanup) => {
            if (!cleanupRegistry.has(namespace)) {
                cleanupRegistry.set(namespace, new Set());
            }
            cleanupRegistry.get(namespace).add(cleanup);
        }
    };
})();