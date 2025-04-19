/**
 * Studio Editor - Widget Yönetim Modülü
 */
window.StudioWidgetManager = (function() {
    let widgetData = {};
    let loadedWidgets = false;
    
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
    
    function cleanTemplateVariables(html) {
        if (!html) return html;
        
        html = html.replace(/\{\{([^}]+)\}\}/g, function(match, content) {
            if (content.trim().startsWith('#') || content.trim().startsWith('/')) {
                return '';
            }
            return content.trim();
        });
        
        return html;
    }
    
    function registerWidgetComponents(editor) {
        if (!editor) return;
        
        const widgetType = 'widget';
        
        if (editor.Components.getType(widgetType)) {
            return;
        }
        
        editor.DomComponents.addType(widgetType, {
            model: {
                defaults: {
                    name: 'Widget',
                    tagName: 'div',
                    draggable: true,
                    droppable: false,
                    highlightable: true,
                    stylable: true,
                    selectable: true,
                    attributes: {
                        class: 'gjs-widget-wrapper',
                        'data-type': 'widget'
                    },
                    traits: [
                        {
                            type: 'text',
                            name: 'widget_id',
                            label: 'Widget ID',
                            changeProp: true
                        },
                        {
                            type: 'text',
                            name: 'widget_type',
                            label: 'Widget Tipi',
                            changeProp: true
                        }
                    ],
                    
                    init() {
                        this.on('change:widget_id', this.onWidgetIdChange);
                    },
                    
                    onWidgetIdChange() {
                        const widgetId = this.get('widget_id');
                        if (widgetId && widgetData[widgetId]) {
                            const widget = widgetData[widgetId];
                            this.set('content_html', widget.content_html);
                            this.set('content_css', widget.content_css);
                            this.set('content_js', widget.content_js);
                            this.set('widget_type', widget.type);
                            
                            if (widget.type === 'dynamic') {
                                this.set('editable', false);
                            } else {
                                this.set('editable', true);
                            }
                        }
                    }
                },
                
                isComponent(el) {
                    if (el.getAttribute && 
                        (el.getAttribute('data-type') === 'widget' || 
                         el.getAttribute('data-widget-id'))) {
                        return { type: 'widget' };
                    }
                    return false;
                }
            },
            
            view: {
                events: {
                    dblclick: 'onDblClick',
                    click: 'onClick'
                },
                
                init() {
                    this.listenTo(this.model, 'change:widget_id', this.onRender);
                },
                
                onRender() {
                    const el = this.el;
                    if (!el) return;
                    
                    const model = this.model;
                    const widgetId = model.get('widget_id') || model.getAttributes()['data-widget-id'];
                    const widget = widgetData[widgetId];
                    
                    if (!widget) return;
                    
                    const isDynamic = widget.type === 'dynamic';
                    
                    // Widget tamamını tıklanabilir yap
                    el.style.position = 'relative';
                    el.style.padding = '10px';
                    el.style.margin = '10px 0';
                    el.style.borderRadius = '4px';
                    el.style.cursor = isDynamic ? 'pointer' : 'default';
                    
                    if (isDynamic) {
                        el.style.border = '1px solid transparent';
                        el.style.boxShadow = '0 0 0 1px rgba(59, 130, 246, 0.5)';
                        el.contentEditable = "false";
                        
                        // Tamamı tıklanabilir olsun
                        el.style.pointerEvents = 'auto';
                        el.addEventListener('click', (e) => {
                            e.stopPropagation();
                            if (widgetId) {
                                window.open(`/admin/widgetmanagement/items/${widgetId}`, '_blank');
                            }
                        });
                        
                        // Alt öğeleri koru
                        const allChildren = el.querySelectorAll('*');
                        allChildren.forEach(child => {
                            child.contentEditable = "false";
                            // Her elementi tıklanabilir yap
                            child.style.pointerEvents = 'auto';
                            child.addEventListener('click', (e) => {
                                e.stopPropagation();
                                if (widgetId) {
                                    window.open(`/admin/widgetmanagement/items/${widgetId}`, '_blank');
                                }
                            });
                        });
                        
                        const templateText = el.innerHTML;
                        el.innerHTML = cleanTemplateVariables(templateText);
                    } else {
                        el.style.border = '1px solid #e2e8f0';
                    }
                    
                    // Widget kontrollerini temizle
                    const existingControls = el.querySelectorAll('.widget-controls');
                    existingControls.forEach(control => control.remove());
                    
                    if (isDynamic) {
                        // Dynamic widget için mini etiket ekle
                        const miniLabel = document.createElement('div');
                        miniLabel.className = 'widget-mini-label';
                        miniLabel.style.position = 'absolute';
                        miniLabel.style.top = '-8px';
                        miniLabel.style.right = '8px';
                        miniLabel.style.backgroundColor = '#3b82f6';
                        miniLabel.style.color = 'white';
                        miniLabel.style.padding = '1px 4px';
                        miniLabel.style.borderRadius = '2px';
                        miniLabel.style.fontSize = '9px';
                        miniLabel.style.fontWeight = 'bold';
                        miniLabel.style.opacity = '0.7';
                        miniLabel.style.zIndex = '10';
                        miniLabel.style.pointerEvents = 'none';
                        miniLabel.innerHTML = 'Widget';
                        el.appendChild(miniLabel);
                    }
                },
                
                onDblClick(e) {
                    if (e) {
                        e.stopPropagation();
                        e.preventDefault();
                    }
                    
                    const model = this.model;
                    const widgetId = model.get('widget_id') || model.getAttributes()['data-widget-id'];
                    
                    if (widgetId) {
                        showWidgetModal(widgetId);
                    }
                },
                
                onClick(e) {
                    const model = this.model;
                    const widgetId = model.get('widget_id') || model.getAttributes()['data-widget-id'];
                    const widget = widgetData[widgetId];
                    
                    if (widget && widget.type === 'dynamic') {
                        e.stopPropagation();
                        e.preventDefault();
                        showWidgetModal(widgetId);
                    } else {
                        this.model.set('status', 'selected');
                        
                        if (window.studioEditor) {
                            window.studioEditor.select(this.model);
                        }
                    }
                }
            }
        });
    }
    
    function showWidgetModal(widgetId) {
        const widget = widgetData[widgetId];
        if (!widget) return;
        
        // Mevcut modalı kaldır
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
        
        modalContent.innerHTML = `
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">
                <h3 style="margin: 0; font-size: 18px; font-weight: 600; color: #1e293b;">
                    ${widget.name} (${widget.type === 'dynamic' ? 'Dinamik' : 'Statik'})
                </h3>
                <button id="widget-modal-close" style="background: none; border: none; cursor: pointer; font-size: 18px;">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div style="margin-bottom: 20px;">
                <p>${widget.description || 'Bu widget hakkında açıklama yok.'}</p>
            </div>
            <div style="text-align: center; margin-top: 20px;">
                <a href="/admin/widgetmanagement/items/${widgetId}" target="_blank" style="
                    display: inline-block;
                    background-color: #3b82f6;
                    color: white;
                    padding: 8px 16px;
                    border-radius: 4px;
                    text-decoration: none;
                    font-weight: 500;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                ">
                    <i class="fa fa-edit mr-1"></i> Widget'ı Düzenle
                </a>
            </div>
        `;
        
        modal.appendChild(modalContent);
        document.body.appendChild(modal);
        
        // Kapatma olaylarını ekle
        document.getElementById('widget-modal-close').addEventListener('click', () => {
            modal.remove();
        });
        
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.remove();
            }
        });
    }
    
    function loadWidgetBlocks(editor) {
        if (!editor) return;
        
        loadWidgetData().then(() => {
            const widgets = Object.values(widgetData);
            
            widgets.forEach(widget => {
                const blockId = `widget-${widget.id}`;
                const isDynamic = widget.type === 'dynamic';
                
                let widgetHtml = widget.content_html || '';
                if (!widgetHtml) {
                    widgetHtml = `<div class="widget-placeholder">Widget: ${widget.name}</div>`;
                }
                
                if (isDynamic) {
                    widgetHtml = cleanTemplateVariables(widgetHtml);
                }
                
                const widgetWrapped = `<div data-widget-id="${widget.id}" class="gjs-widget-wrapper" data-type="widget" data-widget-type="${widget.type}" ${isDynamic ? 'contenteditable="false"' : ''}>
                    ${widgetHtml}
                </div>`;
                
                editor.BlockManager.add(blockId, {
                    label: widget.name,
                    category: widget.category || 'widget',
                    attributes: {
                        class: isDynamic ? 'fa fa-puzzle-piece' : 'fa fa-code',
                        title: `${widget.name} (${isDynamic ? 'Dinamik' : 'Statik'})`
                    },
                    content: {
                        type: 'widget',
                        widget_id: widget.id,
                        widget_type: widget.type,
                        html: widgetWrapped,
                        css: widget.content_css || '',
                        js: widget.content_js || '',
                        editable: !isDynamic
                    },
                    render: ({ model, className }) => {
                        return `
                            <div class="${className}">
                                <i class="${isDynamic ? 'fa fa-puzzle-piece' : 'fa fa-code'}"></i>
                                <div class="gjs-block-label">${widget.name}</div>
                                ${widget.thumbnail ? `<img src="${widget.thumbnail}" alt="${widget.name}" class="gjs-block-thumbnail" />` : ''}
                            </div>
                        `;
                    }
                });
            });
            
            if (window.StudioBlocks && typeof window.StudioBlocks.updateBlocksInCategories === 'function') {
                setTimeout(() => {
                    window.StudioBlocks.updateBlocksInCategories(editor);
                }, 300);
            }
        });
    }
    
    function processExistingWidgets(editor) {
        if (!editor) return;
        
        loadWidgetData().then(() => {
            const components = editor.DomComponents.getWrapper().find('[data-widget-id]');
            
            if (components && components.length > 0) {
                components.forEach(component => {
                    component.set('type', 'widget');
                    
                    const attrs = component.getAttributes();
                    const widgetId = attrs['data-widget-id'];
                    
                    if (widgetId && widgetData[widgetId]) {
                        const widget = widgetData[widgetId];
                        component.set('widget_id', widgetId);
                        component.set('widget_type', widget.type);
                        
                        if (widget.type === 'dynamic') {
                            component.set('editable', false);
                            component.set('highlightable', false);
                            component.set('selectable', false);
                        } else {
                            component.set('editable', true);
                        }
                        
                        const view = component.view;
                        if (view && typeof view.onRender === 'function') {
                            view.onRender();
                        }
                    }
                });
            }
        });
    }
    
    function setup(editor) {
        if (!editor) return;
        
        loadWidgetData();
        registerWidgetComponents(editor);
        loadWidgetBlocks(editor);
        
        editor.on('load', () => {
            processExistingWidgets(editor);
        });
        
        editor.on('component:add', (component) => {
            const type = component.get('type');
            if (type === 'widget' || component.getAttributes()['data-type'] === 'widget') {
                component.set('type', 'widget');
                
                const attrs = component.getAttributes();
                const widgetId = attrs['data-widget-id'];
                
                if (widgetId && widgetData[widgetId]) {
                    const widget = widgetData[widgetId];
                    
                    if (widget.type === 'dynamic') {
                        component.set('editable', false);
                        component.set('highlightable', false);
                        component.set('selectable', false);
                    }
                }
                
                const view = component.view;
                if (view && typeof view.onRender === 'function') {
                    view.onRender();
                }
            }
        });
    }
    
    return {
        setup: setup,
        loadWidgetData: loadWidgetData,
        registerWidgetComponents: registerWidgetComponents,
        loadWidgetBlocks: loadWidgetBlocks,
        processExistingWidgets: processExistingWidgets,
        showWidgetModal: showWidgetModal
    };
})();