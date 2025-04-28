/**
 * Studio Editor - Widget Yönetim Modülü
 */

window.StudioWidgetManager = (function() {
    // Widget verilerini sakla
    let widgetData = {};
    let loadedWidgets = false;
    
    /**
     * Widget verilerini yükle
     * @returns {Promise} Widget verileri ile çözülen promise
     */
    function loadWidgetData() {
        if (loadedWidgets) return Promise.resolve(widgetData);
        
        return fetch('/admin/studio/api/widgets')
            .then(response => response.json())
            .then(data => {
                if (data.widgets && Array.isArray(data.widgets)) {
                    widgetData = data.widgets.reduce((obj, widget) => {
                        obj[widget.id] = widget;
                        return obj;
                    }, {});
                    
                    loadedWidgets = true;
                    return widgetData;
                }
                return {};
            })
            .catch(error => {
                console.error('Widget verileri yüklenirken hata:', error);
                return {};
            });
    }
    
    /**
     * Widget verilerini getir
     * @param {number|string} widgetId - Widget ID
     * @returns {Object|null} Widget verisi
     */
    function getWidgetData(widgetId) {
        return widgetData[widgetId] || null;
    }
    
    /**
     * Widget modalını göster
     * @param {number|string} widgetId - Widget ID
     */
    function showWidgetModal(widgetId) {
        const widget = widgetData[widgetId];
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
        
        switch(widget.type) {
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
                <a href="/admin/widgetmanagement/items/${widgetId}" target="_blank" style="
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
        if (window.StudioWidgetLoader && typeof window.StudioWidgetLoader.loadWidgetBlocks === 'function') {
            window.StudioWidgetLoader.loadWidgetBlocks(editor);
        }
        
        // Editor yüklendiğinde mevcut widget'ları işle
        editor.on('load', () => {
            if (window.StudioWidgetLoader && typeof window.StudioWidgetLoader.processExistingWidgets === 'function') {
                window.StudioWidgetLoader.processExistingWidgets(editor);
            }
        });
        
        // Bileşen ekleme olayı
        editor.on('component:add', (component) => {
            const type = component.get('type');
            if (type === 'widget' || component.getAttributes()['data-type'] === 'widget') {
                component.set('type', 'widget');
                
                const attrs = component.getAttributes();
                const widgetId = attrs['data-widget-id'];
                const widgetType = attrs['data-widget-type'] || 'static';
                const filePath = attrs['data-file-path'] || '';
                
                // Özellikleri ayarla
                component.set('widget_id', widgetId);
                component.set('widget_type', widgetType);
                
                if (filePath) {
                    component.set('file_path', filePath);
                }
                
                // Widget tipine göre kısıtla
                if (widgetType === 'dynamic' || widgetType === 'module') {
                    component.set('draggable', false);
                    component.set('droppable', false);
                    component.set('selectable', true);
                    component.set('highlightable', false);
                    component.set('editable', false);
                    component.set('locked', true);
                    
                    component.setStyle({
                        'pointer-events': 'none',
                        'cursor': 'not-allowed'
                    });
                } else {
                    component.set('draggable', true);
                    component.set('selectable', true);
                    component.set('highlightable', true);
                    component.set('editable', true);
                    component.set('locked', false);
                }
                
                // Görünümü güncelle
                const view = component.view;
                if (view && typeof view.onRender === 'function') {
                    view.onRender();
                }
            }
            
            // Widget-embed tipini işle
            if (component.getClasses().includes('widget-embed') || component.getAttributes()['data-type'] === 'widget-embed') {
                component.set('type', 'widget-embed');
                
                // Görünümü güncelle
                const view = component.view;
                if (view && typeof view.onRender === 'function') {
                    setTimeout(() => view.onRender(), 50); // Biraz gecikme ekleyerek DOM'un hazır olmasını sağla
                }
            }
        });
        
        // Canvas drop olayını izle
        editor.on('canvas:drop', (droppedModel) => {
            setTimeout(() => {
                // Tüm widget ve widget-embed bileşenlerini yeniden işle
                let componentsToProcess = editor.DomComponents.getWrapper().find('[data-widget-id]');
                componentsToProcess = componentsToProcess.concat(editor.DomComponents.getWrapper().find('.widget-embed'));
                
                componentsToProcess.forEach(component => {
                    const attrs = component.getAttributes();
                    
                    // Widget tipini belirle
                    if (component.getClasses().includes('widget-embed') || 
                        attrs['data-type'] === 'widget-embed') {
                        // Widget-embed bileşeni
                        component.set('type', 'widget-embed');
                        
                        // Görünümü güncelle
                        const view = component.view;
                        if (view && typeof view.onRender === 'function') {
                            setTimeout(() => view.onRender(), 100);
                        }
                    } else {
                        // Normal widget bileşeni
                        const widgetType = attrs['data-widget-type'];
                        
                        if (widgetType === 'dynamic' || widgetType === 'module') {
                            component.set('type', 'widget');
                            component.set('draggable', false);
                            component.set('droppable', false);
                            component.set('selectable', true);
                            component.set('highlightable', false);
                            component.set('editable', false);
                            component.set('locked', true);
                            
                            component.setStyle({
                                'pointer-events': 'none',
                                'cursor': 'not-allowed'
                            });
                            
                            const view = component.view;
                            if (view && typeof view.onRender === 'function') {
                                view.onRender();
                            }
                        }
                    }
                });
                
                // Widget-embed elementlerini özel olarak işle
                if (window.StudioWidgetLoader && typeof window.StudioWidgetLoader.processWidgetEmbeds === 'function') {
                    window.StudioWidgetLoader.processWidgetEmbeds(editor);
                }
            }, 100);
        });
        
        // Aktif Widget bileşenleri blokları
        editor.BlockManager.add('tenant-widget-embed', {
            label: 'Aktif Widget',
            category: 'active-widgets',
            content: {
                type: 'widget-embed',
                classes: ['widget-embed'],
                attributes: { 'data-type': 'widget-embed', 'data-widget-id': '1' }
            },
            attributes: { class: 'fa fa-star' },
            render: ({ model, className }) => {
                return `
                    <div class="${className}">
                        <i class="fa fa-star"></i>
                        <div class="gjs-block-label">Aktif Widget</div>
                        <div class="gjs-block-type-badge dynamic">Dinamik</div>
                    </div>
                `;
            }
        });
        
        // Global widget yükleme fonksiyonu
        window.loadTenantWidget = function(widgetId) {
            const container = document.querySelector(`.widget-embed[data-tenant-widget-id='${widgetId}'] .widget-content-placeholder`);
            if (!container) return;
            
            fetch(`/admin/widgetmanagement/preview/embed/${widgetId}`)
                .then(response => response.text())
                .then(html => {
                    container.innerHTML = html;
                    
                    // Handlebars scriptlerini çalıştır
                    setTimeout(() => {
                        const scripts = container.querySelectorAll('script');
                        scripts.forEach(script => {
                            const newScript = document.createElement('script');
                            if (script.src) {
                                newScript.src = script.src;
                            } else {
                                newScript.textContent = script.textContent;
                            }
                            document.body.appendChild(newScript);
                        });
                    }, 100);
                })
                .catch(error => {
                    container.innerHTML = `<div class="alert alert-danger">Widget yüklenirken hata: ${error.message}</div>`;
                });
        };
    }
    
    return {
        loadWidgetData: loadWidgetData,
        getWidgetData: getWidgetData,
        showWidgetModal: showWidgetModal,
        setup: setup
    };
})();