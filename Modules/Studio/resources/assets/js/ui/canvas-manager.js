// Modules/Studio/resources/assets/js/ui/canvas-manager.js

/**
 * Studio Canvas Manager
 * Çalışma alanı yapılandırmasını yöneten modül
 */
const StudioCanvasManager = (function() {
    let editor = null;
    let config = {};
    
    /**
     * Çalışma alanını yapılandır
     * @param {Object} editorInstance GrapesJS editor örneği
     * @param {Object} options Çalışma alanı seçenekleri
     */
    function setupCanvas(editorInstance, options = {}) {
        editor = editorInstance;
        
        // Varsayılan yapılandırma
        config = {
            canvasStyles: [
                'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css',
                'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'
            ],
            canvasScripts: [
                'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js'
            ],
            ...options
        };
        
        // Canvas stilleri ve scriptlerini ekle
        setupCanvasAssets();
        
        // Canvas Frame yüklendikten sonra sürükle-bırak olaylarını yönet
        const waitForCanvasFrame = setInterval(() => {
            const frame = editor.Canvas.getFrame();
            if (frame) {
                clearInterval(waitForCanvasFrame);
                console.log('Canvas frame bulundu, sürükle-bırak işlemleri etkinleştiriliyor...');
                
                // Frame yüklendikten sonra bırakma olayını yönet
                frame.view.el.contentDocument.addEventListener('DOMContentLoaded', function() {
                    handleDropEvents();
                    console.log('Canvas sürükle-bırak işlemleri başarıyla etkinleştirildi');
                });
            }
        }, 500);
        
        // Bileşen seçimini ayarla
        setupComponentSelection();
        
        console.log('Canvas yöneticisi başlatıldı');
    }
    
    /**
     * Canvas stil ve script varlıklarını ekle
     */
    function setupCanvasAssets() {
        const canvas = editor.Canvas;
        
        // Stilleri ekle
        if (config.canvasStyles && config.canvasStyles.length) {
            canvas.getConfig().styles = config.canvasStyles;
        }
        
        // Scriptleri ekle
        if (config.canvasScripts && config.canvasScripts.length) {
            canvas.getConfig().scripts = config.canvasScripts;
        }
        
        console.log('Canvas varlıkları eklendi');
    }
        
    /**
     * Sürükle-bırak olaylarını yönet
     */
    function handleDropEvents() {
        const canvas = editor.Canvas;
        
        // Canvas frame'ini al
        const frame = canvas.getFrame();
        if (!frame) {
            console.log('Canvas frame bulunamadı, sürükle-bırak olayları ayarlanıyor...');
            return;
        }
        
        const canvasBody = frame.view.el.contentDocument.body;
        
        if (!canvasBody) {
            console.warn('Canvas body elemanı bulunamadı');
            return;
        }
        
        // Her zaman sadece bir sürükle-bırak olayı ekle
        if (canvasBody._dropHandlersAttached) {
            return;
        }
        
        canvasBody._dropHandlersAttached = true;
        
        canvasBody.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.add('gjs-droppable-active');
        });
        
        canvasBody.addEventListener('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.remove('gjs-droppable-active');
        });
        
        // Sadece bir kez ekleyelim
        canvasBody.addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.remove('gjs-droppable-active');
            
            // Çift eklemeyi önlemek için işlemi kilitle
            if (window.isHandlingDrop) {
                return;
            }
            
            window.isHandlingDrop = true;
            
            try {
                // Blok sürüklenme kontrolü
                if (e.dataTransfer.getData('blockId')) {
                    const blockId = e.dataTransfer.getData('blockId');
                    const block = editor.BlockManager.get(blockId);
                    
                    if (block) {
                        handleDrop(e, block);
                    }
                }
                // Dosya sürüklenme kontrolü
                else if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length) {
                    const files = e.dataTransfer.files;
                    handleFilesDrop(files, e);
                }
            } finally {
                // İşlemi her durumda bitir
                setTimeout(() => {
                    window.isHandlingDrop = false;
                }, 100);
            }
        });
    }
    
    /**
     * Bırakma işlemi gerçekleştiğinde
     * @param {Event} e Olay
     * @param {Object} block Sürüklenen blok
     */
    function handleDrop(e, block) {
        const content = block.get('content');
        
        if (typeof content === 'string') {
            safelyAddComponent(content);
        } else if (typeof content === 'object') {
            const component = editor.DomComponents.addComponent(content);
            safelyAddComponent(component);
        }
    }
    
    /**
     * Bileşeni güvenli bir şekilde ekle
     * @param {String|Object} component Eklenecek bileşen
     */
    function safelyAddComponent(component) {
        try {
            // Bileşeni ekle
            editor.addComponents(component);
            
            // Bileşene tıklama olayını eklemeden önce kısa bir bekleme
            setTimeout(() => {
                // En son eklenen bileşeni seç
                const components = editor.DomComponents.getComponents();
                const lastComponent = components.at(components.length - 1);
                
                if (lastComponent) {
                    editor.select(lastComponent);
                }
            }, 10);
        } catch (error) {
            console.error('Bileşen eklenirken hata:', error);
        }
    }
    
    /**
     * Dosya sürükle-bırak işlemini yönet
     * @param {FileList} files Dosya listesi
     * @param {Event} dropEvent Sürükle-bırak olayı
     */
    function handleFilesDrop(files, dropEvent) {
        if (!files || !files.length) {
            return;
        }
        
        // Dosya tiplerini kontrol et (sadece görsel dosyalarını kabul et)
        Array.from(files).forEach(file => {
            if (file.type.startsWith('image/')) {
                uploadImage(file, dropEvent);
            } else {
                showNotification('Yalnızca görsel dosyaları kabul edilir.', 'error');
            }
        });
    }
    
    /**
     * Görsel yükle
     * @param {File} imageFile Görsel dosyası
     * @param {Event} dropEvent Sürükle-bırak olayı
     */
    function uploadImage(imageFile, dropEvent) {
        // Form verisi oluştur
        const formData = new FormData();
        formData.append('files[]', imageFile);
        
        // Upload URL'sini belirle (yapılandırmadan veya varsayılan olarak)
        const uploadUrl = config.uploadUrl || '/admin/studio/api/assets/upload';
        
        // CSRF token'ı al
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        
        // Gönder
        fetch(uploadUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Yükleme başarılıysa görsel bileşenini ekle
                addImageComponent(data.data[0].src, dropEvent);
                showNotification('Görsel başarıyla yüklendi.', 'success');
            } else {
                showNotification('Görsel yüklenemedi: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Görsel yükleme hatası:', error);
            showNotification('Görsel yüklenirken bir hata oluştu.', 'error');
        });
    }
    
    /**
     * Görsel bileşeni ekle
     * @param {string} src Görsel URL'si
     * @param {Event} dropEvent Sürükle-bırak olayı
     */
    function addImageComponent(src, dropEvent) {
        // Görsel bileşeni ekle
        const imgComponent = {
            type: 'media-image',
            attributes: { 
                class: 'img-fluid', 
                src: src,
                alt: 'Yüklenen görsel'
            }
        };
        
        // Bileşeni editöre ekle
        const component = editor.DomComponents.addComponent(imgComponent);
        safelyAddComponent(component);
    }
    
    /**
     * Bileşen seçimi işlevselliğini ayarla
     */
    function setupComponentSelection() {
        // Seçili bileşeni takip et
        editor.on('component:selected', model => {
            if (!model) return;
            
            try {
                // Bileşen seçimi olayını tetikle
                const event = new CustomEvent('studio:component-selected', { 
                    detail: { 
                        model: model
                    } 
                });
                document.dispatchEvent(event);
                
                // Özellikler panelini güncelle (setTimeout ile hatayı önle)
                setTimeout(() => {
                    try {
                        updateTraitPanel(model);
                    } catch (e) {
                        console.error('Özellikler paneli güncellenirken hata:', e);
                    }
                }, 50);
            } catch (e) {
                console.warn('Bileşen seçim olayı işlenirken hata:', e);
            }
        });
        
        // Bileşen deselect edildiğinde
        editor.on('component:deselected', () => {
            // Bileşen deselect olayını tetikle
            const event = new CustomEvent('studio:component-deselected');
            document.dispatchEvent(event);
        });
        
        console.log('Bileşen seçimi işlevselliği ayarlandı');
    }
    
    /**
     * Özellikler panelini güncelle
     * @param {Object} model Seçilen bileşen modeli
     */
    function updateTraitPanel(model) {
        // Tab'ı otomatik olarak açma (isteğe bağlı)
        const traitsTab = document.querySelector('.panel-tab[data-tab="traits"]');
        if (traitsTab && !traitsTab.classList.contains('active')) {
            // İsteğe bağlı: otomatik tab geçişi
            // traitsTab.click();
        }
        
        // TraitManager'ı güvenli şekilde kullan
        if (editor.TraitManager && typeof editor.TraitManager.setTarget === 'function') {
            editor.TraitManager.setTarget(model);
        }
    }
    
    /**
     * Bildirim göster
     * @param {string} message Mesaj
     * @param {string} type Tip (success, error, warning, info)
     */
    function showNotification(message, type = 'info') {
        if (typeof StudioUI !== 'undefined' && StudioUI.showNotification) {
            StudioUI.showNotification(message, type);
        } else {
            console.log(message);
        }
    }
    
    // Dışa aktarılan fonksiyonlar
    return {
        setupCanvas: setupCanvas,
        handleDropEvents: handleDropEvents,
        setupComponentSelection: setupComponentSelection
    };
})();

// Global olarak kullanılabilir yap
window.StudioCanvasManager = StudioCanvasManager;