@include('settingmanagement::helper')

@push('styles')
<link rel="stylesheet" href="{{ asset('admin/libs/form-builder/css/form-builder.css') }}">
@endpush

<div>
    <!-- Form Builder Header -->
    <div class="studio-header">
        <div class="header-left">
            <div class="btn-group btn-group-sm me-4">
                <button id="device-desktop" class="btn btn-light btn-sm active" title="Masaüstü">
                    <i class="fas fa-desktop"></i>
                </button>
                <button id="device-tablet" class="btn btn-light btn-sm" title="Tablet">
                    <i class="fas fa-tablet-alt"></i>
                </button>
                <button id="device-mobile" class="btn btn-light btn-sm" title="Mobil">
                    <i class="fas fa-mobile-alt"></i>
                </button>
            </div>

            <div class="btn-group btn-group-sm me-4">
                <button id="sw-visibility" class="btn btn-light btn-sm" title="Bileşen sınırlarını göster/gizle">
                    <i class="fas fa-border-all"></i>
                </button>

                <button id="cmd-clear" class="btn btn-light btn-sm" title="İçeriği temizle">
                    <i class="fas fa-trash-alt"></i>
                </button>

                <button id="cmd-undo" class="btn btn-light btn-sm" title="Geri al">
                    <i class="fas fa-undo"></i>
                </button>

                <button id="cmd-redo" class="btn btn-light btn-sm" title="Yinele">
                    <i class="fas fa-redo"></i>
                </button>
            </div>
        </div>

        <div class="header-center">
            <div class="studio-brand">
                {{ $group->name }} <i class="fa-solid fa-wand-magic-sparkles mx-2"></i> Form Düzenleyici
            </div>
        </div>

        <div class="header-right">
            <a href="{{ route('admin.settingmanagement.index') }}" id="back-btn" class="btn btn-light btn-sm me-2" title="Geri">
                <i class="fa-solid fa-arrow-left me-1"></i>
                <span>Geri Dön</span>
            </a>
            
            <button id="preview-btn" class="btn btn-light btn-sm me-2" title="Önizleme">
                <i class="fa-solid fa-eye me-1"></i>
                <span>Önizleme</span>
            </button>

            <button id="save-btn" class="btn btn-primary btn-sm" title="Kaydet">
                <i class="fa-solid fa-save me-1"></i>
                <span>Kaydet</span>
            </button>
        </div>
    </div>

    <div class="editor-main">
        <!-- Sol Panel: Form Elemanları -->
        <div class="panel__left">
            <div class="panel-tabs">
                <div class="panel-tab active" data-tab="elements">
                    <div class="tab-icon-container">
                        <i class="fa fa-cubes tab-icon"></i>
                    </div>
                    <span class="tab-text">Elemanlar</span>
                </div>
            </div>
            
            <!-- Elemanlar Sekmesi İçeriği -->
            <div class="panel-tab-content active" data-tab-content="elements">
                <div class="blocks-search">
                    <input type="text" id="elements-search" class="form-control form-control-sm" placeholder="Eleman ara...">
                </div>
                
                <div id="element-palette">
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
                        </div>
                    </div>
                    
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
                        </div>
                    </div>
                    
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
                </div>
            </div>
            
            <div class="panel-toggle">
                <i class="fas fa-chevron-left"></i>
            </div>
        </div>
        
        <!-- Orta Panel: Form Canvas -->
        <div class="form-canvas">
            <div class="card shadow-sm w-100" style="max-width: 800px;">
                <div class="card-header">
                    <h3 class="card-title">Form Düzenleme</h3>
                </div>
                <div class="card-body p-0">
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
            <div class="panel-tabs">
                <div class="panel-tab active" data-tab="properties">
                    <div class="tab-icon-container">
                        <i class="fa fa-sliders-h tab-icon"></i>
                    </div>
                    <span class="tab-text">Özellikler</span>
                </div>
            </div>
            
            <!-- Özellikler Sekmesi İçeriği -->
            <div class="panel-tab-content active" data-tab-content="properties">
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
            
            <div class="panel-toggle">
                <i class="fas fa-chevron-right"></i>
            </div>
        </div>
    </div>

    <input type="hidden" id="group-id" value="{{ $group->id }}">
</div>

