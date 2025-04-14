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
    // GrapesJS 'component:add' eventine log ekle (editör hazırken)
    if (editor && editor.on) {
        editor.on('component:add', (model, collection, opts) => {
            console.log('[STUDIO DEBUG] [GrapesJS] component:add event TETİKLENDİ!', model, collection, opts);
        });
        editor.on('block:drag:start', block => {
            console.log('[STUDIO DEBUG] block:drag:start', block && block.get && block.get('id'), block);
        });
        editor.on('block:drag:stop', block => {
            console.log('[STUDIO DEBUG] block:drag:stop', block && block.get && block.get('id'), block);
        });
    }
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
        
    function handleDropEvents() {
        const canvas = editor.Canvas;
        const frame = canvas.getFrame();
        
        if (!frame) {
            console.warn('Canvas frame bulunamadı');
            return;
        }
        
        const canvasEl = frame.view.el.contentDocument.body;
        
        if (!canvasEl) {
            console.warn('Canvas elemanı bulunamadı');
            return;
        }

        // Aynı frame'e birden fazla event listener eklenmesini engelle
        if (canvasEl.__studioDropListenerAdded) {
            return;
        }
        canvasEl.__studioDropListenerAdded = true;

        // Sürükleme işlemini kilitleyen global değişken
        let dropLock = false;
        
        canvasEl.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (!dropLock) {
                this.classList.add('gjs-droppable-active');
            }
        });
        
        canvasEl.addEventListener('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.remove('gjs-droppable-active');
        });
        
        console.log('[STUDIO DEBUG] Drop event listener canvasEl üzerine EKLENDİ!', canvasEl);
// canvasEl parent'larını da logla
let parent = canvasEl.parentNode;
let lvl = 0;
while (parent && lvl < 5) {
    console.log(`[STUDIO DEBUG] canvasEl parent [${lvl}]:`, parent);
    parent = parent.parentNode;
    lvl++;
}
canvasEl.addEventListener('drop', function(e) {
    console.log('[STUDIO DEBUG] Drop event TETİKLENDİ!');
            // Eğer kilit varsa hiçbir şey yapma
            if (dropLock) {
                return;
            }
    
            // Kilidi aktifleştir
            dropLock = true;
    
            e.preventDefault();
            e.stopPropagation();
            this.classList.remove('gjs-droppable-active');
    
            const blockId = e.dataTransfer.getData('blockId');
            if (!blockId) {
                dropLock = false;
                return;
            }
    
            const block = editor.BlockManager.get(blockId);
            if (!block) {
                dropLock = false;
                return;
            }
    
            // İçeriği ekle
            let content = block.get('content');
console.log('[STUDIO DEBUG] Drop Event: blockId =', blockId, '| block:', block);
console.log('[STUDIO DEBUG] Orijinal content:', content, '| Tip:', typeof content, '| Array mi:', Array.isArray(content));

// Eğer content bir array veya birden fazla root ise sadece ilkini ekle
if (Array.isArray(content)) {
    console.log('[STUDIO DEBUG] Content bir array, ilk eleman:', content[0]);
    content = content[0];
}
if (typeof content === 'string') {
    // Birden fazla root varsa sadece ilk root'u al
    const match = content.match(/<([\s\S]*?)>([\s\S]*?)<\/[a-zA-Z0-9]+>/);
    if (match) {
        console.log('[STUDIO DEBUG] Content bir string ve birden fazla root içeriyor, ilk root:', match[0]);
        content = match[0];
    }
}
console.log('[STUDIO DEBUG] addComponents fonksiyonuna gönderilen content:', content);
const components = editor.addComponents(content);
console.log('[STUDIO DEBUG] addComponents sonucu, eklenen component(ler):', components, '| Kaç tane:', components.length);
if (components.length > 0) {
    components.forEach((cmp, i) => {
        console.log(`[STUDIO DEBUG] [${i}] Eklenen component detayları:`, cmp, '| type:', cmp.get && cmp.get('type'));
    });
}
const component = components[0];

            // Eklenen bileşeni seç
            if (component) {
                editor.select(component);
            }
    
            // 500ms sonra kilidi kaldır
            setTimeout(() => {
                dropLock = false;
            }, 500);
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

// Drop event listener eklenirken, canvasEl ve parent'larını logla
function handleDropEvents() {
    canvasEl.addEventListener('drop', handleDrop);
    console.log('Drop event listener eklendi:', canvasEl, canvasEl.parentNode);
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