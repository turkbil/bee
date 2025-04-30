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
        
    /**
     * Global widget yükleme fonksiyonu
     */
    function setupGlobalWidgetLoader() {
        window.studioLoadWidget = function(widgetId) {
            console.log("studioLoadWidget başlatıldı: widget_id=" + widgetId);
            
            // Editor iframe'ini bul
            const editorFrame = document.querySelector('.gjs-frame');
            const editorDocument = editorFrame ? editorFrame.contentDocument || editorFrame.contentWindow.document : null;
            
            if (!editorDocument) {
                console.error("Editor iframe belge nesnesi bulunamadı!");
                return;
            }
            
            // Olası tüm container ID'lerini dene
            const containerIds = [
                `widget-content-${widgetId}`,
                `widget-placeholder-${widgetId}`
            ];
            
            let container = null;
            
            // İlk olarak iframe içinde ara
            for (const id of containerIds) {
                container = editorDocument.getElementById(id);
                if (container) {
                    console.log(`Widget container iframe içinde bulundu: #${id}`);
                    break;
                }
            }
            
            // iframe içinde querySelector ile dene
            if (!container) {
                for (const id of containerIds) {
                    container = editorDocument.querySelector(`#${id}`);
                    if (container) {
                        console.log(`Widget container iframe içinde querySelector ile bulundu: #${id}`);
                        break;
                    }
                }
            }
            
            // iframe içinde attribute selector ile dene
            if (!container) {
                container = editorDocument.querySelector(`[id="widget-content-${widgetId}"]`);
                if (container) {
                    console.log(`Widget container iframe içinde attribute selector ile bulundu`);
                }
            }
            
            // iframe içinde widget embed içinde ara
            if (!container) {
                const widgetEmbed = editorDocument.getElementById(`widget-embed-${widgetId}`) || 
                                  editorDocument.querySelector(`.widget-embed[data-tenant-widget-id="${widgetId}"]`);
                if (widgetEmbed) {
                    container = widgetEmbed.querySelector('.widget-content-placeholder');
                    if (container) {
                        console.log(`Widget container iframe içinde widget embed içinde bulundu`);
                        // ID'si yoksa ekle
                        if (!container.id) {
                            container.id = `widget-content-${widgetId}`;
                        }
                    }
                }
            }
            
            // Son çare - iframe içindeki tüm placeholder'ları kontrol et
            if (!container) {
                const allPlaceholders = editorDocument.querySelectorAll('.widget-content-placeholder');
                if (allPlaceholders && allPlaceholders.length > 0) {
                    console.log(`iframe içinde ${allPlaceholders.length} adet placeholder bulundu, kontrol ediliyor...`);
                    
                    for (const placeholder of allPlaceholders) {
                        const parent = placeholder.closest('.widget-embed');
                        if (parent && (parent.getAttribute('data-widget-id') == widgetId || 
                                      parent.getAttribute('data-tenant-widget-id') == widgetId)) {
                            container = placeholder;
                            console.log(`iframe içinde eşleşen placeholder bulundu`);
                            break;
                        }
                    }
                }
            }
            
            // iframe içinde hiçbir eşleşme bulunamadıysa ana sayfada dene
            if (!container) {
                console.log("iframe içinde container bulunamadı, ana sayfada aranıyor...");
                
                // Ana sayfada ID ile ara
                for (const id of containerIds) {
                    container = document.getElementById(id);
                    if (container) {
                        console.log(`Widget container ana sayfada bulundu: #${id}`);
                        break;
                    }
                }
                
                // Ana sayfada widget embed içinde ara
                if (!container) {
                    const widgetEmbed = document.querySelector(`.widget-embed[data-tenant-widget-id="${widgetId}"]`);
                    if (widgetEmbed) {
                        container = widgetEmbed.querySelector('.widget-content-placeholder');
                        if (container) {
                            console.log(`Widget container ana sayfada widget embed içinde bulundu`);
                        }
                    }
                }
            }
            
            // Hala bulunamadıysa, iframe içine yeni bir element oluştur
            if (!container) {
                console.log("Hiçbir container bulunamadı, iframe içinde yeni bir element oluşturuluyor");
                
                // iframe içinde widget-embed ara
                const widgetEmbed = editorDocument.querySelector(`.widget-embed[data-tenant-widget-id="${widgetId}"]`);
                
                if (widgetEmbed) {
                    // Widget embed içine placeholder ekle
                    container = document.createElement('div');
                    container.id = `widget-content-${widgetId}`;
                    container.className = 'widget-content-placeholder';
                    container.innerHTML = '<div class="widget-loading"><i class="fa fa-spin fa-spinner"></i> Widget içeriği yükleniyor...</div>';
                    widgetEmbed.appendChild(container);
                    console.log("Widget embed içine yeni container eklendi");
                } else {
                    // iframe body'sine ekle
                    container = document.createElement('div');
                    container.id = `widget-content-${widgetId}`;
                    container.className = 'widget-content-placeholder widget-content-floating';
                    container.innerHTML = '<div class="widget-loading"><i class="fa fa-spin fa-spinner"></i> Widget içeriği yükleniyor...</div>';
                    container.style.position = 'relative';
                    container.style.margin = '10px auto';
                    container.style.padding = '10px';
                    container.style.border = '2px dashed #e11d48';
                    container.style.borderRadius = '5px';
                    
                    if (editorDocument.body) {
                        editorDocument.body.appendChild(container);
                        console.log("Yeni container iframe body'sine eklendi");
                    } else {
                        console.error("iframe body bulunamadı!");
                        // Son çare - ana sayfaya ekle
                        document.body.appendChild(container);
                        console.log("Yeni container ana sayfaya eklendi (son çare)");
                    }
                }
            }
            
            // Widget içeriğini yükle
            console.log(`Widget ${widgetId} içeriği yükleniyor...`);
            fetch(`/admin/widgetmanagement/preview/embed/${widgetId}`)
                .then(response => {
                    console.log(`Widget ${widgetId} yanıtı alındı:`, response.status);
                    if (!response.ok) {
                        throw new Error(`Widget yanıtı başarısız: ${response.status}`);
                    }
                    return response.text();
                })
                .then(html => {
                    console.log(`Widget ${widgetId} içeriği alındı, uzunluk:`, html.length);
                    container.innerHTML = html;
                    
                    // Script etiketlerini güvenli şekilde çalıştır
                    try {
                        const scripts = container.querySelectorAll('script');
                        console.log(`Widget ${widgetId} için ${scripts.length} script işlenecek`);
                        
                        if (scripts.length > 0) {
                            Array.from(scripts).forEach((script, index) => {
                                try {
                                    // İframe içinde mi yoksa ana sayfada mı olduğumuzu kontrol et
                                    const targetDocument = container.ownerDocument;
                                    const isInIframe = targetDocument !== document;
                                    
                                    // Script içeriğini kontrol et
                                    if (script.src) {
                                        // Harici script - src özelliği var
                                        const newScript = targetDocument.createElement('script');
                                        newScript.src = script.src;
                                        
                                        // Diğer özellikleri kopyala
                                        for (const attr of script.attributes) {
                                            if (attr.name !== 'src') {
                                                newScript.setAttribute(attr.name, attr.value);
                                            }
                                        }
                                        
                                        // İframe içindeyse iframe head'e, değilse document head'e ekle
                                        if (isInIframe) {
                                            targetDocument.head.appendChild(newScript);
                                        } else {
                                            document.head.appendChild(newScript);
                                        }
                                        
                                        console.log(`Script ${index + 1}/${scripts.length} başarıyla işlendi (harici)`);
                                    } 
                                    else {
                                        // İç script
                                        const scriptContent = script.textContent;
                                        const safeScript = targetDocument.createElement('script');
                                        safeScript.type = 'text/javascript';
                                        
                                        // İframe içindeyse, iframe context'inde çalıştır
                                        if (isInIframe) {
                                            safeScript.text = scriptContent;
                                            targetDocument.body.appendChild(safeScript);
                                        } else {
                                            // Ana sayfa için IIFE ile çalıştır
                                            safeScript.text = `
                                                try {
                                                    (function() {
                                                        ${scriptContent}
                                                    })();
                                                } catch(e) {
                                                    console.warn("Widget script çalıştırma hatası:", e);
                                                }
                                            `;
                                            document.body.appendChild(safeScript);
                                        }
                                        
                                        console.log(`Script ${index + 1}/${scripts.length} başarıyla işlendi (iç script)`);
                                    }
                                } catch (err) {
                                    console.error(`Script ${index + 1}/${scripts.length} çalıştırma hatası:`, err);
                                }
                            });
                        }
                    } catch (err) {
                        console.error(`Widget ${widgetId} script işleme genel hatası:`, err);
                    }
                    
                    console.log(`Widget ${widgetId} başarıyla yüklendi`);
                })
                .catch(error => {
                    console.error(`Widget ${widgetId} yükleme hatası:`, error);
                    container.innerHTML = `<div class="alert alert-danger">Widget yüklenirken hata: ${error.message}</div>`;
                });
        };
        
        // Eski fonksiyon adıyla uyumluluk için
        window.loadTenantWidget = window.studioLoadWidget;
    }
    
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
                    
                    const blockId = widget.id;
                    let tenantWidgetId = blockId;
                    
                    // ID prefix'ini temizle
                    if (typeof tenantWidgetId === 'string' && tenantWidgetId.startsWith('tenant-widget-')) {
                        tenantWidgetId = tenantWidgetId.replace('tenant-widget-', '');
                    }
                    
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
        
        // Global widget yükleyiciyi ayarla
        setupGlobalWidgetLoader();
        
        // Widget bloklarını yükle
        loadWidgetBlocks(editor);
        
        // Editor yüklendiğinde mevcut widget'ları işle
        editor.on('load', () => {
            if (window.StudioWidgetLoader && typeof window.StudioWidgetLoader.processExistingWidgets === 'function') {
                window.StudioWidgetLoader.processExistingWidgets(editor);
            }
        });
        
        // Bileşen ekleme olayı
        editor.on('component:add', (component) => {
            const type = component.get('type');
            
            if (type === 'widget-embed' || component.getAttributes()['data-tenant-widget-id']) {
                component.set('type', 'widget-embed');
                
                // Görünümü güncelle
                const view = component.view;
                if (view && typeof view.onRender === 'function') {
                    setTimeout(() => view.onRender(), 50);
                }
                
                // Widget ID'sini al
                const tenantWidgetId = component.getAttributes()['data-tenant-widget-id'] || 
                                      component.getAttributes()['data-widget-id'];
                                      
                if (tenantWidgetId) {
                    // Widget ID'sini komponent özelliği olarak ayarla
                    component.set('tenant_widget_id', tenantWidgetId);
                    
                    // Widget içeriğini yükle
                    setTimeout(() => {
                        if (window.studioLoadWidget) {
                            window.studioLoadWidget(tenantWidgetId);
                        }
                    }, 100);
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
        loadWidgetBlocks: loadWidgetBlocks
    };
})();