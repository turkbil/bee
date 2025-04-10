/**
 * Studio Editor Uygulama Başlatıcı
 * Tüm modülleri yükler ve uygulamayı başlatır
 */
document.addEventListener('DOMContentLoaded', function() {
    // Önce düzeltmeleri uygula
    if (window.StudioFix && typeof window.StudioFix.applyFixes === 'function') {
        window.StudioFix.applyFixes();
    }
    
    // Eğer sayfada yapılandırma varsa editörü başlat
    const editorConfig = window.studioEditorConfig || null;
    
    if (editorConfig && typeof window.initStudioEditor === 'function') {
        try {
            const editor = window.initStudioEditor(editorConfig);
            
            // Global olarak erişilebilir olmasını sağla
            window.studioEditor = editor;
            
            // Editor yüklendiğinde stil yöneticisi düzeltmelerini uygula
            if (editor && typeof editor.on === 'function') {
                editor.on('load', function() {
                    if (window.StudioFix && typeof window.StudioFix.fixStyleManager === 'function') {
                        window.StudioFix.fixStyleManager();
                    }
                });
            } else {
                // Alternatif: Gecikmeyle stil yöneticisini düzelt
                setTimeout(function() {
                    if (window.StudioFix && typeof window.StudioFix.fixStyleManager === 'function') {
                        window.StudioFix.fixStyleManager();
                    }
                }, 2000);
            }
        } catch (error) {
            console.error('Editor hatası');
        }
    }
});