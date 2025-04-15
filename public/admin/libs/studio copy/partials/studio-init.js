/**
 * Studio Editor - Başlatıcı
 * Studio Editor'ün sayfa yüklendiğinde başlatılmasını yönetir
 */
(function() {
    // DOM yüklendiğinde çalışacak kod
    document.addEventListener('DOMContentLoaded', function() {
        // Direkt olarak alternatif konfigürasyonu kullanmayı tercih ediyoruz
        // çünkü JSON ayrıştırmasında sorunlar yaşanıyor
        tryAlternativeConfig();
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
            
            // Content ve CSS içeriklerini hidden input'lardan al
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
        // Sadece bir kez başlatıldığından emin ol
        if (window._studioEditorInitialized) {
            console.warn('Studio Editor zaten başlatılmış, tekrar başlatma işlemi atlanıyor.');
            return;
        }
        window._studioEditorInitialized = true;

        if (!config || !config.moduleId || config.moduleId <= 0) {
            console.error('Geçersiz konfigürasyon veya modül ID:', config);
            window._studioEditorInitialized = false; // Hata durumunda bayrağı geri al
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