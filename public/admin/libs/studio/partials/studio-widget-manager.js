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
        
        // Mustache değişkenlerini ({{variable}}) temizle
        html = html.replace(/\{\{([^}]+)\}\}/g, function(match, content) {
            // Özel direktifler (# ve /) ise tamamen kaldır
            if (content.trim().startsWith('#') || content.trim().startsWith('/')) {
                return '';
            }
            // Normal değişkenleri ismiyle değiştir (placeholder olarak)
            return `<span class="widget-variable">${content.trim()}</span>`;
        });
        
        // Blade direktiflerini temizle (@if, @foreach gibi)
        html = html.replace(/@(if|foreach|for|while|php|switch|case|break|continue|endforeach|endif|endfor|endwhile|endswitch|yield|section|endsection|include|extends)(\s*\([^)]*\)|\s+[^{]*)/g, '');
        
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
                        },
                        {
                            type: 'text',
                            name: 'file_path',
                            label: 'Dosya Yolu',
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
                            this.set('file_path', widget.file_path || '');
                            
                            // Widget tipine göre düzenlenebilirlik ayarları
                            if (widget.type === 'dynamic' || widget.type === 'module') {
                                this.set('editable', false);
                                this.set('highlightable', true);
                                this.set('selectable', true);
                            } else if (widget.type === 'static' || widget.type === 'file') {
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
                    
                    const widgetType = widget.type || 'static';
                    const isEditable = widgetType === 'static' || widgetType === 'file';
                    const filePath = widget.file_path || '';
                    
                    // Widget wrapper stil ve özelliklerini sıfırla
                    el.className = 'gjs-widget-wrapper';
                    
                    // Widget tipine göre farklı görsel stil uygula
                    switch(widgetType) {
                        case 'dynamic':
                            el.classList.add('dynamic-widget');
                            el.style.border = '2px solid #3b82f6'; // Mavi
                            el.style.backgroundColor = 'rgba(59, 130, 246, 0.05)';
                            el.style.boxShadow = '0 0 0 1px rgba(59, 130, 246, 0.5)';
                            el.contentEditable = "false";
                            break;
                            
                        case 'module':
                            el.classList.add('module-widget');
                            el.style.border = '2px solid #8b5cf6'; // Mor
                            el.style.backgroundColor = 'rgba(139, 92, 246, 0.05)';
                            el.style.boxShadow = '0 0 0 1px rgba(139, 92, 246, 0.5)';
                            el.contentEditable = "false";
                            break;
                            
                        case 'file':
                            el.classList.add('file-widget');
                            el.style.border = '2px solid #f59e0b'; // Turuncu
                            el.style.backgroundColor = 'rgba(245, 158, 11, 0.05)';
                            el.style.boxShadow = '0 0 0 1px rgba(245, 158, 11, 0.5)';
                            break;
                            
                        case 'static':
                        default:
                            el.classList.add('static-widget');
                            el.style.border = '2px solid #10b981'; // Yeşil
                            el.style.backgroundColor = 'rgba(16, 185, 129, 0.05)';
                            el.style.boxShadow = '0 0 0 1px rgba(16, 185, 129, 0.5)';
                            break;
                    }
                    
                    // Ortak stiller
                    el.style.padding = '12px';
                    el.style.margin = '10px 0';
                    el.style.borderRadius = '6px';
                    el.style.position = 'relative';
                    
                    // Düzenlenemeyen widget'lar için içerik koruma
                    if (!isEditable) {
                        // Tüm içeriklerin düzenlenebilmesini engelle
                        const allChildren = el.querySelectorAll('*');
                        allChildren.forEach(child => {
                            child.contentEditable = "false";
                            child.style.pointerEvents = 'inherit';
                        });
                        
                        // Template değişkenlerini temizle ve görsel olarak göster
                        if (widgetType === 'dynamic') {
                            const templateText = el.innerHTML;
                            el.innerHTML = cleanTemplateVariables(templateText);
                        }
                        
                        // Module tipinde widget'lar için canlı veri gösterimini simüle et
                        if (widgetType === 'module' && filePath) {
                            el.setAttribute('data-file-path', filePath);
                        }
                    }
                    
                    // Mevcut widget etiketlerini temizle
                    const existingLabels = el.querySelectorAll('.widget-label, .widget-edit-icon, .widget-mini-label');
                    existingLabels.forEach(label => label.remove());
                    
                    // Widget etiketi oluştur
                    createWidgetLabel(el, widget, widgetType);
                    
                    // Düzenlenemez widget'lar için düzenleme butonu ekle
                    if (!isEditable) {
                        createEditButton(el, widget, widgetType);
                    }
                    
                    // Widget tipine göre içerik davranışı ayarla
                    setupWidgetContentBehavior(el, widget, widgetType);
                },
                
                onDblClick(e) {
                    if (e) {
                        e.stopPropagation();
                        e.preventDefault();
                    }
                    
                    const model = this.model;
                    const widgetId = model.get('widget_id') || model.getAttributes()['data-widget-id'];
                    
                    if (widgetId) {
                        const widget = widgetData[widgetId];
                        if (!widget) return;
                        
                        const widgetType = widget.type || 'static';
                        
                        // Widget tipine göre davranış
                        if (widgetType === 'dynamic' || widgetType === 'module') {
                            showWidgetModal(widgetId);
                        } else if (widgetType === 'file' && widget.file_path) {
                            // File tipi için dosya yolu bilgisi göster
                            alert(`Dosya Yolu: ${widget.file_path}`);
                        }
                    }
                },
                
                onClick(e) {
                    const model = this.model;
                    const widgetId = model.get('widget_id') || model.getAttributes()['data-widget-id'];
                    
                    if (!widgetId) return;
                    
                    const widget = widgetData[widgetId];
                    if (!widget) return;
                    
                    const widgetType = widget.type || 'static';
                    
                    // Dynamic ve Module tipleri için özel tıklama davranışı
                    if (widgetType === 'dynamic' || widgetType === 'module') {
                        e.stopPropagation();
                        e.preventDefault();
                        showWidgetModal(widgetId);
                    } else {
                        // Static ve File tipleri için normal seçim davranışı
                        this.model.set('status', 'selected');
                        
                        if (window.studioEditor) {
                            window.studioEditor.select(this.model);
                        }
                    }
                }
            }
        });
    }
    
    // Widget etiketi oluşturma yardımcı fonksiyonu
    function createWidgetLabel(el, widget, widgetType) {
        const labelElement = document.createElement('div');
        labelElement.className = 'widget-label';
        labelElement.style.position = 'absolute';
        labelElement.style.top = '-10px';
        labelElement.style.left = '10px';
        labelElement.style.fontSize = '11px';
        labelElement.style.fontWeight = 'bold';
        labelElement.style.padding = '2px 8px';
        labelElement.style.borderRadius = '3px';
        labelElement.style.zIndex = '10';
        
        // Widget tipine göre farklı etiket
        switch(widgetType) {
            case 'dynamic':
                labelElement.style.backgroundColor = '#3b82f6'; // Mavi
                labelElement.style.color = 'white';
                labelElement.innerHTML = '<i class="fa fa-puzzle-piece me-1"></i> Dinamik Widget';
                break;
                
            case 'module':
                labelElement.style.backgroundColor = '#8b5cf6'; // Mor
                labelElement.style.color = 'white';
                labelElement.innerHTML = '<i class="fa fa-cube me-1"></i> Modül Widget';
                break;
                
            case 'file':
                labelElement.style.backgroundColor = '#f59e0b'; // Turuncu
                labelElement.style.color = 'white';
                labelElement.innerHTML = '<i class="fa fa-file-code me-1"></i> Dosya Widget';
                if (widget.file_path) {
                    labelElement.title = `Dosya: ${widget.file_path}`;
                }
                break;
                
            case 'static':
            default:
                labelElement.style.backgroundColor = '#10b981'; // Yeşil
                labelElement.style.color = 'white';
                labelElement.innerHTML = '<i class="fa fa-code me-1"></i> Statik Widget';
                break;
        }
        
        el.appendChild(labelElement);
    }
    
    // Düzenleme butonu oluşturma yardımcı fonksiyonu
    function createEditButton(el, widget, widgetType) {
        // Butonu sadece dynamic ve module tiplerinde göster
        if (widgetType !== 'dynamic' && widgetType !== 'module') return;
        
        const editIcon = document.createElement('div');
        editIcon.className = 'widget-edit-icon';
        editIcon.style.position = 'absolute';
        editIcon.style.top = '-10px';
        editIcon.style.right = '10px';
        editIcon.style.color = 'white';
        editIcon.style.padding = '2px 8px';
        editIcon.style.borderRadius = '3px';
        editIcon.style.fontSize = '11px';
        editIcon.style.fontWeight = 'bold';
        editIcon.style.zIndex = '10';
        editIcon.style.cursor = 'pointer';
        
        // Widget tipine göre buton rengi
        if (widgetType === 'dynamic') {
            editIcon.style.backgroundColor = '#3b82f6'; // Mavi
        } else if (widgetType === 'module') {
            editIcon.style.backgroundColor = '#8b5cf6'; // Mor
        }
        
        editIcon.innerHTML = '<i class="fa fa-edit"></i> Düzenle';
        
        editIcon.addEventListener('click', (e) => {
            e.stopPropagation();
            if (widget.id) {
                window.open(`/admin/widgetmanagement/items/${widget.id}`, '_blank');
            }
        });
        
        el.appendChild(editIcon);
    }
    
    // Widget içerik davranışı ayarlama yardımcı fonksiyonu
    function setupWidgetContentBehavior(el, widget, widgetType) {
        // Dynamic ve module tiplerinde tıklama ve düzenleme özelliklerini engelle
        if (widgetType === 'dynamic' || widgetType === 'module') {
            el.style.cursor = 'pointer';
            
            // İçeriğe tıklandığında widget modal göster
            el.addEventListener('click', (e) => {
                e.stopPropagation();
                showWidgetModal(widget.id);
            });
        }
        
        // Module ve file tiplerinde dosya yolunu göster
        if ((widgetType === 'module' || widgetType === 'file') && widget.file_path) {
            // Dosya yolu bilgisini widget alt kısmında göster
            const filePathInfo = document.createElement('div');
            filePathInfo.className = 'widget-file-path';
            filePathInfo.style.borderTop = '1px dashed rgba(0,0,0,0.1)';
            filePathInfo.style.marginTop = '8px';
            filePathInfo.style.paddingTop = '4px';
            filePathInfo.style.fontSize = '10px';
            filePathInfo.style.color = '#666';
            filePathInfo.innerHTML = `<i class="fa fa-link me-1"></i> ${widget.file_path}`;
            
            el.appendChild(filePathInfo);
        }
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
                const widgetType = widget.type || 'static';
                const isEditable = widgetType === 'static' || widgetType === 'file';
                
                let widgetHtml = widget.content_html || '';
                if (!widgetHtml) {
                    widgetHtml = `<div class="widget-placeholder">Widget: ${widget.name}</div>`;
                }
                
                // Dynamic ve module tiplerindeki template değişkenlerini temizle
                if (widgetType === 'dynamic') {
                    widgetHtml = cleanTemplateVariables(widgetHtml);
                }
                
                const widgetWrapped = `<div data-widget-id="${widget.id}" class="gjs-widget-wrapper" data-type="widget" data-widget-type="${widgetType}" ${!isEditable ? 'contenteditable="false"' : ''} ${widget.file_path ? `data-file-path="${widget.file_path}"` : ''}>
                    ${widgetHtml}
                </div>`;
                
                // Widget tipine göre icon ve kategori belirle
                let iconClass = 'fa fa-code'; // Varsayılan (static)
                let badgeClass = 'static';
                
                switch(widgetType) {
                    case 'dynamic':
                        iconClass = 'fa fa-puzzle-piece';
                        badgeClass = 'dynamic';
                        break;
                    case 'module':
                        iconClass = 'fa fa-cube';
                        badgeClass = 'module';
                        break;
                    case 'file':
                        iconClass = 'fa fa-file-code';
                        badgeClass = 'file';
                        break;
                }
                
                editor.BlockManager.add(blockId, {
                    label: widget.name,
                    category: widget.category || 'widget',
                    attributes: {
                        class: iconClass,
                        title: `${widget.name} (${widgetType})`
                    },
                    content: {
                        type: 'widget',
                        widget_id: widget.id,
                        widget_type: widgetType,
                        file_path: widget.file_path || '',
                        html: widgetWrapped,
                        css: widget.content_css || '',
                        js: widget.content_js || '',
                        editable: isEditable
                    },
                    render: ({ model, className }) => {
                        return `
                            <div class="${className}">
                                <i class="${iconClass}"></i>
                                <div class="gjs-block-label">${widget.name}</div>
                                ${widget.thumbnail ? `<img src="${widget.thumbnail}" alt="${widget.name}" class="gjs-block-thumbnail" />` : ''}
                                <div class="gjs-block-type-badge ${badgeClass}">${widgetType}</div>
                            </div>
                        `;
                    }
                });
            });
            
            // Blok tipleri için stil ekle (eğer yoksa)
            if (!document.getElementById('widget-block-styles')) {
                const styleEl = document.createElement('style');
                styleEl.id = 'widget-block-styles';
                styleEl.innerHTML = `
                    .gjs-block-type-badge {
                        position: absolute;
                        right: 5px;
                        bottom: 5px;
                        font-size: 9px;
                        padding: 1px 4px;
                        border-radius: 3px;
                    }
                    .gjs-block-type-badge.dynamic {
                        background-color: #3b82f6;
                        color: white;
                    }
                    .gjs-block-type-badge.static {
                        background-color: #10b981;
                        color: white;
                    }
                    .gjs-block-type-badge.module {
                        background-color: #8b5cf6;
                        color: white;
                    }
                    .gjs-block-type-badge.file {
                        background-color: #f59e0b;
                        color: white;
                    }
                    .widget-variable {
                        display: inline-block;
                        background-color: rgba(59, 130, 246, 0.1);
                        padding: 0 4px;
                        border-radius: 3px;
                        color: #3b82f6;
                        font-style: italic;
                    }
                `;
                document.head.appendChild(styleEl);
            }
            
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
                        component.set('widget_type', widget.type || 'static');
                        
                        if (widget.file_path) {
                            component.set('file_path', widget.file_path);
                        }
                        
                        // Widget tipine göre editör davranışını ayarla
                        const widgetType = widget.type || 'static';
                        const isEditable = widgetType === 'static' || widgetType === 'file';
                        
                        component.set('editable', isEditable);
                        component.set('highlightable', true);
                        component.set('selectable', true);
                        
                        // Görünümü güncelle
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
                const widgetType = attrs['data-widget-type'] || 'static';
                const filePath = attrs['data-file-path'] || '';
                
                // Özellikleri ayarla
                component.set('widget_id', widgetId);
                component.set('widget_type', widgetType);
                
                if (filePath) {
                    component.set('file_path', filePath);
                }
                
                // Widget tipine göre editör davranışını ayarla
                const isEditable = widgetType === 'static' || widgetType === 'file';
                component.set('editable', isEditable);
                component.set('highlightable', true);
                component.set('selectable', true);
                
                // Görünümü güncelle
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