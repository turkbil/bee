/**
 * Studio Editor - Sekme Modülü
 * Panel sekmelerini yönetme
 */

window.StudioTabs = (function() {
    // Sekmelerin başlatılıp başlatılmadığını takip et
    let tabsInitialized = false;
    
    /**
     * Sekmeleri başlat
     */
    function setupTabs() {
        // İşaretlenen işlemi tekrar yapmayı engelle
        if (tabsInitialized) {
            console.log("Panel sekmeleri zaten ayarlanmış, işlem atlanıyor");
            return;
        }
        tabsInitialized = true;
        
        // Tüm önceki event listener'ları temizle
        cleanupTabListeners();
        
        // Sol panel tablarını ayarla
        setupPanelTabs('left');
        
        // Sağ panel tablarını ayarla
        setupPanelTabs('right');
    }
    
    /**
     * Panel sekmelerini yapılandır
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
    
    return {
        setupTabs: setupTabs,
        activateTab: activateTab,
        setupPanelTabsOnStartup: setupPanelTabsOnStartup,
        cleanupTabListeners: cleanupTabListeners
    };
})();