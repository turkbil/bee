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
                    <div class="accordion-item category-section mb-2">
                        <h2 class="accordion-header" id="heading-${category}">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" 
                                    data-bs-target="#collapse-${category}" aria-expanded="true" 
                                    aria-controls="collapse-${category}">
                                ${categoryTitle}
                            </button>
                        </h2>
                        <div id="collapse-${category}" class="accordion-collapse collapse show" 
                             aria-labelledby="heading-${category}">
                            <div class="accordion-body p-2">
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
                    <div class="accordion-item category-section mb-2">
                        <h2 class="accordion-header" id="heading-${category}">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" 
                                    data-bs-target="#collapse-${category}" aria-expanded="true" 
                                    aria-controls="collapse-${category}">
                                ${categoryTitle}
                            </button>
                        </h2>
                        <div id="collapse-${category}" class="accordion-collapse collapse show" 
                             aria-labelledby="heading-${category}">
                            <div class="accordion-body p-2">
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
                        </div>
                    </div>
                `;
            }
        });
        
        if (blocksHTML === '') {
            blocksHTML = '<div class="alert alert-info">Henüz blok kaydedilmemiş.</div>';
        } else {
            // Akordiyon yapısına sar
            blocksHTML = `<div class="accordion" id="blocksAccordion">${blocksHTML}</div>`;
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
        document.querySelectorAll('.block-item').forEach(item => {
            // Sürükle başlangıç olayı
            item.addEventListener('dragstart', function(e) {
                const blockId = this.getAttribute('data-block-id');
                e.dataTransfer.setData('text/plain', blockId);
                e.dataTransfer.effectAllowed = 'copy';
                this.classList.add('dragging');
            });
            
            // Sürükle bitiş olayı
            item.addEventListener('dragend', function(e) {
                this.classList.remove('dragging');
            });
            
            // Tıklama olayı (yalnızca tıklama için - sürükle olmadığında)
            item.addEventListener('click', function(e) {
                const blockId = this.getAttribute('data-block-id');
                const block = editorInstance.BlockManager.get(blockId);
                
                if (block) {
                    // Blok içeriğini al
                    const content = block.get('content');
                    
                    // İçerik türüne göre editöre ekle
                    if (typeof content === 'string') {
                        const component = editorInstance.addComponents(content)[0];
                        if (component) {
                            // Eklenen bileşeni seç (düzenleme için)
                            editorInstance.select(component);
                        }
                    } else if (typeof content === 'object') {
                        const component = editorInstance.addComponents(editorInstance.DomComponents.addComponent(content))[0];
                        if (component) {
                            // Eklenen bileşeni seç (düzenleme için)
                            editorInstance.select(component);
                        }
                    }
                }
            });
        });
    }
    
    // Dışa aktarılan fonksiyonlar
    return {
        registerBlocks: registerBlocks,
        renderBlocksToDOM: renderBlocksToDOM
    };
})();

// Global olarak kullanılabilir yap
window.StudioBlocks = StudioBlocks;