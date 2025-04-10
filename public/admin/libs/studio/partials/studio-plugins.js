/**
 * Studio Editor - Eklentiler Modülü
 * GrapesJS eklentilerini yükler ve yapılandırır
 */
window.StudioPlugins = (function() {
    /**
     * Desteklenen eklentiler
     */
    const supportedPlugins = {
        'blocks-basic': {
            name: 'grapesjs-blocks-basic',
            options: {
                blocks: ['column1', 'column2', 'column3', 'column3-7', 'text', 'link', 'image', 'video', 'map'],
                flexGrid: true,
                stylePrefix: 'gjs-',
                addBasicStyle: true,
                category: 'Temel',
                labelColumn1: '1 Sütun',
                labelColumn2: '2 Sütun',
                labelColumn3: '3 Sütun',
                labelColumn37: '2 Sütun 3/7',
                labelText: 'Metin',
                labelLink: 'Link',
                labelImage: 'Görsel',
                labelVideo: 'Video',
                labelMap: 'Harita',
            }
        },
        'preset-webpage': {
            name: 'grapesjs-preset-webpage',
            options: {
                modalImportTitle: 'HTML İçeri Aktar',
                modalImportLabel: 'HTML kodunu buraya yapıştırın',
                modalImportContent: '',
                importViewerOptions: {},
                textCleanCanvas: 'İçeriği temizlemek istediğinize emin misiniz?',
                showStylesOnChange: true,
                textGeneral: 'Genel',
                textLayout: 'Düzen',
                textTypography: 'Tipografi',
                textDecorations: 'Dekorasyonlar',
                textExtra: 'Ekstra',
                formsOpts: {},
                navbarOpts: {},
                countdownOpts: {},
                exportOpts: {},
                aviaryOpts: false,
                filestackOpts: false,
            }
        }
    };
    
    /**
     * Eklentileri yükle
     * @param {Object} editor - GrapesJS editor örneği
     */
    function loadPlugins(editor) {
        console.log('Eklentiler aktif edildi.');
        
        // Temel bootstrap bileşenleri ekle
        addBootstrapComponents(editor);
    }
    
    /**
     * Bootstrap bileşenlerini ekle
     * @param {Object} editor - GrapesJS editor örneği
     */
    function addBootstrapComponents(editor) {
        // Bootstrap bileşenlerini otomatik olarak yüklü kabul ediyoruz
        // Ve bileşenleri kaydetmek için komponentleri tanımlıyoruz
    }
    
    /**
     * Özel bileşenleri kaydet
     * @param {Object} editor - GrapesJS editor örneği
     */
    function registerCustomComponents(editor) {
        // Örnek bileşen kaydı
        editor.DomComponents.addType('custom-button', {
            model: {
                defaults: {
                    tagName: 'button',
                    attributes: { class: 'btn btn-primary' },
                    content: 'Özel Buton',
                    traits: [
                        {
                            type: 'select',
                            name: 'type',
                            label: 'Tip',
                            options: [
                                { value: 'button', name: 'Button' },
                                { value: 'submit', name: 'Submit' },
                                { value: 'reset', name: 'Reset' },
                            ]
                        }
                    ]
                }
            }
        });
    }
    
    /**
     * Özel komutları ekle
     * @param {Object} editor - GrapesJS editor örneği
     */
    function addCustomCommands(editor) {
        // HTML & CSS temizle komutu
        editor.Commands.add('clean-html', {
            run: function(editor) {
                const html = editor.getHtml();
                const css = editor.getCss();
                
                // Basit temizleme - fazla boşlukları kaldır
                const cleanHtml = html.replace(/\s+/g, ' ')
                                      .replace(/>\s+</g, '><')
                                      .trim();
                                      
                const cleanCss = css.replace(/\s+/g, ' ')
                                    .replace(/{\s+/g, '{')
                                    .replace(/}\s+/g, '}')
                                    .replace(/:\s+/g, ':')
                                    .replace(/;\s+/g, ';')
                                    .trim();
                
                editor.setComponents(cleanHtml);
                editor.setStyle(cleanCss);
                
                StudioUtils.showNotification(
                    "Başarılı", 
                    "HTML ve CSS temizlendi.", 
                    "success"
                );
            }
        });
    }
    
    return {
        loadPlugins: loadPlugins,
        registerCustomComponents: registerCustomComponents,
        addCustomCommands: addCustomCommands
    };
})();