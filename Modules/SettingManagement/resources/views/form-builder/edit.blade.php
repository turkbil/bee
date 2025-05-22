<div id="widget-form-builder-app">
        <!-- Form Builder Header -->
    <div class="studio-header">
        <div class="header-left">
            <div class="toolbar-buttons me-4">
                <button id="sw-visibility" class="toolbar-button" title="Bileşen sınırlarını göster/gizle">
                    <i class="fas fa-border-all"></i>
                </button>
                <button id="cmd-clear" class="toolbar-button" title="İçeriği temizle">
                    <i class="fas fa-trash-alt"></i>
                </button>
                <button id="cmd-undo" class="toolbar-button" title="Geri al">
                    <i class="fas fa-undo"></i>
                </button>
                <button id="cmd-redo" class="toolbar-button" title="Yinele">
                    <i class="fas fa-redo"></i>
                </button>
            </div>
        </div>

        <div class="header-center">
            <div class="studio-brand">
                {{ $group->name }} <i class="fa-solid fa-wand-magic-sparkles mx-2"></i> Studio Form
            </div>
        </div>

        <div class="header-right">
            <a href="{{ route('admin.settingmanagement.index') }}" id="back-btn" class="btn-back me-2" title="Geri">
                <i class="fa-solid fa-arrow-left me-1"></i>
                <span>Geri Dön</span>
            </a>
            
            <button id="preview-btn" class="btn-view me-2" title="Önizleme">
                <i class="fa-solid fa-eye me-1"></i>
                <span>Önizleme</span>
            </button>

            <button id="save-btn" class="btn-save" title="Kaydet">
                <i class="fa-solid fa-save me-1"></i>
                <span>Kaydet</span>
            </button>
        </div>
    </div>

    <div class="editor-main">
        <!-- Sol Panel: Form Elemanları -->
        <div class="panel__left">
            <div id="element-palette">
                <!-- Düzen Elemanları -->
                <div class="block-category">
                    <div class="block-category-header">
                        <i class="fas fa-grip-lines-vertical"></i>
                        <span>Düzen Elemanları</span>
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </div>
                    <div class="block-items">
                        <div class="element-palette-item" data-type="row">
                            <i class="fas fa-grip-lines-vertical"></i>
                            <span>Satır</span>
                        </div>
                        <div class="element-palette-item" data-type="heading">
                            <i class="fas fa-heading"></i>
                            <span>Başlık</span>
                        </div>
                        <div class="element-palette-item" data-type="paragraph">
                            <i class="fas fa-paragraph"></i>
                            <span>Paragraf</span>
                        </div>
                        <div class="element-palette-item" data-type="divider">
                            <i class="fas fa-minus"></i>
                            <span>Ayırıcı Çizgi</span>
                        </div>
                        <div class="element-palette-item" data-type="spacer">
                            <i class="fas fa-arrows-alt-v"></i>
                            <span>Boşluk</span>
                        </div>
                        <div class="element-palette-item" data-type="card">
                            <i class="fas fa-credit-card"></i>
                            <span>Kart</span>
                        </div>
                        <div class="element-palette-item" data-type="tab_group">
                            <i class="fas fa-folder"></i>
                            <span>Sekmeler</span>
                        </div>
                    </div>
                </div>
                
                <!-- Metin Elemanları -->
                <div class="block-category">
                    <div class="block-category-header">
                        <i class="fas fa-font"></i>
                        <span>Metin Elemanları</span>
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </div>
                    <div class="block-items">
                        <div class="element-palette-item" data-type="text">
                            <i class="fas fa-font"></i>
                            <span>Metin</span>
                        </div>
                        <div class="element-palette-item" data-type="textarea">
                            <i class="fas fa-align-left"></i>
                            <span>Uzun Metin</span>
                        </div>
                        <div class="element-palette-item" data-type="number">
                            <i class="fas fa-hashtag"></i>
                            <span>Sayı</span>
                        </div>
                        <div class="element-palette-item" data-type="email">
                            <i class="fas fa-at"></i>
                            <span>E-posta</span>
                        </div>
                        <div class="element-palette-item" data-type="password">
                            <i class="fas fa-key"></i>
                            <span>Şifre</span>
                        </div>
                        <div class="element-palette-item" data-type="tel">
                            <i class="fas fa-phone"></i>
                            <span>Telefon</span>
                        </div>
                        <div class="element-palette-item" data-type="url">
                            <i class="fas fa-globe"></i>
                            <span>Web Adresi</span>
                        </div>
                    </div>
                </div>
                
                <!-- Seçim Elemanları -->
                <div class="block-category">
                    <div class="block-category-header">
                        <i class="fas fa-check-square"></i>
                        <span>Seçim Elemanları</span>
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </div>
                    <div class="block-items">
                        <div class="element-palette-item" data-type="select">
                            <i class="fas fa-caret-square-down"></i>
                            <span>Açılır Liste</span>
                        </div>
                        <div class="element-palette-item" data-type="checkbox">
                            <i class="fas fa-check-square"></i>
                            <span>Onay Kutusu</span>
                        </div>
                        <div class="element-palette-item" data-type="radio">
                            <i class="fas fa-circle"></i>
                            <span>Seçim Düğmesi</span>
                        </div>
                        <div class="element-palette-item" data-type="switch">
                            <i class="fas fa-toggle-on"></i>
                            <span>Anahtar</span>
                        </div>
                    </div>
                </div>
                
                <!-- Veri Tipi Elemanları -->
                <div class="block-category">
                    <div class="block-category-header">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Veri Elemanları</span>
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </div>
                    <div class="block-items">
                        <div class="element-palette-item" data-type="date">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Tarih</span>
                        </div>
                        <div class="element-palette-item" data-type="time">
                            <i class="fas fa-clock"></i>
                            <span>Saat</span>
                        </div>
                        <div class="element-palette-item" data-type="color">
                            <i class="fas fa-palette"></i>
                            <span>Renk</span>
                        </div>
                        <div class="element-palette-item" data-type="range">
                            <i class="fas fa-sliders-h"></i>
                            <span>Değer Aralığı</span>
                        </div>
                    </div>
                </div>
                
                <!-- Dosya Elemanları -->
                <div class="block-category">
                    <div class="block-category-header">
                        <i class="fas fa-file-upload"></i>
                        <span>Dosya Elemanları</span>
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </div>
                    <div class="block-items">
                        <div class="element-palette-item" data-type="file">
                            <i class="fas fa-file"></i>
                            <span>Dosya</span>
                        </div>
                        <div class="element-palette-item" data-type="image">
                            <i class="fas fa-image"></i>
                            <span>Resim</span>
                        </div>
                        <div class="element-palette-item" data-type="image_multiple">
                            <i class="fas fa-images"></i>
                            <span>Çoklu Resim</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            
        <!-- Orta Panel: Form Canvas -->
        <div class="form-canvas">
            <!-- Loading animasyonu -->
            <div class="canvas-loading" id="canvas-loading">
                <div class="loading-spinner"></div>
                <div class="loading-text">Form yükleniyor...</div>
            </div>
            
            <div class="card shadow-sm w-100" style="max-width: 800px;">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit me-2"></i>
                        Form Düzenleme
                    </h3>
                </div>
                <div class="card-body p-0">
                    <div id="form-container"></div>
                    <div id="form-canvas" class="p-3">
                        <div class="empty-canvas" id="empty-canvas">
                            <div class="text-center">
                                <div class="h1 text-muted mb-3">
                                    <i class="fas fa-arrows-alt"></i>
                                </div>
                                <h3 class="text-muted">Form Oluşturmaya Başlayın</h3>
                                <p class="text-muted">Sol taraftaki elemanları sürükleyip buraya bırakın.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sağ Panel: Element Özellikleri -->
        <div class="panel__right">
            <div id="properties-panel">
                <div class="text-center p-4">
                    <div class="h1 text-muted mb-3">
                        <i class="fas fa-mouse-pointer"></i>
                    </div>
                    <h3 class="text-muted">Element Seçilmedi</h3>
                    <p class="text-muted">Özelliklerini düzenlemek için bir form elementi seçin.</p>
                </div>
            </div>
        </div>
    </div>

    <div id="toast-container" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050;"></div>

    <input type="hidden" id="group-id" value="{{ $group->id }}">
