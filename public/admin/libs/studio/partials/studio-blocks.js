/**
 * Studio Editor - Bloklar Modülü
 * Blade şablonlarından yüklenen bloklar
 */
// public/admin/libs/studio/partials/studio-blocks.js

window.StudioBlocks = (function() {
    /**
     * Blade şablonlarından blokları kaydet
     * @param {Object} editor - GrapesJS editor örneği
     */
    function registerBlocks(editor) {
        console.log("Server tarafından bloklar yükleniyor...");
        
        // Reset mevcut kategoriler ve bloklar
        editor.BlockManager.getAll().reset();
        editor.BlockManager.getCategories().reset();
        
        // Server'dan blokları al
        fetch('/admin/studio/api/blocks')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.blocks) {
                    // Kategorileri tanımla
                    const categories = {};
                    Object.keys(data.categories || {}).forEach(key => {
                        categories[key] = data.categories[key];
                        editor.BlockManager.getCategories().add({ id: key, label: data.categories[key] });
                    });
                    
                    // Blokları ekle
                    data.blocks.forEach(block => {
                        editor.BlockManager.add(block.id, {
                            label: block.label,
                            category: block.category,
                            attributes: { class: block.icon },
                            content: block.content
                        });
                    });
                    
                    console.log(`${data.blocks.length} adet blok başarıyla yüklendi`);
                    
                    // DOM'da blok kategorileri oluştur
                    createBlockCategories(editor, data.categories);
                } else {
                    console.error("Blok yüklenemedi:", data.message || "Server yanıt vermedi");
                }
            })
            .catch(error => {
                console.error("Bloklar yüklenirken hata oluştu:", error);
            });
    }
    
    /**
     * DOM'da blok kategorileri oluştur
     * @param {Object} editor - GrapesJS editor örneği
     * @param {Object} categories - Kategori bilgileri
     */
    function createBlockCategories(editor, categories) {
        const blocksContainer = document.getElementById('blocks-container');
        
        if (!blocksContainer) {
            console.error("Blok container bulunamadı!");
            return;
        }
        
        // Önce içeriği temizle
        blocksContainer.innerHTML = '';
        
        // Her kategori için bir div oluştur
        Object.keys(categories).forEach(categoryId => {
            const categoryName = categories[categoryId];
            const categoryIcon = getCategoryIcon(categoryId);
            
            const categoryDiv = document.createElement('div');
            categoryDiv.className = 'block-category';
            categoryDiv.setAttribute('data-category', categoryId);
            
            categoryDiv.innerHTML = `
                <div class="block-category-header">
                    <i class="${categoryIcon}"></i>
                    <span>${categoryName}</span>
                    <i class="fas fa-chevron-down toggle-icon"></i>
                </div>
                <div class="block-items"></div>
            `;
            
            blocksContainer.appendChild(categoryDiv);
            
            // Kategori başlığına tıklama olayı ekle
            const header = categoryDiv.querySelector('.block-category-header');
            header.addEventListener('click', function() {
                categoryDiv.classList.toggle('collapsed');
                const itemsContainer = categoryDiv.querySelector('.block-items');
                if (categoryDiv.classList.contains('collapsed')) {
                    itemsContainer.style.display = 'none';
                } else {
                    itemsContainer.style.display = 'grid';
                }
            });
        });
        
        // Kategorilere blokları ekle
        setTimeout(() => {
            updateBlocksInCategories(editor);
        }, 500);
    }
    
    /**
     * Kategori için ikon sınıfını al
     * @param {string} categoryId - Kategori ID'si
     * @return {string} - İkon sınıfı
     */
    function getCategoryIcon(categoryId) {
        const icons = {
            'layout': 'fa fa-columns',
            'content': 'fa fa-font',
            'form': 'fa fa-wpforms',
            'media': 'fa fa-image',
            'widget': 'fa fa-puzzle-piece',
            'hero': 'fa fa-star',
            'cards': 'fa fa-id-card',
            'features': 'fa fa-list-check',
            'testimonials': 'fa fa-quote-right',
        };
        
        return icons[categoryId] || 'fa fa-cube';
    }
    
    /**
     * Editördeki blokları kategori elementlerine ekler
     * @param {Object} editor - GrapesJS editor örneği
     */
    function updateBlocksInCategories(editor) {
        if (!editor) {
            console.error('Editor örneği bulunamadı');
            return;
        }
        
        // Tüm blokları al
        const blocks = editor.BlockManager.getAll();
        
        // Her bir kategori için blokları işle
        const categories = document.querySelectorAll('.block-category');
        
        categories.forEach(category => {
            const categoryId = category.getAttribute('data-category');
            if (!categoryId) return;
            
            // Kategori içerik alanını temizle
            const blockItems = category.querySelector('.block-items');
            if (blockItems) {
                blockItems.innerHTML = '';
                
                // Bu kategoriye ait blokları ekle
                blocks.filter(block => block.get('category') === categoryId).forEach(block => {
                    const blockEl = document.createElement('div');
                    blockEl.className = 'block-item';
                    blockEl.setAttribute('data-block-id', block.get('id'));
                    
                    // İçeriği oluştur
                    blockEl.innerHTML = `
                        <div class="block-item-icon">
                            <i class="${block.getAttributes().class || 'fa fa-cube'}"></i>
                        </div>
                        <div class="block-item-label">${block.get('label')}</div>
                    `;
                    
                    // Drag-drop işlevini ekle
                    blockEl.setAttribute('draggable', 'true');
                    blockEl.addEventListener('dragstart', (e) => {
                        e.dataTransfer.setData('block-id', block.get('id'));
                        blockEl.classList.add('dragging');
                    });
                    
                    blockEl.addEventListener('dragend', () => {
                        blockEl.classList.remove('dragging');
                    });
                    
                    blockEl.addEventListener('click', () => {
                        // Blok içeriğini editöre ekle
                        editor.addComponents(block.get('content'));
                    });
                    
                    blockItems.appendChild(blockEl);
                });
            }
        });
        
        // Arama işlevini ekle
        setupBlockSearch(editor);
    }
    
    /**
     * Blok araması için event listener ekle
     * @param {Object} editor - GrapesJS editor örneği
     */
    function setupBlockSearch(editor) {
        const searchInput = document.getElementById('blocks-search');
        if (!searchInput) return;
        
        // Eski event listener'ı temizle
        const newSearchInput = searchInput.cloneNode(true);
        if (searchInput.parentNode) {
            searchInput.parentNode.replaceChild(newSearchInput, searchInput);
        }
        
        newSearchInput.addEventListener('input', function() {
            const searchText = this.value.toLowerCase();
            filterBlocks(searchText, editor);
        });
    }
    
    /**
     * Arama metnine göre blokları filtrele
     * @param {string} searchText - Arama metni
     * @param {Object} editor - GrapesJS editor örneği
     */
    function filterBlocks(searchText, editor) {
        const blockItems = document.querySelectorAll('.block-item');
        const categories = document.querySelectorAll('.block-category');
        
        // Arama metni boşsa tüm kategorileri ve blokları göster
        if (!searchText) {
            blockItems.forEach(item => {
                item.style.display = 'flex';
            });
            
            categories.forEach(category => {
                category.style.display = 'block';
                if (category.classList.contains('collapsed')) {
                    category.classList.remove('collapsed');
                    const itemsContainer = category.querySelector('.block-items');
                    if (itemsContainer) {
                        itemsContainer.style.display = 'grid';
                    }
                }
            });
            return;
        }
        
        // Her bloğu kontrol et
        let visibleCategoryIds = new Set();
        
        blockItems.forEach(item => {
            const label = item.querySelector('.block-item-label').textContent.toLowerCase();
            const blockId = item.getAttribute('data-block-id');
            const visible = label.includes(searchText) || blockId.includes(searchText);
            
            item.style.display = visible ? 'flex' : 'none';
            
            if (visible) {
                const category = item.closest('.block-category');
                if (category) {
                    visibleCategoryIds.add(category.getAttribute('data-category'));
                }
            }
        });
        
        // Kategorileri göster/gizle
        categories.forEach(category => {
            const categoryId = category.getAttribute('data-category');
            if (visibleCategoryIds.has(categoryId)) {
                category.style.display = 'block';
                if (category.classList.contains('collapsed')) {
                    category.classList.remove('collapsed');
                    const itemsContainer = category.querySelector('.block-items');
                    if (itemsContainer) {
                        itemsContainer.style.display = 'grid';
                    }
                }
            } else {
                category.style.display = 'none';
            }
        });
    }
    
    return {
        registerBlocks: registerBlocks,
        updateBlocksInCategories: updateBlocksInCategories
    };
})();