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
                            <span>Ayırıcı</span>
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
                <h3 class="card-title">
                    <i class="fas fa-edit me-2"></i>
                    Form Düzenleme
                </h3>
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

@push('scripts')
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
            
            // Livewire bileşenine gönder
            window.livewire.emit('saveFormLayout', groupId, JSON.stringify(formData));
        });
        
        // Kayıtlı form yapısını yükle
        fetch(`/admin/settingmanagement/form-builder/${groupId}/load`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
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
        
        // Form eleman arama işlevi
        const searchInput = document.getElementById('elements-search');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchText = this.value.toLowerCase();
                const elements = document.querySelectorAll('.element-palette-item');
                
                elements.forEach(element => {
                    const elementText = element.textContent.toLowerCase();
                    if (elementText.includes(searchText) || searchText === '') {
                        element.style.display = 'flex';
                    } else {
                        element.style.display = 'none';
                    }
                });
                
                // Eğer bir kategori tüm elemanları gizlenmişse, kategoriyi de gizle
                const categories = document.querySelectorAll('.block-category');
                categories.forEach(category => {
                    const visibleElements = category.querySelectorAll('.element-palette-item[style="display: flex;"]');
                    if (visibleElements.length === 0) {
                        category.style.display = 'none';
                    } else {
                        category.style.display = 'block';
                    }
                });
            });
        }
        
        // Kategori header tıklama olayı
        const categoryHeaders = document.querySelectorAll('.block-category-header');
        categoryHeaders.forEach(header => {
            header.addEventListener('click', function() {
                const category = this.closest('.block-category');
                category.classList.toggle('collapsed');
                
                // Kategori durumunu localStorage'a kaydet
                const categoryName = this.querySelector('span').textContent.trim();
                const categories = JSON.parse(localStorage.getItem('form_builder_categories') || '{}');
                categories[categoryName] = category.classList.contains('collapsed');
                localStorage.setItem('form_builder_categories', JSON.stringify(categories));
            });
        });
        
        // Panel toggle butonları için olay dinleyiciler
        const toggleButtons = document.querySelectorAll('.panel-toggle');
        toggleButtons.forEach(button => {
            button.addEventListener('click', function() {
                const panel = this.closest('.panel__left, .panel__right');
                panel.classList.toggle('collapsed');
                
                // Panel durumunu localStorage'a kaydet
                const panelSide = panel.classList.contains('panel__left') ? 'left' : 'right';
                localStorage.setItem(`form_builder_${panelSide}_collapsed`, panel.classList.contains('collapsed'));
            });
        });
    });
</script>
@endpush