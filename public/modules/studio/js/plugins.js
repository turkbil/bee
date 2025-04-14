/**
 * Studio Plugins
 * GrapesJS eklentilerini yöneten modül
 */
const StudioPlugins = (function() {
    let editor = null;
    
    /**
     * Tüm eklentileri yükle
     * @param {Object} editorInstance GrapesJS editor örneği
     * @param {Object} options Eklenti seçenekleri
     */
    function loadPlugins(editorInstance, options = {}) {
        editor = editorInstance;
        
        // Varsayılan yapılandırma
        const pluginConfig = {
            basicBlocks: true,     // Temel bloklar
            presetWebpage: true,    // Sayfa şablonu
            forms: true,           // Form bileşenleri
            customCode: true,      // Özel kod
            touch: true,           // Dokunmatik destek
            styleBackground: true, // Arka plan stilleri
            export: true,          // Dışa aktarma
            ...options
        };
        
        // Eklentileri başlat
        initPlugins(pluginConfig);
        
        // Özel bileşenleri kaydet
        registerCustomComponents();
        
        console.log('Studio eklentileri yüklendi');
    }
    
    /**
     * Eklentileri başlat
     * @param {Object} config Eklenti yapılandırması
     */
    function initPlugins(config) {
        // Temel bloklar
        if (config.basicBlocks && typeof grapesjs.plugins.get('gjs-blocks-basic') !== 'undefined') {
            editor.Plugins.add('gjs-blocks-basic', {
                blocks: ['column1', 'column2', 'column3', 'column3-7', 'text', 'link', 'image', 'video', 'map'],
                category: 'Basic'
            });
        }
        
        // Sayfa şablonu
        if (config.presetWebpage && typeof grapesjs.plugins.get('gjs-preset-webpage') !== 'undefined') {
            editor.Plugins.add('gjs-preset-webpage', {
                navbarOpts: false,
                countdownOpts: false,
                formsOpts: false,
                blocks: []
            });
        }
        
        // Form bileşenleri
        if (config.forms && typeof grapesjs.plugins.get('gjs-plugin-forms') !== 'undefined') {
            editor.Plugins.add('gjs-plugin-forms', {
                blocks: ['form', 'input', 'textarea', 'select', 'button', 'label', 'checkbox', 'radio'],
                category: 'Forms'
            });
        }
        
        // Özel kod
        if (config.customCode && typeof grapesjs.plugins.get('gjs-custom-code') !== 'undefined') {
            editor.Plugins.add('gjs-custom-code');
        }
        
        // Dokunmatik destek
        if (config.touch && typeof grapesjs.plugins.get('gjs-touch') !== 'undefined') {
            editor.Plugins.add('gjs-touch');
        }
        
        // Arka plan stilleri
        if (config.styleBackground && typeof grapesjs.plugins.get('gjs-style-bg') !== 'undefined') {
            editor.Plugins.add('gjs-style-bg');
        }
        
        // Dışa aktarma
        if (config.export && typeof grapesjs.plugins.get('gjs-plugin-export') !== 'undefined') {
            editor.Plugins.add('gjs-plugin-export');
        }
        
        console.log('GrapesJS eklentileri başlatıldı');
    }
    
    /**
     * Özel bileşenleri kaydet
     */
    function registerCustomComponents() {
        // Özel bileşen #1: Hero Section
        editor.DomComponents.addType('hero-section', {
            model: {
                defaults: {
                    tagName: 'section',
                    attributes: { class: 'hero-section py-5' },
                    droppable: true,
                    traits: [
                        {
                            type: 'select',
                            name: 'background-style',
                            label: 'Arka Plan',
                            options: [
                                { value: 'bg-light', name: 'Açık' },
                                { value: 'bg-dark text-white', name: 'Koyu' },
                                { value: 'bg-primary text-white', name: 'Primary' },
                                { value: 'bg-secondary text-white', name: 'Secondary' },
                                { value: 'bg-image', name: 'Görsel' }
                            ],
                            changeProp: true
                        },
                        {
                            type: 'select',
                            name: 'text-align',
                            label: 'Metin Hizalama',
                            options: [
                                { value: 'text-start', name: 'Sola' },
                                { value: 'text-center', name: 'Ortaya' },
                                { value: 'text-end', name: 'Sağa' }
                            ],
                            changeProp: true
                        }
                    ],
                    'background-style': 'bg-light',
                    'text-align': 'text-center'
                }
            },
            view: {
                init() {
                    this.listenTo(this.model, 'change:background-style change:text-align', this.updateHeroSection);
                },
                updateHeroSection() {
                    const model = this.model;
                    const bgStyle = model.get('background-style');
                    const textAlign = model.get('text-align');
                    
                    // Mevcut sınıfları al
                    const classes = model.getClasses();
                    
                    // Arka plan ve metin hizalama sınıflarını kaldır
                    const filteredClasses = classes.filter(cls => 
                        !cls.startsWith('bg-') && 
                        !cls.startsWith('text-')
                    );
                    
                    // Temel sınıflar
                    filteredClasses.push('hero-section');
                    filteredClasses.push('py-5');
                    
                    // Arka plan sınıfı
                    if (bgStyle === 'bg-image') {
                        filteredClasses.push('bg-image');
                        // Görsel arka plan için stil ekle
                        model.addStyle({
                            'background-image': 'url("https://via.placeholder.com/1600x800")',
                            'background-size': 'cover',
                            'background-position': 'center'
                        });
                    } else {
                        // Arka plan rengine göre sınıfları ekle
                        bgStyle.split(' ').forEach(cls => {
                            filteredClasses.push(cls);
                        });
                        
                        // Görsel arka plan stilini kaldır
                        model.removeStyle('background-image');
                        model.removeStyle('background-size');
                        model.removeStyle('background-position');
                    }
                    
                    // Metin hizalama sınıfı
                    filteredClasses.push(textAlign);
                    
                    // Sınıfları güncelle
                    model.setClass(filteredClasses);
                }
            }
        });
        
        // Özel bileşen #2: Feature Box
        editor.DomComponents.addType('feature-box', {
            model: {
                defaults: {
                    tagName: 'div',
                    attributes: { class: 'feature-box p-4 mb-4' },
                    traits: [
                        {
                            type: 'select',
                            name: 'icon',
                            label: 'İkon',
                            options: [
                                { value: 'star', name: 'Yıldız' },
                                { value: 'heart', name: 'Kalp' },
                                { value: 'check', name: 'Tik' },
                                { value: 'lightbulb', name: 'Ampul' },
                                { value: 'cog', name: 'Dişli' }
                            ],
                            changeProp: true
                        },
                        {
                            type: 'color',
                            name: 'icon-color',
                            label: 'İkon Rengi',
                            changeProp: true
                        },
                        {
                            type: 'select',
                            name: 'box-style',
                            label: 'Kutu Stili',
                            options: [
                                { value: 'border rounded', name: 'Kenarlıklı' },
                                { value: 'shadow rounded', name: 'Gölgeli' },
                                { value: 'bg-light rounded', name: 'Açık Arkaplan' },
                                { value: 'bg-dark text-white rounded', name: 'Koyu Arkaplan' }
                            ],
                            changeProp: true
                        }
                    ],
                    icon: 'star',
                    'icon-color': '#007bff',
                    'box-style': 'border rounded'
                }
            },
            view: {
                init() {
                    this.listenTo(this.model, 'change:icon change:icon-color change:box-style', this.updateFeatureBox);
                    this.updateFeatureBox();
                },
                updateFeatureBox() {
                    const model = this.model;
                    const icon = model.get('icon');
                    const iconColor = model.get('icon-color');
                    const boxStyle = model.get('box-style');
                    
                    // Mevcut sınıfları al
                    const classes = model.getClasses();
                    
                    // Kutu stili sınıflarını kaldır
                    const filteredClasses = classes.filter(cls => 
                        cls !== 'border' && 
                        cls !== 'shadow' && 
                        cls !== 'rounded' && 
                        !cls.startsWith('bg-') && 
                        !cls.startsWith('text-')
                    );
                    
                    // Temel sınıflar
                    filteredClasses.push('feature-box');
                    filteredClasses.push('p-4');
                    filteredClasses.push('mb-4');
                    
                    // Kutu stili sınıfları
                    boxStyle.split(' ').forEach(cls => {
                        filteredClasses.push(cls);
                    });
                    
                    // Sınıfları güncelle
                    model.setClass(filteredClasses);
                    
                    // İçeriği güncelle
                    const iconHtml = `<i class="fas fa-${icon} mb-3" style="font-size: 2rem; color: ${iconColor};"></i>`;
                    const content = `
                        <div class="text-center">
                            ${iconHtml}
                            <h4>Özellik Başlığı</h4>
                            <p>Bu özelliğin açıklaması buraya gelecek. Özellikleri veya faydaları burada belirtebilirsiniz.</p>
                        </div>
                    `;
                    
                    model.components(content);
                }
            }
        });
        
        // Özel bileşenleri blok olarak kaydet
        editor.BlockManager.add('hero-section', {
            label: 'Hero Section',
            category: 'özel',
            attributes: { class: 'fa fa-window-maximize' },
            content: {
                type: 'hero-section',
                content: `
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <h1 class="display-4 fw-bold mb-4">Etkileyici Başlık</h1>
                                <p class="lead mb-4">Bu açıklama metni ziyaretçilerin dikkatini çekecek ve onları harekete geçirecek.</p>
                                <button class="btn btn-primary btn-lg">Hemen Başlayın</button>
                            </div>
                        </div>
                    </div>
                `
            }
        });
        
        editor.BlockManager.add('feature-box', {
            label: 'Özellik Kutusu',
            category: 'özel',
            attributes: { class: 'fa fa-cube' },
            content: {
                type: 'feature-box'
            }
        });
        
        console.log('Özel bileşenler kaydedildi');
    }
    
    // Dışa aktarılan fonksiyonlar
    return {
        load: loadPlugins,
        registerCustomComponents: registerCustomComponents,
        initPlugins: initPlugins
    };
})();

// Global olarak kullanılabilir yap
window.StudioPlugins = StudioPlugins;