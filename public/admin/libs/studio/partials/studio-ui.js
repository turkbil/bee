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
        setupDragAndDrop(editor);
        fixDuplicateStyles();
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
                if (parent.classList.contains('collapsed')) {
                    content.style.display = 'none';
                } else {
                    content.style.display = 'grid';
                }
            });
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
                                category.querySelector('.block-items').style.display = 'grid';
                            }
                        } else {
                            block.style.display = "none";
                        }
                    }
                });
                
                // Boş kategorileri gizle
                const categories = document.querySelectorAll('.block-category');
                categories.forEach(category => {
                    const visibleBlocks = category.querySelectorAll('.block-item[style*="display: none"]').length;
                    const totalBlocks = category.querySelectorAll('.block-item').length;
                    
                    if (visibleBlocks === totalBlocks) {
                        category.style.display = 'none';
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
     * Sürükle ve bırak işlevselliğini yapılandırır
     * @param {Object} editor - GrapesJS editor örneği
     */
    function setupDragAndDrop(editor) {
        const blockItems = document.querySelectorAll('.block-item');
        
        blockItems.forEach(item => {
            item.addEventListener('dragstart', function(e) {
                // Blok ID'sini sakla
                const blockId = this.getAttribute('data-block-id');
                e.dataTransfer.setData('blockId', blockId);
                
                // Sürükleme efekti
                this.classList.add('dragging');
            });
            
            item.addEventListener('dragend', function() {
                this.classList.remove('dragging');
            });
            
            // Tıklama ile de blokları ekleyebilme
            item.addEventListener('click', function() {
                const blockId = this.getAttribute('data-block-id');
                const block = editor.BlockManager.get(blockId);
                
                if (block) {
                    editor.addComponents(block.get('content'));
                    // Bildirim göster
                    StudioUtils.showNotification(
                        "Başarılı", 
                        "Bileşen eklendi.", 
                        "success"
                    );
                }
            });
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
    
    return {
        setupUI: setupUI
    };
})();