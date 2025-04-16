/**
 * Studio Editor Uygulama Başlatıcı
 * Tüm modülleri yükler ve uygulamayı başlatır
 */

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
            
            // Editor yükleme olayını dinle
            editor.on('load', function() {
                console.log('Editor yükleme olayı tetiklendi');
                
                // Blokları kaydet
                if (window.StudioBlocks && typeof window.StudioBlocks.registerBlocks === 'function') {
                    window.StudioBlocks.registerBlocks(editor);
                }
                
                // Arama özelliğini ayarla
                if (window.StudioBlocks && typeof window.StudioBlocks.setupBlockSearch === 'function') {
                    window.StudioBlocks.setupBlockSearch(editor);
                }
                
                // UI bileşenlerini ayarla
                if (window.StudioUI && typeof window.StudioUI.setupUI === 'function') {
                    window.StudioUI.setupUI(editor);
                }
                
                // Butonları ayarla
                if (window.StudioActions && typeof window.StudioActions.setupActions === 'function') {
                    window.StudioActions.setupActions(editor, config);
                }
                
                // Panel sekmelerini ayarla
                setupTabs();
            });
            
            // Global erişim için kaydet
            window.studioEditor = editor;
        } catch (error) {
            console.error('Studio Editor başlatılırken hata:', error);
        }
    } else {
        console.error('Studio Editor başlatılamıyor: initStudioEditor fonksiyonu bulunamadı!');
    }
});

/**
 * Sol panel sekmelerini ayarla
 */
function setupTabs() {
    const tabs = document.querySelectorAll(".panel-tab");
    const tabContents = document.querySelectorAll(".panel-tab-content");

    tabs.forEach((tab) => {
        // Eski event listener'ları temizle
        const newTab = tab.cloneNode(true);
        if (tab.parentNode) {
            tab.parentNode.replaceChild(newTab, tab);
        }
        
        newTab.addEventListener("click", function () {
            const tabName = this.getAttribute("data-tab");

            // Aktif tab değiştir
            tabs.forEach((t) => t.classList.remove("active"));
            this.classList.add("active");

            // İçeriği değiştir
            tabContents.forEach((content) => {
                if (content.getAttribute("data-tab-content") === tabName) {
                    content.classList.add("active");
                } else {
                    content.classList.remove("active");
                }
            });
        });
    });
}