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
                
                if (data.success && data.blocks && data.blocks.length > 0) {
                    console.log("Bloklar: ", data.blocks);
                    console.log("Kategoriler: ", data.categories);
                    
                    // Kategorileri tanımla
                    const categories = {};
                    Object.keys(data.categories || {}).forEach(key => {
                        categories[key] = data.categories[key];
                        console.log(`Kategori ekleniyor: ${key} - ${data.categories[key]}`);
                        editor.BlockManager.getCategories().add({ id: key, label: data.categories[key] });
                    });
                    
                    // GrapesJS kategorilerini kontrol et
                    const editorCategories = editor.BlockManager.getCategories().models;
                    console.log("GrapesJS Kategorileri: ", editorCategories);
                    
                    // Blokları ekle
                    data.blocks.forEach(block => {
                        console.log(`Blok ekleniyor: ${block.id} - ${block.label} - Kategori: ${block.category}`);
                        editor.BlockManager.add(block.id, {
                            label: block.label,
                            category: block.category,
                            attributes: { class: block.icon },
                            content: block.content
                        });
                    });
                    
                    // GrapesJS bloklarını kontrol et
                    const editorBlocks = editor.BlockManager.getAll().models;
                    console.log("GrapesJS Blokları: ", editorBlocks);
                    
                    console.log(`${data.blocks.length} adet blok başarıyla yüklendi`);
                    
                    // DOM'da blok kategorileri oluştur
                    console.log("Blok kategorileri oluşturuluyor...");
                    createBlockCategories(editor, data.categories || {});
                    
                    // Kategorilere blokları ekle
                    setTimeout(() => {
                        console.log("Bloklar kategorilere ekleniyor...");
                        updateBlocksInCategories(editor);
                    }, 1000);
                } else {
                    console.error("Blok yüklenemedi:", data.message || "Server yanıt vermedi veya blok bulunamadı");
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
            console.error("Blok container bulunamadı! ID: blocks-container eleman bulunamadı");
            
            // Alternatif metod: DOM'u tarayarak uygun container'ı bul
            const possibleContainers = document.querySelectorAll('.blocks-container, .gjs-blocks-container');
            if (possibleContainers.length > 0) {
                console.log("Alternatif container bulundu: ", possibleContainers[0]);
                blocksContainer = possibleContainers[0];
            } else {
                console.error("Hiçbir uygun container bulunamadı!");
                return;
            }
        }
        
        // Önce içeriği temizle
        console.log("Blok container içeriği temizleniyor...");
        blocksContainer.innerHTML = '';
        
        // DOM yolunu görüntüle
        let parent = blocksContainer;
        let domPath = 'blocks-container';
        while (parent.parentNode && parent.parentNode.tagName) {
            parent = parent.parentNode;
            domPath = parent.tagName + (parent.id ? '#' + parent.id : '') + ' > ' + domPath;
        }
        console.log("Blok container DOM yolu: ", domPath);
        
        console.log("Kategoriler oluşturuluyor:", categories);
        
        // Her kategori için bir div oluştur
        Object.keys(categories).forEach(categoryId => {
            const categoryName = categories[categoryId];
            const categoryIcon = getCategoryIcon(categoryId);
            
            console.log(`Kategori oluşturuluyor: ${categoryId} - ${categoryName}`);
            
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
            
            console.log(`Kategori DOM'a ekleniyor: ${categoryId}`);
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
        const blocks = editor.BlockManager.getAll().models;
        
        if (!blocks || blocks.length === 0) {
            console.warn('Güncellenecek blok bulunamadı. Editor blok sayısı: 0');
            return;
        }
        
        console.log(`Editor blokları güncelleniyor. Toplam ${blocks.length} blok var.`);
        
        // Her bir kategori için blokları işle
        const categories = document.querySelectorAll('.block-category');
        
        if (categories.length === 0) {
            console.error('Kategori elementleri bulunamadı. DOM\'da .block-category elementi mevcut değil.');
            
            // HTML yapısını görüntüle
            console.log("Blocks container HTML: ", document.getElementById('blocks-container')?.innerHTML || "Container bulunamadı");
            return;
        }
        
        console.log(`${categories.length} adet kategori elementi bulundu`);
        
        categories.forEach(category => {
            const categoryId = category.getAttribute('data-category');
            if (!categoryId) {
                console.warn('Kategori ID\'si bulunamadı');
                return;
            }
            
            console.log(`Kategori için bloklar işleniyor: ${categoryId}`);
            
            // Kategori içerik alanını temizle
            const blockItems = category.querySelector('.block-items');
            if (blockItems) {
                blockItems.innerHTML = '';
                
                // Bu kategoriye ait blokları ekle
                const categoryBlocks = blocks.filter(block => block.get('category') === categoryId);
                
                console.log(`${categoryId} kategorisine ${categoryBlocks.length} blok ekleniyor`);
                
                if (categoryBlocks.length === 0) {
                    blockItems.innerHTML = `<div class="block-empty">Bu kategoride blok bulunamadı</div>`;
                } else {
                    // Bu kategoriye ait blokları ekle
                    categoryBlocks.forEach(block => {
                        const blockId = block.get('id');
                        const blockLabel = block.get('label');
                        const blockAttributes = block.getAttributes() || {};
                        const iconClass = blockAttributes.class || 'fa fa-cube';
                        
                        console.log(`Blok ekleniyor: ${blockId} - ${blockLabel} - Icon: ${iconClass}`);
                        
                        const blockEl = document.createElement('div');
                        blockEl.className = 'block-item';
                        blockEl.setAttribute('data-block-id', blockId);
                        
                        // İçeriği oluştur
                        blockEl.innerHTML = `
                            <div class="block-item-icon">
                                <i class="${iconClass}"></i>
                            </div>
                            <div class="block-item-label">${blockLabel}</div>
                        `;
                        
                        // Drag-drop işlevini ekle
                        blockEl.setAttribute('draggable', 'true');
                        blockEl.addEventListener('dragstart', (e) => {
                            e.dataTransfer.setData('block-id', blockId);
                            blockEl.classList.add('dragging');
                        });
                        
                        blockEl.addEventListener('dragend', () => {
                            blockEl.classList.remove('dragging');
                        });
                        
                        blockEl.addEventListener('click', () => {
                            console.log(`Blok tıklandı: ${blockId}`);
                            // Blok içeriğini editöre ekle
                            editor.addComponents(block.get('content'));
                        });
                        
                        blockItems.appendChild(blockEl);
                    });
                }
            } else {
                console.error(`Kategori için block-items elementi bulunamadı: ${categoryId}`);
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
        if (!searchInput) {
            console.warn('Arama kutusu bulunamadı (#blocks-search)');
            return;
        }
        
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