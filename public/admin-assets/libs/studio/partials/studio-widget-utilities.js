/**
 * Studio Editor - Widget Utilities
 * Widget işlemleri için yardımcı fonksiyonlar
 */

window.StudioWidgetUtilities = (function() {
    
    /**
     * Widget container'ı bul
     * @param {string} widgetId - Widget ID
     * @param {Document} doc - Document context
     * @returns {Object} Container elements
     */
    function findWidgetContainer(widgetId, doc = document) {
        const elements = {
            embedEl: null,
            placeholder: null,
            targetDocument: doc
        };
        
        // Widget container'ını bul
        elements.placeholder = doc.getElementById(`widget-content-${widgetId}`);
        elements.embedEl = doc.querySelector(`[data-tenant-widget-id="${widgetId}"]`);
        
        // iframe içinde ara
        if (!elements.placeholder && !elements.embedEl) {
            try {
                const iframes = document.querySelectorAll('iframe');
                for (const iframe of iframes) {
                    try {
                        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                        elements.placeholder = iframeDoc.getElementById(`widget-content-${widgetId}`);
                        elements.embedEl = iframeDoc.querySelector(`[data-tenant-widget-id="${widgetId}"]`);
                        
                        if (elements.placeholder || elements.embedEl) {
                            elements.targetDocument = iframeDoc;
                            break;
                        }
                    } catch(e) {
                        // Cross-origin iframe - skip
                    }
                }
            } catch(e) {
                console.error('iframe erişim hatası:', e);
            }
        }
        
        return elements;
    }
    
    /**
     * Widget loading indicator'ları yönet
     * @param {string} widgetId - Widget ID
     * @param {boolean} show - Show/hide
     */
    function toggleWidgetLoading(widgetId, show = true) {
        const { placeholder, targetDocument } = findWidgetContainer(widgetId);
        
        if (show) {
            // Loading göster
            if (placeholder) {
                const loading = placeholder.querySelector('.widget-loading');
                if (loading) {
                    loading.classList.add('loading-active');
                }
            }
            
            // Ana belgedeki loading'i de göster
            const mainContainer = document.getElementById(`widget-content-${widgetId}`);
            if (mainContainer) {
                const mainLoading = mainContainer.querySelector('.widget-loading');
                if (mainLoading) {
                    mainLoading.classList.add('loading-active');
                }
            }
        } else {
            // Loading gizle
            if (placeholder) {
                const loading = placeholder.querySelector('.widget-loading');
                if (loading) {
                    loading.classList.remove('loading-active');
                }
            }
            
            // Ana belgedeki loading'i de gizle
            const mainContainer = document.getElementById(`widget-content-${widgetId}`);
            if (mainContainer) {
                const mainLoading = mainContainer.querySelector('.widget-loading');
                if (mainLoading) {
                    mainLoading.classList.remove('loading-active');
                }
            }
        }
    }
    
    /**
     * Widget content'ini güvenli şekilde render et
     * @param {string} widgetId - Widget ID
     * @param {string} html - HTML content
     * @param {Document} targetDocument - Target document
     */
    function renderWidgetContent(widgetId, html, targetDocument = document) {
        const { embedEl, placeholder } = findWidgetContainer(widgetId, targetDocument);
        
        // Güvenlik kontrolü
        if (window.StudioSecurity && window.StudioSecurity.safeInnerHTML) {
            if (placeholder) {
                toggleWidgetLoading(widgetId, false);
                window.StudioSecurity.safeInnerHTML(placeholder, html);
            } else if (embedEl) {
                // İçerik container'ı oluştur
                const contentContainer = targetDocument.createElement('div');
                contentContainer.className = 'widget-content-placeholder';
                contentContainer.id = `widget-content-${widgetId}`;
                
                window.StudioSecurity.safeInnerHTML(contentContainer, html);
                
                // Mevcut placeholder'ı güncelle veya yeni ekle
                const existingPlaceholder = embedEl.querySelector('.widget-content-placeholder');
                if (existingPlaceholder) {
                    window.StudioSecurity.safeInnerHTML(existingPlaceholder, html);
                } else {
                    embedEl.appendChild(contentContainer);
                }
            }
        } else {
            // Fallback: basit sanitization
            const sanitizedHtml = html.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '');
            
            if (placeholder) {
                toggleWidgetLoading(widgetId, false);
                placeholder.innerHTML = sanitizedHtml;
            } else if (embedEl) {
                const contentContainer = targetDocument.createElement('div');
                contentContainer.className = 'widget-content-placeholder';
                contentContainer.id = `widget-content-${widgetId}`;
                contentContainer.innerHTML = sanitizedHtml;
                
                const existingPlaceholder = embedEl.querySelector('.widget-content-placeholder');
                if (existingPlaceholder) {
                    existingPlaceholder.innerHTML = sanitizedHtml;
                } else {
                    embedEl.appendChild(contentContainer);
                }
            }
        }
    }
    
    /**
     * Widget CSS'ini inject et
     * @param {string} widgetId - Widget ID
     * @param {string} css - CSS content
     * @param {Document} targetDocument - Target document
     */
    function injectWidgetCSS(widgetId, css, targetDocument = document) {
        if (!css) return;
        
        const styleId = `widget-style-${widgetId}`;
        let styleEl = targetDocument.getElementById(styleId);
        
        // Stil elemanı yoksa oluştur
        if (!styleEl) {
            styleEl = targetDocument.createElement('style');
            styleEl.id = styleId;
            targetDocument.head.appendChild(styleEl);
        }
        
        // CSS içeriğini ayarla
        styleEl.textContent = css;
    }
    
    /**
     * Widget JavaScript'ini güvenli şekilde inject et
     * @param {string} widgetId - Widget ID
     * @param {string} js - JavaScript content
     * @param {Object} context - Template context
     * @param {Document} targetDocument - Target document
     */
    function injectWidgetJS(widgetId, js, context = {}, targetDocument = document) {
        if (!js) return;
        
        // JavaScript güvenlik kontrolü
        if (window.StudioSecurity && window.StudioSecurity.validateJavaScript) {
            if (!window.StudioSecurity.validateJavaScript(js)) {
                console.error(`Widget ${widgetId} JavaScript kodu güvenlik kontrolünden geçemedi`);
                if (window.StudioErrorHandler) {
                    window.StudioErrorHandler.handleWidgetError(widgetId, 
                        new Error('JavaScript güvenlik kontrolü başarısız'), 
                        'javascript-inject'
                    );
                }
                return;
            }
        }
        
        const scriptId = `widget-script-${widgetId}`;
        let scriptEl = targetDocument.getElementById(scriptId);
        
        // Handlebars template işleme
        if (context && Object.keys(context).length > 0 && window.Handlebars) {
            try {
                const template = Handlebars.compile(js);
                js = template(context);
            } catch (err) {
                console.error(`Widget ${widgetId} JavaScript Handlebars işleme hatası:`, err);
                if (window.StudioErrorHandler) {
                    window.StudioErrorHandler.handleWidgetError(widgetId, err, 'javascript-template');
                }
                return;
            }
        }
        
        // İkinci güvenlik kontrolü (template sonrası)
        if (window.StudioSecurity && window.StudioSecurity.validateJavaScript) {
            if (!window.StudioSecurity.validateJavaScript(js)) {
                console.error(`Widget ${widgetId} JavaScript kodu template sonrası güvenlik kontrolünden geçemedi`);
                if (window.StudioErrorHandler) {
                    window.StudioErrorHandler.handleWidgetError(widgetId, 
                        new Error('JavaScript template sonrası güvenlik kontrolü başarısız'), 
                        'javascript-post-template'
                    );
                }
                return;
            }
        }
        
        // Handlebars helper'ları ayrı işle
        if (js.includes('Handlebars.registerHelper')) {
            if (!window._handlebarsHelpersInjected) {
                const helperScript = targetDocument.createElement('script');
                helperScript.type = 'text/javascript';
                helperScript.textContent = js;
                const container = targetDocument.querySelector('footer') || targetDocument.body;
                container.appendChild(helperScript);
                window._handlebarsHelpersInjected = true;
            }
        } else {
            // IIFE wrapper ile güvenlik
            js = `(function(widgetId) {\n// Widget #${widgetId} script\n${js}\n})(${widgetId});`;
            
            // Script inject et
            if (!scriptEl) {
                scriptEl = targetDocument.createElement('script');
                scriptEl.id = scriptId;
                scriptEl.type = 'text/javascript';
                scriptEl.textContent = js;
                targetDocument.body.appendChild(scriptEl);
            } else {
                scriptEl.textContent = js;
            }
        }
    }
    
    /**
     * Widget'ı block panel'den disable et
     * @param {string} widgetId - Widget ID
     */
    function disableWidgetBlock(widgetId) {
        const blockElement = document.querySelector(`[data-widget-id="${widgetId}"]`);
        if (blockElement) {
            blockElement.classList.add('disabled');
            
            const badge = blockElement.querySelector('.gjs-block-type-badge');
            if (badge) {
                badge.classList.replace('active', 'inactive');
                badge.textContent = 'Kullanımda';
            }
        }
    }
    
    /**
     * Widget'ı block panel'de enable et
     * @param {string} widgetId - Widget ID
     */
    function enableWidgetBlock(widgetId) {
        const blockElement = document.querySelector(`[data-widget-id="${widgetId}"]`);
        if (blockElement) {
            blockElement.classList.remove('disabled');
            
            const badge = blockElement.querySelector('.gjs-block-type-badge');
            if (badge) {
                badge.classList.replace('inactive', 'active');
                badge.textContent = 'Aktif';
            }
        }
    }
    
    /**
     * Widget overlay sistemini setup et
     * @param {string} widgetId - Widget ID
     * @param {Element} widgetElement - Widget element
     */
    function setupWidgetOverlay(widgetId, widgetElement) {
        if (!widgetElement) return;
        
        // Overlay oluştur
        const overlay = document.createElement('div');
        overlay.className = 'widget-overlay';
        overlay.style.cssText = `
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(139, 92, 246, 0.05);
            pointer-events: none;
            z-index: 10;
            display: none;
        `;
        
        // Hover olayları
        widgetElement.addEventListener('mouseenter', () => {
            overlay.style.display = 'block';
        });
        
        widgetElement.addEventListener('mouseleave', () => {
            overlay.style.display = 'none';
        });
        
        // Double click olayı
        widgetElement.addEventListener('dblclick', () => {
            window.open(`/admin/widgetmanagement/items/${widgetId}`, '_blank');
        });
        
        widgetElement.appendChild(overlay);
    }
    
    /**
     * Widget cleanup işlemi
     * @param {string} widgetId - Widget ID
     */
    function cleanupWidget(widgetId) {
        // Memory manager ile cleanup
        if (window.StudioMemoryManager) {
            window.StudioMemoryManager.cleanupWidget(widgetId);
        }
        
        // Block'u enable et
        enableWidgetBlock(widgetId);
        
        // Global cache'den temizle
        if (window._loadedWidgets) {
            window._loadedWidgets.delete(widgetId);
        }
        
        // DOM'dan temizle
        const widgetElements = document.querySelectorAll(`[data-tenant-widget-id="${widgetId}"]`);
        widgetElements.forEach(element => {
            element.remove();
        });
        
        // CSS ve JS'i temizle
        const styleElement = document.getElementById(`widget-style-${widgetId}`);
        if (styleElement) {
            styleElement.remove();
        }
        
        const scriptElement = document.getElementById(`widget-script-${widgetId}`);
        if (scriptElement) {
            scriptElement.remove();
        }
    }
    
    /**
     * Widget HTML template'ini işle
     * @param {string} html - HTML template
     * @param {Object} context - Template context
     * @returns {string} Processed HTML
     */
    function processWidgetTemplate(html, context = {}) {
        if (!html) return '';
        
        // Handlebars template işleme
        if (context && Object.keys(context).length > 0 && window.Handlebars) {
            try {
                const template = Handlebars.compile(html);
                return template(context);
            } catch (err) {
                console.error('Widget template işleme hatası:', err);
                return html; // Fallback to original
            }
        }
        
        return html;
    }
    
    /**
     * Widget error display
     * @param {string} widgetId - Widget ID
     * @param {Error} error - Error object
     */
    function displayWidgetError(widgetId, error) {
        const { embedEl, placeholder } = findWidgetContainer(widgetId);
        const container = placeholder || embedEl;
        
        if (container) {
            const errorHtml = `
                <div class="widget-error alert alert-danger">
                    <i class="fa fa-exclamation-triangle"></i>
                    <strong>Widget Hatası:</strong> ${error.message}
                    <button class="btn btn-sm btn-outline-danger retry-widget" 
                            onclick="window.StudioErrorHandler.retryWidget('${widgetId}')">
                        <i class="fa fa-refresh"></i> Tekrar Dene
                    </button>
                </div>
            `;
            
            container.innerHTML = errorHtml;
        }
    }
    
    // Public API
    return {
        findWidgetContainer: findWidgetContainer,
        toggleWidgetLoading: toggleWidgetLoading,
        renderWidgetContent: renderWidgetContent,
        injectWidgetCSS: injectWidgetCSS,
        injectWidgetJS: injectWidgetJS,
        disableWidgetBlock: disableWidgetBlock,
        enableWidgetBlock: enableWidgetBlock,
        setupWidgetOverlay: setupWidgetOverlay,
        cleanupWidget: cleanupWidget,
        processWidgetTemplate: processWidgetTemplate,
        displayWidgetError: displayWidgetError
    };
})();