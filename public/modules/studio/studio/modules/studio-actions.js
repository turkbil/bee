/**
 * Studio Editor - Eylemler Modülü
 * Kullanıcı eylemleri: kaydetme, dışa aktarma, önizleme
 */
window.StudioActions = (function() {
    let saveInProgress = false;
    
    /**
     * Eylem butonlarını ayarla
     * @param {Object} editor - GrapesJS editör örneği
     * @param {Object} config - Yapılandırma parametreleri
     */
    function setupActions(editor, config) {
        if (!editor) {
            console.error('Eylemler modülü başlatılırken editor örneği bulunamadı!');
            return;
        }
        
        setupSaveButton(editor, config);
        setupPreviewButton(editor);
        setupExportButton(editor);
        setupCodeEditors(editor);
        setupCanvasCleanup(editor);
        setupUndoRedo(editor);
        
        console.log('Eylem butonları başarıyla ayarlandı.');
    }
    
    /**
     * Kaydet butonunu ayarla
     * @param {Object} editor - GrapesJS editör örneği
     * @param {Object} config - Yapılandırma parametreleri
     */
    function setupSaveButton(editor, config) {
        const saveBtn = document.getElementById('save-btn');
        if (!saveBtn) return;
        
        saveBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Kaydetme işlemi devam ediyorsa engelle
            if (saveInProgress) {
                console.log('Kaydetme işlemi devam ediyor, lütfen bekleyin...');
                return;
            }
            
            saveInProgress = true;
            
            // Buton durumunu güncelle
            const originalText = this.innerHTML;
            this.disabled = true;
            this.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Kaydediliyor...';
            
            try {
                // Modül ve ID bilgilerini al
                const moduleType = config.moduleType || document.getElementById('gjs').getAttribute('data-module-type');
                const moduleId = config.moduleId || document.getElementById('gjs').getAttribute('data-module-id');
                
                if (!moduleType || !moduleId) {
                    throw new Error('Modül tipi veya ID bilgisi bulunamadı.');
                }
                
                // İçeriği hazırla
                const content = window.StudioEditor.prepareContentForSave();
                
                // Kaydetme URL'si
                const saveUrl = `/admin/studio/save/${moduleType}/${moduleId}`;
                
                // İçeriği kaydet
                window.StudioEditor.saveContent(saveUrl, {
                    content: content.html,
                    css: content.css,
                    js: content.js
                })
                .then(data => {
                    // Başarılı bildirim göster
                    window.StudioUI.showNotification('Başarılı', data.message || 'İçerik başarıyla kaydedildi!');
                })
                .catch(error => {
                    // Hata bildirimi göster
                    window.StudioUI.showNotification('Hata', error.message || 'İçerik kaydedilirken bir hata oluştu.', 'error');
                })
                .finally(() => {
                    // Buton durumunu sıfırla
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = originalText;
                    
                    // Kaydetme işlemini tamamla
                    setTimeout(() => {
                        saveInProgress = false;
                    }, 500);
                });
                
            } catch (error) {
                console.error('Kaydetme hatası:', error);
                
                // Buton durumunu sıfırla
                saveBtn.disabled = false;
                saveBtn.innerHTML = originalText;
                saveInProgress = false;
                
                // Hata bildirimi göster
                window.StudioUI.showNotification('Hata', error.message || 'İçerik kaydedilirken bir hata oluştu.', 'error');
            }
        });
        
        console.log('Kaydetme butonu etkinleştirildi.');
    }
    
    /**
     * Önizleme butonunu ayarla
     * @param {Object} editor - GrapesJS editör örneği
     */
    function setupPreviewButton(editor) {
        const previewBtn = document.getElementById('preview-btn');
        if (!previewBtn) return;
        
        previewBtn.addEventListener('click', function() {
            // İçeriği hazırla
            const content = window.StudioEditor.prepareContentForSave();
            
            // Önizleme penceresi oluştur
            const previewWindow = window.open('', '_blank');
            
            // HTML içeriğini oluştur
            const previewContent = `
            <!DOCTYPE html>
            <html lang="tr">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Sayfa Önizleme</title>
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
                <style>
                    ${content.css}
                </style>
            </head>
            <body>
                ${content.html}
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
                <script>
                    ${content.js}
                </script>
            </body>
            </html>`;
            
            // İçeriği yaz ve pencereyi kapat
            previewWindow.document.open();
            previewWindow.document.write(previewContent);
            previewWindow.document.close();
        });
        
        console.log('Önizleme butonu etkinleştirildi.');
    }
    
    /**
     * Dışa aktar butonunu ayarla
     * @param {Object} editor - GrapesJS editör örneği
     */
    function setupExportButton(editor) {
        const exportBtn = document.getElementById('export-btn');
        if (!exportBtn) return;
        
        exportBtn.addEventListener('click', function() {
            // İçeriği hazırla
            const content = window.StudioEditor.prepareContentForSave();
            
            // Dışa aktarma içeriği oluştur
            const exportContent = `
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dışa Aktarılan Sayfa</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
${content.css}
    </style>
</head>
<body>
${content.html}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
${content.js}
    </script>
</body>
</html>`;
            
            // Modal oluştur
            const modalEl = document.createElement('div');
            modalEl.className = 'modal fade';
            modalEl.id = 'exportModal';
            modalEl.setAttribute('tabindex', '-1');
            modalEl.setAttribute('aria-hidden', 'true');
            
            // Modal içeriği
            modalEl.innerHTML = `
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">HTML Dışa Aktar</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                        </div>
                        <div class="modal-body">
                            <textarea id="export-content" class="form-control font-monospace" rows="20">${exportContent}</textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                            <button type="button" class="btn btn-primary" id="copyExportBtn">Kopyala</button>
                            <button type="button" class="btn btn-success" id="downloadExportBtn">İndir</button>
                        </div>
                    </div>
                </div>
            `;
            
            // Modalı ekle
            document.body.appendChild(modalEl);
            
            // Bootstrap Modal sınıfı varsa kullan
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
                
                // Kopyala butonu
                document.getElementById('copyExportBtn').addEventListener('click', function() {
                    const exportContent = document.getElementById('export-content');
                    exportContent.select();
                    document.execCommand('copy');
                    window.StudioUI.showNotification('Başarılı', 'İçerik panoya kopyalandı.', 'success');
                });
                
                // İndir butonu
                document.getElementById('downloadExportBtn').addEventListener('click', function() {
                    const blob = new Blob([exportContent], { type: 'text/html' });
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'sayfa_export.html';
                    a.click();
                    URL.revokeObjectURL(url);
                });
                
                // Modal kapandığında temizle
                modalEl.addEventListener('hidden.bs.modal', function() {
                    document.body.removeChild(modalEl);
                });
            } else {
                // Alternatif gösterme yöntemi
                modalEl.style.display = 'block';
                
                // Kopyala butonu
                document.getElementById('copyExportBtn').addEventListener('click', function() {
                    const exportContent = document.getElementById('export-content');
                    exportContent.select();
                    document.execCommand('copy');
                    alert('İçerik panoya kopyalandı.');
                });
                
                // İndir butonu
                document.getElementById('downloadExportBtn').addEventListener('click', function() {
                    const blob = new Blob([exportContent], { type: 'text/html' });
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'sayfa_export.html';
                    a.click();
                    URL.revokeObjectURL(url);
                });
                
                // Kapat butonları
                const closeButtons = modalEl.querySelectorAll('.btn-close, .btn-secondary');
                closeButtons.forEach(btn => {
                    btn.addEventListener('click', function() {
                        document.body.removeChild(modalEl);
                    });
                });
            }
        });
        
        console.log('Dışa aktarma butonu etkinleştirildi.');
    }
    
    /**
     * Kod düzenleme butonlarını ayarla
     * @param {Object} editor - GrapesJS editör örneği
     */
    function setupCodeEditors(editor) {
        // HTML kodu düzenleme butonu
        const cmdCodeEdit = document.getElementById('cmd-code-edit');
        if (cmdCodeEdit) {
            cmdCodeEdit.addEventListener('click', function() {
                const htmlContent = editor.getHtml();
                
                // Modal oluştur
                const modalEl = document.createElement('div');
                modalEl.className = 'modal fade';
                modalEl.id = 'codeEditModal';
                modalEl.setAttribute('tabindex', '-1');
                modalEl.setAttribute('aria-hidden', 'true');
                
                // Modal içeriği
                modalEl.innerHTML = `
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">HTML Düzenle</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                            </div>
                            <div class="modal-body">
                                <textarea id="html-editor" class="form-control font-monospace" rows="20">${htmlContent}</textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                                <button type="button" class="btn btn-primary" id="saveHtmlBtn">Uygula</button>
                            </div>
                        </div>
                    </div>
                `;
                
                // Modalı ekle
                document.body.appendChild(modalEl);
                
                // Bootstrap Modal sınıfı varsa kullan
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const modal = new bootstrap.Modal(modalEl);
                    modal.show();
                    
                    // Uygula butonu
                    document.getElementById('saveHtmlBtn').addEventListener('click', function() {
                        const newHtml = document.getElementById('html-editor').value;
                        editor.setComponents(newHtml);
                        modal.hide();
                    });
                    
                    // Modal kapandığında temizle
                    modalEl.addEventListener('hidden.bs.modal', function() {
                        document.body.removeChild(modalEl);
                    });
                } else {
                    // Alternatif gösterme yöntemi
                    modalEl.style.display = 'block';
                    
                    // Uygula butonu
                    document.getElementById('saveHtmlBtn').addEventListener('click', function() {
                        const newHtml = document.getElementById('html-editor').value;
                        editor.setComponents(newHtml);
                        document.body.removeChild(modalEl);
                    });
                    
                    // Kapat butonları
                    const closeButtons = modalEl.querySelectorAll('.btn-close, .btn-secondary');
                    closeButtons.forEach(btn => {
                        btn.addEventListener('click', function() {
                            document.body.removeChild(modalEl);
                        });
                    });
                }
            });
        }
        
        // CSS kodu düzenleme butonu
        const cmdCssEdit = document.getElementById('cmd-css-edit');
        if (cmdCssEdit) {
            cmdCssEdit.addEventListener('click', function() {
                const cssContent = editor.getCss();
                
                // Modal oluştur
                const modalEl = document.createElement('div');
                modalEl.className = 'modal fade';
                modalEl.id = 'cssEditModal';
                modalEl.setAttribute('tabindex', '-1');
                modalEl.setAttribute('aria-hidden', 'true');
                
                // Modal içeriği
                modalEl.innerHTML = `
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">CSS Düzenle</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                            </div>
                            <div class="modal-body">
                                <textarea id="css-editor" class="form-control font-monospace" rows="20">${cssContent}</textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                                <button type="button" class="btn btn-primary" id="saveCssBtn">Uygula</button>
                            </div>
                        </div>
                    </div>
                `;
                
                // Modalı ekle
                document.body.appendChild(modalEl);
                
                // Bootstrap Modal sınıfı varsa kullan
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const modal = new bootstrap.Modal(modalEl);
                    modal.show();
                    
                    // Uygula butonu
                    document.getElementById('saveCssBtn').addEventListener('click', function() {
                        const newCss = document.getElementById('css-editor').value;
                        
                        // Gizli alanda da güncelle
                        const cssContentEl = document.getElementById('css-content');
                        if (cssContentEl) {
                            cssContentEl.value = newCss;
                        }
                        
                        editor.setStyle(newCss);
                        modal.hide();
                    });
                    
                    // Modal kapandığında temizle
                    modalEl.addEventListener('hidden.bs.modal', function() {
                        document.body.removeChild(modalEl);
                    });
                } else {
                    // Alternatif gösterme yöntemi
                    modalEl.style.display = 'block';
                    
                    // Uygula butonu
                    document.getElementById('saveCssBtn').addEventListener('click', function() {
                        const newCss = document.getElementById('css-editor').value;
                        
                        // Gizli alanda da güncelle
                        const cssContentEl = document.getElementById('css-content');
                        if (cssContentEl) {
                            cssContentEl.value = newCss;
                        }
                        
                        editor.setStyle(newCss);
                        document.body.removeChild(modalEl);
                    });
                    
                    // Kapat butonları
                    const closeButtons = modalEl.querySelectorAll('.btn-close, .btn-secondary');
                    closeButtons.forEach(btn => {
                        btn.addEventListener('click', function() {
                            document.body.removeChild(modalEl);
                        });
                    });
                }
            });
        }
        
        // JS kodu düzenleme butonu
        const cmdJsEdit = document.getElementById('cmd-js-edit');
        if (cmdJsEdit) {
            cmdJsEdit.addEventListener('click', function() {
                const jsContentEl = document.getElementById('js-content');
                const jsContent = jsContentEl ? jsContentEl.value : '';
                
                // Modal oluştur
                const modalEl = document.createElement('div');
                modalEl.className = 'modal fade';
                modalEl.id = 'jsEditModal';
                modalEl.setAttribute('tabindex', '-1');
                modalEl.setAttribute('aria-hidden', 'true');
                
                // Modal içeriği
                modalEl.innerHTML = `
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">JavaScript Düzenle</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                            </div>
                            <div class="modal-body">
                                <textarea id="js-editor" class="form-control font-monospace" rows="20">${jsContent}</textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                                <button type="button" class="btn btn-primary" id="saveJsBtn">Uygula</button>
                            </div>
                        </div>
                    </div>
                `;
                
                // Modalı ekle
                document.body.appendChild(modalEl);
                
                // Bootstrap Modal sınıfı varsa kullan
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const modal = new bootstrap.Modal(modalEl);
                    modal.show();
                    
                    // Uygula butonu
                    document.getElementById('saveJsBtn').addEventListener('click', function() {
                        const newJs = document.getElementById('js-editor').value;
                        
                        // Gizli alanda güncelle
                        if (jsContentEl) {
                            jsContentEl.value = newJs;
                        }
                        
                        modal.hide();
                    });
                    
                    // Modal kapandığında temizle
                    modalEl.addEventListener('hidden.bs.modal', function() {
                        document.body.removeChild(modalEl);
                    });
                } else {
                    // Alternatif gösterme yöntemi
                    modalEl.style.display = 'block';
                    
                    // Uygula butonu
                    document.getElementById('saveJsBtn').addEventListener('click', function() {
                        const newJs = document.getElementById('js-editor').value;
                        
                        // Gizli alanda güncelle
                        if (jsContentEl) {
                            jsContentEl.value = newJs;
                        }
                        
                        document.body.removeChild(modalEl);
                    });
                    
                    // Kapat butonları
                    const closeButtons = modalEl.querySelectorAll('.btn-close, .btn-secondary');
                    closeButtons.forEach(btn => {
                        btn.addEventListener('click', function() {
                            document.body.removeChild(modalEl);
                        });
                    });
                }
            });
        }
        
        console.log('Kod düzenleyici butonları etkinleştirildi.');
    }
    
    /**
     * Canvas temizleme butonunu ayarla
     * @param {Object} editor - GrapesJS editör örneği
     */
    function setupCanvasCleanup(editor) {
        const cmdClear = document.getElementById('cmd-clear');
        if (!cmdClear) return;
        
        cmdClear.addEventListener('click', function() {
            // Onay diyaloğu göster
            window.StudioUI.showConfirmDialog(
                'İçeriği Temizle',
                'İçeriği temizlemek istediğinize emin misiniz? Bu işlem geri alınamaz.',
                function(confirmed) {
                    if (confirmed) {
                        editor.DomComponents.clear();
                        editor.CssComposer.clear();
                        
                        // Olayı tetikle
                        window.StudioEvents.trigger('action:canvas:cleared');
                        
                        // Bildirim göster
                        window.StudioUI.showNotification('Başarılı', 'İçerik temizlendi.');
                    }
                }
            );
        });
        
        console.log('Canvas temizleme butonu etkinleştirildi.');
    }
    
    /**
     * Geri Al / Yinele butonlarını ayarla
     * @param {Object} editor - GrapesJS editör örneği
     */
    function setupUndoRedo(editor) {
        const cmdUndo = document.getElementById('cmd-undo');
        const cmdRedo = document.getElementById('cmd-redo');
        
        if (cmdUndo) {
            cmdUndo.addEventListener('click', function() {
                editor.UndoManager.undo();
                
                // Olayı tetikle
                window.StudioEvents.trigger('action:undo');
            });
        }
        
        if (cmdRedo) {
            cmdRedo.addEventListener('click', function() {
                editor.UndoManager.redo();
                
                // Olayı tetikle
                window.StudioEvents.trigger('action:redo');
            });
        }
        
        console.log('Geri Al / Yinele butonları etkinleştirildi.');
    }
    
    return {
        setupActions: setupActions
    };
})();