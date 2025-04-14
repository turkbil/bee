/**
 * Studio Plugins - Eklenti Yöneticisi
 * GrapesJS eklentilerini yükler ve yapılandırır
 */
const StudioPlugins = (function() {
    // Yüklü eklentiler listesi
    let loadedPlugins = [];
    
    // Varsayılan eklenti yapılandırmaları
    const pluginDefaults = {
        'blocks-basic': {
            flexGrid: true,
            blocks: ['column1', 'column2', 'column3', 'text', 'link', 'image', 'video']
        },
        'preset-webpage': {
            modalImportTitle: 'HTML İçeri Aktar',
            modalImportLabel: 'HTML kodunu buraya yapıştırın'
        },
        'forms': {
            blocks: ['form', 'input', 'textarea', 'select', 'button', 'label', 'checkbox']
        },
        'custom-code': {
            blockLabel: 'Özel Kod',
            blockCustomCode: {
                label: 'Özel Kod',
                category: 'özel',
                attributes: { class: 'fa fa-code' }
            }
        },
        'touch': {}
    };
    
    /**
     * Eklentileri yükler
     * @param {Object} editor - GrapesJS editor örneği
     * @param {Object} options - Eklenti yapılandırma seçenekleri
     */
    function loadPlugins(editor, options = {}) {
        if (!editor) {
            console.error('Eklentiler yüklenirken hata: Editor örneği geçerli değil');
            return;
        }
        
        console.log('Eklentiler yükleniyor...');
        
        // Yapılandırmayı birleştir
        const config = mergeConfig(options);
        
        // Aktif eklentileri yükle
        loadActivePlugins(editor, config);
        
        // Özel bileşenleri kaydet
        registerCustomComponents(editor);
        
        console.log('Eklentiler başarıyla yüklendi:', loadedPlugins);
    }
    
    /**
     * Yapılandırmaları birleştirir
     * @param {Object} options - Kullanıcı yapılandırması
     * @returns {Object} - Birleştirilmiş yapılandırma
     */
    function mergeConfig(options) {
        const result = {};
        
        // Varsayılan yapılandırmaya kullanıcı yapılandırmasını birleştir
        for (const plugin in pluginDefaults) {
            if (Object.prototype.hasOwnProperty.call(pluginDefaults, plugin)) {
                result[plugin] = {
                    ...pluginDefaults[plugin],
                    ...(options[plugin] || {})
                };
            }
        }
        
        return result;
    }
    
    /**
     * Aktif eklentileri yükler
     * @param {Object} editor - GrapesJS editor örneği
     * @param {Object} config - Eklenti yapılandırması
     */
    function loadActivePlugins(editor, config) {
        // Kontrol et: Eklentiler zaten yüklü mü?
        if (typeof window.grapesjs === 'undefined') {
            console.error('GrapesJS bulunamadı. Eklentiler yüklenemez.');
            return;
        }
        
        // Aktif eklentileri yükle
        try {
            // Basic Blocks
            if (typeof window.grapesjs.plugins.get('gjs-blocks-basic') !== 'undefined') {
                editor.loadPlugin('gjs-blocks-basic', config['blocks-basic']);
                loadedPlugins.push('blocks-basic');
            }
            
            // Webpage Preset
            if (typeof window.grapesjs.plugins.get('gjs-preset-webpage') !== 'undefined') {
                editor.loadPlugin('gjs-preset-webpage', config['preset-webpage']);
                loadedPlugins.push('preset-webpage');
            }
            
            // Forms
            if (typeof window.grapesjs.plugins.get('gjs-plugin-forms') !== 'undefined') {
                editor.loadPlugin('gjs-plugin-forms', config['forms']);
                loadedPlugins.push('forms');
            }
            
            // Custom Code
            if (typeof window.grapesjs.plugins.get('gjs-custom-code') !== 'undefined') {
                editor.loadPlugin('gjs-custom-code', config['custom-code']);
                loadedPlugins.push('custom-code');
            }
            
            // Touch
            if (typeof window.grapesjs.plugins.get('gjs-touch') !== 'undefined') {
                editor.loadPlugin('gjs-touch', config['touch']);
                loadedPlugins.push('touch');
            }
        } catch (error) {
            console.error('Eklentiler yüklenirken hata:', error);
        }
    }
    
    /**
     * Özel bileşenleri kaydeder
     * @param {Object} editor - GrapesJS editor örneği
     */
    function registerCustomComponents(editor) {
        try {
            // Özel buton bileşeni
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
                            },
                            {
                                type: 'select',
                                name: 'class',
                                label: 'Stil',
                                options: [
                                    { value: 'btn-primary', name: 'Primary' },
                                    { value: 'btn-secondary', name: 'Secondary' },
                                    { value: 'btn-success', name: 'Success' },
                                    { value: 'btn-danger', name: 'Danger' },
                                    { value: 'btn-warning', name: 'Warning' },
                                    { value: 'btn-info', name: 'Info' },
                                ]
                            }
                        ]
                    }
                }
            });
            
            // Özel kart bileşeni
            editor.DomComponents.addType('custom-card', {
                model: {
                    defaults: {
                        tagName: 'div',
                        attributes: { class: 'card' },
                        content: `
                            <div class="card-body">
                                <h5 class="card-title">Kart Başlığı</h5>
                                <p class="card-text">Kart içeriği buraya gelir.</p>
                            </div>
                        `,
                        traits: [
                            {
                                type: 'checkbox',
                                name: 'data-has-image',
                                label: 'Görsel Ekle',
                            }
                        ]
                    }
                }
            });
            
            console.log('Özel bileşenler başarıyla kaydedildi');
        } catch (error) {
            console.error('Özel bileşenler kaydedilirken hata:', error);
        }
    }
    
    /**
     * Eklentileri başlatır
     * @param {Object} editor - GrapesJS editor örneği
     */
    function initPlugins(editor) {
        // Aktif eklentileri ve özel yapılandırmaları yükle
        loadPlugins(editor);
    }
    
    // Dışa aktarılan fonksiyonlar
    return {
        load: loadPlugins,
        init: initPlugins,
        registerCustomComponents: registerCustomComponents,
        getLoadedPlugins: function() { return [...loadedPlugins]; }
    };
})();

// Global olarak kullanılabilir yap
window.StudioPlugins = StudioPlugins;