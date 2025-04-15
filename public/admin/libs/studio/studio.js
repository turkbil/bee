/**
 * Studio Editor Ana Modül
 * Tüm modülleri birleştiren ve başlatan ana dosya
 */

/**
 * Studio Editor için GrapesJS yapılandırması
 */
window.initStudioEditor = function (config) {
    console.log('Studio Editor başlatılıyor:', config);
    
    try {
        if (!config || !config.moduleId || config.moduleId <= 0) {
            console.error('Geçersiz konfigürasyon veya modül ID:', config);
            return null;
        }
        
        // Mevcut yükleme göstergesini temizle
        const existingLoader = document.querySelector('.studio-loader');
        if (existingLoader) {
            existingLoader.remove();
        }
        
        // İlerleme durumu
        let currentProgress = 0;
        
        // Yükleme aşamaları
        const loadingStages = [
            { text: "Bileşenler yükleniyor...", icon: "fa-puzzle-piece" },
            { text: "Modüller hazırlanıyor...", icon: "fa-cube" },
            { text: "Görsel motoru başlatılıyor...", icon: "fa-paint-brush" },
            { text: "Blok sistemi oluşturuluyor...", icon: "fa-cubes" },
            { text: "İçerik analiz ediliyor...", icon: "fa-microscope" },
            { text: "Düzenleme araçları hazırlanıyor...", icon: "fa-tools" },
            { text: "Stil şablonları yükleniyor...", icon: "fa-palette" },
            { text: "Widget sistemi entegre ediliyor...", icon: "fa-cogs" },
            { text: "Kullanıcı arayüzü optimize ediliyor...", icon: "fa-sliders-h" },
            { text: "Son hazırlıklar tamamlanıyor...", icon: "fa-check-double" }
        ];
        
        // Yükleme göstergesi ekle
        const loaderElement = document.createElement('div');
        loaderElement.className = 'studio-loader';
        loaderElement.style.position = 'fixed';
        loaderElement.style.top = '0';
        loaderElement.style.left = '0';
        loaderElement.style.width = '100%';
        loaderElement.style.height = '100%';
        loaderElement.style.backgroundColor = 'rgba(255, 255, 255, 0.95)';
        loaderElement.style.display = 'flex';
        loaderElement.style.alignItems = 'center';
        loaderElement.style.justifyContent = 'center';
        loaderElement.style.zIndex = '10000';
        loaderElement.style.transition = 'opacity 0.3s ease';
        
        loaderElement.innerHTML = `
            <div class="studio-loader-content" style="text-align: center; background-color: #ffffff; padding: 40px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); max-width: 500px; width: 90%;">
                <div style="margin-bottom: 30px;">
                    <div style="font-size: 28px; margin-bottom: 15px; color: #206bc4;">
                        <i class="fas fa-wand-magic-sparkles"></i> Studio Editor
                    </div>
                    <div id="loader-spinner" style="margin-bottom: 25px;">
                        <div class="spinner-grow text-primary mx-1" role="status" style="width: 0.8rem; height: 0.8rem;"></div>
                        <div class="spinner-grow text-primary mx-1" role="status" style="width: 0.8rem; height: 0.8rem; animation-delay: 0.2s;"></div>
                        <div class="spinner-grow text-primary mx-1" role="status" style="width: 0.8rem; height: 0.8rem; animation-delay: 0.4s;"></div>
                    </div>
                </div>
                
                <div id="loading-icon" style="margin-bottom: 20px; font-size: 36px; color: #206bc4;">
                    <i class="fas fa-puzzle-piece"></i>
                </div>
                
                <h4 id="loading-text" style="margin-bottom: 30px; color: #334155; font-weight: 500;">Bileşenler yükleniyor...</h4>
                
                <div style="background-color: #f0f5fa; height: 12px; border-radius: 6px; overflow: hidden; margin-bottom: 15px;">
                    <div id="loading-progress" style="height: 100%; width: 0%; background-color: #206bc4; border-radius: 6px; transition: width 0.5s ease;"></div>
                </div>
                
                <div id="loading-status" style="font-size: 13px; color: #64748b;">
                    %0
                </div>
            </div>
        `;
        
        document.body.appendChild(loaderElement);
        
        // İlerleme çubuğunu güncelleme fonksiyonu
        function updateProgress(stage, isCompleted = false) {
            const loadingText = document.getElementById('loading-text');
            const loadingIcon = document.getElementById('loading-icon');
            const loadingProgress = document.getElementById('loading-progress');
            const loadingStatus = document.getElementById('loading-status');
            
            if (!loadingText || !loadingIcon || !loadingProgress || !loadingStatus) return;
            
            if (isCompleted) {
                // Tamamlandı durumu
                loadingText.textContent = "Studio Editor Hazır!";
                loadingIcon.innerHTML = '<i class="fas fa-check-circle"></i>';
                loadingProgress.style.width = "100%";
                loadingStatus.textContent = "%100";
                return;
            }
            
            // Normal ilerleme
            if (stage < loadingStages.length) {
                loadingText.textContent = loadingStages[stage].text;
                loadingIcon.innerHTML = `<i class="fas ${loadingStages[stage].icon}"></i>`;
                currentProgress = Math.round((stage + 1) * (90 / loadingStages.length)); // %90'a kadar ilerle
                loadingProgress.style.width = `${currentProgress}%`;
                loadingStatus.textContent = `%${currentProgress}`;
            }
        }
        
        // Aşamalı ilerleme
        function startLoading() {
            let currentStage = 0;
            
            function nextStage() {
                if (currentStage >= loadingStages.length) return;
                
                updateProgress(currentStage);
                currentStage++;
                
                if (currentStage < loadingStages.length) {
                    setTimeout(nextStage, 700);
                }
            }
            
            // İlk aşamayı başlat
            nextStage();
        }
        
        // Yükleme başlat
        startLoading();
        
        // GrapesJS Editor yapılandırması
        let editor = grapesjs.init({
            container: "#" + config.elementId,
            fromElement: false,
            height: "100%",
            width: "100%",
            storageManager: false,
            panels: { defaults: [] },
            blockManager: {
                appendTo: '.blocks-container'
            },
            styleManager: {
                appendTo: "#styles-container",
                sectors: [
                    {
                        name: 'Boyut',
                        open: true,
                        properties: [
                            'width', 'height', 'max-width', 'min-height', 'margin', 'padding'
                        ]
                    },
                    {
                        name: 'Düzen',
                        open: false,
                        properties: [
                            'display', 'position', 'top', 'right', 'bottom', 'left', 'float', 'clear', 'z-index'
                        ]
                    },
                    {
                        name: 'Flex',
                        open: false,
                        properties: [
                            'flex-direction', 'flex-wrap', 'justify-content', 'align-items', 'align-content', 'order', 'flex-basis', 'flex-grow', 'flex-shrink', 'align-self'
                        ]
                    },
                    {
                        name: 'Tipografi',
                        open: false,
                        properties: [
                            'font-family', 'font-size', 'font-weight', 'letter-spacing', 'color', 'line-height', 'text-align', 'text-decoration', 'text-shadow'
                        ]
                    },
                    {
                        name: 'Dekorasyon',
                        open: false,
                        properties: [
                            'background-color', 'border', 'border-radius', 'box-shadow'
                        ]
                    }
                ]
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
                ]
            }
        });

        // İçerik yükleme işlemi
        loadContent(editor, config);

        // Editor'ü yükleme olayını dinle
        editor.on('load', function() {
            console.log('Editor loaded event triggered');

            // Tamamlandı göster (%100)
            updateProgress(0, true);
            
            // Bekle ve animasyonu gizle
            setTimeout(() => {
                const loaderElement = document.querySelector('.studio-loader');
                if (loaderElement) {
                    loaderElement.style.opacity = '0';
                    setTimeout(() => {
                        if (loaderElement && loaderElement.parentNode) {
                            loaderElement.parentNode.removeChild(loaderElement);
                        }
                    }, 300);
                }
            }, 800);
            
            // Blokları kaydet
            registerBlocks(editor);
            
            // Butonlara olay dinleyiciler ekle
            setupButtons(editor, config);
        });
        
        // Global erişim için editörü kaydet
        window.studioEditor = editor;
        
        return editor;
        
    } catch (error) {
        console.error('Studio Editor başlatılırken kritik hata:', error);
        return null;
    }
};

