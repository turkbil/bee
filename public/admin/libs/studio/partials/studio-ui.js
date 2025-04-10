/**
 * Studio Editor - UI Modülü
 * Wix/Canvas Builder benzeri modern arayüz
 */
window.StudioUI = (function() {
    /**
     * Arayüz olaylarını kaydeder
     * @param {Object} editor - GrapesJS editor örneği
     */
    function setupUI(editor) {
        editorInstance = editor; // editorInstance'ı ayarla
        console.log('Setting up Studio UI with editor instance:', editorInstance ? 'Yes' : 'No');
        initializeBlockCategories();
        setupSearch();
        setupTabs();
        setupToolbar(editor);
        setupDeviceToggle(editor);
        setupGrapesJSCustomizations(editor);
        fixDuplicateStyles();
        setupDragAndDrop(editor);
    }
    
    /**
     * Blok kategorilerini başlat
     */
    function initializeBlockCategories() {
        const categories = document.querySelectorAll('.block-category-header');
        console.log('Initializing block categories. Found headers:', categories.length);

        categories.forEach(category => {
            // Mevcut listener'ı kaldır (varsa)
            // category.removeEventListener('click', handleCategoryClick); // İhtiyaç olursa bu satırı aktif et
            
            const handleCategoryClick = function() { // Fonksiyonu isimlendir
                console.log('Category header clicked:', this.textContent.trim()); // Log eklendi
                const parent = this.closest('.block-category');
                if (!parent) {
                    console.error('Could not find parent .block-category for:', this);
                    return;
                }
                parent.classList.toggle('collapsed');
                console.log('Toggled collapsed class. Parent classList:', parent.classList); // Log eklendi

                const content = parent.querySelector('.block-items'); // .block-items-container yerine .block-items
                if (content) {
                    if (parent.classList.contains('collapsed')) {
                        content.style.display = 'none';
                        console.log('Set display to none for content area.'); // Log eklendi
                    } else {
                        content.style.display = 'grid'; // veya 'block'/'flex', içeriğe göre ayarla
                        console.log('Set display to grid for content area.'); // Log eklendi
                    }
                } else {
                    console.warn('Could not find .block-items within:', parent);
                }
                
                // Doğrudan stil ayarı (CSS çakışmasını önlemek için)
                const contentArea = parent.querySelector('.block-items');
                if (contentArea) {
                    if (parent.classList.contains('collapsed')) {
                        contentArea.style.display = 'none';
                        console.log('Set display to none for content area.');
                    } else {
                        contentArea.style.display = 'grid'; // Veya 'block', orijinali neyse
                        console.log('Set display to grid for content area.');
                    }
                }
            };

            // Tıklama olayını ekle
            category.addEventListener('click', handleCategoryClick);

            // İlk başta tüm kategoriler açık olsun (Bu kısım tekrar gözden geçirilebilir)
            const parent = category.closest('.block-category');
            if (parent) {
                parent.classList.remove('collapsed');
                const content = parent.querySelector('.block-items'); // .block-items-container yerine .block-items
                if (content) {
                    content.style.display = 'grid'; // veya 'block'/'flex'
                }
            }
        });
    }
    
    /**
     * Arama kutusunu yapılandırır
     */
    function setupSearch() {
        const searchInput = document.getElementById("blocks-search");
        if (searchInput) {
            searchInput.addEventListener("input", function () {
                const query = this.value.toLowerCase();
                const blocks = document.querySelectorAll(".block-item");

                blocks.forEach((block) => {
                    const label = block.querySelector(".block-item-label");
                    if (label) {
                        const text = label.textContent.toLowerCase();
                        if (text.includes(query)) {
                            block.style.display = "";
                            
                            // Ebeveyn kategoriyi göster
                            const category = block.closest('.block-category');
                            if (category) {
                                category.style.display = '';
                                category.classList.remove('collapsed');
                                const blockItems = category.querySelector('.block-items');
                                if (blockItems) {
                                    blockItems.style.display = 'grid';
                                }
                            }
                        } else {
                            block.style.display = "none";
                        }
                    }
                });
                
                // Boş kategorileri gizle
                const categories = document.querySelectorAll('.block-category');
                categories.forEach(category => {
                    const blockItems = category.querySelectorAll('.block-item');
                    const visibleItems = Array.from(blockItems).filter(item => 
                        item.style.display !== 'none'
                    ).length;
                    
                    if (visibleItems === 0) {
                        category.style.display = 'none';
                    } else {
                        category.style.display = '';
                        category.classList.remove('collapsed');
                        const blockItemsContainer = category.querySelector('.block-items');
                        if (blockItemsContainer) {
                            blockItemsContainer.style.display = 'grid';
                        }
                    }
                });
                
                // Arama kutusu boşsa tüm kategorileri göster
                if (query === '') {
                    const categories = document.querySelectorAll('.block-category');
                    categories.forEach(category => {
                        category.style.display = '';
                    });
                }
            });
        }
    }
    
    /**
     * Tab panellerini yapılandırır
     */
    function setupTabs() {
        const tabs = document.querySelectorAll(".panel-tab");
        const tabContents = document.querySelectorAll(".panel-tab-content");

        tabs.forEach((tab) => {
            tab.addEventListener("click", function () {
                const tabName = this.getAttribute("data-tab");

                // Aktif tab değiştir
                tabs.forEach((t) => t.classList.remove("active"));
                this.classList.add("active");

                // İçeriği değiştir
                tabContents.forEach((content) => {
                    if (content.getAttribute("data-tab-content") === tabName) {
                        content.classList.add("active");
                    } else {
                        content.classList.remove("active");
                    }
                });
            });
        });
    }
    
    /**
     * Toolbar butonlarını yapılandırır
     * @param {Object} editor - GrapesJS editor örneği
     */
    function setupToolbar(editor) {
        // Bileşen görünürlük butonu
        const swVisibility = document.getElementById("sw-visibility");
        if (swVisibility) {
            swVisibility.addEventListener("click", () => {
                editor.runCommand("sw-visibility");
                swVisibility.classList.toggle("active");
            });
        }

        // İçerik temizle butonu
        const cmdClear = document.getElementById("cmd-clear");
        if (cmdClear) {
            cmdClear.addEventListener("click", () => {
                if (
                    confirm(
                        "İçeriği temizlemek istediğinize emin misiniz? Bu işlem geri alınamaz."
                    )
                ) {
                    editor.runCommand("core:canvas-clear");
                }
            });
        }

        // Geri Al butonu
        const cmdUndo = document.getElementById("cmd-undo");
        if (cmdUndo) {
            cmdUndo.addEventListener("click", () => {
                editor.runCommand("core:undo");
            });
        }

        // Yinele butonu
        const cmdRedo = document.getElementById("cmd-redo");
        if (cmdRedo) {
            cmdRedo.addEventListener("click", () => {
                editor.runCommand("core:redo");
            });
        }

        // Kod düzenleme butonları
        setupCodeEditors(editor);
    }
    
    /**
     * Kod düzenleme modallarını yapılandırır
     * @param {Object} editor - GrapesJS editor örneği
     */
    function setupCodeEditors(editor) {
        // HTML kodu düzenleme
        const cmdCodeEdit = document.getElementById("cmd-code-edit");
        if (cmdCodeEdit) {
            cmdCodeEdit.addEventListener("click", () => {
                const htmlContent = editor.getHtml();
                StudioUtils.showEditModal("HTML Düzenle", htmlContent, (newHtml) => {
                    editor.setComponents(newHtml);
                });
            });
        }

        // CSS kodu düzenleme
        const cmdCssEdit = document.getElementById("cmd-css-edit");
        if (cmdCssEdit) {
            cmdCssEdit.addEventListener("click", () => {
                const cssContent = editor.getCss();
                const cssContentEl = document.getElementById("css-content");
                if (cssContentEl) {
                    cssContentEl.value = cssContent;
                }
                StudioUtils.showEditModal("CSS Düzenle", cssContent, (newCss) => {
                    if (cssContentEl) {
                        cssContentEl.value = newCss;
                    }
                    editor.setStyle(newCss);
                });
            });
        }

        // JS kodu düzenleme
        const cmdJsEdit = document.getElementById("cmd-js-edit");
        if (cmdJsEdit) {
            cmdJsEdit.addEventListener("click", () => {
                const jsContentEl = document.getElementById("js-content");
                const jsContent = jsContentEl ? jsContentEl.value : "";
                StudioUtils.showEditModal("JavaScript Düzenle", jsContent, (newJs) => {
                    if (jsContentEl) {
                        jsContentEl.value = newJs;
                    }
                });
            });
        }
    }
    
    /**
     * Cihaz görünümü değiştirme butonlarını yapılandırır
     * @param {Object} editor - GrapesJS editor örneği
     */
    function setupDeviceToggle(editor) {
        const deviceDesktop = document.getElementById("device-desktop");
        const deviceTablet = document.getElementById("device-tablet");
        const deviceMobile = document.getElementById("device-mobile");

        function toggleDeviceButtons(activeBtn) {
            const deviceBtns = document.querySelectorAll(
                ".device-btns .toolbar-btn"
            );
            if (deviceBtns) {
                deviceBtns.forEach((btn) => {
                    btn.classList.remove("active");
                });
                if (activeBtn) {
                    activeBtn.classList.add("active");
                }
            }
        }

        if (deviceDesktop) {
            deviceDesktop.addEventListener("click", function () {
                editor.setDevice("Desktop");
                toggleDeviceButtons(this);
            });
        }

        if (deviceTablet) {
            deviceTablet.addEventListener("click", function () {
                editor.setDevice("Tablet");
                toggleDeviceButtons(this);
            });
        }

        if (deviceMobile) {
            deviceMobile.addEventListener("click", function () {
                editor.setDevice("Mobile");
                toggleDeviceButtons(this);
            });
        }
    }
    
    /**
     * GrapesJS özelleştirmeleri
     * @param {Object} editor - GrapesJS editor örneği
     */
    function setupGrapesJSCustomizations(editor) {
        // Stil yöneticisi entegrasyonu ve açılır-kapanır menüler
        editor.on('load', () => {
            // Stil yöneticisi için özel işleyiş
            setTimeout(() => {
                try {
                    // Sector başlıklarına tıklama olay dinleyicileri ekleme
                    const sectorTitles = document.querySelectorAll('.gjs-sm-sector-title');
                    sectorTitles.forEach((title, index) => {
                        // Mevcut olay dinleyicileri kaldır ve yeniden ekle
                        const newTitle = title.cloneNode(true);
                        if (title.parentNode) {
                            title.parentNode.replaceChild(newTitle, title);
                        }
                        
                        // Yeni olay dinleyicisi ekle
                        newTitle.addEventListener('click', function(e) {
                            e.preventDefault();
                            const sector = this.closest('.gjs-sm-sector');
                            if (sector) {
                                sector.classList.toggle('gjs-collapsed');
                                
                                // İçeriği göster/gizle
                                const properties = sector.querySelector('.gjs-sm-properties');
                                if (properties) {
                                    properties.style.display = sector.classList.contains('gjs-collapsed') ? 'none' : 'block';
                                }
                            }
                        });
                        
                        // İlk sektörü açık, diğerlerini kapalı yap
                        const sector = newTitle.closest('.gjs-sm-sector');
                        if (sector) {
                            if (index === 0) {
                                sector.classList.remove('gjs-collapsed');
                                const properties = sector.querySelector('.gjs-sm-properties');
                                if (properties) {
                                    properties.style.display = 'block';
                                }
                            } else {
                                sector.classList.add('gjs-collapsed');
                                const properties = sector.querySelector('.gjs-sm-properties');
                                if (properties) {
                                    properties.style.display = 'none';
                                }
                            }
                        }
                    });
                    
                    // Stil panelini özelleştir
                    document.querySelectorAll('.gjs-sm-sector').forEach(sector => {
                        sector.classList.add('custom-style-sector');
                        const title = sector.querySelector('.gjs-sm-sector-title');
                        if (title) {
                            title.classList.add('custom-sector-title');
                            
                            // Sektör başlığını daha belirgin yap
                            title.style.cursor = 'pointer';
                            title.style.userSelect = 'none';
                        }
                    });
                    
                    // Katmanlar panelini özelleştir
                    document.querySelectorAll('.gjs-layer').forEach(layer => {
                        layer.classList.add('custom-layer');
                    });
                    
                    // BlockManager akordiyon davranışını düzenleme
                    const blockCategoryTitles = document.querySelectorAll('.gjs-block-category .gjs-title');
                    blockCategoryTitles.forEach(title => {
                        // Event listener zaten eklenmemişse ekle
                        const newTitle = title.cloneNode(true);
                        if (title.parentNode) {
                            title.parentNode.replaceChild(newTitle, title);
                        }
                        
                        newTitle.addEventListener('click', function() {
                            const category = this.closest('.gjs-block-category');
                            if (category) {
                                category.classList.toggle('gjs-open');
                                
                                // İçeriği göster/gizle
                                const blocks = category.querySelector('.gjs-blocks-c');
                                if (blocks) {
                                    blocks.style.display = category.classList.contains('gjs-open') ? 'grid' : 'none';
                                }
                            }
                        });
                        
                        // İlk yüklemede açık olarak ayarla
                        const category = newTitle.closest('.gjs-block-category');
                        if (category) {
                            category.classList.add('gjs-open');
                            const blocks = category.querySelector('.gjs-blocks-c');
                            if (blocks) {
                                blocks.style.display = 'grid';
                            }
                        }
                    });
                    
                    // Sağ panel görünümünü iyileştir
                    const rightPanel = document.querySelector('.panel__right');
                    if (rightPanel) {
                        rightPanel.classList.add('custom-right-panel');
                    }
                    
                    // Özellik panelini güçlendir
                    enhancePropertyFields();
                } catch (error) {
                    console.error('GrapesJS özelleştirmeleri ayarlanırken hata:', error);
                }
            }, 800); // Daha fazla bekleme süresi
        });
        
        // Stil özelliklerini geliştir
        function enhancePropertyFields() {
            // Renk seçicileri için iyileştirmeler
            document.querySelectorAll('.gjs-field-color').forEach(colorField => {
                colorField.classList.add('custom-color-field');
                // Renk seçiciye tıklandığında özel bir sınıf ekle
                colorField.addEventListener('click', function() {
                    this.classList.add('active-color-picker');
                });
            });
            
            // Select elemanlarını güçlendir
            document.querySelectorAll('.gjs-field-select').forEach(selectField => {
                selectField.classList.add('custom-select-field');
            });
            
            // Giriş alanlarını güçlendir
            document.querySelectorAll('.gjs-field-integer, .gjs-field-number').forEach(inputField => {
                inputField.classList.add('custom-input-field');
            });
        }
    }
    
    /**
     * Duplike stil kategorilerini düzeltir
     */
    function fixDuplicateStyles() {
        // Editor yüklendikten sonra çalıştır
        setTimeout(() => {
            // Sağ paneldeki duplike kategorileri temizle
            const styleContainer = document.getElementById('styles-container');
            if (styleContainer) {
                // Tüm kategorileri al
                const categories = styleContainer.querySelectorAll('.gjs-sm-sector');
                const processedTitles = new Set();
                
                categories.forEach(category => {
                    const titleEl = category.querySelector('.gjs-sm-sector-title');
                    if (titleEl) {
                        const title = titleEl.textContent.trim();
                        
                        // Eğer bu başlık daha önce işlendiyse, kategoriyi gizle
                        if (processedTitles.has(title)) {
                            category.style.display = 'none';
                        } else {
                            processedTitles.add(title);
                        }
                    }
                });
            }
        }, 1000);
    }
    
    /**
     * Sürükle ve bırak işlevselliğini yapılandırır
     * @param {Object} editor - GrapesJS editor örneği
     */
    function setupDragAndDrop(editor) {
        // Özel blok öğeleri için gelişmiş sürükle-bırak işlevi
        setupCustomDragDrop(editor);
        
        // Block Manager için özelleştirmeler
        setupBlockManagerDragDrop(editor);
        
        // Canvas içi sürükle bırak olaylarını iyileştir
        enhanceCanvasDragDrop(editor);
    }
    
    /**
     * Özel blok öğeleri için gelişmiş sürükle-bırak
     * @param {Object} editor - GrapesJS editor örneği
     */
    function setupCustomDragDrop(editor) {
        // Tüm özel blok öğelerine event listener ekle
        document.querySelectorAll('.block-item').forEach(blockItem => {
            // Her öğeye benzersiz bir ID ekle
            const uniqueId = 'block-' + Math.random().toString(36).substr(2, 9);
            blockItem.id = uniqueId;
            
            // Mevcut listener'ları temizle (güvenlik önlemi)
            blockItem.removeEventListener('dragstart', handleDragStart);
            blockItem.removeEventListener('dragend', handleDragEnd);
            // blockItem.removeEventListener('click', handleBlockClick); // Tıklamayı engellemek için KESİN kaldır.

            // Sadece drag event listener'larını ekle
            blockItem.addEventListener('dragstart', handleDragStart);
            blockItem.addEventListener('dragend', handleDragEnd);
            
            // Tıklamayı kesin olarak engellemek için ek listener
            blockItem.addEventListener('click', function(e) {
                console.log('Block item click intercepted and prevented.');
                e.preventDefault();
                e.stopPropagation();
                return false;
            }, true); // Use capture phase to intercept early

            // Tıklama olayını ekleme (bu satırlar yorumlu kalmalı)
            // blockItem.addEventListener('click', handleBlockClick);
        });
        
        // Drag başlatma olayı
        function handleDragStart(e) {
            // Block ID'sini al
            const blockId = this.getAttribute('data-block-id');
            if (!blockId) return;
            
            // Data transfer içeriğini ayarla
            // e.dataTransfer.setData('text/plain', blockId); // GrapesJS'in varsayılanını tetiklememesi için kaldırıldı
            window._draggedBlockId = blockId; // ID'yi global değişkende sakla
            e.dataTransfer.effectAllowed = 'copy';
            console.log(`Drag started for block: ${blockId}`);
            
            // Drag elementi için bir klon oluştur
            const clone = this.cloneNode(true);
            clone.style.position = 'absolute';
            clone.style.left = '-1000px';
            clone.style.top = '-1000px';
            clone.style.opacity = '0.8';
            clone.style.pointerEvents = 'none';
            clone.id = 'drag-ghost';
            document.body.appendChild(clone);
            
            try {
                // Drag resmi olarak klonu ayarla (destekleniyorsa)
                e.dataTransfer.setDragImage(clone, 20, 20);
            } catch (err) {
                console.warn('Drag resmi ayarlanamadı:', err);
            }
            
            // Sürükleme başladığında CSS sınıfı ekle
            this.classList.add('dragging');
            
            // Sürükleme olayını takip et
            editor.trigger('custom:block:drag:start', blockId);
        }
        
        // Drag bitirme olayı
        function handleDragEnd(e) {
            // Sürükleme CSS sınıfını kaldır
            this.classList.remove('dragging');
            
            // Drag ghost'u temizle
            const dragGhost = document.getElementById('drag-ghost');
            if (dragGhost) {
                document.body.removeChild(dragGhost);
            }
            
            // Editor'e olayı bildir
            editor.trigger('custom:block:drag:end');
        }
        
        // Blok tıklama olayı
        function handleBlockClick(e) {
            const blockId = this.getAttribute('data-block-id');
            if (!blockId) return;
            
            // GrapesJS bloğunu bul
            const block = editor.BlockManager.get(blockId);
            if (block) {
                // Bloğu canvas'a ekle
                editor.addComponents(block.get('content'));
                
                // Bildirim göster
                StudioUtils.showNotification(
                    "Başarılı", 
                    "Bileşen eklendi", 
                    "success"
                );
            }
        }
    }
    
    /**
     * Block Manager için özelleştirmeler
     * @param {Object} editor - GrapesJS editor örneği
     */
    function setupBlockManagerDragDrop(editor) {
        // Editor yüklendikten sonra GrapesJS'in kendi bloklarını özelleştir
        editor.on('load', () => {
            setTimeout(() => {
                // GrapesJS blok öğelerini güçlendir
                document.querySelectorAll('.gjs-block').forEach(block => {
                    block.classList.add('custom-gjs-block');
                    
                    // Hover efektlerini güçlendir
                    block.addEventListener('mouseenter', function() {
                        this.classList.add('custom-gjs-block-hover');
                    });
                    
                    block.addEventListener('mouseleave', function() {
                        this.classList.remove('custom-gjs-block-hover');
                    });
                    
                    // Sürükleme başladığında stili güçlendir
                    block.addEventListener('dragstart', function() {
                        this.classList.add('custom-gjs-block-dragging');
                    });
                    
                    block.addEventListener('dragend', function() {
                        this.classList.remove('custom-gjs-block-dragging');
                    });
                });
                
                // Blok kategorilerini güçlendir
                document.querySelectorAll('.gjs-block-category').forEach(category => {
                    category.classList.add('custom-gjs-category');
                });
            }, 800);
        });
    }
    
    /**
     * Canvas içi sürükle bırak olaylarını iyileştir
     * @param {Object} editor - GrapesJS editor örneği
     */
    function enhanceCanvasDragDrop(editor) {
        // Canvas içerisindeki bileşenleri sürükleme olaylarını geliştir
        editor.on('component:selected', (component) => {
            const selectedEl = component.getEl();
            if (selectedEl) {
                selectedEl.classList.add('custom-selected-component');
            }
        });
        
        editor.on('component:deselected', (component) => {
            const selectedEl = component.getEl();
            if (selectedEl) {
                selectedEl.classList.remove('custom-selected-component');
            }
        });
        
        // Komponentlere hover olayı ekle
        editor.on('component:mount', (component) => {
            const el = component.getEl();
            if (el) {
                el.addEventListener('mouseenter', function() {
                    if (!component.get('selected')) {
                        this.classList.add('custom-component-hover');
                    }
                });
                
                el.addEventListener('mouseleave', function() {
                    this.classList.remove('custom-component-hover');
                });
            }
        });
    }
    
    // Canvas dragover olayı (Görsel geri bildirim için)
    function handleDragOver(e) {
        e.preventDefault(); // Drop olayının çalışması için gerekli
        e.dataTransfer.dropEffect = 'copy';
    }

    // Canvas dragenter olayı (Görsel geri bildirim için)
    function handleDragEnter(e) {
        e.preventDefault();
        // `this` iframe body'si olacak, ona class ekleyebiliriz
        if (this.classList) { // Bazen this window olabilir, kontrol et
             this.classList.add('drop-target');
        }
        // console.log('Drag entered canvas iframe body');
    }

    // Canvas dragleave olayı (Görsel geri bildirim için)
    function handleDragLeave(e) {
        // Sadece iframe body'sinden çıkıldığında class'ı kaldır
        if (e.target === this && this.classList) {
             this.classList.remove('drop-target');
             // console.log('Drag left canvas iframe body');
        }
    }

    // Canvas drop olayı
    function handleCanvasDrop(e) {
        console.log('handleCanvasDrop triggered on iframe body!'); // Tetiklendi mi kontrol et
        e.preventDefault();
        e.stopPropagation();
        if (this.classList) { // `this` iframe body'si
            this.classList.remove('drop-target'); 
        }

        const blockId = window._draggedBlockId; // ID'yi global değişkenden al
        window._draggedBlockId = null; // Global değişkeni temizle

        console.log(`Drop detected on iframe. Attempting to add blockId from global var: ${blockId}`);

        if (!blockId) {
            console.warn('No blockId found after drop.');
            return;
        }

        // GrapesJS bloğunu bul
        // editorInstance'a erişim gerekiyor. Bunu setupUI'da ayarlayalım.
        if (!editorInstance) {
            console.error('Editor instance is not available in handleCanvasDrop');
            return;
        }
        const block = editorInstance.BlockManager.get(blockId);
        if (block) {
            console.log('Adding component to canvas. Block ID:', blockId);
            const content = block.get('content');
            console.log('--- Content being passed to addComponents: ---');
            console.log(content);
            console.log('----------------------------------------------');
            try {
                // HTML içeriğini temizle ve parse et
                const parser = new DOMParser();
                const doc = parser.parseFromString(content, 'text/html');
                
                // Body içeriğini al
                let cleanContent = '';
                if (doc.body) {
                    // Body'nin iç HTML'ini al ve ID'yi kaldır
                    doc.body.removeAttribute('id');
                    cleanContent = doc.body.innerHTML;
                } else {
                    // Eğer body yoksa, doğrudan içeriği kullan
                    cleanContent = content;
                }
                
                // Temizlenmiş içeriği ekle
                editorInstance.addComponents(cleanContent);

                // Bildirim göster
                StudioUtils.showNotification(
                    "Başarılı",
                    `'${block.get('label')}' eklendi`,
                    "success"
                );
            } catch (error) {
                console.error('Error adding component:', error);
                StudioUtils.showNotification(
                    "Hata",
                    "Bileşen eklenirken hata oluştu.",
                    "error"
                );
            }
        } else {
             console.error(`Block not found for ID: ${blockId}`);
        }
        
        return false; // Olayın daha fazla yayılmasını ve varsayılan davranışı engelle
    }

    let editorInstance = null;
    window._draggedBlockId = null; // Sürüklenen blok ID'sini saklamak için

    // Dışarıya aktarılacak fonksiyonlar
    return {
        setupUI: setupUI,
        handleDragOver: handleDragOver,
        handleDragEnter: handleDragEnter,
        handleDragLeave: handleDragLeave,
        handleCanvasDrop: handleCanvasDrop
    };
})();