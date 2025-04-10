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
    window.StudioPluginsLoader.loadPlugins(editor);
    
    // Blokları kaydet
    window.StudioBlocks.registerBlocks(editor);
    
    // Arayüz etkileşimlerini ayarla
    window.StudioUI.setupUI(editor);
    
    // Eylem butonlarını ayarla
    window.StudioActions.setupActions(editor, config);
    
    console.log("Studio Editor başarıyla yüklendi!");
    
    return editor;
};