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
    const leftPanelTabs = document.querySelectorAll(".panel__left .panel-tab");
    const leftPanelContents = document.querySelectorAll(".panel__left .panel-tab-content");
    
    const rightPanelTabs = document.querySelectorAll(".panel__right .panel-tab");
    const rightPanelContents = document.querySelectorAll(".panel__right .panel-tab-content");

    // Sol panel sekmeleri için
    leftPanelTabs.forEach((tab) => {
        // Eski event listener'ları temizle
        const newTab = tab.cloneNode(true);
        if (tab.parentNode) {
            tab.parentNode.replaceChild(newTab, tab);
        }
        
        newTab.addEventListener("click", function () {
            const tabName = this.getAttribute("data-tab");

            // Aktif tab değiştir (sadece sol panel içinde)
            leftPanelTabs.forEach((t) => t.classList.remove("active"));
            this.classList.add("active");

            // İçeriği değiştir (sadece sol panel içinde)
            leftPanelContents.forEach((content) => {
                if (content.getAttribute("data-tab-content") === tabName) {
                    content.classList.add("active");
                } else {
                    content.classList.remove("active");
                }
            });
            
            // Aktif sekme bilgisini localStorage'a kaydet (sol panel için)
            localStorage.setItem('studio_left_panel_tab', tabName);
        });
    });
    
    // Sağ panel sekmeleri için
    rightPanelTabs.forEach((tab) => {
        // Eski event listener'ları temizle
        const newTab = tab.cloneNode(true);
        if (tab.parentNode) {
            tab.parentNode.replaceChild(newTab, tab);
        }
        
        newTab.addEventListener("click", function () {
            const tabName = this.getAttribute("data-tab");

            // Aktif tab değiştir (sadece sağ panel içinde)
            rightPanelTabs.forEach((t) => t.classList.remove("active"));
            this.classList.add("active");

            // İçeriği değiştir (sadece sağ panel içinde)
            rightPanelContents.forEach((content) => {
                if (content.getAttribute("data-tab-content") === tabName) {
                    content.classList.add("active");
                } else {
                    content.classList.remove("active");
                }
            });
            
            // Aktif sekme bilgisini localStorage'a kaydet (sağ panel için)
            localStorage.setItem('studio_right_panel_tab', tabName);
        });
    });
    
    // Önceki aktif sekmeleri yükle
    const savedLeftTab = localStorage.getItem('studio_left_panel_tab');
    if (savedLeftTab) {
        const activeLeftTab = document.querySelector(`.panel__left .panel-tab[data-tab="${savedLeftTab}"]`);
        if (activeLeftTab) {
            activeLeftTab.click();
        }
    }
    
    const savedRightTab = localStorage.getItem('studio_right_panel_tab');
    if (savedRightTab) {
        const activeRightTab = document.querySelector(`.panel__right .panel-tab[data-tab="${savedRightTab}"]`);
        if (activeRightTab) {
            activeRightTab.click();
        }
    }
}