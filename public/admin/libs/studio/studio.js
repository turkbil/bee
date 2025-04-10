/**
 * Studio Editor - Ana Modül
 * GrapesJS tabanlı görsel düzenleyici
 * 
 * Bu dosya modüler yapıdaki diğer tüm bileşenleri bir araya getirir.
 */

/**
 * Yükleme sırasını kontrol eden fonksiyon
 * @param {string} src - Yüklenecek script kaynağı
 * @returns {Promise} - Script yüklendiğinde çözülen promise
 */
function loadScript(src) {
    return new Promise((resolve, reject) => {
        const script = document.createElement('script');
        script.src = src;
        script.async = true;
        
        script.onload = () => resolve();
        script.onerror = () => reject(new Error(`Script yüklenemedi: ${src}`));
        
        document.head.appendChild(script);
    });
}

/**
 * Tüm modülleri yükler ve editor'ü başlatır
 * @param {Object} config - Editor yapılandırması
 */
window.initStudioEditor = async function(config) {
    try {
        // Modüllerin yüklenmesi
        const baseUrl = '/admin/libs/studio/partials';
        const modules = [
            'studio-core.js',
            'studio-blocks.js',
            'studio-ui.js',
            'studio-actions.js',
            'studio-utils.js',
            'studio-plugins.js'
        ];
        
        // Tüm modülleri sırayla yükle
        for (const module of modules) {
            await loadScript(`${baseUrl}/${module}`);
        }
        
        // Editor'ü başlat
        const editor = window.StudioCore.initEditor(config);
        
        // Blokları kaydet
        window.StudioBlocks.registerBlocks(editor);
        
        // Eklentileri yükle
        window.StudioPlugins.loadPlugins(editor);
        window.StudioPlugins.registerCustomComponents(editor);
        window.StudioPlugins.addCustomCommands(editor);
        
        // UI bileşenlerini kur
        window.StudioUI.setupUI(editor);
        
        // Kaydetme, önizleme ve dışa aktarma eylemlerini kur
        window.StudioActions.setupActions(editor, config);
        
        console.log("Studio Editor başarıyla yüklendi!");
        
    } catch (error) {
        console.error("Studio Editor yüklenirken hata oluştu:", error);
        
        if (document.body) {
            const errorElement = document.createElement('div');
            errorElement.className = 'alert alert-danger';
            errorElement.innerHTML = `
                <h4 class="alert-heading">Editor Yüklenirken Hata!</h4>
                <p>Editor modülleri yüklenirken bir hata oluştu. Lütfen sayfayı yenileyip tekrar deneyin.</p>
                <hr>
                <p class="mb-0">Hata detayı: ${error.message}</p>
            `;
            
            const container = document.querySelector('#gjs') || document.querySelector('.editor-main');
            if (container) {
                container.prepend(errorElement);
            } else {
                document.body.prepend(errorElement);
            }
        }
    }
};