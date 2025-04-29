/**
 * Studio Editor - Widget Yükleme Modülü
 * Widget verilerini yükleme ve dönüştürme
 */

window.StudioWidgetLoader = (function() {
    // Widget bloklarını yükle
    function loadWidgetBlocks(editor) {
        if (!editor || !window.StudioWidgetManager) return;
        
        window.StudioWidgetManager.loadWidgetData().then((widgets) => {
            const widgetList = Object.values(widgets);
            
            widgetList.forEach(widget => {
                const blockId = `widget-${widget.id}`;
                const widgetType = widget.type || 'static';
                const isEditable = widgetType === 'static' || widgetType === 'file';
                
                let widgetHtml = widget.content_html || '';
                if (!widgetHtml) {
                    widgetHtml = `<div class="widget-placeholder">Widget: ${widget.name}</div>`;
                }
                
                // Dynamic ve module tiplerinde değişkenleri temizle
                if (widgetType === 'dynamic') {
                    widgetHtml = cleanTemplateVariables(widgetHtml);
                }
                
                // Widget wrapper oluştur
                let widgetWrapped = '';
                
                if (widgetType === 'dynamic' || widgetType === 'module') {
                    // Kilitli widget - overlay ile
                    widgetWrapped = `
                    <div data-widget-id="${widget.id}" class="gjs-widget-wrapper" data-type="widget" data-widget-type="${widgetType}" data-locked="true" style="position:relative; padding:12px; border-radius:6px; margin:10px 0; pointer-events:none;" ${widget.file_path ? `data-file-path="${widget.file_path}"` : ''}>
                        <div class="widget-overlay" style="position:absolute; top:0; left:0; width:100%; height:100%; z-index:100; background:repeating-linear-gradient(45deg,rgba(0,0,0,0.03),rgba(0,0,0,0.03) 10px,rgba(0,0,0,0.05) 10px,rgba(0,0,0,0.05) 20px); cursor:not-allowed; pointer-events:auto;">
                            <span class="widget-type-badge" style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); padding:5px 10px; border-radius:3px; font-size:12px; font-weight:bold; color:white; background:${widgetType === 'dynamic' ? '#3b82f6' : '#8b5cf6'}; text-transform:uppercase; letter-spacing:1px;">
                                ${widgetType === 'dynamic' ? 'DİNAMİK BİLEŞEN' : 'MODÜL BİLEŞEN'}
                            </span>
                        </div>
                        <div class="widget-content" style="filter:grayscale(20%) blur(0.3px); opacity:0.7;">
                            ${widgetHtml}
                        </div>
                    </div>`;
                } else {
                    // Düzenlenebilir widget
                    widgetWrapped = `<div data-widget-id="${widget.id}" class="gjs-widget-wrapper" data-type="widget" data-widget-type="${widgetType}" ${widget.file_path ? `data-file-path="${widget.file_path}"` : ''}>
                        ${widgetHtml}
                    </div>`;
                }
                
                // Widget tipine göre icon belirle
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
                
                // Blok ekle
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
                        draggable: !(widgetType === 'dynamic' || widgetType === 'module'),
                        editable: isEditable,
                        locked: (widgetType === 'dynamic' || widgetType === 'module')
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
            
            // Stil ekle
            addWidgetStyles();
            
            // Kategorileri güncelle
            if (window.StudioBlocks && typeof window.StudioBlocks.updateBlocksInCategories === 'function') {
                setTimeout(() => {
                    window.StudioBlocks.updateBlocksInCategories(editor);
                }, 300);
            }
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
        `;
        document.head.appendChild(styleEl);
    }
    
    // Mevcut widget'ları işle
    function processExistingWidgets(editor) {
        if (!editor || !window.StudioWidgetManager) return;
        
        window.StudioWidgetManager.loadWidgetData().then(() => {
            const components = editor.DomComponents.getWrapper().find('[data-widget-id]');
            
            if (components && components.length > 0) {
                components.forEach(component => {
                    component.set('type', 'widget');
                    
                    const attrs = component.getAttributes();
                    const widgetId = attrs['data-widget-id'];
                    
                    if (widgetId && window.StudioWidgetManager.getWidgetData) {
                        const widget = window.StudioWidgetManager.getWidgetData(widgetId);
                        if (widget) {
                            component.set('widget_id', widgetId);
                            component.set('widget_type', widget.type || 'static');
                            
                            if (widget.file_path) {
                                component.set('file_path', widget.file_path);
                            }
                            
                            // Widget tipine göre denetleyicileri ayarla
                            const widgetType = widget.type || 'static';
                            
                            if (widgetType === 'dynamic' || widgetType === 'module') {
                                // Dinamik ve module tiplerini tamamen kilitli yap
                                component.set('editable', false);
                                component.set('draggable', false);
                                component.set('droppable', false);
                                component.set('selectable', true); // Sadece bilgi için seçilebilir
                                component.set('highlightable', false);
                                component.set('hoverable', false);
                                component.set('locked', true);
                                
                                // Css değişiklikleri
                                component.setStyle({
                                    'pointer-events': 'none',
                                    'cursor': 'not-allowed'
                                });
                            } else {
                                // Static ve file tiplerini düzenlenebilir yap
                                component.set('editable', true);
                                component.set('draggable', true);
                                component.set('highlightable', true);
                                component.set('selectable', true);
                                component.set('hoverable', true);
                                component.set('locked', false);
                            }
                            
                            // Görünümü güncelle
                            const view = component.view;
                            if (view && typeof view.onRender === 'function') {
                                view.onRender();
                            }
                        }
                    }
                });
            }
            
            // Widget embed elementlerini işle
            processWidgetEmbeds(editor);
        });
    }
                
    // Widget embed elementlerini işle
    function processWidgetEmbeds(editor) {
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
                    component.set('widget_id', widgetId);
                    component.set('tenant_widget_id', widgetId);
                    component.set('editable', false);
                    component.set('droppable', false);
                    component.set('draggable', true);
                    component.set('highlightable', true);
                    component.set('selectable', true);
                    
                    // Stiller ekle
                    component.setStyle({
                        'position': 'relative',
                        'display': 'block',
                        'min-height': '50px',
                        'border': '2px solid #3b82f6',
                        'border-radius': '6px',
                        'padding': '8px',
                        'margin': '10px 0',
                        'background-color': 'rgba(59, 130, 246, 0.05)'
                    });
                    
                    // Ana container için ID ekle
                    component.addAttributes({
                        'id': `widget-embed-${widgetId}`
                    });
                    
                    // Kritik - HTML içeriğini güncellemeden önce içerik alanını düzenle
                    setTimeout(() => {
                        try {
                            const el = component.view ? component.view.el : null;
                            if (!el) {
                                console.error(`Widget ${widgetId} için view elementi bulunamadı`);
                                return;
                            }
                            
                            // Widget tip etiketi ekle
                            if (!el.querySelector('.widget-embed-label')) {
                                const labelEl = document.createElement('div');
                                labelEl.className = 'widget-embed-label';
                                labelEl.style.position = 'absolute';
                                labelEl.style.top = '-10px';
                                labelEl.style.left = '10px';
                                labelEl.style.backgroundColor = '#3b82f6';
                                labelEl.style.color = 'white';
                                labelEl.style.padding = '2px 6px';
                                labelEl.style.fontSize = '10px';
                                labelEl.style.borderRadius = '3px';
                                labelEl.style.fontWeight = 'bold';
                                labelEl.innerHTML = '<i class="fa fa-puzzle-piece me-1"></i> Aktif Widget #' + widgetId;
                                el.appendChild(labelEl);
                            }
                            
                            // İçerik alanını bul veya oluştur
                            const uniqueId = 'widget-content-' + widgetId;
                            let container = document.getElementById(uniqueId);
                            
                            // DOM'da direkt arama
                            if (!container) {
                                container = el.querySelector(`#${uniqueId}`);
                            }
                            
                            // querySelector ile ID arama
                            if (!container) {
                                container = el.querySelector(`[id="${uniqueId}"]`);
                            }
                            
                            // Placeholder sınıfıyla arama
                            if (!container) {
                                container = el.querySelector(`.widget-content-placeholder`);
                                
                                // ID ekle
                                if (container && !container.id) {
                                    container.id = uniqueId;
                                    console.log(`Widget ${widgetId} için container ID eklendi`);
                                }
                            }
                            
                            // Hiç yoksa yeni oluştur
                            if (!container) {
                                console.log(`Widget ${widgetId} için yeni içerik alanı oluşturuluyor`);
                                container = document.createElement('div');
                                container.id = uniqueId;
                                container.className = 'widget-content-placeholder';
                                container.innerHTML = '<div class="widget-loading" style="text-align:center; padding:20px;"><i class="fa fa-spin fa-spinner"></i> Widget içeriği yükleniyor...</div>';
                                el.appendChild(container);
                            }
                            
                            if (container) {
                                console.log("Widget içerik alanı bulundu, içerik yükleniyor:", uniqueId);
                                
                                // Global fonksiyon varsa kullan
                                if (typeof window.studioLoadWidget === "function") {
                                    setTimeout(() => {
                                        // Biraz bekleyerek DOM'un render olmasını sağla
                                        try {
                                            window.studioLoadWidget(widgetId);
                                        } catch(err) {
                                            console.error(`Widget ${widgetId} için fonksiyon çağrısı hatası:`, err);
                                        }
                                    }, 500);
                                } else {
                                    // Sayfanın tamamen yüklenmesini bekleyip içeriği yükle
                                    setTimeout(() => {
                                        fetch(`/admin/widgetmanagement/preview/embed/${widgetId}`)
                                            .then(response => {
                                                console.log(`Widget ${widgetId} yanıtı:`, response.status);
                                                return response.text();
                                            })
                                            .then(html => {
                                                console.log(`Widget ${widgetId} içeriği alındı:`, html.substring(0, 100) + '...');
                                                container.innerHTML = html;
                                                
                                                // Script etiketlerini çalıştır
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
                                            })
                                            .catch(error => {
                                                console.error(`Widget ${widgetId} yükleme hatası:`, error);
                                                container.innerHTML = `<div class="alert alert-danger">Widget yüklenirken hata: ${error.message}</div>`;
                                            });
                                    }, 300);
                                }
                            } else {
                                console.error("Widget içerik alanı bulunamadı:", uniqueId);
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
                            name: 'widget_id',
                            label: 'Widget ID',
                            changeProp: true
                        }
                    ],
                    
                    init() {
                        this.on('change:widget_id', this.updateWidgetId);
                    },
                    
                    updateWidgetId() {
                        const widgetId = this.get('widget_id');
                        if (widgetId) {
                            this.setAttributes({ 
                                'data-widget-id': widgetId,
                                'data-tenant-widget-id': widgetId 
                            });
                            
                            // İçerik alanını sıfırla ve yükleme fonksiyonunu ekle
                            this.components().reset();
                            
                            const placeholderDiv = {
                                tagName: 'div',
                                classes: ['widget-content-placeholder'],
                                content: '<div class="widget-loading" style="text-align:center; padding:20px;"><i class="fa fa-spin fa-spinner"></i> Widget içeriği yükleniyor...</div>'
                            };
                            
                            this.append(placeholderDiv);
                            
                            // Widget etiketini güncelle
                            if (this.view && this.view.el) {
                                let labelEl = this.view.el.querySelector('.widget-embed-label');
                                if (!labelEl) {
                                    labelEl = document.createElement('div');
                                    labelEl.className = 'widget-embed-label';
                                    labelEl.style.position = 'absolute';
                                    labelEl.style.top = '-10px';
                                    labelEl.style.left = '10px';
                                    labelEl.style.backgroundColor = '#3b82f6';
                                    labelEl.style.color = 'white';
                                    labelEl.style.padding = '2px 6px';
                                    labelEl.style.fontSize = '10px';
                                    labelEl.style.borderRadius = '3px';
                                    labelEl.style.fontWeight = 'bold';
                                    this.view.el.appendChild(labelEl);
                                }
                                
                                labelEl.innerHTML = '<i class="fa fa-puzzle-piece me-1"></i> Aktif Widget #' + widgetId;
                                
                                // Yükleme fonksiyonunu çağır
                                if (typeof window.loadTenantWidget === 'function') {
                                    window.loadTenantWidget(widgetId);
                                }
                            }
                        }
                    }
                },
                
                isComponent(el) {
                    if (el.classList && el.classList.contains('widget-embed') || 
                        (el.getAttribute && el.getAttribute('data-type') === 'widget-embed')) {
                        return { type: 'widget-embed' };
                    }
                    return false;
                }
            },
            
            view: {
                onRender() {
                    const el = this.el;
                    if (!el) return;
                    
                    const model = this.model;
                    const widgetId = model.get('widget_id') || 
                                     model.getAttributes()['data-widget-id'] || 
                                     model.getAttributes()['data-tenant-widget-id'];
                    
                    if (!widgetId) return;
                    
                    // Etiket ekle
                    let labelEl = el.querySelector('.widget-embed-label');
                    if (!labelEl) {
                        labelEl = document.createElement('div');
                        labelEl.className = 'widget-embed-label';
                        labelEl.style.position = 'absolute';
                        labelEl.style.top = '-10px';
                        labelEl.style.left = '10px';
                        labelEl.style.backgroundColor = '#3b82f6';
                        labelEl.style.color = 'white';
                        labelEl.style.padding = '2px 6px';
                        labelEl.style.fontSize = '10px';
                        labelEl.style.borderRadius = '3px';
                        labelEl.style.fontWeight = 'bold';
                        el.appendChild(labelEl);
                    }
                    
                    labelEl.innerHTML = '<i class="fa fa-puzzle-piece me-1"></i> Aktif Widget #' + widgetId;
                    
                    // Stil ekle
                    el.style.position = 'relative';
                    el.style.display = 'block';
                    el.style.minHeight = '50px';
                    el.style.border = '2px solid #3b82f6';
                    el.style.borderRadius = '6px';
                    el.style.padding = '8px';
                    el.style.margin = '10px 0';
                    el.style.backgroundColor = 'rgba(59, 130, 246, 0.05)';
                    
                    // İçerik alanı yoksa oluştur
                    if (!el.querySelector('.widget-content-placeholder')) {
                        const placeholder = document.createElement('div');
                        placeholder.className = 'widget-content-placeholder';
                        placeholder.innerHTML = '<div class="widget-loading" style="text-align:center; padding:20px;"><i class="fa fa-spin fa-spinner"></i> Widget içeriği yükleniyor...</div>';
                        el.appendChild(placeholder);
                        
                        // Yükleme fonksiyonunu çağır
                        if (typeof window.loadTenantWidget === 'function') {
                            setTimeout(() => window.loadTenantWidget(widgetId), 100);
                        }
                    }
                }
            }
        });
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