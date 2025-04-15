/**
 * Studio Editor - Çekirdek Modül
 * GrapesJS editor yapılandırması ve temel kurulumu
 */
window.StudioCore = (function() {
    let editor = null;
    
    /**
     * GrapesJS editor örneğini başlatır
     * @param {Object} config - Editor yapılandırması
     * @returns {Object} - GrapesJS editor örneği
     */
    function initEditor(config) {
        // Başlatma bilgilerini logla
        console.log('GrapesJS editor başlatılıyor...');
        
        try {
            // GrapesJS Editor yapılandırması
            editor = grapesjs.init({
                container: "#" + config.elementId,
                fromElement: false,
                height: "100%",
                width: "100%",
                storageManager: false,
                panels: { defaults: [] },
                blockManager: { appendTo: '' }, // Default UI render'ı engelle
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
                        },
                        {
                            name: 'Ekstra',
                            open: false,
                            properties: [
                                'opacity', 'transition', 'transform', 'perspective', 'transform-style'
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
            
            // Editor başlatıldı
            console.log('GrapesJS editor başarıyla oluşturuldu.');
            
        } catch (error) {
            console.error('GrapesJS başlatılırken hata oluştu:', error);
            return null;
        }

        // İçerik yükleme işlemi
        loadContent(config);

        // Editor'ü yükleme olayını dinle
        editor.on('load', function() {
            console.log('Editor loaded event triggered');

            // Editor yüklendi, animasyonu gizle
            const loaderElement = document.querySelector('.studio-loader');
            if (loaderElement) {
                loaderElement.style.opacity = '0';
                setTimeout(() => {
                    if (loaderElement && loaderElement.parentNode) {
                        loaderElement.parentNode.removeChild(loaderElement);
                    }
                }, 300);
            }
            
            // DOM'un hazır olmasından sonra stil yöneticisini düzenle
            setTimeout(() => {
                fixStyleManagerIssues();
                loadEventHandlers();
                
                // Editor hazır olayını tetikle
                const editorReadyEvent = new CustomEvent('editor:loaded');
                document.dispatchEvent(editorReadyEvent);
            }, 500);
        });
        
        return editor;
    }
    
    /**
     * Stil yöneticisi sorunlarını düzelt
     */
    function fixStyleManagerIssues() {
        // Stil yöneticisi UI sorunlarını düzelt
        const styleManager = editor.StyleManager;
        if (styleManager) {
            // Gerekli düzeltmeler burada yapılabilir
            console.log('Stil yöneticisi düzeltmeleri uygulandı');
        }
    }
    
    /**
     * İçeriği kaydetmek için hazırla
     * @returns {string} - Kaydedilmeye hazır HTML içeriği
     */
    function prepareContentForSave() {
        // HTML Parser modülünü kullanarak içeriği kaydetmeye hazırla
        if (window.StudioHtmlParser && typeof window.StudioHtmlParser.prepareContentForSave === 'function') {
            console.log('StudioHtmlParser ile içerik kaydetmeye hazırlanıyor...');
            return window.StudioHtmlParser.prepareContentForSave(editor);
        }
        
        // HTML Parser modülü yoksa, editörün kendi metodunu kullan
        console.warn('StudioHtmlParser modülü bulunamadı, editörün kendi getHtml metodunu kullanıyor');
        return editor.getHtml();
    }
    
    /**
     * İçeriği yükle
     * @param {Object} config - Editor yapılandırması
     */
    function loadContent(config) {
        // İçerik yükleme gecikmesi - canvas hazır olduktan sonra
        setTimeout(() => {
            try {
                console.log('İçerik yükleme işlemi başlatılıyor...');
                
                // Değişkenler için console.log yaparak içerik durumunu izle
                console.log('Editor İçerik Durumu:', {
                    'config.content uzunluğu': config.content ? config.content.length : 0,
                    'config.content kısa önizleme': config.content ? 
                        (config.content.length > 50 ? 
                            config.content.substring(0, 50) + '...' : config.content) 
                        : null,
                    'içerik tipi': typeof config.content,
                    'HTML içeriyor mu': config.content ? config.content.includes('<') : false
                });
                
                let content = '';
                
                // StudioHtmlParser modülünü kullanarak içeriği işle
                if (window.StudioHtmlParser && typeof window.StudioHtmlParser.parseAndFixHtml === 'function') {
                    // HTML Parser modülünü kullanarak içeriği temizle ve düzelt
                    content = window.StudioHtmlParser.parseAndFixHtml(config.content);
                    console.log('StudioHtmlParser ile içerik başarıyla işlendi:', 
                        content.length > 50 ? content.substring(0, 50) + '...' : content);
                } else {
                    // HTML Parser modülü yüklenemezse, ham içeriği kullan
                    console.warn('StudioHtmlParser modülü bulunamadı, ham içerik kullanılıyor');
                    content = config.content || '';
                }
                
                // İçerik kontrolü - geçerli HTML içeriği var mı?
                if (!content || content.trim() === '' || content.trim() === '<body></body>' || content.length < 20) {
                    console.warn('Geçerli içerik bulunamadı. Varsayılan içerik yükleniyor...');
                    
                    // Varsayılan içerik için HTML Parser modülünü kullan
                    if (window.StudioHtmlParser && typeof window.StudioHtmlParser.getDefaultContent === 'function') {
                        content = window.StudioHtmlParser.getDefaultContent();
                        console.log('StudioHtmlParser varsayılan içeriği kullanıldı');
                    } else {
                        // HTML Parser modülü yoksa, dahili varsayılan içeriği kullan
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
                }
                
                // İçeriği editöre yükle
                editor.setComponents(content);
                console.log('İçerik editöre başarıyla yüklendi');
                
            } catch (error) {
                console.error('İçerik yüklenirken hata oluştu:', error);
                
                // Hata durumunda varsayılan içeriği yüklemeyi dene
                try {
                    let errorContent;
                    
                    // HTML Parser modülünü kullanarak hata içeriğini oluştur
                    if (window.StudioHtmlParser && typeof window.StudioHtmlParser.getDefaultContent === 'function') {
                        // Özel hata içeriği oluştur
                        errorContent = `
                        <div class="container py-4">
                            <div class="row">
                                <div class="col-12">
                                    <h1 class="mb-4">Hata Oluştu</h1>
                                    <div class="alert alert-danger">
                                        <i class="fas fa-exclamation-triangle me-2"></i> İçerik yüklenirken bir hata oluştu.
                                        Lütfen sayfayı yenileyin veya yöneticinize başvurun.
                                    </div>
                                    <div class="alert alert-secondary mt-3">
                                        <small>Hata detayı: ${error.message || 'Bilinmeyen hata'}</small>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                    } else {
                        // Basit hata içeriği
                        errorContent = `
                        <div class="container py-4">
                            <div class="row">
                                <div class="col-12">
                                    <h1 class="mb-4">Hata Oluştu</h1>
                                    <div class="alert alert-danger">
                                        <i class="fas fa-exclamation-triangle me-2"></i> İçerik yüklenirken bir hata oluştu.
                                        Lütfen sayfayı yenileyin veya yöneticinize başvurun.
                                    </div>
                                </div>
                            </div>
                        </div>`;
                    }
                    
                    editor.setComponents(errorContent);
                    console.log('Hata sonrası özel içerik yüklendi');
                } catch (e) {
                    console.error('Varsayılan içerik yüklenirken de hata oluştu:', e);
                }
            }
        }, 500);
    }
    
    /**
     * Olay dinleyicilerini yükle
     */
    function loadEventHandlers() {
        // Gerekli olay dinleyicileri burada tanımlanabilir
        console.log('Olay dinleyicileri yüklendi');
    }
    
    return {
        initEditor: initEditor,
        getEditor: function() { return editor; },
        prepareContentForSave: prepareContentForSave
    };
})();