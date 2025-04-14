/**
 * Studio CSS Parser
 * CSS içeriğini işleyen ve düzenleyen modül
 */
const StudioCssParser = (function() {
    /**
     * CSS içeriğini parse et
     * @param {string} css CSS içeriği
     * @param {Object} options Seçenekler
     * @returns {string} İşlenmiş CSS
     */
    function parseCss(css, options = {}) {
        // Varsayılan seçenekler
        options = {
            optimize: false,     // CSS'i optimize et
            minify: false,       // CSS'i sıkıştır
            prefix: false,       // Vendor öneklerini ekle
            ...options
        };
        
        if (!css) {
            return '';
        }
        
        // İçeriği işle
        let processedCss = css;
        
        // CSS'i optimize et
        if (options.optimize) {
            processedCss = optimizeCss(processedCss);
        }
        
        // CSS'i sıkıştır
        if (options.minify) {
            processedCss = minifyCss(processedCss);
        }
        
        // Vendor öneklerini ekle
        if (options.prefix) {
            processedCss = addVendorPrefixes(processedCss);
        }
        
        return processedCss;
    }
    
    /**
     * CSS içeriğini optimize et
     * @param {string} css CSS içeriği
     * @returns {string} Optimize edilmiş CSS
     */
    function optimizeCss(css) {
        if (!css) {
            return '';
        }
        
        let processedCss = css;
        
        // CSS yorumlarını kaldır
        processedCss = processedCss.replace(/\/\*[\s\S]*?\*\//g, '');
        
        // Tekrarlayan seçicileri birleştir
        const cssRules = {};
        
        // CSS kurallarını ayır
        const ruleRegex = /([^{]+)({[^}]*})/g;
        let match;
        
        while ((match = ruleRegex.exec(processedCss)) !== null) {
            const selector = match[1].trim();
            const rules = match[2].trim();
            
            if (!cssRules[selector]) {
                cssRules[selector] = [];
            }
            
            // Kural içeriğini ayır
            const rulesContent = rules.substring(1, rules.length - 1).trim();
            const rulesList = rulesContent.split(';').filter(rule => rule.trim() !== '');
            
            // Her kuralı ekle
            rulesList.forEach(rule => {
                const trimmedRule = rule.trim();
                if (trimmedRule && !cssRules[selector].includes(trimmedRule)) {
                    cssRules[selector].push(trimmedRule);
                }
            });
        }
        
        // Optimize edilmiş CSS'i oluştur
        let optimizedCss = '';
        Object.keys(cssRules).forEach(selector => {
            const rules = cssRules[selector];
            if (rules.length > 0) {
                optimizedCss += `${selector} {\n`;
                rules.forEach(rule => {
                    optimizedCss += `  ${rule};\n`;
                });
                optimizedCss += '}\n\n';
            }
        });
        
        return optimizedCss;
    }
    
    /**
     * CSS içeriğini sıkıştır
     * @param {string} css CSS içeriği
     * @returns {string} Sıkıştırılmış CSS
     */
    function minifyCss(css) {
        if (!css) {
            return '';
        }
        
        // CSS yorumlarını kaldır
        let minifiedCss = css.replace(/\/\*[\s\S]*?\*\//g, '');
        
        // Gereksiz boşlukları kaldır
        minifiedCss = minifiedCss.replace(/\s+/g, ' ');
        
        // Seçici sonrasındaki boşlukları kaldır
        minifiedCss = minifiedCss.replace(/\s*{\s*/g, '{');
        
        // Kural sonrasındaki boşlukları kaldır
        minifiedCss = minifiedCss.replace(/\s*}\s*/g, '}');
        
        // Özellik-değer arasındaki boşlukları kaldır
        minifiedCss = minifiedCss.replace(/\s*:\s*/g, ':');
        
        // Kural ayırıcı sonrasındaki boşlukları kaldır
        minifiedCss = minifiedCss.replace(/\s*;\s*/g, ';');
        
        // Son noktalı virgülü kaldır
        minifiedCss = minifiedCss.replace(/;\}/g, '}');
        
        // Virgül sonrasındaki boşlukları kaldır
        minifiedCss = minifiedCss.replace(/\s*,\s*/g, ',');
        
        return minifiedCss;
    }
    
    /**
     * CSS'e vendor öneklerini ekle
     * @param {string} css CSS içeriği
     * @returns {string} Vendor önekleri eklenmiş CSS
     */
    function addVendorPrefixes(css) {
        if (!css) {
            return '';
        }
        
        // Prefixlenecek özelliklerin listesi
        const prefixProperties = [
            'animation', 'animation-delay', 'animation-direction', 'animation-duration',
            'animation-fill-mode', 'animation-iteration-count', 'animation-name',
            'animation-play-state', 'animation-timing-function', 'appearance',
            'backface-visibility', 'box-shadow', 'box-sizing', 'filter',
            'flex', 'flex-basis', 'flex-direction', 'flex-flow', 'flex-grow',
            'flex-shrink', 'flex-wrap', 'transform', 'transform-origin',
            'transition', 'transition-delay', 'transition-duration',
            'transition-property', 'transition-timing-function', 'user-select'
        ];
        
        // Basit bir prefixleme algoritması
        let prefixedCss = css;
        
        prefixProperties.forEach(property => {
            const regex = new RegExp(`(^|[^-])${property}\\s*:`, 'g');
            prefixedCss = prefixedCss.replace(regex, (match, p1) => {
                return `${p1}-webkit-${property}: ${p1}-moz-${property}: ${p1}-ms-${property}: ${p1}${property}:`;
            });
        });
        
        return prefixedCss;
    }
    
    // Dışa aktarılan fonksiyonlar
    return {
        parseCss: parseCss,
        optimizeCss: optimizeCss,
        minifyCss: minifyCss,
        addVendorPrefixes: addVendorPrefixes
    };
})();

// Global olarak kullanılabilir yap
window.StudioCssParser = StudioCssParser;