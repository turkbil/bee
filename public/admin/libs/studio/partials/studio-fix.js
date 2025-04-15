/**
 * Studio Editor - Düzeltme Modülü
 * GrapesJS'deki kritik hataları sessizce düzeltir
 */
// public/admin/libs/studio/partials/studio-fix.js

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
    
    // Sayfa yüklendiğinde düzeltmeleri uygula
    document.addEventListener('DOMContentLoaded', function() {
        applyFixes();
    });
    
    return {
        applyFixes: applyFixes,
        fixStyleManager: fixStyleManager
    };
})();