/**
 * İçeriği yükle
 * @param {Object} editor - GrapesJS editor örneği
 * @param {Object} config - Yapılandırma
 */
function loadContent(editor, config) {
    // İçerik yükleme gecikmesi - canvas hazır olduktan sonra
    setTimeout(() => {
        try {
            console.log('İçerik yükleme işlemi başlatılıyor...');
            
            const htmlContentEl = document.getElementById('html-content');
            const cssContentEl = document.getElementById('css-content');
            const jsContentEl = document.getElementById('js-content');
            
            let content = htmlContentEl ? htmlContentEl.value : '';
            
            // İçerik kontrolü - geçerli HTML içeriği var mı?
            if (!content || content.trim() === '' || content.trim() === '<body></body>' || content.length < 20) {
                console.warn('Geçerli içerik bulunamadı. Varsayılan içerik yükleniyor...');
                
                content = `
                <div class="container py-4">
                    <div class="row">
                        <div class="col-12">
                            <h1 class="mb-4">Yeni Sayfa</h1>
                            <p class="lead">Bu sayfayı düzenlemek için sol taraftaki bileşenleri kullanabilirsiniz.</p>
                            <div class="alert alert-info mt-4">
                                <i class="fas fa-info-circle me-2"></i> Studio Editor ile görsel düzenleme yapabilirsiniz.
                                Düzenlemelerinizi kaydetmek için sağ üstteki Kaydet butonunu kullanın.
                            </div>
                        </div>
                    </div>
                </div>`;
            }
            
            // İçeriği editöre yükle
            editor.setComponents(content);
            console.log('İçerik editöre başarıyla yüklendi');
            
            // CSS içeriği
            if (cssContentEl && cssContentEl.value) {
                editor.setStyle(cssContentEl.value);
            }
            
        } catch (error) {
            console.error('İçerik yüklenirken hata oluştu:', error);
        }
    }, 500);
}

