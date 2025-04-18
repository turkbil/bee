/**
 * Studio Editor - HTML Parser Modülü
 * HTML içeriğini düzenlemek için yardımcı fonksiyonlar
 */
// public/admin/libs/studio/partials/studio-html-parser.js

window.StudioHtmlParser = (function() {
    /**
     * HTML içeriğini temizler ve düzeltmeler yapar
     *
     * @param {string} htmlString HTML içeriği
     * @return {string} Temizlenmiş HTML
     */
    function parseAndFixHtml(htmlString) {
        if (!htmlString || typeof htmlString !== 'string') {
            return getDefaultContent();
        }
        
        // HTML içeriğini temizle
        const cleanHtml = htmlString.trim();
        
        // Sadece <body></body> gibi boş bir yapı mı kontrol et
        if (cleanHtml === '<body></body>' || 
            cleanHtml === '<body> </body>' ||
            cleanHtml.length < 20) {
            return getDefaultContent();
        }
        
        // Body içeriğini al
        let bodyContent = cleanHtml;
        const bodyMatchRegex = /<body[^>]*>([\s\S]*?)<\/body>/;
        const bodyMatch = bodyMatchRegex.exec(cleanHtml);
        
        if (bodyMatch && bodyMatch[1]) {
            bodyContent = bodyMatch[1].trim();
        }
        
        // Eğer içerik hala boşsa, varsayılan içerik ver
        if (!bodyContent || bodyContent.length < 10) {
            return getDefaultContent();
        }
        
        return bodyContent;
    }
    
    /**
     * HTML içeriğinden CSS içeriğini ayırır
     *
     * @param {string} htmlString HTML içeriği
     * @return {string} CSS içeriği
     */
    function extractCss(htmlString) {
        if (!htmlString || typeof htmlString !== 'string') {
            return '';
        }
        
        let css = '';
        const styleRegex = /<style[^>]*>([\s\S]*?)<\/style>/g;
        let match;
        
        while ((match = styleRegex.exec(htmlString)) !== null) {
            css += match[1] + '\n';
        }
        
        return css.trim();
    }
    
    /**
     * HTML içeriğinden JS içeriğini ayırır
     *
     * @param {string} htmlString HTML içeriği
     * @return {string} JS içeriği
     */
    function extractJs(htmlString) {
        if (!htmlString || typeof htmlString !== 'string') {
            return '';
        }
        
        let js = '';
        const scriptRegex = /<script[^>]*>([\s\S]*?)<\/script>/g;
        let match;
        
        while ((match = scriptRegex.exec(htmlString)) !== null) {
            // src attribute'u olmayan script taglerini al
            const hasSource = /<script[^>]*src=[^>]*>/.test(match[0]);
            if (!hasSource) {
                js += match[1] + '\n';
            }
        }
        
        return js.trim();
    }
    
    /**
     * HTML içeriğini güvenli hale getirir
     *
     * @param {string} html HTML içeriği
     * @return {string} Güvenli HTML
     */
    function sanitizeHtml(html) {
        if (!html || typeof html !== 'string') {
            return '';
        }
        
        // İzin verilen HTML etiketleri
        const allowedTags = [
            'div', 'span', 'p', 'br', 'hr', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
            'ul', 'ol', 'li', 'dl', 'dt', 'dd',
            'table', 'thead', 'tbody', 'tfoot', 'tr', 'th', 'td',
            'a', 'img', 'strong', 'em', 'b', 'i', 'u', 'strike', 'small', 'sub', 'sup',
            'blockquote', 'pre', 'code',
            'form', 'input', 'select', 'option', 'textarea', 'button', 'label',
            'section', 'article', 'aside', 'header', 'footer', 'nav', 'main',
            'figure', 'figcaption', 'audio', 'video', 'source', 'canvas',
            'iframe'
        ];
        
        // Burada basit bir sanitizer örneği veriyoruz
        // Gerçek uygulamada DOMPurify gibi daha kapsamlı bir çözüm kullanılmalıdır
        
        // XSS koruması için basit önlemler
        html = html.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '');
        html = html.replace(/on\w+="[^"]*"/gi, '');
        html = html.replace(/on\w+='[^']*'/gi, '');
        
        return html;
    }
    
    /**
     * Varsayılan içerik
     *
     * @return {string} Varsayılan HTML içeriği
     */
    function getDefaultContent() {
        return `
        <div class="container py-4">
            <div class="row">
                <div class="col-12">
                    <h1 class="mb-4">Yeni Sayfa</h1>
                    <p class="lead">Bu sayfayı düzenlemek için sol taraftaki bileşenleri kullanabilirsiniz.</p>
                    <div class="alert alert-info mt-4">
                        <i class="fas fa-info-circle me-2"></i> Studio Editor ile görsel düzenleme yapabilirsiniz.
                        Düzenlemelerinizi kaydetmek için sağ üstteki Kaydet butonunu kullanın.
                    </div>
                </div>
            </div>
        </div>`;
    }
    
    /**
     * HTML içeriğine CSS ve JS ekler
     *
     * @param {string} html HTML içeriği
     * @param {string} css CSS içeriği
     * @param {string} js JS içeriği
     * @return {string} Birleştirilmiş tam HTML
     */
    function buildFullHtml(html, css, js) {
        return `<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Studio İçeriği</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
${css}
    </style>
</head>
<body>
${html}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
${js}
    </script>
</body>
</html>`;
    }
    
    return {
        parseAndFixHtml: parseAndFixHtml,
        extractCss: extractCss,
        extractJs: extractJs,
        sanitizeHtml: sanitizeHtml,
        getDefaultContent: getDefaultContent,
        buildFullHtml: buildFullHtml
    };
})();