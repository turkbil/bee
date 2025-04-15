/**
 * Studio Editor - Başlatıcı
 * Studio Editor'ün sayfa yüklendiğinde başlatılmasını yönetir
 */
(function() {
    // DOM yüklendiğinde çalışacak kod
    document.addEventListener('DOMContentLoaded', function() {
        // Editor element'ini bul
        const editorElement = document.getElementById('gjs');
        if (!editorElement) {
            console.log('Studio Editor başlatılamıyor: #gjs elementi bulunamadı!');
            return;
        }
        
        // Konfigürasyon oluştur
        const config = {
            elementId: 'gjs',
            module: editorElement.getAttribute('data-module-type') || 'page',
            moduleId: parseInt(editorElement.getAttribute('data-module-id') || '0'),
            content: document.getElementById('html-content') ? document.getElementById('html-content').value : '',
            css: document.getElementById('css-content') ? document.getElementById('css-content').value : '',
        };
        
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
            try {
                const editor = window.initStudioEditor(config);
                
                // Global erişim için kaydet
                window.studioEditor = editor;
            } catch (error) {
                console.error('Studio Editor başlatılırken hata:', error);
            }
        } else {
            console.error('Studio Editor başlatılamıyor: initStudioEditor fonksiyonu bulunamadı!');
        }
    });
})();