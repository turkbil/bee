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
        previewBtn.addEventListener("click", async function(e) {
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
                
                // Preview için widget embed içeriklerini yükle
                let contentHtml = html;
                if (window.StudioHtmlParser && typeof window.StudioHtmlParser.convertAllWidgetReferencesToEmbeds === 'function') {
                    contentHtml = window.StudioHtmlParser.convertAllWidgetReferencesToEmbeds(contentHtml);
                }
                // Widget embed öğelerini DOMParser ile değiştir
                let widgetIds = [];
                if (window.StudioHtmlParser && typeof window.StudioHtmlParser.findWidgetEmbeds === 'function') {
                    widgetIds = window.StudioHtmlParser.findWidgetEmbeds(contentHtml);
                }
                console.log('Preview widgetIds:', widgetIds);
                const parser = new DOMParser();
                const doc = parser.parseFromString(contentHtml, 'text/html');
                for (const widgetId of widgetIds) {
                    try {
                        const res = await fetch(`/admin/widgetmanagement/preview/embed/${widgetId}`, { credentials: 'same-origin' });
                        console.log(`Fetch status for widget ${widgetId}:`, res.status);
                        if (res.ok) {
                            const widgetHtml = await res.text();
                            console.log(`Widget HTML for preview ${widgetId}:`, widgetHtml);
                            const tempDiv = document.createElement('div');
                            tempDiv.innerHTML = widgetHtml;
                            doc.querySelectorAll(`.widget-embed[data-tenant-widget-id="${widgetId}"]`).forEach(el => {
                                el.replaceWith(tempDiv.cloneNode(true));
                                console.log(`Replaced embed for widget ${widgetId}`);
                            });
                        }
                    } catch (err) {
                        console.error('Widget preview embed yüklenirken hata:', err);
                    }
                }
                console.log('Final parsed contentHtml:', doc.body.innerHTML);
                contentHtml = doc.body.innerHTML;

                // Önizleme penceresi oluştur
                const previewWindow = window.open('', '_blank');
                
                if (!previewWindow) {
                    console.error("Preview window could not be opened!");
                    window.StudioNotification.warning('Önizleme penceresi açılamadı. Lütfen popup engelleyicinizi kontrol edin.');
                    return;
                }
                
                // HTML içeriğini oluştur
                const previewContent = window.StudioConfig.getFullHtmlTemplate(contentHtml, css, js);
                
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