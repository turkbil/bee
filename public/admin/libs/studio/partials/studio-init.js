/**
 * Studio Editor - Başlatıcı
 * Studio Editor'ün sayfa yüklendiğinde başlatılmasını yönetir
 */
(function() {
    // DOM yüklendiğinde çalışacak kod
    document.addEventListener('DOMContentLoaded', function() {
        // Editor konfigürasyonu kontrol et
        const editorConfigScript = document.getElementById('editor-config');
        let config = null;
        
        if (editorConfigScript) {
            try {
                config = JSON.parse(editorConfigScript.textContent);
                console.log('Editor konfigürasyonu yüklendi:', config);
                initializeEditor(config);
            } catch (e) {
                console.error('Editor konfigürasyonu ayrıştırılamadı:', e);
                tryAlternativeConfig();
            }
        } else {
            tryAlternativeConfig();
        }
    });
    
    /**
     * Alternatif konfigürasyon arama
     */
    function tryAlternativeConfig() {
        // Sayfada manuel olarak tanımlanmamış, elementlerden bilgiyi çekelim
        const editorElement = document.getElementById('gjs');
        if (editorElement) {
            const moduleType = editorElement.getAttribute('data-module-type') || 'page';
            const moduleId = parseInt(editorElement.getAttribute('data-module-id') || '0');
            
            if (moduleId <= 0) {
                console.error('Geçersiz modül ID:', moduleId);
                return;
            }
            
            const config = {
                elementId: 'gjs',
                moduleType: moduleType,
                moduleId: moduleId,
                content: document.getElementById('html-content') ? document.getElementById('html-content').value : '',
                css: document.getElementById('css-content') ? document.getElementById('css-content').value : '',
                csrfToken: document.querySelector('meta[name="csrf-token"]') ? 
                          document.querySelector('meta[name="csrf-token"]').getAttribute('content') : ''
            };
            
            console.log('Alternatif konfigürasyon oluşturuldu:', config);
            initializeEditor(config);
        } else {
            console.error('Studio Editor başlatılamıyor: #gjs elementi bulunamadı!');
        }
    }
    
    /**
     * Editor'ü başlatır ve gerekli modülleri yükler
     * @param {Object} config - Editor konfigürasyonu
     */
    function initializeEditor(config) {
        if (!config || !config.moduleId || config.moduleId <= 0) {
            console.error('Geçersiz konfigürasyon veya modül ID:', config);
            return;
        }
        
        // Global değişkende sakla
        window.studioEditorConfig = config;
        
        // Editor başlat
        if (typeof window.initStudioEditor === 'function') {
            const editor = window.initStudioEditor(config);
            // Global erişim için kaydet
            window.studioEditor = editor;
        } else {
            console.error('Studio Editor başlatılamıyor: initStudioEditor fonksiyonu bulunamadı!');
        }
    }
})();