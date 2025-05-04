/**
 * Studio Editor - Editor Kurulum Modülü
 * Editor yapılandırması ve kurulumu
 */

// Bu dosya StudioEditorSetup modülünü sağlar
// Global değişkenler app.js'de zaten tanımlanmıştır, burada tekrar tanımlamaya gerek yok

window.StudioEditorSetup = (function() {
    /**
     * Editor'ı yapılandır ve başlat
     * @param {Object} config - Yapılandırma parametreleri
     * @returns {Object} GrapesJS editor örneği
     */
    function initEditor(config) {
        // Editör zaten başlatılmışsa mevcut örneği döndür
        if (window.__STUDIO_EDITOR_INSTANCE) {
            console.log('Editor zaten başlatılmış, mevcut örnek döndürülüyor.');
            return window.__STUDIO_EDITOR_INSTANCE;
        }
        
        console.log('Studio Editor başlatılıyor:', config);
        
        if (!config || !config.moduleId || config.moduleId <= 0) {
            console.error('Geçersiz konfigürasyon veya modül ID:', config);
            return null;
        }
        
        // Yükleme göstergesini başlat
        if (window.StudioLoader && typeof window.StudioLoader.show === 'function') {
            window.StudioLoader.show();
        }
        
        try {
            // GrapesJS Editor yapılandırması
            let editor = grapesjs.init({
                container: "#" + config.elementId,
                fromElement: false,
                height: "100%",
                width: "100%",
                storageManager: false,
                panels: { defaults: [] },
                blockManager: {
                    appendTo: '#blocks-container'
                },
                styleManager: {
                    appendTo: "#styles-container",
                    sectors: window.StudioConfig.getConfig('styleManagerSectors')
                },
                layerManager: {
                    appendTo: "#layers-container",
                },
                traitManager: {
                    appendTo: "#traits-container",
                },
                deviceManager: {
                    devices: window.StudioConfig.getConfig('devices')
                },
                canvas: {
                    scripts: window.StudioConfig.getConfig('canvas.scripts'),
                    styles: window.StudioConfig.getConfig('canvas.styles')
                },
                protectedCss: '' // Koruma altındaki CSS'i devre dışı bırak
            });
            
            // Widget bileşeni tipini kaydet
            setupComponentTypes(editor);
            
            // Editor komutlarını ekle
            setupCommands(editor);
            
            // Editor yükleme olayını dinle
            editor.on('load', function() {
                console.log('Editor yüklendi');
                
                // CSS tekrarlama sorununu çöz
                fixCssIssues(editor);
                
                // Widget sistemini kur
                if (window.StudioWidgetManager && typeof window.StudioWidgetManager.setup === 'function') {
                    window.StudioWidgetManager.setup(editor);
                }
                
                // Widget-embed component tipini kaydet
                if (window.StudioWidgetLoader && typeof window.StudioWidgetLoader.registerWidgetEmbedComponent === 'function') {
                    window.StudioWidgetLoader.registerWidgetEmbedComponent(editor);
                }
                
                // Yükleme göstergesini gizle
                if (window.StudioLoader && typeof window.StudioLoader.hide === 'function') {
                    window.StudioLoader.hide();
                }
                
                // Blokları kaydet
                if (window.StudioBlocks && typeof window.StudioBlocks.registerBlocks === 'function') {
                    window.StudioBlocks.registerBlocks(editor);
                }
                
                // Custom event tetikle
                document.dispatchEvent(new CustomEvent('editor:loaded', { detail: { editor } }));
                
                // Embed silindiğinde tüm ilişkili model ve DOM'u temizle
                editor.on('component:remove', component => {
                    // Widget embed modelini bul
                    let curr = component;
                    while (curr && curr.get('type') !== 'widget-embed' && curr.get('type') !== 'module-widget') curr = curr.parent();
                    if (!curr) return;
                    
                    const widgetModel = curr;
                    
                    // Widget tipi ve ID'yi al
                    const widgetType = widgetModel.get('type');
                    
                    if (widgetType === 'widget-embed') {
                        const widgetId = widgetModel.get('tenant_widget_id') || widgetModel.getAttributes()['data-tenant-widget-id'] || widgetModel.getAttributes()['data-widget-id'];
                        if (window._loadedWidgets) window._loadedWidgets.delete(widgetId);
                        
                        // Canvas DOM'dan embed elemanını sil
                        const frameEl = editor.Canvas.getFrameEl();
                        const doc = frameEl.contentDocument || frameEl.contentWindow.document;
                        const embedEl = doc.querySelector(`[data-tenant-widget-id="${widgetId}"]`);
                        if (embedEl) embedEl.remove();
                        
                        // Model'deki wrapper veya embed'i kaldır
                        const wrapperModel = widgetModel.parent();
                        if (wrapperModel && /\bcol-md-\d+\b/.test(wrapperModel.getAttributes().class || '')) {
                            wrapperModel.destroy();
                        } else {
                            widgetModel.destroy();
                        }
                        
                        // Blok butonlarını aktifleştir
                        window.StudioBlockManager.updateBlocksInCategories(editor);
                    } 
                    else if (widgetType === 'module-widget') {
                        const moduleId = widgetModel.get('widget_module_id') || widgetModel.getAttributes()['data-widget-module-id'];
                        if (window._loadedModules) window._loadedModules.delete(moduleId);
                        
                        // Canvas DOM'dan module elemanını sil
                        const frameEl = editor.Canvas.getFrameEl();
                        const doc = frameEl.contentDocument || frameEl.contentWindow.document;
                        const moduleEl = doc.querySelector(`[data-widget-module-id="${moduleId}"]`);
                        if (moduleEl) moduleEl.remove();
                        
                        // Model'deki wrapper veya module'ü kaldır
                        const wrapperModel = widgetModel.parent();
                        if (wrapperModel && /\bcol-md-\d+\b/.test(wrapperModel.getAttributes().class || '')) {
                            wrapperModel.destroy();
                        } else {
                            widgetModel.destroy();
                        }
                        
                        // Blok butonlarını aktifleştir
                        window.StudioBlockManager.updateBlocksInCategories(editor);
                    }
                    
                    // HTML içeriğini sanitize ve güncelle
                    const htmlEl = document.getElementById('html-content');
                    if (htmlEl) {
                        htmlEl.value = `<body>${editor.getHtml()}</body>`;
                    }
                });
            });
            
            return editor;
            
        } catch (error) {
            console.error('Studio Editor başlatılırken kritik hata:', error);
            window.__STUDIO_EDITOR_INITIALIZED = false;
            
            if (window.StudioLoader && typeof window.StudioLoader.hide === 'function') {
                window.StudioLoader.hide();
            }
            
            if (window.StudioNotification && typeof window.StudioNotification.error === 'function') {
                window.StudioNotification.error('Editor başlatılırken bir hata oluştu!');
            }
            
            return null;
        }
    }
    
    /**
     * İçeriği yükle
     * @param {Object} editor - GrapesJS editor örneği
     * @param {Object} config - Yapılandırma
     */
    function loadContent(editor, config) {
        setTimeout(() => {
            try {
                console.log('İçerik yükleme işlemi başlatılıyor...');
                
                const htmlContentEl = document.getElementById('html-content');
                const cssContentEl = document.getElementById('css-content');
                
                let content = htmlContentEl ? htmlContentEl.value : '';
                
                // İçerik kontrolü - geçerli HTML içeriği var mı?
                if (!content || content.trim() === '' || content.trim() === '<body></body>' || content.length < 20) {
                    console.warn('Geçerli içerik bulunamadı. Varsayılan içerik yükleniyor...');
                    content = window.StudioConfig.getConfig('defaultHtml');
                }

                // Module widget'ları için [[module:XX]] formatı kontrol et
                const moduleRegex = /\[\[module:(\d+)\]\]/g;
                let moduleMatch;
                const moduleIds = [];
                
                // Önce module ID'lerini topla
                while ((moduleMatch = moduleRegex.exec(content)) !== null) {
                    moduleIds.push(moduleMatch[1]);
                }
                
                // Module widget'ları için gerçek HTML yapısını oluştur
                if (moduleIds.length > 0) {
                    moduleIds.forEach(moduleId => {
                        const modulePattern = new RegExp(`\\[\\[module:${moduleId}\\]\\]`, 'g');
                        content = content.replace(modulePattern, `
                            <div class="module-widget-container" data-widget-module-id="${moduleId}" id="module-widget-${moduleId}">
                                <div class="module-widget-content-placeholder" id="module-content-${moduleId}">
                                    <div class="widget-loading" style="text-align:center; padding:20px;">
                                        <i class="fa fa-spin fa-spinner"></i> Modül içeriği yükleniyor...
                                    </div>
                                </div>
                            </div>
                        `);
                    });
                }

                // Shortcode ve widget embed referanslarını dönüştür
                if (window.StudioHtmlParser) {
                    if (typeof window.StudioHtmlParser.convertAllWidgetReferencesToEmbeds === 'function') {
                        content = window.StudioHtmlParser.convertAllWidgetReferencesToEmbeds(content);
                    }
                    if (typeof window.StudioHtmlParser.parseInput === 'function') {
                        content = window.StudioHtmlParser.parseInput(content);
                    }
                }

                // İçeriği editöre yükle
                editor.setComponents(content);
                console.log('İçerik editöre başarıyla yüklendi');
                
                // Widget-embed component tipini kaydet
                if (window.StudioWidgetLoader && typeof window.StudioWidgetLoader.registerWidgetEmbedComponent === 'function') {
                    window.StudioWidgetLoader.registerWidgetEmbedComponent(editor);
                }
                
                // CSS içeriği
                if (cssContentEl && cssContentEl.value) {
                    editor.setStyle(cssContentEl.value);
                }
                
                // Widget embed elementlerini içerik yüklendikten sonra işle
                if (window.StudioWidgetLoader && typeof window.StudioWidgetLoader.processExistingWidgets === 'function') {
                    window.StudioWidgetLoader.processExistingWidgets(editor);
                }
                
                // Canvas yükleme sonrası tüm widget embed'ler için ek işleme (disable, load)
                if (window.StudioWidgetLoader && typeof window.StudioWidgetLoader.processWidgetEmbeds === 'function') {
                    window.StudioWidgetLoader.processWidgetEmbeds(editor);
                }
                
                // İçerik yüklendikten sonra module widget'ları otomatik olarak yükle
                setTimeout(() => {
                    // Canvas'ta tüm module widget'ları bul ve yükle
                    const moduleComponents = editor.DomComponents.getWrapper().find('[data-widget-module-id]');
                    if (moduleComponents && moduleComponents.length > 0) {
                        console.log(`${moduleComponents.length} adet module widget bulundu`);
                        
                        moduleComponents.forEach(component => {
                            const moduleId = component.getAttributes()['data-widget-module-id'];
                            if (moduleId && window.studioLoadModuleWidget) {
                                console.log(`Module widget #${moduleId} otomatik yükleniyor...`);
                                window.studioLoadModuleWidget(moduleId);
                            }
                        });
                    }
                }, 1000);
                
            } catch (error) {
                console.error('İçerik yüklenirken hata oluştu:', error);
                if (window.StudioNotification && typeof window.StudioNotification.error === 'function') {
                    window.StudioNotification.error('İçerik yüklenirken bir hata oluştu!');
                }
            }
        }, 500);
    }
    
    /**
     * Bileşen tiplerini ayarla
     * @param {Object} editor - GrapesJS editor örneği
     */
    function setupComponentTypes(editor) {
        // Widget bileşeni tipi
        editor.DomComponents.addType('widget', {
            model: {
                defaults: {
                    name: 'Widget',
                    draggable: true,
                    droppable: false,
                    editable: true,
                    attributes: {
                        'data-type': 'widget'
                    },
                    traits: [
                        {
                            type: 'text',
                            name: 'widget_id',
                            label: 'Widget ID',
                            changeProp: true
                        }
                    ],
                    
                    // Widget ID değiştiğinde içeriği güncelle
                    init() {
                        this.on('change:widget_id', this.updateWidgetContent);
                    },
                    
                    updateWidgetContent() {
                        const widgetId = this.get('widget_id');
                        if (widgetId) {
                            // Widget içeriği buraya yüklenecek
                            console.log(`Widget ID ${widgetId} için içerik güncellenecek`);
                        }
                    }
                }
            },
            
            view: {
                events: {
                    'dblclick': 'onDblClick'
                },
                
                onDblClick() {
                    // Widget ID'sini doğru şekilde al
                    const model = this.model;
                    const widgetId = model.get('widget_id') || model.getAttributes()['data-widget-id'];
                    
                    if (widgetId) {
                        // Widget düzenleme sayfasına yönlendir
                        window.open(`/admin/widgetmanagement/items/${widgetId}`, '_blank');
                    }
                },
                
                onRender() {
                    // Widget görünümü için stil ekle
                    const el = this.el;
                    if (el) {
                        el.style.border = '2px dashed #2a6dcf';
                        el.style.padding = '5px';
                        el.style.borderRadius = '3px';
                        el.style.position = 'relative';
                        
                        // Widget etiketi ekle
                        if (!el.querySelector('.widget-label')) {
                            const label = document.createElement('div');
                            label.className = 'widget-label';
                            label.innerHTML = '<i class="fa fa-puzzle-piece"></i> Widget';
                            label.style.position = 'absolute';
                            label.style.top = '-15px';
                            label.style.left = '10px';
                            label.style.backgroundColor = '#2a6dcf';
                            label.style.color = 'white';
                            label.style.padding = '2px 6px';
                            label.style.fontSize = '10px';
                            label.style.borderRadius = '3px';
                            label.style.zIndex = '1';
                            
                            el.appendChild(label);
                        }
                    }
                }
            }
        });
        
        // Module widget bileşeni tipini ekle
        editor.DomComponents.addType('module-widget', {
            model: {
                defaults: {
                    name: 'Module Widget',
                    draggable: true,
                    droppable: false,
                    editable: false,
                    attributes: {
                        'data-type': 'module-widget'
                    },
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
                    },
                    
                    updateModuleId() {
                        const moduleId = this.get('widget_module_id');
                        if (moduleId) {
                            this.setAttributes({
                                'data-widget-module-id': moduleId,
                                'id': `module-widget-${moduleId}`
                            });
                            
                            // İçeriği yükle
                            setTimeout(() => {
                                if (window.studioLoadModuleWidget) {
                                    window.studioLoadModuleWidget(moduleId);
                                }
                            }, 100);
                        }
                    },
                    
                    // Module widget'ı [[module:XX]] formatında kaydet
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
    
    /**
     * CSS tekrarlama sorunlarını düzelt
     * @param {Object} editor - GrapesJS editor örneği
     */
    function fixCssIssues(editor) {
        // CSS'i çekme metodunu tamamen override et
        const originalGetCss = editor.getCss;
        editor.getCss = function(opts = {}) {
            // Her zaman avoidProtected: true kullan
            opts.avoidProtected = true;
            
            // Orijinal metodu çağır
            let css = originalGetCss.call(this, opts);
            
            // Yine de box-sizing ve margin sıfırlama kodu varsa kaldır
            return css.replace(/\*\s*{\s*box-sizing:\s*border-box;\s*}\s*body\s*{\s*margin(-top|-right|-bottom|-left)?:?\s*0(px)?;?\s*}/g, '');
        };
        
        // CSS Composer Config'ini değiştir
        if (editor.CssComposer) {
            editor.CssComposer.getConfig().protectedCss = '';
        }
        
        // Stil Composer'ın buildCSS metodunu da override et (başka bir yaklaşım)
        if (editor.CssComposer && editor.CssComposer.buildCSS) {
            const originalBuildCSS = editor.CssComposer.buildCSS;
            editor.CssComposer.buildCSS = function() {
                const result = originalBuildCSS.apply(this, arguments);
                return result.replace(/\*\s*{\s*box-sizing:\s*border-box;\s*}\s*body\s*{\s*margin(-top|-right|-bottom|-left)?:?\s*0(px)?;?\s*}/g, '');
            };
        }
    }
    
    /**
     * Editor komutlarını ayarla
     * @param {Object} editor - GrapesJS editor örneği
     */
    function setupCommands(editor) {
        // Canvası görünür kılma komutu ekle
        editor.Commands.add('sw-visibility', {
            state: false,
            
            run(editor) {
                const $ = editor.$;
                const state = !this.state;
                this.state = state;
                
                const canvas = editor.Canvas;
                const frames = canvas.getFrames();
                
                frames.forEach(frame => {
                    const $elFrame = $(frame.getBody());
                    const $allElsFrame = $elFrame.find('*');
                    
                    if (state) {
                        $allElsFrame.each((i, el) => {
                            const $el = $(el);
                            const pfx = $el.css('outline-style') || 'none';
                            
                            if (pfx === 'none') {
                                $el.css('outline', '1px solid rgba(170, 170, 170, 0.7)');
                            }
                        });
                    } else {
                        $allElsFrame.css('outline', '');
                    }
                });
                
                // Buton aktif durumunu güncelle
                const btn = document.getElementById('sw-visibility');
                if (btn) {
                    state ? btn.classList.add('active') : btn.classList.remove('active');
                }
                
                return state;
            },
            
            stop() {
                this.state = false;
                this.run(editor);
            }
        });
        
        // Canvas temizleme komutu ekle
        editor.Commands.add('canvas-clear', {
            run(editor) {
                editor.DomComponents.clear();
                editor.CssComposer.clear();
                // Widget yükleme durumu ve blok butonlarını resetle
                if (window._loadedWidgets) {
                    window._loadedWidgets.clear();
                }
                document.querySelectorAll('.block-item.disabled').forEach(blockEl => {
                    blockEl.classList.remove('disabled');
                    const badge = blockEl.querySelector('.gjs-block-type-badge');
                    if (badge) {
                        badge.classList.replace('inactive', 'active');
                        badge.textContent = 'Aktif';
                    }
                });
            }
        });
    }
    
    return {
        initEditor: initEditor,
        loadContent: loadContent
    };
})();