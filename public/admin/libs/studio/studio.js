/**
 * Studio Editor Ana Modül
 * Tüm modülleri birleştiren ve başlatan ana dosya
 */

/**
 * Studio Editor için GrapesJS yapılandırması
 */
window.initStudioEditor = function (config) {
    // GrapesJS Editor yapılandırması
    const editor = window.StudioCore.initEditor(config);
    
    // Plugin'leri yükle
    if (window.StudioPlugins && typeof window.StudioPlugins.loadPlugins === 'function') {
        window.StudioPlugins.loadPlugins(editor);
    }
    
    // Blokları kaydet
    if (window.StudioBlocks && typeof window.StudioBlocks.registerBlocks === 'function') {
        window.StudioBlocks.registerBlocks(editor);
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