/**
 * Blokları kaydet
 * @param {Object} editor - GrapesJS editor örneği
 */
function registerBlocks(editor) {
    try {
        // Temel blokları ekle
        editor.BlockManager.add("section-1col", {
            label: "1 Sütun",
            category: "layout",
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

        editor.BlockManager.add("section-2col", {
            label: "2 Sütun",
            category: "layout",
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

        editor.BlockManager.add("text", {
            label: "Metin",
            category: "content",
            attributes: { class: "fa fa-font" },
            content: `<div class="my-3">
                <h3>Başlık</h3>
                <p>Buraya metin içeriği gelecek. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
            </div>`
        });
        
        // Diğer bloklar eklenebilir
    } catch (error) {
        console.error('Bloklar kaydedilirken hata:', error);
    }
}

/**
 * Butonlara olay dinleyiciler ekle
 * @param {Object} editor - GrapesJS editor örneği
 * @param {Object} config - Yapılandırma
 */
function setupButtons(editor, config) {
    try {
        // Bileşen görünürlük butonu
        const swVisibility = document.getElementById("sw-visibility");
        if (swVisibility) {
            // Eski event listener'ları temizle
            const newVisibility = swVisibility.cloneNode(true);
            if (swVisibility.parentNode) {
                swVisibility.parentNode.replaceChild(newVisibility, swVisibility);
            }
            
            newVisibility.addEventListener("click", function() {
                editor.runCommand("sw-visibility");
                this.classList.toggle("active");
            });
        }

        // İçerik temizle butonu
        const cmdClear = document.getElementById("cmd-clear");
        if (cmdClear) {
            // Eski event listener'ları temizle
            const newClear = cmdClear.cloneNode(true);
            if (cmdClear.parentNode) {
                cmdClear.parentNode.replaceChild(newClear, cmdClear);
            }
            
            newClear.addEventListener("click", function() {
                if (confirm("İçeriği temizlemek istediğinize emin misiniz? Bu işlem geri alınamaz.")) {
                    editor.DomComponents.clear();
                    editor.CssComposer.clear();
                }
            });
        }

        // Geri Al butonu
        const cmdUndo = document.getElementById("cmd-undo");
        if (cmdUndo) {
            // Eski event listener'ları temizle
            const newUndo = cmdUndo.cloneNode(true);
            if (cmdUndo.parentNode) {
                cmdUndo.parentNode.replaceChild(newUndo, cmdUndo);
            }
            
            newUndo.addEventListener("click", function() {
                editor.UndoManager.undo();
            });
        }

        // Yinele butonu
        const cmdRedo = document.getElementById("cmd-redo");
        if (cmdRedo) {
            // Eski event listener'ları temizle
            const newRedo = cmdRedo.cloneNode(true);
            if (cmdRedo.parentNode) {
                cmdRedo.parentNode.replaceChild(newRedo, cmdRedo);
            }
            
            newRedo.addEventListener("click", function() {
                editor.UndoManager.redo();
            });
        }
        
        // HTML kodu düzenleme
        const cmdCodeEdit = document.getElementById("cmd-code-edit");
        if (cmdCodeEdit) {
            // Eski event listener'ları temizle
            const newCodeEdit = cmdCodeEdit.cloneNode(true);
            if (cmdCodeEdit.parentNode) {
                cmdCodeEdit.parentNode.replaceChild(newCodeEdit, cmdCodeEdit);
            }
            
            newCodeEdit.addEventListener("click", function() {
                const htmlContent = editor.getHtml();
                showEditModal("HTML Düzenle", htmlContent, (newHtml) => {
                    editor.setComponents(newHtml);
                });
            });
        }

        // CSS kodu düzenleme
        const cmdCssEdit = document.getElementById("cmd-css-edit");
        if (cmdCssEdit) {
            // Eski event listener'ları temizle
            const newCssEdit = cmdCssEdit.cloneNode(true);
            if (cmdCssEdit.parentNode) {
                cmdCssEdit.parentNode.replaceChild(newCssEdit, cmdCssEdit);
            }
            
            newCssEdit.addEventListener("click", function() {
                const cssContent = editor.getCss();
                showEditModal("CSS Düzenle", cssContent, (newCss) => {
                    editor.setStyle(newCss);
                });
            });
        }
        
        // Kaydet butonu
        const saveBtn = document.getElementById("save-btn");
        if (saveBtn) {
            // Eski event listener'ları temizle
            const newSaveBtn = saveBtn.cloneNode(true);
            if (saveBtn.parentNode) {
                saveBtn.parentNode.replaceChild(newSaveBtn, saveBtn);
            }
            
            newSaveBtn.addEventListener("click", function() {
                // Butonu devre dışı bırak ve animasyon ekle
                this.disabled = true;
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Kaydediliyor...';
                
                // Kaydedilecek içeriği hazırla
                const htmlContent = editor.getHtml();
                const cssContent = editor.getCss();
                
                // JS içeriğini al
                const jsContentEl = document.getElementById("js-content");
                const jsContent = jsContentEl ? jsContentEl.value : '';
                
                // CSRF token
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                // Kaydetme URL'si
                const saveUrl = `/admin/studio/save/${config.module}/${config.moduleId}`;
                
                // İçeriği kaydet
                fetch(saveUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        content: htmlContent,
                        css: cssContent,
                        js: jsContent
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Başarılı', data.message || 'İçerik başarıyla kaydedildi!');
                    } else {
                        showNotification('Hata', data.message || 'Kayıt işlemi başarısız.', 'error');
                    }
                })
                .catch(error => {
                    showNotification('Hata', error.message || 'Sunucuya bağlanırken bir hata oluştu.', 'error');
                })
                .finally(() => {
                    // Butonu normal haline getir
                    this.disabled = false;
                    this.innerHTML = originalText;
                });
            });
        }
        
        // Önizleme butonu
        const previewBtn = document.getElementById("preview-btn");
        if (previewBtn) {
            // Eski event listener'ları temizle
            const newPreviewBtn = previewBtn.cloneNode(true);
            if (previewBtn.parentNode) {
                previewBtn.parentNode.replaceChild(newPreviewBtn, previewBtn);
            }
            
            newPreviewBtn.addEventListener("click", function() {
                // Butonu devre dışı bırak ve animasyon ekle
                this.disabled = true;
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Yükleniyor...';
                
                try {
                    // İçeriği al
                    const html = editor.getHtml() || '';
                    const css = editor.getCss() || '';
                    const jsContentEl = document.getElementById("js-content");
                    const js = jsContentEl ? jsContentEl.value || '' : '';
                    
                    // Önizleme penceresi oluştur
                    const previewWindow = window.open('', '_blank');
                    
                    if (!previewWindow) {
                        showNotification('Uyarı', 'Önizleme penceresi açılamadı. Lütfen popup engelleyicinizi kontrol edin.', 'warning');
                        return;
                    }
                    
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
                } catch (error) {
                    showNotification('Hata', 'Önizleme oluşturulurken bir hata oluştu: ' + error.message, 'error');
                } finally {
                    // Butonu normal haline getir
                    this.disabled = false;
                    this.innerHTML = originalText;
                }
            });
        }
        
        // Dışa aktar butonu
        const exportBtn = document.getElementById("export-btn");
        if (exportBtn) {
            // Eski event listener'ları temizle
            const newExportBtn = exportBtn.cloneNode(true);
            if (exportBtn.parentNode) {
                exportBtn.parentNode.replaceChild(newExportBtn, exportBtn);
            }
            
            newExportBtn.addEventListener("click", function() {
                // Butonu devre dışı bırak ve animasyon ekle
                this.disabled = true;
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Hazırlanıyor...';
                
                try {
                    // İçeriği al
                    const html = editor.getHtml() || '';
                    const css = editor.getCss() || '';
                    const jsContentEl = document.getElementById("js-content");
                    const js = jsContentEl ? jsContentEl.value || '' : '';

                    const exportContent = `<!DOCTYPE html>
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

                    // Dışa aktarma modalını göster
                    showEditModal("HTML Dışa Aktar", exportContent, function(newContent) {
                        // HTML olarak indirme seçeneği
                        try {
                            const blob = new Blob([newContent], {type: 'text/html'});
                            const url = URL.createObjectURL(blob);
                            const a = document.createElement('a');
                            a.href = url;
                            a.download = 'exported-page.html';
                            document.body.appendChild(a);
                            a.click();
                            document.body.removeChild(a);
                            URL.revokeObjectURL(url);
                            
                            showNotification('Başarılı', 'Sayfa başarıyla dışa aktarıldı!', 'success');
                        } catch (error) {
                            showNotification('Hata', 'Dışa aktarma sırasında bir hata oluştu: ' + error.message, 'error');
                        }
                    });
                } catch (error) {
                    showNotification('Hata', 'Dışa aktarma sırasında bir hata oluştu: ' + error.message, 'error');
                } finally {
                    // Butonu normal haline getir
                    setTimeout(() => {
                        this.disabled = false;
                        this.innerHTML = originalText;
                    }, 500);
                }
            });
        }
        
        // Cihaz görünümü değiştirme butonları
        setupDeviceButtons(editor);
    } catch (error) {
        console.error('Buton ayarlarken hata:', error);
    }
}

/**
 * Cihaz görünümü değiştirme butonlarını ayarla
 * @param {Object} editor - GrapesJS editor örneği
 */
function setupDeviceButtons(editor) {
    const deviceDesktop = document.getElementById("device-desktop");
    const deviceTablet = document.getElementById("device-tablet");
    const deviceMobile = document.getElementById("device-mobile");

    // Tüm butonları kopyalama ve eski olay dinleyicileri temizleme
    function recreateButton(button) {
        if (!button) return null;
        
        const newButton = button.cloneNode(true);
        if (button.parentNode) {
            button.parentNode.replaceChild(newButton, button);
        }
        return newButton;
    }
    
    const newDesktopBtn = recreateButton(deviceDesktop);
    const newTabletBtn = recreateButton(deviceTablet);
    const newMobileBtn = recreateButton(deviceMobile);

    function toggleDeviceButtons(activeBtn) {
        const deviceBtns = document.querySelectorAll(".device-btns button");
        if (deviceBtns) {
            deviceBtns.forEach((btn) => {
                btn.classList.remove("active");
            });
            if (activeBtn) {
                activeBtn.classList.add("active");
            }
        }
    }

    if (newDesktopBtn) {
        newDesktopBtn.addEventListener("click", function () {
            editor.setDevice("Desktop");
            toggleDeviceButtons(this);
        });
    }

    if (newTabletBtn) {
        newTabletBtn.addEventListener("click", function () {
            editor.setDevice("Tablet");
            toggleDeviceButtons(this);
        });
    }

    if (newMobileBtn) {
        newMobileBtn.addEventListener("click", function () {
            editor.setDevice("Mobile");
            toggleDeviceButtons(this);
        });
    }
}

/**
 * Kod düzenleme modalı göster
 * @param {string} title - Modal başlığı
 * @param {string} content - Düzenlenecek içerik
 * @param {Function} callback - Değişiklik kaydedildiğinde çağrılacak fonksiyon
 */
function showEditModal(title, content, callback) {
    // Mevcut modalı temizle
    const existingModal = document.getElementById("codeEditModal");
    if (existingModal) {
        existingModal.remove();
    }
    
    // Mevcut backdrop'ları temizle
    const backdropElements = document.querySelectorAll('.modal-backdrop');
    backdropElements.forEach(element => {
        if (element.parentNode) {
            element.parentNode.removeChild(element);
        }
    });
    
    const modal = document.createElement("div");
    modal.className = "modal fade";
    modal.id = "codeEditModal";
    modal.setAttribute("tabindex", "-1");
    modal.setAttribute("aria-modal", "true");
    modal.setAttribute("role", "dialog");
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
                const newCode = document.getElementById("code-editor").value;
                callback(newCode);
                modalInstance.hide();
            });

        modal.addEventListener("hidden.bs.modal", function () {
            modal.remove();
            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(backdrop => {
                if (backdrop.parentNode) {
                    backdrop.parentNode.removeChild(backdrop);
                }
            });
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

/**
 * Bildirim göster
 * @param {string} title - Bildirim başlığı
 * @param {string} message - Bildirim mesajı
 * @param {string} type - Bildirim tipi (success, error, warning, info)
 */
function showNotification(title, message, type = "success") {
    const notif = document.createElement("div");
    notif.className = `toast align-items-center text-white bg-${
        type === "success" ? "success" : 
        type === "error" ? "danger" : 
        type === "warning" ? "warning" : 
        "info"
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
        container.style.zIndex = "9999";
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
                if (container.contains(notif)) {
                    container.removeChild(notif);
                }
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
 
 // Cihaz görünümü değiştirme butonları için tab işlemleri
 document.addEventListener('DOMContentLoaded', function() {
    setupTabs();
    
    // Editor elementini kontrol et
    const editorElement = document.getElementById('gjs');
    if (editorElement) {
        // Konfigürasyon oluştur
        const config = {
            elementId: 'gjs',
            module: editorElement.getAttribute('data-module-type') || 'page',
            moduleId: parseInt(editorElement.getAttribute('data-module-id') || '0'),
            content: document.getElementById('html-content') ? document.getElementById('html-content').value : '',
            css: document.getElementById('css-content') ? document.getElementById('css-content').value : '',
        };
        
        // Editor başlat
        if (typeof window.initStudioEditor === 'function') {
            window.initStudioEditor(config);
        }
    }
 });
 
 /**
 * Sol panel sekmelerini ayarla
 */
 function setupTabs() {
    const tabs = document.querySelectorAll(".panel-tab");
    const tabContents = document.querySelectorAll(".panel-tab-content");
 
    tabs.forEach((tab) => {
        // Eski event listener'ları temizle
        const newTab = tab.cloneNode(true);
        if (tab.parentNode) {
            tab.parentNode.replaceChild(newTab, tab);
        }
        
        newTab.addEventListener("click", function () {
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
 }