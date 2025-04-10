/**
 * Studio Editor - Eklentiler Yükleyici
 * GrapesJS eklentilerini dinamik olarak yükler
 */
window.StudioPluginLoader = (function() {
    // Aktif eklentiler listesi
    const enabledPlugins = [
        'blocks-basic',
        'preset-webpage',
        'forms',
        'custom-code',
        'touch'
    ];
    
    // Eklenti yapılandırmaları
    const pluginConfigs = {
        'blocks-basic': {
            flexGrid: true
        },
        'preset-webpage': {
            modalImportTitle: 'HTML İçeri Aktar',
            modalImportLabel: 'HTML kodunu buraya yapıştırın',
            modalImportContent: '',
        },
        'forms': {
            // Forms eklentisi yapılandırması
        },
        'custom-code': {
            // Özel kod eklentisi yapılandırması
        }
    };
    
    /**
     * Eklentileri güvenli bir şekilde yükle
     * @param {Object} editor - GrapesJS editor örneği
     */
    function loadPlugins(editor) {
        if (!editor) {
            console.error('GrapesJS editor örneği bulunamadı!');
            return;
        }
        
        console.log('GrapesJS eklentileri yükleniyor...');
        
        // Eklenti yükleme kodları burada olacak - şimdilik simüle ediyoruz
        // Gerçekte bu eklentiler script etiketleriyle yükleniyor
        console.log('Aktif eklentiler:', enabledPlugins);
        
        // Temel bloklar ve bileşenler eklentisi aktif
        if (window.StudioPlugins && typeof window.StudioPlugins.loadPlugins === 'function') {
            window.StudioPlugins.loadPlugins(editor);
        }
        
        // Özel bileşenler kaydediliyor
        if (window.StudioPlugins && typeof window.StudioPlugins.registerCustomComponents === 'function') {
            window.StudioPlugins.registerCustomComponents(editor);
        }
        
        // Özel komutlar ekleniyor
        if (window.StudioPlugins && typeof window.StudioPlugins.addCustomCommands === 'function') {
            window.StudioPlugins.addCustomCommands(editor);
        }
    }
    
    return {
        loadPlugins: loadPlugins
    };
})();