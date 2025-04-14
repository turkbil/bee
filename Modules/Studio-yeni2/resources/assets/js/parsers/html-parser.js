/**
 * Studio Editor - HTML Parser
 * HTML içeriğini işleyen ve düzenleyen yardımcı fonksiyonlar
 */
const StudioHtmlParser = (function() {
    /**
     * HTML içeriğini işler ve düzenler
     * @param {string} html - HTML içeriği
     * @returns {string} - İşlenmiş HTML içeriği
     */
    function parseHtml(html) {
        if (!html) {
            return getDefaultHtml();
        }
        
        try {
            // HTML içeriğini temizle
            let cleanedHtml = cleanupHtml(html);
            
            // Boş içerik kontrolü
            if (!cleanedHtml || cleanedHtml === '<body></body>' || cleanedHtml.length < 20) {
                return getDefaultHtml();
            }
            
            // Body içeriğini al
            const bodyContent = extractBodyContent(cleanedHtml);
            
            return bodyContent;
        } catch (error) {
            console.error('HTML işleme hatası:', error);
            return getDefaultHtml();
        }
    }
    
    /**
     * HTML içeriğini temizler
     * @param {string} html - HTML içeriği
     * @returns {string} - Temizlenmiş HTML içeriği
     */
    function cleanupHtml(html) {
        if (!html) {
            return '';
        }
        
        // Gereksiz boşlukları temizle
        let cleanHtml = html.trim();
        
        // Tehlikeli kodları temizle
        cleanHtml = cleanHtml.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '')
                              .replace(/javascript:/gi, 'nojavascript:')
                              .replace(/onerror=/gi, 'data-onerror=')
                              .replace(/onclick=/gi, 'data-onclick=')
                              .replace(/onload=/gi, 'data-onload=')
                              .replace(/onmouseover=/gi, 'data-onmouseover=');
        
        return cleanHtml;
    }
    
    /**
     * Body etiketleri arasındaki içeriği çıkarır
     * @param {string} html - HTML içeriği
     * @returns {string} - Body içeriği
     */
    function extractBodyContent(html) {
        const bodyRegex = /<body[^>]*>([\s\S]*?)<\/body>/i;
        const match = html.match(bodyRegex);
        
        if (match && match[1]) {
            return match[1].trim();
        }
        
        // Body etiketi yoksa, içeriği olduğu gibi döndür
        return html;
    }
    
    /**
     * Varsayılan HTML içeriğini döndürür
     * @returns {string} - Varsayılan HTML içeriği
     */
    function getDefaultHtml() {
        return `
        <div class="container py-5">
            <div class="row">
                <div class="col-md-12">
                    <h2>Hoş Geldiniz</h2>
                    <p>Studio Editör ile sayfanızı düzenlemeye başlayabilirsiniz. Sol taraftaki bileşenleri sürükleyip bırakarak içerik ekleyebilirsiniz.</p>
                    <div class="alert alert-info mt-4">
                        <i class="fas fa-info-circle me-2"></i> Düzenlemelerinizi kaydetmek için sağ üstteki <strong>Kaydet</strong> butonunu kullanın.
                    </div>
                </div>
            </div>
        </div>`;
    }
    
    /**
     * HTML içeriğini kaydetmeye hazırlar
     * @param {Object} editor - GrapesJS editor örneği
     * @returns {string} - Kaydedilmeye hazır HTML içeriği
     */
    function prepareContentForSave(editor) {
        if (!editor) {
            return '';
        }
        
        try {
            // HTML içeriğini al
            let html = editor.getHtml();
            
            // HTML içeriğini temizle
            html = cleanupHtml(html);
            
            // Boş içerik kontrolü
            if (!html || html.trim() === '' || html === '<body></body>') {
                return getDefaultHtml();
            }
            
            return html;
        } catch (error) {
            console.error('HTML kaydetme hatası:', error);
            return getDefaultHtml();
        }
    }
    
    // Dışa aktarılan API
    return {
        parseHtml,
        cleanupHtml,
        extractBodyContent,
        getDefaultHtml,
        prepareContentForSave
    };
})();

// Global olarak kullanılabilir yap
window.StudioHtmlParser = StudioHtmlParser;