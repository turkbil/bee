/**
 * Studio Core - GrapesJS Editör Çekirdeği
 * GrapesJS Editor yapılandırması ve temel kurulumu
 */
const StudioCore = (function() {
    let editor = null;

    /**
     * GrapesJS editor örneğini başlat
     * @param {Object} config - Editor yapılandırması
     * @returns {Object} - GrapesJS editor örneği
     */
    function initEditor(config) {
        console.log('GrapesJS editor başlatılıyor...');

        try {
            // GrapesJS'in canvas ayarlarını özelleştir
            const editorConfig = {
                // Temel ayarlar
                container: '#' + config.elementId,
                fromElement: false,
                height: '100%',
                width: '100%',
                storageManager: false,
                panels: { defaults: [] },
                
                // Bileşen düzenleme ayarları
                dragMode: 'absolute',
                selectorManager: { componentFirst: true },
                
                // Bileşen davranışları
                components: {
                    // Her yeni bileşeni düzenlenebilir yap
                    defaults: {
                        editable: true,
                        draggable: true,
                        droppable: true,
                        selectable: true,
                        hoverable: true,
                        stylable: true
                    }
                },
                
                // Canvas (iframe) ayarları
                canvas: {
                    // Harici stil ve betikleri geçici olarak devre dışı bırak
                    styles: [],
                    scripts: [],
                    
                    // Canvas içeriğinin görünmesi için ek ayarlar
                    frameStyle: `
                        html, body { 
                            background-color: #fff !important;
                            margin: 0 !important;
                            padding: 0 !important;
                            min-height: 100vh !important;
                            height: auto !important;
                            width: 100% !important;
                            display: block !important;
                            visibility: visible !important;
                            opacity: 1 !important;
                            overflow: auto !important;
                        }
                        * { box-sizing: border-box; }
                    `,
                    autoscroll: true,
                    autorender: true
                },
                
                // Bileşen ekleme davranışı
                deviceManager: {
                    devices: [
                        {
                            name: 'Masaüstü',
                            width: ''
                        },
                        {
                            name: 'Tablet',
                            width: '768px',
                            widthMedia: '992px'
                        },
                        {
                            name: 'Mobil',
                            width: '320px',
                            widthMedia: '576px'
                        }
                    ]
                },
                
                // Önemli: Bileşenlerin düzgün eklenmesi için
                allowScripts: true,
                avoidInlineStyle: false,
                forceClass: false,
                showOffsets: true,
                autorender: true,
                noticeOnUnload: false,
                
                // Bileşen yöneticisi ayarları
                domComponents: {
                    storeUndo: true,
                    trackChanges: true,
                    // Bileşen içeriğini düzgün işlemek için
                    processor: (obj) => {
                        return obj;
                    }
                },
                
                // Blok yöneticisi ayarları
                blockManager: {
                    appendTo: '#blocks-container',
                    blocks: []
                }
            };
            
            // GrapesJS editörünü başlat
            editor = grapesjs.init(editorConfig);
    
            // Bileşen ayarlarını global olarak güncelle
            editor.on('component:selected', (component) => {
                if (component) {
                    component.set('editable', true);
                    component.set('draggable', true);
                    component.set('droppable', true);
                    component.set('selectable', true);
                    component.set('hoverable', true);
                    component.set('stylable', true);
                }
            });
    
            console.log('GrapesJS editor başarıyla oluşturuldu.');
            return editor;
    
        } catch (error) {
            console.error('GrapesJS başlatılırken hata oluştu:', error);
            throw error;
        }
    }

    function processMustacheTemplates(component) {
        try {
            // Eğer bileşenin içeriği bir string ise ve {{ }} içeriyorsa
            const content = component.get('content');
            if (typeof content === 'string' && (content.includes('{{') || content.includes('{{'))) {
                // Önce öznitelik tırnak sorunlarını düzelt
                let processedContent = content;
                processedContent = processedContent.replace(/([\w\-]+)="([^"]*)"/g, (match, attr, value) => {
                    if (attr.endsWith('"')) {
                        const fixedAttr = attr.slice(0, -1);
                        return `${fixedAttr}="${value}"`;
                    }
                    return match;
                });
                
                // Şablonları işle
                processedContent = processedContent.replace(/\{\{([^\}]+)\}\}/g, (match, variable) => {
                    return `<span class="studio-template" data-tpl="${encodeURIComponent(match)}">TEMPLATE:${variable.trim()}</span>`;
                });
                
                if (content !== processedContent) {
                    component.set('content', processedContent);
                }
            }
            
            // Alt bileşenleri de işle
            if (component.get('components') && component.get('components').length > 0) {
                component.get('components').each(child => {
                    processMustacheTemplates(child);
                });
            }
        } catch (error) {
            console.error('Mustache şablonları işlenirken hata:', error);
        }
    }

    /**
     * İçeriği yükle
     * @param {Object} editor - GrapesJS editor
     * @param {Object} config - Yapılandırma
     */
    function loadContent(editor, config) {
        try {
            // HTML içeriği
            const htmlContentEl = document.getElementById('html-content');
            if (htmlContentEl) {
                const htmlContent = htmlContentEl.value;
                
                console.log('HTML İçeriği Yükleniyor:', {
                    length: htmlContent.length,
                    excerpt: htmlContent.substring(0, 100) + '...',
                    empty: !htmlContent || htmlContent.trim() === '',
                    element: htmlContentEl
                });
                
                if (htmlContent && htmlContent.trim() !== '') {
                    // İçeriği doğrudan yükle
                    editor.setComponents(htmlContent);
                    console.log('İçerik başarıyla yüklendi');
                } else {
                    console.warn('HTML içeriği boş!');
                    
                    // Varsayılan içerik oluştur
                    editor.setComponents(`
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
                        </div>
                    `);
                }
            } else {
                console.error('HTML içerik alanı (textarea#html-content) bulunamadı!');
            }

            // CSS içeriği
            const cssContentEl = document.getElementById('css-content');
            if (cssContentEl && cssContentEl.value && cssContentEl.value.trim() !== '') {
                editor.setStyle(cssContentEl.value);
            }

            // JS içeriği
            const jsContentEl = document.getElementById('js-content');
            // JS içeriği textarea'da zaten mevcut olmalı
            
        } catch (error) {
            console.error('İçerik yüklenirken hata oluştu:', error);
        }
    }

    /**
     * Temel komutları ayarla
     * @param {Object} editor - GrapesJS editor
     */
    function setupCommands(editor) {
        // Bileşen görünürlük komutu
        editor.Commands.add('sw-visibility', {
            run(editor) {
                const canvas = editor.Canvas;
                const classId = 'canvas-show-nodes';
                
                // Görünürlük durumunu değiştir
                // hasClass yerine Canvas'ın doğru metodunu kullanalım
                if (canvas.getBody().classList.contains(classId)) {
                    canvas.getBody().classList.remove(classId);
                    return false;
                } else {
                    canvas.getBody().classList.add(classId);
                    return true;
                }
            },
            stop() {
                editor.Canvas.getBody().classList.remove('canvas-show-nodes');
            }
        });

        // Temizleme komutu
        editor.Commands.add('core:canvas-clear', {
            run(editor) {
                editor.DomComponents.clear();
                editor.CssComposer.clear();
            }
        });

        // Geri al komutu
        editor.Commands.add('core:undo', {
            run(editor) {
                editor.UndoManager.undo();
            }
        });

        // Yinele komutu
        editor.Commands.add('core:redo', {
            run(editor) {
                editor.UndoManager.redo();
            }
        });
    }

    /**
     * HTML içeriğini kaydetmek için hazırla
     * @param {Object} editor - GrapesJS editor
     * @returns {string} - HTML içeriği
     */
    function prepareContentForSave(editor) {
        if (!editor) {
            console.error('Editor örneği geçersiz');
            return '';
        }

        try {
            // HTML içeriğini doğrudan al, işleme yapmadan
            return editor.getHtml();
        } catch (error) {
            console.error('İçerik hazırlanırken hata:', error);
            return '';
        }
    }

    /**
     * CSS içeriğini kaydetmek için hazırla
     * @param {Object} editor - GrapesJS editor
     * @returns {string} - CSS içeriği
     */
    function prepareCssForSave(editor) {
        if (!editor) {
            console.error('Editor örneği geçersiz');
            return '';
        }

        try {
            // CSS içeriğini al
            return editor.getCss();
        } catch (error) {
            console.error('CSS hazırlanırken hata:', error);
            return '';
        }
    }

    // Dışa aktarılan fonksiyonlar
    return {
        initEditor: initEditor,
        prepareContentForSave: prepareContentForSave,
        prepareCssForSave: prepareCssForSave
    };
})();

// Global olarak kullanılabilir yap
window.StudioCore = StudioCore;