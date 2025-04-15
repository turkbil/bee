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
            .then(response => {
                console.log("API yanıtı alındı:", response.status);
                return response.json();
            })
            .then(data => {
                console.log("API verileri alındı:", data);
                console.log("Bloklar: ", data.blocks);
                console.log("Kategoriler: ", data.categories);
                
                if (data.success && data.blocks) {
                    // Kategorileri tanımla
                    Object.keys(data.categories || {}).forEach(key => {
                        console.log("Kategori ekleniyor:", key, "-", data.categories[key]);
                        editor.BlockManager.getCategories().add({ 
                            id: key, 
                            label: data.categories[key] 
                        });
                    });
                    
                    console.log("GrapesJS Kategorileri: ", editor.BlockManager.getCategories().models);
                    
                    // Blokları ekle
                    data.blocks.forEach(block => {
                        console.log("Blok ekleniyor:", block.id, "-", block.label, "-", "Kategori:", block.category);
                        editor.BlockManager.add(block.id, {
                            label: block.label,
                            category: block.category,
                            attributes: { class: block.icon || 'fa fa-cube' },
                            content: block.content
                        });
                    });
                    
                    console.log("GrapesJS Blokları: ", editor.BlockManager.getAll().models);
                    console.log(`${data.blocks.length} adet blok başarıyla yüklendi`);
                    
                    // Blokları kategorilere atamalıyız
                    console.log("Blok kategorileri oluşturuluyor...");
                    createBlockCategories(editor, data.categories || {});
                    
                    // Kategorilere blokları ekle
                    setTimeout(() => {
                        updateBlocksInCategories(editor);
                    }, 500);
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
        console.log("Blok container içeriği temizleniyor...");
        blocksContainer.innerHTML = '';
        
        // DOM yolunu göster
        let element = blocksContainer;
        let path = element.id;
        while(element.parentElement) {
            element = element.parentElement;
            if (element.tagName) 
                path = element.tagName + " > " + path;
        }
        console.log("Blok container DOM yolu: ", path);
        
        // Her kategori için bir div oluştur
        console.log("Kategoriler oluşturuluyor:", categories);
        Object.keys(categories).forEach(categoryId => {
            const categoryName = categories[categoryId];
            const categoryIcon = getCategoryIcon(categoryId);
            
            console.log("Kategori oluşturuluyor:", categoryId, "-", categoryName);
            
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
            console.log("Kategori DOM'a ekleniyor:", categoryId);
            
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
        
        console.log("Tüm kategoriler oluşturuldu");
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
            'pricing': 'fa fa-tag'
        };
        
        return icons[categoryId] || 'fa fa-cube';
    }
    
    /**
     * Editördeki blokları kategori elementlerine ekler
     * @param {Object} editor - GrapesJS editor örneği
     */
    function updateBlocksInCategories(editor) {
        console.log("Editor blokları güncelleniyor. Toplam " + editor.BlockManager.getAll().length + " blok var.");
        
        // Her bir kategori için blokları işle
        const categories = document.querySelectorAll('.block-category');
        console.log(categories.length + " adet kategori elementi bulundu");
        
        categories.forEach(category => {
            const categoryId = category.getAttribute('data-category');
            if (!categoryId) return;
            
            console.log("Kategori için bloklar işleniyor:", categoryId);
            
            // Bu kategoriye ait blokları al
            // ÖNEMLİ DÜZELTME: BlockManager.getAll().filter() yerine doğrudan array üzerinde filter kullanılmalı
            const categoryBlocks = [];
            editor.BlockManager.getAll().models.forEach(block => {
                if (block.get('category') === categoryId) {
                    categoryBlocks.push(block);
                }
            });
            
            // Kategori içerik alanını bul
            const blockItems = category.querySelector('.block-items');
            if (blockItems) {
                // İçeriği temizle
                blockItems.innerHTML = '';
                
                console.log(categoryId + " kategorisine " + categoryBlocks.length + " blok ekleniyor");
                
                // Bu kategoriye blok yok mesajı göster
                if (categoryBlocks.length === 0) {
                    const emptyMessage = document.createElement('div');
                    emptyMessage.className = 'block-empty';
                    emptyMessage.textContent = 'Bu kategoride blok bulunamadı';
                    blockItems.appendChild(emptyMessage);
                    return;
                }
                
                // Bu kategoriye ait blokları ekle
                categoryBlocks.forEach(block => {
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
                        const blockId = block.get('id');
                        e.dataTransfer.setData('text/plain', blockId);
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
        
        console.log("Bloklar başarıyla kategorilere eklendi");
        
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
        
        console.log("Arama işlevi ayarlandı");
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