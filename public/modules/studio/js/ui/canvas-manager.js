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
        
        // Sürükle-bırak olaylarını yönet
        setTimeout(() => {
            // Canvas framei hazır olduğunda sürükle-bırak işlevlerini kur
            handleDropEvents();
        }, 1000);
        
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
        try {
            const canvas = editor.Canvas;
            
            // Canvas elemanını al - önce frame'i sonra body'yi al
            const frame = canvas.getFrame();
            if (!frame) {
                console.warn('Canvas frame bulunamadı, yeniden deneniyor...');
                setTimeout(handleDropEvents, 500);
                return;
            }
            
            const canvasEl = frame.view.el.contentDocument.body;
            
            if (!canvasEl) {
                console.warn('Canvas body elemanı bulunamadı, yeniden deneniyor...');
                setTimeout(handleDropEvents, 500);
                return;
            }
            
            console.log('Canvas frame bulundu, sürükle-bırak olayları ayarlanıyor...');
            
            // Frame içindeki canvas'ı al
            const canvasBody = canvasEl;
            
            // Canvas'ta sürükleme olayları
            canvasBody.addEventListener('dragover', function(e) {
                e.preventDefault();
                e.stopPropagation();
                this.style.outline = '2px dashed #3b82f6';
                this.style.outlineOffset = '-2px';
            });
            
            canvasBody.addEventListener('dragleave', function(e) {
                e.preventDefault();
                e.stopPropagation();
                this.style.outline = 'none';
                this.style.outlineOffset = '0';
            });
            
            canvasBody.addEventListener('drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                this.style.outline = 'none';
                this.style.outlineOffset = '0';
                
                // Blok sürüklenme kontrolü
                const blockId = e.dataTransfer.getData('text/plain');
                if (blockId) {
                    const block = editor.BlockManager.get(blockId);
                    
                    if (block) {
                        // Blok içeriğini al
                        const content = block.get('content');
                        
                        // İçerik türüne göre editöre ekle
                        if (typeof content === 'string') {
                            const component = editor.addComponents(content)[0];
                            if (component) {
                                // Eklenen bileşeni seç (düzenleme için)
                                editor.select(component);
                            }
                        } else if (typeof content === 'object') {
                            const component = editor.addComponents(editor.DomComponents.addComponent(content))[0];
                            if (component) {
                                // Eklenen bileşeni seç (düzenleme için)
                                editor.select(component);
                            }
                        }
                    }
                }
                // Dosya sürüklenme kontrolü
                else if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length) {
                    const files = e.dataTransfer.files;
                    handleFilesDrop(files, e);
                }
            });
            
            console.log('Sürükle-bırak olayları başarıyla ayarlandı');
        } catch (error) {
            console.error('Sürükle-bırak olaylarını ayarlarken hata:', error);
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
        
        // Bileşeni seç
        editor.select(component);
    }
    
    /**
     * Bileşen seçimi işlevselliğini ayarla
     */
    function setupComponentSelection() {
        // Seçili bileşeni takip et
        editor.on('component:selected', model => {
            if (!model) return;
            
            // Özellikler panelini güncelle
            updateTraitPanel(model);
            
            // Bileşen seçimi olayını tetikle
            const event = new CustomEvent('studio:component-selected', { 
                detail: { 
                    model: model
                } 
            });
            document.dispatchEvent(event);
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
        // Tab'ı otomatik olarak açma
        const traitsTab = document.querySelector('.panel-tab[data-tab="traits"]');
        if (traitsTab && !traitsTab.classList.contains('active')) {
            // Özellikleri tab'ını otomatik olarak aktifleştir
            traitsTab.click();
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