/**
 * Studio Editor için GrapesJS yapılandırması
 */
window.initStudioEditor = function (config) {
    // GrapesJS Editor yapılandırması
    const editor = grapesjs.init({
        container: "#" + config.elementId,
        fromElement: false,
        height: "100%",
        width: "auto",
        storageManager: false,
        panels: { defaults: [] },
        blockManager: {
            appendTo: "#blocks-container",
            blocks: [
                {
                    id: "section",
                    label: "Bölüm",
                    category: "Temel",
                    content:
                        '<section class="section"><div class="container"><h2>Bölüm Başlığı</h2><p>İçeriğiniz burada yer alacak.</p></div></section>',
                    attributes: { class: "fa fa-puzzle-piece" },
                },
                {
                    id: "text",
                    label: "Metin",
                    category: "Temel",
                    content:
                        '<div data-gjs-type="text">Metin içeriği buraya gelecek.</div>',
                    attributes: { class: "fa fa-text-width" },
                },
                {
                    id: "heading",
                    label: "Başlık",
                    category: "Temel",
                    content: "<h2>Başlık</h2>",
                    attributes: { class: "fa fa-heading" },
                },
                {
                    id: "image",
                    label: "Görsel",
                    category: "Temel",
                    select: true,
                    content: { type: "image" },
                    attributes: { class: "fa fa-image" },
                },
                {
                    id: "video",
                    label: "Video",
                    category: "Temel",
                    select: true,
                    content: { type: "video" },
                    attributes: { class: "fa fa-film" },
                },
                {
                    id: "link",
                    label: "Bağlantı",
                    category: "Temel",
                    content: { type: "link", content: "Bağlantı" },
                    attributes: { class: "fa fa-link" },
                },
                {
                    id: "button",
                    label: "Düğme",
                    category: "Temel",
                    content: '<button class="btn btn-primary">Tıkla</button>',
                    attributes: { class: "fa fa-square" },
                },
                {
                    id: "container",
                    label: "Konteyner",
                    category: "Düzen",
                    content: '<div class="container"></div>',
                    attributes: { class: "fa fa-square-o" },
                },
                {
                    id: "row",
                    label: "Satır",
                    category: "Düzen",
                    content: '<div class="row"></div>',
                    attributes: { class: "fa fa-ellipsis-h" },
                },
                {
                    id: "column",
                    label: "Sütun",
                    category: "Düzen",
                    content: '<div class="col"></div>',
                    attributes: { class: "fa fa-ellipsis-v" },
                },
                {
                    id: "column-3",
                    label: "3 Sütun",
                    category: "Düzen",
                    content: `<div class="row">
                                <div class="col-md-4">Sütun 1</div>
                                <div class="col-md-4">Sütun 2</div>
                                <div class="col-md-4">Sütun 3</div>
                              </div>`,
                    attributes: { class: "fa fa-columns" },
                },
                {
                    id: "form",
                    label: "Form",
                    category: "Form",
                    content: `<form>
                                <div class="mb-3">
                                  <label class="form-label">Ad Soyad</label>
                                  <input type="text" class="form-control" placeholder="Adınız Soyadınız">
                                </div>
                                <div class="mb-3">
                                  <label class="form-label">E-posta</label>
                                  <input type="email" class="form-control" placeholder="E-posta adresiniz">
                                </div>
                                <div class="mb-3">
                                  <label class="form-label">Mesaj</label>
                                  <textarea class="form-control" rows="3"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Gönder</button>
                              </form>`,
                    attributes: { class: "fa fa-wpforms" },
                },
            ],
        },
        styleManager: {
            appendTo: "#styles-container",
        },
        layerManager: {
            appendTo: "#layers-container",
        },
        traitManager: {
            appendTo: "#traits-container",
        },
        deviceManager: {
            devices: [
                {
                    name: "Desktop",
                    width: "",
                },
                {
                    name: "Tablet",
                    width: "768px",
                    widthMedia: "992px",
                },
                {
                    name: "Mobile",
                    width: "320px",
                    widthMedia: "480px",
                },
            ],
        },
        canvas: {
            scripts: [
                "https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js",
            ],
            styles: [
                "https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css",
                "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css",
            ],
        },
        plugins: [
            "grapesjs-blocks-basic",
            "grapesjs-preset-webpage",
            "grapesjs-style-bg",
        ],
        pluginsOpts: {
            "grapesjs-preset-webpage": {
                textCleanCanvas:
                    "İçeriği temizlemek istediğinize emin misiniz?",
                showStylesOnChange: true,
                importPlaceholder: "HTML/CSS kodu yapıştırın",
                modalImportTitle: "Kod İçe Aktar",
                modalImportLabel: "HTML veya CSS kodunu yapıştırın",
                modalImportContent: "",
            },
        },
    });

    // Önceden oluşturulmuş içeriği yükle
    if (config.content) {
        editor.setComponents(config.content);
    }

    if (config.css) {
        editor.setStyle(config.css);
    }

    // Arama fonksiyonu
    const searchInput = document.getElementById("blocks-search");
    if (searchInput) {
        searchInput.addEventListener("input", function () {
            const query = this.value.toLowerCase();
            const blocks = document.querySelectorAll(".gjs-block");

            blocks.forEach((block) => {
                const label = block.querySelector(".gjs-block-label");
                if (label) {
                    const text = label.textContent.toLowerCase();
                    if (text.includes(query)) {
                        block.style.display = "";
                    } else {
                        block.style.display = "none";
                    }
                }
            });
        });
    }

    // Tab değiştirme fonksiyonu
    const tabs = document.querySelectorAll(".panel-tab");
    const tabContents = document.querySelectorAll(".panel-tab-content");

    tabs.forEach((tab) => {
        tab.addEventListener("click", function () {
            const tabName = this.getAttribute("data-tab");

            // Aktif tab değiştir
            tabs.forEach((t) => t.classList.remove("active"));
            this.classList.add("active");

            // İçeriği değiştir
            tabContents.forEach((content) => {
                if (content.getAttribute("data-tab-content") === tabName) {
                    content.classList.add("active");
                } else {
                    content.classList.remove("active");
                }
            });
        });
    });

    // Toolbar işlevselliği
    document.getElementById("sw-visibility").addEventListener("click", () => {
        editor.runCommand("sw-visibility");
    });

    document.getElementById("cmd-clear").addEventListener("click", () => {
        if (
            confirm(
                "İçeriği temizlemek istediğinize emin misiniz? Bu işlem geri alınamaz."
            )
        ) {
            editor.runCommand("core:canvas-clear");
        }
    });

    document.getElementById("cmd-undo").addEventListener("click", () => {
        editor.runCommand("core:undo");
    });

    document.getElementById("cmd-redo").addEventListener("click", () => {
        editor.runCommand("core:redo");
    });

    // Kod düzenleme modalları
    document.getElementById("cmd-code-edit").addEventListener("click", () => {
        const htmlContent = editor.getHtml();
        showEditModal("HTML Düzenle", htmlContent, (newHtml) => {
            editor.setComponents(newHtml);
        });
    });

    document.getElementById("cmd-css-edit").addEventListener("click", () => {
        const cssContent = editor.getCss();
        document.getElementById("css-content").value = cssContent;
        showEditModal("CSS Düzenle", cssContent, (newCss) => {
            document.getElementById("css-content").value = newCss;
            editor.setStyle(newCss);
        });
    });

    document.getElementById("cmd-js-edit").addEventListener("click", () => {
        const jsContent = document.getElementById("js-content").value;
        showEditModal("JavaScript Düzenle", jsContent, (newJs) => {
            document.getElementById("js-content").value = newJs;
        });
    });

    // Modal oluştur
    function showEditModal(title, content, callback) {
        const modal = document.createElement("div");
        modal.className = "modal fade";
        modal.id = "codeEditModal";
        modal.setAttribute("tabindex", "-1");
        modal.innerHTML = `
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">${title}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                    </div>
                    <div class="modal-body">
                        <textarea id="code-editor" class="form-control font-monospace" rows="20">${content}</textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                        <button type="button" class="btn btn-primary" id="saveCodeBtn">Uygula</button>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(modal);

        const modalInstance = new bootstrap.Modal(modal);
        modalInstance.show();

        document
            .getElementById("saveCodeBtn")
            .addEventListener("click", function () {
                const newCode = document.getElementById("code-editor").value;
                callback(newCode);
                modalInstance.hide();

                modal.addEventListener("hidden.bs.modal", function () {
                    modal.remove();
                });
            });

        modal.addEventListener("hidden.bs.modal", function () {
            modal.remove();
        });
    }

    // Cihaz görünümü
    document
        .getElementById("device-desktop")
        .addEventListener("click", function () {
            editor.setDevice("Desktop");
            toggleDeviceButtons(this);
        });

    document
        .getElementById("device-tablet")
        .addEventListener("click", function () {
            editor.setDevice("Tablet");
            toggleDeviceButtons(this);
        });

    document
        .getElementById("device-mobile")
        .addEventListener("click", function () {
            editor.setDevice("Mobile");
            toggleDeviceButtons(this);
        });

    function toggleDeviceButtons(activeBtn) {
        document
            .querySelectorAll(".device-btns .toolbar-btn")
            .forEach((btn) => {
                btn.classList.remove("active");
            });
        activeBtn.classList.add("active");
    }

    // Kaydetme
    document.getElementById("save-btn").addEventListener("click", function () {
        const htmlContent = editor.getHtml();
        const cssContent = editor.getCss();
        const jsContent = document.getElementById("js-content").value;

        // Kaydetme URL'si
        const saveUrl = `/admin/studio/save/${config.moduleType}/${config.moduleId}`;

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
                if (data.success) {
                    showNotification(
                        "Başarılı",
                        "İçerik başarıyla kaydedildi.",
                        "success"
                    );
                } else {
                    showNotification("Hata", data.message, "error");
                }
            })
            .catch((error) => {
                console.error("Kaydetme hatası:", error);
                showNotification(
                    "Hata",
                    "İçerik kaydedilirken bir hata oluştu.",
                    "error"
                );
            });
    });

    // Bildirim gösterme
    function showNotification(title, message, type = "success") {
        const notif = document.createElement("div");
        notif.className = `toast align-items-center text-white bg-${
            type === "success" ? "success" : "danger"
        } border-0`;
        notif.setAttribute("role", "alert");
        notif.setAttribute("aria-live", "assertive");
        notif.setAttribute("aria-atomic", "true");

        notif.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <strong>${title}</strong>: ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Kapat"></button>
            </div>
        `;

        // Toast container
        let container = document.querySelector(".toast-container");
        if (!container) {
            container = document.createElement("div");
            container.className =
                "toast-container position-fixed top-0 end-0 p-3";
            document.body.appendChild(container);
        }

        container.appendChild(notif);

        const toast = new bootstrap.Toast(notif, {
            autohide: true,
            delay: 3000,
        });
        toast.show();

        // Belli bir süre sonra kaldır
        setTimeout(() => {
            notif.remove();
        }, 3300);
    }

    // Preview butonu için olay dinleyici
    document
        .getElementById("preview-btn")
        .addEventListener("click", function () {
            editor.runCommand("preview");
        });

    // Export butonu için olay dinleyici
    document
        .getElementById("export-btn")
        .addEventListener("click", function () {
            const html = editor.getHtml();
            const css = editor.getCss();

            const exportContent = `
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dışa Aktarılan Sayfa</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
<style>
${css}
</style>
</head>
<body>
${html}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
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
                    showNotification(
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
        });

    // Nav bar'daki sw-visibility butonu için olay dinleyici
    document
        .getElementById("sw-visibility")
        .addEventListener("click", function () {
            editor.runCommand("sw-visibility");
        });

    console.log("Studio Editor başarıyla yüklendi!");
};
