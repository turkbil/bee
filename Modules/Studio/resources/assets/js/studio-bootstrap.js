// Modules/Studio/resources/assets/js/studio-bootstrap.js - düzeltilmiş versiyonu

/**
 * Studio Editor - Bootstrap
 * Editörü başlatan ve ana yapılandırmayı sağlayan modül
 */

window.cdn = function(path) {
    // Path'i temizle
    path = path.replace(/^\/+/, '');
    // Basit URL oluştur
    return '/storage/' + path;
};

let Studio = (function() {
    // Private değişkenler
    let editor = null;
    let isInitialized = false;
    let config = {};

    /**
     * Editörü başlat
     * @param {Object} options Yapılandırma seçenekleri
     */
    function init(options = {}) {
        if (isInitialized) {
            console.warn('Studio Editor zaten başlatılmış!');
            return editor;
        }

        console.log('Studio Editor başlatılıyor...');

        // Varsayılan yapılandırmayı oluştur
        config = {
            elementId: 'gjs',
            moduleType: 'page',
            moduleId: 0,
            content: '',
            css: '',
            js: '',
            ...options
        };

        // Gerekli parametreleri kontrol et
        if (!config.moduleId || config.moduleId <= 0) {
            console.error('Geçersiz modül ID:', config.moduleId);
            return null;
        }

        // Yükleme göstergesi ekle
        showLoader();

        try {
            // GrapesJS editörü başlat
            editor = StudioCore.initEditor(config);
            
            // GLOBAL OLARAK TANIMLA - ÖNEMLİ
            window.studioEditor = editor;
            window.editor = editor; // Uyumluluk için

            // Panel yöneticisini başlat
            if (typeof StudioPanelManager !== 'undefined') {
                StudioPanelManager.setupPanels(editor);
            }

            // Araç çubuğu yöneticisini başlat
            if (typeof StudioToolbarManager !== 'undefined') {
                StudioToolbarManager.setupToolbar(editor);
            }

            // Canvas yöneticisini başlat
            if (typeof StudioCanvasManager !== 'undefined') {
                StudioCanvasManager.setupCanvas(editor);
            }

            // Eklentileri yükle
            if (typeof StudioPlugins !== 'undefined') {
                StudioPlugins.load(editor);
            }

            // Blok sistemini yükle
            if (typeof StudioBlocks !== 'undefined') {
                StudioBlocks.registerBlocks(editor);
                
                // Manuel olarak blok panelini doldur
                setTimeout(() => {
                    StudioBlocks.renderBlocksToDOM(editor);
                    
                    // Editor hazır olduğunda olay tetikle
                    document.dispatchEvent(new CustomEvent('studio:editor-ready'));
                }, 800);
            }

            // Eylem işleyicilerini yükle
            setupEditorActions();
            
            // Başarıyla başlatıldı
            isInitialized = true;
            console.log('Studio Editor başarıyla yüklendi!');

            // Yükleme göstergesini gizle
            hideLoader();
            
            // Editör başlatıldıktan sonra komponentleri hemen düzenlenebilir hale getir
            setupComponentEditing();

            return editor;
        } catch (error) {
            console.error('Studio Editor başlatma hatası:', error);
            hideLoader();
            showError('Editör başlatılırken bir hata oluştu: ' + error.message);
            return null;
        }
    }

    /**
     * Eylem işleyicileri
     */
    function setupEditorActions() {
        // Save eylemi
        if (typeof StudioSaveAction !== 'undefined') {
            StudioSaveAction.init(editor, {
                saveButtonId: 'save-btn',
                csrfToken: document.querySelector('meta[name="csrf-token"]')?.content || '',
                saveEndpoint: '/admin/studio/save'
            });
        }
        
        // Preview eylemi
        if (typeof StudioPreviewAction !== 'undefined') {
            StudioPreviewAction.init(editor, {
                previewButtonId: 'preview-btn' 
            });
        }
        
        // Export eylemi
        if (typeof StudioExportAction !== 'undefined') {
            StudioExportAction.init(editor, {
                exportButtonId: 'export-btn'
            });
        }
    }
    
    /**
     * Bileşenlerin hemen düzenlenebilir olmasını sağla
     */
    function setupComponentEditing() {
        // Bileşen eklendiğinde otomatik olarak seç
        editor.on('component:add', (model) => {
            editor.select(model);
            
            // Traits panelini aç
            setTimeout(() => {
                const traitsTab = document.querySelector('.panel-tab[data-tab="traits"]');
                if (traitsTab && !traitsTab.classList.contains('active')) {
                    traitsTab.click();
                }
            }, 100);
        });
        
        // Editable özellikleri güçlendir
        editor.on('load', () => {
            // RTE seçeneklerini iyileştir
            editor.RichTextEditor.remove('link');
            editor.RichTextEditor.add('link', {
                icon: '<i class="fa fa-link"></i>',
                attributes: {title: 'Link ekle'},
                result: rte => rte.insertHTML('<a href="#" class="link">Bağlantı metni</a>')
            });
            
            // Double-click ile düzenlemeyi kolaylaştır
            editor.on('component:selected', component => {
                if (component.get('editable')) {
                    setTimeout(() => component.trigger('active'), 0);
                }
            });
        });
    }

    /**
     * Yükleme göstergesini göster
     */
    function showLoader() {
        // Mevcut yükleme göstergesini temizle
        const existingLoader = document.querySelector('.studio-loader');
        if (existingLoader) {
            existingLoader.remove();
        }

        // Yeni yükleme göstergesi oluştur
        const loader = document.createElement('div');
        loader.className = 'studio-loader';
        loader.style.position = 'fixed';
        loader.style.top = '0';
        loader.style.left = '0';
        loader.style.width = '100%';
        loader.style.height = '100%';
        loader.style.backgroundColor = 'rgba(255, 255, 255, 0.8)';
        loader.style.display = 'flex';
        loader.style.alignItems = 'center';
        loader.style.justifyContent = 'center';
        loader.style.zIndex = '10000';
        loader.style.transition = 'opacity 0.3s ease';

        loader.innerHTML = `
            <div class="studio-loader-content" style="text-align: center; background-color: white; padding: 30px; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                <div style="margin-bottom: 15px;">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Yükleniyor...</span>
                    </div>
                </div>
                <h3 style="margin-bottom: 10px;">Studio Editor Yükleniyor</h3>
                <p style="color: #6c757d;">Lütfen bekleyin...</p>
            </div>
        `;

        document.body.appendChild(loader);
    }

    /**
     * Yükleme göstergesini gizle
     */
    function hideLoader() {
        const loader = document.querySelector('.studio-loader');
        if (loader) {
            loader.style.opacity = '0';
            setTimeout(() => {
                if (loader && loader.parentNode) {
                    loader.parentNode.removeChild(loader);
                }
            }, 300);
        }
    }

    /**
     * Hata mesajı göster
     * @param {string} message Hata mesajı
     */
    function showError(message) {
        // Toast notifikasyonu
        if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
            const toastEl = document.createElement('div');
            toastEl.className = 'toast align-items-center text-white bg-danger border-0 position-fixed top-0 end-0 m-3';
            toastEl.setAttribute('role', 'alert');
            toastEl.setAttribute('aria-live', 'assertive');
            toastEl.setAttribute('aria-atomic', 'true');
            toastEl.style.zIndex = '10001';

            toastEl.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas fa-exclamation-circle me-2"></i> ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Kapat"></button>
                </div>
            `;

            document.body.appendChild(toastEl);
            const toast = new bootstrap.Toast(toastEl, { delay: 5000 });
            toast.show();

            // Toast kapatıldığında DOM'dan kaldır
            toastEl.addEventListener('hidden.bs.toast', () => {
                if (toastEl.parentNode) {
                    toastEl.parentNode.removeChild(toastEl);
                }
            });
        } else {
            // Fallback olarak alert göster
            alert('Hata: ' + message);
        }
    }

    // Kamu API'sini döndür
    return {
        init: init,
        getEditor: function() { return editor; },
        getConfig: function() { return {...config}; },
        isInitialized: function() { return isInitialized; }
    };
})();

// DOM yüklendiğinde, otomatik başlatmayı kontrol et
document.addEventListener('DOMContentLoaded', function() {
    // Otomatik başlatma için veri özniteliklerini kontrol et
    const editorEl = document.getElementById('gjs');
    
    if (editorEl && editorEl.dataset.autoInit === 'true') {
        // HTML'den veri alma
        const moduleType = editorEl.dataset.moduleType || 'page';
        const moduleId = parseInt(editorEl.dataset.moduleId || '0');
        
        // İçerikleri al
        const contentEl = document.getElementById('html-content');
        const cssEl = document.getElementById('css-content');
        const jsEl = document.getElementById('js-content');
        
        // Editörü başlat
        Studio.init({
            elementId: 'gjs',
            moduleType: moduleType,
            moduleId: moduleId,
            content: contentEl ? contentEl.value : '',
            css: cssEl ? cssEl.value : '',
            js: jsEl ? jsEl.value : ''
        });
    }
});

// Global olarak kullanılabilir yap
window.Studio = Studio;