/**
 * Studio Editor - Yapılandırma Modülü
 * Tüm yapılandırma ayarları ve sabitler
 */

window.StudioConfig = (function() {
    // Varsayılan ayarlar
    const defaults = {
        // Canvas ayarları
        canvas: {
            styles: [
                "https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css",
                "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css",
            ],
            scripts: [
                "https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js",
                "/admin/libs/handlebars/handlebars.min.js",
                "/admin/libs/studio/partials/studio-widget-manager.js",
                "/admin/libs/studio/partials/studio-widget-loader.js"
            ]
        },
        
        // Cihaz ayarları
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
        
        // Stil yöneticisi sektörleri
        styleManagerSectors: [
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
        ],
        
        // Widget kategorileri
        widgetCategories: {
            'layout': { name: 'Düzen', icon: 'fa fa-columns', order: 1 },
            'content': { name: 'İçerik', icon: 'fa fa-font', order: 2 },
            'form': { name: 'Form', icon: 'fa fa-wpforms', order: 3 },
            'media': { name: 'Medya', icon: 'fa fa-image', order: 4 },
            'widget': { name: 'Widgetlar', icon: 'fa fa-puzzle-piece', order: 5 }
        },
        
        // Varsayılan HTML içeriği
        defaultHtml: `
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
        </div>`
    };
    
    // Ayarları al
    function getConfig(key) {
        if (key) {
            const keys = key.split('.');
            let result = defaults;
            
            for (let i = 0; i < keys.length; i++) {
                if (result[keys[i]] === undefined) {
                    return null;
                }
                result = result[keys[i]];
            }
            
            return result;
        }
        
        return defaults;
    }
    
    // Tam HTML şablonu
    function getFullHtmlTemplate(html, css, js) {
        return `<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Studio İçeriği</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
${css}
.widget-container { pointer-events: auto !important; }
.widget-content { filter: none !important; opacity: 1 !important; }
    </style>
</head>
<body>
${html}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
${js}
    </script>
</body>
</html>`;
    }
    
    return {
        getConfig: getConfig,
        getFullHtmlTemplate: getFullHtmlTemplate
    };
})();