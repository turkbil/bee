// Modules/Studio/resources/assets/js/blocks/registry.js

/**
 * Studio Blocks Registry
 * Blok yönetimini sağlayan temel modül
 */
const StudioBlocks = (function() {
    let editor = null;
    let categories = {};
    
    /**
     * Tüm blokları kaydet
     * @param {Object} editorInstance GrapesJS editor örneği
     */
    function registerBlocks(editorInstance) {
        editor = editorInstance;
        
        // Temel blokları kaydet
        if (typeof StudioBasicBlocks !== 'undefined') {
            StudioBasicBlocks.registerBasicBlocks(editor);
        }
        
        // Bootstrap bloklarını kaydet
        if (typeof StudioBootstrapBlocks !== 'undefined') {
            StudioBootstrapBlocks.registerBootstrapBlocks(editor);
        }
        
        // Medya bloklarını kaydet
        if (typeof StudioMediaBlocks !== 'undefined') {
            StudioMediaBlocks.registerMediaBlocks(editor);
        }
        
        console.log('Bloklar başarıyla kaydedildi');
    }
        
    /**
     * Blokları DOM'a render et
     * @param {Object} editorInstance GrapesJS editor örneği
     */
    function renderBlocksToDOM(editorInstance) {
        if (!editorInstance) return;
        
        // Blok panelini al
        const blockContainer = document.getElementById('blocks-container');
        if (!blockContainer) return;
        
        // Tüm blokları al
        const blockManager = editorInstance.BlockManager;
        const blocks = blockManager.getAll().models;
        
        console.log("Blokları hazırlıyorum, toplam:", blocks.length);
        
        // Kategori bazlı grupla
        const categorizedBlocks = {};
        
        // Kategorilerin sıralaması için
        const categoryOrder = [
            'temel', 'mizanpaj', 'bootstrap', 'medya', 'özel', 'widget', 'diğer'
        ];
        
        // Önce tüm kategorileri başlat
        categoryOrder.forEach(cat => {
            categorizedBlocks[cat] = [];
        });
        
        // Blokları ilgili kategorilere ekle
        blocks.forEach(block => {
            let category = block.get('category');
            
            // Kategori bir string değilse veya tanımlı değilse "diğer" kategorisine ekle
            if (typeof category !== 'string' || !category) {
                category = 'diğer';
            }
            
            // Kategori daha önce tanımlanmadıysa ekle
            if (!categorizedBlocks[category]) {
                categorizedBlocks[category] = [];
            }
            
            categorizedBlocks[category].push(block);
        });
        
        // HTML oluştur
        let blocksHTML = '';
        
        // Önce kategori sırasına göre, sonra diğer kategorileri ekle
        categoryOrder.forEach(category => {
            if (categorizedBlocks[category] && categorizedBlocks[category].length > 0) {
                // Kategori başlığı için düzgün bir format
                const categoryTitle = category.charAt(0).toUpperCase() + category.slice(1);
                
                blocksHTML += `
                    <div class="category-section mb-3">
                        <h6 class="category-title mb-2">${categoryTitle}</h6>
                        <div class="row g-2">
                `;
                
                categorizedBlocks[category].forEach(block => {
                    const icon = block.get('attributes')?.class || 'fa fa-cube';
                    
                    blocksHTML += `
                        <div class="col-6 mb-2">
                            <div class="block-item card p-2 text-center" data-block-id="${block.id}" draggable="true">
                                <i class="${icon} mb-1"></i>
                                <small>${block.get('label')}</small>
                            </div>
                        </div>
                    `;
                });
                
                blocksHTML += `
                        </div>
                    </div>
                `;
            }
        });
        
        // Kategori sırasında olmayan diğer kategorileri ekle
        Object.keys(categorizedBlocks).forEach(category => {
            if (!categoryOrder.includes(category) && categorizedBlocks[category].length > 0) {
                const categoryTitle = category.charAt(0).toUpperCase() + category.slice(1);
                
                blocksHTML += `
                    <div class="category-section mb-3">
                        <h6 class="category-title mb-2">${categoryTitle}</h6>
                        <div class="row g-2">
                `;
                
                categorizedBlocks[category].forEach(block => {
                    const icon = block.get('attributes')?.class || 'fa fa-cube';
                    
                    blocksHTML += `
                        <div class="col-6 mb-2">
                            <div class="block-item card p-2 text-center" data-block-id="${block.id}" draggable="true">
                                <i class="${icon} mb-1"></i>
                                <small>${block.get('label')}</small>
                            </div>
                        </div>
                    `;
                });
                
                blocksHTML += `
                        </div>
                    </div>
                `;
            }
        });
        
        if (blocksHTML === '') {
            blocksHTML = '<div class="alert alert-info">Henüz blok kaydedilmemiş.</div>';
        }
        
        // Bloklarla doldur
        blockContainer.innerHTML = blocksHTML;
        
        console.log("Bloklar hazırlandı ve DOM'a eklendi!");
        
        // Blok tıklama ve sürükleme olaylarını ekle
        setupBlockInteractions(editorInstance);
    }

    /**
     * Blok etkileşimlerini kur
     * @param {Object} editorInstance GrapesJS editor örneği
     */
    function setupBlockInteractions(editorInstance) {
        console.log("Blok etkileşimleri ayarlanıyor...");
        
        document.querySelectorAll('.block-item').forEach(item => {
            // MouseDown yerine dragstart kullan (sürükle bırak için)
            item.addEventListener('dragstart', function(e) {
                const blockId = this.getAttribute('data-block-id');
                console.log("Blok dragstart olayı, blockId:", blockId);
                e.dataTransfer.setData('blockId', blockId);
                e.dataTransfer.effectAllowed = 'copy';
            });
            
            // Tıklama olayı kaldırıldı - sadece sürükle-bırak aktif olacak
        });
        
        console.log("Blok etkileşimleri başarıyla ayarlandı - yalnızca sürükle-bırak aktif");
    }

    // Dışa aktarılan fonksiyonlar
    return {
        registerBlocks: registerBlocks,
        renderBlocksToDOM: renderBlocksToDOM
    };
})();

// Global olarak kullanılabilir yap
window.StudioBlocks = StudioBlocks;