/**
 * Studio Editor - Editor Kurulum Modülü
 * Editor yapılandırması ve kurulumu
 */

window.StudioEditorSetup = (function() {
    /**
     * Editor'ı yapılandır ve başlat
     * @param {Object} config - Yapılandırma parametreleri
     * @returns {Object} GrapesJS editor örneği
     */
    function initEditor(config) {
        console.log('Studio Editor başlatılıyor:', config);
        
        if (!config || !config.moduleId || config.moduleId <= 0) {
            console.error('Geçersiz konfigürasyon veya modül ID:', config);
            return null;
        }
        
        // Yükleme göstergesini başlat
        window.StudioLoader.show();
        
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
                
                // Yükleme göstergesini gizle
                window.StudioLoader.hide();
                
                // Custom event tetikle
                document.dispatchEvent(new CustomEvent('editor:loaded', { detail: { editor } }));
            });
            
            return editor;
            
        } catch (error) {
            console.error('Studio Editor başlatılırken kritik hata:', error);
            window.StudioLoader.hide();
            window.StudioNotification.error('Editor başlatılırken bir hata oluştu!');
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
                
                // İçeriği editöre yükle
                editor.setComponents(content);
                console.log('İçerik editöre başarıyla yüklendi');
                
                // CSS içeriği
                if (cssContentEl && cssContentEl.value) {
                    editor.setStyle(cssContentEl.value);
                }
                
            } catch (error) {
                console.error('İçerik yüklenirken hata oluştu:', error);
                window.StudioNotification.error('İçerik yüklenirken bir hata oluştu!');
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
            }
        });
    }
    
    return {
        initEditor: initEditor,
        loadContent: loadContent
    };
})();