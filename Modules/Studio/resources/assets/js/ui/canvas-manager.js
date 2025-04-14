/**
 * Studio Canvas Manager
 * Çalışma alanı yapılandırmasını yöneten modül
 */
const StudioCanvasManager = (function() {
    let editor = null;
    let config = {};
    let canvasInitialized = false;
    
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
        
        // Canvas hazır olduğunda sürükle-bırak olaylarını başlat
        editor.on('canvas:ready', () => {
            console.log('Canvas hazır, sürükle-bırak olayları ayarlanıyor...');
            handleDropEvents();
        });
        
        // Editör yüklendikten sonra manuel olarak da deneyelim
        setTimeout(() => {
            if (!canvasInitialized) {
                handleDropEvents();
            }
        }, 1500);
        
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
        console.log('Sürükle-bırak olayları ayarlanıyor...');
        
        // Canvas elemanını al
        const canvas = editor.Canvas;
        const frame = canvas.getFrame();
        
        if (!frame) {
            console.warn('Canvas frame bulunamadı');
            return;
        }
        
        // Canvas iframe'inin yüklenmesini bekle
        const setupDropEvents = () => {
            try {
                // Canvas document ve body'ye eriş
                const canvasDoc = frame.view.getEl().contentDocument || frame.view.el.contentDocument;
                const canvasEl = canvasDoc.body;
                
                if (!canvasEl) {
                    console.warn('Canvas body elemanı bulunamadı');
                    return;
                }
                
                console.log('Canvas elementi bulundu, drop olayları ayarlanıyor...');
                
                // Önceki olay dinleyicilerini kaldır
                const newCanvasEl = canvasEl.cloneNode(true);
                canvasEl.parentNode.replaceChild(newCanvasEl, canvasEl);
                
                // İşlem durumunu takip et
                let isProcessing = false;
                
                // Dragover olayı
                newCanvasEl.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    this.classList.add('gjs-droppable-active');
                });
                
                // Dragleave olayı
                newCanvasEl.addEventListener('dragleave', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    this.classList.remove('gjs-droppable-active');
                });
                
                // Drop olayı - en önemli kısım
                newCanvasEl.addEventListener('drop', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    this.classList.remove('gjs-droppable-active');
                    
                    // İşlem devam ediyorsa çık
                    if (isProcessing) return;
                    isProcessing = true;
                    
                    console.log('Canvas drop olayı tetiklendi');
                    
                    // Blok sürüklenme kontrolü
                    if (e.dataTransfer.getData('blockId')) {
                        const blockId = e.dataTransfer.getData('blockId');
                        console.log('Blok düşürüldü:', blockId);
                        
                        // Blok yöneticisinden blok bilgisini al
                        const block = editor.BlockManager.get(blockId);
                        
                        if (block) {
                            // Blok içeriğini al
                            const content = block.get('content');
                            console.log('Blok içeriği:', content);
                            
                            // Fare pozisyonunu al
                            const pos = {
                                x: e.clientX,
                                y: e.clientY
                            };
                            
                            try {
                                // Mevcut seçimleri temizle
                                editor.select(null);
                                
                                // İçerik türüne göre ekle
                                let component;
                                
                                if (typeof content === 'string') {
                                    // HTML içeriği doğrudan ekle
                                    component = editor.addComponents(content);
                                    console.log('HTML içerik eklendi:', component);
                                } else if (typeof content === 'object') {
                                    // Nesne içeriği için
                                    if (Array.isArray(content)) {
                                        // Dizi ise her bir öğeyi ekle
                                        component = editor.addComponents(content);
                                    } else {
                                        // Tek bir nesne ise
                                        component = editor.DomComponents.addComponent(content);
                                    }
                                    console.log('Nesne içerik eklendi:', component);
                                }
                                
                                // Eklenen bileşeni seç ve görünür yap
                                setTimeout(() => {
                                    try {
                                        // Son eklenen bileşeni bul ve seç
                                        const components = editor.DomComponents.getComponents();
                                        if (components.length > 0) {
                                            const lastComponent = components.at(components.length - 1);
                                            editor.select(lastComponent);
                                            
                                            // Bileşeni görünür yap
                                            editor.Commands.run('core:component-highlight', {
                                                component: lastComponent
                                            });
                                            
                                            console.log('Son eklenen bileşen seçildi:', lastComponent);
                                        }
                                        
                                        // Editörü yenile
                                        editor.refresh();
                                    } catch (err) {
                                        console.error('Bileşen seçilirken hata:', err);
                                    }
                                }, 100);
                            } catch (error) {
                                console.error('Blok eklenirken hata:', error);
                            }
                        }
                    }
                    
                    // İşlemi tamamla
                    setTimeout(() => {
                        isProcessing = false;
                    }, 300);
                });
                
                console.log('Sürükle-bırak olayları ayarlandı');
            } catch (error) {
                console.error('Drop olayları ayarlanırken hata:', error);
            }
        };
        
        // Canvas hazır olduğunda olayları ayarla
        editor.on('canvas:ready', () => {
            setupDropEvents();
        });
        
        // Hemen de deneyelim
        setupDropEvents();
        
        // Gecikmeli olarak bir kez daha deneyelim
        setTimeout(setupDropEvents, 1000);
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
    
    // Diğer kodlar aynı...
    
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
        // Tab'ı otomatik olarak açma (isteğe bağlı)
        const traitsTab = document.querySelector('.panel-tab[data-tab="traits"]');
        if (traitsTab && !traitsTab.classList.contains('active')) {
            // traitsTab.click();
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