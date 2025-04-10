/**
 * Studio Editor - Uygulama Başlatıcı
 * Tüm modülleri, eklentileri ve özellikleri başlatır
 */
(function() {
    /**
     * Gerekli script'leri dinamik olarak yükler
     * @param {string} src - Script URL'si
     * @param {Function} callback - Yüklendikten sonra çağrılacak fonksiyon
     */
    function loadScript(src, callback) {
        const script = document.createElement('script');
        script.src = src;
        script.async = true;
        
        script.onload = callback || function() {};
        script.onerror = function() {
            console.error(`Script yüklenemedi: ${src}`);
        };
        
        document.head.appendChild(script);
    }
    
    /**
     * Uygulama önyükleme fonksiyonu
     */
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Studio Editor yükleniyor...');
        
        // Temel GrapesJS yüklü mü kontrol et
        if (typeof grapesjs === 'undefined') {
            console.error('GrapesJS yüklü değil! Studio Editor başlatılamıyor.');
            return;
        }
        
        // Temel modülleri yükle
        const modules = [
            'partials/studio-core.js',
            'partials/studio-utils.js',
            'partials/studio-blocks.js',
            'partials/studio-ui.js',
            'partials/studio-actions.js',
            'partials/studio-plugins.js',
            'partials/studio-plugins-loader.js'
        ];
        
        let loadedModules = 0;
        
        // Modülleri sırayla yükle
        modules.forEach(function(module) {
            loadScript(`/admin/libs/studio/${module}`, function() {
                loadedModules++;
                console.log(`Modül yüklendi: ${module}`);
                
                // Tüm modüller yüklendiyse
                if (loadedModules === modules.length) {
                    console.log('Tüm modüller yüklendi. Studio Editor hazır!');
                }
            });
        });
        
        // Eklentileri yükle (eklentiler direk çağrılmaz, studio.js tarafından kullanılır)
        const pluginsToLoad = [
            'blocks-basic-master.min.js',
            'preset-webpage-master.min.js',
            'style-bg-master.min.js',
            'grapesjs-plugin-forms.min.js',
            'components-custom-code-master.min.js',
            'touch-master.min.js',
            'components-countdown-master.min.js',
            'components-tabs-master.min.js',
            'components-typed-master.min.js'
        ];
        
        // Eklentileri paralel olarak yükle (sırayla yükleme gerekmez)
        pluginsToLoad.forEach(function(plugin) {
            loadScript(`/admin/libs/studio/plugins/${plugin}`, function() {
                console.log(`Eklenti yüklendi: ${plugin}`);
            });
        });
    });
    
    /**
     * initStudioEditor fonksiyonu için global tanımlamayı yap
     * Bu fonksiyon diğer modüller yüklendikten sonra kullanılabilir
     */
    window.initStudioEditor = window.initStudioEditor || function(config) {
        console.warn('Studio Editor modülleri henüz yüklenmedi!');
        
        // Modüller yüklendiğinde tekrar dene
        const checkInterval = setInterval(function() {
            if (window.StudioCore && window.StudioBlocks && window.StudioUI && 
                window.StudioActions && window.StudioUtils && window.StudioPlugins) {
                
                clearInterval(checkInterval);
                
                // Editor'ü başlat
                const editor = window.StudioCore.initEditor(config);
                
                // Blokları kaydet
                window.StudioBlocks.registerBlocks(editor);
                
                // UI bileşenlerini kur
                window.StudioUI.setupUI(editor);
                
                // Eklentileri yükle
                window.StudioPlugins.loadPlugins(editor);
                window.StudioPlugins.registerCustomComponents(editor);
                window.StudioPlugins.addCustomCommands(editor);
                
                // Eylem butonlarını kur
                window.StudioActions.setupActions(editor, config);
                
                console.log("Studio Editor başarıyla yüklendi!");
                
                return editor;
            }
        }, 100);
    };
})();