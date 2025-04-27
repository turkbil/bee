/**
 * Studio Editor - Ana Modül
 * Tüm modülleri birleştiren ve başlatan ana dosya
 */

window.StudioCore = (function() {
    // Editor örneğini global olarak sakla
    let editorInstance = null;
    
    /**
     * Studio Editor'ı başlat
     * @param {Object} config - Yapılandırma parametreleri
     * @returns {Object} GrapesJS editor örneği
     */
    function initStudioEditor(config) {
        console.log('Studio Editor başlatılıyor:', config);
        
        // Editor örneğini oluştur
        editorInstance = window.StudioEditorSetup.initEditor(config);
        
        if (!editorInstance) {
            console.error('Editor başlatılamadı!');
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
        return editorInstance;
    }
    
    return {
        initStudioEditor: initStudioEditor,
        getEditor: getEditor
    };
})();

// Legacy uyumluluk için global fonksiyon
window.initStudioEditor = window.StudioCore.initStudioEditor;