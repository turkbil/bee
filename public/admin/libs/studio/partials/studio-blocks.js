/**
 * Studio Editor - Bloklar Modülü
 * Blade şablonlarından yüklenen bloklar
 */

window.StudioBlocks = (function() {
    // Global kilitleme için flag
    let blocksLoaded = false;
    let apiRequested = false;
    let categoriesCreated = false;
    
    /**
     * Blade şablonlarından blokları kaydet
     * @param {Object} editor - GrapesJS editor örneği
     */
    function registerBlocks(editor) {
        // Global kilitleme sistemi - eğer zaten istek yapıldıysa veya bloklar yüklendiyse işlem yapma
        if (blocksLoaded || apiRequested) {
            console.log("Bloklar zaten yüklenmiş veya API isteği yapılmış, işlem atlanıyor.");
            return;
        }
        
        // API isteği kilidi aktif
        apiRequested = true;
        
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
                    });
                    
                    console.log(`${data.blocks.length} adet blok başarıyla yüklendi`);
                    
                    // Blokları kategorilere ata - bir kez yapılıyor
                    if (!categoriesCreated) {
                        console.log("Blok kategorileri oluşturuluyor...");
                        createBlockCategories(editor, data.categories || {});
                        categoriesCreated = true;
                        
                        // Kategorilere blokları ekle ve işlemi tamamla
                        setTimeout(() => {
                            updateBlocksInCategories(editor);
                            // İşaretleyelim ki tekrar yüklenmesin
                            blocksLoaded = true;
                            
                            // Kategori durumlarını yükle
                            if (window._blockCategoryStatesLoaded !== true) {
                                window._blockCategoryStatesLoaded = true;
                                loadBlockCategoryStates();
                            }
                        }, 500);
                    }

                    // Arama işlevini bir kez ayarla
                    if (!window._searchSetupDone) {
                        setupBlockSearch(editor);
                        window._searchSetupDone = true;
                    }
                    
                    // Widget API'sini çağır ve widget bloklarını yükle
                    loadWidgetBlocks(editor);
                } else {
                    console.error("Blok yüklenemedi:", data.message || "Server yanıt vermedi");
                }
            })
            .catch(error => {
                console.error("Bloklar yüklenirken hata oluştu:", error);
                // Hata durumunda kilidi serbest bırak ki yeniden deneme yapılabilsin
                apiRequested = false;
            });
    }

    /**
     * Widget bloklarını yükle
     * @param {Object} editor - GrapesJS editor örneği
     */
    function loadWidgetBlocks(editor) {
        // Yeni widget manager modülünü kullan
        if (window.StudioWidgetManager && typeof window.StudioWidgetManager.loadWidgetBlocks === 'function') {
            window.StudioWidgetManager.loadWidgetBlocks(editor);
            return;
        }
        
        // Eski fonksiyon içeriği (uyumluluk için)
        fetch('/admin/studio/api/widgets')
            .then(response => response.json())
            .then(data => {
                if (data.widgets && Array.isArray(data.widgets)) {
                    console.log(`${data.widgets.length} adet widget bulundu`);
                    
                    data.widgets.forEach(widget => {
                        if (!widget.id || !widget.name) {
                            console.warn("Geçersiz widget verisi:", widget);
                            return;
                        }
                        
                        const blockId = `widget-${widget.id}`;
                        const widgetHtml = `<div data-widget-id="${widget.id}" class="gjs-widget-wrapper" data-type="widget">
                            ${widget.content_html || `<div class="widget-placeholder">Widget: ${widget.name}</div>`}
                        </div>`;
                        
                        const widgetCss = widget.content_css || '';
                        const widgetJs = widget.content_js || '';
                        
                        const category = widget.category || 'widget';
                        
                        editor.BlockManager.add(blockId, {
                            label: widget.name,
                            category: category,
                            attributes: { class: 'fa fa-puzzle-piece' },
                            content: {
                                type: 'widget',
                                widget_id: widget.id,
                                html: widgetHtml,
                                css: widgetCss,
                                js: widgetJs
                            }
                        });
                    });
                    
                    setTimeout(() => {
                        updateBlocksInCategories(editor);
                    }, 500);
                }
            })
            .catch(error => {
                console.error("Widget blokları yüklenirken hata:", error);
            });
    }
    

    
    /**
     * Widget komponentlerini tanımla
     * @param {Object} editor - GrapesJS editor örneği
     */
    function registerWidgetComponents(editor) {
        // Yeni widget manager modülünü kullan
        if (window.StudioWidgetManager && typeof window.StudioWidgetManager.registerWidgetComponents === 'function') {
            window.StudioWidgetManager.registerWidgetComponents(editor);
            return;
        }
        
        // Eski fonksiyon içeriği (uyumluluk için)
        const widgetType = 'widget';
        
        if (editor.Components.getType(widgetType)) {
            return;
        }
        
        editor.DomComponents.addType(widgetType, {
            model: {
                defaults: {
                    name: 'Widget',
                    tagName: 'div',
                    draggable: true,
                    droppable: false,
                    attributes: {
                        class: 'gjs-widget-wrapper'
                    },
                    traits: [
                        {
                            type: 'select',
                            name: 'widget_id',
                            label: 'Widget',
                            changeProp: 1,
                            options: []
                        }
                    ],
                    
                    init() {
                        this.on('change:widget_id', this.onWidgetIdChange);
                    },
                    
                    onWidgetIdChange() {
                        const widgetId = this.get('widget_id');
                        if (widgetId) {
                            // Widget içeriğini güncelle (gelecekte implement edilecek)
                        }
                    }
                }
            },
            
            view: {
                events: {
                    dblclick: 'onDblClick'
                },
                
                onDblClick() {
                    const model = this.model;
                    const widgetId = model.get('widget_id') || model.getAttributes()['data-widget-id'];
                    
                    if (widgetId) {
                        window.open(`/admin/widgetmanagement/items/${widgetId}`, '_blank');
                    }
                }
            }
        });
    }

    /**
     * Editördeki blokları kategori elementlerine ekler
     * @param {Object} editor - GrapesJS editor örneği
     */
    function updateBlocksInCategories(editor) {
        // Bu metod yalnızca bir kez çalışmalı
        if (window._blocksUpdatedInCategories) {
            return;
        }
        window._blocksUpdatedInCategories = true;
        
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
                        showToast('Bileşeni eklemek için sürükleyip bırakın', 'info');
                        e.preventDefault();
                        e.stopPropagation();
                    });
                    
                    blockItems.appendChild(blockEl);
                });
            }
        });
        
        // Widget bileşenlerini editöre doğru şekilde ekleyebilmek için
        // drop olayını dinle ve widget elementlerini doğru şekilde işle
        setupCanvasDropEvents(editor);
        
        console.log("Bloklar başarıyla kategorilere eklendi");
    }
    
    /**
     * Canvas drop olaylarını ayarla
     * @param {Object} editor - GrapesJS editor örneği
     */
    function setupCanvasDropEvents(editor) {
        editor.on('block:drag:stop', (component, block) => {
            // Yeni eklenen komponenti kontrol et
            if (!component) return;
            
            // Block widget mi kontrol et
            const blockId = block.get('id');
            if (!blockId || !blockId.startsWith('widget-')) return;
            
            // Widget ID'sini al
            const widgetId = block.get('content').widget_id;
            if (!widgetId) return;
            
            // Tüm yeni eklenen komponentleri dolaş ve widget sınıfı içerenleri bul
            const checkComponents = (innerComponent) => {
                if (!innerComponent) return;
                
                // Element widget wrapper ise tipini ayarla
                if (innerComponent.getClasses().includes('gjs-widget-wrapper') || 
                    innerComponent.getAttributes()['data-type'] === 'widget') {
                    
                    // Komponentin tipini widget olarak ayarla
                    innerComponent.set('type', 'widget');
                    innerComponent.set('widget_id', widgetId);
                    
                    // Data attribute ekle
                    innerComponent.addAttributes({
                        'data-widget-id': widgetId,
                        'data-type': 'widget'
                    });
                    
                    console.log(`Widget komponenti ayarlandı: ${widgetId}`);
                }
                
                // Alt komponentleri kontrol et
                if (innerComponent.get('components')) {
                    innerComponent.get('components').each(child => {
                        checkComponents(child);
                    });
                }
            };
            
            // Komponenti kontrol et
            checkComponents(component);
        });
    }
    
    /**
     * DOM'da blok kategorileri oluştur
     * @param {Object} editor - GrapesJS editor örneği
     * @param {Object} categories - Kategori bilgileri
     */
    function createBlockCategories(editor, categories) {
        // Bu metod yalnızca bir kez çalışmalı
        if (window._blockCategoriesCreated) {
            return;
        }
        window._blockCategoriesCreated = true;
        
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
                    <i class="${categoryIcon} text-primary"></i>
                    <span>${categoryName}</span>
                    <i class="fas fa-chevron-down toggle-icon"></i>
                </div>
                <div class="block-items"></div>
            `;
            
            blocksContainer.appendChild(categoryDiv);
            
            // Kategori başlığına tıklama olayı ekle - ancak sadece bir kez
            const header = categoryDiv.querySelector('.block-category-header');
            header.addEventListener('click', function(e) {
                // Zaten bu olaya yanıt veriliyorsa işleme devam etme
                if (e._categoryClickHandled) return;
                e._categoryClickHandled = true;
                
                const parent = this.closest('.block-category');
                if (!parent) return;

                parent.classList.toggle('collapsed');
                const itemsContainer = parent.querySelector('.block-items');
                if (itemsContainer) {
                    itemsContainer.style.display = parent.classList.contains('collapsed') ? 'none' : 'grid';
                }

                // Kategori durumlarını localStorage'a kaydet
                saveBlockCategoryStates();
            });
        });
    }
    
    /**
     * Kategori açılıp kapanma durumlarını localStorage'a kaydet
     */
    function saveBlockCategoryStates() {
        // Eğer bir zamanlayıcı zaten aktifse onu temizle ve yeni bir tane oluştur
        if (window._saveBlockCategoriesTimeout) {
            clearTimeout(window._saveBlockCategoriesTimeout);
        }
        
        window._saveBlockCategoriesTimeout = setTimeout(() => {
            const categories = document.querySelectorAll('.block-category');
            const states = {};
            
            categories.forEach(category => {
                const categoryId = category.getAttribute('data-category');
                if (categoryId) {
                    states[categoryId] = category.classList.contains('collapsed');
                }
            });
            
            localStorage.setItem('studio_block_categories', JSON.stringify(states));
        }, 300);
    }
    
    /**
     * Kategori açılıp kapanma durumlarını localStorage'dan yükle
     */
    function loadBlockCategoryStates() {
        // Bu metod yalnızca bir kez çalışmalı
        if (window._blockCategoryStatesLoaded) {
            return;
        }
        window._blockCategoryStatesLoaded = true;
        
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
            container.className = "toast-container position-fixed bottom-0 end-0 p-3";
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