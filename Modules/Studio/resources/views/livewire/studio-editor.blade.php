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
    </style>
    
    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Studio Core yüklendikten sonra blokları doldurmayı dene
        setTimeout(() => {
            if (window.StudioBlocks && window.studioEditor) {
                console.log('Bloklar manuel olarak doldurma işlemi başlatılıyor...');
                
                // Kategorileri düzgün göstermek için ekstra kod
                const categories = {
                    'temel': 'Temel Bileşenler',
                    'mizanpaj': 'Mizanpaj Bileşenleri',
                    'bootstrap': 'Bootstrap Bileşenleri',
                    'medya': 'Medya Bileşenleri',
                    'özel': 'Özel Bileşenler',
                    'widget': 'Widget Bileşenleri',
                    'diğer': 'Diğer Bileşenler'
                };
                
                // Kategori bilgilerini global olarak kaydet
                window.studioCategories = categories;
                
                // Blokları render et
                StudioBlocks.renderBlocksToDOM(window.studioEditor);
                
                // Tıklama olaylarını devre dışı bırak
                setTimeout(() => {
            } else {
                console.warn('StudioBlocks veya studioEditor bulunamadı');
            }
        }, 1500);
    });
    </script>
    @endpush

    @push('scripts')
    <script>
    // Editör hazır olduğunda manuel olarak drop desteğini etkinleştir
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            if (window.studioEditor) {
                try {
                    // Canvas'ın doğru yüklenmesini bekle
                    const canvas = window.studioEditor.Canvas;
                    const iframe = canvas.getFrameEl();
                    
                    if (!iframe) {
                        console.warn('Canvas iframe bulunamadı');
                        return;
                    }
                    
                    // iframe yüklendikten sonra drop olaylarını ayarla
                    iframe.onload = function() {
                        console.log('Canvas iframe yüklendi, drop olayları ayarlanıyor...');
                        
                        const canvasDoc = iframe.contentDocument;
                        const canvasEl = canvasDoc.body;
                        
                        if (!canvasEl) {
                            console.warn('Canvas body bulunamadı');
                            return;
                        }
                    };
                    
                    // Eğer iframe zaten yüklendiyse
                    if (iframe.contentDocument) {
                        iframe.onload();
                    }
                } catch (e) {
                    console.error('Canvas drop olaylarını ayarlarken hata:', e);
                }
            }
        }, 2000);
    });
    </script>
    @endpush
</div></div>