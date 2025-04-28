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
        });
    }
    
    return {
        loadWidgetBlocks: loadWidgetBlocks,
        processExistingWidgets: processExistingWidgets,
        cleanTemplateVariables: cleanTemplateVariables,
        addWidgetStyles: addWidgetStyles
    };
})();