/**
 * Studio Editor - Bloklar Modülü
 * Blade şablonlarından yüklenen bloklar
 */

window.StudioBlocks = (function() {
    // Global kilitleme için flag - güçlendirildi
    let blocksLoaded = false;
    let apiRequested = false;
    let categoriesCreated = false;
    let isProcessing = false;
    let widgetManagerCalled = false;
    
    /**
     * Blade şablonlarından blokları kaydet
     * @param {Object} editor - GrapesJS editor örneği
     */
    function registerBlocks(editor) {
        // Güçlendirilmiş kilitleme sistemi
        if (blocksLoaded || apiRequested || isProcessing) {
            console.log("Bloklar zaten yüklenmiş veya işlem devam ediyor, işlem atlanıyor.");
            return Promise.resolve();
        }
        
        // İşlem kilidi aktif
        isProcessing = true;
        apiRequested = true;
        
        console.log("Server tarafından bloklar yükleniyor...");
        
        // Reset mevcut kategoriler ve bloklar
        try {
            editor.BlockManager.getAll().reset();
            editor.BlockManager.getCategories().reset();
        } catch (error) {
            console.warn("Blok reset hatası:", error);
        }
        
        // Server'dan blokları al
        return fetch('/admin/studio/api/blocks')
            .then(response => {
                console.log("API yanıtı alındı:", response.status);
                if (!response.ok) {
                    throw new Error(`API yanıtı başarısız: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log("API verileri alındı:", data);
                
                // Blokların başarıyla alındığını data.success kontrolü olmadan değerlendir
                if (data.blocks && Array.isArray(data.blocks)) {
                    // Kategorileri tanımla - API'den gelen kategorileri kaydet
                    Object.keys(data.categories || {}).forEach(key => {
                        console.log("Kategori ekleniyor:", key, "-", data.categories[key]);
                        try {
                            editor.BlockManager.getCategories().add({
                                id: key,
                                label: data.categories[key]
                            });
                        } catch (error) {
                            console.warn(`Kategori ${key} eklenirken hata:`, error);
                        }
                    });
                    
                    // Detaylı kategori bilgilerini global değişkene kaydet
                    if (data.categories_full) {
                        window.studioCategories = data.categories_full;
                    }
                    
                    // Blokları ekle
                    data.blocks.forEach(block => {
                        console.log("Blok ekleniyor:", block.id, "-", block.label, "-", "Kategori:", block.category);
                        
                        try {
                            // Blok konfigürasyonu
                            const blockConfig = {
                                label: block.label,
                                category: block.category,
                                content: block.content,
                                attributes: { class: block.icon || 'fa fa-cube' }
                            };
                            
                            if (block.media) {
                                blockConfig.media = block.media;
                            }
                            
                            editor.BlockManager.add(block.id, blockConfig);
                        } catch (error) {
                            console.warn(`Blok ${block.id} eklenirken hata:`, error);
                        }
                    });
                    
                    console.log(`${data.blocks.length} adet blok başarıyla yüklendi`);
                    
                    // Blokları kategorilere ata - bir kez yapılıyor
                    if (!categoriesCreated) {
                        console.log("Blok kategorileri oluşturuluyor...");
                        try {
                            window.StudioBlockCategories.createBlockCategories(editor, data.categories || {});
                            categoriesCreated = true;
                            
                            // Kategorilere blokları ekle ve işlemi tamamla
                            setTimeout(() => {
                                window.StudioBlockManager.updateBlocksInCategories(editor);
                                // Mevcut widget embed'leri işleyip blok butonlarını pasifleştir
                                if (window.StudioWidgetLoader && typeof window.StudioWidgetLoader.processExistingWidgets === 'function') {
                                    window.StudioWidgetLoader.processExistingWidgets(editor);
                                }
                                // İşaretleyelim ki tekrar yüklenmesin
                                blocksLoaded = true;
                                isProcessing = false;
                                
                                // Kategori durumlarını yükle
                                if (!window._blockCategoryStatesLoaded) {
                                    window._blockCategoryStatesLoaded = true;
                                    window.StudioBlockCategories.loadBlockCategoryStates();
                                }
                            }, 500);
                        } catch (error) {
                            console.error("Blok kategorileri oluşturulurken hata:", error);
                            isProcessing = false;
                        }
                    }

                    // Arama işlevini bir kez ayarla
                    if (!window._searchSetupDone) {
                        window.StudioBlockManager.setupBlockSearch(editor);
                        window._searchSetupDone = true;
                    }
                    
                    // Widget API'sini çağır ve widget bloklarını yükle - TEK SEFER
                    if (!widgetManagerCalled && window.StudioWidgetManager && typeof window.StudioWidgetManager.loadWidgetBlocks === 'function') {
                        widgetManagerCalled = true;
                        try {
                            window.StudioWidgetManager.loadWidgetBlocks(editor);
                        } catch (error) {
                            console.error("Widget blokları yüklenirken hata:", error);
                        }
                    }
                } else {
                    console.error("Blok yüklenemedi:", data.message || "Server yanıt vermedi");
                    isProcessing = false;
                }
            })
            .catch(error => {
                console.error("Bloklar yüklenirken hata oluştu:", error);
                // Hata durumunda kilidi serbest bırak ki yeniden deneme yapılabilsin
                apiRequested = false;
                isProcessing = false;
            });
    }
    
    /**
     * Canvas drop olaylarını ayarla
     * @param {Object} editor - GrapesJS editor örneği
     */
    function setupCanvasDropEvents(editor) {
        editor.on('block:drag:stop', (component, block) => {
            // Yeni eklenen komponenti kontrol et
            if (!component) return;
            
            // Module widget kontrolü
            if (component.get('type') === 'module-widget' || 
                (component.getAttributes && component.getAttributes()['data-widget-module-id'])) {
                
                const moduleId = component.get('widget_module_id') || 
                                component.getAttributes()['data-widget-module-id'];
                
                if (moduleId) {
                    console.log(`Module widget #${moduleId} eklendi, hemen yükleniyor...`);
                    
                    // Module widget bileşeni tipini ayarla
                    component.set('type', 'module-widget');
                    component.set('widget_module_id', moduleId);
                    
                    // Module içeriğini hemen yükle
                    setTimeout(() => {
                        if (window.studioLoadModuleWidget) {
                            window.studioLoadModuleWidget(moduleId);
                        }
                    }, 50);
                }
            }
            
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
            
            // Komponeti kontrol et
            checkComponents(component);
        });

        // Canvas'a module widget eklendiğinde hemen yükle
        editor.on('component:add', component => {
            // Module widget kontrolü
            if (component.get('type') === 'module-widget' || 
                (component.getAttributes && component.getAttributes()['data-widget-module-id'])) {
                
                const moduleId = component.get('widget_module_id') || 
                                component.getAttributes()['data-widget-module-id'];
                
                if (moduleId) {
                    // Module içeriğini hemen yükle
                    setTimeout(() => {
                        if (window.studioLoadModuleWidget) {
                            window.studioLoadModuleWidget(moduleId);
                        }
                    }, 50);
                }
            }
        });
    }

    /**
     * Bildirim toast'ı göster
     * @param {string} message - Bildirim mesajı 
     * @param {string} type - Bildirim tipi (success, error, warning, info)
     */
    function showToast(message, type = 'info') {
        try {
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
        } catch (error) {
            console.error("Toast gösterim hatası:", error);
        }
    }

    return {
        registerBlocks: registerBlocks,
        updateBlocksInCategories: window.StudioBlockManager ? window.StudioBlockManager.updateBlocksInCategories : null,
        setupBlockSearch: window.StudioBlockManager ? window.StudioBlockManager.setupBlockSearch : null,
        filterBlocks: window.StudioBlockManager ? window.StudioBlockManager.filterBlocks : null,
        showToast: showToast,
        saveBlockCategoryStates: window.StudioBlockCategories ? window.StudioBlockCategories.saveBlockCategoryStates : null,
        loadBlockCategoryStates: window.StudioBlockCategories ? window.StudioBlockCategories.loadBlockCategoryStates : null,
        setupCanvasDropEvents: setupCanvasDropEvents
    };
})();