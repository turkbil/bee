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
        if (window.__STUDIO_EDITOR_INSTANCE) {
            console.log('Editor zaten başlatılmış, mevcut örnek döndürülüyor.');
            return window.__STUDIO_EDITOR_INSTANCE;
        }
        
        console.log('Studio Editor başlatılıyor:', config);
        
        if (!config || !config.moduleId || config.moduleId <= 0) {
            console.error('Geçersiz konfigürasyon veya modül ID:', config);
            return null;
        }
        
        if (window.StudioLoader && typeof window.StudioLoader.show === 'function') {
            window.StudioLoader.show();
        }
        
        try {
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
                protectedCss: ''
            });
            
            setupComponentTypes(editor);
            setupCommands(editor);
            setupTinyMCERTE(editor);
            
            editor.on('component:add', component => {
                if (component.get('type') === 'module-widget' || 
                    (component.getAttributes && component.getAttributes()['data-widget-module-id'])) {
                    
                    const moduleId = component.get('widget_module_id') || 
                                    component.getAttributes()['data-widget-module-id'];
                    
                    if (moduleId) {
                        console.log(`Module widget #${moduleId} eklendi, hemen işleniyor...`);
                        
                        component.set('type', 'module-widget');
                        component.set('widget_module_id', moduleId);
                        
                        setTimeout(() => {
                            if (window.StudioWidgetLoader && typeof window.StudioWidgetLoader.processWidgetEmbeds === 'function') {
                                window.StudioWidgetLoader.processWidgetEmbeds(editor);
                            }
                            
                            if (window.studioLoadModuleWidget) {
                                window.studioLoadModuleWidget(moduleId);
                            }
                        }, 0);
                    }
                }
                
                else if (component.get('type') === 'widget-embed' || 
                        (component.getAttributes && component.getAttributes()['data-tenant-widget-id'])) {
                    
                    const widgetId = component.get('tenant_widget_id') || 
                                    component.getAttributes()['data-tenant-widget-id'];
                    
                    if (widgetId) {
                        console.log(`Widget #${widgetId} eklendi, hemen işleniyor...`);
                        
                        component.set('type', 'widget-embed');
                        component.set('tenant_widget_id', widgetId);
                        
                        setTimeout(() => {
                            if (window.StudioWidgetLoader && typeof window.StudioWidgetLoader.processWidgetEmbeds === 'function') {
                                window.StudioWidgetLoader.processWidgetEmbeds(editor);
                            }
                            
                            if (window.studioLoadWidget) {
                                window.studioLoadWidget(widgetId);
                            }
                        }, 0);
                    }
                }
            });

            editor.on('canvas:drop', (droppedModel) => {
                console.log('Canvas Drop olayı tetiklendi, widget işlemleri yapılıyor...');
                
                setTimeout(() => {
                    if (window.StudioWidgetLoader && typeof window.StudioWidgetLoader.processWidgetEmbeds === 'function') {
                        window.StudioWidgetLoader.processWidgetEmbeds(editor);
                    }
                }, 50);
            });
            
            editor.on('load', function() {
                console.log('Editor yüklendi');
                
                fixCssAndHtmlIssues(editor);
                
                if (window.StudioWidgetManager && typeof window.StudioWidgetManager.setup === 'function') {
                    window.StudioWidgetManager.setup(editor);
                }
                
                if (window.StudioWidgetLoader && typeof window.StudioWidgetLoader.registerWidgetEmbedComponent === 'function') {
                    window.StudioWidgetLoader.registerWidgetEmbedComponent(editor);
                }
                
                if (window.StudioLoader && typeof window.StudioLoader.hide === 'function') {
                    window.StudioLoader.hide();
                }
                
                if (window.StudioBlocks && typeof window.StudioBlocks.registerBlocks === 'function') {
                    window.StudioBlocks.registerBlocks(editor);
                }
                
                document.dispatchEvent(new CustomEvent('editor:loaded', { detail: { editor } }));
                
                editor.on('component:remove', component => {
                    let curr = component;
                    while (curr && curr.get('type') !== 'widget-embed' && curr.get('type') !== 'module-widget') curr = curr.parent();
                    if (!curr) return;
                    
                    const widgetModel = curr;
                    
                    const widgetType = widgetModel.get('type');
                    
                    if (widgetType === 'widget-embed') {
                        const widgetId = widgetModel.get('tenant_widget_id') || widgetModel.getAttributes()['data-tenant-widget-id'] || widgetModel.getAttributes()['data-widget-id'];
                        if (window._loadedWidgets) window._loadedWidgets.delete(widgetId);
                        
                        const frameEl = editor.Canvas.getFrameEl();
                        const doc = frameEl.contentDocument || frameEl.contentWindow.document;
                        const embedEl = doc.querySelector(`[data-tenant-widget-id="${widgetId}"]`);
                        if (embedEl) embedEl.remove();
                        
                        const wrapperModel = widgetModel.parent();
                        if (wrapperModel && /\bcol-md-\d+\b/.test(wrapperModel.getAttributes().class || '')) {
                            wrapperModel.destroy();
                        } else {
                            widgetModel.destroy();
                        }
                        
                        if (window.StudioBlockManager && typeof window.StudioBlockManager.updateBlocksInCategories === 'function') {
                            window.StudioBlockManager.updateBlocksInCategories(editor);
                        }
                    } 
                    else if (widgetType === 'module-widget') {
                        const moduleId = widgetModel.get('widget_module_id') || widgetModel.getAttributes()['data-widget-module-id'];
                        if (window._loadedModules) window._loadedModules.delete(moduleId);
                        
                        const frameEl = editor.Canvas.getFrameEl();
                        const doc = frameEl.contentDocument || frameEl.contentWindow.document;
                        const moduleEl = doc.querySelector(`[data-widget-module-id="${moduleId}"]`);
                        if (moduleEl) moduleEl.remove();
                        
                        const wrapperModel = widgetModel.parent();
                        if (wrapperModel && /\bcol-md-\d+\b/.test(wrapperModel.getAttributes().class || '')) {
                            wrapperModel.destroy();
                        } else {
                            widgetModel.destroy();
                        }
                        
                        if (window.StudioBlockManager && typeof window.StudioBlockManager.updateBlocksInCategories === 'function') {
                            window.StudioBlockManager.updateBlocksInCategories(editor);
                        }
                    }
                    
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
    
    function setupTinyMCERTE(editor) {
        if (typeof tinymce === 'undefined') {
            console.log('TinyMCE yüklenmemiş, varsayılan RTE kullanılacak');
            return;
        }
        
        editor.setCustomRte({
            enable(el, rte) {
                const id = 'tinymce-' + Date.now();
                el.id = id;
                el.contentEditable = true;
                
                const tinyConfig = {
                    target: el,
                    inline: true,
                    toolbar: 'bold italic underline | bullist numlist | link | removeformat',
                    menubar: false,
                    plugins: ['lists', 'link'],
                    toolbar_mode: 'floating',
                    branding: false,
                    statusbar: false,
                    language: 'tr',
                    content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; }',
                    setup(ed) {
                        ed.on('init', () => {
                            ed.setContent(el.innerHTML);
                            setTimeout(() => ed.focus(), 0);
                        });
                        
                        ed.on('change keyup', () => {
                            const content = ed.getContent();
                            el.innerHTML = content;
                            rte.updateContent(content);
                        });
                        
                        ed.on('blur', () => {
                            const content = ed.getContent();
                            el.innerHTML = content;
                            rte.updateContent(content);
                        });
                    }
                };
                
                setTimeout(() => {
                    tinymce.init(tinyConfig);
                }, 0);
            },
            
            disable(el, rte) {
                if (el.id && tinymce.get(el.id)) {
                    const ed = tinymce.get(el.id);
                    const content = ed.getContent();
                    el.innerHTML = content;
                    ed.destroy();
                }
                el.contentEditable = false;
            }
        });
    }
    
    function loadContent(editor, config) {
        setTimeout(() => {
            try {
                console.log('İçerik yükleme işlemi başlatılıyor...');
                
                const htmlContentEl = document.getElementById('html-content');
                const cssContentEl = document.getElementById('css-content');
                
                let content = htmlContentEl ? htmlContentEl.value : '';
                
                if (!content || content.trim() === '' || content.trim() === '<body></body>' || content.length < 20) {
                    console.warn('Geçerli içerik bulunamadı. Varsayılan içerik yükleniyor...');
                    content = window.StudioConfig.getConfig('defaultHtml');
                }

                const moduleRegex = /\[\[module:(\d+)\]\]/g;
                let moduleMatch;
                const moduleIds = [];
                
                while ((moduleMatch = moduleRegex.exec(content)) !== null) {
                    moduleIds.push(moduleMatch[1]);
                }
                
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

                if (window.StudioHtmlParser) {
                    if (typeof window.StudioHtmlParser.convertAllWidgetReferencesToEmbeds === 'function') {
                        content = window.StudioHtmlParser.convertAllWidgetReferencesToEmbeds(content);
                    }
                    if (typeof window.StudioHtmlParser.parseInput === 'function') {
                        content = window.StudioHtmlParser.parseInput(content);
                    }
                }

                editor.setComponents(content);
                console.log('İçerik editöre başarıyla yüklendi');
                
                if (window.StudioWidgetLoader && typeof window.StudioWidgetLoader.registerWidgetEmbedComponent === 'function') {
                    window.StudioWidgetLoader.registerWidgetEmbedComponent(editor);
                }
                
                if (cssContentEl && cssContentEl.value) {
                    editor.setStyle(cssContentEl.value);
                }
                
                if (window.StudioWidgetLoader && typeof window.StudioWidgetLoader.processExistingWidgets === 'function') {
                    window.StudioWidgetLoader.processExistingWidgets(editor);
                }
                
                if (window.StudioWidgetLoader && typeof window.StudioWidgetLoader.processWidgetEmbeds === 'function') {
                    window.StudioWidgetLoader.processWidgetEmbeds(editor);
                }
                
                setTimeout(() => {
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
    
    function setupComponentTypes(editor) {
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
                    
                    init() {
                        this.on('change:widget_id', this.updateWidgetContent);
                    },
                    
                    updateWidgetContent() {
                        const widgetId = this.get('widget_id');
                        if (widgetId) {
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
                    const model = this.model;
                    const widgetId = model.get('widget_id') || model.getAttributes()['data-widget-id'];
                    
                    if (widgetId) {
                        window.open(`/admin/widgetmanagement/items/${widgetId}`, '_blank');
                    }
                },
                
                onRender() {
                    const el = this.el;
                    if (el) {
                        el.style.border = '2px dashed #2a6dcf';
                        el.style.padding = '5px';
                        el.style.borderRadius = '3px';
                        el.style.position = 'relative';
                        
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
    
    function fixCssAndHtmlIssues(editor) {
        const originalGetCss = editor.getCss;
        editor.getCss = function(opts = {}) {
            opts.avoidProtected = true;
            
            let css = originalGetCss.call(this, opts);
            
            css = css.replace(/body\s*{\s*[^}]*}/g, '');
            css = css.replace(/\[data-gjs-type="wrapper"\]\s*{\s*[^}]*}/g, '');
            css = css.replace(/\.gjs-dashed\s*\[data-gjs-highlightable\]\s*{\s*[^}]*}/g, '');
            css = css.replace(/\.gjs-selected\s*{\s*[^}]*}/g, '');
            css = css.replace(/\.gjs-isgrabbing[^{]*{\s*[^}]*}/g, '');
            css = css.replace(/\.gjs-is__grabbing[^{]*{\s*[^}]*}/g, '');
            css = css.replace(/\.gjs-hovered[^{]*{\s*[^}]*}/g, '');
            css = css.replace(/\.gjs-freezed[^{]*{\s*[^}]*}/g, '');
            css = css.replace(/\.gjs-no-select[^{]*{\s*[^}]*}/g, '');
            css = css.replace(/\.gjs-plh-image[^{]*{\s*[^}]*}/g, '');
            css = css.replace(/\.gjs-text-node[^{]*{\s*[^}]*}/g, '');
            css = css.replace(/\.gjs-comp-selected[^{]*{\s*[^}]*}/g, '');
            css = css.replace(/\.gjs-comp-highlighted[^{]*{\s*[^}]*}/g, '');
            css = css.replace(/\*\s*{\s*box-sizing:\s*border-box;\s*}\s*body\s*{\s*margin(-top|-right|-bottom|-left)?:?\s*0(px)?;?\s*}/g, '');
            
            return css;
        };
        
        const originalGetHtml = editor.getHtml;
        editor.getHtml = function(opts = {}) {
            let html = originalGetHtml.call(this, opts);
            
            html = cleanHtmlOutput(html);
            
            html = html.replace(/^<div>\s*/, '');
            html = html.replace(/\s*<\/div>$/, '');
            
            return html;
        };
        
        if (editor.CssComposer) {
            editor.CssComposer.getConfig().protectedCss = '';
        }
        
        if (editor.CssComposer && editor.CssComposer.buildCSS) {
            const originalBuildCSS = editor.CssComposer.buildCSS;
            editor.CssComposer.buildCSS = function() {
                const result = originalBuildCSS.apply(this, arguments);
                return result.replace(/body\s*{\s*[^}]*}/g, '')
                            .replace(/\[data-gjs-type="wrapper"\]\s*{\s*[^}]*}/g, '')
                            .replace(/\.gjs-dashed\s*\[data-gjs-highlightable\]\s*{\s*[^}]*}/g, '')
                            .replace(/\.gjs-selected\s*{\s*[^}]*}/g, '')
                            .replace(/\.gjs-isgrabbing[^{]*{\s*[^}]*}/g, '')
                            .replace(/\.gjs-is__grabbing[^{]*{\s*[^}]*}/g, '')
                            .replace(/\.gjs-hovered[^{]*{\s*[^}]*}/g, '')
                            .replace(/\.gjs-freezed[^{]*{\s*[^}]*}/g, '')
                            .replace(/\.gjs-no-select[^{]*{\s*[^}]*}/g, '')
                            .replace(/\.gjs-plh-image[^{]*{\s*[^}]*}/g, '')
                            .replace(/\.gjs-text-node[^{]*{\s*[^}]*}/g, '')
                            .replace(/\.gjs-comp-selected[^{]*{\s*[^}]*}/g, '')
                            .replace(/\.gjs-comp-highlighted[^{]*{\s*[^}]*}/g, '')
                            .replace(/\*\s*{\s*box-sizing:\s*border-box;\s*}\s*body\s*{\s*margin(-top|-right|-bottom|-left)?:?\s*0(px)?;?\s*}/g, '');
            };
        }
    }
    
    function cleanHtmlOutput(html) {
        html = html.replace(/<div[^>]*class="gjs-css-rules"[^>]*>[\s\S]*?<\/div>/gi, '');
        html = html.replace(/<div[^>]*class="gjs-js-cont"[^>]*>[\s\S]*?<\/div>/gi, '');
        html = html.replace(/<div[^>]*id="gjs-css-rules[^"]*"[^>]*>[\s\S]*?<\/div>/gi, '');
        
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        
        const allElements = doc.querySelectorAll('*');
        allElements.forEach(element => {
            if (element.id && /^i[a-z0-9]{2,5}(-\d+)?$/i.test(element.id)) {
                element.removeAttribute('id');
            }
            
            element.removeAttribute('draggable');
            
            if (element.classList) {
                const classesToRemove = [
                    'gjs-hovered', 'gjs-selected', 'gjs-freezed', 
                    'gjs-css-rules', 'gjs-js-cont', 'gjs-isgrabbing',
                    'gjs-is__grabbing', 'gjs-no-select', 'gjs-plh-image',
                    'gjs-text-node', 'gjs-comp-selected', 'gjs-comp-highlighted'
                ];
                
                classesToRemove.forEach(className => {
                    element.classList.remove(className);
                });
                
                if (element.classList.length === 0) {
                    element.removeAttribute('class');
                }
            }
            
            const attributesToRemove = [];
            for (let i = 0; i < element.attributes.length; i++) {
                const attr = element.attributes[i];
                if (attr.name.startsWith('data-gjs-') && 
                    !attr.name.includes('data-gjs-type') &&
                    !attr.name.includes('data-widget') &&
                    !attr.name.includes('data-tenant-widget')) {
                    attributesToRemove.push(attr.name);
                }
            }
            
            attributesToRemove.forEach(attrName => {
                element.removeAttribute(attrName);
            });
        });
        
        const cssRulesElements = doc.querySelectorAll('[class*="gjs-css-rules"], [class*="gjs-js-cont"], [id*="gjs-css-rules"]');
        cssRulesElements.forEach(element => {
            element.remove();
        });
        
        const bodyContent = doc.body.innerHTML;
        
        return bodyContent
            .replace(/\s+/g, ' ')
            .replace(/>\s+</g, '><')
            .trim();
    }
    
    function setupCommands(editor) {
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
        
        editor.Commands.add('canvas-clear', {
            run(editor) {
                editor.DomComponents.clear();
                editor.CssComposer.clear();
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