/**
 * Studio Editor - Blok Yönetim Modülü
 * Blokları yönetme ve arama
 */

window.StudioBlockManager = (function() {
    // Güçlendirilmiş guard sistemi
    let blocksUpdatedInCategories = false;
    let isUpdating = false;
    
    /**
     * Editördeki blokları kategori elementlerine ekler
     * @param {Object} editor - GrapesJS editor örneği
     */
    function updateBlocksInCategories(editor) {
        // Çift kontrol - hem işlem durumu hem de tamamlanma durumu
        if (blocksUpdatedInCategories || isUpdating) {
            console.log("Bloklar zaten kategorilere eklendi, işlem atlanıyor.");
            return;
        }
        
        // İşlem başladığını işaretle
        isUpdating = true;
        
        console.log("Editor blokları güncelleniyor. Toplam " + editor.BlockManager.getAll().length + " blok var.");
        
        try {
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
                        
                        // Tenant widget bloklarını, içerikte bağlı embed varsa pasifleştir
                        if (block.id.startsWith('tenant-widget-')) {
                            const widgetId = block.id.replace('tenant-widget-', '');
                            const comps = editor.DomComponents.getWrapper().find(`[data-tenant-widget-id="${widgetId}"]`);
                            if (comps && comps.length > 0) {
                                blockEl.classList.add('disabled');
                                blockEl.setAttribute('draggable', 'false');
                                blockEl.draggable = false;
                                blockEl.style.cursor = 'not-allowed';
                            } else {
                                blockEl.classList.remove('disabled');
                                blockEl.setAttribute('draggable', 'true');
                                blockEl.draggable = true;
                                blockEl.style.cursor = 'grab';
                            }
                        } else {
                            blockEl.classList.remove('disabled');
                            blockEl.setAttribute('draggable', 'true');
                            blockEl.draggable = true;
                            blockEl.style.cursor = 'grab';
                        }
                        
                        // Event listener'ı sadece bir kez ekle
                        if (!blockEl.hasAttribute('data-events-added')) {
                            blockEl.setAttribute('data-events-added', 'true');
                            
                            blockEl.addEventListener('dragstart', (e) => {
                                // Pasif blok ise sürüklemeyi engelle
                                if (blockEl.classList.contains('disabled')) {
                                    e.preventDefault();
                                    return;
                                }
                                // Canvas iframe'ini bul
                                const editorFrame = document.querySelector('.gjs-frame');
                                const iframDocument = editorFrame ? editorFrame.contentDocument || editorFrame.contentWindow.document : null;
                                
                                // Widget mi kontrol et
                                const isActiveWidget = block.id.startsWith('tenant-widget-');
                                const isWidget = block.id.startsWith('widget-') || isActiveWidget;
                                
                                // Blok içeriğini doğrudan aktarma
                                const blockContent = block.get('content');
                                let contentToAdd;
                                
                                if (isWidget && typeof blockContent === 'object') {
                                    // Özel widget içeriğini doğru şekilde ekle
                                    contentToAdd = blockContent.html || blockContent;
                                    
                                    // Widget bilgisini data attribute olarak kaydet
                                    if (blockContent.widget_id || isActiveWidget) {
                                        const widgetId = blockContent.widget_id || block.id.replace('tenant-widget-', '');
                                        const tempDiv = document.createElement('div');
                                        tempDiv.innerHTML = typeof contentToAdd === 'string' ? contentToAdd : '';
                                        const wrapperEl = tempDiv.querySelector('.gjs-widget-wrapper') || tempDiv;
                                        
                                        if (wrapperEl) {
                                            const widgetAttr = isActiveWidget ? 'data-tenant-widget-id' : 'data-widget-id';
                                            wrapperEl.setAttribute(widgetAttr, widgetId);
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
                                
                                // Özel widget kontrolü - aktif widget'lar için HTML olarak widget embed kullanımı
                                if (isActiveWidget) {
                                    const widgetId = block.id.replace('tenant-widget-', '');
                                    contentToAdd = `<div class="widget-embed" data-type="widget-embed" data-tenant-widget-id="${widgetId}" id="widget-embed-${widgetId}">
                                        <div class="widget-content-placeholder" id="widget-content-${widgetId}">
                                            <div class="widget-loading" style="text-align:center; padding:20px;">
                                                <i class="fa fa-spin fa-spinner"></i> Widget içeriği yükleniyor...
                                            </div>
                                        </div>
                                    </div>`;
                                    
                                    // Canvas hazırlama eventi 
                                    window._needsWidgetRefresh = true;
                                    window._lastDroppedWidgetId = widgetId;
                                }
                                
                                // DataTransfer nesnesine içeriği aktar
                                e.dataTransfer.setData('text/html', contentToAdd);
                                e.dataTransfer.setData('text/plain', block.id);
                                blockEl.classList.add('dragging');
                                
                                // Widget için ekstra bilgileri sakla
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
                                        id: blockContent.widget_id || block.id.replace('tenant-widget-', ''),
                                        html: blockContent.html,
                                        css: blockContent.css,
                                        js: blockContent.js,
                                        isActiveWidget: isActiveWidget
                                    };
                                }
                            });
                            
                            blockEl.addEventListener('dragend', () => {
                                blockEl.classList.remove('dragging');
                                
                                // Geçici widget stil elementlerini temizle
                                const widgetStyles = document.querySelectorAll('style[data-widget-css="true"]');
                                widgetStyles.forEach(el => el.remove());
                                
                                // İçeriği iframe'e yerleştirildiyse widget içeriğini yükle
                                setTimeout(() => {
                                    if (window._needsWidgetRefresh && window._lastDroppedWidgetId) {
                                        if (window.studioLoadWidget) {
                                            window.studioLoadWidget(window._lastDroppedWidgetId);
                                        }
                                        window._needsWidgetRefresh = false;
                                        window._lastDroppedWidgetId = null;
                                    }
                                    
                                    // Widget bilgilerini temizle
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
                                if (window.StudioNotification && window.StudioNotification.info) {
                                    window.StudioNotification.info('Bileşeni eklemek için sürükleyip bırakın');
                                }
                                e.preventDefault();
                                e.stopPropagation();
                            });
                        }
                        
                        blockItems.appendChild(blockEl);
                    });
                }
            });
            
            // İşlem tamamlandı
            blocksUpdatedInCategories = true;
            console.log("Bloklar başarıyla kategorilere eklendi");
            
        } catch (error) {
            console.error("Blok güncelleme hatası:", error);
        } finally {
            // İşlem durumunu sıfırla
            isUpdating = false;
        }
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
    
    // Stil: Pasif blok öğeleri soluk ve tıklanamaz yap
    (function() {
        const styleId = 'block-disabled-style';
        if (!document.getElementById(styleId)) {
            const styleEl = document.createElement('style');
            styleEl.id = styleId;
            styleEl.textContent = `
                .block-item.disabled {
                    opacity: 0.5;
                    pointer-events: none;
                    cursor: not-allowed;
                }
            `;
            document.head.appendChild(styleEl);
        }
    })();
    
    return {
        updateBlocksInCategories: updateBlocksInCategories,
        setupBlockSearch: setupBlockSearch,
        filterBlocks: filterBlocks
    };
})();