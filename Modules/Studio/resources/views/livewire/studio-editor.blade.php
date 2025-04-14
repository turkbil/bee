<div><div>
    <!-- İçerik editörü alanları (hidden textarea) -->
    <textarea id="html-content" style="display:none;">{!! $content !!}</textarea>
    <textarea id="css-content" style="display:none;">{!! $css !!}</textarea>
    <textarea id="js-content" style="display:none;">{!! $js !!}</textarea>
    
    <div class="editor-main">
        <!-- Sol Panel: Tab'lı Panel -->
        <div class="panel__left">
            <div class="panel-tabs">
                <div class="panel-tab active" data-tab="blocks">
                    <i class="fa fa-cubes tab-icon"></i>
                    <span class="tab-text">Bileşenler</span>
                </div>
                <div class="panel-tab" data-tab="styles">
                    <i class="fa fa-paint-brush tab-icon"></i>
                    <span class="tab-text">Stiller</span>
                </div>
                <div class="panel-tab" data-tab="layers">
                    <i class="fa fa-layer-group tab-icon"></i>
                    <span class="tab-text">Katmanlar</span>
                </div>
                <div class="panel-tab" data-tab="traits">
                    <i class="fa fa-cog tab-icon"></i>
                    <span class="tab-text">Özellikler</span>
                </div>
            </div>
            
            <!-- Bileşenler İçeriği -->
            <div class="panel-tab-content active" data-tab-content="blocks">
                <div class="blocks-search">
                    <input type="text" id="blocks-search" class="form-control form-control-sm" placeholder="Bileşen ara...">
                </div>
                
                <div class="blocks-container" id="blocks-container">
                    <!-- Blok kategorileri ve bloklar JavaScript tarafından eklenecek -->
                </div>
            </div>
            
            <!-- Stiller İçeriği -->
            <div class="panel-tab-content" data-tab-content="styles">
                <div id="styles-container" class="styles-container">
                    <!-- Stil yöneticisi buraya eklenecek -->
                </div>
            </div>
            
            <!-- Katmanlar İçeriği -->
            <div class="panel-tab-content" data-tab-content="layers">
                <div id="layers-container" class="layers-container">
                    <!-- Katman yöneticisi buraya eklenecek -->
                </div>
            </div>
            
            <!-- Özellikler İçeriği -->
            <div class="panel-tab-content" data-tab-content="traits">
                <div id="traits-container" class="traits-container">
                    <!-- Özellik yöneticisi buraya eklenecek -->
                </div>
            </div>
        </div>
        
        <!-- Orta Panel: Canvas -->
        <div class="editor-canvas">
            <div id="gjs" 
                data-module-type="{{ $moduleType }}" 
                data-module-id="{{ $moduleId }}"
                data-auto-init="true"></div>
        </div>
    </div>
    
    <!-- Tema Değiştirme Modalı -->
    <div class="modal fade" id="themeModal" tabindex="-1" aria-labelledby="themeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="themeModalLabel">Tema Değiştir</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        @foreach($editorData['themes'] ?? [] as $theme)
                        <div class="col-md-4">
                            <div class="card h-100 theme-card {{ isset($editorData['settings']['theme']) && $editorData['settings']['theme'] == $theme['folder_name'] ? 'border-primary' : '' }}">
                                <div class="card-img-top theme-img">
                                    @if($theme['screenshot'])
                                        <img src="{{ $theme['screenshot'] }}" alt="{{ $theme['title'] }}" class="img-fluid">
                                    @else
                                        <div class="theme-placeholder">
                                            <i class="fa-solid fa-image fa-3x text-muted"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title">{{ $theme['title'] }}</h5>
                                    <p class="card-text small">{{ $theme['description'] }}</p>
                                </div>
                                <div class="card-footer">
                                    <button type="button" class="btn btn-sm {{ isset($editorData['settings']['theme']) && $editorData['settings']['theme'] == $theme['folder_name'] ? 'btn-primary' : 'btn-outline-primary' }}" 
                                            wire:click="changeTheme('{{ $theme['folder_name'] }}')"
                                            wire:loading.attr="disabled">
                                        {{ isset($editorData['settings']['theme']) && $editorData['settings']['theme'] == $theme['folder_name'] ? 'Aktif Tema' : 'Temayı Seç' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                </div>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        // Tema değiştirme olayını dinle
        document.addEventListener('livewire:initialized', function () {
            Livewire.on('theme-changed', (event) => {
                // Bootstrap modalı kapat
                const themeModal = document.getElementById('themeModal');
                if (themeModal && bootstrap.Modal.getInstance(themeModal)) {
                    bootstrap.Modal.getInstance(themeModal).hide();
                }
            });
            
            // Bildirim göster
            Livewire.on('notify', (event) => {
                const { type, message } = event;
                
                // Tabler notifikasyonu
                if (type && message) {
                    const notificationClass = type === 'success' ? 'bg-success text-white' : 
                                             type === 'error' ? 'bg-danger text-white' : 
                                             type === 'warning' ? 'bg-warning' : 'bg-info text-white';
                    
                    const notification = document.createElement('div');
                    notification.className = `toast align-items-center ${notificationClass} border-0 show`;
                    notification.setAttribute('role', 'alert');
                    notification.setAttribute('aria-live', 'assertive');
                    notification.setAttribute('aria-atomic', 'true');
                    
                    notification.innerHTML = `
                        <div class="d-flex">
                            <div class="toast-body">
                                ${message}
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Kapat"></button>
                        </div>
                    `;
                    
                    // Notification container
                    let container = document.querySelector('.toast-container');
                    if (!container) {
                        container = document.createElement('div');
                        container.className = 'toast-container position-fixed top-0 end-0 p-3';
                        container.style.zIndex = '9999';
                        document.body.appendChild(container);
                    }
                    
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
            });
        });
    </script>
    @endpush
    
    <style>
    .theme-card {
        transition: all 0.2s ease-in-out;
        cursor: pointer;
    }
    
    .theme-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    .theme-img {
        height: 150px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
    }
    
    .theme-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        width: 100%;
        background-color: #f8f9fa;
    }
    .block-item {
    cursor: grab;
    transition: all 0.2s ease;
    }

    .block-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .block-item.dragging {
        opacity: 0.5;
        cursor: grabbing;
    }

    /* Akordiyon stilleri */
    .accordion-item {
        overflow: hidden;
        border: 1px solid rgba(0,0,0,.125);
        border-radius: 0.25rem;
    }

    .accordion-button {
        padding: 0.75rem 1rem;
        font-weight: 500;
    }

    .accordion-button:not(.collapsed) {
        background-color: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
    }

    .accordion-body {
        padding: 0.75rem;
    }

    /* Canvas bölgesini vurgula */
    .editor-canvas {
        transition: all 0.25s ease;
    }

    .editor-canvas.drop-active {
        background-color: rgba(59, 130, 246, 0.05);
    }
    </style>
    @push('scripts')
    <script>
    // Canvas frame yüklendikten sonra çalışacak fonksiyon
    function checkCanvasFrame() {
        const gjs = document.getElementById('gjs');
        if (!gjs) return;
        
        // Canvas frame'ini kontrol et
        if (window.studioEditor) {
            const frame = window.studioEditor.Canvas.getFrame();
            if (frame) {
                console.log("Canvas frame bulundu, sürükle-bırak işlemleri etkinleştiriliyor...");
                
                // Manuel olarak sürükle-bırak olaylarını kur
                setupCanvasDrop();
                return;
            }
        }
        
        // Frame bulunamadıysa tekrar dene
        setTimeout(checkCanvasFrame, 500);
    }

    // Canvas için sürükle-bırak olaylarını kur
    function setupCanvasDrop() {
        try {
            const frame = window.studioEditor.Canvas.getFrame();
            if (!frame) return;
            
            const canvasDoc = frame.view.el.contentDocument;
            if (!canvasDoc) return;
            
            const canvasBody = canvasDoc.body;
            if (!canvasBody) return;
            
            // Canvas'ta sürükleme olayları
            canvasBody.addEventListener('dragover', function(e) {
                e.preventDefault();
                e.stopPropagation();
                this.style.outline = '2px dashed #3b82f6';
                this.style.background = 'rgba(59, 130, 246, 0.05)';
            });
            
            canvasBody.addEventListener('dragleave', function(e) {
                e.preventDefault();
                e.stopPropagation();
                this.style.outline = 'none';
                this.style.background = '';
            });
            
            canvasBody.addEventListener('drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                this.style.outline = 'none';
                this.style.background = '';
                
                // Blok sürüklenme kontrolü
                const blockId = e.dataTransfer.getData('text/plain');
                if (blockId) {
                    const block = window.studioEditor.BlockManager.get(blockId);
                    
                    if (block) {
                        // Blok içeriğini al
                        const content = block.get('content');
                        
                        // Eklenecek pozisyonu hesapla (fare konumuna göre)
                        const coords = {
                            x: e.clientX, 
                            y: e.clientY
                        };
                        
                        // İçerik türüne göre editöre ekle
                        let component;
                        
                        if (typeof content === 'string') {
                            component = window.studioEditor.addComponents(content)[0];
                        } else if (typeof content === 'object') {
                            component = window.studioEditor.addComponents(window.studioEditor.DomComponents.addComponent(content))[0];
                        }
                        
                        // Eklenen bileşeni hemen seç (düzenleme için)
                        if (component) {
                            window.studioEditor.select(component);
                            
                            // Özellikler tab'ını aktifleştir
                            const traitsTab = document.querySelector('.panel-tab[data-tab="traits"]');
                            if (traitsTab) {
                                traitsTab.click();
                            }
                        }
                    }
                }
            });
            
            console.log("Canvas sürükle-bırak işlemleri başarıyla etkinleştirildi");
        } catch (error) {
            console.error("Canvas sürükle-bırak ayarlarken hata:", error);
        }
    }

    // Canvas frame yüklenince işlemleri başlat
    document.addEventListener('DOMContentLoaded', function() {
        // Canvas frame yüklendikten sonra sürükle-bırak olaylarını ayarla
        setTimeout(checkCanvasFrame, 1000);
        
        // Blok sürükle-bırak olaylarını ayarla
        document.addEventListener('studio:editor-ready', function() {
            // Blok etkileşimlerini ayarla
            setupBlockDragDrop();
        });
        
        // Editor hazır olduğunda tetikle
        setTimeout(() => {
            document.dispatchEvent(new CustomEvent('studio:editor-ready'));
        }, 2000);
    });

    // Blok sürükle-bırak olaylarını ayarla
    function setupBlockDragDrop() {
        document.querySelectorAll('.block-item').forEach(item => {
            // Sürükle başlangıç olayı
            item.addEventListener('dragstart', function(e) {
                const blockId = this.getAttribute('data-block-id');
                e.dataTransfer.setData('text/plain', blockId);
                e.dataTransfer.effectAllowed = 'copy';
                this.classList.add('dragging');
            });
            
            // Sürükle bitiş olayı
            item.addEventListener('dragend', function(e) {
                this.classList.remove('dragging');
            });
        });
    }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Bootstrap akordiyon için tanımlamaları ekle
            const blocksContainer = document.getElementById('blocks-container');
            
            // Observer oluştur - DOM'a blokların eklendiğini izlemek için
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.addedNodes.length) {
                        // Akordiyon varsa, bootstrap ile ilişkilendir
                        const accordions = blocksContainer.querySelectorAll('.accordion');
                        if (accordions.length > 0) {
                            // Bootstrap ile initialize et
                            accordions.forEach(accordion => {
                                // Akordiyon başlıklarını tıklanabilir yap
                                const headers = accordion.querySelectorAll('.accordion-header button');
                                headers.forEach(header => {
                                    header.addEventListener('click', function(e) {
                                        e.preventDefault();
                                        const target = this.getAttribute('data-bs-target');
                                        const collapse = document.querySelector(target);
                                        
                                        if (collapse) {
                                            // Manuel olarak açılıp kapanmayı yönet
                                            if (collapse.classList.contains('show')) {
                                                collapse.classList.remove('show');
                                                this.classList.add('collapsed');
                                            } else {
                                                collapse.classList.add('show');
                                                this.classList.remove('collapsed');
                                            }
                                        }
                                    });
                                });
                            });
                            
                            // Blok elementlerini sürüklenebilir yap
                            setupDraggableBlocks();
                        }
                    }
                });
            });
            
            // Observer'ı başlat
            observer.observe(blocksContainer, { childList: true });
            
            // Blok elementlerini sürüklenebilir yap
            function setupDraggableBlocks() {
                const blockItems = document.querySelectorAll('.block-item');
                blockItems.forEach(item => {
                    item.setAttribute('draggable', 'true');
                    
                    // Sürükleme başladığında
                    item.addEventListener('dragstart', function(e) {
                        const blockId = this.getAttribute('data-block-id');
                        e.dataTransfer.setData('text/plain', blockId);
                        e.dataTransfer.effectAllowed = 'copy';
                        this.classList.add('dragging');
                    });
                    
                    // Sürükleme bittiğinde
                    item.addEventListener('dragend', function() {
                        this.classList.remove('dragging');
                    });
                });
            }
            
            // Canvas'a sürükle-bırak davranışı ekle
            function setupCanvasDrop() {
                if (!window.studioEditor) return;
                
                const frame = window.studioEditor.Canvas.getFrame();
                if (!frame) return setTimeout(setupCanvasDrop, 500);
                
                const canvasDoc = frame.view.el.contentDocument;
                if (!canvasDoc) return;
                
                const canvasBody = canvasDoc.body;
                
                // Sürükleme olayları
                canvasBody.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    this.style.outline = '2px dashed #3b82f6';
                    this.style.background = 'rgba(59, 130, 246, 0.05)';
                });
                
                canvasBody.addEventListener('dragleave', function(e) {
                    e.preventDefault();
                    this.style.outline = 'none';
                    this.style.background = '';
                });
                
                canvasBody.addEventListener('drop', function(e) {
                    e.preventDefault();
                    this.style.outline = 'none';
                    this.style.background = '';
                    
                    const blockId = e.dataTransfer.getData('text/plain');
                    if (blockId && window.studioEditor) {
                        const block = window.studioEditor.BlockManager.get(blockId);
                        
                        if (block) {
                            const content = block.get('content');
                            let component;
                            
                            if (typeof content === 'string') {
                                component = window.studioEditor.addComponents(content)[0];
                            } else if (typeof content === 'object') {
                                component = window.studioEditor.addComponents(
                                    window.studioEditor.DomComponents.addComponent(content)
                                )[0];
                            }
                            
                            // Eklenen bileşeni hemen seç
                            if (component) {
                                window.studioEditor.select(component);
                            }
                        }
                    }
                });
            }
            
            // Editor hazır olduğunda canvas drop işlevini ayarla
            document.addEventListener('studio:editor-ready', function() {
                setTimeout(setupCanvasDrop, 500);
            });
        });
        </script>
    @endpush

</div></div>