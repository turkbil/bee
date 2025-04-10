/**
 * Studio Editor Ana Modül
 * Tüm modülleri birleştiren ve başlatan ana dosya
 */

/**
 * Studio Editor için GrapesJS yapılandırması
 */
window.initStudioEditor = function (config) {
    console.log('Studio Editor başlatılıyor:', config);
    console.log('arg1:', arguments[0]);
    
    try {
        if (!config || !config.moduleId || config.moduleId <= 0) {
            console.error('Geçersiz konfigürasyon veya modül ID:', config);
            return null;
        }
        
        // Mevcut yükleme göstergesini temizle
        const existingLoader = document.querySelector('.studio-loader');
        if (existingLoader) {
            existingLoader.remove();
        }
        
        // Yükleme göstergesi ekle
        const loaderElement = document.createElement('div');
        loaderElement.className = 'studio-loader';
        loaderElement.style.position = 'fixed';
        loaderElement.style.top = '0';
        loaderElement.style.left = '0';
        loaderElement.style.width = '100%';
        loaderElement.style.height = '100%';
        loaderElement.style.backgroundColor = 'rgba(255, 255, 255, 0.8)';
        loaderElement.style.display = 'flex';
        loaderElement.style.alignItems = 'center';
        loaderElement.style.justifyContent = 'center';
        loaderElement.style.zIndex = '10000';
        loaderElement.style.transition = 'opacity 0.3s ease';
        
        loaderElement.innerHTML = `
            <div class="studio-loader-content" style="text-align: center; background-color: white; padding: 30px; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                <div style="margin-bottom: 15px;">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Yükleniyor...</span>
                    </div>
                </div>
                <h3 style="margin-bottom: 10px;">Studio Editor Yükleniyor</h3>
                <p style="color: #6c757d;">Lütfen bekleyin...</p>
            </div>
        `;
        
        document.body.appendChild(loaderElement);
        
        // Dummy editor objesi oluştur (asenkron yükleme öncesi)
        const dummyEditor = {
            on: function(eventName, callback) {
                // Event'leri kaydet
                if (!window.studioEditorEvents) {
                    window.studioEditorEvents = {};
                }
                if (!window.studioEditorEvents[eventName]) {
                    window.studioEditorEvents[eventName] = [];
                }
                window.studioEditorEvents[eventName].push(callback);
                console.log(`Event dinleyicisi eklendi: ${eventName}`);
            },
            getComponents: function() { return []; },
            getStyle: function() { return {}; }
        };
        
        // Asenkron yükleme işlemi
        setTimeout(() => {
            try {
                // Blok konteynerini sorgula
                const blocksContainer = document.querySelector('.blocks-container');
                
                // Yapılandırmayı güncelle
                if (blocksContainer) {
                    config.blocksContainer = blocksContainer;
                }
                
                let realEditor = null;
                
                // GrapesJS Editor yapılandırması
                if (window.StudioCore && typeof window.StudioCore.initEditor === 'function') {
                    realEditor = window.StudioCore.initEditor(config);
                } else {
                    console.error('StudioCore.initEditor bulunamadı!');
                    return null;
                }
                
                if (!realEditor) {
                    console.error('Editor başlatılamadı!');
                    return null;
                }
                
                // Dummy üzerine kayıtlı event'leri gerçek editöre geçir
                if (window.studioEditorEvents) {
                    Object.keys(window.studioEditorEvents).forEach(eventName => {
                        window.studioEditorEvents[eventName].forEach(callback => {
                            realEditor.on(eventName, callback);
                        });
                    });
                }
                
                // Global erişim için editörü güncelle
                window.studioEditor = realEditor;
                
                // Eklentileri yükle
                if (window.StudioPluginLoader && typeof window.StudioPluginLoader.loadPlugins === 'function') {
                    try {
                        window.StudioPluginLoader.loadPlugins(realEditor);
                    } catch (error) {
                        console.error('Eklentiler yüklenirken hata:', error);
                    }
                }
                
                // Komutları ve diğer işlemleri ekle
                try {
                    if (window.StudioBlocks) window.StudioBlocks.registerBlocks(realEditor);
                    if (window.StudioUI) window.StudioUI.setupUI(realEditor);
                    if (window.StudioActions) window.StudioActions.setupActions(realEditor, config);
                    setupCustomCommands(realEditor);
                    enhanceDragDrop(realEditor);
                } catch (error) {
                    console.error('Editör modülleri yüklenirken hata:', error);
                }
                
                // Yükleme işlemi tamamlandı
                console.log("Studio Editor başarıyla yüklendi!");
                
            } catch (error) {
                console.error('Studio Editor başlatılırken beklenmeyen hata:', error);
            }
        }, 500);
        
        return dummyEditor;
        
    } catch (error) {
        console.error('Studio Editor başlatılırken kritik hata:', error);
        return {
            on: function() {} // Boş fonksiyon
        };
    }
};

