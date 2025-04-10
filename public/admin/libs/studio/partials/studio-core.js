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
        // GrapesJS Editor yapılandırması
        editor = grapesjs.init({
            container: "#" + config.elementId,
            fromElement: false,
            height: "100%",
            width: "100%",
            storageManager: false,
            panels: { defaults: [] },
            blockManager: {
                appendTo: "#blocks-container",
            },
            styleManager: {
                appendTo: "#styles-container",
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
        
        return editor;
    }
    
    return {
        initEditor: initEditor,
        getEditor: function() {
            return editor;
        }
    };
})();