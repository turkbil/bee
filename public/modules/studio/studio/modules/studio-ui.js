/**
 * Studio Editor - UI Modülü
 * Kullanıcı arayüzü bileşenleri ve etkileşimleri
 */
window.StudioUI = (function() {
    // Aktif sekme ID'si
    let activeTabId = 'blocks';
    
    /**
     * UI bileşenlerini başlat
     * @param {Object} editor - GrapesJS editör örneği
     */
    function init(editor) {
        if (!editor) {
            console.error('UI başlatılırken editor örneği bulunamadı!');
            return;
        }
        
        setupTabs();
        setupBlockSearch();
        setupSidePanelToggle();
        setupDeviceButtons(editor);
        setupOutlineToggle(editor);
        
        // Editör yüklendikten sonra diğer UI iyileştirmelerini yap
        editor.on('load', () => {
            setupStyleManagerUI();
            setupLayerManagerUI();
            enhanceTraitManager();
            setupDraggableBlocks();
            
            // Yükleme tamamlandı mesajı
            console.log('Studio UI modülleri başarıyla yüklendi.');
        });
    }
    
    /**
     * Tab panelleri için işlevsellik
     */
    function setupTabs() {
        const tabs = document.querySelectorAll('.panel-tab');
        const tabContents = document.querySelectorAll('.panel-tab-content');
        
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const tabId = this.getAttribute('data-tab');
                
                // Zaten aktif olan sekmeye tıklandıysa işlem yapma
                if (activeTabId === tabId) return;
                
                // Aktif tab'ı değiştir
                activeTabId = tabId;
                
                // Tüm tab'lardan active sınıfını kaldır
                tabs.forEach(t => t.classList.remove('active'));
                
                // Tıklanan tab'a active sınıfı ekle
                this.classList.add('active');
                
                // Tüm tab içeriklerini gizle
                tabContents.forEach(content => {
                    content.classList.remove('active');
                });
                
                // İlgili tab içeriğini göster
                const activeContent = document.querySelector(`.panel-tab-content[data-tab-content="${tabId}"]`);
                if (activeContent) {
                    activeContent.classList.add('active');
                }
                
                // Tab değişikliği olayını tetikle
                window.StudioEvents.trigger('ui:tab:changed', tabId);
            });
        });
        
        console.log('Tab panelleri etkinleştirildi.');
    }
    
    /**
     * Blok arama işlevselliği
     */
    function setupBlockSearch() {
        const searchInput = document.getElementById('blocks-search');
        if (!searchInput) return;
        
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            // GrapesJS bloklarını ara
            const blocks = document.querySelectorAll('.block-item');
            
            blocks.forEach(block => {
                const blockLabel = block.querySelector('.block-item-label');
                if (blockLabel) {
                    const label = blockLabel.textContent.toLowerCase();
                    
                    // Eşleşme durumuna göre göster/gizle
                    if (label.includes(searchTerm)) {
                        block.style.display = '';
                        
                        // Ebeveyn kategoriyi göster
                        const category = block.closest('.block-category');
                        if (category) {
                            category.style.display = '';
                            category.classList.remove('collapsed');
                            
                            // Bloklar kısmını göster
                            const blockItems = category.querySelector('.block-items');
                            if (blockItems) {
                                blockItems.style.display = '';
                            }
                        }
                    } else {
                        block.style.display = 'none';
                    }
                }
            });
            
            // Boş kategorileri kontrol et ve gizle
            const categories = document.querySelectorAll('.block-category');
            categories.forEach(category => {
                const blocks = category.querySelectorAll('.block-item');
                const visibleBlocks = Array.from(blocks).filter(b => b.style.display !== 'none');
                
                if (visibleBlocks.length === 0) {
                    category.style.display = 'none';
                } else {
                    category.style.display = '';
                }
            });
            
            // Arama terimi boşsa tüm kategorileri göster ve varsayılan durumlarına getir
            if (searchTerm === '') {
                categories.forEach(category => {
                    category.style.display = '';
                    
                    // Kategori varsayılan olarak açık mı?
                    const isOpenByDefault = !category.classList.contains('collapsed-default');
                    
                    if (isOpenByDefault) {
                        category.classList.remove('collapsed');
                        const blockItems = category.querySelector('.block-items');
                        if (blockItems) {
                            blockItems.style.display = '';
                        }
                    } else {
                        category.classList.add('collapsed');
                        const blockItems = category.querySelector('.block-items');
                        if (blockItems) {
                            blockItems.style.display = 'none';
                        }
                    }
                });
            }
            
            // Arama olayını tetikle
            window.StudioEvents.trigger('ui:blocks:search', searchTerm);
        });
        
        console.log('Blok arama işlevselliği etkinleştirildi.');
    }
    
    /**
     * Yan panel genişliğini ayarlama butonu
     */
    function setupSidePanelToggle() {
        const leftPanel = document.querySelector('.panel__left');
        const toggleBtn = document.createElement('button');
        
        toggleBtn.className = 'panel-toggle-btn';
        toggleBtn.innerHTML = '<i class="fas fa-chevron-left"></i>';
        toggleBtn.title = 'Panel genişliğini değiştir';
        
        // Buton stilini ayarla
        toggleBtn.style.position = 'absolute';
        toggleBtn.style.right = '-12px';
        toggleBtn.style.top = '50%';
        toggleBtn.style.transform = 'translateY(-50%)';
        toggleBtn.style.zIndex = '999';
        toggleBtn.style.width = '24px';
        toggleBtn.style.height = '40px';
        toggleBtn.style.background = '#ffffff';
        toggleBtn.style.border = '1px solid #e5e7eb';
        toggleBtn.style.borderRadius = '0 4px 4px 0';
        toggleBtn.style.cursor = 'pointer';
        toggleBtn.style.boxShadow = '0 2px 5px rgba(0,0,0,0.1)';
        
        let isPanelCollapsed = false;
        
        // Panel genişletme/daraltma işlevi
        toggleBtn.addEventListener('click', function() {
            if (isPanelCollapsed) {
                // Paneli genişlet
                leftPanel.style.width = '280px';
                toggleBtn.innerHTML = '<i class="fas fa-chevron-left"></i>';
                
            } else {
                // Paneli daralt
                leftPanel.style.width = '50px';
                toggleBtn.innerHTML = '<i class="fas fa-chevron-right"></i>';
                
                // Tab içeriklerini gizle
                const tabContents = document.querySelectorAll('.panel-tab-content');
                tabContents.forEach(content => {
                    content.style.display = 'none';
                });
            }
            
            isPanelCollapsed = !isPanelCollapsed;
            
            // Panel durumu olayını tetikle
            window.StudioEvents.trigger('ui:panel:toggled', isPanelCollapsed);
        });
        
        if (leftPanel) {
            leftPanel.style.position = 'relative';
            leftPanel.appendChild(toggleBtn);
            console.log('Yan panel genişlik butonu eklendi.');
        }
    }
    
    /**
     * Cihaz görünümü butonları
     * @param {Object} editor - GrapesJS editör örneği
     */
    function setupDeviceButtons(editor) {
        const deviceBtns = document.querySelectorAll('.device-btns button');
        
        deviceBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const deviceType = this.getAttribute('id').replace('device-', '');
                
                // Tüm butonlardan active sınıfını kaldır
                deviceBtns.forEach(b => b.classList.remove('active'));
                
                // Tıklanan butona active sınıfı ekle
                this.classList.add('active');
                
                // Cihaz modunu ayarla
                switch (deviceType) {
                    case 'desktop':
                        editor.setDevice('Desktop');
                        break;
                    case 'tablet':
                        editor.setDevice('Tablet');
                        break;
                    case 'mobile':
                        editor.setDevice('Mobile');
                        break;
                }
                
                // Cihaz değişikliği olayını tetikle
                window.StudioEvents.trigger('ui:device:changed', deviceType);
            });
        });
        
        console.log('Cihaz görünümü butonları etkinleştirildi.');
    }
    
    /**
     * Bileşen sınırlarını göster/gizle butonu
     * @param {Object} editor - GrapesJS editör örneği
     */
    function setupOutlineToggle(editor) {
        const outlineBtn = document.getElementById('sw-visibility');
        if (!outlineBtn) return;
        
        let outlineVisible = false;
        
        outlineBtn.addEventListener('click', function() {
            outlineVisible = !outlineVisible;
            
            // Buton durumunu güncelle
            this.classList.toggle('active', outlineVisible);
            
            // Görünürlük komutunu çalıştır
            editor.runCommand('sw-visibility');
            
            // Olayı tetikle
            window.StudioEvents.trigger('ui:outline:toggled', outlineVisible);
        });
        
        console.log('Bileşen sınırları butonu etkinleştirildi.');
    }
    
    /**
     * Stil yöneticisi UI iyileştirmeleri
     */
    function setupStyleManagerUI() {
        // Stil panelindeki sektörlere açılır-kapanır özelliği ekle
        const sectors = document.querySelectorAll('.gjs-sm-sector');
        
        sectors.forEach((sector, index) => {
            const sectorTitle = sector.querySelector('.gjs-sm-sector-title');
            const properties = sector.querySelector('.gjs-sm-properties');
            
            if (sectorTitle && properties) {
                // Başlığa tıklama işlevi ekle
                sectorTitle.addEventListener('click', function() {
                    sector.classList.toggle('gjs-collapsed');
                    
                    // Akordiyon için özellikler kısmını göster/gizle
                    if (sector.classList.contains('gjs-collapsed')) {
                        properties.style.display = 'none';
                    } else {
                        properties.style.display = 'block';
                    }
                });
                
                // İlk sektör açık, diğerleri kapalı olarak başlasın
                if (index === 0) {
                    sector.classList.remove('gjs-collapsed');
                    properties.style.display = 'block';
                } else {
                    sector.classList.add('gjs-collapsed');
                    properties.style.display = 'none';
                }
            }
        });
        
        console.log('Stil yöneticisi UI iyileştirmeleri yapıldı.');
    }
    
    /**
     * Katman yöneticisi UI iyileştirmeleri
     */
    function setupLayerManagerUI() {
        // Katman paneline başlık ekle
        const layersContainer = document.getElementById('layers-container');
        
        if (layersContainer) {
            // Başlık eklenmemiş mi kontrol et
            if (!layersContainer.querySelector('.custom-panel-header')) {
                const layersHeader = document.createElement('div');
                layersHeader.className = 'custom-panel-header';
                layersHeader.innerHTML = '<i class="fas fa-layer-group me-2"></i> Katmanlar';
                
                // Başlığı panelin en üstüne ekle
                layersContainer.insertBefore(layersHeader, layersContainer.firstChild);
            }
        }
        
        console.log('Katman yöneticisi UI iyileştirmeleri yapıldı.');
    }
    
    /**
     * Özellik yöneticisi iyileştirmeleri
     */
    function enhanceTraitManager() {
        // Özellikler paneline başlık ekle
        const traitsContainer = document.getElementById('traits-container');
        
        if (traitsContainer) {
            // Başlık eklenmemiş mi kontrol et
            if (!traitsContainer.querySelector('.custom-panel-header')) {
                const traitsHeader = document.createElement('div');
                traitsHeader.className = 'custom-panel-header';
                traitsHeader.innerHTML = '<i class="fas fa-cog me-2"></i> Özellikler';
                
                // Başlığı panelin en üstüne ekle
                traitsContainer.insertBefore(traitsHeader, traitsContainer.firstChild);
            }
        }
        
        console.log('Özellik yöneticisi UI iyileştirmeleri yapıldı.');
    }
    
    /**
     * Sürüklenebilir bloklar için gelişmiş işlevsellik
     */
    function setupDraggableBlocks() {
        const blocks = document.querySelectorAll('.block-item');
        
        blocks.forEach(block => {
            // Sürükleme başlangıcı olayı
            block.addEventListener('dragstart', function(e) {
                this.classList.add('dragging');
                
                const blockId = this.getAttribute('data-block-id');
                const blockContent = this.getAttribute('data-content');
                
                // Sürükleme verileri
                if (blockId) {
                    e.dataTransfer.setData('text/plain', blockId);
                    e.dataTransfer.setData('application/studio-block-id', blockId);
                }
                
                if (blockContent) {
                    e.dataTransfer.setData('text/html', blockContent);
                    e.dataTransfer.setData('application/studio-block-content', blockContent);
                }
                
                // Sürükleme efekti
                e.dataTransfer.effectAllowed = 'copy';
                
                // Olayı tetikle
                window.StudioEvents.trigger('ui:block:dragstart', {
                    blockId: blockId,
                    element: this
                });
            });
            
            // Sürükleme bitişi olayı
            block.addEventListener('dragend', function() {
                this.classList.remove('dragging');
                
                // Olayı tetikle
                window.StudioEvents.trigger('ui:block:dragend', {
                    blockId: this.getAttribute('data-block-id'),
                    element: this
                });
            });
        });
        
        console.log('Sürüklenebilir bloklar geliştirildi.');
    }
    
    /**
     * Notifikasyon göster
     * @param {string} title - Bildirim başlığı
     * @param {string} message - Bildirim mesajı
     * @param {string} type - Bildirim tipi (success, error, warning, info)
     */
    function showNotification(title, message, type = 'success') {
        // Toast div'ini oluştur
        const toastEl = document.createElement('div');
        toastEl.className = `toast align-items-center text-white bg-${
            type === 'success' ? 'success' : 
            type === 'error' ? 'danger' : 
            type === 'warning' ? 'warning' : 'info'
        } border-0`;
        toastEl.setAttribute('role', 'alert');
        toastEl.setAttribute('aria-live', 'assertive');
        toastEl.setAttribute('aria-atomic', 'true');
        
        // Toast içeriği
        toastEl.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <strong>${title}</strong>: ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Kapat"></button>
            </div>
        `;
        
        // Toast container
        let toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
            toastContainer.style.zIndex = '9999';
            document.body.appendChild(toastContainer);
        }
        
        // Toast'u ekle
        toastContainer.appendChild(toastEl);
        
        // Bootstrap Toast sınıfı varsa kullan
        if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
            const toast = new bootstrap.Toast(toastEl, {
                delay: 3000
            });
            toast.show();
        } else {
            // Alternatif gösterme yöntemi
            toastEl.style.display = 'block';
            setTimeout(() => {
                toastEl.style.opacity = '0';
                setTimeout(() => {
                    if (toastContainer.contains(toastEl)) {
                        toastContainer.removeChild(toastEl);
                    }
                }, 300);
            }, 3000);
        }
        
        // Belirli bir süre sonra kaldır
        setTimeout(() => {
            if (toastContainer.contains(toastEl)) {
                toastContainer.removeChild(toastEl);
            }
        }, 3300);
    }
    
    /**
     * Onay diyaloğu göster
     * @param {string} title - Diyalog başlığı
     * @param {string} message - Diyalog mesajı
     * @param {Function} callback - Onaylandığında çağrılacak fonksiyon
     */
    function showConfirmDialog(title, message, callback) {
        // Modal div'ini oluştur
        const modalEl = document.createElement('div');
        modalEl.className = 'modal fade';
        modalEl.id = 'studioConfirmModal';
        modalEl.setAttribute('tabindex', '-1');
        modalEl.setAttribute('aria-hidden', 'true');
        
        // Modal içeriği
        modalEl.innerHTML = `
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">${title}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                    </div>
                    <div class="modal-body">
                        <p>${message}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                        <button type="button" class="btn btn-primary" id="confirmOkBtn">Tamam</button>
                    </div>
                </div>
            </div>
        `;
        
        // Modalı ekle
        document.body.appendChild(modalEl);
        
        // Bootstrap Modal sınıfı varsa kullan
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
            
            // Tamam butonuna tıklama
            document.getElementById('confirmOkBtn').addEventListener('click', function() {
                modal.hide();
                if (typeof callback === 'function') {
                    callback(true);
                }
            });
            
            // Modal kapandığında temizle
            modalEl.addEventListener('hidden.bs.modal', function() {
                document.body.removeChild(modalEl);
            });
        } else {
            // Alternatif gösterme yöntemi
            modalEl.style.display = 'block';
            
            // Tamam butonuna tıklama
            document.getElementById('confirmOkBtn').addEventListener('click', function() {
                document.body.removeChild(modalEl);
                if (typeof callback === 'function') {
                    callback(true);
                }
            });
            
            // İptal butonuna tıklama
            modalEl.querySelector('.btn-secondary').addEventListener('click', function() {
                document.body.removeChild(modalEl);
                if (typeof callback === 'function') {
                    callback(false);
                }
            });
        }
    }
    
    return {
        init: init,
        showNotification: showNotification,
        showConfirmDialog: showConfirmDialog
    };
})();