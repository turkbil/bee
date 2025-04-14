/**
 * Studio Editor - Preview Action
 * İçerik önizleme işlemlerini yönetir
 */
const StudioPreviewAction = (function() {
    /**
     * Önizleme düğmesini yapılandırır
     * @param {Object} editor - GrapesJS editor örneği
     */
    function setupPreviewButton(editor) {
        const previewBtn = document.getElementById('preview-btn');
        if (previewBtn) {
            // Eski listener'ları temizle
            const newPreviewBtn = previewBtn.cloneNode(true);
            if (previewBtn.parentNode) {
                previewBtn.parentNode.replaceChild(newPreviewBtn, previewBtn);
            }
            
            newPreviewBtn.addEventListener('click', function() {
                showPreview(editor);
            });
        }
    }
    
    /**
     * Önizleme penceresini gösterir
     * @param {Object} editor - GrapesJS editor örneği
     */
    function showPreview(editor) {
        // HTML, CSS ve JS içeriğini al
        const html = StudioCore.prepareContentForSave(editor);
        const css = StudioCore.prepareCssForSave(editor);
        const jsContentEl = document.getElementById('js-content');
        const js = jsContentEl ? jsContentEl.value : '';
        
        // Tam HTML sayfası oluştur
        const previewContent = generatePreviewHTML(html, css, js);
        
        // Önizleme penceresini aç
        openPreviewWindow(previewContent);
    }
    
    /**
     * Önizleme içeriğini oluşturur
     * @param {string} html - HTML içeriği
     * @param {string} css - CSS içeriği
     * @param {string} js - JavaScript içeriği
     * @returns {string} - Önizleme HTML içeriği
     */
    function generatePreviewHTML(html, css, js) {
        return `<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sayfa Önizleme</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
${css}
    </style>
</head>
<body>
${html}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
${js}
    </script>
</body>
</html>`;
    }
    
    /**
     * Önizleme penceresini açar
     * @param {string} content - HTML içeriği
     */
    function openPreviewWindow(content) {
        // Önizleme penceresi oluştur
        const previewWindow = window.open('', '_blank');
        
        // İçeriği yaz
        previewWindow.document.open();
        previewWindow.document.write(content);
        previewWindow.document.close();
    }
    
    // Dışa aktarılan API
    return {
        setupPreviewButton,
        showPreview,
        generatePreviewHTML
    };
})();

// Global olarak kullanılabilir yap
window.StudioPreviewAction = StudioPreviewAction;