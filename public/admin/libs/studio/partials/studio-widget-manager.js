/**
 * Studio Editor - Widget Yönetim Modülü
 */

window.StudioWidgetManager = (function() {
    // Widget verilerini sakla
    let widgetData = {};
    let tenantWidgetData = {};
    let loadedWidgets = false;
    
    /**
     * Widget verilerini yükle
     * @returns {Promise} Widget verileri ile çözülen promise
     */
    function loadWidgetData() {
        if (loadedWidgets) return Promise.resolve({widgets: widgetData, tenant_widgets: tenantWidgetData});
        
        return fetch('/admin/studio/api/widgets')
            .then(response => {
                if (!response.ok) {
                    throw new Error(`API yanıtı başarısız: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.widgets && Array.isArray(data.widgets)) {
                    widgetData = data.widgets.reduce((obj, widget) => {
                        obj[widget.id] = widget;
                        return obj;
                    }, {});
                }
                
                if (data.tenant_widgets && Array.isArray(data.tenant_widgets)) {
                    tenantWidgetData = data.tenant_widgets.reduce((obj, widget) => {
                        obj[widget.id.replace('tenant-widget-', '')] = widget;
                        return obj;
                    }, {});
                }
                
                loadedWidgets = true;
                return {widgets: widgetData, tenant_widgets: tenantWidgetData};
            })
            .catch(error => {
                console.error('Widget verileri yüklenirken hata:', error);
                return {widgets: {}, tenant_widgets: {}};
            });
    }
    
    /**
     * Widget verilerini getir
     * @param {number|string} widgetId - Widget ID
     * @returns {Object|null} Widget verisi
     */
    function getWidgetData(widgetId) {
        if (typeof widgetId === 'string' && widgetId.startsWith('tenant-widget-')) {
            const id = widgetId.replace('tenant-widget-', '');
            return tenantWidgetData[id] || null;
        }
        return widgetData[widgetId] || null;
    }
    
    /**
     * Tenant Widget verilerini getir
     * @param {number|string} tenantWidgetId - Tenant Widget ID
     * @returns {Object|null} Tenant Widget verisi
     */
    function getTenantWidgetData(tenantWidgetId) {
        return tenantWidgetData[tenantWidgetId] || null;
    }
    
    /**
     * Widget modalını göster
     * @param {number|string} widgetId - Widget ID
     */
    function showWidgetModal(widgetId) {
        const widget = getWidgetData(widgetId);
        if (!widget) return;
        
        // Mevcut modal varsa kaldır
        const existingModal = document.getElementById('widget-edit-modal');
        if (existingModal) {
            existingModal.remove();
        }
        
        // Modal oluştur
        const modal = document.createElement('div');
        modal.id = 'widget-edit-modal';
        modal.style.position = 'fixed';
        modal.style.top = '0';
        modal.style.left = '0';
        modal.style.width = '100%';
        modal.style.height = '100%';
        modal.style.backgroundColor = 'rgba(0,0,0,0.5)';
        modal.style.display = 'flex';
        modal.style.alignItems = 'center';
        modal.style.justifyContent = 'center';
        modal.style.zIndex = '9999';
        
        const modalContent = document.createElement('div');
        modalContent.style.backgroundColor = 'white';
        modalContent.style.borderRadius = '8px';
        modalContent.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
        modalContent.style.padding = '20px';
        modalContent.style.width = '500px';
        modalContent.style.maxWidth = '90%';
        modalContent.style.maxHeight = '90%';
        modalContent.style.overflow = 'auto';
        
        // Widget tipine göre başlık rengi
        let modalHeaderColor = '#10b981'; // Varsayılan yeşil (static)
        let widgetTypeText = 'Statik';
        
        const widgetType = widget.type || 'static';
        switch(widgetType) {
            case 'dynamic':
                modalHeaderColor = '#3b82f6'; // Mavi
                widgetTypeText = 'Dinamik';
                break;
            case 'module':
                modalHeaderColor = '#8b5cf6'; // Mor
                widgetTypeText = 'Modül';
                break;
            case 'file':
                modalHeaderColor = '#f59e0b'; // Turuncu
                widgetTypeText = 'Dosya';
                break;
        }
        
        let targetId = widgetId;
        if (widget.tenant_widget_id) {
            targetId = widget.tenant_widget_id;
        } else if (widget.is_tenant_widget) {
            targetId = widget.id.replace('tenant-widget-', '');
        }
        
        modalContent.innerHTML = `
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">
                <h3 style="margin: 0; font-size: 18px; font-weight: 600; color: ${modalHeaderColor};">
                    ${widget.name} 
                    <span style="display: inline-block; padding: 2px 6px; background-color: ${modalHeaderColor}; color: white; font-size: 12px; border-radius: 4px; margin-left: 8px;">
                        ${widgetTypeText}
                    </span>
                </h3>
                <button id="widget-modal-close" style="background: none; border: none; cursor: pointer; font-size: 18px;">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div style="margin-bottom: 20px;">
                <p>${widget.description || 'Bu widget hakkında açıklama yok.'}</p>
                ${widget.file_path ? `<p style="font-size: 13px; padding: 5px 10px; background-color: #f8fafc; border-radius: 4px;"><i class="fa fa-link me-1"></i> <strong>Dosya Yolu:</strong> ${widget.file_path}</p>` : ''}
            </div>
            <div style="text-align: center; margin-top: 20px;">
                <a href="/admin/widgetmanagement/items/${targetId}" target="_blank" style="
                    display: inline-block;
                    background-color: ${modalHeaderColor};
                    color: white;
                    padding: 8px 16px;
                    border-radius: 4px;
                    text-decoration: none;
                    font-weight: 500;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                ">
                    <i class="fa fa-edit me-2"></i> Widget'ı Düzenle
                </a>
            </div>
        `;
        
        modal.appendChild(modalContent);
        document.body.appendChild(modal);
        
        // Kapatma işlemleri
        document.getElementById('widget-modal-close').addEventListener('click', () => {
            modal.remove();
        });
        
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.remove();
            }
        });
    }
        
    // Custom studioLoadWidget removed; use loader module implementation
    
    /**
     * Widget bloklarını yükle
     */
    function loadWidgetBlocks(editor) {
        if (!editor) return;
        
        loadWidgetData().then((data) => {
            // Tenant Widget'ları yükle (aktif bileşenler)
            if (data.tenant_widgets) {
                const tenantWidgets = Object.values(data.tenant_widgets);
                
                tenantWidgets.forEach(widget => {
                    if (!widget || !widget.id) return;
                    
                    // Blok ID'sini prefix formatta ayarla
                    const widgetId = widget.id.toString();
                    const blockId = `tenant-widget-${widgetId}`;
                    const tenantWidgetId = widgetId;
                    
                    // Widget referans bloku oluştur
                    editor.BlockManager.add(blockId, {
                        label: widget.name,
                        category: 'active-widgets',
                        attributes: {
                            class: 'fa fa-star',
                            title: widget.name
                        },
                        content: {
                            type: 'widget-embed',
                            tenant_widget_id: tenantWidgetId,
                            attributes: {
                                'class': 'widget-embed',
                                'data-type': 'widget-embed',
                                'data-tenant-widget-id': tenantWidgetId,
                                'id': 'widget-embed-' + tenantWidgetId
                            },
                            components: [
                                {
                                    type: 'div',
                                    attributes: {
                                        'class': 'widget-content-placeholder',
                                        'id': 'widget-content-' + tenantWidgetId
                                    },
                                    content: '<div class="widget-loading" style="text-align:center; padding:20px;"><i class="fa fa-spin fa-spinner"></i> Widget içeriği yükleniyor...</div>'
                                }
                            ]
                        },
                        render: ({ model, className }) => {
                            return `
                                <div class="${className}">
                                    <i class="fa fa-star"></i>
                                    <div class="gjs-block-label">${widget.name}</div>
                                    <div class="gjs-block-type-badge active">Aktif</div>
                                </div>
                            `;
                        }
                    });
                });
            }
            
            // Widget stillerini ekle
            if (window.StudioWidgetLoader && typeof window.StudioWidgetLoader.addWidgetStyles === 'function') {
                window.StudioWidgetLoader.addWidgetStyles();
            }
            
            // Dinamik widget blokları eklendikten sonra blok durumlarını güncelle
            if (window.StudioBlockManager && typeof window.StudioBlockManager.updateBlocksInCategories === 'function') {
                window.StudioBlockManager.updateBlocksInCategories(editor);
            }
            
            // Module widget'ları ve tenant-widget'lar için butonları kontrol et
            checkAndDisableWidgetButtons(editor);
        });
    }
    
    /**
     * Mevcut widget'lar için blok butonlarını kontrol et ve devre dışı bırak
     * @param {Object} editor - GrapesJS editor örneği
     */
    function checkAndDisableWidgetButtons(editor) {
        // Sayfa içeriğini al
        const pageHtml = editor.getHtml();
        
        // Tenant widget ID'lerini bul
        const tenantWidgetRegex = /data-tenant-widget-id="(\d+)"/g;
        let tenantMatch;
        while ((tenantMatch = tenantWidgetRegex.exec(pageHtml)) !== null) {
            const widgetId = tenantMatch[1];
            if (!widgetId) continue;
            
            // Tenant widget block butonunu pasifleştir
            const blockEl = document.querySelector(`.block-item[data-block-id="tenant-widget-${widgetId}"]`);
            if (blockEl && blockEl.closest('.block-category[data-category="active-widgets"]')) {
                blockEl.classList.add('disabled');
                blockEl.setAttribute('draggable', 'false');
                blockEl.style.cursor = 'not-allowed';
                const badge = blockEl.querySelector('.gjs-block-type-badge');
                if (badge) {
                    badge.classList.replace('active', 'inactive');
                    badge.textContent = 'Pasif';
                }
            }
        }
        
        // Module widget ID'lerini bul
        const moduleWidgetRegex = /data-widget-module-id="(\d+)"/g;
        let moduleMatch;
        while ((moduleMatch = moduleWidgetRegex.exec(pageHtml)) !== null) {
            const moduleId = moduleMatch[1];
            if (!moduleId) continue;
            
            // Module widget block butonunu pasifleştir
            const blockEl = document.querySelector(`.block-item[data-block-id="widget-${moduleId}"]`);
            if (blockEl) {
                blockEl.classList.add('disabled');
                blockEl.setAttribute('draggable', 'false');
                blockEl.style.cursor = 'not-allowed';
                const badge = blockEl.querySelector('.gjs-block-type-badge');
                if (badge) {
                    badge.classList.replace('active', 'inactive');
                    badge.textContent = 'Pasif';
                }
            }
        }
        
        // Kısa modul kodlarını da kontrol et [[module:XX]]
        const moduleShortcodeRegex = /\[\[module:(\d+)\]\]/g;
        while ((moduleMatch = moduleShortcodeRegex.exec(pageHtml)) !== null) {
            const moduleId = moduleMatch[1];
            if (!moduleId) continue;
            
            // Module widget block butonunu pasifleştir
            const blockEl = document.querySelector(`.block-item[data-block-id="widget-${moduleId}"]`);
            if (blockEl) {
                blockEl.classList.add('disabled');
                blockEl.setAttribute('draggable', 'false');
                blockEl.style.cursor = 'not-allowed';
                const badge = blockEl.querySelector('.gjs-block-type-badge');
                if (badge) {
                    badge.classList.replace('active', 'inactive');
                    badge.textContent = 'Pasif';
                }
            }
        }
    }
    
    /**
     * Editör kurulumu
     * @param {Object} editor - GrapesJS editor örneği
     */
    function setup(editor) {
        if (!editor) return;
        
        // Widget verilerini yükle
        loadWidgetData();
        
        // Widget bileşenlerini kaydet
        if (window.StudioWidgetComponents && typeof window.StudioWidgetComponents.registerWidgetComponents === 'function') {
            window.StudioWidgetComponents.registerWidgetComponents(editor);
        }
        
        // Widget embed bileşenini kaydet
        if (window.StudioWidgetLoader && typeof window.StudioWidgetLoader.registerWidgetEmbedComponent === 'function') {
            window.StudioWidgetLoader.registerWidgetEmbedComponent(editor);
        }
        
        // Widget bloklarını yükle
        loadWidgetBlocks(editor);
        
        // Sayfa yüklenir yüklenmez mevcut widget'lar için butonları kontrol et
        setTimeout(() => {
            checkAndDisableWidgetButtons(editor);
        }, 300);
        
        // İlk yüklemede mevcut widget embed'ler için blok butonlarını pasifleştir
        if (window.StudioWidgetLoader && typeof window.StudioWidgetLoader.processExistingWidgets === 'function') {
            window.StudioWidgetLoader.processExistingWidgets(editor);
        }
        
        // Wrapper bileşenlerini tarayarak blok butonlarını pasifleştir
        editor.DomComponents.getWrapper().find('[data-tenant-widget-id]').forEach(comp => {
            const widgetId = comp.getAttributes()['data-tenant-widget-id'];
            if (!widgetId) return;
            const blockEl = document.querySelector(`.block-item[data-block-id="tenant-widget-${widgetId}"]`);
            if (blockEl && blockEl.closest('.block-category[data-category="active-widgets"]')) {
                blockEl.classList.add('disabled');
                blockEl.setAttribute('draggable', 'false');
                blockEl.style.cursor = 'not-allowed';
                const badge = blockEl.querySelector('.gjs-block-type-badge');
                if (badge) {
                    badge.classList.replace('active', 'inactive');
                    badge.textContent = 'Pasif';
                }
            }
        });
        
        // Module widget'ları için butonları pasifleştir
        editor.DomComponents.getWrapper().find('[data-widget-module-id]').forEach(comp => {
            const moduleId = comp.getAttributes()['data-widget-module-id'];
            if (!moduleId) return;
            const blockEl = document.querySelector(`.block-item[data-block-id="widget-${moduleId}"]`);
            if (blockEl) {
                blockEl.classList.add('disabled');
                blockEl.setAttribute('draggable', 'false');
                blockEl.style.cursor = 'not-allowed';
                const badge = blockEl.querySelector('.gjs-block-type-badge');
                if (badge) {
                    badge.classList.replace('active', 'inactive');
                    badge.textContent = 'Pasif';
                }
            }
        });
        
        // Editor yüklendiğinde mevcut widget'ları işle
        editor.on('load', () => {
            // Tüm tenant-widget ve module-widget'ları işle
            if (window.StudioWidgetLoader && typeof window.StudioWidgetLoader.processExistingWidgets === 'function') {
                window.StudioWidgetLoader.processExistingWidgets(editor);
            }
            
            // Sayfa yüklendiğinde mevcut widget embed'ler için blok butonlarını pasifleştir
            editor.DomComponents.getWrapper().find('[data-tenant-widget-id]').forEach(comp => {
                const widgetId = comp.getAttributes()['data-tenant-widget-id'];
                if (!widgetId) return;
                
                const blockEl = document.querySelector(`.block-item[data-block-id="tenant-widget-${widgetId}"]`);
                if (blockEl && blockEl.closest('.block-category[data-category="active-widgets"]')) {
                    blockEl.classList.add('disabled');
                    blockEl.setAttribute('draggable', 'false');
                    blockEl.style.cursor = 'not-allowed';
                    const badge = blockEl.querySelector('.gjs-block-type-badge');
                    if (badge) {
                        badge.classList.replace('active', 'inactive');
                        badge.textContent = 'Pasif';
                    }
                }
            });
            
            // Module widget'ları için aynı işlemi yap
            editor.DomComponents.getWrapper().find('[data-widget-module-id]').forEach(comp => {
                const moduleId = comp.getAttributes()['data-widget-module-id'];
                if (!moduleId) return;
                
                // Module widget block butonunu pasifleştir
                const blockEl = document.querySelector(`.block-item[data-block-id="widget-${moduleId}"]`);
                if (blockEl) {
                    blockEl.classList.add('disabled');
                    blockEl.setAttribute('draggable', 'false');
                    blockEl.style.cursor = 'not-allowed';
                    const badge = blockEl.querySelector('.gjs-block-type-badge');
                    if (badge) {
                        badge.classList.replace('active', 'inactive');
                        badge.textContent = 'Pasif';
                    }
                }
            });
            
            // Sayfa içeriğini analiz ederek module shortcode'ları da kontrol et
            const htmlContent = editor.getHtml();
            const moduleRegex = /\[\[module:(\d+)\]\]/g;
            let match;
            
            while ((match = moduleRegex.exec(htmlContent)) !== null) {
                const moduleId = match[1];
                if (!moduleId) continue;
                
                // Module widget block butonunu pasifleştir
                const blockEl = document.querySelector(`.block-item[data-block-id="widget-${moduleId}"]`);
                if (blockEl) {
                    blockEl.classList.add('disabled');
                    blockEl.setAttribute('draggable', 'false');
                    blockEl.style.cursor = 'not-allowed';
                    const badge = blockEl.querySelector('.gjs-block-type-badge');
                    if (badge) {
                        badge.classList.replace('active', 'inactive');
                        badge.textContent = 'Pasif';
                    }
                }
            }
        });
        
        // Bileşen ekleme olayı
        editor.on('component:add', component => {
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
                    
                    // Module widget block butonunu disable et
                    const blockEl = document.querySelector(`.block-item[data-block-id="widget-${moduleId}"]`);
                    if (blockEl) {
                        blockEl.classList.add('disabled');
                        blockEl.setAttribute('draggable', 'false');
                        blockEl.style.cursor = 'not-allowed';
                        const badge = blockEl.querySelector('.gjs-block-type-badge');
                        if (badge) {
                            badge.classList.replace('active', 'inactive');
                            badge.textContent = 'Pasif';
                        }
                    }
                    
                    // Module içeriğini hemen yükle
                    setTimeout(() => {
                        if (window.studioLoadModuleWidget) {
                            window.studioLoadModuleWidget(moduleId);
                        }
                    }, 50);
                }
            }
            
            // Widget embed kontrolü
            if (component.get('type') === 'widget-embed' || component.getAttributes()['data-tenant-widget-id']) {
                component.set('type', 'widget-embed');
                
                const view = component.view;
                if (view && typeof view.onRender === 'function') {
                    setTimeout(() => view.onRender(), 50);
                }
                
                const tenantWidgetId = component.getAttributes()['data-tenant-widget-id'] || 
                                    component.getAttributes()['data-widget-id'];
                                    
                if (tenantWidgetId) {
                    const blockId = `tenant-widget-${tenantWidgetId}`;
                    const blockEl = document.querySelector(`.block-item[data-block-id="${blockId}"]`);
                    if (blockEl && blockEl.closest('.block-category[data-category="active-widgets"]')) {
                        blockEl.classList.add('disabled');
                        blockEl.setAttribute('draggable', 'false');
                        blockEl.style.cursor = 'not-allowed';
                        const badge = blockEl.querySelector('.gjs-block-type-badge');
                        if (badge) {
                            badge.classList.replace('active', 'inactive');
                            badge.textContent = 'Pasif';
                        }
                    }
                    
                    component.set('tenant_widget_id', tenantWidgetId);
                    
                    setTimeout(() => {
                        if (window.studioLoadWidget) {
                            window.studioLoadWidget(tenantWidgetId);
                        }
                    }, 100);
                }
            }
        });
        
        // Widget silindiğinde loadedWidgets ve blok durumu resetle
        editor.on('component:remove', (component) => {
            const attrs = component.getAttributes();
            
            // Module widget silindiğinde
            const moduleId = attrs['data-widget-module-id'];
            if (moduleId) {
                // Module widget için loadedModules setinden sil
                if (window._loadedModules) {
                    window._loadedModules.delete(moduleId);
                }
                
                // Module widget block butonunu aktif yap
                const blockEl = document.querySelector(`.block-item[data-block-id="widget-${moduleId}"]`);
                if (blockEl) {
                    blockEl.classList.remove('disabled');
                    blockEl.setAttribute('draggable', 'true');
                    blockEl.style.cursor = 'grab';
                    const badge = blockEl.querySelector('.gjs-block-type-badge');
                    if (badge) {
                        badge.classList.replace('inactive', 'active');
                        badge.textContent = 'Aktif';
                    }
                }
            }
            
            // Widget embed silindiğinde
            const widgetId = attrs['data-tenant-widget-id'] || attrs['data-widget-id'];
            if (widgetId) {
                // loadedWidgets setinden sil
                if (window._loadedWidgets) {
                    window._loadedWidgets.delete(widgetId);
                }
                
                // Sadece Aktif Bileşenler kategorisindeki blokları aktif yap
                const blockId = `tenant-widget-${widgetId}`;
                const blockEl = document.querySelector(`.block-item[data-block-id="${blockId}"]`);
                if (blockEl && blockEl.closest('.block-category[data-category="active-widgets"]')) {
                    blockEl.classList.remove('disabled');
                    blockEl.setAttribute('draggable', 'true');
                    blockEl.style.cursor = 'grab';
                    const badge = blockEl.querySelector('.gjs-block-type-badge');
                    if (badge) {
                        badge.classList.replace('inactive', 'active');
                        badge.textContent = 'Aktif';
                    }
                }
            }
        });
        
        // Canvas drop olayını izle
        editor.on('canvas:drop', (droppedModel) => {
            setTimeout(() => {
                // Tüm widget-embed bileşenlerini yeniden işle
                let embedComponents = editor.DomComponents.getWrapper().find('.widget-embed');
                embedComponents = embedComponents.concat(
                    editor.DomComponents.getWrapper().find('[data-tenant-widget-id]')
                );
                
                embedComponents.forEach(component => {
                    component.set('type', 'widget-embed');
                    
                    // Görünümü güncelle
                    const view = component.view;
                    if (view && typeof view.onRender === 'function') {
                        setTimeout(() => view.onRender(), 100);
                    }
                    
                    // Widget ID'sini al ve widget içeriğini yükle
                    const tenantWidgetId = component.getAttributes()['data-tenant-widget-id'] || 
                                          component.getAttributes()['data-widget-id'];
                                          
                    if (tenantWidgetId) {
                        // Widget ID'sini komponent özelliği olarak ayarla
                        component.set('tenant_widget_id', tenantWidgetId);
                        
                        setTimeout(() => {
                            if (window.studioLoadWidget) {
                                window.studioLoadWidget(tenantWidgetId);
                            }
                        }, 200);
                    }
                });
                
                // Widget-embed elementlerini özel olarak işle
                if (window.StudioWidgetLoader && typeof window.StudioWidgetLoader.processWidgetEmbeds === 'function') {
                    window.StudioWidgetLoader.processWidgetEmbeds(editor);
                }
            }, 100);
        });
    }
    
    return {
        loadWidgetData: loadWidgetData,
        getWidgetData: getWidgetData,
        getTenantWidgetData: getTenantWidgetData,
        showWidgetModal: showWidgetModal,
        setup: setup,
        loadWidgetBlocks: loadWidgetBlocks,
        checkAndDisableWidgetButtons: checkAndDisableWidgetButtons
    };
})();