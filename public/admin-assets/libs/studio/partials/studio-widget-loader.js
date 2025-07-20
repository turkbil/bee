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
                                'class': 'studio-widget-container widget-embed',
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
                                    content: '<div class="widget-loading" style="display:none;text-align:center; padding:20px;"><i class="fa fa-spin fa-spinner"></i> Widget içeriği yükleniyor...</div>'
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
        
        // Widget shortcoder ({{widget:id}}) embed'e dönüştür
        html = html.replace(/\{\{widget:(\d+)\}\}/gi, function(match, widgetId) {
            return `<div class="studio-widget-container widget-embed" data-tenant-widget-id="${widgetId}"></div>`;
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
            
            /* Her widget için ayrı overlay */
            .studio-widget-container {
                position: relative !important;
                display: block !important;
                min-height: 50px;
            }
            
            /* Widget overlay temel stilleri */
            .widget-overlay {
                position: absolute !important;
                top: 0 !important;
                left: 0 !important;
                right: 0 !important;
                bottom: 0 !important;
                width: auto !important;
                height: auto !important;
                background: rgba(0, 0, 0, 0.4) !important;
                opacity: 0 !important;
                pointer-events: none !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                transition: opacity 0.3s ease !important;
                z-index: 9999 !important;
                margin: 0 !important;
                padding: 0 !important;
                border-radius: inherit !important;
            }
            
            .studio-widget-container:hover > .widget-overlay {
                opacity: 1 !important;
                pointer-events: none !important;
            }
            
            .widget-action-btn {
                background-color: #3b82f6 !important; 
                color: #fff !important; 
                padding: 6px 12px !important; 
                border-radius: 4px !important; 
                text-decoration: none !important; 
                font-size: 14px !important; 
                transition: background-color 0.2s ease !important;
                z-index: 10000 !important;
                position: relative !important;
                border: none !important;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2) !important;
                pointer-events: auto !important;
            }
            
            .widget-action-btn:hover {
                background-color: #2563eb !important;
            }
            
            .widget-type-badge {
                display: none !important;
            }
            
            .studio-widget-container:hover > .widget-type-badge {
                opacity: 1 !important;
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
            
            /* Widget tiplerine özgü stilleri */
            .widget-embed {
                border: 2px solid #e11d48;
                background-color: rgba(225, 29, 72, 0.05);
            }
            
            .module-widget-container {
                border: 2px solid #8b5cf6;
                background-color: rgba(139, 92, 246, 0.05);
            }
            
            /* Module widget için özel buton renkleri */
            .module-widget-container .widget-action-btn {
                background-color: #8b5cf6 !important;
                color: #fff !important;
            }
            
            .module-widget-container .widget-action-btn:hover {
                background-color: #7c3aed !important;
                box-shadow: 0 4px 8px rgba(139, 92, 246, 0.3) !important;
            }
            
            /* İçerik alanları için ortak stil */
            .widget-content-placeholder {
                width: 100%;
                padding: 0;
                margin: 0;
                pointer-events: none !important;
            }
            
            /* Yükleme göstergesi - varsayılan gizli */
            .widget-loading {
                display: none !important;
                text-align: center;
                padding: 20px;
            }
            
            /* Sadece aktif yükleme durumunda göster */
            .widget-loading.loading-active {
                display: block !important;
            }
        `;
        document.head.appendChild(styleEl);
    }
    
    // Widget embed overlay'i oluşturan fonksiyon
    function createWidgetOverlay(el, widgetId, isStatic = false) {
        // Etiketleri temizle
        const labels = el.querySelectorAll('.widget-label');
        labels.forEach(label => label.remove());
        
        // Mevcut overlay'leri temizle
        const existingOverlays = el.querySelectorAll('.widget-overlay');
        existingOverlays.forEach(overlay => overlay.remove());
        
        // Overlay oluştur
        const overlay = document.createElement('div');
        overlay.className = 'widget-overlay';
        overlay.setAttribute('data-widget-id', widgetId);
        
        // Stillemeleri doğrudan içine ekle
        overlay.style.cssText = 'position:absolute !important;top:0 !important;left:0 !important;width:100% !important;height:100% !important;background:rgba(0,0,0,0.4) !important;opacity:0 !important;display:flex !important;align-items:center !important;justify-content:center !important;transition:opacity 0.3s ease !important;z-index:9999 !important;pointer-events:none !important;';
        
        // Buton oluştur
        const actionBtn = document.createElement('a');
        actionBtn.className = 'widget-action-btn';
        
        // Buton stillerini doğrudan içine ekle
        actionBtn.style.cssText = 'background-color:#3b82f6 !important;color:#fff !important;padding:6px 12px !important;border-radius:4px !important;text-decoration:none !important;font-size:14px !important;transition:background-color 0.2s ease !important;z-index:10000 !important;position:relative !important;pointer-events:auto !important;';
        
        // Dinamik URL oluştur - window.location.origin kullanarak
        const origin = window.location.origin;
        
        if (isStatic) {
            actionBtn.href = `${origin}/admin/widgetmanagement/manage/item/${widgetId}/1`;
        } else {
            actionBtn.href = `${origin}/admin/widgetmanagement/items/${widgetId}`;
        }
        
        actionBtn.target = '_blank';
        actionBtn.innerHTML = '<i class="fa fa-pencil-alt me-1"></i> Düzenle';
        
        // Buton tıklanabilir olmalı
        actionBtn.style.pointerEvents = 'auto';
        
        // Badge oluştur - görünmez yapılıyor
        const badge = document.createElement('span');
        badge.className = 'widget-type-badge';
        badge.style.display = 'none';
        
        // Overlay'e buton ekle
        overlay.appendChild(actionBtn);
        
        // Hover efektlerini JavaScript ile yönet
        el.addEventListener('mouseenter', function() {
            overlay.style.opacity = '1';
        });
        
        el.addEventListener('mouseleave', function() {
            overlay.style.opacity = '0';
        });
        
        // Overlay'i widget container'a ekle
        el.appendChild(overlay);
        el.appendChild(badge);
        
        // MutationObserver kullanarak overlay'in her zaman korunmasını sağla
        const observer = new MutationObserver(function(mutations) {
            if (!el.querySelector('.widget-overlay')) {
                el.appendChild(overlay);
            }
        });
        
        observer.observe(el, { childList: true });
    }
    
    // Widget tipini kontrol et ve kopyalama ayarını belirle
    function getWidgetTypeSettings(widgetId) {
        let widgetData = null;
        if (window.StudioWidgetManager && window.StudioWidgetManager.getWidgetData) {
            widgetData = window.StudioWidgetManager.getWidgetData(widgetId);
        }
        
        const widgetType = widgetData ? (widgetData.type || 'static') : 'static';
        
        // Module, dynamic ve static tipler için kopyalama kapalı
        const copyable = false;
        
        return {
            copyable: copyable,
            widgetType: widgetType
        };
    }
    
    // Widget-embed komponenti ekle
    function registerWidgetEmbedComponent(editor) {
        if (!editor) return;
        
        if (editor.DomComponents.getType('widget-embed')) {
            return;
        }
        
        editor.DomComponents.addType('widget-embed', {
            model: {
                defaults: {
                    tagName: 'div',
                    classes: ['studio-widget-container', 'widget-embed'],
                    draggable: true,
                    droppable: false,
                    editable: false,
                    selectable: true,
                    hoverable: true,
                    resizable: false,
                    copyable: false,
                    removable: true,
                    attributes: {
                        'data-type': 'widget-embed'
                    },
                    toolbar: [
                        {
                            attributes: { class: 'fa fa-trash' },
                            command: 'tlb-delete'
                        }
                    ],
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
                        
                        // Alt komponentleri konfigüre et
                        this.get('components').each(comp => {
                            comp.set({
                                draggable: false,
                                droppable: false,
                                editable: false,
                                selectable: false,
                                hoverable: false,
                                copyable: false,
                                removable: false
                            });
                        });
                        
                        // Widget tipi ayarlarını uygula
                        const widgetId = this.get('tenant_widget_id') || 
                                        this.getAttributes()['data-tenant-widget-id'] || 
                                        this.getAttributes()['data-widget-id'];
                                        
                        if (widgetId) {
                            const settings = getWidgetTypeSettings(widgetId);
                            this.set('copyable', settings.copyable);
                        }
                    },
                    
                    updateWidgetId() {
                        const widgetId = this.get('tenant_widget_id');
                        if (widgetId) {
                            this.setAttributes({
                                'data-widget-id': widgetId,
                                'data-tenant-widget-id': widgetId,
                                'id': `widget-embed-${widgetId}`
                            });
                            
                            // Widget tipi ayarlarını uygula
                            const settings = getWidgetTypeSettings(widgetId);
                            this.set('copyable', settings.copyable);
                            
                            // İçeriği yükle
                            if (window.studioLoadWidget) {
                                window.studioLoadWidget(widgetId);
                            }
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
                    },
                    
                    // Widget-embed toHTML metodu - tamamen boş bir div döndürür
                    toHTML() {
                        const widgetId = this.get('tenant_widget_id') || 
                                    this.getAttributes()['data-tenant-widget-id'] || 
                                    this.getAttributes()['data-widget-id'];
                                    
                        if (widgetId) {
                            return `<div class="widget-embed" data-tenant-widget-id="${widgetId}"></div>`;
                        }
                        
                        return '<div class="widget-embed"></div>';
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
                
                onDblClick() {
                    const model = this.model;
                    const widgetId = model.get('tenant_widget_id') || 
                                    model.getAttributes()['data-tenant-widget-id'] || 
                                    model.getAttributes()['data-widget-id'];
                    
                    if (widgetId) {
                        const origin = window.location.origin;
                        window.open(`${origin}/admin/widgetmanagement/items/${widgetId}`, '_blank');
                    }
                },
                
                onRender() {
                    const model = this.model;
                    const widgetId = model.get('tenant_widget_id') || 
                                   model.getAttributes()['data-tenant-widget-id'] || 
                                   model.getAttributes()['data-widget-id'];
                                   
                    if (!widgetId) return;
                    
                    // Widget tipi ayarlarını uygula
                    const settings = getWidgetTypeSettings(widgetId);
                    model.set('copyable', settings.copyable);
                    
                    // Element pozisyonunu RELATIVE yap (kesinlikle gerekli)
                    const el = this.el;
                    if (el) {
                        el.style.position = 'relative';
                        
                        // Sınıfları ekle
                        el.classList.add('studio-widget-container', 'widget-embed');
                        
                        // Alt komponentleri konfigüre et
                        model.get('components').each(comp => {
                            comp.set({
                                draggable: false,
                                droppable: false,
                                editable: false,
                                selectable: false,
                                hoverable: false,
                                copyable: false,
                                removable: false
                            });
                        });
                        
                        // Mevcut overlay ve etiketleri temizle
                        const labels = el.querySelectorAll('.widget-label, .widget-type-badge');
                        labels.forEach(label => label.remove());
                        
                        const existingOverlays = el.querySelectorAll('.widget-overlay');
                        existingOverlays.forEach(overlay => overlay.remove());
                        
                        // Overlay oluştur
                        const overlay = document.createElement('div');
                        overlay.className = 'widget-overlay';
                        overlay.setAttribute('data-widget-id', widgetId);
                        
                        // Overlay stilini düzelt - en üstte olması için yüksek z-index ve absolute
                        overlay.style.cssText = 'position:absolute !important;top:0 !important;left:0 !important;width:100% !important;height:100% !important;background:rgba(0,0,0,0.4) !important;opacity:0 !important;display:flex !important;align-items:center !important;justify-content:center !important;transition:opacity 0.3s ease !important;z-index:9999 !important;pointer-events:none !important;';
                        
                        // Buton oluştur
                        const actionBtn = document.createElement('a');
                        actionBtn.className = 'widget-action-btn';
                        
                        // Buton stilini düzelt
                        actionBtn.style.cssText = 'background-color:#3b82f6 !important;color:#fff !important;padding:6px 12px !important;border-radius:4px !important;text-decoration:none !important;font-size:14px !important;transition:background-color 0.2s ease !important;z-index:10000 !important;position:relative !important;pointer-events:auto !important;';
                        
                        // Dinamik URL oluştur
                        const origin = window.location.origin;
                        actionBtn.href = `${origin}/admin/widgetmanagement/items/${widgetId}`;
                        actionBtn.target = '_blank';
                        actionBtn.innerHTML = '<i class="fa fa-pencil-alt me-1"></i> Düzenle';
                        
                        // Buton mutlaka tıklanabilir olmalı
                        actionBtn.style.pointerEvents = 'auto';
                        
                        // Hover olaylarını doğrudan JavaScript ile kontrol et
                        el.addEventListener('mouseenter', function() {
                            overlay.style.opacity = '1';
                        });
                        
                        el.addEventListener('mouseleave', function() {
                            overlay.style.opacity = '0';
                        });
                        
                        // Overlay'e buton ekle ve container'a ekle
                        overlay.appendChild(actionBtn);
                        el.appendChild(overlay);
                        
                        // İçerik kontrol et ve yükle
                        const uniqueId = 'widget-content-' + widgetId;
                        let container = el.querySelector(`#${uniqueId}`) || el.querySelector('.widget-content-placeholder');
                        
                        if (!container) {
                            container = document.createElement('div');
                            container.id = uniqueId;
                            container.className = 'widget-content-placeholder';
                            container.innerHTML = '<div class="widget-loading"><i class="fa fa-spin fa-spinner"></i> Widget içeriği yükleniyor...</div>';
                            el.appendChild(container);
                        }
                        
                        // Widget içeriğini yükle
                        if (window.studioLoadWidget) {
                            setTimeout(() => window.studioLoadWidget(widgetId), 100);
                        }
                    }
                }
            }
        });
        
        // Module widget için de aynı ayarları uygula
        editor.DomComponents.addType('module-widget', {
            model: {
                defaults: {
                    tagName: 'div',
                    classes: ['studio-widget-container', 'module-widget-container'],
                    draggable: true,
                    droppable: false,
                    editable: false,
                    selectable: true,
                    hoverable: true,
                    resizable: false,
                    copyable: false,
                    removable: true,
                    attributes: {
                        'data-type': 'module-widget'
                    },
                    toolbar: [
                        {
                            attributes: { class: 'fa fa-trash' },
                            command: 'tlb-delete'
                        }
                    ],
                    traits: [
                        {
                            type: 'number',
                            name: 'widget_module_id',
                            label: 'Module ID',
                            changeProp: true
                        }
                    ],
                    
                    init() {
                        this.on('change:widget_module_id', this.updateModuleId);
                        
                        // Alt komponentleri konfigüre et
                        this.get('components').each(comp => {
                            comp.set({
                                draggable: false,
                                droppable: false,
                                editable: false,
                                selectable: false,
                                hoverable: false,
                                copyable: false,
                                removable: false
                            });
                        });
                    },
                    
                    updateModuleId() {
                        const moduleId = this.get('widget_module_id');
                        if (moduleId) {
                            this.setAttributes({
                                'data-widget-module-id': moduleId,
                                'id': `module-widget-${moduleId}`
                            });
                            
                            setTimeout(() => {
                                if (window.studioLoadModuleWidget) {
                                    window.studioLoadModuleWidget(moduleId);
                                }
                            }, 100);
                        }
                    },
                    
                    toHTML() {
                        const moduleId = this.get('widget_module_id') || this.getAttributes()['data-widget-module-id'];
                        if (moduleId) {
                            return `[[module:${moduleId}]]`;
                        }
                        return this.view.el.outerHTML;
                    }
                },
                
                isComponent(el) {
                    if (el.classList && el.classList.contains('module-widget-container') || 
                        (el.getAttribute && el.getAttribute('data-widget-module-id'))) {
                        return { type: 'module-widget' };
                    }
                    return false;
                }
            },
            
            view: {
                events: {
                    'dblclick': 'onDblClick'
                },
                
                onRender() {
                    const moduleId = this.model.get('widget_module_id') || 
                                     this.model.getAttributes()['data-widget-module-id'];
                    
                    if (moduleId && window.studioLoadModuleWidget) {
                        window.studioLoadModuleWidget(moduleId);
                    }
                    
                    const el = this.el;
                    el.style.position = 'relative';
                    
                    // Overlay (UI geri bildirimi)
                    if (!el.querySelector('.widget-overlay')) {
                        const overlay = document.createElement('div');
                        overlay.className = 'widget-overlay';
                        overlay.style.cssText = 'position:absolute;top:0;left:0;width:100%;height:100%;background:rgba(139,92,246,0.05);pointer-events:none;z-index:10;';
                        el.appendChild(overlay);
                    }
                },
                
                onDblClick() {
                    const model = this.model;
                    const moduleId = model.get('widget_module_id') || 
                                     model.getAttributes()['data-widget-module-id'];
                    
                    if (moduleId) {
                        window.open(`/admin/widgetmanagement/modules/preview/${moduleId}`, '_blank');
                    }
                }
            }
        });
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
                    
                    // Widget tipi ayarlarını uygula
                    const settings = getWidgetTypeSettings(widgetId);
                    component.set({
                        draggable: true,
                        droppable: false,
                        editable: false,
                        selectable: true,
                        hoverable: true,
                        resizable: false,
                        copyable: settings.copyable,
                        removable: true
                    });
                    
                    // Alt komponentlerin droppable özelliklerini kısıtla
                    component.get('components').each(childComp => {
                        childComp.set({
                            draggable: false,
                            droppable: false,
                            editable: false,
                            selectable: false,
                            hoverable: false,
                            copyable: false,
                            removable: false
                        });
                    });
                    
                    // Sınıf adını güncelle
                    if (!component.getAttributes().class.includes('studio-widget-container')) {
                        component.addClass('studio-widget-container');
                    }
                    
                    // Ana container için ID ekle
                    component.addAttributes({
                        'id': `widget-embed-${widgetId}`
                    });
                    
                    // Panelde ilgili blok butonunu pasifleştir
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
                            
                            // Tenant widget container'a sınıf ekleyerek CSS overlay için hazırlık
                            if (!el.classList.contains('studio-widget-container')) {
                                el.classList.add('studio-widget-container');
                            }
                            
                            // Element pozisyonunu kesinlikle relative yap
                            el.style.position = 'relative';
                            
                            // Overlay ekle
                            createWidgetOverlay(el, widgetId, false);
                            
                            // İçerik alanını bul veya oluştur
                            const uniqueId = 'widget-content-' + widgetId;
                            let container = el.querySelector(`#${uniqueId}`) || el.querySelector(`.widget-content-placeholder`);
                            
                            // Hiç yoksa yeni oluştur
                            if (!container) {
                                container = document.createElement('div');
                                container.id = uniqueId;
                                container.className = 'widget-content-placeholder';
                                container.innerHTML = '<div class="widget-loading"><i class="fa fa-spin fa-spinner"></i> Widget içeriği yükleniyor...</div>';
                                el.appendChild(container);
                            }
                            
                            if (container) {
                                // Widget içeriğini yükle - iframe içinde çalışacak şekilde düzenlenmiş olan yükleme fonksiyonunu çağır
                                if (typeof window.studioLoadWidget === "function") {
                                    setTimeout(() => {
                                        window.studioLoadWidget(widgetId);
                                    }, 300);
                                }
                            }
                        } catch (err) {
                            console.error(`Widget ${widgetId} işleme hatası:`, err);
                        }
                    }, 100);
                });
            }
            
            // Module widget elementlerini bul ve işle
            const moduleComponents = editor.DomComponents.getWrapper().find('[data-widget-module-id]');
            
            if (moduleComponents && moduleComponents.length > 0) {
                console.log("Module widget bileşenleri bulundu:", moduleComponents.length);
                
                moduleComponents.forEach(component => {
                    const moduleId = component.getAttributes()['data-widget-module-id'];
                    
                    if (!moduleId) return;
                    
                    console.log("Module widget işleniyor:", moduleId);
                    
                    // Module-widget tipini ekle
                    component.set('type', 'module-widget');
                    component.set({
                        draggable: true,
                        droppable: false,
                        editable: false,
                        selectable: true,
                        hoverable: true,
                        resizable: false,
                        copyable: false,
                        removable: true
                    });
                    
                    // Alt komponentlerin droppable özelliklerini kısıtla
                    component.get('components').each(childComp => {
                        childComp.set({
                            draggable: false,
                            droppable: false,
                            editable: false,
                            selectable: false,
                            hoverable: false,
                            copyable: false,
                            removable: false
                        });
                    });
                    
                    // Sınıf adını güncelle
                    if (!component.getAttributes().class.includes('studio-widget-container')) {
                        component.addClass('studio-widget-container');
                    }
                    
                    // Ana container için ID ekle
                    component.addAttributes({
                        'id': `module-widget-${moduleId}`
                    });
                    
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
                    
                    // Etiketleri temizle ve overlay ekle
                    setTimeout(() => {
                        try {
                            const el = component.view.el;
                            if (!el) return;
                            
                            // Element pozisyonunu relative yap
                            el.style.position = 'relative';
                            
                            // Etiketleri temizle
                            const labels = el.querySelectorAll('.widget-label');
                            labels.forEach(label => label.remove());
                            
                            // Mevcut overlay'leri temizle
                            const existingOverlays = el.querySelectorAll('.widget-overlay');
                            existingOverlays.forEach(overlay => overlay.remove());
                            
                            // Overlay oluştur
                            const overlay = document.createElement('div');
                            overlay.className = 'widget-overlay';
                            
                            // Stili doğrudan içine ekle
                            overlay.style.cssText = 'position:absolute !important;top:0 !important;left:0 !important;width:100% !important;height:100% !important;background:rgba(139,92,246,0.4) !important;opacity:0 !important;display:flex !important;align-items:center !important;justify-content:center !important;transition:opacity 0.3s ease !important;z-index:9999 !important;pointer-events:none !important;';
                            
                            // Buton oluştur
                            const actionBtn = document.createElement('a');
                            actionBtn.className = 'widget-action-btn';
                            
                            // Buton stilini doğrudan içine ekle
                            actionBtn.style.cssText = 'background-color:#8b5cf6 !important;color:#fff !important;padding:6px 12px !important;border-radius:4px !important;text-decoration:none !important;font-size:14px !important;transition:background-color 0.2s ease !important;z-index:10000 !important;position:relative !important;pointer-events:auto !important;';
                            
                            // Dinamik URL oluştur
                            const origin = window.location.origin;
                            actionBtn.href = `${origin}/admin/widgetmanagement/modules/preview/${moduleId}`;
                            actionBtn.target = '_blank';
                            actionBtn.innerHTML = '<i class="fa fa-eye me-1"></i> Önizle';
                            
                            // Buton tıklanabilir olmalı
                            actionBtn.style.pointerEvents = 'auto';
                            
                            // Hover efektlerini JavaScript ile yönet
                            el.addEventListener('mouseenter', function() {
                                overlay.style.opacity = '1';
                            });
                            
                            el.addEventListener('mouseleave', function() {
                                overlay.style.opacity = '0';
                            });
                            
                            // Overlay'e buton ekle
                            overlay.appendChild(actionBtn);
                            
                            // Overlay'i widget container'a ekle
                            el.appendChild(overlay);
                            
                            // MutationObserver kullanarak overlay'in her zaman korunmasını sağla
                            const observer = new MutationObserver(function(mutations) {
                                if (!el.querySelector('.widget-overlay')) {
                                    el.appendChild(overlay);
                                }
                            });
                            
                            observer.observe(el, { childList: true });
                        } catch (err) {
                            console.error(`Module widget ${moduleId} etiket hatası:`, err);
                        }
                    }, 100);
                    
                    // Module içeriğini yükle
                    setTimeout(() => {
                        if (typeof window.studioLoadModuleWidget === "function") {
                            window.studioLoadModuleWidget(moduleId);
                        }
                    }, 300);
                });
            }
            
            // Module widget shortcode'larını bul ve işle [[module:XX]]
            const wrapper = editor.DomComponents.getWrapper();
            const htmlContent = wrapper.get('content');
            
            if (typeof htmlContent === 'string') {
                // Module shortcode'ları için regex
                const moduleRegex = /\[\[module:(\d+)\]\]/g;
                let match;
                
                while ((match = moduleRegex.exec(htmlContent)) !== null) {
                    const moduleId = match[1];
                    console.log("Module shortcode bulundu:", moduleId);
                    
                    // Shortcode'u module widget componenti ile değiştir
                    const moduleWidget = editor.DomComponents.addComponent({
                        type: 'module-widget',
                        widget_module_id: moduleId,
                        draggable: true,
                        droppable: false,
                        editable: false,
                        selectable: true,
                        hoverable: true,
                        resizable: false,
                        copyable: false,
                        removable: true,
                        attributes: {
                            'data-widget-module-id': moduleId,
                            'id': `module-widget-${moduleId}`,
                            'class': 'studio-widget-container module-widget-container'
                        },
                        content: `<div class="widget-content-placeholder" id="module-content-${moduleId}"><div class="widget-loading"><i class="fa fa-spin fa-spinner"></i> Module yükleniyor...</div></div>`
                    });
                    
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
                    
                    // Module içeriğini yükle
                    setTimeout(() => {
                        if (window.studioLoadModuleWidget) {
                            window.studioLoadModuleWidget(moduleId);
                        }
                    }, 300);
                }
            }
        } catch (err) {
            console.error("Widget embed işleme genel hatası:", err);
        }
    }

    // Widget embed elementlerini işle
    function processWidgetEmbeds(editor) {
        try {
            if (!editor || !editor.DomComponents) {
                console.error("Editor veya DomComponents bulunamadı");
                return;
            }
            
            // Widget embed elementlerini bul
            const embedComponents = editor.DomComponents.getWrapper().find('[data-tenant-widget-id]');
            
            if (embedComponents && embedComponents.length > 0) {
                console.log("Widget embed bileşenleri bulundu:", embedComponents.length);
                
                // Eş zamanlı işlemleri kontrol etmek için sayaç
                let processedCount = 0;
                
                embedComponents.forEach((component, index) => {
                    // Zamanlama hatalarını önlemek için kısa gecikmeler ekle
                    setTimeout(() => {
                        try {
                            const widgetId = component.getAttributes()['data-tenant-widget-id'];
                            
                            if (!widgetId) return;
                            
                            console.log(`Widget embed işleniyor: ${widgetId} (${index + 1}/${embedComponents.length})`);
                            
                            // Widget-embed tipini ekle
                            component.set('type', 'widget-embed');
                            component.set('tenant_widget_id', widgetId);
                            
                            // Widget tipi ayarlarını uygula
                            const settings = getWidgetTypeSettings(widgetId);
                            component.set({
                                draggable: true,
                                droppable: false,
                                editable: false,
                                selectable: true,
                                hoverable: true,
                                resizable: false,
                                copyable: settings.copyable,
                                removable: true
                            });
                            
                            // Alt komponentlerin droppable özelliklerini kısıtla
                            component.get('components').each(childComp => {
                                childComp.set({
                                    draggable: false,
                                    droppable: false,
                                    editable: false,
                                    selectable: false,
                                    hoverable: false,
                                    copyable: false,
                                    removable: false
                                });
                            });
                            
                            // Sınıf adını güncelle
                            if (!component.getAttributes().class.includes('studio-widget-container')) {
                                component.addClass('studio-widget-container');
                            }
                            
                            // Önemli: DOM elemanı üzerinde doğrudan işlem yap
                            try {
                                // Elementlere erişmeye çalış
                                const el = component.view.el;
                                if (!el) {
                                    console.warn("Widget element bulunamadı:", widgetId);
                                    return;
                                }
                                
                                // Elementin pozisyonunu kesinlikle RELATIVE yap (zorunlu)
                                el.style.position = 'relative';
                                
                                // Tüm sınıfları ekle
                                el.classList.add('studio-widget-container', 'widget-embed');
                                
                                // Tüm mevcut overlay ve etiketleri temizle
                                const labels = el.querySelectorAll('.widget-label, .widget-type-badge');
                                labels.forEach(label => label.remove());
                                
                                const existingOverlays = el.querySelectorAll('.widget-overlay');
                                existingOverlays.forEach(overlay => overlay.remove());
                                
                                // Yeni overlay oluştur
                                const overlay = document.createElement('div');
                                overlay.className = 'widget-overlay';
                                overlay.setAttribute('data-widget-id', widgetId);
                                
                                // Z-index ve stillemeleri doğrudan ekle
                                overlay.style.cssText = 'position:absolute !important;top:0 !important;left:0 !important;width:100% !important;height:100% !important;background:rgba(0,0,0,0.4) !important;opacity:0 !important;display:flex !important;align-items:center !important;justify-content:center !important;transition:opacity 0.3s ease !important;z-index:9999 !important;pointer-events:none !important;';
                                
                                // Buton oluştur
                                const actionBtn = document.createElement('a');
                                actionBtn.className = 'widget-action-btn';
                                
                                // Buton stillerini direkt içine ekle
                                actionBtn.style.cssText = 'background-color:#3b82f6 !important;color:#fff !important;padding:6px 12px !important;border-radius:4px !important;text-decoration:none !important;font-size:14px !important;transition:background-color 0.2s ease !important;z-index:10000 !important;position:relative !important;pointer-events:auto !important;';
                                
                                // Dinamik URL oluştur
                                const origin = window.location.origin;
                                const isStatic = component.getAttributes()['data-widget-id'] && !component.getAttributes()['data-tenant-widget-id'];
                                
                                if (isStatic) {
                                    actionBtn.href = `${origin}/admin/widgetmanagement/manage/item/${widgetId}/1`;
                                } else {
                                    actionBtn.href = `${origin}/admin/widgetmanagement/items/${widgetId}`;
                                }
                                
                                actionBtn.target = '_blank';
                                actionBtn.innerHTML = '<i class="fa fa-pencil-alt me-1"></i> Düzenle';
                                
                                // Buton her zaman tıklanabilir olmalı
                                actionBtn.style.pointerEvents = 'auto';
                                
                                // Tıklama ve hover olaylarını JavaScript ile yönet
                                el.addEventListener('mouseenter', function() {
                                    overlay.style.opacity = '1';
                                });
                                
                                el.addEventListener('mouseleave', function() {
                                    overlay.style.opacity = '0';
                                });
                                
                                // Overlay'e buton ekle
                                overlay.appendChild(actionBtn);
                                
                                // Overlay'i widget container'a ekle
                                el.appendChild(overlay);
                                
                                // Önemli: MutationObserver kullanarak DOM değişikliklerini izle ve overlay'i koru
                                const observer = new MutationObserver(function(mutations) {
                                    // Eğer overlay kaldırıldıysa, tekrar ekle
                                    if (!el.querySelector('.widget-overlay')) {
                                        el.appendChild(overlay);
                                    }
                                });
                                
                                // DOM değişikliklerini izle
                                observer.observe(el, { childList: true });
                            } catch (err) {
                                console.error(`Widget ${widgetId} overlay işleme hatası:`, err);
                            }
                            
                            // Blok butonunu pasifleştir
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
                            
                            // Widget içeriğini yükle - belirli bir gecikmeyle uygula
                            // (Çok sayıda widget varsa, performans sorunlarını önlemek için)
                            const loadDelay = Math.min(100 * index, 1000); // En fazla 1 sn gecikme
                            
                            setTimeout(() => {
                                try {
                                    if (window.studioLoadWidget) {
                                        window.studioLoadWidget(widgetId);
                                    }
                                    
                                    // İşleme sayacını artır
                                    processedCount++;
                                    
                                    // Tüm widgetlar işlendikten sonra bir olay tetikle
                                    if (processedCount >= embedComponents.length) {
                                        document.dispatchEvent(new CustomEvent('widgets:processed', {
                                            detail: { count: processedCount }
                                        }));
                                    }
                                } catch (loadErr) {
                                    console.error(`Widget ${widgetId} yükleme tetikleme hatası:`, loadErr);
                                }
                            }, loadDelay);
                        } catch (compError) {
                            console.error(`Widget bileşen işleme hatası #${index}:`, compError);
                        }
                    }, 20 * index); // Her bir widget için kısa bir gecikme ekle
                });
            }
            
            // Module widget'ları için benzer işlemler
            const moduleComponents = editor.DomComponents.getWrapper().find('[data-widget-module-id]');
            
            if (moduleComponents && moduleComponents.length > 0) {
                console.log("Module widget bileşenleri bulundu:", moduleComponents.length);
                
                moduleComponents.forEach((component, index) => {
                    // Zamanlama hatalarını önlemek için kısa gecikmeler ekle
                    setTimeout(() => {
                        try {
                            const moduleId = component.getAttributes()['data-widget-module-id'];
                            
                            if (!moduleId) return;
                            
                            console.log(`Module widget işleniyor: ${moduleId} (${index + 1}/${moduleComponents.length})`);
                            
                            // Module-widget tipini ekle
                            component.set('type', 'module-widget');
                            component.set('widget_module_id', moduleId);
                            component.set({
                                draggable: true,
                                droppable: false,
                                editable: false,
                                selectable: true,
                                hoverable: true,
                                resizable: false,
                                copyable: false,
                                removable: true
                            });
                            
                            // Alt komponentlerin droppable özelliklerini kısıtla
                            component.get('components').each(childComp => {
                                childComp.set({
                                    draggable: false,
                                    droppable: false,
                                    editable: false,
                                    selectable: false,
                                    hoverable: false,
                                    copyable: false,
                                    removable: false
                                });
                            });
                            
                            // Sınıf adını güncelle
                            if (!component.getAttributes().class.includes('studio-widget-container')) {
                                component.addClass('studio-widget-container');
                            }
                            
                            // DOM elemanı üzerinde işlem yap
                            try {
                                const el = component.view.el;
                                if (!el) {
                                    console.warn("Module widget element bulunamadı:", moduleId);
                                    return;
                                }
                                
                                // Elementin pozisyonunu kesinlikle RELATIVE yap (zorunlu)
                                el.style.position = 'relative';
                                
                                // Tüm sınıfları ekle
                                el.classList.add('studio-widget-container', 'module-widget-container');
                                
                                // Tüm mevcut overlay ve etiketleri temizle
                                const labels = el.querySelectorAll('.widget-label, .widget-type-badge');
                                labels.forEach(label => label.remove());
                                
                                const existingOverlays = el.querySelectorAll('.widget-overlay');
                                existingOverlays.forEach(overlay => overlay.remove());
                                
                                // Yeni overlay oluştur
                                const overlay = document.createElement('div');
                                overlay.className = 'widget-overlay';
                                overlay.setAttribute('data-module-id', moduleId);
                                
                                // Z-index ve stillemeleri doğrudan ekle (modül widget için farklı arka plan)
                                overlay.style.cssText = 'position:absolute !important;top:0 !important;left:0 !important;width:100% !important;height:100% !important;background:rgba(139,92,246,0.4) !important;opacity:0 !important;display:flex !important;align-items:center !important;justify-content:center !important;transition:opacity 0.3s ease !important;z-index:9999 !important;pointer-events:none !important;';
                                
                                // Buton oluştur
                                const actionBtn = document.createElement('a');
                                actionBtn.className = 'widget-action-btn';
                                
                                // Buton stillerini direkt içine ekle (modül widget için farklı renkler)
                                actionBtn.style.cssText = 'background-color:#8b5cf6 !important;color:#fff !important;padding:6px 12px !important;border-radius:4px !important;text-decoration:none !important;font-size:14px !important;transition:background-color 0.2s ease !important;z-index:10000 !important;position:relative !important;pointer-events:auto !important;';
                                
                                // Dinamik URL oluştur
                                const origin = window.location.origin;
                                const isStatic = component.getAttributes()['data-widget-static'] === 'true';
                                
                                if (isStatic) {
                                    actionBtn.href = `${origin}/admin/widgetmanagement/manage/module/${moduleId}/1`;
                                    actionBtn.innerHTML = '<i class="fa fa-pencil-alt me-1"></i> Düzenle';
                                } else {
                                    actionBtn.href = `${origin}/admin/widgetmanagement/modules/preview/${moduleId}`;
                                    actionBtn.innerHTML = '<i class="fa fa-eye me-1"></i> Önizle';
                                }
                                
                                actionBtn.target = '_blank';
                                
                                // Buton her zaman tıklanabilir olmalı
                                actionBtn.style.pointerEvents = 'auto';
                                
                                // Tıklama ve hover olaylarını JavaScript ile yönet
                                el.addEventListener('mouseenter', function() {
                                    overlay.style.opacity = '1';
                                });
                                
                                el.addEventListener('mouseleave', function() {
                                    overlay.style.opacity = '0';
                                });
                                
                                // Overlay'e buton ekle
                                overlay.appendChild(actionBtn);
                                
                                // Overlay'i widget container'a ekle
                                el.appendChild(overlay);
                                
                                // MutationObserver kullanarak DOM değişikliklerini izle ve overlay'i koru
                                const observer = new MutationObserver(function(mutations) {
                                    // Eğer overlay kaldırıldıysa, tekrar ekle
                                    if (!el.querySelector('.widget-overlay')) {
                                        el.appendChild(overlay);
                                    }
                                });
                                
                                // DOM değişikliklerini izle
                                observer.observe(el, { childList: true });
                            } catch (err) {
                                console.error(`Module widget ${moduleId} overlay işleme hatası:`, err);
                            }
                            
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
                            
                            // Module içeriğini yükle - belirli bir gecikmeyle
                            const loadDelay = Math.min(100 * index, 1000); // En fazla 1 sn gecikme
                            
                            setTimeout(() => {
                                if (window.studioLoadModuleWidget) {
                                    window.studioLoadModuleWidget(moduleId);
                                }
                            }, loadDelay);
                        } catch (compError) {
                            console.error(`Module bileşen işleme hatası #${index}:`, compError);
                        }
                    }, 20 * index); // Her bir modül için kısa bir gecikme ekle
                });
            }
        } catch (err) {
            console.error("Widget embed işleme genel hatası:", err);
        }
    }
    
    // Tüm loading göstergelerini gizle
    function hideAllLoadingIndicators() {
        // Ana belgede
        document.querySelectorAll('.widget-loading').forEach(loading => {
            loading.classList.remove('loading-active');
        });
        
        // iframe'lerde
        document.querySelectorAll('iframe').forEach(iframe => {
            try {
                const doc = iframe.contentDocument || iframe.contentWindow.document;
                doc.querySelectorAll('.widget-loading').forEach(loading => {
                    loading.classList.remove('loading-active');
                });
            } catch(e) {}
        });
    }
            
    // Widget içeriğini yükle
    window.studioLoadWidget = function(widgetId) {
        if (!widgetId) {
            console.error('Widget ID belirtilmedi');
            return;
        }
        
        window._loadedWidgets = window._loadedWidgets || new Set();
        
        // Zaten yüklenmekte olan widgetları izle
        window._loadingWidgets = window._loadingWidgets || new Set();
        
        // Aynı widget için çoklu istek varsa, yalnızca bir kez yükle
        if (window._loadingWidgets.has(widgetId)) {
            console.log(`Widget ${widgetId} zaten yükleniyor, işlem atlanıyor...`);
            return;
        }
        
        // Yükleme işlemi başladı olarak işaretle
        window._loadingWidgets.add(widgetId);
        
        console.log(`Widget ${widgetId} içeriği yükleniyor...`);
        
        // Önce tüm loading göstergelerini gizle
        hideAllLoadingIndicators();
        
        // Sadece belirli widget ID'sine sahip container'larda loading göster
        const showLoadingForSpecificWidget = function(targetWidgetId) {
            // Ana belge içinde sadece bu widget'ın container'ını bul
            const specificWidgetContainer = document.getElementById(`widget-content-${targetWidgetId}`);
            if (specificWidgetContainer) {
                const loading = specificWidgetContainer.querySelector('.widget-loading');
                if (loading) {
                    loading.classList.add('loading-active');
                }
            }
            
            // iframe'lerde sadece bu widget ID'sine sahip container'ları bul
            try {
                document.querySelectorAll('iframe').forEach(iframe => {
                    try {
                        const doc = iframe.contentDocument || iframe.contentWindow.document;
                        const iframeSpecificContainer = doc.getElementById(`widget-content-${targetWidgetId}`);
                        if (iframeSpecificContainer) {
                            const loading = iframeSpecificContainer.querySelector('.widget-loading');
                            if (loading) {
                                loading.classList.add('loading-active');
                            }
                        }
                    } catch(e) {}
                });
            } catch(e) {}
        };
        
        // Yükleme göstergesini göster
        showLoadingForSpecificWidget(widgetId);
        
        // İstek yapılma zamanını kaydet (zaman aşımı kontrolü için)
        const requestTime = Date.now();
        
        // Widget içeriğini yükle (artırılmış hata yakalama ile)
        fetch(`/admin/widgetmanagement/preview/embed/json/${widgetId}`, { 
            credentials: 'same-origin',
            cache: 'no-cache',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            console.log(`Widget ${widgetId} JSON yanıtı alındı:`, response.status);
            
            // Yanıt başarısız mı kontrol et
            if (!response.ok) {
                throw new Error(`Widget JSON yanıtı başarısız: ${response.status}`);
            }
            
            // JSON olarak parse edin (hata yakalama ile)
            return response.json().catch(e => {
                throw new Error(`Widget yanıtı geçerli JSON değil: ${e.message}`);
            });
        })
        .then(data => {
            // Yükleme için işaretlendiği listeden çıkar
            window._loadingWidgets.delete(widgetId);
            
            // Başarıyla yüklendi olarak işaretle
            window._loadedWidgets.add(widgetId);
            
            // Container bul (main doc veya iframe)
            const findEmbedElements = function(targetWidgetId) {
                const elements = {
                    embedEl: null,
                    placeholder: null,
                    targetDocument: document
                };
                
                // SADECE bu widget için spesifik container'ı bul
                elements.placeholder = document.getElementById(`widget-content-${targetWidgetId}`);
                elements.embedEl = document.querySelector(`[data-tenant-widget-id="${targetWidgetId}"]`);
                
                // Ana belgede bulunamadıysa, iframe'leri kontrol et
                if (!elements.embedEl || !elements.placeholder) {
                    Array.from(document.querySelectorAll('iframe')).forEach(fr => {
                        if (!elements.embedEl || !elements.placeholder) {
                            try {
                                const doc = fr.contentDocument || fr.contentWindow.document;
                                
                                if (!elements.embedEl) {
                                    const el = doc.querySelector(`[data-tenant-widget-id="${targetWidgetId}"]`);
                                    if (el) {
                                        elements.embedEl = el;
                                        elements.targetDocument = doc;
                                    }
                                }
                                
                                if (!elements.placeholder) {
                                    const placeholder = doc.getElementById(`widget-content-${targetWidgetId}`);
                                    if (placeholder) {
                                        elements.placeholder = placeholder;
                                        elements.targetDocument = doc;
                                    }
                                }
                            } catch(e) {
                                console.warn(`iframe için widget elementi erişilemedi (CORS):`, e);
                            }
                        }
                    });
                }
                
                return elements;
            };
            
            const elements = findEmbedElements(widgetId);
            const {embedEl, placeholder, targetDocument} = elements;
            
            if (!embedEl && !placeholder) {
                console.error(`Widget embed elementi veya placeholder bulunamadı: ${widgetId}`);
                window._loadingWidgets.delete(widgetId);
                return;
            }
            
            try {
                // HTML render - Handlebars ile şablon işleme
                let html = data.content_html || '';
                if (data.useHandlebars && window.Handlebars) {
                    try {
                        const template = Handlebars.compile(html);
                        html = template(data.context || {});
                    } catch(err) {
                        console.error(`Handlebars şablonu derleme hatası:`, err);
                        // Hata durumunda orijinal HTML'yi kullan
                    }
                }
                
                // HTML'i SADECE bu widget'ın container'ına yaz
                if (placeholder) {
                    // Yükleme göstergesini kaldırma
                    const loadingEl = placeholder.querySelector('.widget-loading');
                    if (loadingEl) {
                        loadingEl.classList.remove('loading-active');
                    }
                    
                    // XSS koruması ile güvenli HTML render
                    if (window.StudioSecurity && window.StudioSecurity.safeInnerHTML) {
                        window.StudioSecurity.safeInnerHTML(placeholder, html);
                    } else {
                        // Fallback: basit sanitization
                        const sanitizedHtml = html.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '');
                        placeholder.innerHTML = sanitizedHtml;
                    }
                } else if (embedEl) {
                    // İçerik alanı oluştur ve ekle
                    const contentContainer = targetDocument.createElement('div');
                    contentContainer.className = 'widget-content-placeholder';
                    contentContainer.id = `widget-content-${widgetId}`;
                    
                    // XSS koruması ile güvenli HTML render
                    if (window.StudioSecurity && window.StudioSecurity.safeInnerHTML) {
                        window.StudioSecurity.safeInnerHTML(contentContainer, html);
                    } else {
                        // Fallback: basit sanitization
                        const sanitizedHtml = html.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '');
                        contentContainer.innerHTML = sanitizedHtml;
                    }
                    
                    // Mevcut içeriği temizle ve yeni içeriği ekle
                    const existingPlaceholder = embedEl.querySelector('.widget-content-placeholder');
                    if (existingPlaceholder) {
                        if (window.StudioSecurity && window.StudioSecurity.safeInnerHTML) {
                            window.StudioSecurity.safeInnerHTML(existingPlaceholder, html);
                        } else {
                            const sanitizedHtml = html.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '');
                            existingPlaceholder.innerHTML = sanitizedHtml;
                        }
                    } else {
                        embedEl.appendChild(contentContainer);
                    }
                }
                
                // CSS enjeksiyon - tekrarları önlemek için stil ID'si kullan
                if (data.content_css) {
                    let css = data.content_css;
                    const styleId = `widget-style-${widgetId}`;
                    let styleEl = targetDocument.getElementById(styleId);
                    
                    // Stil elemanı yoksa oluştur
                    if (!styleEl) {
                        styleEl = targetDocument.createElement('style');
                        styleEl.id = styleId;
                        targetDocument.head.appendChild(styleEl);
                    }
                    
                    // Handlebars ile CSS şablonu işle
                    if (data.useHandlebars && window.Handlebars) {
                        try {
                            const cssTpl = Handlebars.compile(css);
                            css = cssTpl(data.context || {});
                        } catch(err) {
                            console.error(`CSS Handlebars derlerken hata:`, err);
                        }
                    }
                    
                    // CSS içeriğini ayarla
                    styleEl.textContent = css;
                }
                
                // JS enjeksiyon - güvenlik kontrolü ile
                if (data.content_js) {
                    let js = data.content_js;
                    const scriptId = `widget-script-${widgetId}`;
                    let scriptEl = targetDocument.getElementById(scriptId);
                    
                    // JavaScript güvenlik kontrolü
                    if (window.StudioSecurity && window.StudioSecurity.validateJavaScript) {
                        if (!window.StudioSecurity.validateJavaScript(js)) {
                            console.error(`Widget ${widgetId} JavaScript kodu güvenlik kontrolünden geçemedi`);
                            window.StudioSecurity.logSecurityEvent('js_injection_blocked', `Widget ${widgetId} JavaScript blocked`, { widgetId, js });
                            return;
                        }
                    }
                    
                    // Handlebars ile JS şablonu işle
                    if (data.useHandlebars && window.Handlebars) {
                        try {
                            const jsTpl = Handlebars.compile(js);
                            js = jsTpl(data.context || {});
                        } catch(err) {
                            console.error(`JS Handlebars derlerken hata:`, err);
                        }
                    }
                    
                    // İkinci güvenlik kontrolü (template işleme sonrası)
                    if (window.StudioSecurity && window.StudioSecurity.validateJavaScript) {
                        if (!window.StudioSecurity.validateJavaScript(js)) {
                            console.error(`Widget ${widgetId} JavaScript kodu template işleme sonrası güvenlik kontrolünden geçemedi`);
                            window.StudioSecurity.logSecurityEvent('js_injection_blocked_post_template', `Widget ${widgetId} JavaScript blocked after template`, { widgetId, js });
                            return;
                        }
                    }
                    
                    // Eğer Handlebars yardımcıları içeriyorsa ayrı işle
                    if (js.includes('Handlebars.registerHelper')) {
                        if (!window._handlebarsHelpersInjected) {
                            const helperScript = targetDocument.createElement('script');
                            helperScript.type = 'text/javascript';
                            helperScript.textContent = js;
                            const container = targetDocument.querySelector('footer') || targetDocument.body;
                            container.appendChild(helperScript);
                            window._handlebarsHelpersInjected = true;
                        }
                    } else {
                        // Güvenliği artırmak için kapsama (IIFE) içinde çalıştır
                        js = `(function(widgetId) {\n// Widget #${widgetId} script\n${js}\n})(${widgetId});`;
                        
                        // Script elemanı yoksa oluştur, varsa güncelle
                        if (!scriptEl) {
                            scriptEl = targetDocument.createElement('script');
                            scriptEl.id = scriptId;
                            scriptEl.type = 'text/javascript';
                            scriptEl.textContent = js;
                            targetDocument.body.appendChild(scriptEl);
                        } else {
                            scriptEl.textContent = js;
                        }
                    }
                }
                
                console.log(`Widget ${widgetId} başarıyla yüklendi`);
                
                // Widget yüklendikten sonra olayı tetikle
                const widgetLoadedEvent = new CustomEvent('widget:loaded', {
                    detail: { widgetId: widgetId, timestamp: Date.now() }
                });
                document.dispatchEvent(widgetLoadedEvent);
                
                // İçerik kontrolü yap - boş içerik varsa tekrar dene
                if (placeholder && placeholder.innerHTML.trim() === '') {
                    console.warn(`Widget ${widgetId} içeriği boş, tekrar deneniyor...`);
                    setTimeout(() => {
                        if (!window._loadedWidgets.has(widgetId)) {
                            window.studioLoadWidget(widgetId);
                        }
                    }, 1000);
                }
            } catch (renderErr) {
                console.error(`Widget ${widgetId} render hatası:`, renderErr);
                window._loadingWidgets.delete(widgetId);
                
                // Hata olduğunda basit bir hata mesajı göster
                showWidgetError(widgetId, `Render hatası: ${renderErr.message}`);
            }
        })
        .catch(error => {
            console.error(`Widget ${widgetId} yükleme hatası:`, error);
            window._loadingWidgets.delete(widgetId);
            
            // Hata durumunda basit bir hata mesajı göster
            showWidgetError(widgetId, `Yükleme hatası: ${error.message}`);
            
            // Zaman aşımı veya ağ hatası durumunda tekrar dene
            if (error.name === 'TypeError' || error.message.includes('timeout') || error.message.includes('network')) {
                setTimeout(() => {
                    if (!window._loadedWidgets.has(widgetId)) {
                        console.log(`Widget ${widgetId} için tekrar deneme yapılıyor...`);
                        window.studioLoadWidget(widgetId);
                    }
                }, 2000);
            }
        });
        
        // Widget için hata mesajı göster
        function showWidgetError(targetWidgetId, errorMessage) {
            // SADECE bu widget için spesifik container'ı bul
            let placeholder = document.getElementById(`widget-content-${targetWidgetId}`);
            let embedEl = document.querySelector(`[data-tenant-widget-id="${targetWidgetId}"]`);
            let targetDocument = document;
            
            if (!placeholder && !embedEl) {
                Array.from(document.querySelectorAll('iframe')).forEach(fr => {
                    if (!placeholder && !embedEl) {
                        try {
                            const doc = fr.contentDocument || fr.contentWindow.document;
                            
                            if (!placeholder) {
                                placeholder = doc.getElementById(`widget-content-${targetWidgetId}`);
                            }
                            
                            if (!embedEl) {
                                embedEl = doc.querySelector(`[data-tenant-widget-id="${targetWidgetId}"]`);
                            }
                            
                            if (placeholder || embedEl) {
                                targetDocument = doc;
                            }
                        } catch(e) {}
                    }
                });
            }
            
            // Hata mesajını göster
            if (placeholder) {
                placeholder.innerHTML = `<div class="alert alert-danger" style="margin:10px;padding:10px;border:1px solid #f5c2c7;border-radius:4px;background-color:#f8d7da;color:#842029;">${errorMessage}</div>`;
            } else if (embedEl) {
                const existingPlaceholder = embedEl.querySelector('.widget-content-placeholder');
                if (existingPlaceholder) {
                    existingPlaceholder.innerHTML = `<div class="alert alert-danger" style="margin:10px;padding:10px;border:1px solid #f5c2c7;border-radius:4px;background-color:#f8d7da;color:#842029;">${errorMessage}</div>`;
                }
            }
        }
        
        // Eğer 10 saniye içinde yükleme tamamlanmazsa temizle (takılma koruması)
        setTimeout(() => {
            if (window._loadingWidgets && window._loadingWidgets.has(widgetId)) {
                console.warn(`Widget ${widgetId} yükleme zaman aşımı`);
                window._loadingWidgets.delete(widgetId);
            }
        }, 10000);
    };
                
    // Module widget içeriğini yükle
    window.studioLoadModuleWidget = function(moduleId) {
        if (!moduleId) {
            console.error('Module ID belirtilmedi');
            return;
        }
        
        // Küresel yükleme durumu takibi
        window._loadedModules = window._loadedModules || new Set();
        
        // Yüklenmekte olan modülleri izle
        window._loadingModules = window._loadingModules || new Set();
        
        // Aynı modül için çoklu istek varsa, yalnızca bir kez yükle
        if (window._loadingModules.has(moduleId)) {
            console.log(`Module ${moduleId} zaten yükleniyor, işlem atlanıyor...`);
            return;
        }
        
        // Zaten yüklenmişse tekrar yükleme
        if (window._loadedModules.has(moduleId)) {
            console.log(`Module widget ${moduleId} zaten yüklenmiş.`);
            
            // DOM'da görünmüyorsa yeniden yükle
            let moduleVisible = false;
            try {
                let moduleEl = document.querySelector(`[data-widget-module-id="${moduleId}"]`);
                if (moduleEl) moduleVisible = true;
                
                if (!moduleVisible) {
                    document.querySelectorAll('iframe').forEach(iframe => {
                        try {
                            const doc = iframe.contentDocument || iframe.contentWindow.document;
                            if (doc.querySelector(`[data-widget-module-id="${moduleId}"]`)) {
                                moduleVisible = true;
                            }
                        } catch(e) {}
                    });
                }
                
                // DOM'da görünmüyorsa, durum kaydını temizle
                if (!moduleVisible) {
                    console.log(`Module ${moduleId} DOM'da bulunamadı, yeniden yüklenecek`);
                    window._loadedModules.delete(moduleId);
                } else {
                    return; // DOM'da görünüyorsa işlemi sonlandır
                }
            } catch(e) {
                console.warn(`Module görünürlük kontrolü hatası:`, e);
            }
        }
        
        // Yükleme başlangıcında durumu işaretle
        window._loadingModules.add(moduleId);
        
        console.log(`Module widget ${moduleId} içeriği yükleniyor...`);
        
        // Önce tüm loading göstergelerini gizle
        hideAllLoadingIndicators();
        
        // Sadece belirli module ID'sine sahip container'larda loading göster
        const showLoadingForSpecificModule = function(targetModuleId) {
            // Ana belge içinde sadece bu module'ün container'ını bul
            const specificModuleContainer = document.getElementById(`module-content-${targetModuleId}`);
            if (specificModuleContainer) {
                const loading = specificModuleContainer.querySelector('.widget-loading');
                if (loading) {
                    loading.classList.add('loading-active');
                }
            }
            
            // iframe'lerde sadece bu module ID'sine sahip container'ları bul
            try {
                document.querySelectorAll('iframe').forEach(iframe => {
                    try {
                        const doc = iframe.contentDocument || iframe.contentWindow.document;
                        const iframeSpecificContainer = doc.getElementById(`module-content-${targetModuleId}`);
                        if (iframeSpecificContainer) {
                            const loading = iframeSpecificContainer.querySelector('.widget-loading');
                            if (loading) {
                                loading.classList.add('loading-active');
                            }
                        }
                    } catch(e) {}
                });
            } catch(e) {}
        };
        
        // Yükleme göstergesini göster
        showLoadingForSpecificModule(moduleId);
        
        // API'den modül içeriğini al
        fetch(`/admin/studio/api/module-widget/${moduleId}`, { 
            credentials: 'same-origin',
            cache: 'no-cache',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            console.log(`Module ${moduleId} JSON yanıtı alındı:`, response.status);
            
            if (!response.ok) {
                throw new Error(`Module JSON yanıtı başarısız: ${response.status}`);
            }
            
            return response.json().catch(e => {
                throw new Error(`Module yanıtı geçerli JSON değil: ${e.message}`);
            });
        })
        .then(data => {
            // Yükleme işlemi bitti olarak işaretle
            window._loadingModules.delete(moduleId);
            
            // Başarıyla yüklendi olarak işaretle
            window._loadedModules.add(moduleId);
            
            // Container bul (main doc veya iframe) 
            const findModuleElements = function(targetModuleId) {
                const elements = {
                    moduleEl: null,
                    placeholder: null,
                    targetDocument: document
                };
                
                // SADECE bu module için spesifik container'ı bul
                elements.placeholder = document.getElementById(`module-content-${targetModuleId}`);
                elements.moduleEl = document.querySelector(`[data-widget-module-id="${targetModuleId}"]`);
                
                // Ana belgede bulunamadıysa, iframe'leri kontrol et
                if (!elements.moduleEl || !elements.placeholder) {
                    Array.from(document.querySelectorAll('iframe')).forEach(fr => {
                        if (!elements.moduleEl || !elements.placeholder) {
                            try {
                                const doc = fr.contentDocument || fr.contentWindow.document;
                                
                                if (!elements.moduleEl) {
                                    const el = doc.querySelector(`[data-widget-module-id="${targetModuleId}"]`);
                                    if (el) {
                                        elements.moduleEl = el;
                                        elements.targetDocument = doc;
                                    }
                                }
                                
                                if (!elements.placeholder) {
                                    const placeholder = doc.getElementById(`module-content-${targetModuleId}`);
                                    if (placeholder) {
                                        elements.placeholder = placeholder;
                                        elements.targetDocument = doc;
                                    }
                                }
                            } catch(e) {
                                console.warn(`iframe için module elementi erişilemedi (CORS):`, e);
                            }
                        }
                    });
                }
                
                return elements;
            };
            
            const elements = findModuleElements(moduleId);
            const {moduleEl, placeholder, targetDocument} = elements;
            
            if (!moduleEl && !placeholder) {
                console.error(`Module widget elementi veya placeholder bulunamadı: ${moduleId}`);
                return;
            }
            
            try {
                // HTML render - Handlebars ile şablon işleme
                let html = data.content_html || data.html || '';
                
                if (data.useHandlebars && window.Handlebars) {
                    try {
                        const template = Handlebars.compile(html);
                        html = template(data.context || {});
                    } catch(err) {
                        console.error(`Module HTML Handlebars derlerken hata:`, err);
                        // Hata durumunda orijinal HTML'yi kullan
                    }
                }
                
                // HTML'i SADECE bu module'ün container'ına yaz
                if (placeholder) {
                    // Yükleme göstergesini kaldır
                    const loadingEl = placeholder.querySelector('.widget-loading');
                    if (loadingEl) {
                        loadingEl.classList.remove('loading-active');
                    }
                    
                    placeholder.innerHTML = html;
                } else if (moduleEl) {
                    // İçerik alanı oluştur ve ekle
                    const contentContainer = targetDocument.createElement('div');
                    contentContainer.className = 'widget-content-placeholder';
                    contentContainer.id = `module-content-${moduleId}`;
                    contentContainer.innerHTML = html;
                    
                    // Mevcut içeriği temizle ve yeni içeriği ekle
                    const existingPlaceholder = moduleEl.querySelector('.widget-content-placeholder');
                    if (existingPlaceholder) {
                        existingPlaceholder.innerHTML = html;
                    } else {
                        moduleEl.appendChild(contentContainer);
                    }
                }
                
                // CSS enjeksiyon - benzersiz bir stil ID'si kullan
                if (data.content_css || data.css) {
                    let css = data.content_css || data.css;
                    
                    // Stil ID'si
                    const styleId = `module-style-${moduleId}`;
                    let styleEl = targetDocument.getElementById(styleId);
                    
                    if (!styleEl) {
                        styleEl = targetDocument.createElement('style');
                        styleEl.id = styleId;
                        targetDocument.head.appendChild(styleEl);
                    }
                    
                    // Unique class adı oluştur
                    const uniqueClass = `module-widget-${moduleId}`;
                    
                    // CSS'i kapsayıcı class ile içeri al
                    css = css.replace(/([^{}]*){([^{}]*)}/g, function(match, selector, rules) {
                        if (selector.trim() === '') return match;
                        // Selektörü düzenle - yalnızca boş olmayan selektörler için
                        return `.${uniqueClass} ${selector.trim()} {${rules}}`;
                    });
                    
                    // Moduleun kök elementine unique class ekle
                    if (moduleEl) {
                        moduleEl.classList.add(uniqueClass);
                    }
                    
                    // Stil içeriğini ayarla
                    styleEl.textContent = css;
                }
                
                // JS enjeksiyon - benzersiz bir script ID'si kullan
                if (data.content_js || data.js) {
                    let js = data.content_js || data.js;
                    
                    // Script ID
                    const scriptId = `module-script-${moduleId}`;
                    let scriptEl = targetDocument.getElementById(scriptId);
                    
                    // Script elemanı yoksa oluştur
                    if (!scriptEl) {
                        scriptEl = targetDocument.createElement('script');
                        scriptEl.id = scriptId;
                        scriptEl.type = 'text/javascript';
                        
                        // Modüle bağlı kod olduğunu belirten açıklama ve IIFE kapsama içinde çalıştır
                        js = `/* Module widget #${moduleId} script */\n(function(moduleId) {\ntry {\n${js}\n} catch(e) { console.error('Module script hatası:', e); }\n})(${moduleId});`;
                        
                        scriptEl.textContent = js;
                        targetDocument.body.appendChild(scriptEl);
                    } else {
                        // Mevcut script'i güncelle
                        js = `/* Module widget #${moduleId} script - güncellendi */\n(function(moduleId) {\ntry {\n${js}\n} catch(e) { console.error('Module script hatası:', e); }\n})(${moduleId});`;
                        scriptEl.textContent = js;
                    }
                }
                
                console.log(`Module widget ${moduleId} başarıyla yüklendi`);
                
                // Modül yüklendikten sonra olayı tetikle
                const moduleLoadedEvent = new CustomEvent('module:loaded', {
                    detail: { moduleId: moduleId, timestamp: Date.now() }
                });
                document.dispatchEvent(moduleLoadedEvent);
                
                // İçerik kontrolü yap - boş içerik varsa tekrar dene
                if (placeholder && placeholder.innerHTML.trim() === '') {
                    console.warn(`Module ${moduleId} içeriği boş, tekrar deneniyor...`);
                    setTimeout(() => {
                        window._loadedModules.delete(moduleId);
                        window.studioLoadModuleWidget(moduleId);
                    }, 1000);
                }
            } catch (renderErr) {
                console.error(`Module widget ${moduleId} render hatası:`, renderErr);
                
                // Hata olduğunda basit bir hata mesajı göster
                showModuleError(moduleId, `Render hatası: ${renderErr.message}`);
            }
        })
        .catch(error => {
            console.error(`Module widget ${moduleId} yükleme hatası:`, error);
            window._loadingModules.delete(moduleId);
            
            // Hata durumunda basit bir hata mesajı göster
            showModuleError(moduleId, `Yükleme hatası: ${error.message}`);
            
            // Zaman aşımı veya ağ hatası durumunda tekrar dene
            if (error.name === 'TypeError' || error.message.includes('timeout') || error.message.includes('network')) {
                setTimeout(() => {
                    if (!window._loadedModules.has(moduleId)) {
                        console.log(`Module ${moduleId} için tekrar deneme yapılıyor...`);
                        window.studioLoadModuleWidget(moduleId);
                    }
                }, 2000);
            }
        });
        
        // Module için hata mesajı göster
        function showModuleError(targetModuleId, errorMessage) {
            // SADECE bu module için spesifik container'ı bul
            let placeholder = document.getElementById(`module-content-${targetModuleId}`);
            let moduleEl = document.querySelector(`[data-widget-module-id="${targetModuleId}"]`);
            let targetDocument = document;
            
            if (!placeholder && !moduleEl) {
                Array.from(document.querySelectorAll('iframe')).forEach(fr => {
                    if (!placeholder && !moduleEl) {
                        try {
                            const doc = fr.contentDocument || fr.contentWindow.document;
                            
                            if (!placeholder) {
                                placeholder = doc.getElementById(`module-content-${targetModuleId}`);
                            }
                            
                            if (!moduleEl) {
                                moduleEl = doc.querySelector(`[data-widget-module-id="${targetModuleId}"]`);
                            }
                            
                            if (placeholder || moduleEl) {
                                targetDocument = doc;
                            }
                        } catch(e) {}
                    }
                });
            }
            
            // Hata mesajını göster
            if (placeholder) {
                placeholder.innerHTML = `<div class="alert alert-danger" style="margin:10px;padding:10px;border:1px solid #f5c2c7;border-radius:4px;background-color:#f8d7da;color:#842029;">${errorMessage}</div>`;
            } else if (moduleEl) {
                const existingPlaceholder = moduleEl.querySelector('.widget-content-placeholder');
                if (existingPlaceholder) {
                    existingPlaceholder.innerHTML = `<div class="alert alert-danger" style="margin:10px;padding:10px;border:1px solid #f5c2c7;border-radius:4px;background-color:#f8d7da;color:#842029;">${errorMessage}</div>`;
                }
            }
        }
        
        // Eğer 10 saniye içinde yükleme tamamlanmazsa temizle (takılma koruması)
        setTimeout(() => {
            if (window._loadingModules && window._loadingModules.has(moduleId)) {
                console.warn(`Module ${moduleId} yükleme zaman aşımı`);
                window._loadingModules.delete(moduleId);
            }
        }, 10000);
    };
    
    return {
        loadWidgetBlocks: loadWidgetBlocks,
        processExistingWidgets: processExistingWidgets,
        cleanTemplateVariables: cleanTemplateVariables,
        addWidgetStyles: addWidgetStyles,
        registerWidgetEmbedComponent: registerWidgetEmbedComponent,
        processWidgetEmbeds: processWidgetEmbeds
    };
})();