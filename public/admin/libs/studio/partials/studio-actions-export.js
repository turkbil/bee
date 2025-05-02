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
            // Spinner ve metin
            this.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> <span>Yükleniyor...</span>';
            // Progress bar ekle
            const progressContainer = document.createElement('div');
            progressContainer.className = 'progress mt-2';
            progressContainer.innerHTML = '<div id="preview-progress-bar" class="progress-bar" role="progressbar" style="width:0%">0%</div>';
            this.insertAdjacentElement('afterend', progressContainer);
            
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
                
                // Önizleme için widget embed içeriklerini yükle
                let contentHtml = html;
                // Gömülü widget referanslarını placeholder divlerine dönüştür
                if (window.StudioHtmlParser && typeof window.StudioHtmlParser.convertAllWidgetReferencesToEmbeds === 'function') {
                    contentHtml = window.StudioHtmlParser.convertAllWidgetReferencesToEmbeds(contentHtml);
                }
                // Remove any leftover script tags (templates, helpers)
                contentHtml = contentHtml.replace(/<script\b[\s\S]*?<\/script>/gi, '');
                
                // Widget embed öğelerini DOMParser ile değiştir
                let widgetIds = [];
                if (window.StudioHtmlParser && typeof window.StudioHtmlParser.findWidgetEmbeds === 'function') {
                    widgetIds = window.StudioHtmlParser.findWidgetEmbeds(contentHtml);
                }
                console.log('Preview widgetIds:', widgetIds);
                // Progress takibi
                const totalWidgets = widgetIds.length;
                let loadedWidgets = 0;
                if (totalWidgets === 0) {
                    const bar = document.getElementById('preview-progress-bar');
                    if (bar) { bar.style.width = '100%'; bar.textContent = '100%'; }
                }
                const parser = new DOMParser();
                const doc = parser.parseFromString(contentHtml, 'text/html');
                for (const widgetId of widgetIds) {
                    try {
                        const res = await fetch(`/admin/widgetmanagement/preview/embed/json/${widgetId}`, { credentials: 'same-origin' });
                        console.log(`Fetch JSON status for widget ${widgetId}:`, res.status);
                        if (res.ok) {
                            const data = await res.json();
                            let widgetHtml = data.content_html;
                            if (data.useHandlebars && window.Handlebars) {
                                const template = Handlebars.compile(widgetHtml);
                                widgetHtml = template(data.context);
                            }
                            const tempDiv = document.createElement('div');
                            tempDiv.innerHTML = widgetHtml;
                            // Strip any script tags to prevent markup break
                            tempDiv.querySelectorAll('script').forEach(s => s.remove());
                            doc.querySelectorAll(`.widget-embed[data-tenant-widget-id="${widgetId}"]`).forEach(el => {
                                const clone = tempDiv.cloneNode(true);
                                const nodes = Array.from(clone.childNodes);
                                if (nodes.length) {
                                    el.replaceWith(...nodes);
                                } else {
                                    el.replaceWith(clone);
                                }
                                console.log(`Replaced embed for widget ${widgetId}`);
                            });
                        }
                    } catch (err) {
                        console.error('Widget preview embed yüklenirken hata:', err);
                    }
                    // Progress güncelle
                    loadedWidgets++;
                    const percent = totalWidgets ? Math.round((loadedWidgets / totalWidgets) * 100) : 100;
                    const bar = document.getElementById('preview-progress-bar');
                    if (bar) { bar.style.width = percent + '%'; bar.textContent = percent + '%'; }
                }
                console.log('Final parsed contentHtml:', doc.body.innerHTML);
                contentHtml = doc.body.innerHTML;
                // %100 olunca preview açmadan önce güncelle ve küçük gecikme
                {
                    const bar = document.getElementById('preview-progress-bar');
                    if (bar) { bar.style.width = '100%'; bar.textContent = '100%'; }
                }
                await new Promise(r => setTimeout(r, 200));
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
                // Progress bar kaldır
                const barContainer = this.nextElementSibling;
                if (barContainer && barContainer.classList.contains('progress')) barContainer.remove();
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