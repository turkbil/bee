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
        setupSaveButton(editor, config);
        setupPreviewButton(editor);
        setupExportButton(editor);
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
        const handleSaveClick = function () {
            console.log("Save button clicked."); // Tıklama logu eklendi
            const htmlContent = editor.getHtml();
            const cssContent = editor.getCss();
            const jsContentEl = document.getElementById("js-content");
            const jsContent = jsContentEl ? jsContentEl.value : "";

            // moduleId'nin sayı olduğundan emin ol
            const moduleId = parseInt(config.moduleId);
            
            // Kaydetme URL'si
            const saveUrl = `/admin/studio/save/${config.moduleType}/${moduleId}`;

            // Debug için konsola yazdır
            console.log("Kaydediliyor:", {
                url: saveUrl,
                moduleType: config.moduleType,
                moduleId: moduleId,
                contentLength: htmlContent.length,
                cssLength: cssContent.length,
                // jsLength: jsContent.length // Eğer JS kaydediyorsanız
            });

            // AJAX isteği
            StudioUtils.sendRequest(
                saveUrl,
                {
                    html_content: htmlContent,
                    css_content: cssContent,
                    js_content: jsContent
                },
                function(data) {
                    StudioUtils.showNotification('Başarılı', data.message || 'İçerik başarıyla kaydedildi!');
                },
                function(error) {
                    console.error('Kaydetme hatası:', error);
                    StudioUtils.showNotification('Hata', error.message || 'Sunucuya bağlanırken bir hata oluştu.', 'error');
                }
            );
        };
        
        // Önce mevcut listener'ı kaldır (varsa)
        // Not: Eğer handleSaveClick fonksiyonu her çağrıda yeniden tanımlanıyorsa,
        // bu removeEventListener işe yaramaz. Fonksiyon referansının aynı olması gerekir.
        // Ancak bu yapı genellikle iş görür.
        saveBtn.removeEventListener("click", handleSaveClick); 
        // Sonra yeni listener'ı ekle
        saveBtn.addEventListener("click", handleSaveClick);

        console.log("Save button listener setup/reset."); // Kurulum logu
    }
    
    /**
     * Önizleme butonunu yapılandırır
     * @param {Object} editor - GrapesJS editor örneği
     */
    function setupPreviewButton(editor) {
        const previewBtn = document.getElementById("preview-btn");
        if (previewBtn) {
            previewBtn.addEventListener("click", function () {
                // Özel önizleme mantığı
                const html = editor.getHtml();
                const css = editor.getCss();
                const jsContentEl = document.getElementById("js-content");
                const js = jsContentEl ? jsContentEl.value : '';
                
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
            exportBtn.addEventListener("click", function () {
                const html = editor.getHtml();
                const css = editor.getCss();
                const jsContentEl = document.getElementById("js-content");
                const js = jsContentEl ? jsContentEl.value : '';

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

                // Dışa aktarma modalı oluştur
                const modal = document.createElement("div");
                modal.className = "modal fade";
                modal.id = "exportModal";
                modal.setAttribute("tabindex", "-1");
                modal.innerHTML = `
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

                document.body.appendChild(modal);

                if (typeof bootstrap !== "undefined" && bootstrap.Modal) {
                    const modalInstance = new bootstrap.Modal(modal);
                    modalInstance.show();

                    // Kopyala butonu işlevi
                    document
                        .getElementById("copyExportBtn")
                        .addEventListener("click", function () {
                            const exportContent =
                                document.getElementById("export-content");
                            exportContent.select();
                            document.execCommand("copy");
                            StudioUtils.showNotification(
                                "Başarılı",
                                "İçerik panoya kopyalandı.",
                                "success"
                            );
                        });

                    // İndir butonu işlevi
                    document
                        .getElementById("downloadExportBtn")
                        .addEventListener("click", function () {
                            const blob = new Blob([exportContent], {
                                type: "text/html",
                            });
                            const url = URL.createObjectURL(blob);
                            const a = document.createElement("a");
                            a.href = url;
                            a.download = "sayfa_export.html";
                            a.click();
                            URL.revokeObjectURL(url);
                        });

                    modal.addEventListener("hidden.bs.modal", function () {
                        modal.remove();
                    });
                } else {
                    // Fallback - basit modal gösterimi
                    modal.style.display = "block";

                    // Kopyala butonu
                    const copyBtn = modal.querySelector("#copyExportBtn");
                    if (copyBtn) {
                        copyBtn.addEventListener("click", function () {
                            const exportContent =
                                modal.querySelector("#export-content");
                            if (exportContent) {
                                exportContent.select();
                                document.execCommand("copy");
                                alert("İçerik panoya kopyalandı.");
                            }
                        });
                    }

                    // İndir butonu
                    const downloadBtn = modal.querySelector("#downloadExportBtn");
                    if (downloadBtn) {
                        downloadBtn.addEventListener("click", function () {
                            const blob = new Blob([exportContent], {
                                type: "text/html",
                            });
                            const url = URL.createObjectURL(blob);
                            const a = document.createElement("a");
                            a.href = url;
                            a.download = "sayfa_export.html";
                            a.click();
                            URL.revokeObjectURL(url);
                        });
                    }

                    // Kapat butonları
                    const closeButtons = modal.querySelectorAll(
                        ".btn-close, .btn-secondary"
                    );
                    closeButtons.forEach((btn) => {
                        btn.addEventListener("click", function () {
                            document.body.removeChild(modal);
                        });
                    });
                }
            });
        }
    }
    
    return {
        setupActions: setupActions
    };
})();