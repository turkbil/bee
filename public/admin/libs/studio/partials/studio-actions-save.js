/**
 * Studio Editor - Kaydetme Modülü
 * İçerik kaydetme ve veri iletimi
 */

window.StudioSave = (function() {
    // İsteğin çalışıp çalışmadığını izleyen bir flag
    let isSaveInProgress = false;
    
    /**
     * Kaydet butonunu yapılandırır
     * @param {Object} editor - GrapesJS editor örneği
     * @param {Object} config - Yapılandırma parametreleri
     */
    function setupSaveButton(editor, config) {
        const saveBtn = document.getElementById("save-btn");
        if (!saveBtn) {
            console.error("Save button (#save-btn) not found.");
            return;
        }
        
        // Kaydetme işlemini yapacak fonksiyon
        saveBtn.addEventListener("click", function(e) {
            e.preventDefault();
            
            // Zaten bir istek çalışıyorsa engelle
            if (isSaveInProgress) {
                console.log("Save operation already in progress, ignoring this click");
                return;
            }
            
            // İşlem başladı flag'ini ayarla
            isSaveInProgress = true;
            console.log("Save button clicked");
            
            // Butonu geçici olarak devre dışı bırak
            this.disabled = true;
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Kaydediliyor...';
            
            try {
                let htmlContent, cssContent, jsContent;
                
                // HTML içeriğini al ve body etiketlerini temizle
                htmlContent = editor.getHtml() || '';
                htmlContent = htmlContent.replace(/^<body[^>]*>|<\/body>$/g, '');
                
                // CSS içeriğini al
                cssContent = editor.getCss() || '';
                
                // JS içeriğini al
                const jsContentEl = document.getElementById("js-content");
                jsContent = jsContentEl ? jsContentEl.value || '' : '';

                console.log("Save content preparation:", {
                    htmlContentLength: htmlContent.length,
                    cssContentLength: cssContent.length,
                    jsContentLength: jsContent.length
                });

                // moduleId'nin sayı olduğundan emin ol
                const moduleId = parseInt(config.moduleId);
                
                // Kaydetme URL'si
                const saveUrl = `/admin/studio/save/${config.module}/${moduleId}`;

                // CSRF token al
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                
                // AJAX isteği
                fetch(saveUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        content: htmlContent,
                        css: cssContent,
                        js: jsContent
                    })
                })
                .then(response => {
                    console.log("Sunucu yanıt durumu:", response.status);
                    return response.json();
                })
                .then(data => {
                    console.log("Sunucu yanıtı:", data);
                    if (data.success) {
                        console.log("Kayıt başarılı:", data.message);
                        window.StudioNotification.success(data.message || 'İçerik başarıyla kaydedildi!');
                    } else {
                        console.error("Kayıt başarısız:", data.message);
                        window.StudioNotification.error(data.message || 'Kayıt işlemi başarısız.');
                    }
                })
                .catch(error => {
                    console.error('Kaydetme hatası:', error);
                    window.StudioNotification.error(error.message || 'Sunucuya bağlanırken bir hata oluştu.');
                })
                .finally(() => {
                    // Butonu normal haline getir
                    this.disabled = false;
                    this.innerHTML = originalText;
                    
                    // İşlem bittikten sonra kilidi kaldır
                    setTimeout(() => {
                        isSaveInProgress = false;
                        console.log("Save operation lock released");
                    }, 1000); // 1 saniye beklet, hızlı çift tıklamaları önlemek için
                });
            } catch (error) {
                console.error("Save operation error:", error);
                this.disabled = false;
                this.innerHTML = originalText;
                isSaveInProgress = false; // Hata durumunda kilidi kaldır
                window.StudioNotification.error('İçerik kaydedilirken bir sorun oluştu: ' + error.message);
            }
        });
    }
    
    return {
        setupSaveButton: setupSaveButton
    };
})();