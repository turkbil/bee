/**
 * Studio Editor Ana Modül
 * Tüm modülleri birleştiren ve başlatan ana dosya
 */

/**
 * Studio Editor için GrapesJS yapılandırması
 */
window.initStudioEditor = function (config) {
    console.log('Studio Editor başlatılıyor:', config);
    
    if (!config || !config.moduleId || config.moduleId <= 0) {
        console.error('Geçersiz konfigürasyon veya modül ID:', config);
        return null;
    }
    
    // GrapesJS Editor yapılandırması
    const editor = window.StudioCore.initEditor(config);
    
    if (!editor) {
        console.error('GrapesJS Editor başlatılamadı!');
        return null;
    }
    
    // Blokları kaydet
    if (window.StudioBlocks && typeof window.StudioBlocks.registerBlocks === 'function') {
        window.StudioBlocks.registerBlocks(editor);
    }
    
    // Plugin'leri yükle - StudioPluginLoader varsa ve loadPlugins fonksiyonu tanımlıysa
    if (window.StudioPluginLoader && typeof window.StudioPluginLoader.loadPlugins === 'function') {
        window.StudioPluginLoader.loadPlugins(editor);
    }
    
    // Arayüz etkileşimlerini ayarla
    if (window.StudioUI && typeof window.StudioUI.setupUI === 'function') {
        window.StudioUI.setupUI(editor);
    }
    
    // Eylem butonlarını ayarla
    if (window.StudioActions && typeof window.StudioActions.setupActions === 'function') {
        window.StudioActions.setupActions(editor, config);
    }
    
    console.log("Studio Editor başarıyla yüklendi!");
    
    return editor;
};