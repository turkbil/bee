/**
 * Studio Editor - Eklentiler Yükleyici
 * GrapesJS eklentilerini dinamik olarak yükler
 */
window.StudioPluginLoader = (function() {
    // Aktif eklentiler listesi - şimdilik boş bırakıyoruz
    const enabledPlugins = [];
    
    // Eklenti yapılandırmaları
    const pluginConfigs = {};
    
    /**
     * Eklentileri güvenli bir şekilde yükle
     * @param {Object} editor - GrapesJS editor örneği
     */
    function loadPlugins(editor) {
        if (!editor) {
            console.error('GrapesJS editor örneği bulunamadı!');
            return;
        }
        
        console.log('GrapesJS eklentileri devre dışı bırakıldı.');
    }
    
    return {
        loadPlugins: loadPlugins
    };
})();