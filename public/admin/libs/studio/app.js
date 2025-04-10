/**
 * Studio Editor Uygulama Başlatıcı
 * Tüm modülleri yükler ve uygulamayı başlatır
 */
document.addEventListener('DOMContentLoaded', function() {
    // Eğer sayfada yapılandırma varsa editörü başlat
    const editorConfig = window.studioEditorConfig || null;
    
    if (editorConfig && typeof window.initStudioEditor === 'function') {
        const editor = window.initStudioEditor(editorConfig);
        
        // Global olarak erişilebilir olmasını sağla
        window.studioEditor = editor;
    } else {
        console.log("Studio Editor yapılandırması bulunamadı veya başlatılamadı.");
    }
});