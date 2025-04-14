/**
 * Studio Canvas Manager
 * Çalışma alanı yapılandırmasını yöneten modül
 */
const StudioCanvasManager = (function() {
    let editor = null;
    let config = {};
    let isSelecting = false; // Seçim işlemi takibi için bayrak
    
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
        
        // Sürükle-bırak olaylarını yönet
        handleDropEvents();
        
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
        
        // Frame'i bul
        const frame = canvas.getFrame();
        if (!frame) {
            console.warn('Canvas frame bulunamadı, sürükle-bırak olayları ayarlanıyor...');
            
            // Frame yoksa, yüklendiğinde tekrar dene
            editor.on('canvas:frame:load', () => {
                console.log('Canvas frame bulundu, sürükle-bırak işlemleri etkinleştiriliyor...');
                setupFrameDrop(canvas.getFrame());
            });
            return;
        }
        
        console.log('Canvas frame bulundu, sürükle-bırak olayları ayarlanıyor...');
        setupFrameDrop(frame);
    }

    /**
     * Frame için sürükle-bırak olaylarını ayarla
     * @param {Object} frame Canvas frame
     */
    function setupFrameDrop(frame) {
        if (!frame || !frame.view || !frame.view.el || !frame.view.el.contentDocument) {
            console.error('Geçerli frame bulunamadı');
            return;
        }
        
        const canvasBody = frame.view.el.contentDocument.body;
        
        if (!canvasBody) {
            console.error('Canvas body bulunamadı');
            return;
        }
        
        // Önceki olay dinleyicileri temizle
        canvasBody.removeEventListener('dragover', handleDragOver);
        canvasBody.removeEventListener('dragleave', handleDragLeave);
        canvasBody.removeEventListener('drop', handleDrop);
        
        // Yeni olay dinleyicileri ekle
        canvasBody.addEventListener('dragover', handleDragOver);
        canvasBody.addEventListener('dragleave', handleDragLeave);
        canvasBody.addEventListener('drop', handleDrop);
        
        console.log('Canvas sürükle-bırak işlemleri başarıyla etkinleştirildi');
    }

    /**
     * Sürükleme üzerinde olay işleyicisi
     * @param {Event} e Sürükleme olayı
     */
    function handleDragOver(e) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.add('gjs-droppable-active');
    }

    /**
     * Sürükleme çıkışı olay işleyicisi
     * @param {Event} e Sürükleme olayı
     */
    function handleDragLeave(e) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.remove('gjs-droppable-active');
    }

    /**
     * Bırakma olay işleyicisi
     * @param {Event} e Bırakma olayı
     */
    function handleDrop(e) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.remove('gjs-droppable-active');
        
        try {
            // Blok sürüklenme kontrolü
            if (e.dataTransfer.getData('blockId')) {
                const blockId = e.dataTransfer.getData('blockId');
                const block = editor.BlockManager.get(blockId);
                
                if (block) {
                    // Hata yakalama ile güvenli ekleme
                    safelyAddComponent(block);
                }
            }
            // Dosya sürüklenme kontrolü
            else if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length) {
                const files = e.dataTransfer.files;
                handleFilesDrop(files, e);
            }
        } catch (error) {
            console.error('Sürükle-bırak işlemi sırasında hata:', error);
            showNotification('Bileşen eklenirken bir hata oluştu.', 'error');
        }
    }
    
    /**
     * Bileşeni güvenli bir şekilde ekle
     * @param {Object} block Eklenecek blok
     */
    function safelyAddComponent(block) {
        try {
            const content = block.get('content');
            let component = null;
            
            // İçerik tipine göre bileşeni ekle
            if (typeof content === 'string') {
                // HTML string ise
                component = editor.addComponents(content);
                if (Array.isArray(component) && component.length > 0) {
                    component = component[0];
                }
            } else if (typeof content === 'object') {
                // Nesne tipinde ise
                component = editor.DomComponents.addComponent(content);
            }
            
            // Bileşen başarıyla eklendiyse ve henüz bir seçim işlemi yoksa
            if (component && !isSelecting) {
                // Seçim bayrağını ayarla
                isSelecting = true;
                
                // 300ms gecikme ile seçimi yap (döngüyü kırmak için)
                setTimeout(() => {
                    try {
                        // Seçimi yap
                        editor.select(component);
                    } catch (err) {
                        console.error('Bileşen seçilirken hata:', err);
                    } finally {
                        // İşlem tamamlandı, bayrağı sıfırla
                        isSelecting = false;
                    }
                }, 300);
            }
        } catch (error) {
            console.error('Bileşen eklenirken hata:', error);
            isSelecting = false; // Hata durumunda bayrağı sıfırla
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
        try {
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
            
            // Seçim bayrağını kontrol et
            if (!isSelecting) {
                isSelecting = true;
                
                // Bileşeni seç (gecikmeli)
                setTimeout(() => {
                    try {
                        editor.select(component);
                    } catch (err) {
                        console.error('Görsel bileşeni seçilirken hata:', err);
                    } finally {
                        isSelecting = false;
                    }
                }, 300);
            }
        } catch (error) {
            console.error('Görsel bileşeni eklenirken hata:', error);
            isSelecting = false;
        }
    }
    
    /**
     * Bileşen seçimi işlevselliğini ayarla
     */
    function setupComponentSelection() {
        // Seçili bileşeni takip et
        editor.on('component:selected', model => {
            if (!model || isSelecting) return;
            
            try {
                // Bayrak ayarla
                isSelecting = true;
                
                // Özellikler panelini güncelle (gecikmeli çalıştır)
                setTimeout(() => {
                    try {
                        // Özellikler panelini güncelle
                        updateTraitPanel(model);
                        
                        // Bileşen seçimi olayını tetikle
                        const event = new CustomEvent('studio:component-selected', { 
                            detail: { model: model } 
                        });
                        document.dispatchEvent(event);
                    } catch (err) {
                        console.error('Özellikler paneli güncellenirken hata:', err);
                    } finally {
                        // İşlem tamamlandı, bayrağı sıfırla
                        isSelecting = false;
                    }
                }, 200);
            } catch (error) {
                console.error('Bileşen seçimi sırasında hata:', error);
                isSelecting = false;
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
        // Özellik paneli mevcut mu kontrol et
        const traitsContainer = document.getElementById('traits-container');
        if (!traitsContainer) return;
        
        // Paneli güncelle
        editor.TraitManager.setTarget(model);
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