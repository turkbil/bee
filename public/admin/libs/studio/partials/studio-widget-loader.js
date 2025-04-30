/**
 * Studio Editor - Widget Yükleme Modülü
 * Widget verilerini yükleme ve dönüştürme
 */

window.StudioWidgetLoader = (function() {
    // Widget bloklarını yükle
    function loadWidgetBlocks(editor) {
        if (!editor || !window.StudioWidgetManager) return;
        
        window.StudioWidgetManager.loadWidgetData().then((data) => {
            // Tenant Widget'ları yükle (aktif bileşenler)
            if (data.tenant_widgets) {
                const tenantWidgets = Object.values(data.tenant_widgets);
                
                tenantWidgets.forEach(widget => {
                    if (!widget || !widget.id) return;
                    
                    const blockId = widget.id;
                    const tenantWidgetId = widget.tenant_widget_id || widget.id.replace('tenant-widget-', '');
                    
                    // Widget referans bloku oluştur
                    editor.BlockManager.add('tenant-widget-' + tenantWidgetId, {
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
            
            // Stilleri ekle
            addWidgetStyles();
        });
    }
    
    // Template değişkenlerini temizle
    function cleanTemplateVariables(html) {
        if (!html) return html;
        
        // Mustache değişkenlerini ({{variable}}) temizle
        html = html.replace(/\{\{([^}]+)\}\}/g, function(match, content) {
            if (content.trim().startsWith('#') || content.trim().startsWith('/')) {
                return '';
            }
            return `<span class="widget-variable">${content.trim()}</span>`;
        });
        
        // Blade direktiflerini temizle
        html = html.replace(/@(if|foreach|for|while|php|switch|case|break|continue|endforeach|endif|endfor|endwhile|endswitch|yield|section|endsection|include|extends)(\s*\([^)]*\)|\s+[^{]*)/g, '');
        
        return html;
    }
    
    // Widget stilerini ekle
    function addWidgetStyles() {
        if (document.getElementById('widget-block-styles')) {
            return;
        }
        
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
            .gjs-block-type-badge.active {
                background-color: #e11d48;
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
            [data-locked="true"] {
                user-select: none !important;
                -webkit-user-select: none !important;
                cursor: not-allowed !important;
                pointer-events: none !important;
            }
            .widget-overlay {
                pointer-events: auto !important;
            }
            .dynamic-widget, .module-widget {
                cursor: not-allowed !important;
            }
            .widget-embed-placeholder {
                border: 2px dashed #3b82f6;
                padding: 15px;
                border-radius: 5px;
                background-color: rgba(59, 130, 246, 0.05);
                text-align: center;
                font-style: italic;
                position: relative;
            }
            .widget-embed-placeholder:before {
                content: "Dinamik Widget";
                position: absolute;
                top: -10px;
                left: 10px;
                background-color: #3b82f6;
                color: white;
                font-size: 10px;
                padding: 2px 6px;
                border-radius: 3px;
                font-style: normal;
            }
            .widget-embed {
                position: relative;
                display: block;
                min-height: 50px;
                border: 2px solid #e11d48;
                border-radius: 6px;
                padding: 8px;
                margin: 10px 0;
                background-color: rgba(225, 29, 72, 0.05);
            }
            .widget-embed-label {
                position: absolute;
                top: -10px;
                left: 10px;
                background-color: #e11d48;
                color: white;
                padding: 2px 6px;
                font-size: 10px;
                border-radius: 3px;
                font-weight: bold;
            }
        `;
        document.head.appendChild(styleEl);
    }
    
    // Mevcut widget'ları işle
    function processExistingWidgets(editor) {
        try {
            // Widget embed elementlerini bul
            const embedComponents = editor.DomComponents.getWrapper().find('.widget-embed');
            
            if (embedComponents && embedComponents.length > 0) {
                console.log("Widget embed bileşenleri bulundu:", embedComponents.length);
                
                embedComponents.forEach(component => {
                    const widgetId = component.getAttributes()['data-tenant-widget-id'] || 
                                    component.getAttributes()['data-widget-id'];
                    
                    if (!widgetId) return;
                    
                    console.log("Widget embed işleniyor:", widgetId);
                    
                    // Widget-embed tipini ekle
                    component.set('type', 'widget-embed');
                    
                    // Özellikleri ayarla
                    component.set('tenant_widget_id', widgetId);
                    component.set('editable', false);
                    component.set('droppable', false);
                    component.set('draggable', true);
                    component.set('highlightable', true);
                    component.set('selectable', true);
                    
                    // Ana container için ID ekle
                    component.addAttributes({
                        'id': `widget-embed-${widgetId}`
                    });
                    
                    // Kritik - HTML içeriğini güncellemeden önce içerik alanını düzenle
                    setTimeout(() => {
                        try {
                            // Komponent view'ının güncel olduğundan emin ol
                            if (!component.view) {
                                console.error(`Widget ${widgetId} için view bulunamadı`);
                                return;
                            }
                            
                            const el = component.view.el;
                            if (!el) {
                                console.error(`Widget ${widgetId} için view elementi bulunamadı`);
                                return;
                            }
                            
                            // Widget tip etiketi ekle
                            if (!el.querySelector('.widget-embed-label')) {
                                const labelEl = document.createElement('div');
                                labelEl.className = 'widget-embed-label';
                                labelEl.innerHTML = '<i class="fa fa-star me-1"></i> Aktif Widget #' + widgetId;
                                el.appendChild(labelEl);
                            }
                            
                            // İçerik alanını bul veya oluştur
                            const uniqueId = 'widget-content-' + widgetId;
                            let container = el.querySelector(`#${uniqueId}`) || el.querySelector(`.widget-content-placeholder`);
                            
                            // Hiç yoksa yeni oluştur
                            if (!container) {
                                container = document.createElement('div');
                                container.id = uniqueId;
                                container.className = 'widget-content-placeholder';
                                container.innerHTML = '<div class="widget-loading" style="text-align:center; padding:20px;"><i class="fa fa-spin fa-spinner"></i> Widget içeriği yükleniyor...</div>';
                                el.appendChild(container);
                            }
                            
                            if (container) {
                                // Widget içeriğini yükle - iframe içinde çalışacak şekilde düzenlenmiş olan yükleme fonksiyonunu çağır
                                if (typeof window.studioLoadWidget === "function") {
                                    setTimeout(() => {
                                        window.studioLoadWidget(widgetId);
                                    }, 500);
                                }
                            }
                        } catch (err) {
                            console.error(`Widget ${widgetId} işleme hatası:`, err);
                        }
                    }, 100);
                });
            }
        } catch (err) {
            console.error("Widget embed işleme genel hatası:", err);
        }
    }
    
    // Widget-embed komponenti ekle
    function registerWidgetEmbedComponent(editor) {
        if (!editor) return;
        
        if (editor.DomComponents.getType('widget-embed')) {
            return; // Zaten tanımlanmış
        }
        
        editor.DomComponents.addType('widget-embed', {
            model: {
                defaults: {
                    tagName: 'div',
                    classes: ['widget-embed'],
                    attributes: {
                        'data-type': 'widget-embed'
                    },
                    traits: [
                        {
                            type: 'number',
                            name: 'tenant_widget_id',
                            label: 'Widget ID',
                            changeProp: true
                        }
                    ],
                    
                    init() {
                        this.on('change:tenant_widget_id', this.updateWidgetId);
                    },
                    
                    updateWidgetId() {
                        const widgetId = this.get('tenant_widget_id');
                        if (widgetId) {
                            this.setAttributes({ 
                                'data-widget-id': widgetId,
                                'data-tenant-widget-id': widgetId,
                                'id': `widget-embed-${widgetId}`
                            });
                            
                            // İçerik alanını sıfırla ve yükleme fonksiyonunu ekle
                            this.components().reset();
                            
                            const placeholderDiv = {
                                tagName: 'div',
                                classes: ['widget-content-placeholder'],
                                attributes: {
                                    'id': `widget-content-${widgetId}`
                                },
                                content: '<div class="widget-loading" style="text-align:center; padding:20px;"><i class="fa fa-spin fa-spinner"></i> Widget içeriği yükleniyor...</div>'
                            };
                            
                            this.append(placeholderDiv);
                            
                            // Widget etiketini güncelle
                            if (this.view && this.view.el) {
                                let labelEl = this.view.el.querySelector('.widget-embed-label');
                                if (!labelEl) {
                                    labelEl = document.createElement('div');
                                    labelEl.className = 'widget-embed-label';
                                    this.view.el.appendChild(labelEl);
                                }
                                
                                labelEl.innerHTML = '<i class="fa fa-star me-1"></i> Aktif Widget #' + widgetId;
                                
                                // Yükleme fonksiyonunu çağır - iframe içine yükleme için güncellenmiş fonksiyon
                                if (typeof window.studioLoadWidget === 'function') {
                                    setTimeout(() => window.studioLoadWidget(widgetId), 100);
                                }
                            }
                        }
                    }
                },
                
                isComponent(el) {
                    if (el.classList && el.classList.contains('widget-embed') || 
                        (el.getAttribute && (
                            el.getAttribute('data-type') === 'widget-embed' ||
                            el.getAttribute('data-tenant-widget-id')
                        ))) {
                        return { type: 'widget-embed' };
                    }
                    return false;
                }
            },
            
            view: {
                events: {
                    'dblclick': 'onDblClick'
                },
                
                onRender() {
                    const el = this.el;
                    if (!el) return;
                    
                    const model = this.model;
                    const widgetId = model.get('tenant_widget_id') || 
                                     model.getAttributes()['data-tenant-widget-id'] || 
                                     model.getAttributes()['data-widget-id'];
                    
                    if (!widgetId) return;
                    
                    // Stil ekle
                    el.style.position = 'relative';
                    el.style.display = 'block';
                    el.style.minHeight = '50px';
                    el.style.border = '2px solid #e11d48';
                    el.style.borderRadius = '6px';
                    el.style.padding = '8px';
                    el.style.margin = '10px 0';
                    el.style.backgroundColor = 'rgba(225, 29, 72, 0.05)';
                    
                    // Etiket ekle
                    let labelEl = el.querySelector('.widget-embed-label');
                    if (!labelEl) {
                        labelEl = document.createElement('div');
                        labelEl.className = 'widget-embed-label';
                        el.appendChild(labelEl);
                    }
                    
                    labelEl.innerHTML = '<i class="fa fa-star me-1"></i> Aktif Widget #' + widgetId;
                    
                    // İçerik alanı yoksa oluştur
                    if (!el.querySelector('.widget-content-placeholder')) {
                        const placeholder = document.createElement('div');
                        placeholder.className = 'widget-content-placeholder';
                        placeholder.id = `widget-content-${widgetId}`;
                        placeholder.innerHTML = '<div class="widget-loading" style="text-align:center; padding:20px;"><i class="fa fa-spin fa-spinner"></i> Widget içeriği yükleniyor...</div>';
                        el.appendChild(placeholder);
                        
                        // Yükleme fonksiyonunu çağır
                        if (typeof window.studioLoadWidget === 'function') {
                            setTimeout(() => window.studioLoadWidget(widgetId), 100);
                        }
                    }
                },
                
                onDblClick() {
                    const model = this.model;
                    const widgetId = model.get('tenant_widget_id') || 
                                     model.getAttributes()['data-tenant-widget-id'] || 
                                     model.getAttributes()['data-widget-id'];
                    
                    if (widgetId) {
                        window.open(`/admin/widgetmanagement/items/${widgetId}`, '_blank');
                    }
                }
            }
        });
    }
    
    // Widget embed elementlerini işle
    function processWidgetEmbeds(editor) {
        try {
            // Widget embed elementlerini bul
            const embedComponents = editor.DomComponents.getWrapper().find('[data-tenant-widget-id]');
            
            if (embedComponents && embedComponents.length > 0) {
                console.log("Widget embed bileşenleri bulundu:", embedComponents.length);
                
                embedComponents.forEach(component => {
                    const widgetId = component.getAttributes()['data-tenant-widget-id'];
                    
                    if (!widgetId) return;
                    
                    // Widget-embed tipini ekle
                    component.set('type', 'widget-embed');
                    component.set('tenant_widget_id', widgetId);
                    
                    // Görünümü güncelle
                    if (component.view && typeof component.view.onRender === 'function') {
                        setTimeout(() => component.view.onRender(), 100);
                        
                        // Widget içeriğini yükle - iframe içine widget içeriğini yükleyecek şekilde güncellenmiş fonksiyonu çağır
                        setTimeout(() => {
                            if (typeof window.studioLoadWidget === 'function') {
                                window.studioLoadWidget(widgetId);
                            }
                        }, 200);
                    }
                });
            }
        } catch (err) {
            console.error("Widget embed işleme genel hatası:", err);
        }
    }
    
    return {
        loadWidgetBlocks: loadWidgetBlocks,
        processExistingWidgets: processExistingWidgets,
        cleanTemplateVariables: cleanTemplateVariables,
        addWidgetStyles: addWidgetStyles,
        registerWidgetEmbedComponent: registerWidgetEmbedComponent,
        processWidgetEmbeds: processWidgetEmbeds
    };
})();