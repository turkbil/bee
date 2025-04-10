/**
 * Studio Editor - Eklentiler Yükleyici
 * GrapesJS eklentilerini dinamik olarak yükler
 */
window.StudioPluginLoader = (function() {
    // Aktif eklentiler listesi
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
        'style-bg': {},
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
        'touch': {},
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
     * Eklentileri güvenli bir şekilde yükle
     * @param {Object} editor - GrapesJS editor örneği
     */
    function loadPlugins(editor) {
        if (!editor) {
            console.error('GrapesJS editor örneği bulunamadı!');
            return;
        }
        
        enabledPlugins.forEach(plugin => {
            try {
                console.log(`Eklenti yükleniyor: ${plugin}`);
                
                // GrapesJS eklentileri doğrudan plugins içinde olabilir
                if (editor.plugins && typeof editor.plugins.add === 'function') {
                    const config = pluginConfigs[plugin] || {};
                    editor.plugins.add(plugin, config);
                }
            } catch (error) {
                console.error(`Eklenti yüklenirken hata oluştu: ${plugin}`, error);
            }
        });
        
        console.log('GrapesJS eklentileri başarıyla yüklendi!');
    }
    
    return {
        loadPlugins: loadPlugins
    };
})();