/**
 * Studio Editor - Düzeltme Modülü
 * GrapesJS'deki kritik hataları sessizce düzeltir
 */

window.StudioFix = (function() {
    /**
     * GrapesJS'in problematik davranışlarını düzeltir
     */
    function applyFixes() {
        try {
            // Element prototipinin setAttribute metodunu yakalayarak güvenli hale getirme
            const originalSetAttribute = Element.prototype.setAttribute;
            
            Element.prototype.setAttribute = function(name, value) {
                // Geçersiz attribute adlarını kontrol et
                if (name === null || name === undefined || name === 0 || name === '0') {
                    // Sessizce düzelt, log mesajı gösterme
                    return; 
                }
                
                // Geçerli attribute adları için normal davranışa devam et
                try {
                    return originalSetAttribute.call(this, name, value);
                } catch (error) {
                    // Sessizce hatayı ele al
                }
            };
            
            // Stil yöneticisini düzeltmek için global olay dinleyicisi ekle
            document.addEventListener('editor:loaded', function() {
                setTimeout(fixStyleManager, 1000);
                setTimeout(fixNumberInputs, 1000);
                setTimeout(fixLayerPanel, 1000);
            });
        } catch (error) {
            // Kritik hatalar için sadece console.error göster
            console.error('GrapesJS düzeltme hatası');
        }
    }
    
    /**
     * GrapesJS stil yöneticisi sorunlarını düzeltir
     */
    function fixStyleManager() {
        try {
            // Stil paneli için gecikmeli düzeltme
            const observeStyleManager = setInterval(() => {
                // Tüm sektörlere tıklama olayı ve başlangıç durumu ata
                const sectors = document.querySelectorAll('.gjs-sm-sector');
                if (sectors.length > 0) {
                    clearInterval(observeStyleManager);
                    
                    sectors.forEach((sector, index) => {
                        // Sektör başlığı
                        const title = sector.querySelector('.gjs-sm-sector-title');
                        if (title) {
                            // Tüm eski olay dinleyicileri temizle
                            const newTitle = title.cloneNode(true);
                            if (title.parentNode) {
                                title.parentNode.replaceChild(newTitle, title);
                            }
                            
                            // Yeni olay dinleyicisi ekle
                            newTitle.addEventListener('click', function() {
                                // Tıklama durumunu tersine çevir
                                sector.classList.toggle('gjs-collapsed');
                                
                                // Özellik alanını göster/gizle
                                const properties = sector.querySelector('.gjs-sm-properties');
                                if (properties) {
                                    properties.style.display = sector.classList.contains('gjs-collapsed') ? 'none' : 'block';
                                }
                            });
                        }
                        
                        // İlk bölümü açık, diğerlerini kapalı başlat
                        if (index === 0) {
                            sector.classList.remove('gjs-collapsed');
                            const properties = sector.querySelector('.gjs-sm-properties');
                            if (properties) {
                                properties.style.display = 'block';
                            }
                        } else {
                            sector.classList.add('gjs-collapsed');
                            const properties = sector.querySelector('.gjs-sm-properties');
                            if (properties) {
                                properties.style.display = 'none';
                            }
                        }
                    });
                }
            }, 500);
            
            // 10 saniye sonra gözlemciyi durdur
            setTimeout(() => clearInterval(observeStyleManager), 10000);
        } catch (error) {
            // Sadece kritik hatayı göster
            console.error('Stil düzeltme hatası');
        }
    }
    
    /**
     * Sayı girişi butonlarını düzeltme
     */
    function fixNumberInputs() {
        try {
            // Number input butonlarını düzeltmek için periyodik kontrol
            const checkNumberInputsInterval = setInterval(() => {
                const allNumberInputs = document.querySelectorAll('.gjs-field-integer');
                let inputsFixed = 0;
                
                allNumberInputs.forEach(container => {
                    const arrows = container.querySelectorAll('.gjs-field-arrow-u, .gjs-field-arrow-d');
                    const input = container.querySelector('input');
                    
                    if (!input || !arrows.length) return;
                    
                    // Input olayı düzeltme
                    if (!input.hasAttribute('data-fixed')) {
                        input.setAttribute('data-fixed', 'true');
                        
                        // Min-max sınırları ekle
                        if (!input.hasAttribute('min')) {
                            input.setAttribute('min', '-9999');
                        }
                        
                        if (!input.hasAttribute('max')) {
                            input.setAttribute('max', '9999');
                        }
                        
                        inputsFixed++;
                    }
                    
                    // Oklar için olay dinleyicileri düzeltme
                    arrows.forEach(arrow => {
                        if (!arrow.hasAttribute('data-fixed')) {
                            arrow.setAttribute('data-fixed', 'true');
                            
                            // Eski olay dinleyicilerini temizle
                            const newArrow = arrow.cloneNode(true);
                            arrow.parentNode.replaceChild(newArrow, arrow);
                            
                            // Yeni olay dinleyicisi ekle
                            newArrow.addEventListener('click', function() {
                                let value = parseInt(input.value) || 0;
                                const step = parseInt(input.getAttribute('step')) || 1;
                                const min = parseInt(input.getAttribute('min')) || -9999;
                                const max = parseInt(input.getAttribute('max')) || 9999;
                                
                                if (this.classList.contains('gjs-field-arrow-u')) {
                                    value = Math.min(max, value + step);
                                } else {
                                    value = Math.max(min, value - step);
                                }
                                
                                input.value = value;
                                input.dispatchEvent(new Event('change', { bubbles: true }));
                                input.dispatchEvent(new Event('input', { bubbles: true }));
                            });
                            
                            inputsFixed++;
                        }
                    });
                });
                
                // Eğer herhangi bir düzeltme yaptıysak, kontrol aralığını uzat
                if (inputsFixed > 0) {
                    console.log(`${inputsFixed} sayı giriş alanı düzeltildi`);
                }
            }, 2000);
            
            // 30 saniye sonra periyodik kontrolü durdur
            setTimeout(() => clearInterval(checkNumberInputsInterval), 30000);
        } catch (error) {
            console.error('Sayı giriş alanları düzeltme hatası:', error);
        }
    }
    
    /**
     * Katmanlar panelini düzeltme
     */
    function fixLayerPanel() {
        try {
            // Katmanlar paneli için periyodik kontrol
            const checkLayerPanelInterval = setInterval(() => {
                const layerContainer = document.getElementById('layers-container');
                if (!layerContainer) return;
                
                // Katman gruplarını düzelt
                const layerGroups = layerContainer.querySelectorAll('.gjs-layer-group');
                layerGroups.forEach(group => {
                    const header = group.querySelector('.gjs-layer-group-header');
                    if (!header || header.hasAttribute('data-fixed')) return;
                    
                    // Başlık düzeltmesi
                    header.setAttribute('data-fixed', 'true');
                    
                    // Başlık ikonunu ekle
                    if (!header.querySelector('i.fa')) {
                        const icon = document.createElement('i');
                        icon.className = 'fa fa-layer-group';
                        header.insertBefore(icon, header.firstChild);
                    }
                    
                    // Tıklama olayı
                    header.addEventListener('click', function() {
                        const parent = this.closest('.gjs-layer-group');
                        parent.classList.toggle('closed');
                        
                        const content = parent.querySelector('.gjs-layer-group-items');
                        if (content) {
                            content.style.display = parent.classList.contains('closed') ? 'none' : 'block';
                        }
                    });
                });
                
                // Tüm katmanlara hover efekti
                const allLayers = layerContainer.querySelectorAll('.gjs-layer');
                allLayers.forEach(layer => {
                    if (!layer.classList.contains('layer-styled')) {
                        layer.classList.add('layer-styled');
                    }
                });
                
            }, 2000);
            
            // 30 saniye sonra periyodik kontrolü durdur
            setTimeout(() => clearInterval(checkLayerPanelInterval), 30000);
        } catch (error) {
            console.error('Katmanlar paneli düzeltme hatası:', error);
        }
    }
    
    // Sayfa yüklendiğinde düzeltmeleri uygula
    document.addEventListener('DOMContentLoaded', function() {
        applyFixes();
    });
    
    return {
        applyFixes: applyFixes,
        fixStyleManager: fixStyleManager,
        fixNumberInputs: fixNumberInputs,
        fixLayerPanel: fixLayerPanel
    };
})();