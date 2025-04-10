/**
 * Studio Editor - Başlatıcı
 * Studio Editor'ün sayfa yüklendiğinde başlatılmasını yönetir
 */
(function() {
    // DOM yüklendiğinde çalışacak kod
    document.addEventListener('DOMContentLoaded', function() {
        // Editor konfigürasyonu sayfada tanımlanmış mı kontrol et
        if (typeof window.editorConfig === 'object') {
            initializeEditor(window.editorConfig);
        } else {
            // Sayfada manuel olarak tanımlanmamış, elementlerden bilgiyi çekelim
            const editorElement = document.getElementById('gjs');
            if (editorElement) {
                const config = {
                    elementId: 'gjs',
                    moduleType: editorElement.getAttribute('data-module-type') || 'page',
                    moduleId: parseInt(editorElement.getAttribute('data-module-id') || '0'),
                    content: document.getElementById('html-content') ? document.getElementById('html-content').value : '',
                    css: document.getElementById('css-content') ? document.getElementById('css-content').value : '',
                    csrfToken: document.querySelector('meta[name="csrf-token"]') ? 
                              document.querySelector('meta[name="csrf-token"]').getAttribute('content') : ''
                };
                
                initializeEditor(config);
            } else {
                console.error('Studio Editor başlatılamıyor: #gjs elementi bulunamadı!');
            }
        }
    });
    
    /**
     * Editor'ü başlatır ve gerekli modülleri yükler
     * @param {Object} config - Editor konfigürasyonu
     */
    function initializeEditor(config) {
        // Önce plugin yükleyiciyi çalıştır
        if (window.StudioPluginLoader && typeof window.StudioPluginLoader.loadPlugins === 'function') {
            window.StudioPluginLoader.loadPlugins().then(() => {
                // Sonra ana editor'ü başlat
                if (typeof window.initStudioEditor === 'function') {
                    window.initStudioEditor(config);
                } else {
                    console.error('Studio Editor başlatılamıyor: initStudioEditor fonksiyonu bulunamadı!');
                }
            }).catch(error => {
                console.error('Plugin yüklenirken hata oluştu:', error);
            });
        } else {
            // Plugin yükleyici yoksa direkt editor'ü başlat
            if (typeof window.initStudioEditor === 'function') {
                window.initStudioEditor(config);
            } else {
                console.error('Studio Editor başlatılamıyor: initStudioEditor fonksiyonu bulunamadı!');
            }
        }
    }
})();