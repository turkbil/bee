// public/admin/libs/studio/app.js

/**
 * Studio Editor Uygulama Başlatıcı
 * Tüm modülleri yükler ve uygulamayı başlatır
 */

// İkileme sorununu engellemek için global tekil başlatma kilidi
if (window._studioAppInitialized) {
    console.warn('Studio Editor uygulaması zaten başlatıldı, başlatma isteği atlanıyor');
} else {
    window._studioAppInitialized = true;
    
    document.addEventListener('DOMContentLoaded', function() {
        // Eğer başka bir yerde DOMContentLoaded olayı tetiklendiyse kilitli kalacak
        if (window._studioDOMLoadedHandled) {
            console.log('DOMContentLoaded olayı zaten işlendi, işlem atlanıyor');
            return;
        }
        window._studioDOMLoadedHandled = true;
        
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

        // Widget verilerini yükle
        if (window.StudioWidgetManager) {
            window.StudioWidgetManager.loadWidgetData();
        }

        // Geçerli bir konfigürasyon kontrolü
        if (!config || !config.moduleId || config.moduleId <= 0) {
            console.error('Geçersiz konfigürasyon veya modül ID:', config);
            window._studioEditorInitialized = false; // Hata durumunda bayrağı geri al
            return;
        }
        
        // Global değişkende sakla
        window.studioEditorConfig = config;
        
        // İlk önce varsayılan sekmeleri aktifleştir (editör yüklenmeden önce)
        if (window.StudioTabs && typeof window.StudioTabs.setupPanelTabsOnStartup === 'function') {
            window.StudioTabs.setupPanelTabsOnStartup();
        }
        
        // Editor başlat
        if (typeof window.StudioCore.initStudioEditor === 'function') {
            try {
                window.StudioCore.initStudioEditor(config);
            } catch (error) {
                console.error('Studio Editor başlatılırken hata:', error);
            }
        } else {
            console.error('Studio Editor başlatılamıyor: initStudioEditor fonksiyonu bulunamadı!');
        }
    });
}