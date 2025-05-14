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
            }
            
            /* Yükleme göstergesi */
            .widget-loading {
                text-align: center;
                padding: 20px;
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
                    
                    // Sınıf adını güncelle
                    if (!component.getAttributes().class.includes('studio-widget-container')) {
                        component.addClass('studio-widget-container');
                    }
                    
                    // Özellikleri ayarla
                    component.set('editable', false);
                    component.set('droppable', false);
                    component.set('draggable', true);
                    component.set('highlightable', true);
                    component.set('selectable', true);
                    
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
                    
                    // Sınıf adını güncelle
                    if (!component.getAttributes().class.includes('studio-widget-container')) {
                        component.addClass('studio-widget-container');
                    }
                    
                    // Özellikleri ayarla
                    component.set('editable', false);
                    component.set('droppable', false);
                    component.set('draggable', true);
                    component.set('highlightable', true);
                    component.set('selectable', true);
                    
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
                    
                    // Widget içeriğini yükle
                    setTimeout(() => {
                        if (window.studioLoadWidget) {
                            window.studioLoadWidget(widgetId);
                        }
                    }, 100);
                });
            }
            
            // Module widget'ları için benzer işlemler
            const moduleComponents = editor.DomComponents.getWrapper().find('[data-widget-module-id]');
            
            if (moduleComponents && moduleComponents.length > 0) {
                console.log("Module widget bileşenleri bulundu:", moduleComponents.length);
                
                moduleComponents.forEach(component => {
                    const moduleId = component.getAttributes()['data-widget-module-id'];
                    
                    if (!moduleId) return;
                    
                    // Module-widget tipini ekle
                    component.set('type', 'module-widget');
                    component.set('widget_module_id', moduleId);
                    
                    // Sınıf adını güncelle
                    if (!component.getAttributes().class.includes('studio-widget-container')) {
                        component.addClass('studio-widget-container');
                    }
                    
                    // Önemli: DOM elemanı üzerinde doğrudan işlem yap
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
                    
                    // Module içeriğini yükle
                    setTimeout(() => {
                        if (window.studioLoadModuleWidget) {
                            window.studioLoadModuleWidget(moduleId);
                        }
                    }, 100);
                });
            }
        } catch (err) {
            console.error("Widget embed işleme genel hatası:", err);
        }
    }
    
    
    // Widget içeriğini yükle
    window.studioLoadWidget = function(widgetId) {
        window._loadedWidgets = window._loadedWidgets || new Set();
        if (window._loadedWidgets.has(widgetId)) return;
        window._loadedWidgets.add(widgetId);
        console.log(`Widget ${widgetId} içeriği yükleniyor...`);
        fetch(`/admin/widgetmanagement/preview/embed/json/${widgetId}`, { credentials: 'same-origin' })
            .then(response => {
                console.log(`Widget ${widgetId} JSON yanıtı alındı:`, response.status);
                if (!response.ok) throw new Error(`Widget JSON yanıtı başarısız: ${response.status}`);
                return response.json();
            })
            .then(data => {
                // Container bul (main doc veya iframe)
                let embedEl = document.querySelector(`[data-tenant-widget-id="${widgetId}"]`);
                let targetDocument = document;
                if (!embedEl) {
                    Array.from(document.querySelectorAll('iframe')).forEach(fr => {
                        if (!embedEl) {
                            try {
                                const doc = fr.contentDocument || fr.contentWindow.document;
                                const el = doc.querySelector(`[data-tenant-widget-id="${widgetId}"]`);
                                if (el) {
                                    embedEl = el;
                                    targetDocument = doc;
                                }
                            } catch(e) {}
                        }
                    });
                }
                if (!embedEl) {
                    console.error(`Widget embed elementi bulunamadı: ${widgetId}`);
                    return;
                }
                // HTML render
                let html = data.content_html;
                if (data.useHandlebars && window.Handlebars) {
                    const template = Handlebars.compile(html);
                    html = template(data.context);
                }
                // HTML'i placeholder container'a yaz, yoksa embedEl'e
                const placeholder = targetDocument.getElementById(`widget-content-${widgetId}`) || embedEl.querySelector('.widget-content-placeholder');
                if (placeholder) {
                    placeholder.innerHTML = html;
                } else {
                    embedEl.innerHTML = html;
                }
                
                // CSS enjeksiyon
                if (data.content_css) {
                    let css = data.content_css;
                    if (data.useHandlebars && window.Handlebars) {
                        try {
                            const cssTpl = Handlebars.compile(css);
                            css = cssTpl(data.context);
                        } catch(err) {
                            console.error(`CSS Handlebars derlerken hata:`, err);
                        }
                    }
                    const style = targetDocument.createElement('style');
                    style.textContent = css;
                    targetDocument.head.appendChild(style);
                }
                // JS enjeksiyon
                if (data.content_js) {
                    let js = data.content_js;
                    if (data.useHandlebars && window.Handlebars) {
                        try {
                            const jsTpl = Handlebars.compile(js);
                            js = jsTpl(data.context);
                        } catch(err) {
                            console.error(`JS Handlebars derlerken hata:`, err);
                        }
                    }
                    if (js.includes('Handlebars.registerHelper')) {
                        if (!window._handlebarsHelpersInjected) {
                            const script = targetDocument.createElement('script');
                            script.type = 'text/javascript';
                            script.textContent = js;
                            const container = targetDocument.querySelector('footer') || targetDocument.body;
                            container.appendChild(script);
                            window._handlebarsHelpersInjected = true;
                        }
                    } else {
                        const script = targetDocument.createElement('script');
                        script.type = 'text/javascript';
                        script.textContent = js;
                        targetDocument.body.appendChild(script);
                    }
                }
                console.log(`Widget ${widgetId} başarıyla yüklendi`);
            })
            .catch(error => {
                console.error(`Widget ${widgetId} yükleme hatası:`, error);
                // Error durumunda embed elementi içinde göster
                let embedEl = document.querySelector(`[data-tenant-widget-id="${widgetId}"]`);
                let targetDocument = document;
                if (!embedEl) {
                    Array.from(document.querySelectorAll('iframe')).forEach(fr => {
                        if (!embedEl) {
                            try {
                                const doc = fr.contentDocument || fr.contentWindow.document;
                                const el = doc.querySelector(`[data-tenant-widget-id="${widgetId}"]`);
                                if (el) {
                                    embedEl = el;
                                    targetDocument = doc;
                                }
                            } catch(e) {}
                        }
                    });
                }
                if (embedEl) {
                    embedEl.innerHTML = `<div class="alert alert-danger">Widget yüklenirken hata: ${error.message}</div>`;
                }
            });
    };
            
    // Module widget içeriğini yükle
    window.studioLoadModuleWidget = function(moduleId) {
        // Küresel yükleme durumu takibi
        window._loadedModules = window._loadedModules || new Set();
        
        // Zaten yüklenmişse tekrar yükleme
        if (window._loadedModules.has(moduleId)) return;
        
        // Yükleme başlangıcında durumu işaretle
        window._loadedModules.add(moduleId);
        
        console.log(`Module widget ${moduleId} içeriği yükleniyor...`);
        
        // Yükleniyor mesajını göster
        let loadingElements = document.querySelectorAll(`#module-content-${moduleId} .widget-loading`);
        loadingElements.forEach(el => {
            el.style.display = 'block';
        });
        
        // iFrame içindeki yükleniyor mesajlarını da kontrol et
        document.querySelectorAll('iframe').forEach(iframe => {
            try {
                const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                const iframeLoadingElements = iframeDoc.querySelectorAll(`#module-content-${moduleId} .widget-loading`);
                iframeLoadingElements.forEach(el => {
                    el.style.display = 'block';
                });
            } catch(e) {}
        });
        
        // API'den modül içeriğini al
        fetch(`/admin/widgetmanagement/api/module/${moduleId}`, { credentials: 'same-origin' })
            .then(response => {
                console.log(`Module ${moduleId} JSON yanıtı alındı:`, response.status);
                if (!response.ok) throw new Error(`Module JSON yanıtı başarısız: ${response.status}`);
                return response.json();
            })
            .then(data => {
                // Container bul (main doc veya iframe) 
                let moduleEl = document.querySelector(`[data-widget-module-id="${moduleId}"]`);
                let targetDocument = document;
                
                if (!moduleEl) {
                    Array.from(document.querySelectorAll('iframe')).forEach(fr => {
                        if (!moduleEl) {
                            try {
                                const doc = fr.contentDocument || fr.contentWindow.document;
                                const el = doc.querySelector(`[data-widget-module-id="${moduleId}"]`);
                                if (el) {
                                    moduleEl = el;
                                    targetDocument = doc;
                                }
                            } catch(e) {}
                        }
                    });
                }
                
                if (!moduleEl) {
                    console.error(`Module widget elementi bulunamadı: ${moduleId}`);
                    return;
                }
                
                // HTML render
                let html = data.content_html || data.html || '';
                
                if (data.useHandlebars && window.Handlebars) {
                    try {
                        const template = Handlebars.compile(html);
                        html = template(data.context || {});
                    } catch(err) {
                        console.error(`Module HTML Handlebars derlerken hata:`, err);
                    }
                }
                
                // HTML'i placeholder container'a yaz, yoksa moduleEl'e
                const placeholder = targetDocument.getElementById(`module-content-${moduleId}`) || 
                                moduleEl.querySelector('.widget-content-placeholder');
                
                if (placeholder) {
                    placeholder.innerHTML = html;
                } else {
                    moduleEl.innerHTML = html;
                }
                
                // CSS enjeksiyon
                if (data.content_css || data.css) {
                    let css = data.content_css || data.css;
                    
                    // Unique class adı oluştur
                    const uniqueClass = `module-widget-${moduleId}`;
                    
                    // CSS'i kapsayıcı class ile içeri al
                    css = css.replace(/([^{}]*){([^{}]*)}/g, function(match, selector, rules) {
                        // Selektorü düzenle
                        return `.${uniqueClass} ${selector} {${rules}}`;
                    });
                    
                    // Moduleun kök elementine unique class ekle
                    moduleEl.classList.add(uniqueClass);
                    
                    // CSS'i head'e ekle
                    const styleId = `module-style-${moduleId}`;
                    let styleEl = targetDocument.getElementById(styleId);
                    
                    if (!styleEl) {
                        styleEl = targetDocument.createElement('style');
                        styleEl.id = styleId;
                        targetDocument.head.appendChild(styleEl);
                    }
                    
                    styleEl.textContent = css;
                }
                
                // JS enjeksiyon
                if (data.content_js || data.js) {
                    let js = data.content_js || data.js;
                    
                    // Script ID
                    const scriptId = `module-script-${moduleId}`;
                    let scriptEl = targetDocument.getElementById(scriptId);
                    
                    if (!scriptEl) {
                        scriptEl = targetDocument.createElement('script');
                        scriptEl.id = scriptId;
                        scriptEl.type = 'text/javascript';
                        
                        // Modüle bağlı kod olduğunu belirten açıklama
                        js = `/* Module widget #${moduleId} script */\n(function() {\n${js}\n})();`;
                        
                        scriptEl.textContent = js;
                        targetDocument.body.appendChild(scriptEl);
                    } else {
                        scriptEl.textContent = js;
                    }
                }
                
                console.log(`Module widget ${moduleId} başarıyla yüklendi`);
            })
            .catch(error => {
                console.error(`Module widget ${moduleId} yükleme hatası:`, error);
                
                // Error durumunda module elementi içinde göster
                let moduleEl = document.querySelector(`[data-widget-module-id="${moduleId}"]`);
                let targetDocument = document;
                
                if (!moduleEl) {
                    Array.from(document.querySelectorAll('iframe')).forEach(fr => {
                        if (!moduleEl) {
                            try {
                                const doc = fr.contentDocument || fr.contentWindow.document;
                                const el = doc.querySelector(`[data-widget-module-id="${moduleId}"]`);
                                if (el) {
                                    moduleEl = el;
                                    targetDocument = doc;
                                }
                            } catch(e) {}
                        }
                    });
                }
                
                if (moduleEl) {
                    moduleEl.innerHTML = `<div class="alert alert-danger">Module yüklenirken hata: ${error.message}</div>`;
                }
            });
    };
    
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
                    classes: ['studio-widget-container', 'widget-embed'],
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
                    
                    // Element pozisyonunu RELATIVE yap (kesinlikle gerekli)
                    const el = this.el;
                    if (el) {
                        el.style.position = 'relative';
                        
                        // Sınıfları ekle
                        el.classList.add('studio-widget-container', 'widget-embed');
                        
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