/**
 * Studio Editor - Dışa Aktarma Modülü
 * İçeriği dışa aktarma ve önizleme
 */

window.StudioExport = (function() {
    /**
     * Önizleme butonunu yapılandırır
     * @param {Object} editor - GrapesJS editor örneği
     */
    function setupPreviewButton(editor) {
        const previewBtn = document.getElementById("preview-btn");
        if (!previewBtn) {
            // Hata mesajını kaldırdım, buton bulunamazsa sessizce geç
            console.log("Önizleme butonu mevcut değil, atlanıyor");
            return;
        }
        
        // Önizleme işlemini yapacak fonksiyon 
        previewBtn.addEventListener("click", function(e) {
            e.preventDefault();
            
            // Butonu geçici olarak devre dışı bırak
            this.disabled = true;
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Yükleniyor...';
            
            try {
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
                    window.StudioNotification.warning('Önizleme penceresi açılamadı. Lütfen popup engelleyicinizi kontrol edin.');
                    return;
                }
                
                // HTML içeriğini oluştur
                const previewContent = window.StudioConfig.getFullHtmlTemplate(html, css, js);
                
                // İçeriği yaz ve pencereyi kapat
                previewWindow.document.open();
                previewWindow.document.write(previewContent);
                previewWindow.document.close();
            } catch (error) {
                console.error("Preview operation error:", error);
                window.StudioNotification.error('Önizleme oluşturulurken bir sorun oluştu: ' + error.message);
            } finally {
                // Butonu normal haline getir
                this.disabled = false;
                this.innerHTML = originalText;
            }
        });
    }
    
    /**
     * Dışa aktar butonunu yapılandırır
     * @param {Object} editor - GrapesJS editor örneği
     */
    function setupExportButton(editor) {
        // Bu özellik kullanılmayacak, fonksiyonu boş bırakıyorum
        // Sessizce geç
        return;
    }
    
    return {
        setupPreviewButton: setupPreviewButton,
        setupExportButton: setupExportButton
    };
})();