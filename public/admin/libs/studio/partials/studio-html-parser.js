/**
 * Studio Editor - HTML Parser Modülü
 * HTML içeriğini düzenlemek için yardımcı fonksiyonlar
 */

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
        if (window.StudioConfig && typeof window.StudioConfig.getConfig === 'function') {
            return window.StudioConfig.getConfig('defaultHtml');
        }
        
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
        if (window.StudioConfig && typeof window.StudioConfig.getFullHtmlTemplate === 'function') {
            return window.StudioConfig.getFullHtmlTemplate(html, css, js);
        }
        
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
    
    /**
     * HTML içeriğindeki widget etiketlerini işle
     * @param {string} html HTML içeriği
     * @return {string} İşlenmiş HTML
     */
    function processWidgetTags(html) {
        if (!html || typeof html !== 'string') {
            return html;
        }
        
        // Widget etiketlerini işle
        html = html.replace(/<widget\s+id="(\d+)"[^>]*><\/widget>/g, function(match, widgetId) {
            return `<div data-widget-id="${widgetId}" data-type="widget" class="gjs-widget-wrapper">
                <div class="widget-placeholder">Widget #${widgetId}</div>
            </div>`;
        });
        
        return html;
    }
                
    /**
     * HTML girişini parse et ve widget embed referanslarını dönüştür
     * Bu fonksiyon, veritabanından yüklenip GrapesJS'e gönderilen HTML içeriğini işler
     * ve basit widget referanslarını genişletilmiş yapıya dönüştürür
     * 
     * @param {string} html HTML içeriği
     * @return {string} İşlenmiş HTML
     */
    function parseInput(html) {
        if (!html || typeof html !== 'string') return html;
        
        // Module shortcode'ları doğrudan içeriğin yükleneceği yapıya dönüştür
        html = html.replace(/\[\[module:(\d+)\]\]/gi, (match, moduleId) => {
            // HTML çıktısı olarak daha basit bir div yapısı oluştur 
            // İçine otomatik yükleme scripti ekle
            return `<div class="module-widget-container" data-widget-module-id="${moduleId}" id="module-widget-${moduleId}">
                <div class="module-widget-label"><i class="fa fa-cube me-1"></i> Module #${moduleId}</div>
                <div class="module-widget-content-placeholder" id="module-content-${moduleId}">
                    <div class="widget-loading" style="display:none;text-align:center; padding:20px;">
                        <i class="fa fa-spin fa-spinner"></i> Modül içeriği yükleniyor...
                    </div>
                </div>
                <script>
                    (function() {
                        // Sayfa yüklenir yüklenmez modül içeriğini yükle
                        if (typeof window.studioLoadModuleWidget === 'function') {
                            window.studioLoadModuleWidget('${moduleId}');
                        } else {
                            // studioLoadModuleWidget fonksiyonu henüz yüklenmediyse, 
                            // yükleme fonksiyonu gelene kadar bekle
                            var checkInterval = setInterval(function() {
                                if (typeof window.studioLoadModuleWidget === 'function') {
                                    window.studioLoadModuleWidget('${moduleId}');
                                    clearInterval(checkInterval);
                                }
                            }, 50);
                        }
                    })();
                </script>
            </div>`;
        });
        
        // Widget embed referanslarını daha karmaşık yapıya çevir
        const simpleWidgetEmbedRegex = /<div[^>]*class="([^"]*widget-embed[^"]*)"[^>]*data-tenant-widget-id="(\d+)"[^>]*><\/div>/gi;
        
        html = html.replace(simpleWidgetEmbedRegex, (match, embedClass, widgetId) => {
            return `<div class="widget-embed" data-tenant-widget-id="${widgetId}" id="widget-embed-${widgetId}">
                <div class="widget-content-placeholder" id="widget-content-${widgetId}">
                    <div class="widget-loading" style="text-align:center; padding:20px;">
                        <i class="fa fa-spin fa-spinner"></i> Widget içeriği yükleniyor...
                    </div>
                </div>
            </div>`;
        });
        
        // Alternatif formları da kontrol et
        const altSimpleWidgetEmbedRegex = /<div[^>]*data-tenant-widget-id="(\d+)"[^>]*class="([^"]*widget-embed[^"]*)"[^>]*><\/div>/gi;
        
        html = html.replace(altSimpleWidgetEmbedRegex, (match, widgetId, embedClass) => {
            return `<div class="widget-embed" data-tenant-widget-id="${widgetId}" id="widget-embed-${widgetId}">
                <div class="widget-content-placeholder" id="widget-content-${widgetId}">
                    <div class="widget-loading" style="text-align:center; padding:20px;">
                        <i class="fa fa-spin fa-spinner"></i> Widget içeriği yükleniyor...
                    </div>
                </div>
            </div>`;
        });
        
        // Eksik HTML içeren formları da kontrol et
        const emptyWidgetEmbedRegex = /<div[^>]*data-tenant-widget-id="(\d+)"[^>]*><\/div>/gi;
        
        html = html.replace(emptyWidgetEmbedRegex, (match, widgetId) => {
            return `<div class="widget-embed" data-tenant-widget-id="${widgetId}" id="widget-embed-${widgetId}">
                <div class="widget-content-placeholder" id="widget-content-${widgetId}">
                    <div class="widget-loading" style="text-align:center; padding:20px;">
                        <i class="fa fa-spin fa-spinner"></i> Widget içeriği yükleniyor...
                    </div>
                </div>
            </div>`;
        });
        
        return html;
    }

    /**
     * HTML çıktısını parse et ve widget referanslarını kısa kodlara dönüştür
     * Bu fonksiyon, GrapesJS'den kaydetme için çıkan HTML içeriğini işler
     * ve widget bileşenlerini kısa kodlara dönüştürür
     * 
     * @param {string} html HTML içeriği
     * @return {string} İşlenmiş HTML
     */
    function parseOutput(html) {
        if (!html || typeof html !== 'string') return html;
        
        // Widget embed divlerini işleyen regex (widget-embed tipinde olanlar)
        // Bu widget-embed'ler görsel önizleme için kullanılan yapılar
        const widgetEmbedRegex = /<div[^>]*class="([^"]*widget-embed[^"]*)"[^>]*data-tenant-widget-id="(\d+)"[^>]*>[\s\S]*?<div[^>]*class="([^"]*)widget-content-placeholder[^"]*"[^>]*>[\s\S]*?<\/div>[\s\S]*?<\/div>/gi;
        
        // Widget embed divlerini daha basit bir formata dönüştür
        html = html.replace(widgetEmbedRegex, (match, embedClass, widgetId) => {
            return `<div class="widget-embed" data-tenant-widget-id="${widgetId}"></div>`;
        });
        
        // Module widget'ları için regex
        const moduleWidgetRegex = /<div[^>]*class="([^"]*module-widget-container[^"]*)"[^>]*data-widget-module-id="(\d+)"[^>]*>[\s\S]*?<\/div>/gi;
        
        // Module widget divlerini module kısa koduna dönüştür
        html = html.replace(moduleWidgetRegex, (match, containerClass, moduleId) => {
            return `[[module:${moduleId}]]`;
        });
        
        // Alternatif module formları için
        const altModuleWidgetRegex = /<div[^>]*data-widget-module-id="(\d+)"[^>]*class="([^"]*module-widget-container[^"]*)"[^>]*>[\s\S]*?<\/div>/gi;
        
        html = html.replace(altModuleWidgetRegex, (match, moduleId, containerClass) => {
            return `[[module:${moduleId}]]`;
        });
        
        // Basit module widget elementi için
        const simpleModuleWidgetRegex = /<div[^>]*data-widget-module-id="(\d+)"[^>]*>[\s\S]*?<\/div>/gi;
        
        html = html.replace(simpleModuleWidgetRegex, (match, moduleId) => {
            return `[[module:${moduleId}]]`;
        });
        
        // Alternatif olarak, eksik veya farklı yapıda olan widget embed divlerini de yakala
        const altWidgetEmbedRegex = /<div[^>]*data-tenant-widget-id="(\d+)"[^>]*class="([^"]*widget-embed[^"]*)"[^>]*>[\s\S]*?<\/div>/gi;
        
        html = html.replace(altWidgetEmbedRegex, (match, widgetId, embedClass) => {
            return `<div class="widget-embed" data-tenant-widget-id="${widgetId}"></div>`;
        });
        
        // Widget etiketlerini de işle
        html = processWidgetTags(html);
        
        return html;
    }
    
    /**
     * HTML içindeki widget embed referanslarını sayfa görüntüleme sırasında işle
     * Bu fonksiyon, sayfa render edilirken çalıştırılacak PHP koduna dönüştürür
     * 
     * @param {string} html HTML içeriği  
     * @return {string} İşlenmiş HTML, PHP kod parçaları içerebilir
     */
    function processWidgetEmbedsInContent(html) {
        if (!html || typeof html !== 'string') return html;
        
        // Widget embed referanslarını dönüştür - her formatta
        const widgetEmbedRegex = /<div[^>]*(?:class="[^"]*widget-embed[^"]*"|data-tenant-widget-id="(\d+)")(?:[^>]*(?:class="[^"]*widget-embed[^"]*"|data-tenant-widget-id="(\d+)"))?[^>]*>(?:[\s\S]*?<\/div>)?/gi;
        
        return html.replace(widgetEmbedRegex, (match) => {
            // Widget ID'sini bul
            const idMatch = match.match(/data-tenant-widget-id="(\d+)"/i);
            if (!idMatch || !idMatch[1]) return match; // ID bulunamazsa orijinal içeriği döndür
            
            const widgetId = idMatch[1];
            
            // PHP render kodu
            return `<?php echo app('widget.service')->renderSingleWidget($tenantWidget = \\Modules\\WidgetManagement\\app\\Models\\TenantWidget::find(${widgetId})); ?>`;
        });
    }
    
    /**
     * HTML içeriğindeki tüm widget referanslarını dinamik referans yapısına dönüştür
     * 
     * @param {string} html HTML içeriği
     * @return {string} İşlenmiş HTML
     */
    function convertAllWidgetReferencesToEmbeds(html) {
        if (!html || typeof html !== 'string') return html;
        
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        
        // Replace <widget> tags
        doc.querySelectorAll('widget[id]').forEach(el => {
            const id = el.getAttribute('id');
            const placeholder = doc.createElement('div');
            placeholder.className = 'widget-embed';
            placeholder.setAttribute('data-tenant-widget-id', id);
            el.replaceWith(placeholder);
        });
        
        // Replace <div data-widget-id data-type="widget">
        doc.querySelectorAll('div[data-widget-id][data-type="widget"]').forEach(el => {
            const id = el.getAttribute('data-widget-id');
            const placeholder = doc.createElement('div');
            placeholder.className = 'widget-embed';
            placeholder.setAttribute('data-tenant-widget-id', id);
            el.replaceWith(placeholder);
        });
        
        // Replace <div data-tenant-widget-id>
        doc.querySelectorAll('div[data-tenant-widget-id]').forEach(el => {
            const id = el.getAttribute('data-tenant-widget-id');
            const placeholder = doc.createElement('div');
            placeholder.className = 'widget-embed';
            placeholder.setAttribute('data-tenant-widget-id', id);
            el.replaceWith(placeholder);
        });
        
        // Handle moustache placeholders
        const result = doc.body.innerHTML.replace(/\{\{widget:(\d+)\}\}/g, (match, widgetId) => `<div class="widget-embed" data-tenant-widget-id="${widgetId}"></div>`);
        
        return result;
    }
    
    /**
     * Widget embed yapısını arar ve varsa ID'yi döndürür
     * 
     * @param {string} html HTML içeriği  
     * @return {Array} Bulunan tenant widget ID'leri
     */
    function findWidgetEmbeds(html) {
        if (!html || typeof html !== 'string') return [];
        
        const widgetIds = [];
        let match;
        
        // Her türlü widget referans formatını ara
        const regexFormats = [
            /<div[^>]*class="[^"]*widget-embed[^"]*"[^>]*data-tenant-widget-id="(\d+)"[^>]*>/gi,
            /<div[^>]*data-tenant-widget-id="(\d+)"[^>]*class="[^"]*widget-embed[^"]*"[^>]*>/gi,
            /<div[^>]*data-widget-id="(\d+)"[^>]*data-type="widget"[^>]*>/gi,
            /<div[^>]*data-type="widget"[^>]*data-widget-id="(\d+)"[^>]*>/gi,
            /<widget\s+id="(\d+)"[^>]*><\/widget>/gi,
            /\{\{widget:(\d+)\}\}/gi,
            /\[\[module:(\d+)\]\]/gi
        ];
        
        regexFormats.forEach(regex => {
            while ((match = regex.exec(html)) !== null) {
                widgetIds.push(match[1]);
            }
        });
        
        // Tekrar edenleri temizle
        return [...new Set(widgetIds)];
    }
    
    return {
        parseAndFixHtml: parseAndFixHtml,
        extractCss: extractCss,
        extractJs: extractJs,
        sanitizeHtml: sanitizeHtml,
        getDefaultContent: getDefaultContent,
        buildFullHtml: buildFullHtml,
        processWidgetTags: processWidgetTags,
        parseOutput: parseOutput,
        parseInput: parseInput,
        processWidgetEmbedsInContent: processWidgetEmbedsInContent,
        convertAllWidgetReferencesToEmbeds: convertAllWidgetReferencesToEmbeds,
        findWidgetEmbeds: findWidgetEmbeds
    };
})();