/**
 * Studio Editor - Eklentiler Modülü
 * GrapesJS eklentilerini yükler ve yapılandırır
 */
window.StudioPlugins = (function() {
    /**
     * Desteklenen eklentiler
     */
    const supportedPlugins = {};
    
    /**
     * Eklentileri yükle
     * @param {Object} editor - GrapesJS editor örneği
     */
    function loadPlugins(editor) {
        // Eklentiler devre dışı bırakıldı
        console.log('Eklentiler devre dışı bırakıldı.');
    }
    
    /**
     * Özel bileşenleri kaydet
     * @param {Object} editor - GrapesJS editor örneği
     */
    function registerCustomComponents(editor) {
        // Şimdilik özel bileşenler devre dışı bırakıldı
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