/**
 * Studio Editor - Yapılandırma Modülü
 * GrapesJS ve Studio editörü için tüm yapılandırma ayarları
 */
window.StudioConfig = (function() {
    // Varsayılan GrapesJS editör ayarları
    const defaultEditorConfig = {
        // Canvas seçenekleri
        canvas: {
            styles: [
                'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css',
                'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css'
            ],
            scripts: [
                'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js'
            ]
        },
        
        // Cihaz ayarları
        deviceManager: {
            devices: [
                {
                    name: 'Desktop',
                    width: '',
                },
                {
                    name: 'Tablet',
                    width: '768px',
                    widthMedia: '992px',
                },
                {
                    name: 'Mobile',
                    width: '320px',
                    widthMedia: '576px',
                }
            ]
        },
        
        // Blok yöneticisi ayarları
        blockManager: {
            appendTo: '#blocks-container'
        },
        
        // Katman yöneticisi ayarları
        layerManager: {
            appendTo: '#layers-container'
        },
        
        // Özellik yöneticisi ayarları
        traitManager: {
            appendTo: '#traits-container'
        },
        
        // Stil yöneticisi ayarları
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
        
        // Panel ayarları
        panels: { 
            defaults: [] 
        },
        
        // Önbellek kapatıldı (manuel yönetim için)
        storageManager: false,
        
        // GrapesJS eklentilerinin ayarları
        pluginsOpts: {
            'grapesjs-blocks-basic': {
                blocks: ['column1', 'column2', 'column3', 'column3-7', 'text', 'link', 'image', 'video', 'map'],
                flexGrid: true,
                category: 'Temel',
                stylePrefix: 'gjs-',
                addBasicStyle: true,
                labelColumn1: '1 Sütun',
                labelColumn2: '2 Sütun',
                labelColumn3: '3 Sütun',
                labelColumn37: '2 Sütun 3/7',
                labelText: 'Metin',
                labelLink: 'Link',
                labelImage: 'Görsel',
                labelVideo: 'Video',
                labelMap: 'Harita',
            },
            'grapesjs-preset-webpage': {
                modalImportTitle: 'HTML İçeri Aktar',
                modalImportLabel: 'HTML kodunu buraya yapıştırın',
                modalImportContent: '',
                textCleanCanvas: 'İçeriği temizlemek istediğinize emin misiniz?',
                showStylesOnChange: true,
                textGeneral: 'Genel',
                textLayout: 'Düzen',
                textTypography: 'Tipografi',
                textDecorations: 'Dekorasyonlar',
                textExtra: 'Ekstra',
            }
        }
    };
    
    /**
     * Editör yapılandırmasını al
     * @param {Object} customConfig - Özel yapılandırma seçenekleri
     * @returns {Object} - Birleştirilmiş yapılandırma
     */
    function getEditorConfig(customConfig = {}) {
        // Özel yapılandırmayı varsayılan yapılandırmayla birleştir
        return mergeDeep(defaultEditorConfig, customConfig);
    }
    
    /**
     * İki nesneyi derinlemesine birleştirir
     * @param {Object} target - Hedef nesne
     * @param {Object} source - Kaynak nesne
     * @returns {Object} - Birleştirilmiş nesne
     */
    function mergeDeep(target, source) {
        const isObject = obj => obj && typeof obj === 'object' && !Array.isArray(obj);
        
        if (!isObject(target) || !isObject(source)) {
            return source;
        }
        
        const output = Object.assign({}, target);
        
        Object.keys(source).forEach(key => {
            if (isObject(source[key])) {
                if (!(key in target)) {
                    Object.assign(output, { [key]: source[key] });
                } else {
                    output[key] = mergeDeep(target[key], source[key]);
                }
            } else {
                Object.assign(output, { [key]: source[key] });
            }
        });
        
        return output;
    }
    
    /**
     * Blok kategori yapılandırması
     */
    const blockCategories = [
        { id: 'düzen', label: 'Düzen Bileşenleri', open: true },
        { id: 'temel', label: 'Temel Bileşenler', open: true },
        { id: 'medya', label: 'Medya', open: false },
        { id: 'bootstrap', label: 'Bootstrap Bileşenleri', open: false },
        { id: 'formlar', label: 'Formlar', open: false },
        { id: 'widget', label: 'Widgetlar', open: false },
    ];
    
    /**
     * Cihaz tanımları
     */
    const devices = [
        {
            name: 'Desktop',
            width: '',
        },
        {
            name: 'Tablet',
            width: '768px',
            widthMedia: '992px',
        },
        {
            name: 'Mobile',
            width: '320px',
            widthMedia: '576px',
        }
    ];
    
    return {
        getEditorConfig: getEditorConfig,
        blockCategories: blockCategories,
        devices: devices
    };
})();