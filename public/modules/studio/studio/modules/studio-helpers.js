/**
 * Studio Editor - Yardımcı Fonksiyonlar Modülü
 * Genel yardımcı fonksiyonlar ve araçlar
 */
window.StudioHelpers = (function() {
    /**
     * DOM elementini seçer
     * @param {string} selector - CSS seçici
     * @param {boolean} all - Tüm eşleşmeleri mi döndürsün?
     * @param {HTMLElement} parent - Üst element (varsayılan: document)
     * @returns {HTMLElement|NodeList|null} - Bulunan element(ler)
     */
    function selectElement(selector, all = false, parent = document) {
        return all 
            ? parent.querySelectorAll(selector) 
            : parent.querySelector(selector);
    }
    
    /**
     * String'i güvenli bir şekilde HTML'e dönüştürür
     * @param {string} str - Dönüştürülecek metin
     * @returns {string} - Güvenli HTML
     */
    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }
    
    /**
     * HTML'i güvenli bir şekilde string'e dönüştürür
     * @param {string} html - Dönüştürülecek HTML
     * @returns {string} - String
     */
    function unescapeHtml(html) {
        const div = document.createElement('div');
        div.innerHTML = html;
        return div.textContent;
    }
    
    /**
     * Widget içeriğini temizler
     * @param {string} html - Temizlenecek HTML
     * @returns {string} - Temizlenmiş HTML
     */
    function cleanWidgetHtml(html) {
        // Script etiketlerini temizle
        return html.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '');
    }
    
    /**
     * Rastgele benzersiz ID oluşturur
     * @param {string} prefix - ID öneki (varsayılan: 'studio-')
     * @returns {string} - Benzersiz ID
     */
    function generateUniqueId(prefix = 'studio-') {
        return prefix + Math.random().toString(36).substring(2, 11);
    }
    
    /**
     * URL parametrelerini ayrıştırır
     * @param {string} url - Ayrıştırılacak URL (varsayılan: window.location.href)
     * @returns {Object} - Parametre nesnesi
     */
    function parseUrlParams(url = window.location.href) {
        const params = {};
        const parser = document.createElement('a');
        parser.href = url;
        
        const searchParams = new URLSearchParams(parser.search);
        for (const [key, value] of searchParams) {
            params[key] = value;
        }
        
        return params;
    }
    
    /**
     * AJAX isteği gönderir
     * @param {Object} options - İstek seçenekleri
     * @returns {Promise} - İstek sonucu
     */
    function ajax(options) {
        const defaultOptions = {
            url: '',
            method: 'GET',
            data: null,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        };
        
        // Seçenekleri birleştir
        const config = { ...defaultOptions, ...options };
        
        // CSRF token'ı al
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (csrfToken) {
            config.headers['X-CSRF-TOKEN'] = csrfToken;
        }
        
        // Fetch API ile istek gönder
        return fetch(config.url, {
            method: config.method,
            headers: config.headers,
            body: config.data ? JSON.stringify(config.data) : null
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Sunucu hatası: ' + response.status);
            }
            
            // İçerik türüne göre dönüştür
            const contentType = response.headers.get('Content-Type');
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            }
            
            return response.text();
        });
    }
    
    /**
     * Dosya yükler
     * @param {File} file - Yüklenecek dosya
     * @param {string} url - Yükleme URL'si
     * @param {Object} additionalData - Ek veri
     * @returns {Promise} - Yükleme sonucu
     */
    function uploadFile(file, url, additionalData = {}) {
        // FormData oluştur
        const formData = new FormData();
        formData.append('file', file);
        
        // Ek verileri ekle
        Object.keys(additionalData).forEach(key => {
            formData.append(key, additionalData[key]);
        });
        
        // CSRF token'ı al
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        // Fetch API ile yükle
        return fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Dosya yükleme hatası: ' + response.status);
            }
            return response.json();
        });
    }
    
    /**
     * HTML içeriğini temizler ve düzenler
     * @param {string} html - Temizlenecek HTML
     * @returns {string} - Temizlenmiş HTML
     */
    function sanitizeHtml(html) {
        // HTML içeriği boşsa, boş string döndür
        if (!html) return '';
        
        try {
            // Zarar verebilecek etiketleri temizle
            let cleanHtml = html
                // Script etiketlerini temizle
                .replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '')
                // iframe kaynaklarını kontrol et - güvenilir kaynaklara izin ver
                .replace(/(<iframe[^>]*src=["'])(?!https:\/\/(www\.youtube\.com|www\.vimeo\.com|www\.google\.com\/maps))(.*?["'][^>]*>)/gi, '$1about:blank$3')
                // style etiketlerini temizle
                .replace(/<style\b[^<]*(?:(?!<\/style>)<[^<]*)*<\/style>/gi, '')
                // on* event işleyicilerini temizle
                .replace(/\son\w+\s*=\s*["'].*?["']/gi, '');
                
            return cleanHtml;
        } catch (error) {
            console.error('HTML temizleme hatası:', error);
            // Hata durumunda orijinal içeriği döndür
            return html;
        }
    }
    
    /**
     * CSS içeriğini temizler ve düzenler
     * @param {string} css - Temizlenecek CSS
     * @returns {string} - Temizlenmiş CSS
     */
    function sanitizeCss(css) {
        // CSS içeriği boşsa, boş string döndür
        if (!css) return '';
        
        try {
            // Zarar verebilecek CSS'i temizle
            let cleanCss = css
                // import işlemlerini temizle
                .replace(/@import\s+url\s*\(\s*["']?[^)]*["']?\s*\)\s*;?/gi, '')
                // url içeriklerini kontrol et
                .replace(/url\s*\(\s*["']?((?!data:)[^)]*?)["']?\s*\)/gi, 'url("$1")');
                
            return cleanCss;
        } catch (error) {
            console.error('CSS temizleme hatası:', error);
            // Hata durumunda orijinal içeriği döndür
            return css;
        }
    }
    
    /**
     * JavaScript içeriğini temizler ve düzenler
     * @param {string} js - Temizlenecek JavaScript
     * @returns {string} - Temizlenmiş JavaScript
     */
    function sanitizeJs(js) {
        // JS içeriği boşsa, boş string döndür
        if (!js) return '';
        
        try {
            // Potansiyel tehlikeli API'leri kontrol et
            const dangerousAPIs = [
                'eval\\s*\\(', 
                'Function\\s*\\(', 
                'document\\.write',
                'window\\.location\\s*=',
                'location\\.href\\s*=',
                'localStorage\\.(set|get|remove|clear)\\s*\\(',
                'sessionStorage\\.(set|get|remove|clear)\\s*\\(',
                'document\\.cookie\\s*='
            ];
            
            // Tehlikeli API'leri içeren kod varsa uyarı ekle
            const dangerousRegex = new RegExp(dangerousAPIs.join('|'), 'gi');
            if (dangerousRegex.test(js)) {
                return `/* 
  UYARI: Bu JavaScript kodunda potansiyel olarak güvenlik riski taşıyan API'ler kullanılmıştır.
  Lütfen kodunuzu kontrol edin ve güvenli olmayan kodları temizleyin.
*/

${js}`;
            }
            
            return js;
        } catch (error) {
            console.error('JS temizleme hatası:', error);
            // Hata durumunda orijinal içeriği döndür
            return js;
        }
    }
    
    /**
     * İki nesneyi derin birleştirir
     * @param {Object} target - Hedef nesne
     * @param {Object} source - Kaynak nesne
     * @returns {Object} - Birleştirilmiş nesne
     */
    function deepMerge(target, source) {
        // Derin kopyalama için yardımcı fonksiyon
        const isObject = obj => obj && typeof obj === 'object' && !Array.isArray(obj);
        
        // Kaynak veya hedef nesne değilse, kaynağı döndür
        if (!isObject(target) || !isObject(source)) {
            return source;
        }
        
        // Hedefin bir kopyasını al
        const output = { ...target };
        
        // Kaynak anahtarlarını işle
        Object.keys(source).forEach(key => {
            if (isObject(source[key])) {
                // Hedefte anahtar yoksa, kaynaktan kopyala
                if (!(key in target)) {
                    output[key] = source[key];
                } else {
                    // Hedefte anahtar varsa, özyinelemeli birleştir
                    output[key] = deepMerge(target[key], source[key]);
                }
            } else {
                // Nesne değilse, kaynaktan atama yap
                output[key] = source[key];
            }
        });
        
        return output;
    }
    
    return {
        selectElement: selectElement,
        escapeHtml: escapeHtml,
        unescapeHtml: unescapeHtml,
        cleanWidgetHtml: cleanWidgetHtml,
        generateUniqueId: generateUniqueId,
        parseUrlParams: parseUrlParams,
        ajax: ajax,
        uploadFile: uploadFile,
        sanitizeHtml: sanitizeHtml,
        sanitizeCss: sanitizeCss,
        sanitizeJs: sanitizeJs,
        deepMerge: deepMerge
    };
})();