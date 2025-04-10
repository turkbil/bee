/**
 * Studio Editor için GrapesJS yapılandırması
 */
window.initStudioEditor = function (config) {
    // GrapesJS Editor yapılandırması
    const editor = grapesjs.init({
        container: "#" + config.elementId,
        fromElement: false,
        height: "100%",
        width: "100%",
        storageManager: false,
        panels: { defaults: [] }, // Varsayılan panelleri temizle
        blockManager: {
            appendTo: "#blocks-container",
            blocks: [
                // ... bloklar
            ],
        },
        styleManager: {
            appendTo: "#styles-container",
            sectors: [
                {
                    name: "Dimension",
                    open: false,
                    buildProps: [
                        "width",
                        "height",
                        "max-width",
                        "min-height",
                        "margin",
                        "padding",
                    ],
                    properties: [
                        {
                            id: "flex-width",
                            type: "integer",
                            name: "Width",
                            units: ["px", "%", "vw"],
                            property: "width",
                            toRequire: 1,
                        },
                        {
                            property: "margin",
                            properties: [
                                { name: "Top", property: "margin-top" },
                                { name: "Right", property: "margin-right" },
                                { name: "Bottom", property: "margin-bottom" },
                                { name: "Left", property: "margin-left" },
                            ],
                        },
                        {
                            property: "padding",
                            properties: [
                                { name: "Top", property: "padding-top" },
                                { name: "Right", property: "padding-right" },
                                { name: "Bottom", property: "padding-bottom" },
                                { name: "Left", property: "padding-left" },
                            ],
                        },
                    ],
                },
                {
                    name: "Typography",
                    open: false,
                    buildProps: [
                        "font-family",
                        "font-size",
                        "font-weight",
                        "letter-spacing",
                        "color",
                        "line-height",
                        "text-align",
                        "text-decoration",
                        "text-shadow",
                    ],
                    properties: [
                        { name: "Font", property: "font-family" },
                        { name: "Weight", property: "font-weight" },
                        { name: "Font color", property: "color" },
                    ],
                },
                {
                    name: "Decorations",
                    open: false,
                    buildProps: [
                        "border-radius-c",
                        "background-color",
                        "border-radius",
                        "border",
                        "box-shadow",
                        "background",
                    ],
                },
                {
                    name: "Extra",
                    open: false,
                    buildProps: [
                        "opacity",
                        "transition",
                        "perspective",
                        "transform",
                    ],
                    properties: [
                        {
                            type: "slider",
                            property: "opacity",
                            defaults: 1,
                            step: 0.01,
                            max: 1,
                            min: 0,
                        },
                    ],
                },
                {
                    name: "Flex",
                    open: false,
                    properties: [
                        {
                            name: "Flex Container",
                            property: "display",
                            type: "select",
                            defaults: "block",
                            list: [
                                { value: "block", name: "Disable" },
                                { value: "flex", name: "Enable" },
                            ],
                        },
                        {
                            name: "Flex Parent",
                            property: "label-parent-flex",
                            type: "integer",
                        },
                        {
                            name: "Direction",
                            property: "flex-direction",
                            type: "select",
                            defaults: "row",
                            list: [
                                {
                                    value: "row",
                                    name: "Row",
                                    className: "icons-flex icon-dir-row",
                                },
                                {
                                    value: "row-reverse",
                                    name: "Row reverse",
                                    className: "icons-flex icon-dir-row-rev",
                                },
                                {
                                    value: "column",
                                    name: "Column",
                                    className: "icons-flex icon-dir-col",
                                },
                                {
                                    value: "column-reverse",
                                    name: "Column reverse",
                                    className: "icons-flex icon-dir-col-rev",
                                },
                            ],
                        },
                        {
                            name: "Wrap",
                            property: "flex-wrap",
                            type: "select",
                            defaults: "nowrap",
                            list: [
                                {
                                    value: "nowrap",
                                    name: "No wrap",
                                },
                                {
                                    value: "wrap",
                                    name: "Wrap",
                                },
                                {
                                    value: "wrap-reverse",
                                    name: "Wrap reverse",
                                },
                            ],
                        },
                        {
                            name: "Justify",
                            property: "justify-content",
                            type: "select",
                            defaults: "flex-start",
                            list: [
                                {
                                    value: "flex-start",
                                    name: "Start",
                                },
                                {
                                    value: "flex-end",
                                    name: "End",
                                },
                                {
                                    value: "center",
                                    name: "Center",
                                },
                                {
                                    value: "space-between",
                                    name: "Space between",
                                },
                                {
                                    value: "space-around",
                                    name: "Space around",
                                },
                                {
                                    value: "space-evenly",
                                    name: "Space evenly",
                                },
                            ],
                        },
                        {
                            name: "Align",
                            property: "align-items",
                            type: "select",
                            defaults: "center",
                            list: [
                                {
                                    value: "flex-start",
                                    name: "Start",
                                },
                                {
                                    value: "flex-end",
                                    name: "End",
                                },
                                {
                                    value: "center",
                                    name: "Center",
                                },
                                {
                                    value: "stretch",
                                    name: "Stretch",
                                },
                            ],
                        },
                        {
                            name: "Align",
                            property: "align-content",
                            type: "select",
                            defaults: "center",
                            list: [
                                {
                                    value: "flex-start",
                                    name: "Start",
                                },
                                {
                                    value: "flex-end",
                                    name: "End",
                                },
                                {
                                    value: "center",
                                    name: "Center",
                                },
                                {
                                    value: "space-between",
                                    name: "Space between",
                                },
                                {
                                    value: "space-around",
                                    name: "Space around",
                                },
                                {
                                    value: "space-evenly",
                                    name: "Space evenly",
                                },
                                {
                                    value: "stretch",
                                    name: "Stretch",
                                },
                            ],
                        },
                        {
                            name: "Align-self",
                            property: "align-self",
                            type: "select",
                            defaults: "auto",
                            list: [
                                {
                                    value: "auto",
                                    name: "Auto",
                                },
                                {
                                    value: "flex-start",
                                    name: "Start",
                                },
                                {
                                    value: "flex-end",
                                    name: "End",
                                },
                                {
                                    value: "center",
                                    name: "Center",
                                },
                                {
                                    value: "stretch",
                                    name: "Stretch",
                                },
                            ],
                        },
                        {
                            name: "Flex-grow",
                            property: "flex-grow",
                            type: "integer",
                            defaults: 0,
                            min: 0,
                        },
                        {
                            name: "Flex-shrink",
                            property: "flex-shrink",
                            type: "integer",
                            defaults: 0,
                            min: 0,
                        },
                        {
                            name: "Flex-basis",
                            property: "flex-basis",
                            type: "integer",
                            units: ["px", "%", ""],
                            defaults: "auto",
                        },
                    ],
                },
            ],
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
        // Tüm eklentileri ekle
        plugins: [
            "grapesjs-blocks-basic",
            "grapesjs-preset-webpage",
            "grapesjs-style-bg",
            "grapesjs-plugin-export",
            "grapesjs-plugin-forms",
            "grapesjs-custom-code",
            "grapesjs-touch",
            "grapesjs-component-countdown",
            "grapesjs-tabs",
            "grapesjs-typed",
            "grapesjs-tui-image-editor",
            "grapesjs-blocks-flexbox",
            "grapesjs-lory-slider",
            "grapesjs-navbar",
            "grapesjs-tooltip",
            "grapesjs-style-filter",
            "grapesjs-style-gradient",
            "grapesjs-preset-newsletter",
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

    console.log("Studio Editor başarıyla yüklendi!");
};
