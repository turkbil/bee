/**
 * Studio Editor - Widget Bileşenleri Modülü
 * Widget bileşenlerinin tanımlanması ve işlenmesi
 */

window.StudioWidgetComponents = (function() {
    /**
     * Widget komponentlerini tanımla
     * @param {Object} editor - GrapesJS editor örneği
     */
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
                        if (widgetId && window.StudioWidgetManager && window.StudioWidgetManager.getWidgetData) {
                            const widget = window.StudioWidgetManager.getWidgetData(widgetId);
                            if (widget) {
                                this.set('content_html', widget.content_html);
                                this.set('content_css', widget.content_css);
                                this.set('content_js', widget.content_js);
                                this.set('widget_type', widget.type);
                                this.set('file_path', widget.file_path || '');
                                
                                // Widget tipine göre düzenlenebilirlik ayarları
                                if (widget.type === 'dynamic' || widget.type === 'module') {
                                    // Tamamen kilitli yap
                                    this.set('editable', false);
                                    this.set('draggable', false);
                                    this.set('droppable', false);
                                    this.set('selectable', true); // Sadece bilgi göstermek için seçilebilir
                                    this.set('highlightable', false);
                                    this.set('hoverable', false);
                                    this.set('locked', true);
                                } else {
                                    // Düzenlenebilir yap
                                    this.set('editable', true);
                                    this.set('draggable', true);
                                    this.set('highlightable', true);
                                    this.set('selectable', true);
                                    this.set('hoverable', true);
                                    this.set('locked', false);
                                }
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
                    'dblclick': 'onDblClick',
                    'click': 'onClick'
                },
                
                init() {
                    this.listenTo(this.model, 'change:widget_id', this.onRender);
                },
                
                onRender() {
                    const el = this.el;
                    if (!el) return;
                    
                    const model = this.model;
                    const widgetId = model.get('widget_id') || model.getAttributes()['data-widget-id'];
                    
                    if (!widgetId) return;
                    
                    // Handlebars widget içerikleri için yardımcı helper fonksiyonları tanımla
                    if (typeof Handlebars !== 'undefined' && !window._handlebarsHelpersRegistered) {
                        window._handlebarsHelpersRegistered = true;
                        
                        Handlebars.registerHelper('eq', function(v1, v2, options) {
                            if (arguments.length < 3)
                                throw new Error("Handlebars Helper 'eq' ihtiyaç duyduğu parametreleri almadı");
                            return v1 === v2 ? options.fn(this) : options.inverse(this);
                        });
                        
                        Handlebars.registerHelper('ne', function(v1, v2, options) {
                            if (arguments.length < 3)
                                throw new Error("Handlebars Helper 'ne' ihtiyaç duyduğu parametreleri almadı");
                            return v1 !== v2 ? options.fn(this) : options.inverse(this);
                        });
                        
                        Handlebars.registerHelper('lt', function(v1, v2, options) {
                            if (arguments.length < 3)
                                throw new Error("Handlebars Helper 'lt' ihtiyaç duyduğu parametreleri almadı");
                            return v1 < v2 ? options.fn(this) : options.inverse(this);
                        });
                        
                        Handlebars.registerHelper('gt', function(v1, v2, options) {
                            if (arguments.length < 3)
                                throw new Error("Handlebars Helper 'gt' ihtiyaç duyduğu parametreleri almadı");
                            return v1 > v2 ? options.fn(this) : options.inverse(this);
                        });
                        
                        Handlebars.registerHelper('lte', function(v1, v2, options) {
                            if (arguments.length < 3)
                                throw new Error("Handlebars Helper 'lte' ihtiyaç duyduğu parametreleri almadı");
                            return v1 <= v2 ? options.fn(this) : options.inverse(this);
                        });
                        
                        Handlebars.registerHelper('gte', function(v1, v2, options) {
                            if (arguments.length < 3)
                                throw new Error("Handlebars Helper 'gte' ihtiyaç duyduğu parametreleri almadı");
                            return v1 >= v2 ? options.fn(this) : options.inverse(this);
                        });
                        
                        Handlebars.registerHelper('and', function() {
                            var options = arguments[arguments.length - 1];
                            for (var i = 0; i < arguments.length - 1; i++) {
                                if (!arguments[i]) {
                                    return options.inverse(this);
                                }
                            }
                            return options.fn(this);
                        });
                        
                        Handlebars.registerHelper('or', function() {
                            var options = arguments[arguments.length - 1];
                            for (var i = 0; i < arguments.length - 1; i++) {
                                if (arguments[i]) {
                                    return options.fn(this);
                                }
                            }
                            return options.inverse(this);
                        });
                        
                        Handlebars.registerHelper('truncate', function(str, len) {
                            if (!str || !len) {
                                return str;
                            }
                            if (str.length > len) {
                                return str.substring(0, len) + '...';
                            }
                            return str;
                        });
                        
                        Handlebars.registerHelper('formatDate', function(date, format) {
                            if (!date) return '';
                            var d = new Date(date);
                            if (isNaN(d.getTime())) return date;
                            
                            var day = d.getDate().toString().padStart(2, '0');
                            var month = (d.getMonth() + 1).toString().padStart(2, '0');
                            var year = d.getFullYear();
                            
                            return day + '.' + month + '.' + year;
                        });
                        
                        Handlebars.registerHelper('json', function(context) {
                            return JSON.stringify(context);
                        });
                    }
                    
                    let widgetData = null;
                    if (window.StudioWidgetManager && window.StudioWidgetManager.getWidgetData) {
                        widgetData = window.StudioWidgetManager.getWidgetData(widgetId);
                    }
                    
                    if (!widgetData) return;
                    
                    const widgetType = widgetData.type || 'static';
                    const isEditable = widgetType === 'static' || widgetType === 'file';
                    
                    // Tüm önceki stilleri temizle
                    el.className = 'gjs-widget-wrapper';
                    el.style = '';
                    
                    // Widget tipine göre stil uygula
                    switch(widgetType) {
                        case 'dynamic':
                            // Dinamik widget - mavi ton
                            el.classList.add('dynamic-widget');
                            el.style.border = '2px solid #3b82f6';
                            el.style.backgroundColor = 'rgba(59, 130, 246, 0.05)';
                            el.style.boxShadow = '0 0 0 1px rgba(59, 130, 246, 0.5)';
                            el.style.pointerEvents = 'none';
                            el.setAttribute('data-locked', 'true');
                            break;
                            
                        case 'module':
                            // Module widget - mor ton
                            el.classList.add('module-widget');
                            el.style.border = '2px solid #8b5cf6';
                            el.style.backgroundColor = 'rgba(139, 92, 246, 0.05)';
                            el.style.boxShadow = '0 0 0 1px rgba(139, 92, 246, 0.5)';
                            el.style.pointerEvents = 'none';
                            el.setAttribute('data-locked', 'true');
                            break;
                            
                        case 'file':
                            // File widget - turuncu ton
                            el.classList.add('file-widget');
                            el.style.border = '2px solid #f59e0b';
                            el.style.backgroundColor = 'rgba(245, 158, 11, 0.05)';
                            el.style.boxShadow = '0 0 0 1px rgba(245, 158, 11, 0.5)';
                            break;
                            
                        case 'static':
                        default:
                            // Static widget - yeşil ton
                            el.classList.add('static-widget');
                            el.style.border = '2px solid #10b981';
                            el.style.backgroundColor = 'rgba(16, 185, 129, 0.05)';
                            el.style.boxShadow = '0 0 0 1px rgba(16, 185, 129, 0.5)';
                            break;
                    }
                    
                    // Ortak stiller
                    el.style.padding = '12px';
                    el.style.margin = '10px 0';
                    el.style.borderRadius = '6px';
                    el.style.position = 'relative';
                    
                    // Dynamic ve Module tipinde özel koruma katmanı ekle
                    if (widgetType === 'dynamic' || widgetType === 'module') {
                        // Mevcut overlayleri temizle
                        const existingOverlays = el.querySelectorAll('.widget-overlay');
                        existingOverlays.forEach(overlay => overlay.remove());
                        
                        // Overlay oluştur
                        const overlay = document.createElement('div');
                        overlay.className = 'widget-overlay';
                        overlay.style.position = 'absolute';
                        overlay.style.top = '0';
                        overlay.style.left = '0';
                        overlay.style.width = '100%';
                        overlay.style.height = '100%';
                        overlay.style.zIndex = '100';
                        overlay.style.background = 'repeating-linear-gradient(45deg, rgba(0,0,0,0.03), rgba(0,0,0,0.03) 10px, rgba(0,0,0,0.05) 10px, rgba(0,0,0,0.05) 20px)';
                        overlay.style.pointerEvents = 'auto';
                        overlay.style.cursor = 'not-allowed';
                        
                        // Tip etiketi ekle
                        const badge = document.createElement('span');
                        badge.className = 'widget-type-badge';
                        badge.style.position = 'absolute';
                        badge.style.top = '50%';
                        badge.style.left = '50%';
                        badge.style.transform = 'translate(-50%, -50%)';
                        badge.style.padding = '5px 10px';
                        badge.style.borderRadius = '3px';
                        badge.style.fontSize = '12px';
                        badge.style.fontWeight = 'bold';
                        badge.style.color = 'white';
                        badge.style.background = widgetType === 'dynamic' ? '#3b82f6' : '#8b5cf6';
                        badge.style.boxShadow = '0 2px 4px rgba(0,0,0,0.2)';
                        badge.style.textTransform = 'uppercase';
                        badge.style.letterSpacing = '1px';
                        badge.innerHTML = widgetType === 'dynamic' ? 'DİNAMİK BİLEŞEN' : 'MODÜL BİLEŞEN';
                        
                        // Tıklama olayı ekle
                        overlay.addEventListener('click', (e) => {
                            e.stopPropagation();
                            e.preventDefault();
                            if (window.StudioWidgetManager && window.StudioWidgetManager.showWidgetModal) {
                                window.StudioWidgetManager.showWidgetModal(widgetId);
                            }
                        });
                        
                        overlay.appendChild(badge);
                        el.appendChild(overlay);
                        
                        // İçerikteki tüm elementleri devre dışı bırak
                        const allChildren = el.querySelectorAll('*:not(.widget-overlay):not(.widget-type-badge)');
                        allChildren.forEach(child => {
                            if (child !== overlay && !overlay.contains(child)) {
                                child.style.pointerEvents = 'none';
                                child.contentEditable = "false";
                                
                                // İçerikleri gri ve flu göster
                                child.style.filter = 'grayscale(20%) blur(0.3px)';
                                child.style.opacity = '0.7';
                            }
                        });
                    } else {
                        // Düzenlenebilir widget için overlay kaldır
                        const overlays = el.querySelectorAll('.widget-overlay');
                        overlays.forEach(overlay => overlay.remove());
                        
                        el.style.pointerEvents = 'auto';
                        el.removeAttribute('data-locked');
                    }
                    
                    // Etiket ekle
                    const existingLabels = el.querySelectorAll('.widget-label');
                    existingLabels.forEach(label => label.remove());
                    
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
                    
                    // Widget tipine göre farklı stil
                    switch(widgetType) {
                        case 'dynamic':
                            labelElement.style.backgroundColor = '#3b82f6';
                            labelElement.style.color = 'white';
                            labelElement.innerHTML = '<i class="fa fa-puzzle-piece me-1"></i> Dinamik Widget';
                            break;
                            
                        case 'module':
                            labelElement.style.backgroundColor = '#8b5cf6';
                            labelElement.style.color = 'white';
                            labelElement.innerHTML = '<i class="fa fa-cube me-1"></i> Modül Widget';
                            break;
                            
                        case 'file':
                            labelElement.style.backgroundColor = '#f59e0b';
                            labelElement.style.color = 'white';
                            labelElement.innerHTML = '<i class="fa fa-file-code me-1"></i> Dosya Widget';
                            break;
                            
                        case 'static':
                        default:
                            labelElement.style.backgroundColor = '#10b981';
                            labelElement.style.color = 'white';
                            labelElement.innerHTML = '<i class="fa fa-code me-1"></i> Statik Widget';
                            break;
                    }
                    
                    el.appendChild(labelElement);
                    
                    // Dosya yolunu göster
                    if ((widgetType === 'module' || widgetType === 'file') && widgetData.file_path) {
                        const filePathInfo = document.createElement('div');
                        filePathInfo.className = 'widget-file-path';
                        filePathInfo.style.borderTop = '1px dashed rgba(0,0,0,0.1)';
                        filePathInfo.style.marginTop = '8px';
                        filePathInfo.style.paddingTop = '4px';
                        filePathInfo.style.fontSize = '10px';
                        filePathInfo.style.color = '#666';
                        filePathInfo.innerHTML = `<i class="fa fa-link me-1"></i> ${widgetData.file_path}`;
                        
                        el.appendChild(filePathInfo);
                    }
                },
                
                onDblClick(e) {
                    const model = this.model;
                    const widgetId = model.get('widget_id') || model.getAttributes()['data-widget-id'];
                    
                    if (!widgetId) return;
                    
                    let widgetData = null;
                    if (window.StudioWidgetManager && window.StudioWidgetManager.getWidgetData) {
                        widgetData = window.StudioWidgetManager.getWidgetData(widgetId);
                    }
                    
                    if (!widgetData) return;
                    
                    const widgetType = widgetData.type || 'static';
                    
                    // Dynamic ve module tipi için bilgi modalı göster
                    if (widgetType === 'dynamic' || widgetType === 'module') {
                        e.stopPropagation();
                        e.preventDefault();
                        if (window.StudioWidgetManager && window.StudioWidgetManager.showWidgetModal) {
                            window.StudioWidgetManager.showWidgetModal(widgetId);
                        }
                    }
                },
                
                onClick(e) {
                    const model = this.model;
                    const widgetId = model.get('widget_id') || model.getAttributes()['data-widget-id'];
                    
                    if (!widgetId) return;
                    
                    let widgetData = null;
                    if (window.StudioWidgetManager && window.StudioWidgetManager.getWidgetData) {
                        widgetData = window.StudioWidgetManager.getWidgetData(widgetId);
                    }
                    
                    if (!widgetData) return;
                    
                    const widgetType = widgetData.type || 'static';
                    
                    // Dynamic ve module için tıklamayı engelle
                    if (widgetType === 'dynamic' || widgetType === 'module') {
                        e.stopPropagation();
                        e.preventDefault();
                        if (window.StudioWidgetManager && window.StudioWidgetManager.showWidgetModal) {
                            window.StudioWidgetManager.showWidgetModal(widgetId);
                        }
                    }
                }
            }
        });
    }
    
    return {
        registerWidgetComponents: registerWidgetComponents
    };
})();