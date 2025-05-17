@include('settingmanagement::helper')
<div>
    <div class="card">
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between gap-3">
                <div class="input-icon flex-grow-1">
                    <span class="input-icon-addon">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                        placeholder="Grup ara...">
                </div>
                <a href="{{ route('admin.settingmanagement.group.manage') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Yeni Grup Ekle
                </a>
            </div>
        </div>

        <div class="card-body">
            <div class="row row-cards">
                @forelse($groups as $group)
                <div class="col-md-6 col-lg-4">
                    <div class="card">
                        <div class="card bg-muted-lt">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="avatar bg-primary-lt me-2">
                                        <i class="{{ $group->icon ?? 'fas fa-folder' }} {{ !$group->is_active ? 'text-danger' : '' }}"></i>
                                    </div>
                                    <div>
                                        <h3 class="card-title mb-0 d-flex align-items-center">
                                            {{ $group->name }}
                                            @if(!$group->is_active)
                                            <span class="badge bg-danger text-white ms-2">Pasif</span>
                                            @endif
                                        </h3>
                                        @if($group->description)
                                        <small class="text-muted">{{ Str::limit($group->description, 50) }}</small>
                                        @endif
                                    </div>
                                    <div class="ms-auto">
                                        <div class="dropdown">
                                            <a href="#" class="btn btn-icon" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a href="{{ route('admin.settingmanagement.group.manage', $group->id) }}"
                                                    class="dropdown-item">
                                                    <i class="fas fa-edit me-2"></i> Düzenle
                                                </a>
                                                <a href="{{ route('admin.settingmanagement.group.manage', ['parent_id' => $group->id]) }}"
                                                    class="dropdown-item">
                                                    <i class="fas fa-plus me-2"></i> Alt Grup Ekle
                                                </a>
                                                <button wire:click="toggleActive({{ $group->id }})"
                                                    class="dropdown-item">
                                                    <i class="fas fa-{{ $group->is_active ? 'ban' : 'check' }} me-2"></i>
                                                    {{ $group->is_active ? 'Pasif Yap' : 'Aktif Yap' }}
                                                </button>
                                                @if($group->children->isEmpty())
                                                <button wire:click="delete({{ $group->id }})"
                                                    wire:confirm="Bu grubu silmek istediğinize emin misiniz?"
                                                    class="dropdown-item text-danger">
                                                    <i class="fas fa-trash me-2"></i> Sil
                                                </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($group->children->isNotEmpty())
                        <div class="list-group list-group-flush">
                            @foreach($group->children as $child)
                            <div class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <span class="avatar avatar-sm bg-primary-lt">
                                            <i class="{{ $child->icon ?? 'fas fa-circle' }} {{ !$child->is_active ? 'text-danger' : '' }}"></i>
                                        </span>
                                    </div>
                                    <div class="col">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-fill">
                                                <div class="font-weight-medium d-flex align-items-center"> 
                                                    <a href="{{ route('admin.settingmanagement.values', $child->id) }}"
                                                        class="text-reset">
                                                        {{ $child->name }}
                                                    </a>
                                                    @if(!$child->is_active)
                                                    <span class="badge bg-danger text-white ms-2">Pasif</span>
                                                    @endif
                                                </div>
                                                @if($child->description)
                                                <div class="text-muted small">{{ Str::limit($child->description, 40) }}
                                                </div>
                                                @endif
                                            </div>
                                            <div class="d-flex align-items-center justify-content-end">
                                                <span class="badge bg-primary text-white text-center align-middle d-flex align-items-center justify-content-center" style="min-width: 2.5rem; padding: 0.35rem 0.5rem;">
                                                    {{ $child->settings->count() }}
                                                </span>
                                                <div class="dropdown ms-2">
                                                    <a href="#" class="btn btn-icon" data-bs-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a href="{{ route('admin.settingmanagement.items', $child->id) }}" class="dropdown-item">
                                                            <i class="fas fa-edit me-2"></i> Ayarları Yapılandır
                                                        </a>
                                                        <a href="{{ route('admin.settingmanagement.group.manage', $child->id) }}" class="dropdown-item">
                                                            <i class="fas fa-edit me-2"></i> Düzenle
                                                        </a>
                                                        <a href="{{ route('admin.settingmanagement.form-builder.edit', $child->id) }}" class="dropdown-item">
                                                            <i class="fas fa-magic me-2"></i> Form Builder
                                                        </a>
                                                        <button wire:click="toggleActive({{ $child->id }})" class="dropdown-item">
                                                            <i class="fas fa-{{ $child->is_active ? 'ban' : 'check' }} me-2"></i>
                                                            {{ $child->is_active ? 'Pasif Yap' : 'Aktif Yap' }}
                                                        </button>
                                                        @if($child->children->isEmpty())
                                                        <button wire:click="delete({{ $child->id }})" wire:confirm="Bu alt grubu silmek istediğinize emin misiniz?" class="dropdown-item text-danger">
                                                            <i class="fas fa-trash me-2"></i> Sil
                                                        </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="card-footer">
                            <div class="d-flex align-items-center">
                                <div>
                                    <div class="text-muted">{{ $group->children->count() }} alt grup</div>
                                </div>
                                <div class="ms-auto">
                                    <a href="{{ route('admin.settingmanagement.group.manage', ['parent_id' => $group->id]) }}"
                                        class="btn btn-link btn-sm">
                                        Alt Grup Ekle
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="empty">
                        <div class="empty-icon">
                            <i class="fas fa-layer-group fa-3x text-muted"></i>
                        </div>
                        <p class="empty-title">Henüz grup eklenmemiş</p>
                        <p class="empty-subtitle text-muted">
                            Yeni gruplar ekleyerek ayarlarınızı düzenlemeye başlayabilirsiniz.
                        </p>
                        <div class="empty-action">
                            <a href="{{ route('admin.settingmanagement.group.manage') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>
                                Yeni Grup Ekle
                            </a>
                        </div>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>
    
    <!-- Form Builder Modal -->
    @if($formBuilderOpen)
    <div class="modal modal-blur fade show" id="formBuilderModal" tabindex="-1" role="dialog" aria-modal="true" style="display: block; padding-right: 15px;">
        <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $selectedGroup ? $selectedGroup->name : '' }} - Form Builder</h5>
                    <button type="button" class="btn-close" wire:click="closeFormBuilder"></button>
                </div>
                <div class="modal-body p-0">
                    @if($selectedGroup)
                    <div class="form-builder-container">
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
                                    {{ $selectedGroup->name }} <i class="fa-solid fa-wand-magic-sparkles mx-2"></i> Form Düzenleyici
                                </div>
                            </div>

                            <div class="header-right">
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

                        <input type="hidden" id="group-id" value="{{ $selectedGroup->id }}">
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary me-auto" wire:click="closeFormBuilder">
                        İptal
                    </button>
                    <button type="button" class="btn btn-primary" id="modal-save-btn">
                        <i class="fa-solid fa-save me-1"></i> Kaydet
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
    @endif
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('admin/libs/form-builder/css/form-builder.css') }}">
<style>
    .modal-fullscreen .modal-dialog {
        max-width: 100%;
        margin: 0;
        height: 100vh;
    }
    
    .modal-fullscreen .modal-content {
        height: 100%;
        border: 0;
        border-radius: 0;
    }
    
    .form-builder-container {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 130px);
    }
    
    .editor-main {
        flex: 1;
        overflow: hidden;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('admin/libs/form-builder/js/form-builder.js') }}"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        function initFormBuilder() {
            if (!document.getElementById('group-id')) return;
            
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
            
            // Modal içindeki kaydet butonu için olay dinleyici ekle
            document.getElementById('modal-save-btn').addEventListener('click', function() {
                // Form Builder'dan form verilerini al
                const formData = getFormJSON();
                
                // Livewire bileşenine veriyi gönder
                Livewire.dispatch('saveFormLayout', { groupId: groupId, formData: JSON.stringify(formData) });
            });
            
            // Grup verilerini yükle
            fetch(`/admin/settingmanagement/api/groups/${groupId}/layout`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.layout) {
                        loadFormFromJSON(data.layout);
                    }
                })
                .catch(error => {
                    console.error('Form yükleme hatası:', error);
                });
        }
        
        // Livewire hookları
        Livewire.hook('message.processed', (message, component) => {
            if (document.getElementById('formBuilderModal') && document.getElementById('formBuilderModal').classList.contains('show')) {
                initFormBuilder();
            }
        });
    });
</script>
@endpush