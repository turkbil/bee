/**
 * Studio Editor - Bootstrap
 * Editörü başlatan ve ana yapılandırmayı sağlayan modül
 */
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

            // Eklentileri yükle
            if (typeof StudioPlugins !== 'undefined') {
                StudioPlugins.load(editor);
            }

            // Blok sistemini yükle
            if (typeof StudioBlocks !== 'undefined') {
                StudioBlocks.registerBlocks(editor);
            }

            // UI'yi yapılandır
            if (typeof StudioUI !== 'undefined') {
                StudioUI.setup(editor);
            }

            // Eylem işleyicilerini yükle
            if (typeof StudioActions !== 'undefined') {
                StudioActions.setupActions(editor, config);
            }

            // Başarıyla başlatıldı
            isInitialized = true;
            console.log('Studio Editor başarıyla yüklendi!');

            // Yükleme göstergesini gizle
            hideLoader();

            return editor;
        } catch (error) {
            console.error('Studio Editor başlatma hatası:', error);
            hideLoader();
            showError('Editör başlatılırken bir hata oluştu: ' + error.message);
            return null;
        }
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