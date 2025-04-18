/**
 * Studio Editor Ana Modül
 * Tüm modülleri birleştiren ve başlatan ana dosya
 */

window.initStudioEditor = function (config) {
    console.log('Studio Editor başlatılıyor:', config);
    
    try {
        if (!config || !config.moduleId || config.moduleId <= 0) {
            console.error('Geçersiz konfigürasyon veya modül ID:', config);
            return null;
        }
        
        // Mevcut yükleme göstergesini temizle
        const existingLoader = document.querySelector('.studio-loader');
        if (existingLoader) {
            existingLoader.remove();
        }
        
        // İlerleme durumu
        let currentProgress = 0;
        
        // Yükleme aşamaları
        const loadingStages = [
            { text: "Bileşenler yükleniyor...", icon: "fa-puzzle-piece" },
            { text: "Modüller hazırlanıyor...", icon: "fa-cube" },
            { text: "Görsel motoru başlatılıyor...", icon: "fa-paint-brush" },
            { text: "Blok sistemi oluşturuluyor...", icon: "fa-cubes" },
            { text: "İçerik analiz ediliyor...", icon: "fa-microscope" },
            { text: "Düzenleme araçları hazırlanıyor...", icon: "fa-tools" },
            { text: "Stil şablonları yükleniyor...", icon: "fa-palette" },
            { text: "Widget sistemi entegre ediliyor...", icon: "fa-cogs" },
            { text: "Kullanıcı arayüzü optimize ediliyor...", icon: "fa-sliders-h" },
            { text: "Son hazırlıklar tamamlanıyor...", icon: "fa-check-double" }
        ];
        
        // Yükleme göstergesi ekle
        const loaderElement = document.createElement('div');
        loaderElement.className = 'studio-loader';
        loaderElement.style.position = 'fixed';
        loaderElement.style.top = '0';
        loaderElement.style.left = '0';
        loaderElement.style.width = '100%';
        loaderElement.style.height = '100%';
        loaderElement.style.backgroundColor = 'rgba(255, 255, 255, 0.95)';
        loaderElement.style.display = 'flex';
        loaderElement.style.alignItems = 'center';
        loaderElement.style.justifyContent = 'center';
        loaderElement.style.zIndex = '10000';
        loaderElement.style.transition = 'opacity 0.3s ease';
        
        loaderElement.innerHTML = `
            <div class="studio-loader-content" style="text-align: center; background-color: #ffffff; padding: 40px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); max-width: 500px; width: 90%;">
                <div style="margin-bottom: 30px;">
                    <div style="font-size: 28px; margin-bottom: 15px; color: #206bc4;">
                        <i class="fas fa-wand-magic-sparkles"></i> Studio Editor
                    </div>
                    <div id="loader-spinner" style="margin-bottom: 25px;">
                        <div class="spinner-grow text-primary mx-1" role="status" style="width: 0.8rem; height: 0.8rem;"></div>
                        <div class="spinner-grow text-primary mx-1" role="status" style="width: 0.8rem; height: 0.8rem; animation-delay: 0.2s;"></div>
                        <div class="spinner-grow text-primary mx-1" role="status" style="width: 0.8rem; height: 0.8rem; animation-delay: 0.4s;"></div>
                    </div>
                </div>
                
                <div id="loading-icon" style="margin-bottom: 20px; font-size: 36px; color: #206bc4;">
                    <i class="fas fa-puzzle-piece"></i>
                </div>
                
                <h4 id="loading-text" style="margin-bottom: 30px; color: #334155; font-weight: 500;">Bileşenler yükleniyor...</h4>
                
                <div style="background-color: #f0f5fa; height: 12px; border-radius: 6px; overflow: hidden; margin-bottom: 15px;">
                    <div id="loading-progress" style="height: 100%; width: 0%; background-color: #206bc4; border-radius: 6px; transition: width 0.5s ease;"></div>
                </div>
                
                <div id="loading-status" style="font-size: 13px; color: #64748b;">
                    %0
                </div>
            </div>
        `;
        
        document.body.appendChild(loaderElement);
        
        // İlerleme çubuğunu güncelleme fonksiyonu
        function updateProgress(stage, isCompleted = false) {
            const loadingText = document.getElementById('loading-text');
            const loadingIcon = document.getElementById('loading-icon');
            const loadingProgress = document.getElementById('loading-progress');
            const loadingStatus = document.getElementById('loading-status');
            
            if (!loadingText || !loadingIcon || !loadingProgress || !loadingStatus) return;
            
            if (isCompleted) {
                // Tamamlandı durumu
                loadingText.textContent = "Studio Editor Hazır!";
                loadingIcon.innerHTML = '<i class="fas fa-check-circle"></i>';
                loadingProgress.style.width = "100%";
                loadingStatus.textContent = "%100";
                return;
            }
            
            // Normal ilerleme
            if (stage < loadingStages.length) {
                loadingText.textContent = loadingStages[stage].text;
                loadingIcon.innerHTML = `<i class="fas ${loadingStages[stage].icon}"></i>`;
                currentProgress = Math.round((stage + 1) * (90 / loadingStages.length)); // %90'a kadar ilerle
                loadingProgress.style.width = `${currentProgress}%`;
                loadingStatus.textContent = `%${currentProgress}`;
            }
        }
        
        // Aşamalı ilerleme
        function startLoading() {
            let currentStage = 0;
            
            function nextStage() {
                if (currentStage >= loadingStages.length) return;
                
                updateProgress(currentStage);
                currentStage++;
                
                if (currentStage < loadingStages.length) {
                    setTimeout(nextStage, 700);
                }
            }
            
            // İlk aşamayı başlat
            nextStage();
        }
        
        // Yükleme başlat
        startLoading();
                                
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
                sectors: [
                    {
                        name: 'Boyut',
                        open: true,
                        properties: [
                            'width', 'height', 'max-width', 'min-height', 'margin', 'padding'
                        ]
                    },
                    {
                        name: 'Düzen',
                        open: false,
                        properties: [
                            'display', 'position', 'top', 'right', 'bottom', 'left', 'float', 'clear', 'z-index'
                        ]
                    },
                    {
                        name: 'Flex',
                        open: false,
                        properties: [
                            'flex-direction', 'flex-wrap', 'justify-content', 'align-items', 'align-content', 'order', 'flex-basis', 'flex-grow', 'flex-shrink', 'align-self'
                        ]
                    },
                    {
                        name: 'Tipografi',
                        open: false,
                        properties: [
                            'font-family', 'font-size', 'font-weight', 'letter-spacing', 'color', 'line-height', 'text-align', 'text-decoration', 'text-shadow'
                        ]
                    },
                    {
                        name: 'Dekorasyon',
                        open: false,
                        properties: [
                            'background-color', 'border', 'border-radius', 'box-shadow'
                        ]
                    }
                ]
            },
            layerManager: {
                appendTo: "#layers-container",
            },
            traitManager: {
                appendTo: "#traits-container",
            },
            deviceManager: {
                devices: [
                    {
                        name: "Desktop",
                        width: "",
                    },
                    {
                        name: "Tablet",
                        width: "768px",
                        widthMedia: "992px",
                    },
                    {
                        name: "Mobile",
                        width: "320px",
                        widthMedia: "480px",
                    },
                ],
            },
            canvas: {
                scripts: [
                    "https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js",
                ],
                styles: [
                    "https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css",
                    "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css",
                ]
            },
            protectedCss: '' // Koruma altındaki CSS'i devre dışı bırak
        });

        // Widget bileşeni tipini kaydet
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

        // Editor yüklendiğinde CSS tekrarlama sorununu çöz
        editor.on('load', function() {
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
            
            // Widget bileşenlerini tanımla
            registerWidgetComponents(editor);
        });

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

        // İçerik yükleme işlemi
        loadContent(editor, config);

        // Widget bileşenlerini kaydet ve tanımla
        function registerWidgetComponents(editor) {
            // Drop olayını izle ve widget elementlerini tanımla
            editor.on('block:drag:stop', (component, block) => {
                if (!component) return;
                
                // Block widget mi kontrol et - ID kontrol et
                const blockId = block?.get('id');
                if (!blockId || !blockId.startsWith('widget-')) return;
                
                // Widget ID'yi al (widget-123 formatındaki ID'den 123 kısmını çıkar)
                const widgetId = blockId.replace('widget-', '');
                
                // Widget tipinde bileşenleri bul ve doğru şekilde işaretle
                function processComponent(comp) {
                    if (!comp) return;
                    
                    // Data attribute'lardan widget tipini kontrol et
                    const attrs = comp.getAttributes();
                    if (attrs['data-type'] === 'widget' || attrs['data-widget-id']) {
                        // Komponentin tipini 'widget' olarak ayarla ve widget_id özelliğini ekle
                        comp.set('type', 'widget');
                        comp.set('widget_id', attrs['data-widget-id'] || widgetId);
                        
                        // Widget özelliklerini güncelle - başlık, sınır renkleri vs.
                        comp.addAttributes({
                            'data-widget-id': attrs['data-widget-id'] || widgetId,
                            'data-type': 'widget'
                        });
                        
                        console.log(`Widget komponenti ayarlandı: ${widgetId}`);
                    }
                    
                    // Alt bileşenleri kontrol et
                    if (comp.get('components')) {
                        comp.get('components').each(child => {
                            processComponent(child);
                        });
                    }
                }
                
                // Ana bileşeni işle
                processComponent(component);
            });
            
            // Widget API'sini çağır ve mevcut widget'ları yükle
            fetch('/admin/studio/api/widgets')
                .then(response => response.json())
                .then(data => {
                    if (data.widgets && Array.isArray(data.widgets)) {
                        console.log(`${data.widgets.length} adet widget bulundu`);
                        
                        // Widget'lar için blok tanımla
                        data.widgets.forEach(widget => {
                            const blockId = `widget-${widget.id}`;
                            const blockContent = `<div data-widget-id="${widget.id}" data-type="widget" class="gjs-widget-wrapper">
                                ${widget.content_html || `<div class="widget-placeholder">Widget: ${widget.name}</div>`}
                            </div>`;
                            
                            // Widget bloğunu kaydet
                            editor.BlockManager.add(blockId, {
                                label: widget.name,
                                category: widget.category || 'widget',
                                attributes: { class: 'fa fa-puzzle-piece' },
                                content: blockContent
                            });
                        });
                        
                        // Blokları güncelle - StudioBlocks modülü mevcut ise bunu çağır
                        if (window.StudioBlocks && typeof window.StudioBlocks.updateBlocksInCategories === 'function') {
                            setTimeout(() => {
                                window.StudioBlocks.updateBlocksInCategories(editor);
                            }, 500);
                        }
                    }
                })
                .catch(error => {
                    console.error("Widget yüklenirken hata:", error);
                });
        }

        // Editor'ü yükleme olayını dinle
        editor.on('load', function() {
            console.log('Editor loaded event triggered');

            // Tamamlandı göster (%100)
            updateProgress(0, true);
            
            // Bekle ve animasyonu gizle
            setTimeout(() => {
                const loaderElement = document.querySelector('.studio-loader');
                if (loaderElement) {
                    loaderElement.style.opacity = '0';
                    setTimeout(() => {
                        if (loaderElement && loaderElement.parentNode) {
                            loaderElement.parentNode.removeChild(loaderElement);
                        }
                    }, 300);
                }
            }, 800);
            
            // Blokları kaydet
            if (window.StudioBlocks && typeof window.StudioBlocks.registerBlocks === 'function') {
                window.StudioBlocks.registerBlocks(editor);
            }
            
            // UI bileşenlerini ayarla
            if (window.StudioUI && typeof window.StudioUI.setupUI === 'function') {
                window.StudioUI.setupUI(editor);
            }
            
            // Eylem butonlarını ayarla
            if (window.StudioActions && typeof window.StudioActions.setupActions === 'function') {
                window.StudioActions.setupActions(editor, config);
            }
            
            // Editor'e özel komutlar ekle
            editor.runCommand('canvas-clear');
            
            // Custom event tetikle
            document.dispatchEvent(new CustomEvent('editor:loaded', { detail: { editor } }));
        });
        
        // Global erişim için editörü kaydet
        window.studioEditor = editor;
        
        return editor;
        
    } catch (error) {
        console.error('Studio Editor başlatılırken kritik hata:', error);
        return null;
    }
};

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
            const jsContentEl = document.getElementById('js-content');
            
            let content = htmlContentEl ? htmlContentEl.value : '';
            
            if (!content || content.trim() === '' || content.trim() === '<body></body>' || content.length < 20) {
                console.warn('Geçerli içerik bulunamadı. Varsayılan içerik yükleniyor...');
                
                content = `
                <div class="container py-4">
                    <div class="row">
                        <div class="col-12">
                            <h1 class="mb-4">Yeni Sayfa</h1>
                            <p class="lead">Bu sayfayı düzenlemek için sol taraftaki bileşenleri kullanabilirsiniz.</p>
                            <div class="alert alert-info mt-4">
                                <i class="fas fa-info-circle me-2"></i> Studio Editor ile görsel düzenleme yapabilirsiniz.
                                Düzenlemelerinizi kaydetmek için sağ üstteki Kaydet butonunu kullanın.
                            </div>
                        </div>
                    </div>
                </div>`;
            }
            
            editor.setComponents(content);
            console.log('İçerik editöre başarıyla yüklendi');
            
            if (cssContentEl && cssContentEl.value) {
                editor.setStyle(cssContentEl.value);
            }
            
            setTimeout(() => {
                if (window.StudioWidgetManager && typeof window.StudioWidgetManager.processExistingWidgets === 'function') {
                    window.StudioWidgetManager.processExistingWidgets(editor);
                }
            }, 1000);
            
        } catch (error) {
            console.error('İçerik yüklenirken hata oluştu:', error);
        }
    }, 500);
}