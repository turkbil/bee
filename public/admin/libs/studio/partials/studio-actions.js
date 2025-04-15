/**
 * Studio Editor - Eylemler Modülü
 * Kaydetme, dışa aktarma ve önizleme işlemleri
 */
window.StudioActions = (function() {
    // İsteğin çalışıp çalışmadığını izleyen bir flag
    let isSaveInProgress = false;
    
    /**
     * Tüm eylem butonlarını ayarlar
     * @param {Object} editor - GrapesJS editor örneği
     * @param {Object} config - Yapılandırma parametreleri
     */
    function setupActions(editor, config) {
        console.log("Setting up actions");
        
        // Daha önce eklenmiş olay dinleyicilerini temizle
        cleanup();
        
        setupSaveButton(editor, config);
        setupPreviewButton(editor);
        setupExportButton(editor);
    }
    
    /**
     * Önceden eklenmiş olay dinleyicilerini temizler
     */
    function cleanup() {
        const buttons = ['save-btn', 'preview-btn', 'export-btn'];
        
        buttons.forEach(buttonId => {
            const button = document.getElementById(buttonId);
            if (button) {
                const newButton = button.cloneNode(true);
                if (button.parentNode) {
                    button.parentNode.replaceChild(newButton, button);
                }
            }
        });
    }
    
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
                
                // HTML içeriğini al
                htmlContent = editor.getHtml() || '';
                
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
                        if (window.StudioUtils) {
                            window.StudioUtils.showNotification('Başarılı', data.message || 'İçerik başarıyla kaydedildi!');
                        }
                    } else {
                        console.error("Kayıt başarısız:", data.message);
                        if (window.StudioUtils) {
                            window.StudioUtils.showNotification('Hata', data.message || 'Kayıt işlemi başarısız.', 'error');
                        }
                    }
                })
                .catch(error => {
                    console.error('Kaydetme hatası:', error);
                    if (window.StudioUtils) {
                        window.StudioUtils.showNotification('Hata', error.message || 'Sunucuya bağlanırken bir hata oluştu.', 'error');
                    }
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
                if (window.StudioUtils) {
                    window.StudioUtils.showNotification('Hata', 'İçerik kaydedilirken bir sorun oluştu: ' + error.message, 'error');
                }
            }
        });
    }
    
    /**
     * Önizleme butonunu yapılandırır
     * @param {Object} editor - GrapesJS editor örneği
     */
    function setupPreviewButton(editor) {
        const previewBtn = document.getElementById("preview-btn");
        if (previewBtn) {
            // Eski listener'ları temizle
            const newPreviewBtn = previewBtn.cloneNode(true);
            if (previewBtn.parentNode) {
                previewBtn.parentNode.replaceChild(newPreviewBtn, previewBtn);
            }
            
            newPreviewBtn.addEventListener("click", function () {
                // İçeriği al
                const html = editor.getHtml() || '';
                const css = editor.getCss() || '';
                const jsContentEl = document.getElementById("js-content");
                const js = jsContentEl ? jsContentEl.value || '' : '';
                
                console.log("Preview content:", {
                    htmlLength: html.length,
                    cssLength: css.length,
                    jsLength: js.length
                });
                
                // Önizleme penceresi oluştur
                const previewWindow = window.open('', '_blank');
                
                if (!previewWindow) {
                    console.error("Preview window could not be opened!");
                    if (window.StudioUtils && typeof window.StudioUtils.showNotification === 'function') {
                        window.StudioUtils.showNotification('Uyarı', 'Önizleme penceresi açılamadı. Lütfen popup engelleyicinizi kontrol edin.', 'warning');
                    } else {
                        alert('Önizleme penceresi açılamadı. Lütfen popup engelleyicinizi kontrol edin.');
                    }
                    return;
                }
                
                // HTML içeriğini oluştur
                const previewContent = `
                <!DOCTYPE html>
                <html lang="tr">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Sayfa Önizleme</title>
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
                
                // İçeriği yaz ve pencereyi kapat
                previewWindow.document.open();
                previewWindow.document.write(previewContent);
                previewWindow.document.close();
            });
        }
    }
    
    /**
     * Dışa aktar butonunu yapılandırır
     * @param {Object} editor - GrapesJS editor örneği
     */
    function setupExportButton(editor) {
        const exportBtn = document.getElementById("export-btn");
        if (exportBtn) {
            // Eski listener'ları temizle
            const newExportBtn = exportBtn.cloneNode(true);
            if (exportBtn.parentNode) {
                exportBtn.parentNode.replaceChild(newExportBtn, exportBtn);
            }
            
            newExportBtn.addEventListener("click", function () {
                // Daha önce oluşturulmuş bir modal varsa kaldır
                const existingModal = document.getElementById("exportModal");
                if (existingModal) {
                    existingModal.remove();
                }
                
                // İçeriği al
                const html = editor.getHtml() || '';
                const css = editor.getCss() || '';
                const jsContentEl = document.getElementById("js-content");
                const js = jsContentEl ? jsContentEl.value || '' : '';

                const exportContent = `
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dışa Aktarılan Sayfa</title>
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

                // Dışa aktarma modalını göster
                if (window.StudioUtils && typeof window.StudioUtils.showEditModal === 'function') {
                    window.StudioUtils.showEditModal("HTML Dışa Aktar", exportContent, function(newContent) {
                        // HTML olarak indirme seçeneği eklenebilir
                        try {
                            const blob = new Blob([newContent], {type: 'text/html'});
                            const url = URL.createObjectURL(blob);
                            const a = document.createElement('a');
                            a.href = url;
                            a.download = 'exported-page.html';
                            document.body.appendChild(a);
                            a.click();
                            document.body.removeChild(a);
                            URL.revokeObjectURL(url);
                            
                            if (window.StudioUtils) {
                                window.StudioUtils.showNotification('Başarılı', 'Sayfa başarıyla dışa aktarıldı!', 'success');
                            }
                        } catch (error) {
                            console.error('Dışa aktarma hatası:', error);
                            if (window.StudioUtils) {
                                window.StudioUtils.showNotification('Hata', 'Dışa aktarma sırasında bir hata oluştu: ' + error.message, 'error');
                            }
                        }
                    });
                }
            });
        }
    }
    
    return {
        setupActions: setupActions
    };
})();