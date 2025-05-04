/**
 * Studio Editor - Ana Modül
 * Tüm modülleri birleştiren ve başlatan ana dosya
 */

// StudioCore ana modülü
// Global değişkenler app.js'de zaten tanımlanmıştır

window.StudioCore = (function() {
    /**
     * Studio Editor'ı başlat
     * @param {Object} config - Yapılandırma parametreleri
     * @returns {Object} GrapesJS editor örneği
     */
    function initStudioEditor(config) {
        // Editör zaten başlatılmışsa mevcut örneği döndür
        if (window.__STUDIO_EDITOR_INSTANCE) {
            console.log('Studio Editor zaten başlatılmış, mevcut örnek döndürülüyor.');
            return window.__STUDIO_EDITOR_INSTANCE;
        }
        
        console.log('Studio Editor başlatılıyor:', config);
        
        // Editor örneğini oluştur - burada _INITIALIZED bayrağı zaten app.js tarafından ayarlandı
        let editorInstance = window.StudioEditorSetup.initEditor(config);
        
        if (!editorInstance) {
            console.error('Editor başlatılamadı!');
            window.__STUDIO_EDITOR_INITIALIZED = false;
            return null;
        }
        
        // Global erişim için örneği sakla
        window.__STUDIO_EDITOR_INSTANCE = editorInstance;
        
        // İçeriği yükle
        window.StudioEditorSetup.loadContent(editorInstance, config);
        
        // Editor yükleme olayını dinle
        editorInstance.on('load', function() {
            // UI bileşenlerini ayarla
            if (window.StudioTabs && typeof window.StudioTabs.setupTabs === 'function') {
                window.StudioTabs.setupTabs();
            }
            
            // Panel açma/kapama butonlarını ekle
            if (window.StudioPanels && typeof window.StudioPanels.initializePanelToggles === 'function') {
                window.StudioPanels.initializePanelToggles();
            }
            
            // Cihaz görünümü butonlarını ayarla
            if (window.StudioDevices && typeof window.StudioDevices.setupDeviceToggle === 'function') {
                window.StudioDevices.setupDeviceToggle(editorInstance);
            }
            
            // Widget sistemini ayarla
            if (window.StudioWidgetManager && typeof window.StudioWidgetManager.setup === 'function') {
                window.StudioWidgetManager.setup(editorInstance);
            }
            
            // Blokları kaydet
            if (window.StudioBlocks && typeof window.StudioBlocks.registerBlocks === 'function') {
                window.StudioBlocks.registerBlocks(editorInstance);
            }
            
            // Eylem butonlarını ayarla
            if (window.StudioActions && typeof window.StudioActions.setupActions === 'function') {
                window.StudioActions.setupActions(editorInstance, config);
            }
            
            // UI komponentlerini ayarla
            if (window.StudioUI && typeof window.StudioUI.setupUI === 'function') {
                window.StudioUI.setupUI(editorInstance);
            }
            
            // İçerik yüklendikten sonra module widget'ları hemen yükle ve butonları devre dışı bırak
            setTimeout(() => {
                const moduleComponents = editorInstance.DomComponents.getWrapper().find('[data-widget-module-id]');
                if (moduleComponents && moduleComponents.length > 0) {
                    console.log(`${moduleComponents.length} adet module widget otomatik yükleniyor...`);
                    
                    moduleComponents.forEach(component => {
                        const moduleId = component.getAttributes()['data-widget-module-id'];
                        if (moduleId && window.studioLoadModuleWidget) {
                            window.studioLoadModuleWidget(moduleId);
                            
                            // Module widget butonunu devre dışı bırak
                            const blockEl = document.querySelector(`.block-item[data-block-id="widget-${moduleId}"]`);
                            if (blockEl) {
                                blockEl.classList.add('disabled');
                                blockEl.setAttribute('draggable', 'false');
                                blockEl.style.cursor = 'not-allowed';
                                const badge = blockEl.querySelector('.gjs-block-type-badge');
                                if (badge) {
                                    badge.classList.replace('active', 'inactive');
                                    badge.textContent = 'Pasif';
                                }
                            }
                        }
                    });
                }
                
                // HTML içindeki [[module:XX]] formatındaki kodları da ara ve işle
                const htmlContent = editorInstance.getHtml();
                if (typeof htmlContent === 'string') {
                    const moduleRegex = /\[\[module:(\d+)\]\]/g;
                    let moduleMatch;
                    
                    while ((moduleMatch = moduleRegex.exec(htmlContent)) !== null) {
                        const moduleId = moduleMatch[1];
                        
                        // Module widget bileşeni oluştur
                        const moduleWidget = editorInstance.DomComponents.addComponent({
                            type: 'module-widget',
                            widget_module_id: moduleId,
                            attributes: {
                                'data-widget-module-id': moduleId,
                                'id': `module-widget-${moduleId}`,
                                'class': 'module-widget-container'
                            },
                            content: `<div class="module-widget-label"><i class="fa fa-cube me-1"></i> Module #${moduleId}</div><div class="module-widget-content-placeholder" id="module-content-${moduleId}"><div class="widget-loading" style="text-align:center; padding:20px;"><i class="fa fa-spin fa-spinner"></i> Module yükleniyor...</div></div>`
                        });
                        
                        // Module içeriğini yükle
                        if (window.studioLoadModuleWidget) {
                            window.studioLoadModuleWidget(moduleId);
                        }
                        
                        // Module widget butonunu devre dışı bırak
                        const blockEl = document.querySelector(`.block-item[data-block-id="widget-${moduleId}"]`);
                        if (blockEl) {
                            blockEl.classList.add('disabled');
                            blockEl.setAttribute('draggable', 'false');
                            blockEl.style.cursor = 'not-allowed';
                            const badge = blockEl.querySelector('.gjs-block-type-badge');
                            if (badge) {
                                badge.classList.replace('active', 'inactive');
                                badge.textContent = 'Pasif';
                            }
                        }
                    }
                }
                
                // Module widget'larının HTML dönüştürmesini ayarla
                if (!editorInstance.DomComponents.getType('module-widget')) {
                    editorInstance.DomComponents.addType('module-widget', {
                        model: {
                            toHTML() {
                                const moduleId = this.get('widget_module_id') || this.getAttributes()['data-widget-module-id'];
                                if (moduleId) {
                                    return `[[module:${moduleId}]]`;
                                }
                                return this.view.el.outerHTML;
                            }
                        }
                    });
                }
            }, 500);
        });
        
        // Global erişim için editörü kaydet (geriye dönük uyumluluk)
        window.studioEditor = editorInstance;
        
        return editorInstance;
    }
    
    /**
     * Mevcut editor örneğini getir
     * @returns {Object|null} GrapesJS editor örneği
     */
    function getEditor() {
        return window.__STUDIO_EDITOR_INSTANCE;
    }
    
    return {
        initStudioEditor: initStudioEditor,
        getEditor: getEditor
    };
})();

// Legacy uyumluluk için global fonksiyon - güvenli hale getirildi
window.initStudioEditor = function(config) {
    // Global kilit kontrolü
    if (window.__STUDIO_EDITOR_INITIALIZED) {
        console.log('Studio Editor zaten başlatılmış, ikinci başlatma engellendi');
        return window.__STUDIO_EDITOR_INSTANCE;
    }
    window.__STUDIO_EDITOR_INITIALIZED = true;
    return window.StudioCore.initStudioEditor(config);
};