// Özel komutları ayarla
function setupCustomCommands(editor) {
    // Öğeleri görünür/gizli yap
    editor.Commands.add('sw-visibility', {
        run(editor) {
            const options = editor.getConfig().canvasConfig;
            options.customBadgeLabel = '';
            editor.Canvas.refresh();
            setTimeout(() => editor.Canvas.refresh(), 0);
            return options.showOffsets = !options.showOffsets;
        },
        stop() { }
    });

    // İçeriği temizle
    editor.Commands.add('core:canvas-clear', {
        run(editor) {
            editor.DomComponents.clear();
            editor.CssComposer.clear();
            setTimeout(() => editor.runCommand('core:component-outline'), 0);
        }
    });

    // Geri al komutunu iyileştir
    editor.Commands.add('core:undo', {
        run(editor) {
            editor.UndoManager.undo();
        }
    });

    // Yinele komutunu iyileştir
    editor.Commands.add('core:redo', {
        run(editor) {
            editor.UndoManager.redo();
        }
    });
    
    // Bileşen sınırlarını göster komutu
    editor.Commands.add('core:component-outline', {
        run(editor) {
            const options = editor.getConfig().canvasConfig;
            options.showOffsets = 1;
            editor.Canvas.refresh();
            setTimeout(() => editor.Canvas.refresh(), 0);
        }
    });
}

// Drag & Drop olaylarını iyileştir
function enhanceDragDrop(editor) {
    // GrapesJS bloklarını sürüklemek için ayarlar
    editor.on('block:drag:start', function(model) {
        console.log('Blok sürükleniyor:', model.get('label'));
        document.body.classList.add('dragging-mode');
    });
    
    editor.on('block:drag:stop', function(model) {
        console.log('Blok sürükleme bitti');
        document.body.classList.remove('dragging-mode');
    });
    
    // Özel blok öğeleri için ek işlem
    setTimeout(() => {
        const blockItems = document.querySelectorAll('.block-item');
        blockItems.forEach(item => {
            const blockId = item.getAttribute('data-block-id');
            if (!blockId) return;
            
            // Event listener'ları temizle
            ['dragstart', 'dragend', 'click'].forEach(eventName => {
                const oldListeners = item._eventListeners && item._eventListeners[eventName];
                if (oldListeners) {
                    oldListeners.forEach(listener => {
                        item.removeEventListener(eventName, listener);
                    });
                }
            });
            
            // Listener'ları saklamak için nesne
            if (!item._eventListeners) {
                item._eventListeners = {};
            }
            
            // Drag start
            const dragStartHandler = function(e) {
                const blockId = this.getAttribute('data-block-id');
                if (!blockId) return;
                
                e.dataTransfer.setData('application/studio-block', blockId);
                e.dataTransfer.setData('text/plain', blockId);
                e.dataTransfer.effectAllowed = 'copy';
                
                // Görsel bir drag işaretçisi oluştur
                const ghost = document.createElement('div');
                ghost.className = 'drag-ghost';
                ghost.innerHTML = this.innerHTML;
                ghost.style.position = 'absolute';
                ghost.style.top = '-1000px';
                ghost.style.opacity = '0';
                document.body.appendChild(ghost);
                
                try {
                    e.dataTransfer.setDragImage(ghost, 20, 20);
                } catch (err) {
                    console.warn('Drag image ayarlanamadı:', err);
                }
                
                this.classList.add('dragging');
                
                // Temizleme
                setTimeout(() => {
                    document.body.removeChild(ghost);
                }, 0);
            };
            
            // Drag end
            const dragEndHandler = function() {
                this.classList.remove('dragging');
            };
            
            // Click
            const clickHandler = function() {
                const blockId = this.getAttribute('data-block-id');
                if (!blockId) return;
                
                const block = editor.BlockManager.get(blockId);
                if (block) {
                    editor.addComponents(block.get('content'));
                    if (window.StudioUtils && typeof window.StudioUtils.showNotification === 'function') {
                        window.StudioUtils.showNotification('Başarılı', 'Bileşen eklendi', 'success');
                    }
                }
            };
            
            // Listener'ları ekle
            item.addEventListener('dragstart', dragStartHandler);
            item.addEventListener('dragend', dragEndHandler);
            item.addEventListener('click', clickHandler);
            
            // Listener'ları kaydet
            item._eventListeners.dragstart = [dragStartHandler];
            item._eventListeners.dragend = [dragEndHandler];
            item._eventListeners.click = [clickHandler];
        });
        
        // Canvas drop hedefini ayarla
        const canvas = document.querySelector('.editor-canvas');
        if (canvas) {
            // Önceki listener'ları temizle
            ['dragover', 'dragenter', 'dragleave', 'drop'].forEach(eventName => {
                const oldListener = canvas[`_${eventName}Handler`];
                if (oldListener) {
                    canvas.removeEventListener(eventName, oldListener);
                }
            });
            
            // Dragover handler
            canvas._dragoverHandler = function(e) {
                e.preventDefault();
                e.stopPropagation();
                e.dataTransfer.dropEffect = 'copy';
            };
            
            // Dragenter handler
            canvas._dragenterHandler = function(e) {
                e.preventDefault();
                e.stopPropagation();
                this.classList.add('drop-target');
            };
            
            // Dragleave handler
            canvas._dragleaveHandler = function(e) {
                e.preventDefault();
                e.stopPropagation();
                this.classList.remove('drop-target');
            };
            
            // Drop handler
            canvas._dropHandler = function(e) {
                e.preventDefault();
                e.stopPropagation();
                this.classList.remove('drop-target');
                
                const blockId = e.dataTransfer.getData('application/studio-block') || 
                               e.dataTransfer.getData('text/plain');
                
                if (blockId) {
                    const block = editor.BlockManager.get(blockId);
                    if (block) {
                        editor.addComponents(block.get('content'));
                        if (window.StudioUtils && typeof window.StudioUtils.showNotification === 'function') {
                            window.StudioUtils.showNotification('Başarılı', 'Bileşen eklendi', 'success');
                        }
                    }
                }
            };
            
            // Listener'ları ekle
            canvas.addEventListener('dragover', canvas._dragoverHandler);
            canvas.addEventListener('dragenter', canvas._dragenterHandler);
            canvas.addEventListener('dragleave', canvas._dragleaveHandler);
            canvas.addEventListener('drop', canvas._dropHandler);
        }
    }, 1000);
}

