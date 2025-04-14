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
        
        if (!editor) {
            console.error('Editor örneği geçersiz veya tanımsız');
            return;
        }
        
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
        
        try {
            // İçeriği yükle
            loadCanvasContent();
            
            // Canvas hazır olduğunda olayları bağla
            editor.on('canvas:load', function() {
                // Sürükle-bırak olaylarını yönet
                handleDropEvents();
            });
            
            // Bileşen seçimini ayarla
            setupComponentSelection();
            
            console.log('Canvas varlıkları eklendi');
        } catch (error) {
            console.error('Canvas yöneticisi kurulurken hata:', error);
        }
    }
    
    /**
     * Canvas stil ve script varlıklarını ekle
     */
    function setupCanvasAssets() {
        if (!editor || !editor.Canvas) return;
        
        try {
            // Stilleri ekle
            if (config.canvasStyles && config.canvasStyles.length) {
                editor.Canvas.getConfig().styles = config.canvasStyles;
            }
            
            // Scriptleri ekle
            if (config.canvasScripts && config.canvasScripts.length) {
                editor.Canvas.getConfig().scripts = config.canvasScripts;
            }
        } catch (error) {
            console.error('Canvas varlıkları eklenirken hata:', error);
        }
    }
    
    /**
     * Canvas içeriğini yükle
     */
    function loadCanvasContent() {
        if (!editor) return;
        
        try {
            // HTML içeriğini al
            const htmlContentEl = document.getElementById('html-content');
            
            if (htmlContentEl && htmlContentEl.value && htmlContentEl.value.trim() !== '') {
                // HTML içeriğini ayarla
                setTimeout(() => {
                    editor.setComponents(htmlContentEl.value);
                    
                    // CSS içeriğini ayarla
                    const cssContentEl = document.getElementById('css-content');
                    if (cssContentEl && cssContentEl.value) {
                        editor.setStyle(cssContentEl.value);
                    }
                    
                    console.log('Canvas içeriği başarıyla yüklendi');
                }, 500);
            } else {
                console.warn('HTML içeriği bulunamadı veya boş');
            }
        } catch (error) {
            console.error('Canvas içeriği yüklenirken hata:', error);
        }
    }
    
    /**
     * Sürükle-bırak olaylarını yönet
     */
    function handleDropEvents() {
        if (!editor || !editor.Canvas) {
            console.warn('Editor veya Canvas tanımlı değil, drop olayları ayarlanamıyor');
            return;
        }
        
        try {
            console.log('Sürükle-bırak olayları ayarlanıyor...');
            
            // Canvas iframe'inin yüklenmesini bekle
            const frameEl = editor.Canvas.getFrame();
            
            if (!frameEl) {
                console.warn('Canvas frame bulunamadı');
                return;
            }
            
            // Frame hazırsa doğrudan, değilse load olayında olayları ayarla
            if (frameEl.view && frameEl.view.el && frameEl.view.el.contentDocument) {
                const canvasBody = frameEl.view.el.contentDocument.body;
                if (canvasBody) {
                    console.log('Canvas elementi bulundu, drop olayları ayarlanıyor...');
                    setupDropEvents(canvasBody);
                } else {
                    console.warn('Canvas body bulunamadı');
                }
            } else {
                // Frame yüklenmesini dinle
                frameEl.view && frameEl.view.el && frameEl.view.el.addEventListener('load', function() {
                    try {
                        const canvasBody = this.contentDocument.body;
                        if (canvasBody) {
                            console.log('Canvas iframe yüklendi, drop olayları ayarlanıyor...');
                            setupDropEvents(canvasBody);
                        }
                    } catch (e) {
                        console.error('Frame load olayında hata:', e);
                    }
                });
            }
        } catch (error) {
            console.error('Drop olayları ayarlanırken hata:', error);
        }
    }
    
    /**
     * Belirli bir elemana sürükle-bırak olaylarını ekle
     * @param {HTMLElement} element Olayların ekleneceği eleman
     */
    function setupDropEvents(element) {
        if (!element) {
            console.warn('Drop olayları için eleman belirtilmedi');
            return;
        }
        
        try {
            // Dosya yükleme ve blok sürükleme olay yöneticisi
            element.addEventListener('dragover', function(e) {
                e.preventDefault();
                e.stopPropagation();
                this.classList.add('gjs-droppable-active');
            });
            
            element.addEventListener('dragleave', function(e) {
                e.preventDefault();
                e.stopPropagation();
                this.classList.remove('gjs-droppable-active');
            });
            
            element.addEventListener('drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                this.classList.remove('gjs-droppable-active');
                
                console.log('Canvas drop olayı tetiklendi');
                
                // Blok sürüklenme kontrolü
                if (e.dataTransfer.getData('blockId')) {
                    const blockId = e.dataTransfer.getData('blockId');
                    console.log('Blok düşürüldü:', blockId);
                    
                    const block = editor.BlockManager.get(blockId);
                    
                    if (block) {
                        const content = block.get('content');
                        console.log('Blok içeriği:', content);
                        
                        // Blok içeriğini canvas'a ekle
                        let comp;
                        if (typeof content === 'string') {
                            comp = editor.addComponents(content);
                        } else if (typeof content === 'object') {
                            comp = editor.addComponents(editor.DomComponents.addComponent(content));
                        }
                        
                        console.log('Nesne içerik eklendi:', comp);
                        
                        // Bileşeni seç ve görünür yap
                        setTimeout(() => {
                            try {
                                if (comp && comp.length) {
                                    editor.select(comp[0]);
                                    console.log('Son eklenen bileşen seçildi:', comp[0]);
                                } else if (comp) {
                                    editor.select(comp);
                                    console.log('Son eklenen bileşen seçildi:', comp);
                                }
                            } catch (error) {
                                console.error('Bileşen seçilirken hata:', error);
                            }
                        }, 100);
                    }
                }
                // Dosya sürüklenme kontrolü
                else if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length) {
                    const files = e.dataTransfer.files;
                    handleFilesDrop(files, e);
                }
            });
            
            console.log('Sürükle-bırak olayları ayarlandı');
        } catch (error) {
            console.error('Drop olayları ayarlanırken detaylı hata:', error);
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
        if (!editor) return;
        
        try {
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
        } catch (error) {
            console.error('Bileşen seçimi işlevselliği ayarlanırken hata:', error);
        }
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
        setupComponentSelection: setupComponentSelection,
        loadCanvasContent: loadCanvasContent
    };
})();

// Global olarak kullanılabilir yap
window.StudioCanvasManager = StudioCanvasManager;