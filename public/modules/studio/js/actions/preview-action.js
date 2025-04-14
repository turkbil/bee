/**
 * Studio Preview Action
 * Önizleme işlemlerini yöneten modül
 */
const StudioPreviewAction = (function() {
    let editor = null;
    let config = {};
    let previewButton = null;
    let previewWindow = null;

    /**
     * Önizleme eylemlerini ayarla
     * @param {Object} editorInstance GrapesJS editor örneği
     * @param {Object} options Yapılandırma seçenekleri
     */
    function init(editorInstance, options = {}) {
        editor = editorInstance;
        config = {
            previewButtonId: 'preview-btn',
            newWindow: true,
            windowName: 'studio-preview',
            windowFeatures: 'width=1200,height=800,resizable=yes,scrollbars=yes',
            ...options
        };

        // Önizleme butonunu ayarla
        setupPreviewButton();

        // Komut ekle
        editor.Commands.add('preview-content', {
            run: () => previewContent()
        });

        console.log('Preview Action başlatıldı');
    }

    /**
     * Önizleme butonunu ayarla
     */
    function setupPreviewButton() {
        previewButton = document.getElementById(config.previewButtonId);
        
        if (previewButton) {
            previewButton.addEventListener('click', function(e) {
                e.preventDefault();
                previewContent();
            });
            
            console.log('Önizleme butonu hazırlandı');
        } else {
            console.warn('Önizleme butonu bulunamadı:', config.previewButtonId);
        }
    }

    /**
     * İçeriği önizle
     */
    function previewContent() {
        // HTML önizlemesi oluştur
        const previewHTML = generatePreviewHTML();
        
        // Önizleme penceresine gönder
        if (config.newWindow) {
            // Yeni pencerede göster
            if (previewWindow && !previewWindow.closed) {
                previewWindow.close();
            }
            
            previewWindow = window.open('', config.windowName, config.windowFeatures);
            previewWindow.document.open();
            previewWindow.document.write(previewHTML);
            previewWindow.document.close();
            
            // Pencere kapandığında referansı temizle
            previewWindow.onbeforeunload = function() {
                previewWindow = null;
            };
        } else {
            // Modal içinde göster
            showPreviewModal(previewHTML);
        }
    }
    
    /**
     * Önizleme için HTML oluştur
     * @returns {string} Önizleme HTML'i
     */
    function generatePreviewHTML() {
        // Editor içeriğini al
        const htmlContent = editor.getHtml();
        const cssContent = editor.getCss();
        const jsContent = document.getElementById('js-content')?.value || '';
        
        // Modül bilgilerini al
        const studioConfig = Studio.getConfig();
        const pageTitle = document.title || 'Sayfa Önizleme';
        
        // Tema ve şablon bilgilerini al (varsa)
        let themeSettings = {};
        if (typeof StudioThemeManager !== 'undefined') {
            themeSettings = StudioThemeManager.getActiveThemeSettings();
        }
        
        // Bootstrap ve Font Awesome gibi CSS çerçevelerini ekle
        return `<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>${pageTitle} - Önizleme</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Önizleme Stilleri */
        body {
            padding-top: 56px;
        }
        .studio-preview-toolbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background-color: #343a40;
            color: white;
            padding: 10px 15px;
            z-index: 9999;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .studio-preview-close {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
        }
        .studio-preview-size {
            display: flex;
            gap: 10px;
        }
        .studio-preview-size button {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
        }
        .studio-preview-size button.active {
            background-color: #007bff;
        }
        
        /* Kullanıcı CSS */
        ${cssContent}
    </style>
</head>
<body>
    <!-- Önizleme Araç Çubuğu -->
    <div class="studio-preview-toolbar">
        <div class="studio-preview-title">
            ${pageTitle} - Önizleme
        </div>
        <div class="studio-preview-size">
            <button class="size-desktop active" onclick="setPreviewSize('desktop')">
                <i class="fas fa-desktop"></i> Masaüstü
            </button>
            <button class="size-tablet" onclick="setPreviewSize('tablet')">
                <i class="fas fa-tablet-alt"></i> Tablet
            </button>
            <button class="size-mobile" onclick="setPreviewSize('mobile')">
                <i class="fas fa-mobile-alt"></i> Mobil
            </button>
        </div>
        <button class="studio-preview-close" onclick="window.close()">
            <i class="fas fa-times"></i> Kapat
        </button>
    </div>
    
    <!-- Önizleme İçeriği -->
    <div id="studio-preview-content">
        ${htmlContent}
    </div>
    
    <!-- Script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Cihaz boyutları
        function setPreviewSize(device) {
            const contentElement = document.getElementById('studio-preview-content');
            const buttons = document.querySelectorAll('.studio-preview-size button');
            
            // Tüm butonlardan active sınıfını kaldır
            buttons.forEach(button => button.classList.remove('active'));
            
            // Seçili butona active sınıfı ekle
            document.querySelector('.size-' + device).classList.add('active');
            
            // İçerik div'inin genişliğini ayarla
            switch (device) {
                case 'desktop':
                    contentElement.style.maxWidth = '100%';
                    contentElement.style.margin = '0';
                    break;
                case 'tablet':
                    contentElement.style.maxWidth = '768px';
                    contentElement.style.margin = '0 auto';
                    break;
                case 'mobile':
                    contentElement.style.maxWidth = '375px';
                    contentElement.style.margin = '0 auto';
                    break;
            }
        }
        
        // Kullanıcı JavaScript
        ${jsContent}
    </script>
</body>
</html>`;
    }
    
    /**
     * Önizleme modalını göster
     * @param {string} content Önizleme içeriği
     */
    function showPreviewModal(content) {
        // Modal ID'si
        const modalId = 'studio-preview-modal';
        
        // Mevcut modalı kaldır
        const existingModal = document.getElementById(modalId);
        if (existingModal) {
            existingModal.remove();
        }
        
        // Modal HTML yapısı
        const modalHTML = `
        <div class="modal fade" id="${modalId}" tabindex="-1" aria-labelledby="${modalId}-label" aria-hidden="true">
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="${modalId}-label">Sayfa Önizleme</h5>
                        <div class="btn-group ms-3" role="group">
                            <button type="button" class="btn btn-sm btn-outline-secondary active" data-device="desktop">
                                <i class="fas fa-desktop"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-device="tablet">
                                <i class="fas fa-tablet-alt"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-device="mobile">
                                <i class="fas fa-mobile-alt"></i>
                            </button>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                    </div>
                    <div class="modal-body p-0">
                        <iframe id="${modalId}-iframe" style="width: 100%; height: 100%; border: none;"></iframe>
                    </div>
                </div>
            </div>
        </div>
        `;
        
        // Modal'ı body'ye ekle
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        
        // Modal örneğini al
        const modalElement = document.getElementById(modalId);
        const modal = new bootstrap.Modal(modalElement, {
            backdrop: 'static'
        });
        
        // Modal'ı göster
        modal.show();
        
        // iFrame'e içeriği yükle
        const iframe = document.getElementById(`${modalId}-iframe`);
        iframe.onload = function() {
            // iFrame içeriğini ekranda göster
            iframe.style.opacity = '1';
        };
        
        // iFrame içeriğini ayarla
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        iframeDoc.open();
        iframeDoc.write(content);
        iframeDoc.close();
        
        // Cihaz butonları
        const deviceButtons = modalElement.querySelectorAll('[data-device]');
        deviceButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Tüm butonlardan active sınıfını kaldır
                deviceButtons.forEach(btn => btn.classList.remove('active'));
                
                // Bu butona active sınıfı ekle
                this.classList.add('active');
                
                // iFrame'i cihaz boyutuna göre ayarla
                const device = this.getAttribute('data-device');
                
                switch (device) {
                    case 'desktop':
                        iframe.style.maxWidth = '100%';
                        iframe.style.margin = '0 auto';
                        break;
                    case 'tablet':
                        iframe.style.maxWidth = '768px';
                        iframe.style.margin = '0 auto';
                        break;
                    case 'mobile':
                        iframe.style.maxWidth = '375px';
                        iframe.style.margin = '0 auto';
                        break;
                }
            });
        });
    }
    
    // Dışa aktarılan fonksiyonlar
    return {
        init: init,
        previewContent: previewContent
    };
})();

// Global olarak kullanılabilir yap
window.StudioPreviewAction = StudioPreviewAction;