// Butonlara tooltip ekle
function addTooltips() {
    const buttons = document.querySelectorAll('.toolbar-btn');
    buttons.forEach(btn => {
        const id = btn.id;
        let tooltipText = '';
        
        // Buton türüne göre tooltip metni
        switch(id) {
            case 'sw-visibility':
                tooltipText = 'Bileşen sınırlarını göster/gizle';
                break;
            case 'cmd-clear':
                tooltipText = 'İçeriği temizle';
                break;
            case 'cmd-undo':
                tooltipText = 'Geri al';
                break;
            case 'cmd-redo':
                tooltipText = 'Yinele';
                break;
            case 'cmd-code-edit':
                tooltipText = 'HTML düzenle';
                break;
            case 'cmd-css-edit':
                tooltipText = 'CSS düzenle';
                break;
            case 'cmd-js-edit':
                tooltipText = 'JavaScript düzenle';
                break;
            case 'export-btn':
                tooltipText = 'Dışa aktar';
                break;
            case 'device-desktop':
                tooltipText = 'Masaüstü görünümü';
                break;
            case 'device-tablet':
                tooltipText = 'Tablet görünümü';
                break;
            case 'device-mobile':
                tooltipText = 'Mobil görünümü';
                break;
            default:
                tooltipText = '';
        }
        
        if (tooltipText) {
            // Tooltip elementi oluştur
            const tooltip = document.createElement('div');
            tooltip.className = 'studio-tooltip';
            tooltip.textContent = tooltipText;
            tooltip.style.position = 'absolute';
            tooltip.style.top = '-30px';
            tooltip.style.left = '50%';
            tooltip.style.transform = 'translateX(-50%)';
            tooltip.style.backgroundColor = '#333';
            tooltip.style.color = 'white';
            tooltip.style.padding = '5px 10px';
            tooltip.style.borderRadius = '4px';
            tooltip.style.fontSize = '12px';
            tooltip.style.zIndex = '999';
            tooltip.style.pointerEvents = 'none';
            tooltip.style.opacity = '0';
            tooltip.style.transition = 'opacity 0.3s';
            
            // Butonu tooltip için hazırla
            if (btn.style.position !== 'relative') {
                btn.style.position = 'relative';
            }
            
            btn.appendChild(tooltip);
            
            // Fare olayları
            btn.addEventListener('mouseenter', () => {
                tooltip.style.opacity = '1';
            });
            
            btn.addEventListener('mouseleave', () => {
                tooltip.style.opacity = '0';
            });
        }
    });
}

