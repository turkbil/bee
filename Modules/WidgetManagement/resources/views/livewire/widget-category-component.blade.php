@include('widgetmanagement::helper')
<div>
    <div class="card">
        <div class="card-body">
            <!-- Üst Kısım: Ekleme Formu -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-light">
                            <h3 class="card-title">Yeni Kategori Ekle</h3>
                        </div>
                        <div class="card-body">
                            <form wire:submit.prevent="quickAdd">
                                <div class="row g-3">
                                    <div class="col-md-5">
                                        <div class="form-floating">
                                            <input type="text" wire:model="title" 
                                                class="form-control @error('title') is-invalid @enderror" 
                                                placeholder="Kategori başlığı">
                                            <label>Kategori Başlığı</label>
                                            @error('title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-floating">
                                            <select wire:model="parentId" class="form-select">
                                                <option value="">Ana Kategori Olarak Ekle</option>
                                                @foreach($parentCategories as $parentCategory)
                                                <option value="{{ $parentCategory->widget_category_id }}">{{ $parentCategory->title }}</option>
                                                @endforeach
                                            </select>
                                            <label>Üst Kategori (İsteğe Bağlı)</label>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary h-100 w-100" wire:loading.attr="disabled">
                                            <span wire:loading.remove wire:target="quickAdd">
                                                <i class="fas fa-plus me-2"></i> Kategori Ekle
                                            </span>
                                            <span wire:loading wire:target="quickAdd">
                                                <i class="fas fa-spinner fa-spin me-2"></i> Ekleniyor...
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Listeleme Kısmı -->
            <div class="row mb-3">
                <!-- Sol Taraf (Arama ve Toplu İşlemler) -->
                <div class="col-md-6">
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                            placeholder="Aramak için yazmaya başlayın...">
                    </div>
                </div>
                
                <!-- Ortadaki Loading -->
                <div class="col-md-3 position-relative d-flex justify-content-center align-items-center">
                    <div wire:loading
                        wire:target="loadCategories, toggleActive, delete, quickAdd, updatedSearch, updateOrder, toggleExpand, expandAll, collapseAll"
                        class="position-absolute top-50 start-50 translate-middle text-center"
                        style="width: 100%; max-width: 250px;">
                        <div class="small text-muted mb-2">Güncelleniyor...</div>
                        <div class="progress mb-1">
                            <div class="progress-bar progress-bar-indeterminate"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Sağ Taraf (Butonlar) -->
                <div class="col-md-3">
                    <div class="btn-group w-100">
                        <button type="button" class="btn btn-outline-primary" wire:click="expandAll">
                            <i class="fas fa-chevron-down me-1"></i> Tümünü Aç
                        </button>
                        <button type="button" class="btn btn-outline-primary" wire:click="collapseAll">
                            <i class="fas fa-chevron-up me-1"></i> Tümünü Kapat
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Tablo Bölümü -->
            <div id="table-default" class="table-responsive">
                <table class="table table-vcenter card-table table-hover text-nowrap datatable">
                    <thead>
                        <tr>
                            <th style="width: 50px">
                                <div class="d-flex align-items-center gap-2">
                                    <input type="checkbox" wire:model.live="selectAll" class="form-check-input">
                                    <button
                                        class="table-sort {{ $sortField === 'widget_category_id' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                        wire:click="sortBy('widget_category_id')">
                                    </button>
                                </div>
                            </th>
                            <th>
                                <button
                                    class="table-sort {{ $sortField === 'title' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('title')">
                                    Kategori Adı
                                </button>
                            </th>
                            <th>
                                <button
                                    class="table-sort {{ $sortField === 'slug' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('slug')">
                                    Slug
                                </button>
                            </th>
                            <th style="width: 120px">
                                <button
                                    class="table-sort {{ $sortField === 'order' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('order')">
                                    Sıra
                                </button>
                            </th>
                            <th class="text-center" style="width: 80px">
                                <button
                                    class="table-sort {{ $sortField === 'is_active' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('is_active')">
                                    Durum
                                </button>
                            </th>
                            <th class="text-center" style="width: 120px">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody class="table-tbody" id="sortable-categories">
                        @forelse($categories as $category)
                            <!-- Ana Kategori Satırı -->
                            <tr class="hover-trigger sortable-item" data-id="{{ $category->widget_category_id }}">
                                <td class="sort-id small">
                                    <div class="hover-toggle">
                                        <span class="hover-hide">{{ $category->widget_category_id }}</span>
                                        <input type="checkbox" wire:model.live="selectedItems"
                                            value="{{ $category->widget_category_id }}" 
                                            class="form-check-input hover-show">
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($category->children && $category->children->count() > 0)
                                            <button wire:click="toggleExpand({{ $category->widget_category_id }})" class="btn btn-sm btn-ghost-secondary me-2">
                                                <i class="fas {{ isset($expandedCategories[$category->widget_category_id]) ? 'fa-chevron-down' : 'fa-chevron-right' }}"></i>
                                            </button>
                                        @else
                                            <span class="ps-4"></span>
                                        @endif
                                        
                                        <div class="d-flex align-items-center cursor-move category-title">
                                            <i class="fas fa-grip-vertical text-muted me-2"></i>
                                            @if($category->icon)
                                                <i class="fas {{ $category->icon }} me-2 text-primary"></i>
                                            @else
                                                <i class="fas fa-folder me-2 text-primary"></i>
                                            @endif
                                            <strong>{{ $category->title }}</strong>
                                            
                                            @if($category->widgets_count > 0)
                                                <span class="badge bg-blue-lt ms-2">{{ $category->widgets_count }}</span>
                                            @endif
                                            
                                            @if($category->description)
                                                <span class="text-muted ms-2 small">{{ Str::limit($category->description, 30) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-muted">
                                    {{ $category->slug }}
                                </td>
                                <td>
                                    <span class="badge bg-blue-lt">{{ $category->order }}</span>
                                </td>
                                <td class="text-center align-middle">
                                    <button wire:click="toggleActive({{ $category->widget_category_id }})"
                                        class="btn btn-icon btn-sm {{ $category->is_active ? 'text-muted bg-transparent' : 'text-red bg-transparent' }}">
                                        @if($category->is_active)
                                        <i class="fas fa-check"></i>
                                        @else
                                        <i class="fas fa-times"></i>
                                        @endif
                                    </button>
                                </td>
                                <td class="text-center align-middle">
                                    <div class="btn-list">
                                        <button wire:click="startEdit({{ $category->widget_category_id }})" class="btn btn-icon btn-sm">
                                            <i class="fa-solid fa-pen-to-square link-secondary"></i>
                                        </button>
                                        <button wire:click="delete({{ $category->widget_category_id }})" 
                                            class="btn btn-icon btn-sm"
                                            onclick="return confirm('Bu kategoriyi silmek istediğinize emin misiniz?')">
                                            <i class="fa-solid fa-trash link-danger"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Alt Kategoriler -->
                            @if(isset($expandedCategories[$category->widget_category_id]) && $category->children->count() > 0)
                                @foreach($category->children as $child)
                                <tr class="sortable-item child-item table-light" data-id="{{ $child->widget_category_id }}" data-parent-id="{{ $category->widget_category_id }}">
                                    <td class="sort-id small">
                                        <div class="hover-toggle">
                                            <span class="hover-hide">{{ $child->widget_category_id }}</span>
                                            <input type="checkbox" wire:model.live="selectedItems"
                                                value="{{ $child->widget_category_id }}" 
                                                class="form-check-input hover-show">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center ps-4">
                                            <div class="d-flex align-items-center cursor-move category-title">
                                                <i class="fas fa-grip-vertical text-muted me-2"></i>
                                                <i class="fas fa-level-down-alt fa-rotate-90 text-muted me-2"></i>
                                                @if($child->icon)
                                                    <i class="fas {{ $child->icon }} me-2 text-info"></i>
                                                @else
                                                    <i class="fas fa-folder me-2 text-info"></i>
                                                @endif
                                                <strong>{{ $child->title }}</strong>
                                                
                                                @if($child->widgets_count > 0)
                                                    <span class="badge bg-blue-lt ms-2">{{ $child->widgets_count }}</span>
                                                @endif
                                                
                                                @if($child->description)
                                                    <span class="text-muted ms-2 small">{{ Str::limit($child->description, 30) }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-muted">
                                        {{ $child->slug }}
                                    </td>
                                    <td>
                                        <span class="badge bg-green-lt">{{ $child->order }}</span>
                                    </td>
                                    <td class="text-center align-middle">
                                        <button wire:click="toggleActive({{ $child->widget_category_id }})"
                                            class="btn btn-icon btn-sm {{ $child->is_active ? 'text-muted bg-transparent' : 'text-red bg-transparent' }}">
                                            @if($child->is_active)
                                            <i class="fas fa-check"></i>
                                            @else
                                            <i class="fas fa-times"></i>
                                            @endif
                                        </button>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="btn-list">
                                            <button wire:click="startEdit({{ $child->widget_category_id }})" class="btn btn-icon btn-sm">
                                                <i class="fa-solid fa-pen-to-square link-secondary"></i>
                                            </button>
                                            <button wire:click="delete({{ $child->widget_category_id }})" 
                                                class="btn btn-icon btn-sm"
                                                onclick="return confirm('Bu kategoriyi silmek istediğinize emin misiniz?')">
                                                <i class="fa-solid fa-trash link-danger"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            @endif
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="empty">
                                        <div class="empty-img">
                                            <i class="fas fa-folder-open fa-4x text-muted"></i>
                                        </div>
                                        <p class="empty-title mt-2">Kategori bulunamadı</p>
                                        <p class="empty-subtitle text-muted">
                                            Arama kriterinize uygun kategori bulunamadı veya henüz kategori eklenmemiş.
                                        </p>
                                        <div class="empty-action">
                                            @if(!empty($search))
                                                <button wire:click="$set('search', '')" class="btn btn-primary">
                                                    <i class="fas fa-times me-1"></i> Aramayı Temizle
                                                </button>
                                            @else
                                                <div class="text-muted">Yukarıdaki formu kullanarak yeni kategori ekleyebilirsiniz.</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Düzenleme Modalı -->
    @if($editCategoryId)
    <div class="modal modal-blur show" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Kategori Düzenle</h5>
                    <button type="button" class="btn-close" wire:click="cancelEdit"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">Kategori Başlığı</label>
                        <input type="text" 
                            wire:model="editData.title" 
                            class="form-control @error('editData.title') is-invalid @enderror"
                            placeholder="Kategori adını girin">
                        @error('editData.title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text" 
                            wire:model="editData.slug" 
                            class="form-control @error('editData.slug') is-invalid @enderror"
                            placeholder="kategori-slug">
                        @error('editData.slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-hint">Boş bırakırsanız otomatik oluşturulur</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Üst Kategori</label>
                        <select 
                            wire:model="editData.parent_id" 
                            class="form-select">
                            <option value="">Ana Kategori Olarak Ekle</option>
                            @foreach($parentCategories as $parent)
                                @if($parent->widget_category_id != $editCategoryId)
                                <option value="{{ $parent->widget_category_id }}">{{ $parent->title }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Açıklama</label>
                        <textarea 
                            wire:model="editData.description" 
                            class="form-control" 
                            rows="3"
                            placeholder="Kategori açıklaması"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">İkon</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="{{ $editData['icon'] ?: 'fa-solid fa-folder' }}"></i>
                            </span>
                            <input type="text" 
                                wire:model="editData.icon" 
                                class="form-control"
                                placeholder="fa-folder">
                        </div>
                        <small class="form-hint">FontAwesome ikon kodu (örn: fa-folder)</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-check form-switch">
                            <input type="checkbox" wire:model="editData.is_active" class="form-check-input">
                            <span class="form-check-label">Aktif</span>
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" wire:click="cancelEdit">
                        İptal
                    </button>
                    <button type="button" class="btn btn-primary" wire:click="saveEdit">
                        Kaydet
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Toplu İşlemler Paneli -->
    @if(count($selectedItems) > 0)
    <div class="bulk-actions-panel">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-auto">
                    <span class="bulk-count">{{ count($selectedItems) }} kategori seçildi</span>
                </div>
                <div class="col text-end">
                    <div class="btn-group">
                        <button type="button" class="btn btn-success" wire:click="bulkToggleActive(true)">
                            <i class="fas fa-check me-1"></i> Aktif Yap
                        </button>
                        <button type="button" class="btn btn-warning" wire:click="bulkToggleActive(false)">
                            <i class="fas fa-times me-1"></i> Pasif Yap
                        </button>
                        <button type="button" class="btn btn-danger" wire:click="bulkDelete"
                            onclick="return confirm('Seçili kategorileri silmek istediğinize emin misiniz?')">
                            <i class="fas fa-trash me-1"></i> Sil
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('styles')
<style>
    .bulk-actions-panel {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background-color: rgba(0,0,0,0.8);
        color: white;
        padding: 15px;
        z-index: 1000;
        box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
    }
    
    .bulk-count {
        font-weight: bold;
        font-size: 16px;
    }
    
    .hover-trigger .hover-hide {
        display: inline;
    }
    
    .hover-trigger .hover-show {
        display: none;
    }
    
    .hover-trigger:hover .hover-hide {
        display: none;
    }
    
    .hover-trigger:hover .hover-show {
        display: inline-block;
    }
    
    .child-item {
        background-color: rgba(32, 107, 196, 0.03) !important;
    }
    
    .cursor-move {
        cursor: move;
    }
    
    .category-title {
        user-select: none;
    }
    
    /* Sürüklerken stil */
    .sortable-ghost {
        background-color: #e9ecef !important;
        opacity: 0.8;
    }
    
    /* Alt öğe hoverda ikon göster */
    .category-title .fa-grip-vertical {
        opacity: 0.3;
    }
    
    .category-title:hover .fa-grip-vertical {
        opacity: 1;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('admin/libs/sortable/sortable.min.js') }}"></script>
<script>
document.addEventListener('livewire:initialized', function() {
    initSortable();
    
    Livewire.hook('morph.updated', () => {
        initSortable();
    });
    
    function initSortable() {
        const container = document.getElementById('sortable-categories');
        if (!container) return;
        
        // Mevcut sıralayıcıyı temizle (eğer varsa)
        if (window.categorySortable) {
            window.categorySortable.destroy();
            window.categorySortable = null;
        }
        
        window.categorySortable = new Sortable(container, {
            animation: 150,
            handle: '.category-title',
            ghostClass: 'sortable-ghost',
            group: 'categories',
            fallbackOnBody: true,
            swapThreshold: 0.65,
            onEnd: function(evt) {
                // Eğer bir kategori taşındıysa
                if (evt.oldIndex !== evt.newIndex || evt.from !== evt.to) {
                    // Tüm kategorileri toplayıp sıralayalım
                    const items = [];
                    const rows = container.querySelectorAll('tr.sortable-item');
                    
                    let order = 1;
                    let currentParentId = null;
                    
                    rows.forEach(row => {
                        const id = row.getAttribute('data-id');
                        const isChild = row.classList.contains('child-item');
                        
                        // Eğer ana kategori ise (child değilse), parent ID null olmalı
                        if (!isChild) {
                            currentParentId = null;
                            items.push({
                                id: id,
                                order: order++,
                                parentId: null
                            });
                        } else {
                            // Eğer alt kategori ise, data-parent-id'yi veya en son ana kategori ID'sini kullan
                            const parentId = row.getAttribute('data-parent-id') || currentParentId;
                            items.push({
                                id: id,
                                order: order++,
                                parentId: parentId
                            });
                        }
                    });
                    
                    // Livewire'a gönder
                    if (items.length > 0) {
                        @this.updateOrder(items);
                    }
                }
            }
        });
    }
});
</script>
@endpush