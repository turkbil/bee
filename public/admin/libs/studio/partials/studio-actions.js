/**
 * Studio Editor - Eylemler Modülü
 * Kaydetme, dışa aktarma ve önizleme işlemleri
 */

window.StudioActions = (function() {
    /**
     * Tüm eylem butonlarını ayarlar
     * @param {Object} editor - GrapesJS editor örneği
     * @param {Object} config - Yapılandırma parametreleri
     */
    function setupActions(editor, config) {
        console.log("Setting up actions");
        
        // Daha önce eklenmiş olay dinleyicilerini temizle
        cleanup();
        
        // Kaydetme butonunu ayarla
        window.StudioSave.setupSaveButton(editor, config);
        
        // Önizleme butonunu ayarla
        window.StudioExport.setupPreviewButton(editor);
        
        // Dışa aktarma butonunu ayarla
        window.StudioExport.setupExportButton(editor);
        
        // Diğer buton işlevlerini ayarla
        setupVisibilityButton(editor);
        setupCommandButtons(editor);
        setupBackButton(editor, config);
    }
    
    /**
     * Önceden eklenmiş olay dinleyicilerini temizler
     */
    function cleanup() {
        const buttons = ['save-btn', 'preview-btn', 'export-btn', 'sw-visibility', 'cmd-clear', 'cmd-undo', 'cmd-redo', 'cmd-code-edit', 'cmd-css-edit', 'btn-back'];
        
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
     * Görünürlük butonunu ayarlar (bileşen sınırlarını göster/gizle)
     * @param {Object} editor - GrapesJS editor örneği
     */
    function setupVisibilityButton(editor) {
        const swVisibility = document.getElementById("sw-visibility");
        if (swVisibility) {
            console.log("Görünürlük butonu bulundu:", swVisibility);
            
            swVisibility.addEventListener("click", () => {
                console.log("Görünürlük butonuna tıklandı");
                try {
                    // State değişkenini takip etmek için
                    let currentState = swVisibility.classList.contains('active');
                    console.log("Mevcut durum (active):", currentState);
                    
                    // Canvas'ı kontrol et
                    console.log("Editor:", editor);
                    console.log("Canvas:", editor.Canvas);
                    
                    const frames = editor.Canvas.getFrames();
                    console.log("Frames:", frames);
                    
                    frames.forEach((frame, index) => {
                        console.log(`Frame ${index} işleniyor:`, frame);
                        const body = frame.view.getBody();
                        console.log(`Frame ${index} body:`, body);
                        
                        const allElements = body.querySelectorAll('*');
                        console.log(`Frame ${index} element sayısı:`, allElements.length);
                        
                        if (!currentState) {
                            console.log("Sınırları göster işlemi yapılıyor");
                            allElements.forEach(el => {
                                el.style.outline = '1px dashed rgba(170, 170, 170, 0.7)';
                            });
                        } else {
                            console.log("Sınırları gizle işlemi yapılıyor");
                            allElements.forEach(el => {
                                el.style.outline = '';
                            });
                        }
                    });
                    
                    // Buton durumunu güncelle
                    swVisibility.classList.toggle('active');
                    console.log("Buton durumu güncellendi:", swVisibility.classList.contains('active'));
                    
                    return swVisibility.classList.contains('active');
                } catch (error) {
                    console.error('Bileşen sınırlarını gösterme/gizleme hatası:', error);
                    return false;
                }
            });
        } else {
            console.error("Görünürlük butonu (#sw-visibility) bulunamadı!");
        }
    }
    
    /**
     * Geri dönüş butonunu ayarlar
     * @param {Object} editor - GrapesJS editor örneği
     * @param {Object} config - Yapılandırma parametreleri
     */
    function setupBackButton(editor, config) {
        const backBtn = document.getElementById("btn-back");
        if (backBtn) {
            // Eski event listener'ları temizle
            const newBackBtn = backBtn.cloneNode(true);
            if (backBtn.parentNode) {
                backBtn.parentNode.replaceChild(newBackBtn, backBtn);
            }
            
            newBackBtn.addEventListener("click", function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Module ve ID bilgilerini config'den al
                const module = config.module || 'page';
                const id = config.moduleId || 0;
                
                // Sayfa yönetim URL'sine yönlendir
                window.location.href = `/admin/${module}/manage/${id}`;
            });
        } else {
            console.error("Geri butonu (#btn-back) bulunamadı!");
        }
    }
    
    /**
     * Komut düğmelerini ayarlar (Temizle, Geri Al, İleri Al)
     * @param {Object} editor - GrapesJS editor örneği
     */
    function setupCommandButtons(editor) {
        // İçerik temizle butonu
        const cmdClear = document.getElementById("cmd-clear");
        if (cmdClear) {
            cmdClear.addEventListener("click", () => {
                if (confirm("İçeriği temizlemek istediğinize emin misiniz? Bu işlem geri alınamaz.")) {
                    editor.DomComponents.clear();
                    editor.CssComposer.clear();
                }
            });
        }

        // Geri Al butonu
        const cmdUndo = document.getElementById("cmd-undo");
        if (cmdUndo) {
            cmdUndo.addEventListener("click", () => {
                editor.UndoManager.undo();
            });
        }

        // Yinele butonu
        const cmdRedo = document.getElementById("cmd-redo");
        if (cmdRedo) {
            cmdRedo.addEventListener("click", () => {
                editor.UndoManager.redo();
            });
        }
                
        // HTML kodu düzenleme
        const cmdCodeEdit = document.getElementById("cmd-code-edit");
        if (cmdCodeEdit) {
            cmdCodeEdit.addEventListener("click", () => {
                const htmlContent = editor.getHtml();
                window.StudioModal.showEditModal("HTML Düzenle", htmlContent, (newHtml) => {
                    editor.setComponents(newHtml);
                });
            });
        }

        // CSS kodu düzenleme
        const cmdCssEdit = document.getElementById("cmd-css-edit");
        if (cmdCssEdit) {
            cmdCssEdit.addEventListener("click", () => {
                // avoidProtected parametresi ile CSS içeriğini al
                const cssContent = editor.getCss({ avoidProtected: true });
                
                window.StudioModal.showEditModal("CSS Düzenle", cssContent, (newCss) => {
                    // CSS'i tamamen temizlemeden setle
                    editor.setStyle(newCss, { avoidProtected: true });
                });
            });
        }
    }
    
    return {
        setupActions: setupActions,
        cleanup: cleanup,
        setupVisibilityButton: setupVisibilityButton,
        setupCommandButtons: setupCommandButtons,
        setupBackButton: setupBackButton
    };
})();