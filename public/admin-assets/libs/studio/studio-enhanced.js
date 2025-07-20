/**
 * Studio Editor Enhanced System
 * Tüm geliştirmeleri içeren merkezi yükleyici
 */

// Enhanced Studio Editor system'i başlat
(function() {
    'use strict';
    
    // Yüklenme sırası (dependency order)
    const loadOrder = [
        'studio-security.js',
        'studio-memory-manager.js', 
        'studio-performance.js',
        'studio-error-handler.js',
        'studio-widget-utilities.js'
    ];
    
    // Base path
    const basePath = '/admin-assets/libs/studio/partials/';
    
    // Loading counter
    let loadedCount = 0;
    const totalCount = loadOrder.length;
    
    /**
     * Script yükleme fonksiyonu
     * @param {string} src - Script source
     * @param {Function} callback - Callback function
     */
    function loadScript(src, callback) {
        const script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = src;
        
        script.onload = function() {
            console.log(`✓ Studio Enhancement loaded: ${src}`);
            callback(null);
        };
        
        script.onerror = function() {
            console.error(`✗ Studio Enhancement failed: ${src}`);
            callback(new Error(`Failed to load ${src}`));
        };
        
        document.head.appendChild(script);
    }
    
    /**
     * Sequentıal script loading
     * @param {number} index - Current index
     */
    function loadNext(index) {
        if (index >= loadOrder.length) {
            onAllLoaded();
            return;
        }
        
        const scriptPath = basePath + loadOrder[index];
        loadScript(scriptPath, function(error) {
            if (error) {
                console.error('Studio Enhancement loading error:', error);
                // Continue loading other scripts
            }
            
            loadedCount++;
            loadNext(index + 1);
        });
    }
    
    /**
     * Tüm scriptler yüklendiğinde
     */
    function onAllLoaded() {
        console.log(`🎉 Studio Editor Enhanced System loaded (${loadedCount}/${totalCount})`);
        
        // Enhanced system'i başlat
        initializeEnhancedSystem();
        
        // Global event dispatch
        document.dispatchEvent(new CustomEvent('studio:enhanced:loaded', {
            detail: { loadedCount, totalCount }
        }));
    }
    
    /**
     * Enhanced system initialization
     */
    function initializeEnhancedSystem() {
        // Security system check
        if (window.StudioSecurity) {
            console.log('✓ Security system active');
        }
        
        // Memory manager check
        if (window.StudioMemoryManager) {
            console.log('✓ Memory management system active');
        }
        
        // Performance system check
        if (window.StudioPerformance) {
            console.log('✓ Performance optimization system active');
        }
        
        // Error handler check
        if (window.StudioErrorHandler) {
            console.log('✓ Error handling system active');
        }
        
        // Widget utilities check
        if (window.StudioWidgetUtilities) {
            console.log('✓ Widget utilities system active');
        }
        
        // Performance monitoring başlat
        if (window.StudioPerformance && window.StudioPerformance.setupImageLazyLoading) {
            window.StudioPerformance.setupImageLazyLoading();
        }
        
        // Enhanced widget loading override
        if (window.StudioWidgetUtilities) {
            enhanceWidgetLoading();
        }
        
        // Enhanced error monitoring
        if (window.StudioErrorHandler) {
            console.log('✓ Global error monitoring active');
        }
        
        // Memory monitoring
        if (window.StudioMemoryManager) {
            // Memory usage'i her 30 saniyede logla
            setInterval(() => {
                const memory = window.StudioMemoryManager.monitorMemoryUsage();
                if (memory && memory.used > 50) { // 50MB üzerindeyse warn
                    console.warn('High memory usage detected:', memory);
                }
            }, 30000);
        }
        
        console.log('🚀 Studio Editor Enhanced System ready!');
    }
    
    /**
     * Widget loading'i enhance et
     */
    function enhanceWidgetLoading() {
        // Original widget loading fonksiyonunu wrap et
        if (window.studioLoadWidget) {
            const originalLoadWidget = window.studioLoadWidget;
            
            window.studioLoadWidget = function(widgetId) {
                try {
                    // Performance tracking
                    if (window.StudioPerformance) {
                        const startTime = performance.now();
                        
                        // Widget optimize et
                        setTimeout(() => {
                            window.StudioPerformance.optimizeWidget(widgetId);
                        }, 1000);
                        
                        // Performance log
                        const endTime = performance.now();
                        console.log(`Widget ${widgetId} loading time: ${(endTime - startTime).toFixed(2)}ms`);
                    }
                    
                    // Original fonksiyonu çağır
                    return originalLoadWidget.call(this, widgetId);
                } catch (error) {
                    // Error handling
                    if (window.StudioErrorHandler) {
                        window.StudioErrorHandler.handleWidgetError(widgetId, error, 'enhanced-load');
                    }
                    throw error;
                }
            };
        }
    }
    
    /**
     * System health check
     */
    function healthCheck() {
        const systems = [
            'StudioSecurity',
            'StudioMemoryManager', 
            'StudioPerformance',
            'StudioErrorHandler',
            'StudioWidgetUtilities'
        ];
        
        const status = {
            healthy: true,
            systems: {},
            timestamp: new Date().toISOString()
        };
        
        systems.forEach(system => {
            status.systems[system] = {
                loaded: !!window[system],
                functions: window[system] ? Object.keys(window[system]).length : 0
            };
            
            if (!window[system]) {
                status.healthy = false;
            }
        });
        
        return status;
    }
    
    /**
     * Cleanup function
     */
    function cleanup() {
        if (window.StudioMemoryManager) {
            window.StudioMemoryManager.cleanupEditor();
        }
        
        console.log('Studio Enhanced System cleanup completed');
    }
    
    // Sayfa kapatılırken cleanup
    window.addEventListener('beforeunload', cleanup);
    
    // Global API
    window.StudioEnhanced = {
        healthCheck: healthCheck,
        cleanup: cleanup,
        version: '1.0.0'
    };
    
    // Script loading'i başlat
    console.log('🔄 Loading Studio Editor Enhanced System...');
    loadNext(0);
    
})();