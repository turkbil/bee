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

        if (!config || !config.moduleId || config.moduleId <= 0) {
            console.error('Geçersiz konfigürasyon veya modül ID:', config);
            window._studioEditorInitialized = false; // Hata durumunda bayrağı geri al
            return;
        }
        
        // Global değişkende sakla
        window.studioEditorConfig = config;
        
        // İlk önce varsayılan sekmeleri aktifleştir (editör yüklenmeden önce)
        setupPanelTabsOnStartup();
        
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
                    
                    // Bloklara içerik yüklendikten sonra blok sekmesini yeniden aktif et
                    setTimeout(function() {
                        activateTab('blocks', 'left');
                    }, 500);
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
     * Belirli bir sekmeyi aktif hale getirir
     * @param {string} tabName - Sekme adı
     * @param {string} panel - Panel tipi ('left' veya 'right')
     */
    function activateTab(tabName, panel = 'left') {
        const tabSelector = panel === 'left' ? 
            `.panel__left .panel-tab[data-tab="${tabName}"]` : 
            `.panel__right .panel-tab[data-tab="${tabName}"]`;
            
        const contentSelector = panel === 'left' ? 
            `.panel__left .panel-tab-content[data-tab-content="${tabName}"]` : 
            `.panel__right .panel-tab-content[data-tab-content="${tabName}"]`;
        
        // Sekmeyi aktifleştir
        const allTabs = document.querySelectorAll(panel === 'left' ? `.panel__left .panel-tab` : `.panel__right .panel-tab`);
        allTabs.forEach(tab => tab.classList.remove('active'));
        
        const tab = document.querySelector(tabSelector);
        if (tab) {
            tab.classList.add('active');
        }
        
        // İçeriği görünür yap
        const allContents = document.querySelectorAll(panel === 'left' ? `.panel__left .panel-tab-content` : `.panel__right .panel-tab-content`);
        allContents.forEach(content => content.classList.remove('active'));
        
        const content = document.querySelector(contentSelector);
        if (content) {
            content.classList.add('active');
        }
        
        // LocalStorage'e kaydet
        localStorage.setItem(panel === 'left' ? 'studio_left_panel_tab' : 'studio_right_panel_tab', tabName);
    }
    
    /**
     * Sayfa yüklendiğinde varsayılan sekmeleri aktifleştirir
     */
    function setupPanelTabsOnStartup() {
        // Sol panel
        const leftTab = localStorage.getItem('studio_left_panel_tab') || 'blocks';
        activateTab(leftTab, 'left');
        
        // Sağ panel
        const rightTab = localStorage.getItem('studio_right_panel_tab') || 'element-properties';
        activateTab(rightTab, 'right');
    }

    /**
     * Panel sekmelerini ayarla
     */
    function setupTabs() {
        // İşaretlenen işlemi tekrar yapmayı engelle
        if (window._tabsInitialized) {
            console.log("Panel sekmeleri zaten ayarlanmış, işlem atlanıyor");
            return;
        }
        window._tabsInitialized = true;
        
        // Tüm önceki event listener'ları temizle
        cleanupTabListeners();
        
        // Sol panel tablarını ayarla
        setupPanelTabs('left');
        
        // Sağ panel tablarını ayarla
        setupPanelTabs('right');
    }
    
    /**
     * Belirli bir panel için tab işlevlerini ayarla
     * @param {string} panelType - Panel tipi ('left' veya 'right')
     */
    function setupPanelTabs(panelType) {
        const panelSelector = panelType === 'left' ? '.panel__left' : '.panel__right';
        const tabs = document.querySelectorAll(`${panelSelector} .panel-tab`);
        
        tabs.forEach((tab) => {
            const clonedTab = tab.cloneNode(true);
            tab.parentNode.replaceChild(clonedTab, tab);
            
            clonedTab.addEventListener('click', () => {
                const tabName = clonedTab.getAttribute('data-tab');
                activateTab(tabName, panelType);
            });
        });
        
        // Varsayılan sekmeyi yükle
        const savedTab = localStorage.getItem(panelType === 'left' ? 'studio_left_panel_tab' : 'studio_right_panel_tab');
        if (savedTab) {
            activateTab(savedTab, panelType);
        } else {
            // Varsayılan değerler
            activateTab(panelType === 'left' ? 'blocks' : 'element-properties', panelType);
        }
    }
    
    /**
     * Tüm sekme olay dinleyicilerini temizle
     */
    function cleanupTabListeners() {
        // Sol panel tabları
        document.querySelectorAll('.panel__left .panel-tab').forEach(tab => {
            const clone = tab.cloneNode(true);
            tab.parentNode.replaceChild(clone, tab);
        });
        
        // Sağ panel tabları
        document.querySelectorAll('.panel__right .panel-tab').forEach(tab => {
            const clone = tab.cloneNode(true);
            tab.parentNode.replaceChild(clone, tab);
        });
    }
}