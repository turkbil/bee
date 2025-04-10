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
        
        categories.forEach(category => {
            // Tıklama olayını ekle
            category.addEventListener('click', function() {
                const parent = this.closest('.block-category');
                parent.classList.toggle('collapsed');
                
                // İçeriği göster/gizle
                const content = parent.querySelector('.block-items');
                if (content) {
                    if (parent.classList.contains('collapsed')) {
                        content.style.display = 'none';
                    } else {
                        content.style.display = 'grid';
                    }
                }
            });
            
            // İlk başta tüm kategoriler açık olsun
            const parent = category.closest('.block-category');
            if (parent) {
                parent.classList.remove('collapsed');
                const content = parent.querySelector('.block-items');
                if (content) {
                    content.style.display = 'grid';
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
        // Stil yöneticisi akordiyon davranışı
        editor.on('load', () => {
            // Sector başlıklarına tıklama olay dinleyicileri ekleme
            setTimeout(() => {
                const sectorTitles = document.querySelectorAll('.gjs-sm-sector-title');
                sectorTitles.forEach(title => {
                    // Event listener zaten eklenmemişse ekle
                    if (!title.hasClickListener) {
                        title.hasClickListener = true;
                        title.addEventListener('click', function() {
                            const sector = this.closest('.gjs-sm-sector');
                            sector.classList.toggle('gjs-collapsed');
                            
                            // İçeriği göster/gizle
                            const properties = sector.querySelector('.gjs-sm-properties');
                            if (properties) {
                                properties.style.display = sector.classList.contains('gjs-collapsed') ? 'none' : 'block';
                            }
                        });
                    }
                });
                
                // BlockManager akordiyon davranışını düzenleme
                const blockCategoryTitles = document.querySelectorAll('.gjs-block-category .gjs-title');
                blockCategoryTitles.forEach(title => {
                    // Event listener zaten eklenmemişse ekle
                    if (!title.hasClickListener) {
                        title.hasClickListener = true;
                        title.addEventListener('click', function() {
                            const category = this.closest('.gjs-block-category');
                            category.classList.toggle('gjs-open');
                            
                            // İçeriği göster/gizle
                            const blocks = category.querySelector('.gjs-blocks-c');
                            if (blocks) {
                                blocks.style.display = category.classList.contains('gjs-open') ? 'grid' : 'none';
                            }
                        });
                    }
                    
                    // İlk yüklemede açık olarak ayarla
                    const category = title.closest('.gjs-block-category');
                    if (category) {
                        category.classList.add('gjs-open');
                        const blocks = category.querySelector('.gjs-blocks-c');
                        if (blocks) {
                            blocks.style.display = 'grid';
                        }
                    }
                });
            }, 500);
        });
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
        // GrapesJS'in kendi sürükle-bırak mekanizmasını geliştir
        editor.on('block:drag:start', function(blockModel) {
            // Sürükleme başladığında yapılacak işlemler
            console.log('Blok sürükleniyor:', blockModel.get('label'));
        });

        // Özel blok öğeleri için sürükle-bırak davranışını iyileştir
        const blockItems = document.querySelectorAll('.block-item');
        blockItems.forEach(item => {
            item.addEventListener('click', function() {
                const blockId = this.getAttribute('data-block-id');
                console.log('Blok tıklandı:', blockId);
                
                // GrapesJS bloğunu bul
                const block = editor.BlockManager.get(blockId);
                if (block) {
                    // Bloğu ekle
                    editor.addComponents(block.get('content'));
                    
                    // Bildirim göster
                    StudioUtils.showNotification(
                        "Başarılı", 
                        "Bileşen eklendi", 
                        "success"
                    );
                } else {
                    console.warn('Blok bulunamadı:', blockId);
                }
            });
            
            // Sürükleme event handler'ını sil ve yeniden tanımla
            item.removeEventListener('dragstart', item._dragStartHandler);
            
            // Yeni dragstart handler
            item._dragStartHandler = function(e) {
                const blockId = this.getAttribute('data-block-id');
                
                // Drag sırasında taşınacak veriyi ayarla
                e.dataTransfer.setData('text/plain', blockId);
                e.dataTransfer.effectAllowed = 'copy';
                
                // Sürükleme görseli için boş bir element oluştur
                const ghost = document.createElement('div');
                ghost.style.position = 'absolute';
                ghost.style.top = '-1000px';
                ghost.style.opacity = '0';
                ghost.innerHTML = this.innerHTML;
                document.body.appendChild(ghost);
                
                try {
                    e.dataTransfer.setDragImage(ghost, 0, 0);
                } catch (err) {
                    console.warn('Drag image ayarlanamadı:', err);
                }
                
                // CSS class ekle
                this.classList.add('dragging');
                
                // Clean up ghost element
                setTimeout(() => {
                    document.body.removeChild(ghost);
                }, 0);
            };
            
            item.addEventListener('dragstart', item._dragStartHandler);
            
            // Sürükleme bittiğinde stili geri al
            item.addEventListener('dragend', function() {
                this.classList.remove('dragging');
            });
        });
        
        // Canvas'ı drop target yap
        const canvas = document.querySelector('#gjs');
        if (canvas) {
            // Eski event listener'ları kaldır
            canvas.removeEventListener('dragover', canvas._dragOverHandler);
            canvas.removeEventListener('drop', canvas._dropHandler);
            
            // Drop bölgesi olarak ayarla
            canvas._dragOverHandler = function(e) {
                e.preventDefault();
                e.stopPropagation();
                e.dataTransfer.dropEffect = 'copy';
                this.classList.add('drop-target');
            };
            
            canvas._dropHandler = function(e) {
                e.preventDefault();
                e.stopPropagation();
                this.classList.remove('drop-target');
                
                const blockId = e.dataTransfer.getData('text/plain');
                console.log('Drop detected with block ID:', blockId);
                
                if (blockId) {
                    // GrapesJS bloğunu bul
                    const block = editor.BlockManager.get(blockId);
                    if (block) {
                        // Bloğu ekle
                        editor.addComponents(block.get('content'));
                        
                        // Bildirim göster
                        StudioUtils.showNotification(
                            "Başarılı", 
                            "Bileşen eklendi", 
                            "success"
                        );
                    } else {
                        console.warn('Blok bulunamadı:', blockId);
                    }
                }
            };
            
            canvas.addEventListener('dragover', canvas._dragOverHandler);
            canvas.addEventListener('drop', canvas._dropHandler);
        }
        
        // Özel sol panel öğelerini GrapesJS blokları ile eşleştir
        function syncCustomBlocks() {
            console.log('Bloklar eşleştiriliyor...');
            const blockItems = document.querySelectorAll('.block-item');
            
            blockItems.forEach(item => {
                const blockId = item.getAttribute('data-block-id');
                const block = editor.BlockManager.get(blockId);
                
                if (block) {
                    // Bloğun label'ını güncelle
                    const label = item.querySelector('.block-item-label');
                    if (label) {
                        label.textContent = block.get('label');
                    }
                    
                    // İkon sınıfını güncelle (varsa)
                    const iconAttributes = block.get('attributes');
                    if (iconAttributes && iconAttributes.class) {
                        const icon = item.querySelector('.block-item-icon i');
                        if (icon) {
                            icon.className = iconAttributes.class;
                        }
                    }
                }
            });
        }
        
        // Editor yüklendikten sonra blokları eşleştir
        editor.on('load', syncCustomBlocks);
    }
    
    return {
        setupUI: setupUI
    };
})();