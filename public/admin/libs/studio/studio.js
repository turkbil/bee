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
        panels: { defaults: [] },
        blockManager: {
            appendTo: "#blocks-container",
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
        // Plugin'leri kaldırdık
        plugins: [],
        pluginsOpts: {}
    });

    // Blok kategorileri tanımla
    editor.BlockManager.getCategories().reset();
    editor.BlockManager.getCategories().add([
        { id: "Temel", label: "Temel Bileşenler" },
        { id: "Bileşenler", label: "Bootstrap Bileşenleri" },
    ]);

    // Manuel olarak bloklar ekleyelim
    // 1 Sütunlu Bölüm
    editor.BlockManager.add("section-1col", {
        label: "1 Sütun",
        category: "Temel",
        attributes: { class: "fa fa-columns" },
        content: `<section class="container py-5">
            <div class="row">
                <div class="col-md-12">
                    <h2>Başlık Buraya</h2>
                    <p>İçerik buraya gelecek. Çift tıklayarak düzenleyebilirsiniz.</p>
                </div>
            </div>
        </section>`
    });

    // 2 Sütunlu Bölüm
    editor.BlockManager.add("section-2col", {
        label: "2 Sütun",
        category: "Temel",
        attributes: { class: "fa fa-columns" },
        content: `<section class="container py-5">
            <div class="row">
                <div class="col-md-6">
                    <h3>Başlık 1</h3>
                    <p>İçerik buraya gelecek. Çift tıklayarak düzenleyebilirsiniz.</p>
                </div>
                <div class="col-md-6">
                    <h3>Başlık 2</h3>
                    <p>İçerik buraya gelecek. Çift tıklayarak düzenleyebilirsiniz.</p>
                </div>
            </div>
        </section>`
    });

    // 3 Sütunlu Bölüm
    editor.BlockManager.add("section-3col", {
        label: "3 Sütun",
        category: "Temel",
        attributes: { class: "fa fa-columns" },
        content: `<section class="container py-5">
            <div class="row">
                <div class="col-md-4">
                    <h3>Başlık 1</h3>
                    <p>İçerik buraya gelecek.</p>
                </div>
                <div class="col-md-4">
                    <h3>Başlık 2</h3>
                    <p>İçerik buraya gelecek.</p>
                </div>
                <div class="col-md-4">
                    <h3>Başlık 3</h3>
                    <p>İçerik buraya gelecek.</p>
                </div>
            </div>
        </section>`
    });

    // Header Blok
    editor.BlockManager.add("header", {
        label: "Header",
        category: "Temel",
        attributes: { class: "fa fa-header" },
        content: `<header class="bg-primary text-white py-4">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h1>Şirket İsmi</h1>
                    </div>
                    <div class="col-md-6 text-end">
                        <nav>
                            <a href="#" class="btn btn-outline-light me-2">Ana Sayfa</a>
                            <a href="#" class="btn btn-outline-light me-2">Hakkımızda</a>
                            <a href="#" class="btn btn-outline-light">İletişim</a>
                        </nav>
                    </div>
                </div>
            </div>
        </header>`
    });

    // Footer Blok
    editor.BlockManager.add("footer", {
        label: "Footer",
        category: "Temel",
        attributes: { class: "fa fa-window-minimize" },
        content: `<footer class="bg-dark text-white py-4 mt-5">
            <div class="container">
                <div class="row">
                    <div class="col-md-4">
                        <h4>Hakkımızda</h4>
                        <p>Şirketimiz hakkında kısa bir açıklama.</p>
                    </div>
                    <div class="col-md-4">
                        <h4>Hızlı Linkler</h4>
                        <ul class="list-unstyled">
                            <li><a href="#" class="text-white">Ana Sayfa</a></li>
                            <li><a href="#" class="text-white">Hizmetler</a></li>
                            <li><a href="#" class="text-white">İletişim</a></li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <h4>İletişim</h4>
                        <address>
                            <p><i class="fas fa-map-marker-alt"></i> Adres, Şehir, Ülke</p>
                            <p><i class="fas fa-phone"></i> +90 123 456 7890</p>
                            <p><i class="fas fa-envelope"></i> info@ornek.com</p>
                        </address>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col text-center">
                        <p class="mb-0">&copy; 2025 Tüm Hakları Saklıdır.</p>
                    </div>
                </div>
            </div>
        </footer>`
    });

    // Text Blok
    editor.BlockManager.add("text", {
        label: "Metin",
        category: "Temel",
        attributes: { class: "fa fa-text-width" },
        content: {
            type: "text",
            content: "<p>Çift tıklayarak bu metni düzenleyebilirsiniz.</p>",
            style: { padding: "10px" }
        }
    });

    // Link Blok
    editor.BlockManager.add("link", {
        label: "Link",
        category: "Temel",
        attributes: { class: "fa fa-link" },
        content: {
            type: "link",
            content: "Link Metni",
            style: { color: "#007bff" }
        }
    });

    // Image Blok
    editor.BlockManager.add("image", {
        label: "Görsel",
        category: "Temel",
        attributes: { class: "fa fa-image" },
        content: {
            type: "image",
            style: { padding: "10px" },
            attributes: { alt: "Görsel açıklaması" }
        }
    });

    // Button Blok
    editor.BlockManager.add("button", {
        label: "Buton",
        category: "Temel",
        attributes: { class: "fa fa-square" },
        content: '<a class="btn btn-primary" href="#">Butona Tıkla</a>'
    });

    // Card Blok
    editor.BlockManager.add("card", {
        label: "Kart",
        category: "Bileşenler",
        attributes: { class: "fa fa-credit-card" },
        content: `<div class="card" style="width: 18rem;">
            <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="...">
            <div class="card-body">
                <h5 class="card-title">Kart Başlığı</h5>
                <p class="card-text">Kartın içeriği buraya gelecek. Kısa bir açıklama yazabilirsiniz.</p>
                <a href="#" class="btn btn-primary">Detay</a>
            </div>
        </div>`
    });

    // Jumbotron Blok
    editor.BlockManager.add("jumbotron", {
        label: "Jumbotron",
        category: "Bileşenler",
        attributes: { class: "fa fa-newspaper-o" },
        content: `<div class="bg-light p-5 rounded-3 mb-4">
            <div class="container py-5">
                <h1 class="display-5 fw-bold">Hoş Geldiniz!</h1>
                <p class="col-md-8 fs-4">Sitenizin ana mesajı veya sloganı buraya yazılabilir. Bu alan dikkat çekmek için kullanılır.</p>
                <a class="btn btn-primary btn-lg" href="#" role="button">Daha Fazla</a>
            </div>
        </div>`
    });

    // Navbar Blok
    editor.BlockManager.add("navbar", {
        label: "Navbar",
        category: "Bileşenler",
        attributes: { class: "fa fa-bars" },
        content: `<nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container">
                <a class="navbar-brand" href="#">Şirket İsmi</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="#">Ana Sayfa</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Hakkımızda</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Hizmetler</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">İletişim</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>`
    });

    // HTML blok
    editor.BlockManager.add('html', {
        label: 'HTML Kodu',
        category: 'Temel',
        attributes: { class: 'fa fa-code' },
        content: {
            type: 'text',
            content: '<div>HTML kodunuzu buraya yazın</div>',
            style: { padding: '10px' }
        }
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
    const swVisibility = document.getElementById("sw-visibility");
    if (swVisibility) {
        swVisibility.addEventListener("click", () => {
            editor.runCommand("sw-visibility");
        });
    }

    const cmdClear = document.getElementById("cmd-clear");
    if (cmdClear) {
        cmdClear.addEventListener("click", () => {
            if (
                confirm(
                    "İçeriği temizlemek istediğinize emin misiniz? Bu işlem geri alınamaz."
                )
            ) {
                editor.runCommand("core:canvas-clear");
            }
        });
    }

    const cmdUndo = document.getElementById("cmd-undo");
    if (cmdUndo) {
        cmdUndo.addEventListener("click", () => {
            editor.runCommand("core:undo");
        });
    }

    const cmdRedo = document.getElementById("cmd-redo");
    if (cmdRedo) {
        cmdRedo.addEventListener("click", () => {
            editor.runCommand("core:redo");
        });
    }

    // Kod düzenleme modalları
    const cmdCodeEdit = document.getElementById("cmd-code-edit");
    if (cmdCodeEdit) {
        cmdCodeEdit.addEventListener("click", () => {
            const htmlContent = editor.getHtml();
            showEditModal("HTML Düzenle", htmlContent, (newHtml) => {
                editor.setComponents(newHtml);
            });
        });
    }

    const cmdCssEdit = document.getElementById("cmd-css-edit");
    if (cmdCssEdit) {
        cmdCssEdit.addEventListener("click", () => {
            const cssContent = editor.getCss();
            const cssContentEl = document.getElementById("css-content");
            if (cssContentEl) {
                cssContentEl.value = cssContent;
            }
            showEditModal("CSS Düzenle", cssContent, (newCss) => {
                if (cssContentEl) {
                    cssContentEl.value = newCss;
                }
                editor.setStyle(newCss);
            });
        });
    }

    const cmdJsEdit = document.getElementById("cmd-js-edit");
    if (cmdJsEdit) {
        cmdJsEdit.addEventListener("click", () => {
            const jsContentEl = document.getElementById("js-content");
            const jsContent = jsContentEl ? jsContentEl.value : "";
            showEditModal("JavaScript Düzenle", jsContent, (newJs) => {
                if (jsContentEl) {
                    jsContentEl.value = newJs;
                }
            });
        });
    }

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

        // Bootstrap.Modal nesnesi mevcut mu kontrol et
        if (typeof bootstrap !== "undefined" && bootstrap.Modal) {
            const modalInstance = new bootstrap.Modal(modal);
            modalInstance.show();

            document
                .getElementById("saveCodeBtn")
                .addEventListener("click", function () {
                    const newCode =
                        document.getElementById("code-editor").value;
                    callback(newCode);
                    modalInstance.hide();

                    modal.addEventListener("hidden.bs.modal", function () {
                        modal.remove();
                    });
                });

            modal.addEventListener("hidden.bs.modal", function () {
                modal.remove();
            });
        } else {
            // Fallback - basit modal gösterimi
            modal.style.display = "block";
            modal.style.backgroundColor = "rgba(0,0,0,0.5)";

            const saveBtn = modal.querySelector("#saveCodeBtn");
            if (saveBtn) {
                saveBtn.addEventListener("click", function () {
                    const newCode =
                        document.getElementById("code-editor").value;
                    callback(newCode);
                    document.body.removeChild(modal);
                });
            }

            const closeBtn = modal.querySelector(".btn-close");
            if (closeBtn) {
                closeBtn.addEventListener("click", function () {
                    document.body.removeChild(modal);
                });
            }

            const cancelBtn = modal.querySelector(".btn-secondary");
            if (cancelBtn) {
                cancelBtn.addEventListener("click", function () {
                    document.body.removeChild(modal);
                });
            }
        }
    }

    // Cihaz görünümü
    const deviceDesktop = document.getElementById("device-desktop");
    const deviceTablet = document.getElementById("device-tablet");
    const deviceMobile = document.getElementById("device-mobile");

    function toggleDeviceButtons(activeBtn) {
        const deviceBtns = document.querySelectorAll(
            ".device-btns .toolbar-btn"
        );
        if (deviceBtns) {
            deviceBtns.forEach((btn) => {
                btn.classList.remove("active");
            });
            if (activeBtn) {
                activeBtn.classList.add("active");
            }
        }
    }

    if (deviceDesktop) {
        deviceDesktop.addEventListener("click", function () {
            editor.setDevice("Desktop");
            toggleDeviceButtons(this);
        });
    }

    if (deviceTablet) {
        deviceTablet.addEventListener("click", function () {
            editor.setDevice("Tablet");
            toggleDeviceButtons(this);
        });
    }

    if (deviceMobile) {
        deviceMobile.addEventListener("click", function () {
            editor.setDevice("Mobile");
            toggleDeviceButtons(this);
        });
    }

    // Kaydetme
    const saveBtn = document.getElementById("save-btn");
    if (saveBtn) {
        saveBtn.addEventListener("click", function () {
            const htmlContent = editor.getHtml();
            const cssContent = editor.getCss();
            const jsContentEl = document.getElementById("js-content");
            const jsContent = jsContentEl ? jsContentEl.value : "";

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
    }

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

        // Bootstrap Toast API mevcut mu kontrol et
        if (typeof bootstrap !== "undefined" && bootstrap.Toast) {
            const toast = new bootstrap.Toast(notif, {
                autohide: true,
                delay: 3000,
            });
            toast.show();
        } else {
            // Fallback - basit toast gösterimi
            notif.style.display = "block";
            setTimeout(() => {
                notif.style.opacity = "0";
                setTimeout(() => {
                    container.removeChild(notif);
                }, 300);
            }, 3000);
        }

        // Belli bir süre sonra kaldır
        setTimeout(() => {
            if (container.contains(notif)) {
                container.removeChild(notif);
            }
        }, 3300);
    }

    // Preview butonu için olay dinleyici
    const previewBtn = document.getElementById("preview-btn");
    if (previewBtn) {
        previewBtn.addEventListener("click", function () {
            editor.runCommand("preview");
        });
    }

    // Export butonu için olay dinleyici
    const exportBtn = document.getElementById("export-btn");
    if (exportBtn) {
        exportBtn.addEventListener("click", function () {
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

    console.log("Studio Editor başarıyla yüklendi!");
};