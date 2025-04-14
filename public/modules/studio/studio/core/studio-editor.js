/**
 * Studio Editor - Çekirdek Modülü
 * GrapesJS editörü başlatma ve temel işlevler
 */
window.StudioEditor = (function() {
    let editor = null;
    
    /**
     * GrapesJS editörünü başlatır
     * @param {Object} config - Yapılandırma seçenekleri
     * @returns {Object} - GrapesJS editör örneği
     */
    function init(config = {}) {
        // Gerekli DOM elementleri kontrol et
        const container = document.getElementById(config.containerId || 'gjs');
        if (!container) {
            console.error('Editor container bulunamadı:', config.containerId || 'gjs');
            return null;
        }
        
        // HTML, CSS, JS içeriklerini al
        const htmlContent = getInitialContent('html-content', config.content);
        const cssContent = getInitialContent('css-content', config.css);
        const jsContent = getInitialContent('js-content', config.js);
        
        // Editor için loader göster
        showLoader();
        
        try {
            // Varsayılan GrapesJS ayarlarıyla özel ayarları birleştir
            const editorConfig = window.StudioConfig.getEditorConfig({
                container: '#' + (config.containerId || 'gjs'),
                fromElement: false,
                height: '100%',
                width: 'auto',
                // HTML içeriği yükle
                components: htmlContent,
                // CSS içeriği yükle
                style: cssContent,
                // Diğer ayarlar
                ...config.editorOptions
            });
            
            console.log('GrapesJS editör başlatılıyor...', editorConfig);
            
            // GrapesJS editörünü oluştur
            editor = grapesjs.init(editorConfig);
            
            // Editor yüklendikten sonra
            editor.on('load', () => {
                console.log('GrapesJS editör başarıyla yüklendi.');
                
                // Yükleme göstergesini gizle
                hideLoader();
                
                // JS içeriği varsa, gizli alana yerleştir
                if (jsContent) {
                    const jsContentEl = document.getElementById('js-content');
                    if (jsContentEl) {
                        jsContentEl.value = jsContent;
                    }
                }
                
                // Editor yüklendi olayını tetikle
                window.StudioEvents.trigger('editor:loaded', editor);
            });
            
            // Editor değişiklik olayları
            setupChangeEvents(editor);
            
            return editor;
        } catch (error) {
            console.error('GrapesJS başlatma hatası:', error);
            hideLoader();
            showError('Editör başlatılırken bir hata oluştu. Lütfen sayfayı yenileyin veya yöneticinizle iletişime geçin.');
            return null;
        }
    }
    
    /**
     * Editor değişiklik olaylarını ayarlar
     * @param {Object} editor - GrapesJS editör örneği
     */
    function setupChangeEvents(editor) {
        // İçerik değişikliği olayları
        editor.on('change:changesCount', () => {
            window.StudioEvents.trigger('editor:content:changed', editor);
        });
        
        // Bileşen ekleme/seçme olayları
        editor.on('component:selected', (component) => {
            window.StudioEvents.trigger('editor:component:selected', component);
        });
        
        editor.on('component:add', (component) => {
            window.StudioEvents.trigger('editor:component:added', component);
        });
        
        editor.on('component:remove', (component) => {
            window.StudioEvents.trigger('editor:component:removed', component);
        });
    }
    
    /**
     * Başlangıç içeriğini almak için yardımcı fonksiyon
     * @param {string} elementId - İçerik elementi ID'si
     * @param {string} defaultContent - Varsayılan içerik
     * @returns {string} - İçerik
     */
    function getInitialContent(elementId, defaultContent = '') {
        const element = document.getElementById(elementId);
        if (element && element.value) {
            return element.value;
        }
        return defaultContent;
    }
    
    /**
     * Yükleme göstergesi
     */
    function showLoader() {
        // Mevcut bir yükleme göstergesi varsa kaldır
        hideLoader();
        
        // Yeni yükleme göstergesi oluştur
        const loader = document.createElement('div');
        loader.id = 'studio-loader';
        loader.className = 'studio-loader';
        loader.innerHTML = `
            <div class="studio-loader-content">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Yükleniyor...</span>
                </div>
                <h4>Studio Editor Yükleniyor</h4>
                <p class="text-muted">Lütfen bekleyin...</p>
            </div>
        `;
        
        // Stil ekle
        const style = document.createElement('style');
        style.textContent = `
            .studio-loader {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(255, 255, 255, 0.8);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 10000;
                transition: opacity 0.3s ease;
            }
            .studio-loader-content {
                background-color: white;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                padding: 30px;
                text-align: center;
            }
        `;
        
        document.head.appendChild(style);
        document.body.appendChild(loader);
    }
    
    /**
     * Yükleme göstergesini gizle
     */
    function hideLoader() {
        const loader = document.getElementById('studio-loader');
        if (loader) {
            loader.style.opacity = '0';
            setTimeout(() => {
                if (loader.parentNode) {
                    loader.parentNode.removeChild(loader);
                }
            }, 300);
        }
    }
    
    /**
     * Hata mesajı göster
     * @param {string} message - Hata mesajı
     */
    function showError(message) {
        // Hata alanı oluştur
        const errorEl = document.createElement('div');
        errorEl.className = 'studio-error alert alert-danger';
        errorEl.style.margin = '20px';
        errorEl.innerHTML = `
            <div class="d-flex">
                <div>
                    <i class="fas fa-exclamation-triangle me-2"></i>
                </div>
                <div>
                    <h5>Hata Oluştu</h5>
                    <p>${message}</p>
                </div>
            </div>
        `;
        
        // Editör container'ına ekle
        const container = document.getElementById('gjs');
        if (container) {
            container.innerHTML = '';
            container.appendChild(errorEl);
        } else {
            document.body.appendChild(errorEl);
        }
    }
    
    /**
     * İçeriği kaydetmek için hazırla
     * @returns {Object} - HTML, CSS ve JS içerikleri
     */
    function prepareContentForSave() {
        if (!editor) {
            console.error('Editor başlatılmadı!');
            return { html: '', css: '', js: '' };
        }
        
        try {
            // HTML içeriğini al
            const html = editor.getHtml();
            
            // CSS içeriğini al
            const css = editor.getCss();
            
            // JS içeriğini al (gizli alandan)
            const jsContentEl = document.getElementById('js-content');
            const js = jsContentEl ? jsContentEl.value : '';
            
            return {
                html: html,
                css: css,
                js: js
            };
        } catch (error) {
            console.error('İçerik hazırlama hatası:', error);
            return { html: '', css: '', js: '' };
        }
    }
    
    /**
     * İçeriği kaydet
     * @param {string} url - Kaydetme URL'si
     * @param {Object} additionalData - Ek veri
     * @returns {Promise} - Kaydetme işlemi sonucu
     */
    function saveContent(url, additionalData = {}) {
        if (!editor) {
            return Promise.reject(new Error('Editor başlatılmadı!'));
        }
        
        // CSRF token'ı al
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!token) {
            return Promise.reject(new Error('CSRF token bulunamadı!'));
        }
        
        try {
            // İçeriği hazırla
            const content = prepareContentForSave();
            
            // Kaydedilecek verileri birleştir
            const data = {
                content: content.html,
                css: content.css,
                js: content.js,
                ...additionalData
            };
            
            // AJAX isteği gönder
            return fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Sunucu hatası: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    window.StudioEvents.trigger('editor:content:saved', data);
                    return data;
                } else {
                    throw new Error(data.message || 'Bilinmeyen hata');
                }
            });
        } catch (error) {
            return Promise.reject(error);
        }
    }
    
    /**
     * Aktif editör örneğini döndürür
     * @returns {Object|null} - GrapesJS editör örneği
     */
    function getEditor() {
        return editor;
    }
    
    return {
        init: init,
        getEditor: getEditor,
        prepareContentForSave: prepareContentForSave,
        saveContent: saveContent
    };
})();