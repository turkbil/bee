/**
 * Studio Editor - Çekirdek Modül
 * GrapesJS editor yapılandırması ve temel kurulumu
 */
window.StudioCore = (function() {
    let editor = null;
    
    /**
     * GrapesJS editor örneğini başlatır
     * @param {Object} config - Editor yapılandırması
     * @returns {Object} - GrapesJS editor örneği
     */
    function initEditor(config) {
        // BlockManager seçicisini doğru şekilde ayarla
        let blockManagerConfig = {};
        
        // Blok konteynerini kontrol et ve yapılandır
        if (config.blocksContainer) {
            blockManagerConfig.appendTo = config.blocksContainer;
        } else {
            // İlk başta özel alanı kontrol et
            const blocksContainer = document.querySelector('.blocks-container');
            if (blocksContainer) {
                blockManagerConfig.appendTo = blocksContainer;
            } else {
                // Varsayılan olarak body'ye ekle
                blockManagerConfig.appendTo = 'body';
            }
        }
        
        // GrapesJS Editor yapılandırması
        editor = grapesjs.init({
            container: "#" + config.elementId,
            fromElement: false,
            height: "100%",
            width: "100%",
            storageManager: false,
            panels: { defaults: [] },
            blockManager: blockManagerConfig,
            styleManager: {
                appendTo: "#styles-container",
                sectors: [
                    {
                        name: 'Boyut',
                        open: false,
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
            plugins: [
                // Eklentiler app.js'de yüklenecek
            ],
            pluginsOpts: {
                // Eklenti seçenekleri burada yapılandırılacak
            }
        });

        // Önceden oluşturulmuş içeriği yükle
        if (config.content) {
            editor.setComponents(config.content);
        }
        
        if (config.css) {
            editor.setStyle(config.css);
        }

        // Editor'ü yükleme olayını dinle
        editor.on('load', function() {
            // Editor yüklendi, animasyonu gizle
            const loaderElement = document.querySelector('.studio-loader');
            if (loaderElement) {
                loaderElement.style.opacity = '0';
                setTimeout(() => {
                    if (loaderElement && loaderElement.parentNode) {
                        loaderElement.parentNode.removeChild(loaderElement);
                    }
                }, 300);
            }
            
            // DOM'un hazır olmasından sonra blokları ve panelleri tekrar ayarla
            setTimeout(() => {
                // BlockManager seçicisini tekrar ayarla
                const blocksContainer = document.querySelector('.blocks-container');
                if (blocksContainer && editor.BlockManager) {
                    editor.BlockManager.render(blocksContainer);
                }
            }, 500);
        });
        
        return editor;
    }
    
    return {
        initEditor: initEditor,
        getEditor: function() {
            return editor;
        }
    };
})();