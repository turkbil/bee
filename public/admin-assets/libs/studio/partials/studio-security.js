/**
 * Studio Editor - Security Utilities
 * XSS ve Code Injection koruması
 */

window.StudioSecurity = (function() {
    
    /**
     * HTML Sanitization - XSS koruması
     * @param {string} html - Temizlenecek HTML
     * @param {Object} options - Sanitization seçenekleri
     * @returns {string} Temizlenmiş HTML
     */
    function sanitizeHtml(html, options = {}) {
        if (!html || typeof html !== 'string') {
            return '';
        }
        
        const defaults = {
            allowedTags: [
                'div', 'span', 'p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
                'a', 'img', 'ul', 'ol', 'li', 'strong', 'em', 'br',
                'section', 'article', 'header', 'footer', 'nav', 'main',
                'button', 'input', 'textarea', 'form', 'label'
            ],
            allowedAttributes: {
                'a': ['href', 'title', 'target'],
                'img': ['src', 'alt', 'width', 'height', 'class'],
                'div': ['class', 'id', 'data-*'],
                'span': ['class', 'id', 'data-*'],
                'p': ['class', 'id'],
                'button': ['class', 'id', 'type', 'data-*'],
                'input': ['type', 'name', 'value', 'placeholder', 'class', 'id'],
                '*': ['class', 'id', 'data-widget-id', 'data-tenant-widget-id', 'data-widget-module-id']
            },
            forbiddenPatterns: [
                /javascript:/i,
                /vbscript:/i,
                /on\w+\s*=/i,
                /<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi,
                /<iframe\b[^<]*(?:(?!<\/iframe>)<[^<]*)*<\/iframe>/gi,
                /<object\b[^<]*(?:(?!<\/object>)<[^<]*)*<\/object>/gi,
                /<embed\b[^<]*(?:(?!<\/embed>)<[^<]*)*<\/embed>/gi
            ]
        };
        
        const config = { ...defaults, ...options };
        
        // Tehlikeli pattern'leri temizle
        let sanitized = html;
        config.forbiddenPatterns.forEach(pattern => {
            sanitized = sanitized.replace(pattern, '');
        });
        
        // DOM parser kullanarak temizle
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = sanitized;
        
        const cleanElement = (element) => {
            // Tag kontrolü
            if (!config.allowedTags.includes(element.tagName.toLowerCase())) {
                return false;
            }
            
            // Attribute kontrolü
            const allowedAttrs = config.allowedAttributes[element.tagName.toLowerCase()] || 
                                config.allowedAttributes['*'] || [];
            
            Array.from(element.attributes).forEach(attr => {
                const attrName = attr.name;
                const isAllowed = allowedAttrs.some(allowed => {
                    if (allowed === attrName) return true;
                    if (allowed.endsWith('*') && attrName.startsWith(allowed.slice(0, -1))) return true;
                    return false;
                });
                
                if (!isAllowed) {
                    element.removeAttribute(attrName);
                }
            });
            
            return true;
        };
        
        // Recursive temizleme
        const walkElements = (element) => {
            const children = Array.from(element.children);
            children.forEach(child => {
                if (!cleanElement(child)) {
                    child.remove();
                } else {
                    walkElements(child);
                }
            });
        };
        
        walkElements(tempDiv);
        
        return tempDiv.innerHTML;
    }
    
    /**
     * JavaScript Code Validation
     * @param {string} code - Kontrol edilecek kod
     * @returns {boolean} Güvenli mi?
     */
    function validateJavaScript(code) {
        if (!code || typeof code !== 'string') {
            return false;
        }
        
        const forbiddenPatterns = [
            /eval\s*\(/i,
            /Function\s*\(/i,
            /setTimeout\s*\(/i,
            /setInterval\s*\(/i,
            /document\.write/i,
            /document\.writeln/i,
            /innerHTML\s*=/i,
            /outerHTML\s*=/i,
            /insertAdjacentHTML/i,
            /createElement\s*\(\s*['"]script['"]]/i,
            /window\./i,
            /parent\./i,
            /top\./i,
            /self\./i,
            /frames\./i,
            /location\./i,
            /document\.cookie/i,
            /localStorage/i,
            /sessionStorage/i,
            /XMLHttpRequest/i,
            /fetch\s*\(/i,
            /import\s*\(/i,
            /require\s*\(/i
        ];
        
        return !forbiddenPatterns.some(pattern => pattern.test(code));
    }
    
    /**
     * Widget Content Sanitization
     * @param {Object} widgetData - Widget verileri
     * @returns {Object} Temizlenmiş widget verileri
     */
    function sanitizeWidgetData(widgetData) {
        if (!widgetData || typeof widgetData !== 'object') {
            return {};
        }
        
        const sanitized = { ...widgetData };
        
        // HTML içeriğini temizle
        if (sanitized.html) {
            sanitized.html = sanitizeHtml(sanitized.html);
        }
        
        if (sanitized.content) {
            sanitized.content = sanitizeHtml(sanitized.content);
        }
        
        // JavaScript kodunu kontrol et
        if (sanitized.js) {
            if (!validateJavaScript(sanitized.js)) {
                console.warn('Güvenlik: JavaScript kodu engellendi');
                sanitized.js = '';
            }
        }
        
        // CSS'i temizle (basit)
        if (sanitized.css) {
            sanitized.css = sanitized.css.replace(/javascript:/gi, '');
            sanitized.css = sanitized.css.replace(/expression\s*\(/gi, '');
            sanitized.css = sanitized.css.replace(/@import/gi, '');
        }
        
        return sanitized;
    }
    
    /**
     * Safe innerHTML Replacement
     * @param {Element} element - Hedef element
     * @param {string} html - HTML içeriği
     */
    function safeInnerHTML(element, html) {
        if (!element || !html) {
            return;
        }
        
        const sanitized = sanitizeHtml(html);
        element.innerHTML = sanitized;
    }
    
    /**
     * Content Security Policy Validator
     * @param {string} content - Kontrol edilecek içerik
     * @returns {boolean} CSP'ye uygun mu?
     */
    function validateCSP(content) {
        const cspViolations = [
            /'unsafe-inline'/i,
            /'unsafe-eval'/i,
            /data:/i,
            /javascript:/i,
            /vbscript:/i
        ];
        
        return !cspViolations.some(violation => violation.test(content));
    }
    
    /**
     * Error Logging - Güvenlik olaylarını logla
     * @param {string} type - Olay tipi
     * @param {string} message - Mesaj
     * @param {Object} data - Ek veriler
     */
    function logSecurityEvent(type, message, data = {}) {
        const event = {
            timestamp: new Date().toISOString(),
            type: type,
            message: message,
            data: data,
            userAgent: navigator.userAgent,
            url: window.location.href
        };
        
        console.warn('[Studio Security]', event);
        
        // Güvenlik olayını backend'e gönder
        if (window.fetch) {
            fetch('/admin/studio/security/log', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify(event)
            }).catch(err => {
                console.error('Güvenlik log hatası:', err);
            });
        }
    }
    
    // Public API
    return {
        sanitizeHtml: sanitizeHtml,
        validateJavaScript: validateJavaScript,
        sanitizeWidgetData: sanitizeWidgetData,
        safeInnerHTML: safeInnerHTML,
        validateCSP: validateCSP,
        logSecurityEvent: logSecurityEvent
    };
})();