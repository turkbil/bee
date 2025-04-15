/**
 * Studio Editor - UI Modülü
 * Wix/Canvas Builder benzeri modern arayüz
 */
window.StudioUI = (function() {
    // Editor örneğini global olarak sakla
    let editorInstance = null;
    
    // Sürüklenen blok ID'sini global olarak sakla
    window._draggedBlockId = null;

    // Drop olayının işlenip işlenmediğini takip etmek için flag
    let dropProcessed = false;
    
    // UI'ın kurulup kurulmadığını takip etmek için global bayrak
    let isUISetup = false;
    
    /**
     * Arayüz olaylarını kaydeder
     * @param {Object} editor - GrapesJS editor örneği
     */
    function setupUI(editor) {
        // Eğer UI zaten kurulmuşsa, tekrar kurma
        if (isUISetup) {
            console.log('UI zaten kurulmuş, tekrar kurulum atlanıyor.');
            return;
        }
        
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
        enhanceLayersPanel(editor); // Katmanlar panelini geliştir
        
        // UI kurulumunu tamamlandı olarak işaretle
        isUISetup = true;
        console.log('UI kurulumu tamamlandı ve isUISetup=true yapıldı.');
    }
    
    /**
     * Blok kategorilerini başlat
     */
    function initializeBlockCategories() {
        const categories = document.querySelectorAll('.block-category-header');
        console.log('Initializing block categories. Found headers:', categories.length);

        categories.forEach(category => {
            // Mevcut listener'ı kaldır (varsa)
            const newCategory = category.cloneNode(true);
            if (category.parentNode) {
                category.parentNode.replaceChild(newCategory, category);
            }
            
            // Tıklama olayını ekle
            newCategory.addEventListener('click', function() {
                const parent = this.closest('.block-category');
                if (!parent) {
                    console.error('Could not find parent .block-category for:', this);
                    return;
                }
                parent.classList.toggle('collapsed');

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
            const parent = newCategory.closest('.block-category');
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
     * Katmanlar panelini geliştir
     * @param {Object} editor - GrapesJS editor örneği
     */
    function enhanceLayersPanel(editor) {
        // Function to observe changes in the layers panel using MutationObserver
        function observeLayers() {
            console.log('observeLayers function called');
            const layerManagerView = editor.Layers.getLayerView(); // Get Layer Manager View
            if (!layerManagerView || !layerManagerView.el) {
                console.error('Layer Manager View or element not found for observation');
                return;
            }
            const layerContainer = layerManagerView.el; // The main container for layers

            // Debounce timeout variable
            let debounceTimeout;

            const observer = new MutationObserver(mutations => {
                let structureChanged = false;
                // Check mutations to see if relevant layer elements were added/removed
                // This check can be simple or complex depending on needs
                for (const mutation of mutations) {
                    if (mutation.type === 'childList') {
                        // Check if added/removed nodes are layer elements or containers
                        // A simple check for any childList change is often sufficient
                        structureChanged = true;
                        break; // No need to check further mutations if one indicates change
                    }
                    // Optionally, observe attributes if classes/styles indicating structure change
                    // if (mutation.type === 'attributes' && (mutation.attributeName === 'class' || mutation.attributeName === 'style')) {
                    //    structureChanged = true;
                    //    break;
                    // }
                }

                if (structureChanged) {
                    console.log('Layer structure potentially changed, re-running setupLayerStructure (debounced)');
                    // Debounce the call to setupLayerStructure to avoid rapid firing
                    clearTimeout(debounceTimeout);
                    debounceTimeout = setTimeout(() => {
                        console.log('Executing debounced setupLayerStructure...');
                        setupLayerStructure(); 
                    }, 150); // Adjust delay as needed (e.g., 100-250ms)
                }
            });

            observer.observe(layerContainer, {
                childList: true, // Watch for addition/removal of child nodes (layers)
                subtree: true,   // Watch descendants as well
                // attributes: true, // Uncomment if needed, but childList is often enough
                // attributeFilter: ['class', 'style'] // Filter attributes if observing them
            });
            console.log('MutationObserver set up for layer container:', layerContainer);
        }

        // Initial setup when the editor is ready
        editor.on('load', () => {
            console.log('Editor loaded event triggered.');
            try {
                console.log('Running initial setupLayerStructure...');
                setupLayerStructure();
                console.log('Initial setupLayerStructure complete.');
                console.log('Setting up layer observer...');
                // observeLayers(); // Hata verdiği için geçici olarak kapatıldı
                console.log('Layer observer setup complete.');
            } catch (error) {
                console.error('Error during editor load setup:', error);
            }
        });
    }

    /**
     * Katman yapısını düzenle - GrapesJS API Kullanımı
     */
    function setupLayerStructure() {
        console.log('setupLayerStructure function called (GrapesJS API)');
        
        if (!editorInstance) {
            console.error('Editor instance not available');
            return;
        }
        
        // GrapesJS API'de Layers modülünü doğru şekilde kontrol et
        if (!editorInstance.Layers) {
            console.error('Layers module not found in editor instance');
            return;
        }
        
        try {
            // LayerManager API'sine erişmek için doğru metod
            const layerManager = editorInstance.LayerManager || editorInstance.Layers;
            
            // Component modeline önce eriş
            const components = editorInstance.Components || editorInstance.DomComponents;
            if (!components) {
                console.error('Components module not found');
                return;
            }
            
            // Kökteki bileşenleri al
            const rootComponent = components.getWrapper();
            if (!rootComponent) {
                console.error('Root component not found');
                return;
            }
            
            // İç içe bileşenleri güçlendir
            enhanceComponentLayers(rootComponent);
            
            console.log('Layer structure setup completed successfully');
        } catch (error) {
            console.error('Error while setting up layer structure:', error);
        }
    }

    /**
     * Bileşenleri ve alt bileşenlerini iyileştir
     * @param {Component} component - GrapesJS bileşen modeli
     */
    function enhanceComponentLayers(component) {
        if (!component) return;
        
        try {
            // Bileşenin görünümüne eriş
            const view = component.view;
            if (view && view.el) {
                const el = view.el;
                const layerEl = el.closest('.gjs-layer');
                
                if (layerEl) {
                    // Katman başlık elementi
                    const headerEl = layerEl.querySelector('.gjs-layer-title-c');
                    if (headerEl) {
                        // Alt bileşenleri kontrol et
                        const hasChildren = component.components && component.components().length > 0;
                        
                        if (hasChildren) {
                            // Ok simgesi ekle veya güncelle
                            let arrowIcon = headerEl.querySelector('.layer-arrow-i');
                            if (!arrowIcon) {
                                arrowIcon = document.createElement('i');
                                arrowIcon.className = 'layer-arrow-i fa fa-chevron-right';
                                headerEl.insertBefore(arrowIcon, headerEl.firstChild);
                            }
                            
                            // Katman içeriğini bul
                            const childrenEl = layerEl.querySelector('.gjs-layer-children');
                            
                            // Tıklama olayını ayarla
                            if (!headerEl.dataset.clickHandled) {
                                headerEl.addEventListener('click', (e) => {
                                    // Sadece başlığa veya ok simgesine tıklandığında tetikle
                                    if (e.target === headerEl || e.target === arrowIcon) {
                                        // Açık/kapalı durumunu değiştir
                                        if (childrenEl) {
                                            const isOpen = childrenEl.style.display !== 'none';
                                            childrenEl.style.display = isOpen ? 'none' : 'block';
                                            
                                            // Ok simgesini güncelle
                                            if (arrowIcon) {
                                                arrowIcon.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(90deg)';
                                            }
                                        }
                                    }
                                });
                                
                                headerEl.dataset.clickHandled = 'true';
                            }
                        }
                    }
                }
            }
            
            // Alt bileşenleri işle
            if (component.components) {
                const children = component.components();
                children.forEach(child => {
                    enhanceComponentLayers(child);
                });
            }
        } catch (error) {
            console.error('Error enhancing component layer:', error);
        }
    }

    /**
     * Belirli bir katmanı ve alt katmanlarını GrapesJS API'ları ile geliştirir.
     * @param {Layer} layer - GrapesJS Katman Modeli
     */
    function enhanceLayer(layer) {
        const component = layer.get('component'); // Get component from layer model
        if (!component) {
            console.error('Component not found for layer:', layer.getId ? layer.getId() : 'Unknown ID');
            return;
        }

        const layerView = layer.view; // Get the layer's view
        if (!layerView || !layerView.el) {
            console.warn('Layer view or element not found for component:', component.getId());
            return;
        }
        const layerEl = layerView.el; // Get the DOM element for the layer

        const headerEl = layerEl.querySelector('.gjs-layer-header'); // Find the header element within the layer's element
        if (!headerEl) {
            console.warn('Layer header element not found for component:', component.getId());
            return;
        }

        // console.log(`Processing layer for component: ${component.getId()}, Type: ${component.get('type')}`);

        // Use GrapesJS API to check for children components
        const hasChildren = component.components().length > 0;
        // console.log(`Component ${component.getId()} has children: ${hasChildren}`);

        // --- Icon and Event Listener Logic --- 
        let arrowIcon = headerEl.querySelector('.layer-arrow-i'); 
        const childrenEl = layerEl.querySelector('.gjs-layer-children'); // Find children container

        if (hasChildren) {
            if (!arrowIcon) {
                // console.log(`Adding arrow icon to component: ${component.getId()}`);
                arrowIcon = document.createElement('i');
                arrowIcon.className = 'layer-arrow-i';
                // Initial state set later
                headerEl.insertBefore(arrowIcon, headerEl.firstChild);

                // Ensure event listener is added only once per header
                if (!headerEl.dataset.clickListenerAdded) {
                    headerEl.addEventListener('click', (event) => {
                        // Only toggle if the click is directly on the header background or the arrow icon
                        if (event.target !== headerEl && event.target !== arrowIcon) {
                            // Allow click on component name/icons to trigger GrapesJS selection
                            return; 
                        }
                        event.stopPropagation(); // Prevent GrapesJS selection when toggling

                        // console.log(`Header/Arrow clicked for component: ${component.getId()}`);
                        const currentChildrenEl = layerEl.querySelector('.gjs-layer-children'); // Re-select in case DOM changed
                        if (currentChildrenEl) {
                            const isOpen = currentChildrenEl.style.display !== 'none';
                            // console.log(`Current state for ${component.getId()}: ${isOpen ? 'Open' : 'Closed'}. Toggling...`);
                            currentChildrenEl.style.display = isOpen ? 'none' : '';
                            // Update icon based on the new state
                            const currentArrowIcon = headerEl.querySelector('.layer-arrow-i');
                            if(currentArrowIcon) currentArrowIcon.textContent = isOpen ? '►' : '▼';
                            // console.log(`New state for ${component.getId()}: ${!isOpen ? 'Open' : 'Closed'}. Icon set to: ${isOpen ? '►' : '▼'}`);
                        } else {
                            console.log(`Children container not found on toggle for component: ${component.getId()}`);
                        }
                    });
                    headerEl.dataset.clickListenerAdded = 'true';
                }
            }

            // Set initial state (icon and visibility) for components with children
            if (childrenEl) {
                const initiallyOpen = component.get('open'); // Check GrapesJS model state
                // console.log(`Initial open state for ${component.getId()} from GrapesJS: ${initiallyOpen}`);
                let shouldBeOpen = false; // Default to closed

                // Explicitly check GrapesJS state if defined
                if (initiallyOpen === true) {
                    shouldBeOpen = true;
                } else if (initiallyOpen === false) {
                    shouldBeOpen = false;
                } 
                // Add specific component type checks if needed (e.g., keep 'body' open by default)
                // else if (component.get('type') === 'wrapper') { 
                //    shouldBeOpen = true; 
                // } 

                childrenEl.style.display = shouldBeOpen ? '' : 'none';
                if (arrowIcon) arrowIcon.textContent = shouldBeOpen ? '▼' : '►'; // Update existing or newly created icon
                // console.log(`Initial state set for ${component.getId()}: ${shouldBeOpen ? 'Open' : 'Closed'}`);
            }
            
        } else {
            // Component has no children, remove arrow icon and listener flag if they exist
            if (arrowIcon) {
                // console.log(`Removing arrow icon from component: ${component.getId()} as it has no children`);
                arrowIcon.remove();
            }
            if (headerEl.dataset.clickListenerAdded) {
                 // console.log(`Removing click listener flag from component: ${component.getId()}`);
                 delete headerEl.dataset.clickListenerAdded;
            }
        }

        // Recursively enhance children layers using GrapesJS layer collection
        const childLayers = layer.get('layers');
        if (childLayers && childLayers.length > 0) {
            // console.log(`Enhancing ${childLayers.length} children for component: ${component.getId()}`);
            childLayers.forEach(childLayer => {
                enhanceLayer(childLayer); // Recursive call
            });
        }
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
            // Eski event listener'ları temizle
            const newTab = tab.cloneNode(true);
            if (tab.parentNode) {
                tab.parentNode.replaceChild(newTab, tab);
            }
            
            newTab.addEventListener("click", function () {
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
        // Tüm butonları temizle
        const toolbarButtons = ['sw-visibility', 'cmd-clear', 'cmd-undo', 'cmd-redo'];
        toolbarButtons.forEach(id => {
            const button = document.getElementById(id);
            if (button) {
                const newButton = button.cloneNode(true);
                if (button.parentNode) {
                    button.parentNode.replaceChild(newButton, button);
                }
            }
        });
        
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
        // Tüm butonları temizle ve yeniden oluştur
        function resetButton(id) {
            const btn = document.getElementById(id);
            if (btn) {
                const newBtn = btn.cloneNode(true);
                if (btn.parentNode) {
                    btn.parentNode.replaceChild(newBtn, btn);
                }
                return newBtn;
            }
            return null;
        }
        
        // HTML kodu düzenleme
        const cmdCodeEdit = resetButton("cmd-code-edit");
        if (cmdCodeEdit) {
            cmdCodeEdit.addEventListener("click", () => {
                const htmlContent = editor.getHtml();
                StudioUtils.showEditModal("HTML Düzenle", htmlContent, (newHtml) => {
                    editor.setComponents(newHtml);
                });
            });
        }

        // CSS kodu düzenleme
        const cmdCssEdit = resetButton("cmd-css-edit");
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
        const cmdJsEdit = resetButton("cmd-js-edit");
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
        // Butonları temizle
        const deviceButtons = ['device-desktop', 'device-tablet', 'device-mobile'];
        deviceButtons.forEach(id => {
            const button = document.getElementById(id);
            if (button) {
                const newButton = button.cloneNode(true);
                if (button.parentNode) {
                    button.parentNode.replaceChild(newButton, button);
                }
            }
        });
        
        const deviceDesktop = document.getElementById("device-desktop");
        const deviceTablet = document.getElementById("device-tablet");
        const deviceMobile = document.getElementById("device-mobile");

        function toggleDeviceButtons(activeBtn) {
            const deviceBtns = document.querySelectorAll(".device-btns button");
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
            // Stil panelini özelleştir
            setTimeout(() => {
                try {
                    // Stil panelini özelleştir
                    document.querySelectorAll('.gjs-sm-sector').forEach(sector => {
                        sector.classList.add('custom-style-sector');
                        const title = sector.querySelector('.gjs-sm-sector-title');
                        if (title) {
                            title.classList.add('custom-sector-title');
                            
                            // Tüm eski event listener'ları temizle
                            const newTitle = title.cloneNode(true);
                            if (title.parentNode) {
                                title.parentNode.replaceChild(newTitle, title);
                            }
                            
                            // Sektör başlığını daha belirgin yap
                            newTitle.style.cursor = 'pointer';
                            newTitle.style.userSelect = 'none';
                            
                            // Tıklama olayını ekle
                            newTitle.addEventListener('click', function() {
                                const parent = this.closest('.gjs-sm-sector');
                                if (parent) {
                                    parent.classList.toggle('gjs-collapsed');
                                    const properties = parent.querySelector('.gjs-sm-properties');
                                    if (properties) {
                                        properties.style.display = parent.classList.contains('gjs-collapsed') ? 'none' : 'block';
                                    }
                                }
                            });
                        }
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
    }
    
    /**
     * Özel blok öğeleri için gelişmiş sürükle-bırak
     * @param {Object} editor - GrapesJS editor örneği
     * @param {number} retryCount - Tekrar deneme sayısı (opsiyonel)
     */
    function setupCustomDragDrop(editor, retryCount = 0) {
        // Tüm özel blok öğelerine event listener ekle
        document.querySelectorAll('.block-item').forEach(blockItem => {
            // Her öğeye benzersiz bir ID ekle
            const uniqueId = 'block-' + Math.random().toString(36).substr(2, 9);
            blockItem.id = uniqueId;
            
            // Eski event listener'ları temizle
            const newBlockItem = blockItem.cloneNode(true);
            if (blockItem.parentNode) {
                blockItem.parentNode.replaceChild(newBlockItem, blockItem);
            }
            blockItem = newBlockItem;

            // Drag event listener'larını ekle
            blockItem.addEventListener('dragstart', function(e) {
                const blockId = this.getAttribute('data-block-id');
                if (!blockId) return;
                
                console.log(`Drag started for block: ${blockId}`);
                window._draggedBlockId = blockId;
                
                // Özel veri tipi ekle
                e.dataTransfer.setData('application/studio-block', blockId);
                e.dataTransfer.setData('text/plain', blockId);
                
                // Eğer data-content özelliği varsa, bunu da ekle
                const blockContent = this.getAttribute('data-content');
                if (blockContent) {
                    e.dataTransfer.setData('application/studio-content', blockContent);
                    e.dataTransfer.setData('text/html', blockContent);
                }
                
                e.dataTransfer.effectAllowed = 'copy';
                
                // Drop işleme bayrağını sıfırla
                dropProcessed = false;
                
                // Sürükleme başladığında CSS sınıfı ekle
                this.classList.add('dragging');
            });
            
            // Drag bitirme olayı
            blockItem.addEventListener('dragend', function() {
                this.classList.remove('dragging');
                
                // Kısa bir süre sonra global değişkeni temizle
                setTimeout(() => {
                    if (window._draggedBlockId === this.getAttribute('data-block-id')) {
                        window._draggedBlockId = null;
                    }
                }, 500);
            });
        });
        
        // Canvas'a erişmeye çalış
        try {
            // Canvas iframe'ine erişmek için doğru yöntem
            const frame = editor.Canvas.getFrame();
            if (!frame) {
                if (retryCount < 10) {
                    console.log(`Canvas frame bulunamadı, ${retryCount + 1}. deneme yapılıyor...`);
                    setTimeout(() => setupCustomDragDrop(editor, retryCount + 1), 300);
                } else {
                    console.warn('Canvas frame 10 deneme sonrası bulunamadı.');
                }
                return;
            }
            
            // Canvas body'yi bul
            const canvas = frame.view.getBody();
            if (!canvas) {
                if (retryCount < 10) {
                    console.log(`Canvas body bulunamadı, ${retryCount + 1}. deneme yapılıyor...`);
                    setTimeout(() => setupCustomDragDrop(editor, retryCount + 1), 300);
                } else {
                    console.warn('Canvas body 10 deneme sonrası bulunamadı.');
                }
                return;
            }
            
            // Canvas için drop hedefini ayarla
            canvas.removeEventListener('dragenter', handleDragEnter);
            canvas.removeEventListener('dragleave', handleDragLeave);
            canvas.removeEventListener('drop', handleCanvasDrop);
            
            // Yeni dinleyicileri ekle
            canvas.addEventListener('dragover', handleDragOver);
            canvas.addEventListener('dragenter', handleDragEnter);
            canvas.addEventListener('dragleave', handleDragLeave);
            canvas.addEventListener('drop', handleCanvasDrop);
            
            console.log('Canvas drop event listener\'ları eklendi', canvas);
        } catch (error) {
            console.error('Canvas drop hedefi ayarlanırken hata oluştu:', error);
            
            // Hata durumunda tekrar dene
            if (retryCount < 10) {
                console.log(`Hata nedeniyle ${retryCount + 1}. deneme yapılıyor...`);
                setTimeout(() => setupCustomDragDrop(editor, retryCount + 1), 300);
            } else {
                console.warn('10 deneme sonrası başarısız oldu.');
            }
        }
    }
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
        // Canvas erişimini düzelt - editor yüklendikten sonra çağrılmalı
        editor.on('load', () => {
            setTimeout(() => {
                try {
                    // Canvas iframe'ine erişmek için doğru yöntem
                    const frame = editor.Canvas.getFrame();
                    if (!frame) {
                        console.error('Canvas frame bulunamadı');
                        return;
                    }
                    
                    const canvas = frame.view.getBody();
                    if (!canvas) {
                        console.error('Canvas body bulunamadı');
                        return;
                    }
                    
                    // Önceki event listener'ları tamamen kaldır
                    canvas.removeEventListener('dragover', handleDragOver);
                    canvas.removeEventListener('dragenter', handleDragEnter);
                    canvas.removeEventListener('dragleave', handleDragLeave);
                    canvas.removeEventListener('drop', handleCanvasDrop);
                    
                    // Yeni event listener'ları ekle
                    canvas.addEventListener('dragover', handleDragOver);
                    canvas.addEventListener('dragenter', handleDragEnter);
                    canvas.addEventListener('dragleave', handleDragLeave);
                    canvas.addEventListener('drop', handleCanvasDrop);
                    
                    console.log('Canvas drop event listener\'ları başarıyla eklendi.', canvas);
                } catch (error) {
                    console.error('Canvas drop listener ayarlanırken hata:', error);
                }
            }, 1000);
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
        
        // Editor canvas'ına stil ekle
        const editorCanvas = document.querySelector('.editor-canvas');
        if (editorCanvas) {
            editorCanvas.classList.add('drop-target');
        }
    }

    // Canvas dragleave olayı (Görsel geri bildirim için)
    function handleDragLeave(e) {
        // Sadece doğrudan body'den çıkıldığında class'ı kaldır
        if (e.currentTarget === e.target) {
            const editorCanvas = document.querySelector('.editor-canvas');
            if (editorCanvas) {
                editorCanvas.classList.remove('drop-target');
            }
        }
    }

    // Canvas drop olayı - yeniden düzenlendi
    function handleCanvasDrop(e) {
        // Eğer zaten bir drop işleniyorsa, tekrar işleme
        if (dropProcessed) {
            console.log('Drop zaten işleniyor, atlanıyor.');
            return;
        }
        dropProcessed = true; // Drop işlemini başlatıldı olarak işaretle
        console.log('Drop olayı işleniyor...');

        e.preventDefault();
        e.stopPropagation();
        
        console.log('Drop olayı tetiklendi!');
        
        // Global değişkenden blok ID'sini al
        let blockId = window._draggedBlockId;
        
        // Eğer global değişkenden alamadıysak, veri transferinden almayı dene
        if (!blockId && e.dataTransfer) {
            blockId = e.dataTransfer.getData('application/studio-block') || 
                    e.dataTransfer.getData('text/plain');
        }
        
        console.log('Sürüklenen Blok ID:', blockId);
        
        // ÖNEMLİ: Çift drop olayını önlemek için
        window._draggedBlockId = null;
        
        // Editöre erişim için global değişkeni kullan
        const editor = window.studioEditor || editorInstance;
        if (!editor) {
            console.error('Editor erişilemedi!');
            return;
        }
        
        // Blok içeriğini ekle
        if (blockId) {
            try {
                // BlockManager'dan bloğu al
                const block = editor.BlockManager.get(blockId);
                if (block) {
                    const content = block.get('content');
                    console.log('Eklenen içerik:', content);
                    
                    // GrapesJS zaten kendi içinde bileşeni ekliyor, biz tekrar eklemeyelim
                    console.log('GrapesJS tarafından otomatik olarak ekleniyor, editor.addComponents çağrılmıyor.');
                    /*
                    // Komponentleri ekle
                    editor.addComponents(content);
                    */
                    
                    // Başarılı bildirim göster
                    if (window.StudioUtils && typeof window.StudioUtils.showNotification === 'function') {
                        window.StudioUtils.showNotification(
                            'Başarılı',
                            `'${block.get('label')}' bileşeni eklendi`,
                            'success'
                        );
                    }
                } else {
                    // Özel blokları kontrol et
                    const blockContent = getCustomBlockContent(blockId);
                    if (blockContent) {
                        editor.addComponents(blockContent);
                        
                        if (window.StudioUtils && typeof window.StudioUtils.showNotification === 'function') {
                            window.StudioUtils.showNotification(
                                'Başarılı',
                                'Bileşen eklendi',
                                'success'
                            );
                        }
                    } else {
                        console.warn('Uygun içerik bulunamadı.');
                    }
                }
            } catch (error) {
                console.error('Bileşen eklenirken hata:', error);
            } finally {
                // Drop işlemi bittikten sonra, event listener'ı yeniden ekle
                setTimeout(() => {
                    // Reset drop processing flag after a short delay
                    dropProcessed = false;
                }, 50);
            }
        }
    }

    /**
     * Özel blok içeriğini almak için yardımcı fonksiyon
     * @param {string} blockId - Blok ID
     * @returns {string|null} - Blok içeriği veya null
     */
    function getCustomBlockContent(blockId) {
        if (!blockId) return null;
        
        // DOM'daki tüm özel blokları kontrol et
        const customBlocks = document.querySelectorAll('.block-item');
        for (const block of customBlocks) {
            if (block.getAttribute('data-block-id') === blockId) {
                return block.getAttribute('data-content');
            }
        }
        
        return null;
    }

    /**
     * Özel blok içeriğini almak için yardımcı fonksiyon
     * @param {string} blockId - Blok ID
     * @returns {string|null} - Blok içeriği veya null
     */
    function getCustomBlockContent(blockId) {
        if (!blockId) return null;
        
        // DOM'daki tüm özel blokları kontrol et
        const customBlocks = document.querySelectorAll('.block-item');
        for (const block of customBlocks) {
            if (block.getAttribute('data-block-id') === blockId) {
                return block.getAttribute('data-content');
            }
        }
        
        return null;
    }

    /**
     * Özel blokların sürükleme işlemi
     * @param {DragEvent} e - Drop olayı
     * @param {string|null} blockId - Blok ID
     * @param {Object} editor - GrapesJS editor örneği
     */
    function handleCustomBlockDrop(e, blockId, editor) {
        try {
            // DOM'da özel blokları kontrol et
            const customBlocks = document.querySelectorAll('.block-item');
            let blockContent = null;
            
            // BlockId varsa, o ID'ye sahip bloğu bul
            if (blockId) {
                const block = Array.from(customBlocks).find(
                    block => block.getAttribute('data-block-id') === blockId
                );
                
                if (block) {
                    blockContent = block.getAttribute('data-content');
                }
            } 
            
            // BlockId yoksa veya blockContent alınamadıysa, dataTransfer verilerini kontrol et
            if (!blockContent && e.dataTransfer) {
                blockContent = e.dataTransfer.getData('application/studio-content') || 
                            e.dataTransfer.getData('text/html');
            }
            
            if (blockContent) {
                console.log('Özel blok içeriği ekleniyor:', blockContent.substring(0, 100) + '...');
                editor.addComponents(blockContent);
                
                if (window.StudioUtils && typeof window.StudioUtils.showNotification === 'function') {
                    window.StudioUtils.showNotification(
                        'Başarılı',
                        'Bileşen eklendi',
                        'success'
                    );
                }
            } else {
                console.warn('Uygun içerik bulunamadı.');
            }
        } catch (error) {
            console.error('Özel blok eklenirken hata:', error);
        }
    }

    // Dışarıya aktarılacak fonksiyonlar
    return {
        setupUI: setupUI,
        handleDragOver: handleDragOver,
        handleDragEnter: handleDragEnter,
        handleDragLeave: handleDragLeave,
        handleCanvasDrop: handleCanvasDrop,
        enhanceCanvasDragDrop: enhanceCanvasDragDrop
    };
})();