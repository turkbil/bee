/**
 * Studio Editor - Ana Uygulama Modülü
 * Tüm modülleri birleştirir ve uygulamayı başlatır
 */
window.StudioApp = (function() {
    /**
     * Uygulamayı başlat
     * @param {Object} config - Yapılandırma parametreleri
     */
    function init(config = {}) {
        console.log('Studio Editor başlatılıyor...', config);
        
        // Yapılandırma değerlerini ayarla
        const defaultConfig = {
            containerId: 'gjs',
            moduleType: 'page',
            moduleId: 0,
            content: '',
            css: '',
            js: '',
            editorOptions: {}
        };
        
        // Özel yapılandırma ile varsayılanları birleştir
        const mergedConfig = window.StudioHelpers.deepMerge(defaultConfig, config);
        
        // Gerekli DOM elementlerini kontrol et
        const container = document.getElementById(mergedConfig.containerId);
        if (!container) {
            console.error(`Editor container (${mergedConfig.containerId}) bulunamadı!`);
            return;
        }
        
        // HTML/CSS/JS içeriklerini al (container'dan veya yapılandırmadan)
        mergedConfig.content = mergedConfig.content || getContentFromElement('html-content');
        mergedConfig.css = mergedConfig.css || getContentFromElement('css-content');
        mergedConfig.js = mergedConfig.js || getContentFromElement('js-content');
        
        // Module tipi ve ID'yi container özelliklerinden al
        mergedConfig.moduleType = mergedConfig.moduleType || container.getAttribute('data-module-type');
        mergedConfig.moduleId = mergedConfig.moduleId || parseInt(container.getAttribute('data-module-id'));
        
        if (!mergedConfig.moduleType || !mergedConfig.moduleId) {
            console.warn('Module tipi veya ID bilgisi eksik! Bu durum içerik kaydetmeyi etkileyebilir.');
        }
        
        // Global yükleme bilgisi
        window.studioLoadedModules = {};
        
        // Sırasıyla modülleri yükle ve bileşenleri oluştur
        console.log('GrapesJS Editor başlatılıyor...');
        
        // GrapesJS editörünü başlat
        const editor = window.StudioEditor.init(mergedConfig);
        if (!editor) {
            console.error('GrapesJS editörü başlatılamadı!');
            return;
        }
        
        // Editor örneğini oluştur
        window.studioEditor = editor;
        window.studioLoadedModules.editor = true;
        
        // Blokları ayarla
        editor.on('load', () => {
            // Blokları kaydet
            try {
                window.StudioBlocks.registerBasicBlocks(editor);
                window.studioLoadedModules.blocks = true;
                console.log('Bloklar başarıyla kaydedildi.');
            } catch (error) {
                console.error('Bloklar kaydedilirken hata:', error);
            }
            
            // Widget bloklarını yükle
            loadWidgetBlocks(editor, mergedConfig);
            
            // UI bileşenlerini ayarla
            try {
                window.StudioUI.init(editor);
                window.studioLoadedModules.ui = true;
                console.log('UI modülü başarıyla başlatıldı.');
            } catch (error) {
                console.error('UI modülü başlatılırken hata:', error);
            }
            
            // Eylem butonlarını ayarla
            try {
                window.StudioActions.setupActions(editor, mergedConfig);
                window.studioLoadedModules.actions = true;
                console.log('Eylem butonları başarıyla ayarlandı.');
            } catch (error) {
                console.error('Eylem butonları ayarlanırken hata:', error);
            }
            
            // Başlatma tamamlandı bilgisi
            console.log('Studio Editor başarıyla başlatıldı!');
            window.StudioEvents.trigger('app:ready', editor);
        });
    }
    
    /**
     * Element içeriğini al
     * @param {string} elementId - Element ID'si
     * @returns {string} - Element içeriği
     */
    function getContentFromElement(elementId) {
        const element = document.getElementById(elementId);
        return element ? element.value || element.innerHTML : '';
    }
    
    /**
     * Widget bloklarını yükle
     * @param {Object} editor - GrapesJS editör örneği
     * @param {Object} config - Yapılandırma parametreleri
     */
    function loadWidgetBlocks(editor, config) {
        // Widget bloklarını AJAX ile yükle
        const widgetApiUrl = '/admin/studio/api/widgets';
        
        fetch(widgetApiUrl)
            .then(response => response.json())
            .then(data => {
                if (data.widgets && data.widgets.length > 0) {
                    // Widget bloklarını kaydet
                    window.StudioBlocks.registerWidgetBlocks(editor, data.widgets);
                    console.log(`${data.widgets.length} widget bloğu kaydedildi.`);
                } else {
                    console.log('Kayıtlı widget bulunamadı.');
                }
            })
            .catch(error => {
                console.error('Widget verileri yüklenirken hata:', error);
            });
    }
    
    return {
        init: init
    };
})();

// DOM yüklendikten sonra başlat
document.addEventListener('DOMContentLoaded', function() {
    // Editör container'ını kontrol et
    const editorContainer = document.getElementById('gjs');
    if (!editorContainer) {
        console.warn('Editor container (#gjs) bulunamadı. Studio Editor başlatılmadı.');
        return;
    }
    
    // Editör yapılandırması
    const config = {
        containerId: 'gjs',
        moduleType: editorContainer.getAttribute('data-module-type'),
        moduleId: parseInt(editorContainer.getAttribute('data-module-id'))
    };
    
    // Uygulamayı başlat
    window.StudioApp.init(config);
});