@include('widgetmanagement::helper')
<div>
    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">Kategori Yönetimi</h3>
        </div>
        <div class="card-body">
            <form wire:submit.prevent="quickAdd">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <input type="text" wire:model="title" 
                                class="form-control @error('title') is-invalid @enderror" 
                                placeholder="Kategori başlığı">
                            <label>Kategori Başlığı</label>
                            @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating mb-3">
                            <select wire:model="parentId" class="form-select">
                                <option value="">Ana Kategori Olarak Ekle</option>
                                @foreach($parentCategories as $parentCategory)
                                <option value="{{ $parentCategory->widget_category_id }}">{{ $parentCategory->title }}</option>
                                @endforeach
                            </select>
                            <label>Üst Kategori (İsteğe Bağlı)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex">
                            <button type="submit" class="btn btn-primary w-100" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="quickAdd">
                                    <i class="fas fa-plus me-2"></i> Kategori Ekle
                                </span>
                                <span wire:loading wire:target="quickAdd">
                                    <i class="fas fa-spinner fa-spin me-2"></i> Ekleniyor...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Kategori Listesi</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-striped">
                    <thead>
                        <tr>
                            <th width="40">#</th>
                            <th>Kategori</th>
                            <th width="100">Widget Sayısı</th>
                            <th width="120">Durum</th>
                            <th width="180">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody id="sortable-categories">
                        @forelse($categories as $index => $category)
                            <!-- Ana Kategori -->
                            <tr class="sortable-item" data-id="{{ $category->widget_category_id }}" data-parent="">
                                <td>
                                    <i class="fas fa-grip-vertical text-muted cursor-move"></i>
                                    <span class="badge bg-blue-lt ms-1">{{ $category->order }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($category->children->count() > 0)
                                            <button wire:click="toggleExpand({{ $category->widget_category_id }})" class="btn btn-sm btn-icon me-2">
                                                <i class="fas {{ isset($expandedCategories[$category->widget_category_id]) ? 'fa-chevron-down' : 'fa-chevron-right' }}"></i>
                                            </button>
                                        @else
                                            <span class="ps-4"></span>
                                        @endif
                                        
                                        @if($editCategoryId === $category->widget_category_id)
                                            <div class="input-group">
                                                <input type="text" wire:model="editData.title" class="form-control form-control-sm" placeholder="Kategori adı">
                                                <button wire:click="saveEdit" class="btn btn-sm btn-success" type="button">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button wire:click="cancelEdit" class="btn btn-sm btn-danger" type="button">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        @else
                                            <div>
                                                <strong>{{ $category->title }}</strong>
                                                @if($category->icon)
                                                    <i class="fas {{ $category->icon }} ms-1 text-muted"></i>
                                                @endif
                                                <div class="text-muted small">{{ $category->description }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-blue">{{ $category->widgets_count }}</span>
                                </td>
                                <td>
                                    <label class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" 
                                            wire:click="toggleActive({{ $category->widget_category_id }})"
                                            {{ $category->is_active ? 'checked' : '' }}>
                                        <span class="form-check-label">{{ $category->is_active ? 'Aktif' : 'Pasif' }}</span>
                                    </label>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button wire:click="startEdit({{ $category->widget_category_id }})" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button wire:click="delete({{ $category->widget_category_id }})" 
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Bu kategoriyi silmek istediğinize emin misiniz?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Alt Kategoriler -->
                            @if(isset($expandedCategories[$category->widget_category_id]) && $category->children->count() > 0)
                                @foreach($category->children as $childIndex => $child)
                                <tr class="table-active sortable-item" data-id="{{ $child->widget_category_id }}" data-parent="{{ $category->widget_category_id }}">
                                    <td>
                                        <i class="fas fa-grip-vertical text-muted cursor-move"></i>
                                        <span class="badge bg-green-lt ms-1">{{ $child->order }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="ps-4 ms-4">
                                                @if($editCategoryId === $child->widget_category_id)
                                                    <div class="input-group">
                                                        <input type="text" wire:model="editData.title" class="form-control form-control-sm" placeholder="Kategori adı">
                                                        <button wire:click="saveEdit" class="btn btn-sm btn-success" type="button">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button wire:click="cancelEdit" class="btn btn-sm btn-danger" type="button">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                @else
                                                    <div>
                                                        <i class="fas fa-arrow-right text-muted me-1"></i>
                                                        <strong>{{ $child->title }}</strong>
                                                        @if($child->icon)
                                                            <i class="fas {{ $child->icon }} ms-1 text-muted"></i>
                                                        @endif
                                                        <div class="text-muted small">{{ $child->description }}</div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-blue">{{ $child->widgets_count }}</span>
                                    </td>
                                    <td>
                                        <label class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" 
                                                wire:click="toggleActive({{ $child->widget_category_id }})"
                                                {{ $child->is_active ? 'checked' : '' }}>
                                            <span class="form-check-label">{{ $child->is_active ? 'Aktif' : 'Pasif' }}</span>
                                        </label>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button wire:click="startEdit({{ $child->widget_category_id }})" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button wire:click="delete({{ $child->widget_category_id }})" 
                                                class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Bu kategoriyi silmek istediğinize emin misiniz?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            @endif
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <div class="empty">
                                    <div class="empty-img">
                                        <i class="fas fa-folder-open fa-5x text-muted"></i>
                                    </div>
                                    <p class="empty-title mt-2">Henüz kategori eklenmemiş</p>
                                    <p class="empty-subtitle text-muted">
                                        Yukarıdaki formu kullanarak yeni bir kategori ekleyebilirsiniz.
                                    </p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('admin/libs/sortable/sortable.min.js') }}"></script>
<script>
document.addEventListener('livewire:initialized', function() {
    initSortable();
    
    Livewire.hook('morph.updated', () => {
        initSortable();
    });
    
    function initSortable() {
        const sortableList = document.getElementById('sortable-categories');
        if (sortableList) {
            const sortable = new Sortable(sortableList, {
                animation: 150,
                handle: '.cursor-move',
                ghostClass: 'bg-light',
                onEnd: function(evt) {
                    const items = Array.from(sortableList.querySelectorAll('.sortable-item')).map((el, index) => {
                        return {
                            value: el.getAttribute('data-id'),
                            order: index + 1,
                            parent: el.getAttribute('data-parent')
                        };
                    });
                    
                    @this.updateOrder(items);
                }
            });
        }
    }
});
</script>
@endpush