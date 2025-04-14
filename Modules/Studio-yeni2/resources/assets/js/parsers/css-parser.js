/**
 * Studio Editor - CSS Parser
 * CSS içeriğini işleyen ve düzenleyen yardımcı fonksiyonlar
 */
const StudioCssParser = (function() {
    /**
     * CSS içeriğini işler
     * @param {string} css - CSS içeriği
     * @returns {string} - İşlenmiş CSS içeriği
     */
    function parseCss(css) {
        if (!css) {
            return '';
        }
        
        try {
            // CSS içeriğini temizle
            const cleanedCss = cleanupCss(css);
            
            return cleanedCss;
        } catch (error) {
            console.error('CSS işleme hatası:', error);
            return '';
        }
    }
    
    /**
     * CSS içeriğini temizler
     * @param {string} css - CSS içeriği
     * @returns {string} - Temizlenmiş CSS içeriği
     */
    function cleanupCss(css) {
        if (!css) {
            return '';
        }
        
        // Gereksiz boşlukları temizle
        let cleanCss = css.trim();
        
        // Tehlikeli CSS özelliklerini temizle
        cleanCss = cleanCss.replace(/expression\s*\(.*?\)/gi, '')
                          .replace(/behavior\s*:.*?;/gi, '')
                          .replace(/-moz-binding\s*:.*?;/gi, '');
        
        return cleanCss;
    }
    
    /**
     * CSS içeriğini optimize eder
     * @param {string} css - CSS içeriği
     * @returns {string} - Optimize edilmiş CSS içeriği
     */
    function optimizeCss(css) {
        if (!css) {
            return '';
        }
        
        try {
            // Gereksiz boşlukları temizle
            const optimizedCss = css.replace(/\s+/g, ' ')
                                  .replace(/\s*{\s*/g, '{')
                                  .replace(/\s*}\s*/g, '}')
                                  .replace(/\s*:\s*/g, ':')
                                  .replace(/\s*;\s*/g, ';')
                                  .replace(/\s*,\s*/g, ',');
            
            return optimizedCss;
        } catch (error) {
            console.error('CSS optimizasyon hatası:', error);
            return css;
        }
    }
    
    /**
     * CSS içeriğini sıkıştırır
     * @param {string} css - CSS içeriği
     * @returns {string} - Sıkıştırılmış CSS içeriği
     */
    function minifyCss(css) {
        if (!css) {
            return '';
        }
        
        try {
            // Yorumları kaldır
            let minifiedCss = css.replace(/\/\*[\s\S]*?\*\//g, '');
            
            // Gereksiz boşlukları kaldır
            minifiedCss = minifiedCss.replace(/\s+/g, ' ')
                                    .replace(/\s*{\s*/g, '{')
                                    .replace(/\s*}\s*/g, '}')
                                    .replace(/\s*:\s*/g, ':')
                                    .replace(/\s*;\s*/g, ';')
                                    .replace(/\s*,\s*/g, ',')
                                    .replace(/;\}/g, '}');
            
            // Gereksiz noktalı virgülleri kaldır
            minifiedCss = minifiedCss.replace(/;}/g, '}');
            
            return minifiedCss;
        } catch (error) {
            console.error('CSS sıkıştırma hatası:', error);
            return css;
        }
    }
    
    /**
     * CSS içeriğini kaydetmeye hazırlar
     * @param {Object} editor - GrapesJS editor örneği
     * @returns {string} - Kaydedilmeye hazır CSS içeriği
     */
    function prepareContentForSave(editor) {
        if (!editor) {
            return '';
        }
        
        try {
            // CSS içeriğini al
            let css = editor.getCss();
            
            // CSS içeriğini temizle
            css = cleanupCss(css);
            
            return css;
        } catch (error) {
            console.error('CSS kaydetme hatası:', error);
            return '';
        }
    }
    
    // Dışa aktarılan API
    return {
        parseCss,
        cleanupCss,
        optimizeCss,
        minifyCss,
        prepareContentForSave
    };
})();

// Global olarak kullanılabilir yap
window.StudioCssParser = StudioCssParser;