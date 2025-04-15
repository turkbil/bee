/**
 * Studio Editor Uygulama Başlatıcı
 * Tüm modülleri yükler ve uygulamayı başlatır
 */
document.addEventListener('DOMContentLoaded', function() {
    // Önce düzeltmeleri uygula
    if (window.StudioFix && typeof window.StudioFix.applyFixes === 'function') {
        window.StudioFix.applyFixes();
    }
    
    // Modül kontrolünü daha esnek hale getir
    if (!window.StudioCore) {
        console.warn('app.js: StudioCore modülü bulunamadı, ancak işleme devam ediliyor.');
    }
    
    // studio-init.js'in editörü başlatmasını bekle (zaten DOMContentLoaded içinde çalışıyor)
    // ve ardından 'load' olayını dinle.
    
    // window.studioEditor nesnesinin varlığını kontrol et (studio-init.js tarafından oluşturulur)
    if (window.studioEditor && typeof window.studioEditor.on === 'function') {
        console.log('app.js: Zaten başlatılmış studioEditor nesnesi bulundu. \"load\" olayı dinleniyor.');
        window.studioEditor.on('load', function() {
            console.log('app.js: studioEditor \"load\" olayı tetiklendi.');
            if (window.StudioFix && typeof window.StudioFix.fixStyleManager === 'function') {
                console.log('app.js: Stil yöneticisi düzeltmeleri uygulanıyor.');
                window.StudioFix.fixStyleManager();
            } else {
                console.warn('app.js: StudioFix.fixStyleManager fonksiyonu bulunamadı.');
            }
        });
    } else {
        // Eğer hemen bulunamazsa, küçük bir gecikme ile tekrar kontrol et
        // Bu durum normalde olmamalı, çünkü studio-init de DOMContentLoaded'i bekliyor.
        console.warn('app.js: studioEditor nesnesi DOMContentLoaded sonrası hemen bulunamadı. Küçük bir gecikme ile tekrar denenecek.');
        setTimeout(() => {
            if (window.studioEditor && typeof window.studioEditor.on === 'function') {
                 console.log('app.js: Gecikmeli kontrolde studioEditor nesnesi bulundu. \"load\" olayı dinleniyor.');
                 window.studioEditor.on('load', function() {
                    console.log('app.js: Gecikmeli studioEditor \"load\" olayı tetiklendi.');
                    if (window.StudioFix && typeof window.StudioFix.fixStyleManager === 'function') {
                        console.log('app.js: Stil yöneticisi düzeltmeleri (gecikmeli) uygulanıyor.');
                        window.StudioFix.fixStyleManager();
                    } else {
                         console.warn('app.js: StudioFix.fixStyleManager fonksiyonu (gecikmeli) bulunamadı.');
                    }
                });
            } else {
                 console.error('app.js: Gecikmeli kontrolde de studioEditor nesnesi bulunamadı veya \"on\" metodu yok.');
            }
        }, 500); // 500ms bekle
    }
});