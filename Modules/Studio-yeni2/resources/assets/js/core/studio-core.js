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
            // GrapesJS Editor yapılandırması
            editor = grapesjs.init({
                container: '#' + config.elementId,
                fromElement: false,
                height: '100%',
                width: '100%',
                storageManager: false,
                panels: { defaults: [] },
                styleManager: {
                    appendTo: '#styles-container',
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
                        },
                        {
                            name: 'Ekstra',
                            open: false,
                            properties: [
                                'opacity', 'transition', 'transform', 'perspective', 'transform-style'
                            ]
                        }
                    ]
                },
                layerManager: {
                    appendTo: '#layers-container',
                },
                traitManager: {
                    appendTo: '#traits-container',
                },
                deviceManager: {
                    devices: [
                        {
                            name: 'Masaüstü',
                            width: '',
                        },
                        {
                            name: 'Tablet',
                            width: '768px',
                            widthMedia: '992px',
                        },
                        {
                            name: 'Mobil',
                            width: '320px',
                            widthMedia: '576px',
                        },
                    ],
                },
                canvas: {
                    scripts: [
                        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js',
                    ],
                    styles: [
                        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css',
                        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
                    ]
                }
            });

            console.log('GrapesJS editor başarıyla oluşturuldu.');

            // İçerik yükleme
            loadContent(editor, config);

            // Temel komutları ayarla
            setupCommands(editor);

            // Editor'ü döndür
            return editor;
        } catch (error) {
            console.error('GrapesJS başlatılırken hata oluştu:', error);
            throw error;
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
            if (config.content) {
                editor.setComponents(config.content);
            }

            // CSS içeriği
            if (config.css) {
                editor.setStyle(config.css);
            }

            // JS içeriğini kaydet
            const jsContentEl = document.getElementById('js-content');
            if (jsContentEl && config.js) {
                jsContentEl.value = config.js;
            }

            console.log('İçerik başarıyla yüklendi');
        } catch (error) {
            console.error('İçerik yüklenirken hata oluştu:', error);

            // Varsayılan içeriği yüklemeyi dene
            try {
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
            } catch (e) {
                console.error('Varsayılan içerik yüklenirken de hata oluştu:', e);
            }
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
            // HTML içeriğini al
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