</div>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Form Builder JS'nin yüklenmesini bekle
        setTimeout(function() {
            const groupId = document.getElementById('group-id').value;
            const saveBtn = document.getElementById('save-btn');
            
            // Form yükleme işlemini form-builder.js'e bırakalım
            // Bu değişken ile form-builder.js'e formun zaten yüklendiğini bildirelim
            window.formLoadedFromBlade = true;
            console.log('Form yükleme işlemi form-builder.js tarafından yapılacak');
            
            // Ayarları yükle
            fetch(`/admin/settingmanagement/api/settings?group=${groupId}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(settings => {
                console.log('Ayarlar yüklendi:', settings);
                window.availableSettings = settings;
                
                // Setting dropdown'larını doldur
                if (typeof window.populateSettingDropdowns === 'function') {
                    window.populateSettingDropdowns(groupId);
                }
            })
            .catch(error => {
                console.error('Ayarlar yüklenirken hata:', error);
            });
            
            // Kaydet butonuna tıklama olayı
            window.settingSaveBtn = window.settingSaveBtn || document.getElementById('save-btn');
            
            if (window.settingSaveBtn) {
                window.settingSaveBtn.addEventListener('click', function() {
                    // Buton durumunu güncelle
                    const originalContent = window.settingSaveBtn.innerHTML;
                    window.settingSaveBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Kaydediliyor...';
                    window.settingSaveBtn.disabled = true;
                    
                    const formData = window.getFormJSON();
                    console.log('Form verisi kaydediliyor:', formData);
                    
                    // FormControllerComponent'a değeri kaydet
                    fetch(`/admin/settingmanagement/form-builder/${groupId}/save`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            layout: formData
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Buton durumunu geri yükle
                        window.settingSaveBtn.innerHTML = originalContent;
                        window.settingSaveBtn.disabled = false;
                        
                        // Toast mesajı göster
                        showToast(data.success ? 'success' : 'error', 
                                 data.success ? 'Başarılı!' : 'Hata!', 
                                 data.success ? 'Form yapısı başarıyla kaydedildi.' : data.error);
                    })
                    .catch(error => {
                        console.error('Kayıt hatası:', error);
                        
                        // Buton durumunu geri yükle
                        window.settingSaveBtn.innerHTML = originalContent;
                        window.settingSaveBtn.disabled = false;
                        
                        // Hata mesajını göster
                        showToast('error', 'Hata!', 'Form yapısı kaydedilirken bir hata oluştu.');
                    });
                });
            }
            
            // Toast mesajı gösterme fonksiyonu
            window.showToast = function(type, title, message) {
                const toastContainer = document.getElementById('toast-container');
                if (!toastContainer) return;
                
                const toast = document.createElement('div');
                toast.className = `toast show bg-${type === 'success' ? 'success' : 'danger'} text-white`;
                toast.setAttribute('role', 'alert');
                toast.setAttribute('aria-live', 'assertive');
                toast.setAttribute('aria-atomic', 'true');
                
                toast.innerHTML = `
                    <div class="toast-header bg-${type === 'success' ? 'success' : 'danger'} text-white">
                        <strong class="me-auto">${title}</strong>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Kapat"></button>
                    </div>
                    <div class="toast-body">
                        ${message}
                    </div>
                `;
                
                toastContainer.appendChild(toast);
                
                setTimeout(() => {
                    toast.classList.remove('show');
                    setTimeout(() => {
                        toast.remove();
                    }, 300);
                }, 3000);
                
                const closeBtn = toast.querySelector('.btn-close');
                if (closeBtn) {
                    closeBtn.addEventListener('click', function() {
                        toast.classList.remove('show');
                        setTimeout(() => {
                            toast.remove();
                        }, 300);
                    });
                }
            };
        }, 500); 
    });
</script>
@endpush