@include('widgetmanagement::helper')
<div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <!-- Sol Taraf: Ekleme/Düzenleme Formu -->
                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h3 class="card-title">{{ $editCategoryId ? 'Kategori Düzenle' : 'Yeni Kategori Ekle' }}</h3>
                            <div class="card-actions">
                                <span class="badge bg-purple-lt">
                                    <i class="fas fa-code me-1"></i> Debug
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <form wire:submit.prevent="{{ $editCategoryId ? 'saveEdit' : 'quickAdd' }}">
                                <div class="mb-3">
                                    <label class="form-label required">Kategori Başlığı</label>
                                    @if($editCategoryId)
                                        <input type="text" 
                                            wire:model="editData.title" 
                                            class="form-control @error('editData.title') is-invalid @enderror" 
                                            placeholder="Kategori başlığı">
                                        @error('editData.title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    @else
                                        <input type="text" 
                                            wire:model="title" 
                                            class="form-control @error('title') is-invalid @enderror" 
                                            placeholder="Kategori başlığı">
                                        @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    @endif
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Slug</label>
                                    @if($editCategoryId)
                                        <input type="text" 
                                            wire:model="editData.slug" 
                                            class="form-control @error('editData.slug') is-invalid @enderror"
                                            placeholder="kategori-slug">
                                        @error('editData.slug')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    @else
                                        <input type="text" 
                                            wire:model="slug" 
                                            class="form-control @error('slug') is-invalid @enderror"
                                            placeholder="kategori-slug">
                                        @error('slug')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    @endif
                                    <small class="form-hint">Boş bırakırsanız otomatik oluşturulur</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Üst Kategori</label>
                                    @if($editCategoryId)
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
                                    @else
                                        <select 
                                            wire:model="parentId" 
                                            class="form-select">
                                            <option value="">Ana Kategori Olarak Ekle</option>
                                            @foreach($parentCategories as $parent)
                                                <option value="{{ $parent->widget_category_id }}">{{ $parent->title }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Açıklama</label>
                                    @if($editCategoryId)
                                        <textarea 
                                            wire:model="editData.description" 
                                            class="form-control" 
                                            rows="3"
                                            placeholder="Kategori açıklaması"></textarea>
                                    @else
                                        <textarea 
                                            wire:model="description" 
                                            class="form-control" 
                                            rows="3"
                                            placeholder="Kategori açıklaması"></textarea>
                                    @endif
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">İkon</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="{{ $editCategoryId ? $editData['icon'] : ($icon ?? 'fa-solid fa-folder') }}"></i>
                                        </span>
                                        @if($editCategoryId)
                                            <input type="text" 
                                                wire:model="editData.icon" 
                                                class="form-control"
                                                placeholder="fa-folder">
                                        @else
                                            <input type="text" 
                                                wire:model="icon" 
                                                class="form-control"
                                                placeholder="fa-folder">
                                        @endif
                                    </div>
                                    <small class="form-hint">FontAwesome ikon kodu (örn: fa-folder)</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-check form-switch">
                                        @if($editCategoryId)
                                            <input type="checkbox" wire:model="editData.is_active" class="form-check-input">
                                        @else
                                            <input type="checkbox" wire:model="is_active" class="form-check-input">
                                        @endif
                                        <span class="form-check-label">Aktif</span>
                                    </label>
                                </div>
                                
                                <div class="d-flex justify-content-between">
                                    @if($editCategoryId)
                                    <button type="button" class="btn btn-outline-secondary" wire:click="cancelEdit">
                                        <i class="fas fa-times me-1"></i> İptal
                                    </button>
                                    @endif
                                    <button type="submit" class="btn btn-primary ms-auto" wire:loading.attr="disabled">
                                        <i class="fas fa-save me-1"></i> {{ $editCategoryId ? 'Güncelle' : 'Ekle' }}
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Debug Bilgileri -->
                        <div class="card-footer text-end">
                            <span class="small text-muted">
                                İçerikler
                                <span class="badge bg-blue">{{ isset($categories) ? $categories->sum('widgets_count') : 0 }} içerik</span>
                                <span class="badge bg-green">{{ isset($categories) ? $categories->count() : 0 }} kategori</span>
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Sağ Taraf: Kategori Listesi (Sürükle Bırak) -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <h3 class="card-title">Kategoriler</h3>
                                <div class="ms-auto d-flex gap-2">
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <i class="fas fa-search"></i>
                                        </span>
                                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                                            placeholder="Aramak için yazmaya başlayın...">
                                    </div>
                                    <div>
                                        <!-- Debug Rozeti -->
                                        <span class="badge bg-yellow-lt">
                                            <i class="fas fa-bug me-1"></i> Mode: {{ $editCategoryId ? 'Edit' : 'Create' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <!-- Loading Spinner -->
                            <div wire:loading wire:target="loadCategories, toggleActive, delete, quickAdd, saveEdit, updatedSearch, updateOrder"
                                class="text-center my-3">
                                <div class="spinner-border text-primary" role="status"></div>
                                <p class="text-muted mt-2">Yükleniyor...</p>
                            </div>
                            
                            <!-- Kategori Listesi (Tablo) -->
                            <div wire:loading.remove wire:target="loadCategories, toggleActive, delete, quickAdd, saveEdit, updatedSearch, updateOrder">
                                <div class="list-group list-group-flush" id="sortable-list">
                                    @forelse($categories as $category)
                                        <div class="list-group-item p-2" 
                                            wire:key="category-{{ $category->widget_category_id }}" 
                                            id="item-{{ $category->widget_category_id }}" 
                                            data-id="{{ $category->widget_category_id }}">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="d-flex align-items-center">
                                                    <div class="cursor-move me-2">
                                                        <i class="fas fa-grip-vertical text-muted"></i>
                                                    </div>
                                                    @if($category->icon)
                                                        <span class="me-2"><i class="fas {{ $category->icon }}"></i></span>
                                                    @else
                                                        <span class="me-2"><i class="fas fa-folder"></i></span>
                                                    @endif
                                                    <div>
                                                        <div class="fw-bold">{{ $category->title }}</div>
                                                        <span class="badge bg-blue-lt">{{ $category->widgets_count ?? 0 }} içerik</span>
                                                        @if($category->children && $category->children->count() > 0)
                                                            <span class="badge bg-green-lt">{{ $category->children->count() }} alt kategori</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="d-flex">
                                                    <!-- Debug Rozeti -->
                                                    <span class="badge bg-muted-lt me-2">ID: {{ $category->widget_category_id }}</span>
                                                    
                                                    <button wire:click="toggleActive({{ $category->widget_category_id }})"
                                                        class="btn btn-icon btn-sm {{ $category->is_active ? 'text-muted bg-transparent' : 'text-red bg-transparent' }}"
                                                        data-bs-toggle="tooltip" title="{{ $category->is_active ? 'Pasif Yap' : 'Aktif Yap' }}">
                                                        <div wire:loading wire:target="toggleActive({{ $category->widget_category_id }})"
                                                            class="spinner-border spinner-border-sm">
                                                        </div>
                                                        <div wire:loading.remove wire:target="toggleActive({{ $category->widget_category_id }})">
                                                            @if($category->is_active)
                                                            <i class="fas fa-check"></i>
                                                            @else
                                                            <i class="fas fa-times"></i>
                                                            @endif
                                                        </div>
                                                    </button>
                                                    <a href="javascript:void(0);" wire:click="startEdit({{ $category->widget_category_id }})" 
                                                       class="btn btn-icon btn-sm"
                                                       data-bs-toggle="tooltip" title="Düzenle">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="javascript:void(0);" wire:click="delete({{ $category->widget_category_id }})" 
                                                       class="btn btn-icon btn-sm text-danger"
                                                       data-bs-toggle="tooltip" title="Sil"
                                                       onclick="return confirm('Bu kategoriyi silmek istediğinize emin misiniz?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Alt Kategoriler -->
                                        @if($category->children && $category->children->count() > 0)
                                            @foreach($category->children as $child)
                                                <div class="list-group-item ps-5 p-2" 
                                                    wire:key="child-{{ $child->widget_category_id }}" 
                                                    id="item-{{ $child->widget_category_id }}" 
                                                    data-id="{{ $child->widget_category_id }}"
                                                    data-parent-id="{{ $category->widget_category_id }}">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div class="d-flex align-items-center">
                                                            <div class="cursor-move me-2">
                                                                <i class="fas fa-grip-vertical text-muted"></i>
                                                            </div>
                                                            @if($child->icon)
                                                                <span class="me-2"><i class="fas {{ $child->icon }}"></i></span>
                                                            @else
                                                                <span class="me-2"><i class="fas fa-folder"></i></span>
                                                            @endif
                                                            <div>
                                                                <div class="fw-bold">{{ $child->title }}</div>
                                                                <span class="badge bg-blue-lt">{{ $child->widgets_count ?? 0 }} içerik</span>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex">
                                                            <!-- Debug Rozeti -->
                                                            <span class="badge bg-muted-lt me-2">
                                                                ID: {{ $child->widget_category_id }} | Parent: {{ $child->parent_id }}
                                                            </span>
                                                        
                                                            <button wire:click="toggleActive({{ $child->widget_category_id }})"
                                                                class="btn btn-icon btn-sm {{ $child->is_active ? 'text-muted bg-transparent' : 'text-red bg-transparent' }}"
                                                                data-bs-toggle="tooltip" title="{{ $child->is_active ? 'Pasif Yap' : 'Aktif Yap' }}">
                                                                <div wire:loading wire:target="toggleActive({{ $child->widget_category_id }})"
                                                                    class="spinner-border spinner-border-sm">
                                                                </div>
                                                                <div wire:loading.remove wire:target="toggleActive({{ $child->widget_category_id }})">
                                                                    @if($child->is_active)
                                                                    <i class="fas fa-check"></i>
                                                                    @else
                                                                    <i class="fas fa-times"></i>
                                                                    @endif
                                                                </div>
                                                            </button>
                                                            <a href="javascript:void(0);" wire:click="startEdit({{ $child->widget_category_id }})" 
                                                               class="btn btn-icon btn-sm"
                                                               data-bs-toggle="tooltip" title="Düzenle">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <a href="javascript:void(0);" wire:click="delete({{ $child->widget_category_id }})" 
                                                               class="btn btn-icon btn-sm text-danger"
                                                               data-bs-toggle="tooltip" title="Sil"
                                                               onclick="return confirm('Bu kategoriyi silmek istediğinize emin misiniz?')">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    @empty
                                        <div class="list-group-item py-4">
                                            <div class="empty">
                                                <div class="empty-img">
                                                    <i class="fas fa-folder-open fa-4x text-muted"></i>
                                                </div>
                                                <p class="empty-title mt-2">Kategori bulunamadı</p>
                                                <p class="empty-subtitle text-muted">
                                                    @if(!empty($search))
                                                        Arama kriterinize uygun kategori bulunamadı.
                                                    @else
                                                        Henüz kategori eklenmemiş. Sol taraftaki formu kullanarak yeni bir kategori ekleyebilirsiniz.
                                                    @endif
                                                </p>
                                                @if(!empty($search))
                                                    <div class="empty-action">
                                                        <button wire:click="$set('search', '')" class="btn btn-primary">
                                                            <i class="fas fa-times me-1"></i> Aramayı Temizle
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        <!-- Debug Bilgileri Footer -->
                        <div class="card-footer p-2">
                            <div class="d-flex justify-content-end">
                                <div class="small text-muted">
                                    <span class="badge bg-cyan-lt me-1">
                                        <i class="fas fa-info-circle me-1"></i> Üst Kategori: {{ count($parentCategories) }}
                                    </span>
                                    <span class="badge bg-teal-lt me-1">
                                        <i class="fas fa-sort me-1"></i> Sıralama: {{ $sortField }}/{{ $sortDirection }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .cursor-move {
        cursor: move;
    }
    
    /* Sürüklerken stil */
    .sortable-ghost {
        background-color: #e9ecef !important;
        opacity: 0.8;
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
        const container = document.getElementById('sortable-list');
        if (!container) {
            console.log('Sortable listesi bulunamadı');
            return;
        }
        
        // Mevcut sortable'ı temizle
        if (window.categorySortable) {
            window.categorySortable.destroy();
            window.categorySortable = null;
        }
        
        // Yeni sortable oluştur
        window.categorySortable = new Sortable(container, {
            animation: 150,
            ghostClass: 'sortable-ghost',
            handle: '.cursor-move',
            onStart: function(evt) {
                console.log('Sürükleme başladı:', evt.item.getAttribute('data-id'));
            },
            onEnd: function(evt) {
                console.log('Sürükleme bitti. Eski indeks:', evt.oldIndex, 'Yeni indeks:', evt.newIndex);
                
                if (evt.oldIndex !== evt.newIndex || evt.from !== evt.to) {
                    const items = [];
                    const allItems = Array.from(container.querySelectorAll('.list-group-item'));
                    
                    allItems.forEach((item, index) => {
                        if (!item) return;
                        
                        const id = item.getAttribute('data-id');
                        if (!id) {
                            console.error('Öğede data-id özniteliği yok:', item);
                            return;
                        }
                        
                        const parentId = item.hasAttribute('data-parent-id') ? item.getAttribute('data-parent-id') : null;
                        
                        items.push({
                            id: id,
                            order: index + 1,
                            parentId: parentId
                        });
                    });
                    
                    console.log('Sıralama gönderiliyor:', items);
                    
                    // Livewire'a sıralama verilerini gönder
                    if (items.length > 0) {
                        @this.updateOrder(items);
                    }
                }
            }
        });
        
        console.log('Sortable başlatıldı. Eleman sayısı:', container.children.length);
    }
});
</script>
@endpush