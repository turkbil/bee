/**
 * Studio Editor - Panel Manager
 * Panel yapısını oluşturur ve yönetir
 */
const StudioPanelManager = (function() {
    /**
     * Panel yapısını kurar
     */
    function setupPanels() {
        initTabs();
        setupBlocksPanel();
        setupStylesPanel();
        setupLayersPanel();
        setupTraitsPanel();
    }
    
    /**
     * Sekme sistemini başlatır
     */
    function initTabs() {
        const tabs = document.querySelectorAll('.panel-tab');
        const tabContents = document.querySelectorAll('.panel-tab-content');
        
        tabs.forEach(tab => {
            // Eski dinleyicileri temizle
            const newTab = tab.cloneNode(true);
            if (tab.parentNode) {
                tab.parentNode.replaceChild(newTab, tab);
            }
            
            // Sekme tıklama olayını ekle
            newTab.addEventListener('click', function() {
                handleTabSwitch(this, tabs, tabContents);
            });
        });
        
        console.log('Sekmeler başlatıldı.');
    }
    
    /**
     * Sekme değişikliğini işler
     * @param {HTMLElement} clickedTab - Tıklanan sekme
     * @param {NodeList} tabs - Tüm sekmeler
     * @param {NodeList} tabContents - Tüm sekme içerikleri
     */
    function handleTabSwitch(clickedTab, tabs, tabContents) {
        const tabName = clickedTab.getAttribute('data-tab');
        
        // Aktif sekmeyi değiştir
        tabs.forEach(t => t.classList.remove('active'));
        clickedTab.classList.add('active');
        
        // İçeriği değiştir
        tabContents.forEach(content => {
            if (content.getAttribute('data-tab-content') === tabName) {
                content.classList.add('active');
            } else {
                content.classList.remove('active');
            }
        });
        
        console.log(`"${tabName}" sekmesine geçildi.`);
    }
    
    /**
     * Bloklar panelini kurar
     */
    function setupBlocksPanel() {
        // Blok arama işlevselliği
        const searchInput = document.getElementById('blocks-search');
        if (searchInput) {
            // Eski dinleyicileri temizle
            const newSearchInput = searchInput.cloneNode(true);
            if (searchInput.parentNode) {
                searchInput.parentNode.replaceChild(newSearchInput, searchInput);
            }
            
            // Arama olayını ekle
            newSearchInput.addEventListener('input', function() {
                filterBlocks(this.value);
            });
        }
        
        console.log('Bloklar paneli kuruldu.');
    }
    
    /**
     * Arama terimini kullanarak blokları filtreler
     * @param {string} searchTerm - Arama terimi
     */
    function filterBlocks(searchTerm) {
        const term = searchTerm.toLowerCase();
        const blockItems = document.querySelectorAll('.block-item');
        
        blockItems.forEach(block => {
            const label = block.querySelector('.block-item-label');
            if (label) {
                const text = label.textContent.toLowerCase();
                if (text.includes(term)) {
                    block.style.display = '';
                    
                    // Üst kategoriyi göster
                    const category = block.closest('.block-category');
                    if (category) {
                        category.style.display = '';
                        category.classList.remove('collapsed');
                    }
                } else {
                    block.style.display = 'none';
                }
            }
        });
        
        // Boş kategorileri gizle
        const categories = document.querySelectorAll('.block-category');
        
        categories.forEach(category => {
            const visibleBlocks = Array.from(category.querySelectorAll('.block-item')).filter(
                block => block.style.display !== 'none'
            );
            
            if (visibleBlocks.length === 0) {
                category.style.display = 'none';
            } else {
                category.style.display = '';
            }
        });
    }
    
    /**
     * Stiller panelini kurar
     */
    function setupStylesPanel() {
        // Stil paneli için özel işlemler
        console.log('Stiller paneli kuruldu.');
    }
    
    /**
     * Katmanlar panelini kurar
     */
    function setupLayersPanel() {
        // Katmanlar paneli için özel işlemler
        console.log('Katmanlar paneli kuruldu.');
    }
    
    /**
     * Özellikler panelini kurar
     */
    function setupTraitsPanel() {
        // Özellikler paneli için özel işlemler
        console.log('Özellikler paneli kuruldu.');
    }
    
    // Dışa aktarılan API
    return {
        setupPanels,
        initTabs,
        handleTabSwitch,
        filterBlocks
    };
})();

// Global olarak kullanılabilir yap
window.StudioPanelManager = StudioPanelManager;