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
            console.error("Preview button (#preview-btn) not found.");
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
        const exportBtn = document.getElementById("export-btn");
        if (!exportBtn) {
            console.error("Export button (#export-btn) not found.");
            return;
        }
        
        // Dışa aktarma işlemini yapacak fonksiyon
        exportBtn.addEventListener("click", function(e) {
            e.preventDefault();
            
            // Butonu geçici olarak devre dışı bırak
            this.disabled = true;
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Hazırlanıyor...';
            
            try {
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

                const exportContent = window.StudioConfig.getFullHtmlTemplate(html, css, js);

                // Dışa aktarma modalını göster
                window.StudioModal.showEditModal("HTML Dışa Aktar", exportContent, function(newContent) {
                    // HTML olarak indirme seçeneği
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
                        
                        window.StudioNotification.success('Sayfa başarıyla dışa aktarıldı!');
                    } catch (error) {
                        console.error('Dışa aktarma hatası:', error);
                        window.StudioNotification.error('Dışa aktarma sırasında bir hata oluştu: ' + error.message);
                    }
                });
            } catch (error) {
                console.error("Export operation error:", error);
                window.StudioNotification.error('Dışa aktarma sırasında bir sorun oluştu: ' + error.message);
            } finally {
                // Butonu normal haline getir
                this.disabled = false;
                this.innerHTML = originalText;
            }
        });
    }
    
    return {
        setupPreviewButton: setupPreviewButton,
        setupExportButton: setupExportButton
    };
})();