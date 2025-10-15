/**
 * AUTO SEO FILL - FRONTEND TRIGGER
 * Premium tenant'lar için sayfa ilk yüklendiğinde SEO otomatik doldurma
 *
 * Çalışma Mantığı:
 * 1. Sayfa yüklendiğinde data attribute'ları kontrol et
 * 2. Premium tenant ise ve SEO boş ise API'ye istek at
 * 3. Background'da çalış (kullanıcıyı bekletme)
 * 4. Hata olursa sessizce logla
 */

(function() {
    'use strict';

    // Configuration
    const AUTO_SEO_CONFIG = {
        apiEndpoint: '/api/auto-seo-fill',
        enabled: true,
        debug: false,
        retryAttempts: 1,
        retryDelay: 2000
    };

    /**
     * Debug log helper
     */
    function debugLog(message, data = null) {
        if (AUTO_SEO_CONFIG.debug) {
            console.log(`[Auto SEO Fill] ${message}`, data || '');
        }
    }

    /**
     * Check if page should trigger auto SEO fill
     */
    function shouldTriggerAutoFill() {
        // Check if feature is enabled
        if (!AUTO_SEO_CONFIG.enabled) {
            debugLog('Feature disabled');
            return false;
        }

        // Check if page has auto-seo-fill data attribute
        const pageElement = document.querySelector('[data-auto-seo-fill]');
        if (!pageElement) {
            debugLog('No data-auto-seo-fill attribute found');
            return false;
        }

        // Check if premium tenant
        const isPremium = pageElement.getAttribute('data-premium-tenant') === '1';
        if (!isPremium) {
            debugLog('Not a premium tenant');
            return false;
        }

        // Check if SEO is empty
        const seoEmpty = pageElement.getAttribute('data-seo-empty') === '1';
        if (!seoEmpty) {
            debugLog('SEO data already exists');
            return false;
        }

        return true;
    }

    /**
     * Extract page data from DOM
     */
    function extractPageData() {
        const pageElement = document.querySelector('[data-auto-seo-fill]');
        if (!pageElement) {
            return null;
        }

        return {
            model_type: pageElement.getAttribute('data-model-type') || 'page',
            model_id: parseInt(pageElement.getAttribute('data-model-id')) || 0,
            locale: pageElement.getAttribute('data-locale') || document.documentElement.lang || 'tr'
        };
    }

    /**
     * Send auto-fill request to API
     */
    async function triggerAutoFill(pageData, attempt = 1) {
        try {
            debugLog('Triggering auto SEO fill', pageData);

            const response = await fetch(AUTO_SEO_CONFIG.apiEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify(pageData)
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.error || 'API request failed');
            }

            if (data.success) {
                debugLog('✅ Auto SEO Fill successful', data);
                // Optional: Show subtle notification
                if (data.data && AUTO_SEO_CONFIG.debug) {
                    console.log('SEO Data filled:', data.data);
                }
            } else if (data.skipped) {
                debugLog('⏭️ Auto SEO Fill skipped (data already exists)');
            } else {
                debugLog('⚠️ Auto SEO Fill failed', data);
            }

            return data;

        } catch (error) {
            console.error('[Auto SEO Fill] Error:', error);

            // Retry logic
            if (attempt < AUTO_SEO_CONFIG.retryAttempts) {
                debugLog(`Retrying... (${attempt + 1}/${AUTO_SEO_CONFIG.retryAttempts})`);
                await new Promise(resolve => setTimeout(resolve, AUTO_SEO_CONFIG.retryDelay));
                return triggerAutoFill(pageData, attempt + 1);
            }

            return { success: false, error: error.message };
        }
    }

    /**
     * Initialize auto SEO fill on page load
     */
    function initAutoSeoFill() {
        // Check if should trigger
        if (!shouldTriggerAutoFill()) {
            debugLog('Auto SEO Fill not needed');
            return;
        }

        // Extract page data
        const pageData = extractPageData();
        if (!pageData || !pageData.model_id) {
            debugLog('Invalid page data', pageData);
            return;
        }

        // Trigger auto fill (background, non-blocking)
        debugLog('Initializing Auto SEO Fill', pageData);

        // Use setTimeout to ensure it runs after page load
        setTimeout(() => {
            triggerAutoFill(pageData)
                .then(result => {
                    debugLog('Auto SEO Fill completed', result);
                })
                .catch(error => {
                    console.error('[Auto SEO Fill] Failed:', error);
                });
        }, 500); // 500ms delay to not block initial page render
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAutoSeoFill);
    } else {
        initAutoSeoFill();
    }

    // Also listen for Livewire navigation (SPA)
    if (typeof Livewire !== 'undefined') {
        Livewire.hook('message.processed', (message, component) => {
            initAutoSeoFill();
        });
    }

    // Expose to window for manual trigger (debugging)
    window.autoSeoFill = {
        trigger: (modelType, modelId, locale) => {
            return triggerAutoFill({
                model_type: modelType || 'page',
                model_id: modelId || 0,
                locale: locale || 'tr'
            });
        },
        config: AUTO_SEO_CONFIG
    };

})();
