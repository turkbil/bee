/**
 * Studio Editor - UI Modülü
 * Modern arayüz işlevleri
 */

window.StudioUI = (function() {
    // Editor örneğini global olarak sakla
    let editorInstance = null;
    let currentSelectedComponent = null;
    
    /**
     * Arayüz olaylarını kaydeder
     * @param {Object} editor - GrapesJS editor örneği
     */
    function setupUI(editor) {
        editorInstance = editor;
        
        setupPanelSearch();
        setupEditorStyles();
        standardizeLayerPanel();
        handleCanvasEvents(editor);
        
        // Quick Editor entegrasyonunu tamamla
        if (window.StudioQuickEditor) {
            console.log('Quick Editor UI entegrasyonu tamamlandı');
        }
        
        // Bileşen seçimi olayı
        editor.on('component:selected', function(component) {
            currentSelectedComponent = component;
            
            // Yapılandır sekmesini etkinleştir
            setTimeout(() => {
                activateConfigurePanel();
            }, 100);
        });
        
        // Bileşen seçimi iptal olayı
        editor.on('component:deselected', function() {
            currentSelectedComponent = null;
        });
        
        // Canvas tıklama olayını da dinle
        editor.on('canvas:click', function(event) {
            setTimeout(() => {
                const selected = editor.getSelected();
                if (selected) {
                    currentSelectedComponent = selected;
                    activateConfigurePanel();
                }
            }, 50);
        });
    }
    
    /**
     * Panel arama kutuları için olay dinleyicileri ekle
     */
    function setupPanelSearch() {
        // Memory management ile event listener'ları yönet
        const memoryManager = window.StudioMemoryManager;
        
        // Bileşenler arama
        const blocksSearch = document.getElementById("blocks-search");
        if (blocksSearch && memoryManager) {
            // Debounced search fonksiyonu
            const debouncedBlocksSearch = debounce(function(value) {
                if (window.StudioBlocks && window.StudioBlocks.filterBlocks) {
                    window.StudioBlocks.filterBlocks(value.toLowerCase(), editorInstance);
                }
            }, 300);
            
            memoryManager.addEventListener(blocksSearch, "input", function() {
                debouncedBlocksSearch(this.value);
            }, 'ui-search');
        }
        
        // Katmanlar arama
        const layersSearch = document.getElementById("layers-search");
        if (layersSearch && memoryManager) {
            // Debounced search fonksiyonu
            const debouncedLayersSearch = debounce(function(searchText) {
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
                
                // Eğer boş arama ise hepsini göster
                if (searchText === '') {
                    layers.forEach(layer => {
                        layer.style.display = '';
                    });
                }
            }, 300);
            
            memoryManager.addEventListener(layersSearch, "input", function() {
                debouncedLayersSearch(this.value.toLowerCase());
            }, 'ui-search');
        }
    }
    
    /**
     * Debounce utility function
     */
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
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
            // Canvas tıklama olayı - her tıklamada Yapılandır sekmesini aktifleştir
            editor.on('canvas:click', function() {
                setTimeout(() => {
                    activateConfigurePanel();
                }, 50);
            });
            
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
        
        if (window.StudioModal && typeof window.StudioModal.showEditModal === 'function') {
            window.StudioModal.showEditModal('Element HTML', html, function(newHtml) {
                model.replaceWith(newHtml);
            });
        } else {
            alert(html);
        }
    }

    /**
     * Yapılandır paneli otomatik aktivasyonu
     */
    function activateConfigurePanel() {
        // Sağ panelde Yapılandır sekmesini aktifleştir
        const configureTab = document.querySelector('.panel__right .panel-tab[data-tab="configure"]');
        if (configureTab && !configureTab.classList.contains('active')) {
            // StudioTabs modülünü kullan
            if (window.StudioTabs && window.StudioTabs.activateTab) {
                window.StudioTabs.activateTab('configure', 'right');
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
                        icon.style.marginRight = '8px';
                        icon.style.color = '#3b82f6';
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
            }
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
     * Sayı girişi butonlarını düzeltme
     */
    function fixNumberInputs() {
        // GrapesJS elementlerini hedeflemek için gecikme kullan
        setTimeout(() => {
            const allNumberInputs = document.querySelectorAll('.gjs-field-integer');
            
            allNumberInputs.forEach(container => {
                const arrowsContainer = container.querySelector('.gjs-field-arrows');
                if (!arrowsContainer) return;
                
                // Ok butonları
                const arrowUp = container.querySelector('.gjs-field-arrow-u');
                const arrowDown = container.querySelector('.gjs-field-arrow-d');
                
                if (!arrowUp || !arrowDown) return;
                
                // Input alanı
                const input = container.querySelector('input');
                if (!input) return;
                
                // Her bir ok için event listener ekle
                const newArrowUp = arrowUp.cloneNode(true);
                const newArrowDown = arrowDown.cloneNode(true);
                
                if (arrowUp.parentNode) {
                    arrowUp.parentNode.replaceChild(newArrowUp, arrowUp);
                }
                
                if (arrowDown.parentNode) {
                    arrowDown.parentNode.replaceChild(newArrowDown, arrowDown);
                }
                
                // Yukarı ok tıklama
                newArrowUp.addEventListener('click', function() {
                    const value = parseInt(input.value) || 0;
                    const step = parseInt(input.getAttribute('step')) || 1;
                    const max = parseInt(input.getAttribute('max')) || 9999;
                    
                    const newValue = Math.min(max, value + step);
                    input.value = newValue;
                    
                    // Değişikliği editöre bildir
                    const event = new Event('change', { bubbles: true });
                    input.dispatchEvent(event);
                });
                
                // Aşağı ok tıklama
                newArrowDown.addEventListener('click', function() {
                    const value = parseInt(input.value) || 0;
                    const step = parseInt(input.getAttribute('step')) || 1;
                    const min = parseInt(input.getAttribute('min')) || -9999;
                    
                    const newValue = Math.max(min, value - step);
                    input.value = newValue;
                    
                    // Değişikliği editöre bildir
                    const event = new Event('change', { bubbles: true });
                    input.dispatchEvent(event);
                });
            });
        }, 1000); // Sayfa tamamen yüklendikten 1 saniye sonra çalıştır
    }

    return {
        setupUI: setupUI,
        setupEditorStyles: setupEditorStyles,
        standardizeLayerPanel: standardizeLayerPanel,
        handleCanvasEvents: handleCanvasEvents,
        activateConfigurePanel: activateConfigurePanel,
        fixNumberInputs: fixNumberInputs,
        setupPanelSearch: setupPanelSearch
    };
})();