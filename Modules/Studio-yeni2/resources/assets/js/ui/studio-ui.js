/**
 * Studio UI Manager
 * Genel UI işlevlerini ve bildirimlerini yöneten modül
 */
const StudioUI = (function() {
    let editor = null;
    
    /**
     * UI bileşenlerini ayarla
     * @param {Object} editorInstance GrapesJS editor örneği
     * @param {Object} options UI seçenekleri
     */
    function setup(editorInstance, options = {}) {
        editor = editorInstance;
        
        // Panel yöneticisini başlat
        if (typeof StudioPanelManager !== 'undefined') {
            StudioPanelManager.setupPanels(editor, options);
        }
        
        // Araç çubuğu yöneticisini başlat
        if (typeof StudioToolbarManager !== 'undefined') {
            StudioToolbarManager.setupToolbar(editor, options);
        }
        
        // Canvas yöneticisini başlat
        if (typeof StudioCanvasManager !== 'undefined') {
            StudioCanvasManager.setupCanvas(editor, options);
        }
        
        // Eylem işleyicilerini başlat
        setupActions();
        
        // Blok panelini manuel olarak doldur
        populateBlockPanel();
        
        console.log('Studio UI başarıyla ayarlandı');
    }
        
    /**
     * Blok panelini manuel olarak doldur
     */
    function populateBlockPanel() {
        setTimeout(() => {
            const blockContainer = document.getElementById('blocks-container');
            if (!blockContainer || !editor) return;
            
            // Blok yöneticisinden tüm blokları al
            const blocks = editor.BlockManager.getAll().models;
            
            if (blocks.length === 0) {
                blockContainer.innerHTML = '<div class="alert alert-warning">Henüz hiç blok yok</div>';
                return;
            }
            
            console.log('Blok paneline yerleştirilecek blok sayısı:', blocks.length);
            
            // Kategori bazlı grupla
            const categories = {};
            
            blocks.forEach(block => {
                const category = block.get('category');
                if (!categories[category]) {
                    categories[category] = [];
                }
                categories[category].push(block);
            });
            
            // HTML oluştur
            let blocksHtml = '';
            
            Object.keys(categories).forEach(categoryId => {
                const categoryBlocks = categories[categoryId];
                
                blocksHtml += `
                    <div class="category-section mb-3">
                        <h6 class="category-title mb-2">${categoryId}</h6>
                        <div class="row g-2">
                `;
                
                categoryBlocks.forEach(block => {
                    blocksHtml += `
                        <div class="col-6 mb-2">
                            <div class="block-item card p-2 text-center" data-block-id="${block.id}">
                                <i class="fas fa-cube mb-1"></i>
                                <small>${block.get('label')}</small>
                            </div>
                        </div>
                    `;
                });
                
                blocksHtml += `
                        </div>
                    </div>
                `;
            });
            
            blockContainer.innerHTML = blocksHtml;
            
            // Sürükle bırak işlevselliği
            document.querySelectorAll('.block-item').forEach(item => {
                item.addEventListener('mousedown', function(e) {
                    const blockId = this.getAttribute('data-block-id');
                    const block = editor.BlockManager.get(blockId);
                    
                    if (block) {
                        editor.runCommand('block-drag', {
                            block: block
                        });
                    }
                });
            });
        }, 1000);
    }

    /**
     * Eylem işleyicileri
     */
    function setupActions() {
        // Kaydetme eylemi
        if (typeof StudioSaveAction !== 'undefined') {
            StudioSaveAction.init(editor, {
                saveButtonId: 'save-btn',
                csrfToken: document.querySelector('meta[name="csrf-token"]')?.content || '',
                saveEndpoint: '/admin/studio/save'
            });
        }
        
        // Önizleme eylemi
        if (typeof StudioPreviewAction !== 'undefined') {
            StudioPreviewAction.init(editor, {
                previewButtonId: 'preview-btn'
            });
        }
        
        // Dışa aktarma eylemi
        if (typeof StudioExportAction !== 'undefined') {
            StudioExportAction.init(editor, {
                exportButtonId: 'export-btn'
            });
        }
    }
    
    /**
     * Bildirim göster
     * @param {string} message Mesaj
     * @param {string} type Tip (success, error, warning, info)
     */
    function showNotification(message, type = 'info') {
        // Notification container
        let container = document.querySelector('.studio-notification-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'studio-notification-container';
            container.style.position = 'fixed';
            container.style.top = '70px';
            container.style.right = '20px';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
        }
        
        // Notification tipi
        const bgClass = type === 'success' ? 'bg-success' : 
                      type === 'error' ? 'bg-danger' : 
                      type === 'warning' ? 'bg-warning' : 'bg-info';
        
        // Notification oluştur
        const notification = document.createElement('div');
        notification.className = `toast ${bgClass} text-white`;
        notification.setAttribute('role', 'alert');
        notification.setAttribute('aria-live', 'assertive');
        notification.setAttribute('aria-atomic', 'true');
        
        notification.innerHTML = `
            <div class="toast-header ${bgClass} text-white">
                <strong class="me-auto">Studio</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        `;
        
        container.appendChild(notification);
        
        // Bootstrap Toast
        if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
            const toast = new bootstrap.Toast(notification, {
                delay: 3000
            });
            toast.show();
        }
        
        // 3 saniye sonra otomatik kaldır
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 3000);
    }
    
    // Dışa aktarılan fonksiyonlar
    return {
        setup: setup,
        showNotification: showNotification
    };
})();

// Global olarak kullanılabilir yap
window.StudioUI = StudioUI;