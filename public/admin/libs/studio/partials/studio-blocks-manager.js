/**
 * Studio Editor - Blok Yönetim Modülü
 * Blokları yönetme ve arama
 */

window.StudioBlockManager = (function() {
    // Blokların kategorilere eklenip eklenmediğini takip et
    let blocksUpdatedInCategories = false;
    
    /**
     * Editördeki blokları kategori elementlerine ekler
     * @param {Object} editor - GrapesJS editor örneği
     */
    function updateBlocksInCategories(editor) {
        // Bu metod yalnızca bir kez çalışmalı
        if (blocksUpdatedInCategories) {
            return;
        }
        blocksUpdatedInCategories = true;
        
        console.log("Editor blokları güncelleniyor. Toplam " + editor.BlockManager.getAll().length + " blok var.");
        
        // Her bir kategori için blokları işle
        const categories = document.querySelectorAll('.block-category');
        
        categories.forEach(category => {
            const categoryId = category.getAttribute('data-category');
            if (!categoryId) return;
            
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
                } 
                // Kategori değeri bir obje mi?
                else if (typeof blockCategory === 'object' && blockCategory !== null) {
                    // Objedeki id değeri ile karşılaştır
                    categoryMatch = blockCategory.id === categoryId;
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
                        
                        // Widget bloğu mu kontrol et
                        const isWidget = block.id.startsWith('widget-');
                        
                        if (isWidget && typeof blockContent === 'object') {
                            // Özel widget içeriğini doğru şekilde ekle
                            contentToAdd = blockContent.html;
                            
                            // Widget bilgisini data attribute olarak kaydet
                            if (blockContent.widget_id) {
                                const tempDiv = document.createElement('div');
                                tempDiv.innerHTML = contentToAdd;
                                const wrapperEl = tempDiv.querySelector('.gjs-widget-wrapper');
                                if (wrapperEl) {
                                    wrapperEl.setAttribute('data-widget-id', blockContent.widget_id);
                                    wrapperEl.setAttribute('data-type', 'widget');
                                }
                                contentToAdd = tempDiv.innerHTML;
                            }
                        } else if (typeof blockContent === 'string') {
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
                        
                        // Widget için ekstra bilgileri canvas'a aktar
                        if (isWidget && typeof blockContent === 'object') {
                            // Widget CSS ve JS verilerini kaydet
                            if (blockContent.css) {
                                const styleEl = document.createElement('style');
                                styleEl.innerHTML = blockContent.css;
                                styleEl.setAttribute('data-widget-css', 'true');
                                document.head.appendChild(styleEl);
                            }
                            
                            // Widget bilgilerini geçici olarak sakla
                            window._lastDraggedWidget = {
                                id: blockContent.widget_id,
                                html: blockContent.html,
                                css: blockContent.css,
                                js: blockContent.js
                            };
                        }
                    });
                    
                    blockEl.addEventListener('dragend', () => {
                        blockEl.classList.remove('dragging');
                        
                        // Geçici widget stil elementlerini temizle
                        const widgetStyles = document.querySelectorAll('style[data-widget-css="true"]');
                        widgetStyles.forEach(el => el.remove());
                        
                        // Widget bilgilerini temizle
                        setTimeout(() => {
                            window._lastDraggedWidget = null;
                        }, 300);
                    });
                    
                    // Tıklamayla içerik ekleme kaldırıldı, yalnızca sürükle-bırak özelliği korundu
                    blockEl.addEventListener('click', (e) => {
                        // Sadece blok seçimi için UI geri bildirimi göster (içerik ekleme yok)
                        blockEl.classList.add('selected');
                        setTimeout(() => {
                            blockEl.classList.remove('selected');
                        }, 300);
                        
                        // Kullanıcıya bileşenlerin sürükle-bırak ile ekleneceğini bildir
                        window.StudioNotification.info('Bileşeni eklemek için sürükleyip bırakın');
                        e.preventDefault();
                        e.stopPropagation();
                    });
                    
                    blockItems.appendChild(blockEl);
                });
            }
        });
        
        console.log("Bloklar başarıyla kategorilere eklendi");
    }
    
    /**
     * Blok araması için event listener ekle
     * @param {Object} editor - GrapesJS editor örneği
     */
    function setupBlockSearch(editor) {
        // Bu metod yalnızca bir kez çalışmalı
        if (window._searchSetupDone) {
            return;
        }
        window._searchSetupDone = true;
        
        const searchInput = document.getElementById('blocks-search');
        if (!searchInput) return;
        
        searchInput.addEventListener('input', function() {
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
            const label = item.querySelector('.block-item-label')?.textContent.toLowerCase() || '';
            const blockId = item.getAttribute('data-block-id') || '';
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
        updateBlocksInCategories: updateBlocksInCategories,
        setupBlockSearch: setupBlockSearch,
        filterBlocks: filterBlocks
    };
})();