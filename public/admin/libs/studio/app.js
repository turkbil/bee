// public/admin/libs/studio/app.js

/**
 * Studio Editor Uygulama Başlatıcı
 * Tüm modülleri yükler ve uygulamayı başlatır
 */

// Global değişkenler - tek bir noktada tanımlanıyor
if (typeof window.__STUDIO_EDITOR_INSTANCE === 'undefined') {
    window.__STUDIO_EDITOR_INSTANCE = null;
    window.__STUDIO_EDITOR_INITIALIZED = false;
    console.log('Global Studio Editor değişkenleri oluşturuldu');
}

// İkileme sorununu engellemek için global tekil başlatma kilidi
if (window.__STUDIO_EDITOR_INITIALIZED) {
    console.log('Studio Editor uygulaması zaten başlatıldı, başlatma isteği atlanıyor');
} else {
    document.addEventListener('DOMContentLoaded', function() {
        // Editor element'ini bul
        const editorElement = document.getElementById('gjs');
        if (!editorElement) {
            console.log('Studio Editor başlatılamıyor: #gjs elementi bulunamadı!');
            return;
        }
        
        // Editör zaten başlatılmışsa çıkış yap
        if (window.__STUDIO_EDITOR_INITIALIZED) {
            console.log('Studio Editor zaten başlatılmış, DOMContentLoaded olayı atlanıyor');
            return;
        }
        
        // Global başlatma bayrağını hemen ayarla, diğer kodların müdahale etmesini engelle
        window.__STUDIO_EDITOR_INITIALIZED = true;
        
        // Konfigürasyon oluştur
        const config = {
            elementId: 'gjs',
            module: editorElement.getAttribute('data-module-type') || 'page',
            moduleId: parseInt(editorElement.getAttribute('data-module-id') || '0'),
            content: document.getElementById('html-content') ? document.getElementById('html-content').value : '',
            css: document.getElementById('css-content') ? document.getElementById('css-content').value : '',
        };
        
        // Global değişkende sakla
        window.studioEditorConfig = config;
        
        // İlk önce varsayılan sekmeleri aktifleştir (editör yüklenmeden önce)
        if (window.StudioTabs && typeof window.StudioTabs.setupPanelTabsOnStartup === 'function') {
            window.StudioTabs.setupPanelTabsOnStartup();
        }
        
        // Editor başlat
        if (typeof window.StudioCore !== 'undefined' && typeof window.StudioCore.initStudioEditor === 'function') {
            try {
                // StudioCore başlatma - Global kilitleme mekanizmasıyla
                window.__STUDIO_EDITOR_INSTANCE = window.StudioCore.initStudioEditor(config);
            } catch (error) {
                console.error('Studio Editor başlatılırken hata:', error);
                window.__STUDIO_EDITOR_INITIALIZED = false;
            }
        } else {
            console.error('Studio Editor başlatılamıyor: StudioCore modülü bulunamadı!');
            window.__STUDIO_EDITOR_INITIALIZED = false;
        }
    });
}