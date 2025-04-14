/**
 * Studio HTML Parser
 * HTML içeriğini işleyen ve düzenleyen modül
 */
const StudioHtmlParser = (function() {
    /**
     * HTML içeriğini parse et
     * @param {string} html HTML içeriği
     * @param {Object} options Seçenekler
     * @returns {string} İşlenmiş HTML
     */
    function parseHtml(html, options = {}) {
        // Varsayılan seçenekler
        options = {
            cleanupTags: true,        // Gereksiz etiketleri temizle
            extractBody: true,         // Body içeriğini al
            removeScripts: true,       // Script etiketlerini kaldır
            removeMalicious: true,     // Zararlı içeriği kaldır
            beautify: false,           // HTML'i güzelleştir
            ...options
        };
        
        if (!html) {
            return '';
        }
        
        // İçeriği temizle
        let processedHtml = html;
        
        // Zararlı içeriği kaldır
        if (options.removeMalicious) {
            processedHtml = removeMaliciousContent(processedHtml);
        }
        
        // Script etiketlerini kaldır
        if (options.removeScripts) {
            processedHtml = removeScripts(processedHtml);
        }
        
        // Gereksiz etiketleri temizle
        if (options.cleanupTags) {
            processedHtml = cleanupHtml(processedHtml);
        }
        
        // Body içeriğini al
        if (options.extractBody) {
            processedHtml = extractBodyContent(processedHtml);
        }
        
        // HTML'i güzelleştir
        if (options.beautify) {
            processedHtml = beautifyHtml(processedHtml);
        }
        
        return processedHtml;
    }
    
    /**
     * HTML içeriğini temizle
     * @param {string} html HTML içeriği
     * @returns {string} Temizlenmiş HTML
     */
    function cleanupHtml(html) {
        if (!html) {
            return '';
        }
        
        let processedHtml = html;
        
        // Boş satırları temizle
        processedHtml = processedHtml.replace(/^\s*[\r\n]/gm, '');
        
        // Gereksiz boşlukları temizle
        processedHtml = processedHtml.replace(/\s{2,}/g, ' ');
        
        // Boş paragrafları temizle
        processedHtml = processedHtml.replace(/<p>\s*<\/p>/gi, '');
        
        // Boş div'leri temizle
        processedHtml = processedHtml.replace(/<div>\s*<\/div>/gi, '');
        
        // Boş span'ları temizle
        processedHtml = processedHtml.replace(/<span>\s*<\/span>/gi, '');
        
        // Alt ve title özniteliklerini normalize et
        processedHtml = processedHtml.replace(/alt=["']{2}/gi, 'alt=""');
        processedHtml = processedHtml.replace(/title=["']{2}/gi, 'title=""');
        
        return processedHtml;
    }
    
    /**
     * Body etiketleri arasındaki içeriği çıkar
     * @param {string} html HTML içeriği
     * @returns {string} Body içeriği
     */
    function extractBodyContent(html) {
        if (!html) {
            return '';
        }
        
        // Sadece metin veya basit HTML ise olduğu gibi döndür
        if (!html.includes('<body') && !html.includes('</body>')) {
            return html;
        }
        
        // Body etiketleri arasındaki içeriği bul
        const bodyMatch = /<body[^>]*>([\s\S]*?)<\/body>/i.exec(html);
        
        if (bodyMatch && bodyMatch[1]) {
            return bodyMatch[1].trim();
        }
        
        return html;
    }
    
    /**
     * Script etiketlerini kaldır
     * @param {string} html HTML içeriği
     * @returns {string} Script etiketleri kaldırılmış HTML
     */
    function removeScripts(html) {
        if (!html) {
            return '';
        }
        
        // Script etiketlerini kaldır
        let processedHtml = html.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '');
        
        // Inline olay niteliklerini kaldır (onclick, onload vb.)
        processedHtml = processedHtml.replace(/\son\w+\s*=\s*["'][^"']*["']/gi, '');
        
        return processedHtml;
    }
    
    /**
     * Zararlı içeriği kaldır
     * @param {string} html HTML içeriği
     * @returns {string} Zararlı içerik kaldırılmış HTML
     */
    function removeMaliciousContent(html) {
        if (!html) {
            return '';
        }
        
        let processedHtml = html;
        
        // JavaScript URL'lerini kaldır
        processedHtml = processedHtml.replace(/\bhref\s*=\s*["']javascript:[^"']*["']/gi, 'href="javascript:void(0)"');
        
        // Diğer tehlikeli etiketleri kaldır
        const dangerousTags = ['object', 'embed', 'base', 'meta'];
        dangerousTags.forEach(tag => {
            const regex = new RegExp(`<${tag}\\b[^<]*(?:(?!<\\/${tag}>)<[^<]*)*<\\/${tag}>`, 'gi');
            processedHtml = processedHtml.replace(regex, '');
        });
        
        return processedHtml;
    }
    
    /**
     * HTML'i güzelleştir
     * @param {string} html HTML içeriği
     * @returns {string} Güzelleştirilmiş HTML
     */
    function beautifyHtml(html) {
        if (!html) {
            return '';
        }
        
        try {
            // Basit güzelleştirme
            // 1. Her etiketin öncesine yeni satır ekle
            let processedHtml = html.replace(/></g, '>\n<');
            
            // 2. Kapatma etiketleri için boşluk ekle
            processedHtml = processedHtml.replace(/<\/([a-z0-9]+)>/gi, '</$1>');
            
            // 3. Self-closing etiketler için boşluk ekle
            processedHtml = processedHtml.replace(/<([a-z0-9]+)([^>]*)\/>/gi, '<$1$2 />');
            
            return processedHtml;
        } catch (e) {
            console.error('HTML güzelleştirme hatası:', e);
            return html;
        }
    }
    
    // Dışa aktarılan fonksiyonlar
    return {
        parseHtml: parseHtml,
        cleanupHtml: cleanupHtml,
        extractBodyContent: extractBodyContent,
        removeScripts: removeScripts,
        removeMaliciousContent: removeMaliciousContent,
        beautifyHtml: beautifyHtml
    };
})();

// Global olarak kullanılabilir yap
window.StudioHtmlParser = StudioHtmlParser;