@push('scripts')
<script src="{{ asset('admin/libs/form-builder/js/form-builder.js') }}"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Grup ID'sini al
        const groupId = document.getElementById('group-id').value;
        
        // Form JSON'ını yükleyerek canvasa render eder
        function loadFormFromJSON(formData) {
            if (!formData || !formData.elements || !formData.elements.length) {
                return;
            }
            
            // Canvas'ı temizle
            formCanvas.innerHTML = '';
            emptyCanvas.style.display = 'none';
            
            // Form elemanlarını oluştur ve canvas'a ekle
            formData.elements.forEach(element => {
                if (element.type === 'row') {
                    // Row tipinde eleman
                    const rowElement = createFormElement('row', element.properties);
                    formCanvas.appendChild(rowElement);
                    
                    // Row içindeki sütunları doldur
                    if (element.columns && element.columns.length) {
                        const rowContent = rowElement.querySelector('.row-element');
                        const columnElements = rowElement.querySelectorAll('.column-element');
                        
                        element.columns.forEach((column, columnIndex) => {
                            if (columnIndex < columnElements.length) {
                                // Sütun genişliğini ayarla
                                columnElements[columnIndex].dataset.width = column.width;
                                columnElements[columnIndex].className = columnElements[columnIndex].className.replace(/col-md-\d+/, `col-md-${column.width}`);
                                
                                // Placeholder'ı kaldır
                                const placeholder = columnElements[columnIndex].querySelector('.column-placeholder');
                                if (placeholder) {
                                    placeholder.remove();
                                }
                                
                                // Sütun içindeki elementleri oluştur
                                if (column.elements && column.elements.length) {
                                    column.elements.forEach(item => {
                                        const columnItem = createFormElement(item.type, item.properties);
                                        if (columnItem) {
                                            columnElements[columnIndex].appendChild(columnItem);
                                        }
                                    });
                                }
                            }
                        });
                    }
                } else {
                    // Normal eleman
                    const formElement = createFormElement(element.type, element.properties);
                    if (formElement) {
                        formCanvas.appendChild(formElement);
                    }
                }
            });
            
            // Eğer form boşsa, boş canvas göster
            checkEmptyCanvas();
            
            // SortableJS'yi yeniden başlat
            initializeSortable();
            
            // İlk durumu kaydet
            saveState();
        }
        
        // Kaydet butonu için olay dinleyici ekle
        document.getElementById('save-btn').addEventListener('click', function() {
            const formData = getFormJSON(); // form-builder.js'den geliyor
            
            // AJAX isteği ile sunucuya gönder
            fetch(`/admin/settingmanagement/form-builder/${groupId}/save`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    formData: formData
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Başarılı bildirim göster
                    const toast = document.createElement("div");
                    toast.className = "toast position-fixed bottom-0 end-0 m-3 bg-success text-white show";
                    toast.setAttribute("role", "alert");
                    toast.innerHTML = `
                        <div class="toast-header bg-success text-white">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong class="me-auto">Başarılı</strong>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                        </div>
                        <div class="toast-body">
                            ${data.message}
                        </div>
                    `;
                    document.body.appendChild(toast);
                    
                    // 3 saniye sonra toast'ı kaldır
                    setTimeout(() => {
                        toast.remove();
                    }, 3000);
                }
            })
            .catch(error => {
                console.error('Kayıt hatası:', error);
                
                // Hata bildirimi göster
                const toast = document.createElement("div");
                toast.className = "toast position-fixed bottom-0 end-0 m-3 bg-danger text-white show";
                toast.setAttribute("role", "alert");
                toast.innerHTML = `
                    <div class="toast-header bg-danger text-white">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong class="me-auto">Hata</strong>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                    </div>
                    <div class="toast-body">
                        Form kaydedilirken bir hata oluştu.
                    </div>
                `;
                document.body.appendChild(toast);
                
                // 3 saniye sonra toast'ı kaldır
                setTimeout(() => {
                    toast.remove();
                }, 3000);
            });
        });
        
        // Kayıtlı form yapısını yükle
        fetch(`/admin/settingmanagement/form-builder/${groupId}/load`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.layout) {
                loadFormFromJSON(data.layout);
            }
        })
        .catch(error => {
            console.error('Form yükleme hatası:', error);
        });
    });
</script>
@endpush