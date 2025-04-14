/**
 * Studio Panel Manager
 * Panel yapısını oluşturan ve yöneten modül
 */
const StudioPanelManager = (function() {
    let editor = null;
    let panelConfig = {};

    /**
     * Panel yapısını oluştur
     * @param {Object} editorInstance GrapesJS editor örneği
     * @param {Object} options Panel yapılandırması
     */
    function setupPanels(editorInstance, options = {}) {
        editor = editorInstance;
        
        // Varsayılan panel yapılandırması
        panelConfig = {
            defaults: true,           // Varsayılan panelleri yükle
            styles: true,             // Stil panelini göster
            layers: true,             // Katmanlar panelini göster
            blocks: true,             // Bloklar panelini göster
            traits: true,             // Özellikler panelini göster
            deviceManager: true,      // Cihaz yöneticisini göster
            panelElementsSelector: '.panel-tab',              // Panel sekme seçicisi
            panelContentSelector: '.panel-tab-content',       // Panel içerik seçicisi
            activeTabClass: 'active',                         // Aktif sekme sınıfı
            ...options
        };
        
        // Tab sistemini başlat
        initTabs();
        
        // GrapesJS panellerini ayarla
        setupGrapesPanels();
        
        console.log('Panel yöneticisi başlatıldı');
    }
    
    /**
     * Tab sistemini başlat
     */
    function initTabs() {
        // Tab elemanlarını bul
        const tabElements = document.querySelectorAll(panelConfig.panelElementsSelector);
        
        if (!tabElements.length) {
            console.warn('Tab elemanları bulunamadı:', panelConfig.panelElementsSelector);
            return;
        }
        
        // Her tab için olay dinleyicisi ekle
        tabElements.forEach(tab => {
            tab.addEventListener('click', function() {
                // Tüm tabların aktif sınıfını kaldır
                tabElements.forEach(t => t.classList.remove(panelConfig.activeTabClass));
                
                // Bu taba aktif sınıfı ekle
                this.classList.add(panelConfig.activeTabClass);
                
                // Tab içeriğini göster
                const tabId = this.dataset.tab;
                handleTabSwitch(tabId);
            });
        });
        
        console.log('Tab sistemi başlatıldı');
    }
    
    /**
     * Tab değişimini yönet
     * @param {string} tabId Tab kimliği
     */
    function handleTabSwitch(tabId) {
        // Tüm tab içeriklerini gizle
        const contentElements = document.querySelectorAll(panelConfig.panelContentSelector);
        contentElements.forEach(content => content.classList.remove(panelConfig.activeTabClass));
        
        // Seçilen tab içeriğini göster
        const selectedContent = document.querySelector(`${panelConfig.panelContentSelector}[data-tab-content="${tabId}"]`);
        if (selectedContent) {
            selectedContent.classList.add(panelConfig.activeTabClass);
        }
        
        // GrapesJS panellerini güncelle
        switch (tabId) {
            case 'blocks':
                // Bloklar paneli gösterildiğinde blok aramayı aktif et
                setupBlockSearch();
                break;
            case 'styles':
                // Stil panelini göster
                showStyleManager();
                break;
            case 'layers':
                // Katmanlar panelini göster
                showLayerManager();
                break;
            case 'traits':
                // Özellikler panelini göster
                showTraitManager();
                break;
        }
        
        // Tab değişimi olayını tetikle
        const event = new CustomEvent('studio:tab-changed', { 
            detail: { 
                tabId 
            } 
        });
        document.dispatchEvent(event);
    }
    
    /**
     * GrapesJS panellerini ayarla
     */
    function setupGrapesPanels() {
        // Var olan panelleri temizle (isteğe bağlı)
        if (!panelConfig.defaults) {
            editor.Panels.getPanels().reset();
        }
        
        // Gereksiz panelleri kaldır
        removeUnnecessaryPanels();
        
        // Blok arama özelliğini ayarla
        setupBlockSearch();
        
        console.log('GrapesJS panelleri ayarlandı');
    }
        
    function setupBlockInteractions() {
        // Blok öğelerini seç
        const blockItems = document.querySelectorAll('.block-item');
        
        blockItems.forEach(item => {
            // Tıklama olayını kaldır - artık sadece sürükle-bırak çalışacak
            item.draggable = true;

            // Sürükleme başlangıç olayı
            item.addEventListener('dragstart', function(e) {
                e.stopPropagation();
                const blockId = this.getAttribute('data-block-id');
                e.dataTransfer.setData('text/plain', blockId);
                e.dataTransfer.effectAllowed = 'copy';
                
                // Sürükleme sırasında orijinal öğeyi gizle
                this.classList.add('dragging');
            });

            // Sürükleme bitişi olayı
            item.addEventListener('dragend', function(e) {
                e.stopPropagation();
                this.classList.remove('dragging');
            });
        });
    }

    /**
     * Blok arama özelliğini ayarla
     */
    function setupBlockSearch() {
        // Blok arama input'unu bul
        const searchInput = document.getElementById('blocks-search');
        
        if (!searchInput) {
            return;
        }
        
        // Mevcut olay dinleyicisini kaldır
        const newSearchInput = searchInput.cloneNode(true);
        searchInput.parentNode.replaceChild(newSearchInput, searchInput);
        
        // Arama olay dinleyicisini ekle
        newSearchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            const blockContainer = document.getElementById('blocks-container');
            
            // Tüm blok etiketlerini al
            const blockLabels = blockContainer.querySelectorAll('.gjs-block');
            
            if (query.length > 1) {
                // Her bloğu kontrol et
                blockLabels.forEach(block => {
                    const blockLabel = block.dataset.tooltip ? block.dataset.tooltip.toLowerCase() : 
                                      (block.querySelector('.gjs-block-label') ? 
                                       block.querySelector('.gjs-block-label').textContent.toLowerCase() : '');
                    
                    // Eşleşme kontrolü
                    if (blockLabel.includes(query)) {
                        block.style.display = 'block';
                        
                        // Ebeveyn blok kategorisini göster
                        let parent = block.parentNode;
                        while (parent && !parent.classList.contains('gjs-block-category')) {
                            parent = parent.parentNode;
                        }
                        if (parent) {
                            parent.style.display = 'block';
                        }
                    } else {
                        block.style.display = 'none';
                    }
                });
                
                // Boş kategorileri gizle
                const categories = blockContainer.querySelectorAll('.gjs-block-category');
                categories.forEach(category => {
                    const visibleBlocks = category.querySelectorAll('.gjs-block[style="display: block;"]');
                    if (visibleBlocks.length === 0) {
                        category.style.display = 'none';
                    }
                });
            } else {
                // Filtreyi sıfırla
                blockLabels.forEach(block => {
                    block.style.display = 'block';
                });
                
                // Tüm kategorileri göster
                const categories = blockContainer.querySelectorAll('.gjs-block-category');
                categories.forEach(category => {
                    category.style.display = 'block';
                });
            }
        });
    }
    
    /**
     * Stil yöneticisini göster
     */
    function showStyleManager() {
        if (!panelConfig.styles) {
            return;
        }
        
        const styleManager = editor.StyleManager;
        const stylePanelContainer = document.getElementById('styles-container');
        
        if (stylePanelContainer) {
            // Stil yöneticisini taşı
            styleManager.render(stylePanelContainer);
        }
    }
    
    /**
     * Katman yöneticisini göster
     */
    function showLayerManager() {
        if (!panelConfig.layers) {
            return;
        }
        
        const layerManager = editor.LayerManager;
        const layerPanelContainer = document.getElementById('layers-container');
        
        if (layerPanelContainer) {
            // Katman yöneticisini taşı
            layerManager.render(layerPanelContainer);
        }
    }
    
    /**
     * Özellik yöneticisini göster
     */
    function showTraitManager() {
        if (!panelConfig.traits) {
            return;
        }
        
        const traitManager = editor.TraitManager;
        const traitPanelContainer = document.getElementById('traits-container');
        
        if (traitPanelContainer) {
            // Özellik yöneticisini taşı
            traitManager.render(traitPanelContainer);
        }
    }
    
    /**
     * Gereksiz panelleri kaldır
     */
    function removeUnnecessaryPanels() {
        // Örnek: Varsayılan stilleri kaldır
        const styleManager = editor.StyleManager;
        const sectors = styleManager.getSectors();
        
        // Gereksiz sektörleri kaldır
        sectors.reset();
        
        // Özelleştirilmiş sektörler ekle
        styleManager.addSector('dimensions', {
            name: 'Boyut',
            open: true,
            properties: [
                'width',
                'height',
                'max-width',
                'min-height',
                'margin',
                'padding'
            ]
        });
        
        styleManager.addSector('typography', {
            name: 'Tipografi',
            open: false,
            properties: [
                'font-family',
                'font-size',
                'font-weight',
                'letter-spacing',
                'color',
                'line-height',
                'text-align',
                'text-decoration',
                'text-shadow'
            ]
        });
        
        styleManager.addSector('decorations', {
            name: 'Dekorasyon',
            open: false,
            properties: [
                'background-color',
                'border',
                'border-radius',
                'box-shadow'
            ]
        });
        
        styleManager.addSector('layout', {
            name: 'Düzen',
            open: false,
            properties: [
                'display',
                'position',
                'top',
                'right',
                'bottom',
                'left',
                'float',
                'clear',
                'z-index'
            ]
        });
        
        styleManager.addSector('flex', {
            name: 'Flex',
            open: false,
            properties: [
                'flex-direction',
                'flex-wrap',
                'justify-content',
                'align-items',
                'align-content',
                'order',
                'flex-basis',
                'flex-grow',
                'flex-shrink',
                'align-self'
            ]
        });
        
        styleManager.addSector('extra', {
            name: 'Ekstra',
            open: false,
            properties: [
                'opacity',
                'transition',
                'transform',
                'perspective',
                'transform-style'
            ]
        });
    }
    
    // Dışa aktarılan fonksiyonlar
    return {
        setupPanels: setupPanels,
        initTabs: initTabs,
        handleTabSwitch: handleTabSwitch
    };
})();

// Global olarak kullanılabilir yap
window.StudioPanelManager = StudioPanelManager;