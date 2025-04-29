/**
 * Studio Editor - Ana Modül
 * Tüm modülleri birleştiren ve başlatan ana dosya
 */

// Editör Global kilit kontrolü (daha önce tanımlanmadıysa oluştur)
if (typeof window.__STUDIO_EDITOR_INSTANCE === 'undefined') {
    window.__STUDIO_EDITOR_INSTANCE = null;
    window.__STUDIO_EDITOR_INITIALIZED = false;
}

window.StudioCore = (function() {
    /**
     * Studio Editor'ı başlat
     * @param {Object} config - Yapılandırma parametreleri
     * @returns {Object} GrapesJS editor örneği
     */
    function initStudioEditor(config) {
        // Editör zaten başlatılmışsa mevcut örneği döndür
        if (window.__STUDIO_EDITOR_INITIALIZED && window.__STUDIO_EDITOR_INSTANCE) {
            console.log('Studio Editor zaten başlatılmış, mevcut örnek döndürülüyor.');
            return window.__STUDIO_EDITOR_INSTANCE;
        }
        
        console.log('Studio Editor başlatılıyor:', config);
        
        // Global başlatma kilidini ayarla
        window.__STUDIO_EDITOR_INITIALIZED = true;
        
        // Editor örneğini oluştur
        let editorInstance = window.StudioEditorSetup.initEditor(config);
        
        if (!editorInstance) {
            console.error('Editor başlatılamadı!');
            window.__STUDIO_EDITOR_INITIALIZED = false;
            return null;
        }
        
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
        
        // Global erişim için editörü kaydet
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

// Legacy uyumluluk için global fonksiyon
window.initStudioEditor = function(config) {
    // Global kilit kontrolü
    if (window.__STUDIO_EDITOR_INITIALIZED) {
        console.log('Studio Editor zaten başlatılmış, ikinci başlatma engellendi');
        return window.__STUDIO_EDITOR_INSTANCE;
    }
    return window.StudioCore.initStudioEditor(config);
};