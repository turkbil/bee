/**
 * Studio Editor - Eklentiler Yükleyici
 * GrapesJS eklentilerini dinamik olarak yükler
 */
(function() {
    // Aktif eklentiler listesi - config'ten çekilebilir
    const enabledPlugins = [
        'blocks-basic',
        'preset-webpage',
        'style-bg',
        'plugin-forms',
        'custom-code',
        'touch',
        'components-countdown',
        'tabs',
        'typed'
    ];
    
    // Eklenti dosya adları ve bağımlılıkları
    const pluginFiles = {
        'blocks-basic': 'blocks-basic-master.min.js',
        'preset-webpage': 'preset-webpage-master.min.js',
        'style-bg': 'style-bg-master.min.js',
        'plugin-forms': 'grapesjs-plugin-forms.min.js',
        'custom-code': 'components-custom-code-master.min.js',
        'touch': 'touch-master.min.js',
        'components-countdown': 'components-countdown-master.min.js',
        'tabs': 'components-tabs-master.min.js',
        'typed': 'components-typed-master.min.js',
        'export': 'export-master.min.js'
    };
    
    // Eklenti yapılandırmaları
    const pluginConfigs = {
        'blocks-basic': {
            blocks: ['column1', 'column2', 'column3', 'text', 'link', 'image', 'video', 'map'],
            category: 'Temel'
        },
        'preset-webpage': {
            modalImportTitle: 'HTML İçeri Aktar',
            modalImportLabel: 'HTML kodunu yapıştırın',
            modalImportContent: '<div>HTML kodunu buraya yapıştırın</div>'
        },
        'style-bg': {
            // Arkaplan stili yapılandırması
        },
        'plugin-forms': {
            labels: {
                form: 'Form',
                button: 'Buton',
                checkbox: 'Onay Kutusu',
                radio: 'Radyo Butonu',
                text: 'Metin Alanı',
                select: 'Seçim Kutusu'
            }
        },
        'custom-code': {
            buttonLabel: 'Özel Kod',
            placeholderContent: '// Kodunuzu buraya yazın'
        },
        'touch': {
            // Dokunmatik eklenti yapılandırması
        },
        'components-countdown': {
            category: 'Bileşenler',
            dateInputType: 'date',
            labelCountdownCategory: 'Sayaç',
            labelCountdown: 'Sayaç',
            labelDate: 'Tarih',
            labelDaysLeft: 'Gün'
        },
        'tabs': {
            tabsBlock: {
                category: 'Bileşenler',
                label: 'Sekmeler'
            }
        },
        'typed': {
            typeSpeed: 50,
            className: 'typed',
            category: 'Bileşenler'
        }
    };
    
    /**
     * Eklentileri yükle
     * @param {Object} editor - GrapesJS editor örneği
     */
    window.loadGrapesJSPlugins = function(editor) {
        // Aktifleştirilmiş eklentileri filtrele
        const activePlugins = enabledPlugins.filter(plugin => 
            typeof pluginFiles[plugin] !== 'undefined'
        );
        
        // Her eklentiyi kontrol et ve etkinleştir
        activePlugins.forEach(plugin => {
            const pluginFileName = pluginFiles[plugin];
            const pluginPath = `/admin/libs/studio/plugins/${pluginFileName}`;
            
            // Eklenti script'i mevcut mu kontrol et
            const scriptExists = document.querySelector(`script[src="${pluginPath}"]`);
            if (!scriptExists) {
                console.log(`Eklenti yükleniyor: ${plugin}`);
                
                try {
                    // Eklentiyi GrapesJS'e ekle
                    if (typeof window[`grapesjs-${plugin}`] !== 'undefined') {
                        const config = pluginConfigs[plugin] || {};
                        editor.use(`grapesjs-${plugin}`, config);
                    }
                } catch (error) {
                    console.error(`Eklenti yüklenirken hata oluştu: ${plugin}`, error);
                }
            }
        });
        
        console.log('GrapesJS eklentileri başarıyla yüklendi!');
    };
})();