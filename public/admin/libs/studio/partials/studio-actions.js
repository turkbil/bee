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
        if (saveBtn) {
            saveBtn.addEventListener("click", function () {
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
                    contentLength: htmlContent ? htmlContent.length : 0,
                    cssLength: cssContent ? cssContent.length : 0
                });

                // Kaydederken butonu devre dışı bırak
                saveBtn.disabled = true;
                saveBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Kaydediliyor...';

                fetch(saveUrl, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": config.csrfToken,
                    },
                    body: JSON.stringify({
                        content: htmlContent,
                        css: cssContent,
                        js: jsContent,
                    }),
                })
                .then((response) => response.json())
                .then((data) => {
                    // Butonu aktifleştir
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = '<i class="fa-solid fa-save me-1"></i> Kaydet';
                    
                    if (data.success) {
                        StudioUtils.showNotification(
                            "Başarılı",
                            "İçerik başarıyla kaydedildi.",
                            "success"
                        );
                    } else {
                        StudioUtils.showNotification("Hata", data.message, "error");
                    }
                })
                .catch((error) => {
                    // Butonu aktifleştir
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = '<i class="fa-solid fa-save me-1"></i> Kaydet';
                    
                    console.error("Kaydetme hatası:", error);
                    StudioUtils.showNotification(
                        "Hata",
                        "İçerik kaydedilirken bir hata oluştu.",
                        "error"
                    );
                });
            });
        }
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