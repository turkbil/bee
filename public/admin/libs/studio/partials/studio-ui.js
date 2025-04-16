/**
 * Studio Editor - UI Modülü
 * Modern arayüz işlevleri
 */

window.StudioUI = (function() {
    // Editor örneğini global olarak sakla
    let editorInstance = null;
    
    /**
     * Arayüz olaylarını kaydeder
     * @param {Object} editor - GrapesJS editor örneği
     */
    function setupUI(editor) {
        editorInstance = editor;
        
        setupTabs();
        setupDeviceToggle(editor);
        setupPanelSearch();
        initializePanelToggles();
        initializeBlockCategories();
        setupEditorStyles();
        standardizeLayerPanel();
        addCustomFunctions(editor);
        handleCanvasEvents(editor);
        
        // Bileşen seçimi olayı
        editor.on('component:selected', function() {
            // Stiller sekmesini etkinleştir
            setTimeout(() => {
                activateStylePanel();
            }, 100);
        });
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
                
                // Aktif sekme bilgisini localStorage'a kaydet
                localStorage.setItem('studio_active_tab', tabName);
            });
        });
        
        // Önceki aktif sekmeyi yükle
        const savedTab = localStorage.getItem('studio_active_tab');
        if (savedTab) {
            const activeTab = document.querySelector(`.panel-tab[data-tab="${savedTab}"]`);
            if (activeTab) {
                activeTab.click();
            }
        }
    }
    
    /**
     * Panel açma/kapama butonlarını ekle ve yapılandır
     */
    function initializePanelToggles() {
        // Sol panel açma/kapama butonu
        createPanelToggle('panel__left', 'fa-chevron-left');
        
        // Sağ panel açma/kapama butonu
        createPanelToggle('panel__right', 'fa-chevron-right');
        
        // Önceki panel durumlarını yükle
        loadPanelStates();
    }
    
    /**
     * Panel toggle butonunu oluştur
     * @param {string} panelClass - Panel sınıfı
     * @param {string} iconClass - İkon sınıfı
     */
    function createPanelToggle(panelClass, iconClass) {
        const panel = document.querySelector(`.${panelClass}`);
        if (!panel) return;
        
        // Zaten bir toggle butonu varsa kaldır
        const existingToggle = panel.querySelector('.panel-toggle');
        if (existingToggle) {
            existingToggle.remove();
        }
        
        // Toggle butonunu oluştur
        const toggleBtn = document.createElement('div');
        toggleBtn.className = 'panel-toggle';
        toggleBtn.innerHTML = `<i class="fas ${iconClass}"></i>`;
        
        // Toggle butonuna tıklama olayı ekle
        toggleBtn.addEventListener('click', function() {
            // Panel durumunu değiştir
            panel.classList.toggle('collapsed');
            
            // Panel durumunu localStorage'a kaydet
            savePanelStates();
        });
        
        // Panele toggle butonunu ekle
        panel.appendChild(toggleBtn);
    }
    
    /**
     * Panel açık/kapalı durumlarını localStorage'a kaydet
     */
    function savePanelStates() {
        // Sol panel durumu
        const leftPanel = document.querySelector('.panel__left');
        const leftCollapsed = leftPanel && leftPanel.classList.contains('collapsed');
        
        // Sağ panel durumu
        const rightPanel = document.querySelector('.panel__right');
        const rightCollapsed = rightPanel && rightPanel.classList.contains('collapsed');
        
        // Durumları localStorage'a kaydet
        localStorage.setItem('studio_left_panel_collapsed', leftCollapsed ? 'true' : 'false');
        localStorage.setItem('studio_right_panel_collapsed', rightCollapsed ? 'true' : 'false');
    }
    
    /**
     * Panel açık/kapalı durumlarını localStorage'dan yükle
     */
    function loadPanelStates() {
        // Sol panel durumu
        const leftPanel = document.querySelector('.panel__left');
        const leftSavedState = localStorage.getItem('studio_left_panel_collapsed');
        
        if (leftPanel && leftSavedState === 'true') {
            leftPanel.classList.add('collapsed');
        } else if (leftPanel) {
            leftPanel.classList.remove('collapsed');
        }
        
        // Sağ panel durumu
        const rightPanel = document.querySelector('.panel__right');
        const rightSavedState = localStorage.getItem('studio_right_panel_collapsed');
        
        if (rightPanel && rightSavedState === 'true') {
            rightPanel.classList.add('collapsed');
        } else if (rightPanel) {
            rightPanel.classList.remove('collapsed');
        }
    }
    
    /**
     * Panel arama kutuları için olay dinleyicileri ekle
     */
    function setupPanelSearch() {
        // Bileşenler arama - zaten mevcut 
        
        // Stiller arama
        const stylesSearch = document.getElementById("styles-search");
        if (stylesSearch) {
            // Mevcut listener'ı kaldır (varsa)
            const newStylesSearch = stylesSearch.cloneNode(true);
            if (stylesSearch.parentNode) {
                stylesSearch.parentNode.replaceChild(newStylesSearch, stylesSearch);
            }
            
            newStylesSearch.addEventListener("input", function() {
                const searchText = this.value.toLowerCase();
                const styleProperties = document.querySelectorAll('.gjs-sm-property');
                const styleSectors = document.querySelectorAll('.gjs-sm-sector');
                
                if (searchText === '') {
                    // Arama boşsa her şeyi göster
                    styleProperties.forEach(prop => prop.style.display = '');
                    styleSectors.forEach(sector => sector.style.display = '');
                    return;
                }
                
                // Arama varlığında eşleşen özellikleri göster
                let foundInSectors = new Set();
                
                styleProperties.forEach(prop => {
                    const label = prop.querySelector('.gjs-sm-label');
                    if (label && label.textContent.toLowerCase().includes(searchText)) {
                        prop.style.display = '';
                        const sector = prop.closest('.gjs-sm-sector');
                        if (sector) {
                            foundInSectors.add(sector);
                            sector.classList.remove('gjs-collapsed');
                        }
                    } else {
                        prop.style.display = 'none';
                    }
                });
                
                // Eşleşmeyen bölümleri gizle
                styleSectors.forEach(sector => {
                    if (foundInSectors.has(sector)) {
                        sector.style.display = '';
                    } else {
                        sector.style.display = 'none';
                    }
                });
            });
        }
        
        // Katmanlar arama
        const layersSearch = document.getElementById("layers-search");
        if (layersSearch) {
            // Mevcut listener'ı kaldır (varsa)
            const newLayersSearch = layersSearch.cloneNode(true);
            if (layersSearch.parentNode) {
                layersSearch.parentNode.replaceChild(newLayersSearch, layersSearch);
            }
            
            newLayersSearch.addEventListener("input", function() {
                const searchText = this.value.toLowerCase();
                const layers = document.querySelectorAll('.gjs-layer');
                
                layers.forEach(layer => {
                    const title = layer.querySelector('.gjs-layer-title');
                    if (title && title.textContent.toLowerCase().includes(searchText)) {
                        layer.style.display = '';
                        
                        // Ebeveyn katmanları da göster
                        let parent = layer.parentElement;
                        while (parent) {
                            if (parent.classList.contains('gjs-layer-children')) {
                                parent.style.display = '';
                                const parentLayer = parent.closest('.gjs-layer');
                                if (parentLayer) {
                                    parentLayer.style.display = '';
                                }
                            }
                            parent = parent.parentElement;
                        }
                    } else {
                        // Çocuğu var mı kontrol et
                        const children = layer.querySelector('.gjs-layer-children');
                        const hasVisibleChild = children && 
                            Array.from(children.querySelectorAll('.gjs-layer'))
                            .some(child => child.style.display !== 'none');
                        
                        if (hasVisibleChild) {
                            layer.style.display = '';
                        } else {
                            layer.style.display = 'none';
                        }
                    }
                });
            });
        }
    }
    
    /**
     * Blok kategorilerini başlat
     */
    function initializeBlockCategories() {
        const categories = document.querySelectorAll('.block-category-header');

        categories.forEach(category => {
            // Mevcut listener'ı kaldır (varsa)
            const newCategory = category.cloneNode(true);
            if (category.parentNode) {
                category.parentNode.replaceChild(newCategory, category);
            }
            
            // Tıklama olayını ekle
            newCategory.addEventListener('click', function() {
                const parent = this.closest('.block-category');
                if (!parent) return;
                
                parent.classList.toggle('collapsed');

                const content = parent.querySelector('.block-items');
                if (content) {
                    if (parent.classList.contains('collapsed')) {
                        content.style.display = 'none';
                    } else {
                        content.style.display = 'grid';
                    }
                }
                
                // Kategori durumlarını kaydet
                saveBlockCategoryStates();
            });
        });
        
        // Kategori durumlarını yükle
        loadBlockCategoryStates();
    }
    
    /**
     * Kategori açık/kapalı durumlarını localStorage'a kaydet
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
     * Kategori açık/kapalı durumlarını localStorage'dan yükle
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
     * Katmanlar panelini standartlaştır
     */
    function standardizeLayerPanel() {
        // GrapesJS yüklendikten sonra çalışması için bekle
        setTimeout(() => {
            // Katmanlar bölümünü Bileşenler/Stiller ile uyumlu hale getir
            const layerContainer = document.getElementById('layers-container');
            if (layerContainer) {
                // Arama alanı oluştur (eğer yoksa)
                if (!document.getElementById('layers-search')) {
                    const searchBox = document.createElement('div');
                    searchBox.className = 'blocks-search';
                    searchBox.innerHTML = `<input type="text" id="layers-search" class="form-control" placeholder="Katman ara...">`;
                    
                    if (layerContainer.previousElementSibling && layerContainer.previousElementSibling.classList.contains('blocks-search')) {
                        // Arama alanı var, güncelleme yapma
                    } else {
                        layerContainer.parentNode.insertBefore(searchBox, layerContainer);
                    }
                }
                
                // Katmanlar panel başlıklarına ikon ekle
                const layerGroupHeaders = layerContainer.querySelectorAll('.gjs-layer-group-header');
                layerGroupHeaders.forEach(header => {
                    if (!header.querySelector('i.fa')) {
                        const icon = document.createElement('i');
                        icon.className = 'fa fa-layer-group';
                        header.insertBefore(icon, header.firstChild);
                        
                        // Toggle ikon ekle (açılır/kapanır)
                        if (!header.querySelector('.toggle-icon')) {
                            const toggleIcon = document.createElement('i');
                            toggleIcon.className = 'toggle-icon';
                            header.appendChild(toggleIcon);
                        }
                    }
                });
                
                // Layer katmanlarına hover efekti ve diğer düzenlemeler için
                const allLayers = layerContainer.querySelectorAll('.gjs-layer');
                allLayers.forEach(layer => {
                    layer.classList.add('layer-styled');
                });
            }
        }, 1000);
    }
    
    /**
     * Canvas/Editor olaylarını işle
     * @param {Object} editor - GrapesJS editor örneği
     */
    function handleCanvasEvents(editor) {
        try {
            // Canvas içindeki değişiklikleri dinle
            editor.on('component:update', function() {
                // Katmanlar panelini güncelle
                setTimeout(function() {
                    standardizeLayerPanel();
                }, 300);
            });
            
            // Yeni bir bileşen eklendiğinde
            editor.on('component:add', function() {
                // Katmanlar panelini güncelle
                setTimeout(function() {
                    standardizeLayerPanel();
                }, 300);
            });
            
            // Sağ tıklama menüsü
            editor.on('contextmenu', function(event, model) {
                if (model) {
                    createContextMenu(event, model, editor);
                }
            });
            
            // Sürükle-bırak hedefi olarak canvas
            const editorCanvas = document.querySelector('.editor-canvas');
            if (editorCanvas) {
                editorCanvas.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    this.classList.add('drop-target');
                });
                
                editorCanvas.addEventListener('dragleave', function(e) {
                    e.preventDefault();
                    this.classList.remove('drop-target');
                });
                
                editorCanvas.addEventListener('drop', function(e) {
                    this.classList.remove('drop-target');
                });
            }
        } catch (error) {
            console.warn('Canvas olayları ayarlanırken hata:', error);
        }
    }
    
    /**
     * Sağ tıklama menüsü oluştur
     * @param {Event} event - Olay
     * @param {Object} model - Bileşen modeli
     * @param {Object} editor - GrapesJS editor örneği
     */
    function createContextMenu(event, model, editor) {
        event.preventDefault();
        
        // Mevcut menüyü temizle
        const existingMenu = document.querySelector('.studio-context-menu');
        if (existingMenu) {
            existingMenu.remove();
        }
        
        // Menü oluştur
        const menu = document.createElement('div');
        menu.className = 'studio-context-menu';
        menu.style.left = event.pageX + 'px';
        menu.style.top = event.pageY + 'px';
        
        // Menü öğelerini ekle
        const menuItems = [
            { text: 'Düzenle', icon: 'fa-edit', action: () => editor.select(model) },
            { text: 'Kopyala', icon: 'fa-copy', action: () => editor.runCommand('tlb-clone', { target: model }) },
            { text: 'Sil', icon: 'fa-trash', action: () => model.remove() },
            { type: 'divider' },
            { text: 'İçeriği Temizle', icon: 'fa-eraser', action: () => model.empty() },
            { type: 'divider' },
            { text: 'HTML Göster', icon: 'fa-code', action: () => showElementHtml(model) }
        ];
        
        menuItems.forEach(item => {
            if (item.type === 'divider') {
                const divider = document.createElement('div');
                divider.className = 'studio-context-menu-divider';
                menu.appendChild(divider);
            } else {
                const menuItem = document.createElement('div');
                menuItem.className = 'studio-context-menu-item';
                menuItem.innerHTML = `<i class="fas ${item.icon}"></i> ${item.text}`;
                menuItem.addEventListener('click', () => {
                    item.action();
                    menu.remove();
                });
                menu.appendChild(menuItem);
            }
        });
        
        // Menüyü ekle
        document.body.appendChild(menu);
        
        // Dışarı tıklandığında menüyü kapat
        document.addEventListener('click', function closeMenu(e) {
            if (!menu.contains(e.target)) {
                menu.remove();
                document.removeEventListener('click', closeMenu);
            }
        });
    }
    
    /**
     * Element HTML'ini göster
     * @param {Object} model - Bileşen modeli
     */
    function showElementHtml(model) {
        const html = model.toHTML();
        
        if (window.StudioUtils && typeof window.StudioUtils.showEditModal === 'function') {
            window.StudioUtils.showEditModal('Element HTML', html, function(newHtml) {
                model.replaceWith(newHtml);
            });
        } else {
            alert(html);
        }
    }
    
    /**
     * Stiller paneli otomatik aktivasyonu
     */
    function activateStylePanel() {
        const stylesTab = document.querySelector('.panel-tab[data-tab="styles"]');
        if (stylesTab && !stylesTab.classList.contains('active')) {
            // Mevcut aktif sekmeyi devre dışı bırak
            document.querySelectorAll('.panel-tab.active').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Stiller sekmesini etkinleştir
            stylesTab.classList.add('active');
            
            // İçerik panellerini güncelle
            document.querySelectorAll('.panel-tab-content').forEach(content => {
                content.classList.remove('active');
                if (content.getAttribute('data-tab-content') === 'styles') {
                    content.classList.add('active');
                }
            });
            
            // Aktif sekmeyi localStorage'a kaydet
            localStorage.setItem('studio_active_tab', 'styles');
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

        // Tüm butonları temizle ve yeniden oluştur
        function recreateButton(button) {
            if (!button) return null;
            
            const newButton = button.cloneNode(true);
            if (button.parentNode) {
                button.parentNode.replaceChild(newButton, button);
            }
            return newButton;
        }
        
        const newDesktopBtn = recreateButton(deviceDesktop);
        const newTabletBtn = recreateButton(deviceTablet);
        const newMobileBtn = recreateButton(deviceMobile);

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
            
            // Aktif cihazı localStorage'a kaydet
            if (activeBtn) {
                const deviceId = activeBtn.id.replace('device-', '');
                localStorage.setItem('studio_active_device', deviceId);
            }
        }

        if (newDesktopBtn) {
            newDesktopBtn.addEventListener("click", function () {
                editor.setDevice("Desktop");
                toggleDeviceButtons(this);
            });
        }

        if (newTabletBtn) {
            newTabletBtn.addEventListener("click", function () {
                editor.setDevice("Tablet");
                toggleDeviceButtons(this);
            });
        }

        if (newMobileBtn) {
            newMobileBtn.addEventListener("click", function () {
                editor.setDevice("Mobile");
                toggleDeviceButtons(this);
            });
        }
        
        // Önceki aktif cihazı yükle
        const savedDevice = localStorage.getItem('studio_active_device');
        if (savedDevice) {
            const activeDeviceBtn = document.getElementById(`device-${savedDevice}`);
            if (activeDeviceBtn) {
                activeDeviceBtn.click();
            }
        }
    }
    
    /**
     * Editor içindeki stilleri özelleştirir
     */
    function setupEditorStyles() {
        // Stil yöneticisi için gecikmeli düzeltme
        setTimeout(() => {
            // Stiller arama alanı oluştur (eğer yoksa)
            const stylesContainer = document.getElementById('styles-container');
            if (stylesContainer) {
                if (!document.getElementById('styles-search')) {
                    const searchBox = document.createElement('div');
                    searchBox.className = 'blocks-search';
                    searchBox.innerHTML = `<input type="text" id="styles-search" class="form-control" placeholder="Stil ara...">`;
                    
                    if (stylesContainer.previousElementSibling && stylesContainer.previousElementSibling.classList.contains('blocks-search')) {
                        // Arama alanı var, güncelleme yapma
                    } else {
                        stylesContainer.parentNode.insertBefore(searchBox, stylesContainer);
                    }
                }
            }
            
            // Stil sektörlerine ikon ekle
            const styleSectors = document.querySelectorAll('.gjs-sm-sector-title');
            
            styleSectors.forEach((sector, index) => {
                // İkon ekle (eğer yoksa)
                if (!sector.querySelector('i.fa')) {
                    const sectorName = sector.textContent.trim().toLowerCase();
                    let iconClass = 'fa-palette';
                    
                    // Sektör isminden ikon belirle
                    if (sectorName.includes('boyut')) iconClass = 'fa-ruler';
                    else if (sectorName.includes('düzen')) iconClass = 'fa-th-large';
                    else if (sectorName.includes('flex')) iconClass = 'fa-columns';
                    else if (sectorName.includes('tipografi')) iconClass = 'fa-font';
                    else if (sectorName.includes('dekorasyon')) iconClass = 'fa-paint-brush';
                    
                    const icon = document.createElement('i');
                    icon.className = 'fa ' + iconClass;
                    sector.insertBefore(icon, sector.firstChild);
                }
                
                // Katlanma işlevselliği
                const properties = sector.nextElementSibling;
                
                if (properties && properties.classList.contains('gjs-sm-properties')) {
                    // Mevcut listener'ı kaldır
                    const newSector = sector.cloneNode(true);
                    if (sector.parentNode) {
                        sector.parentNode.replaceChild(newSector, sector);
                    }
                    
                    newSector.addEventListener('click', function() {
                        const sectorDiv = this.parentElement;
                        sectorDiv.classList.toggle('gjs-collapsed');
                        
                        if (sectorDiv.classList.contains('gjs-collapsed')) {
                            properties.style.display = 'none';
                        } else {
                            properties.style.display = 'block';
                        }
                        
                        // Stil sektörü durumlarını kaydet
                        saveStyleSectorStates();
                    });
                    
                    // İlk sektör açık, diğerleri kapalı olsun (özel durum yoksa)
                    if (index === 0) {
                        newSector.parentElement.classList.remove('gjs-collapsed');
                        properties.style.display = 'block';
                    } else {
                        newSector.parentElement.classList.add('gjs-collapsed');
                        properties.style.display = 'none';
                    }
                }
            });
            
            // Stil sektörü durumlarını yükle
            loadStyleSectorStates();
        }, 500);
    }
    
    /**
     * Stil sektörü açık/kapalı durumlarını localStorage'a kaydet
     */
    function saveStyleSectorStates() {
        const sectors = document.querySelectorAll('.gjs-sm-sector');
        const states = {};
        
        sectors.forEach(sector => {
            const sectorTitle = sector.querySelector('.gjs-sm-sector-title');
            if (sectorTitle) {
                const sectorName = sectorTitle.textContent.trim();
                states[sectorName] = sector.classList.contains('gjs-collapsed');
            }
        });
        
        localStorage.setItem('studio_style_sectors', JSON.stringify(states));
    }
    
    /**
     * Stil sektörü açık/kapalı durumlarını localStorage'dan yükle
     */
    function loadStyleSectorStates() {
        const savedStates = localStorage.getItem('studio_style_sectors');
        if (!savedStates) return;
        
        try {
            const states = JSON.parse(savedStates);
            const sectors = document.querySelectorAll('.gjs-sm-sector');
            
            sectors.forEach(sector => {
                const sectorTitle = sector.querySelector('.gjs-sm-sector-title');
                if (sectorTitle) {
                    const sectorName = sectorTitle.textContent.trim();
                    if (states[sectorName] !== undefined) {
                        if (states[sectorName]) {
                            sector.classList.add('gjs-collapsed');
                            const properties = sector.querySelector('.gjs-sm-properties');
                            if (properties) {
                                properties.style.display = 'none';
                            }
                        } else {
                            sector.classList.remove('gjs-collapsed');
                            const properties = sector.querySelector('.gjs-sm-properties');
                            if (properties) {
                                properties.style.display = 'block';
                            }
                        }
                    }
                }
            });
        } catch (e) {
            console.error('Style sector states could not be loaded:', e);
        }
    }
    
    /**
     * Editöre özel özellikler ekle
     * @param {Object} editor - GrapesJS editor örneği 
     */
    function addCustomFunctions(editor) {
        // Canvası görünür kılma (bileşen sınırlarını göster/gizle)
        editor.Commands.add('sw-visibility', {
            run(editor) {
                const canvas = editor.Canvas;
                const classCanvas = 'gjs-cv-canvas';
                const classVisible = 'gjs-cv-visible';
                
                const frames = canvas.getFrames();
                frames.forEach(frame => {
                    const canvasBody = frame.view.getBody();
                    const canvasWrapper = frame.view.getWrapper();
                    
                    canvasWrapper.classList.toggle(classVisible);
                    canvasBody.classList.toggle(`${classCanvas}__${classVisible}`);
                });
            },
            stop(editor) {
                this.run(editor);
            }
        });
        
        // Başka özel komutlar buraya eklenebilir
    }
    
    return {
        setupUI: setupUI,
        initializeBlockCategories: initializeBlockCategories,
        setupEditorStyles: setupEditorStyles,
        standardizeLayerPanel: standardizeLayerPanel,
        handleCanvasEvents: handleCanvasEvents,
        addCustomFunctions: addCustomFunctions,
        activateStylePanel: activateStylePanel,
        initializePanelToggles: initializePanelToggles
    };
})();