// Editor yükleme olayını dinle
document.addEventListener('editor:loaded', function() {
    console.log('Editor yüklendi olayı algılandı');
    
    // Akordeonları ve blokları yeniden başlat
    if (window.StudioUI && typeof window.StudioUI.setupUI === 'function') {
        const editor = window.StudioCore.getEditor();
        if (editor) {
            window.StudioUI.setupUI(editor);
        }
    }
    
    // Tooltip ekle
    setTimeout(addTooltips, 500);
    
    // Kategori panel özelleştirmeleri
    setTimeout(customizePanels, 500);
});

// Panel özelleştirmeleri
function customizePanels() {
    // Katmanlar panelini özelleştir
    const layersPanel = document.querySelector('#layers-container');
    if (layersPanel) {
        layersPanel.classList.add('custom-layers-panel');
        
        // Ebeveyn konteyner
        const parent = layersPanel.parentElement;
        if (parent && !parent.querySelector('.custom-panel-header')) {
            // Başlık ekle
            const layersHeader = document.createElement('div');
            layersHeader.className = 'custom-panel-header';
            layersHeader.style.padding = '12px 15px';
            layersHeader.style.backgroundColor = '#f1f5f9';
            layersHeader.style.borderBottom = '1px solid #e5e5e5';
            layersHeader.style.fontWeight = '600';
            layersHeader.style.color = '#64748b';
            layersHeader.innerHTML = '<i class="fas fa-layer-group me-2"></i> Katmanlar';
            
            // Başlığı panelin önüne ekle
            parent.insertBefore(layersHeader, layersPanel);
        }
    }
    
    // Stiller panelini özelleştir
    const stylesPanel = document.querySelector('#styles-container');
    if (stylesPanel) {
        stylesPanel.classList.add('custom-styles-panel');
        
        // Stil kategorilerine renk kodu ekle
        document.querySelectorAll('.gjs-sm-sector').forEach((sector, index) => {
            const categoryColors = [
                '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'
            ];
            
            const colorIndex = index % categoryColors.length;
            const color = categoryColors[colorIndex];
            
            // Kategori başlığına renk çubuğu ekle
            const title = sector.querySelector('.gjs-sm-sector-title');
            if (title) {
                title.style.borderLeft = `4px solid ${color}`;
                title.style.paddingLeft = '12px';
            }
        });
    }
    
    // Editor'ü başlatır ve gerekli modülleri yükler
    function initializeEditor(config) {
        if (!config || !config.moduleId || config.moduleId <= 0) {
            console.error('Geçersiz konfigürasyon veya modül ID:', config);
            return;
        }
        
        // Global değişkende sakla
        window.studioEditorConfig = config;
        
        // Editor başlat
        if (typeof window.initStudioEditor === 'function') {
            try {
                const editor = window.initStudioEditor(config);
                // Global erişim için kaydet
                window.studioEditor = editor;
                
                // Stil yöneticisi sorununu çöz
                setTimeout(() => {
                    if (window.StudioCore && typeof window.StudioCore.fixStyleManagerIssues === 'function') {
                        window.StudioCore.fixStyleManagerIssues();
                    }
                }, 1500); // Editor yüklendikten sonra yeterli bekleme
            } catch (error) {
                console.error('Studio Editor başlatılırken hata:', error);
            }
        } else {
            console.error('Studio Editor başlatılamıyor: initStudioEditor fonksiyonu bulunamadı!');
        }
    }

    // Özellikler panelini özelleştir
    const traitsPanel = document.querySelector('#traits-container');
    if (traitsPanel) {
        traitsPanel.classList.add('custom-traits-panel');
        
        // Ebeveyn konteyner
        const parent = traitsPanel.parentElement;
        if (parent && !parent.querySelector('.custom-panel-header')) {
            // Başlık ekle
            const traitsHeader = document.createElement('div');
            traitsHeader.className = 'custom-panel-header';
            traitsHeader.style.padding = '12px 15px';
            traitsHeader.style.backgroundColor = '#f1f5f9';
            traitsHeader.style.borderBottom = '1px solid #e5e5e5';
            traitsHeader.style.fontWeight = '600';
            traitsHeader.style.color = '#64748b';
            traitsHeader.innerHTML = '<i class="fas fa-sliders-h me-2"></i> Özellikler';
            
            // Başlığı panelin önüne ekle
            parent.insertBefore(traitsHeader, traitsPanel);
        }
    }
}