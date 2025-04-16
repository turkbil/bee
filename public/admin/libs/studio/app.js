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
                
                // Editor yükleme olayını SADECE BİR KEZ dinle 
                // `once` ile event bir kez tetiklendikten sonra dinleyici kaldırılır
                editor.once('load', function() {
                    console.log('Editor yükleme olayı tetiklendi');
                    
                    // Blokları sadece bir kez yükle
                    if (window.StudioBlocks && typeof window.StudioBlocks.registerBlocks === 'function') {
                        window.StudioBlocks.registerBlocks(editor);
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
        // İşaretlenen işlemi tekrar yapmayı engelle
        if (window._tabsInitialized) {
            console.log("Panel sekmeleri zaten ayarlanmış, işlem atlanıyor");
            return;
        }
        window._tabsInitialized = true;
        
        const leftPanelTabs = document.querySelectorAll(".panel__left .panel-tab");
        const leftPanelContents = document.querySelectorAll(".panel__left .panel-tab-content");
        
        const rightPanelTabs = document.querySelectorAll(".panel__right .panel-tab");
        const rightPanelContents = document.querySelectorAll(".panel__right .panel-tab-content");

        // Event sayılarını saymak için - gerçekten her öğeye tek bir olay eklediğimizden emin olmak için
        let eventCounter = 0;

        // Sol panel sekmeleri için
        leftPanelTabs.forEach((tab) => {
            tab.addEventListener("click", function (event) {
                // Eğer bu tıklamada olay işlenmişse, tekrar işleme
                if (event._processed) return;
                event._processed = true;
                
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
                
                eventCounter++;
            });
        });
        
        // Sağ panel sekmeleri için
        rightPanelTabs.forEach((tab) => {
            tab.addEventListener("click", function (event) {
                // Eğer bu tıklamada olay işlenmişse, tekrar işleme
                if (event._processed) return;
                event._processed = true;
                
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
                
                eventCounter++;
            });
        });
        
        // Önceki aktif sekmeleri yükle
        const savedLeftTab = localStorage.getItem('studio_left_panel_tab');
        if (savedLeftTab) {
            const activeLeftTab = document.querySelector(`.panel__left .panel-tab[data-tab="${savedLeftTab}"]`);
            if (activeLeftTab) {
                // Programmatic click, _processed false olmalı
                const clickEvent = new MouseEvent('click');
                clickEvent._processed = false;
                activeLeftTab.dispatchEvent(clickEvent);
            }
        }
        
        const savedRightTab = localStorage.getItem('studio_right_panel_tab');
        if (savedRightTab) {
            const activeRightTab = document.querySelector(`.panel__right .panel-tab[data-tab="${savedRightTab}"]`);
            if (activeRightTab) {
                // Programmatic click, _processed false olmalı
                const clickEvent = new MouseEvent('click');
                clickEvent._processed = false;
                activeRightTab.dispatchEvent(clickEvent);
            }
        }
    }
}