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
            console.log("Önizleme butonu mevcut değil, atlanıyor");
            return;
        }
        
        // Önizleme işlemini yapacak fonksiyon 
        previewBtn.addEventListener("click", async function(e) {
            e.preventDefault();
            
            this.disabled = true;
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> <span>Yükleniyor...</span>';
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
                
                // Module widget'larını bul
                let moduleIds = [];
                if (window.StudioHtmlParser && typeof window.StudioHtmlParser.findModuleWidgets === 'function') {
                    moduleIds = window.StudioHtmlParser.findModuleWidgets(contentHtml);
                } else {
                    // StudioHtmlParser.findModuleWidgets yoksa manuel olarak module widget'larını bul
                    const moduleRegex = /\[\[module:(\d+)\]\]/g;
                    let moduleMatch;
                    while ((moduleMatch = moduleRegex.exec(contentHtml)) !== null) {
                        moduleIds.push(moduleMatch[1]);
                    }
                    
                    // data-widget-module-id attribute'ları da kontrol et
                    const moduleElementRegex = /<div[^>]*data-widget-module-id="(\d+)"[^>]*>/g;
                    while ((moduleMatch = moduleElementRegex.exec(contentHtml)) !== null) {
                        moduleIds.push(moduleMatch[1]);
                    }
                }
                
                // Tüm ID'leri birleştir ve tekrar eden ID'leri temizle
                const allIds = [...widgetIds, ...moduleIds];
                const uniqueIds = [...new Set(allIds)];
                console.log('Preview uniqueIds:', uniqueIds);
                
                // Progress takibi
                const totalItems = uniqueIds.length;
                let loadedItems = 0;
                if (totalItems === 0) {
                    const bar = document.getElementById('preview-progress-bar');
                    if (bar) { bar.style.width = '100%'; bar.textContent = '100%'; }
                }
                
                const parser = new DOMParser();
                const doc = parser.parseFromString(contentHtml, 'text/html');
                
                // Widget'ları yükle
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
                    loadedItems++;
                    const percent = totalItems ? Math.round((loadedItems / totalItems) * 100) : 100;
                    const bar = document.getElementById('preview-progress-bar');
                    if (bar) { bar.style.width = percent + '%'; bar.textContent = percent + '%'; }
                }
                
                // Module widget'ları yükle
                for (const moduleId of moduleIds) {
                    try {
                        // Module shortcode'ları önce HTML elementlerine dönüştür
                        const moduleRegex = new RegExp(`\\[\\[module:${moduleId}\\]\\]`, 'g');
                        contentHtml = contentHtml.replace(moduleRegex, `<div class="module-widget-container" data-widget-module-id="${moduleId}"></div>`);
                        
                        // Module API'den veri al
                        const res = await fetch(`/admin/widgetmanagement/api/module/${moduleId}`, { credentials: 'same-origin' });
                        console.log(`Fetch JSON status for module ${moduleId}:`, res.status);
                        if (res.ok) {
                            const data = await res.json();
                            let moduleHtml = data.html || data.content_html || '';
                            
                            if (data.useHandlebars && window.Handlebars) {
                                const template = Handlebars.compile(moduleHtml);
                                moduleHtml = template(data.context || {});
                            }
                            
                            const tempDiv = document.createElement('div');
                            tempDiv.innerHTML = moduleHtml;
                            
                            // Script tag'lerini temizle
                            tempDiv.querySelectorAll('script').forEach(s => s.remove());
                            
                            // Tüm module widget elementlerini değiştir
                            doc.querySelectorAll(`[data-widget-module-id="${moduleId}"]`).forEach(el => {
                                const clone = tempDiv.cloneNode(true);
                                const nodes = Array.from(clone.childNodes);
                                if (nodes.length) {
                                    el.replaceWith(...nodes);
                                } else {
                                    el.replaceWith(clone);
                                }
                                console.log(`Replaced module for module ${moduleId}`);
                            });
                            
                            // Module shortcode'larını da değiştir
                            const shortcodeElements = doc.body.innerHTML.match(moduleRegex);
                            if (shortcodeElements) {
                                doc.body.innerHTML = doc.body.innerHTML.replace(moduleRegex, moduleHtml);
                                console.log(`Replaced shortcode for module ${moduleId}`);
                            }
                        }
                    } catch (err) {
                        console.error('Module preview embed yüklenirken hata:', err);
                    }
                    
                    // Progress güncelle
                    loadedItems++;
                    const percent = totalItems ? Math.round((loadedItems / totalItems) * 100) : 100;
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