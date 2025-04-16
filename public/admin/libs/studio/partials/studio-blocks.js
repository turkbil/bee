/**
 * Studio Editor - Bloklar Modülü
 * Blade şablonlarından yüklenen bloklar
 */

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
                        
                        // Blok konfigürasyonu
                        const blockConfig = {
                            label: block.label,
                            category: block.category,
                            content: block.content,
                            attributes: { class: block.icon || 'fa fa-cube' }
                        };
                        
                        editor.BlockManager.add(block.id, blockConfig);
                        
                        // Blok kategorisini kontrol et
                        const addedBlock = editor.BlockManager.get(block.id);
                        if (addedBlock) {
                            console.log(`${block.id} bloğu eklendi. Kategori:`, addedBlock.get('category'));
                        }
                    });
                    
                    console.log("GrapesJS Blokları: ", editor.BlockManager.getAll().models);
                    console.log(`${data.blocks.length} adet blok başarıyla yüklendi`);
                    
                    // Blokları kategorilere ata
                    console.log("Blok kategorileri oluşturuluyor...");
                    createBlockCategories(editor, data.categories || {});
                    
                    // Kategorilere blokları ekle
                    setTimeout(() => {
                        updateBlocksInCategories(editor);
                    }, 500);

                    // Arama işlevini ayarla
                    setupBlockSearch(editor);
                } else {
                    console.error("Blok yüklenemedi:", data.message || "Server yanıt vermedi");
                }
            })
            .catch(error => {
                console.error("Bloklar yüklenirken hata oluştu:", error);
            });
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
            const categoryBlocks = [];
            
            // GrapesJS blok koleksiyonunu doğru şekilde işle
            editor.BlockManager.getAll().each(block => {
                const blockCategory = block.get('category');
                
                // Kategori karşılaştırması - string ve obje kontrolü
                let categoryMatch = false;
                
                // Kategori değeri bir string mi?
                if (typeof blockCategory === 'string') {
                    categoryMatch = blockCategory === categoryId;
                    console.log(`Blok: ${block.id} - Kategori(string): ${blockCategory} - Eşleşme: ${categoryMatch}`);
                } 
                // Kategori değeri bir obje mi?
                else if (typeof blockCategory === 'object' && blockCategory !== null) {
                    // Objedeki id değeri ile karşılaştır
                    categoryMatch = blockCategory.id === categoryId;
                    console.log(`Blok: ${block.id} - Kategori(obje): id=${blockCategory.id} - Eşleşme: ${categoryMatch}`);
                }
                
                if (categoryMatch) {
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
                    blockEl.setAttribute('data-block-id', block.id);
                    
                    // İçeriği oluştur
                    let iconClass = 'fa fa-cube'; // Varsayılan ikon
                    
                    // Attributes özelliğini güvenli bir şekilde al
                    try {
                        const attributes = block.get('attributes');
                        if (attributes && attributes.class) {
                            iconClass = attributes.class;
                        }
                    } catch (error) {
                        console.warn(`"${block.id}" bloğu için öznitelikleri alırken hata:`, error);
                    }
                    
                    blockEl.innerHTML = `
                        <div class="block-item-icon">
                            <i class="${iconClass}"></i>
                        </div>
                        <div class="block-item-label">${block.get('label')}</div>
                    `;
                    
                    // Drag-drop işlevini ekle
                    blockEl.setAttribute('draggable', 'true');
                    blockEl.addEventListener('dragstart', (e) => {
                        // Blok içeriğini doğrudan aktarma
                        const blockContent = block.get('content');
                        let contentToAdd;
                        
                        if (typeof blockContent === 'string') {
                            contentToAdd = blockContent;
                        } else if (typeof blockContent === 'object' && blockContent.html) {
                            contentToAdd = blockContent.html;
                        } else {
                            contentToAdd = blockContent;
                        }
                        
                        // DataTransfer nesnesine içeriği aktar
                        e.dataTransfer.setData('text/html', contentToAdd);
                        e.dataTransfer.setData('text/plain', block.id);
                        blockEl.classList.add('dragging');
                    });
                    
                    blockEl.addEventListener('dragend', () => {
                        blockEl.classList.remove('dragging');
                    });
                    
                    blockEl.addEventListener('click', () => {
                        // İçerik string mi yoksa obje mi kontrol et
                        const content = block.get('content');
                        if (typeof content === 'string') {
                            editor.addComponents(content);
                        } else if (typeof content === 'object' && content.html) {
                            editor.addComponents(content.html);
                        } else {
                            editor.addComponents(block.get('content'));
                        }

                        // İşlem bildirimini göster
                        showToast('Blok eklendi', 'success');
                    });
                    
                    blockItems.appendChild(blockEl);
                });
            }
        });
        
        console.log("Bloklar başarıyla kategorilere eklendi");
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
                    <i class="${categoryIcon} text-primary"></i>
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

                // Kategori durumlarını localStorage'a kaydet
                saveBlockCategoryStates();
            });
        });
        
        console.log("Tüm kategoriler oluşturuldu");
        
        // Kategori durumlarını yükle
        loadBlockCategoryStates();
    }
    
    /**
     * Kategori açılıp kapanma durumlarını localStorage'a kaydet
     */
    function saveBlockCategoryStates() {
        const categories = document.querySelectorAll('.block-category');
        const states = {};
        
        categories.forEach(category => {
            const categoryId = category.getAttribute('data-category');
            if (categoryId) {
                states[categoryId] = category.classList.contains('collapsed');
            }
        });
        
        localStorage.setItem('studio_block_categories', JSON.stringify(states));
    }
    
    /**
     * Kategori açılıp kapanma durumlarını localStorage'dan yükle
     */
    function loadBlockCategoryStates() {
        const savedStates = localStorage.getItem('studio_block_categories');
        if (!savedStates) return;
        
        try {
            const states = JSON.parse(savedStates);
            const categories = document.querySelectorAll('.block-category');
            
            categories.forEach(category => {
                const categoryId = category.getAttribute('data-category');
                if (categoryId && states[categoryId] !== undefined) {
                    if (states[categoryId]) {
                        category.classList.add('collapsed');
                        const content = category.querySelector('.block-items');
                        if (content) {
                            content.style.display = 'none';
                        }
                    } else {
                        category.classList.remove('collapsed');
                        const content = category.querySelector('.block-items');
                        if (content) {
                            content.style.display = 'grid';
                        }
                    }
                }
            });
        } catch (e) {
            console.error('Block category states could not be loaded:', e);
        }
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

/**
 * Bildirim toast'ı göster
 * @param {string} message - Bildirim mesajı 
 * @param {string} type - Bildirim tipi (success, error, warning, info)
 */
function showToast(message, type = 'info') {
    // Toast container kontrol et
    let container = document.querySelector(".toast-container");
    if (!container) {
        container = document.createElement("div");
        container.className = "toast-container position-fixed top-0 end-0 p-3";
        container.style.zIndex = "9999";
        document.body.appendChild(container);
    }
    
    // Toast elementi oluştur
    const toastEl = document.createElement('div');
    toastEl.className = `toast align-items-center text-white bg-${
        type === "success" ? "success" : 
        type === "error" ? "danger" : 
        type === "warning" ? "warning" : 
        "info"
    } border-0`;
    toastEl.setAttribute('role', 'alert');
    toastEl.setAttribute('aria-live', 'assertive');
    toastEl.setAttribute('aria-atomic', 'true');
    
    // Toast içeriği
    toastEl.innerHTML = `
    <div class="d-flex">
        <div class="toast-body">
            <i class="fas ${
                type === "success" ? "fa-check-circle" : 
                type === "error" ? "fa-times-circle" : 
                type === "warning" ? "fa-exclamation-triangle" : 
                "fa-info-circle"
            } me-2"></i>
            ${message}
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Kapat"></button>
    </div>
    `;
    
    // Container'a ekle
    container.appendChild(toastEl);
    
    // Bootstrap toast API'si varsa kullan
    if (typeof bootstrap !== "undefined" && bootstrap.Toast) {
        const toast = new bootstrap.Toast(toastEl, {
            autohide: true,
            delay: 3000
        });
        toast.show();
    } else {
        // Fallback - basit toast gösterimi
        toastEl.style.display = 'block';
        setTimeout(() => {
            toastEl.style.opacity = '0';
            setTimeout(() => {
                if (container.contains(toastEl)) {
                    container.removeChild(toastEl);
                }
            }, 300);
        }, 3000);
    }
    
    // Otomatik kaldır
    setTimeout(() => {
        if (container.contains(toastEl)) {
            container.removeChild(toastEl);
        }
    }, 3300);
}

return {
    registerBlocks: registerBlocks,
    updateBlocksInCategories: updateBlocksInCategories,
    setupBlockSearch: setupBlockSearch,
    filterBlocks: filterBlocks,
    showToast: showToast,
    saveBlockCategoryStates: saveBlockCategoryStates,
    loadBlockCategoryStates: loadBlockCategoryStates
};
})();