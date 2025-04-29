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