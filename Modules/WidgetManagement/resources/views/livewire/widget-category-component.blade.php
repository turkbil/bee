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
                    </div>
                </div>
                
                <!-- Sağ Taraf: Kategori Listesi -->
                <div class="col-md-8">
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <h3 class="card-title">Kategoriler</h3>
                                <div class="ms-auto">
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <i class="fas fa-search"></i>
                                        </span>
                                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                                            placeholder="Aramak için yazmaya başlayın...">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Debug Bilgisi -->
                            <div class="alert alert-info mb-3" style="display: none;" id="debug-info">
                                <h4 class="alert-title">Debug Bilgisi</h4>
                                <div id="debug-content"></div>
                            </div>
                            
                            <!-- Loading Spinner -->
                            <div wire:loading wire:target="loadCategories, toggleActive, delete, quickAdd, saveEdit, updatedSearch, updateOrder"
                                class="text-center my-3">
                                <div class="spinner-border text-primary" role="status"></div>
                                <p class="text-muted mt-2">Yükleniyor...</p>
                            </div>
                            
                            <!-- Kategori Listesi (Tablo) -->
                            <div wire:loading.remove wire:target="loadCategories, toggleActive, delete, quickAdd, saveEdit, updatedSearch, updateOrder">
                                <div id="table-default" class="table-responsive">
                                    <table class="table table-vcenter card-table table-hover" id="sortable-list">
                                        <thead>
                                            <tr>
                                                <th style="width: 50px;"></th>
                                                <th>Kategori Adı</th>
                                                <th>Slug</th>
                                                <th>İçerik</th>
                                                <th style="width: 80px" class="text-center">Durum</th>
                                                <th style="width: 120px" class="text-end">İşlemler</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($categories as $category)
                                                <tr wire:key="category-{{ $category->widget_category_id }}" 
                                                    id="item-{{ $category->widget_category_id }}" 
                                                    class="sortable-row" 
                                                    data-id="{{ $category->widget_category_id }}">
                                                    
                                                    <td class="cursor-move">
                                                        <div class="d-flex align-items-center">
                                                            <span class="bg-primary-lt rounded-2 d-flex align-items-center justify-content-center"
                                                                style="width: 2.5rem; height: 2.5rem;">
                                                                @if($category->icon)
                                                                    <i class="fas {{ $category->icon }}"></i>
                                                                @else
                                                                    <i class="fas fa-folder"></i>
                                                                @endif
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="font-weight-medium">{{ $category->title }}</div>
                                                        @if($category->description)
                                                            <div class="text-muted small">{{ Str::limit($category->description, 50) }}</div>
                                                        @endif
                                                    </td>
                                                    <td class="text-muted">{{ $category->slug }}</td>
                                                    <td>
                                                        <span class="badge bg-blue-lt">{{ $category->widgets_count }} içerik</span>
                                                        @if($category->children && $category->children->count() > 0)
                                                            <span class="badge bg-green-lt">{{ $category->children->count() }} alt kategori</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <button wire:click="toggleActive({{ $category->widget_category_id }})"
                                                            class="btn btn-icon btn-sm {{ $category->is_active ? 'text-muted bg-transparent' : 'text-red bg-transparent' }}">
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
                                                    </td>
                                                    <td class="text-end">
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
                                                @if($category->children && $category->children->count() > 0)
                                                    @foreach($category->children as $child)
                                                        <tr wire:key="child-{{ $child->widget_category_id }}" 
                                                            id="item-{{ $child->widget_category_id }}" 
                                                            class="sortable-row child-row" 
                                                            data-id="{{ $child->widget_category_id }}"
                                                            data-parent-id="{{ $category->widget_category_id }}">
                                                            
                                                            <td class="cursor-move">
                                                                <div class="ms-3 d-flex align-items-center">
                                                                    <i class="fas fa-level-down-alt fa-rotate-90 text-muted me-2"></i>
                                                                    <span class="bg-info-lt rounded-2 d-flex align-items-center justify-content-center"
                                                                        style="width: 2.5rem; height: 2.5rem;">
                                                                        @if($child->icon)
                                                                            <i class="fas {{ $child->icon }}"></i>
                                                                        @else
                                                                            <i class="fas fa-folder"></i>
                                                                        @endif
                                                                    </span>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="font-weight-medium">{{ $child->title }}</div>
                                                                @if($child->description)
                                                                    <div class="text-muted small">{{ Str::limit($child->description, 50) }}</div>
                                                                @endif
                                                            </td>
                                                            <td class="text-muted">{{ $child->slug }}</td>
                                                            <td>
                                                                <span class="badge bg-blue-lt">{{ $child->widgets_count }} içerik</span>
                                                            </td>
                                                            <td class="text-center align-middle">
                                                                <button wire:click="toggleActive({{ $child->widget_category_id }})"
                                                                    class="btn btn-icon btn-sm {{ $child->is_active ? 'text-muted bg-transparent' : 'text-red bg-transparent' }}">
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
                                                            </td>
                                                            <td class="text-end">
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
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
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
    
    /* Alt kategori stillemesi */
    .child-row {
        background-color: rgba(32, 107, 196, 0.03) !important;
    }
    
    /* Debug bilgisi */
    #debug-info {
        font-family: monospace;
        font-size: 12px;
        white-space: pre-wrap;
        max-height: 300px;
        overflow: auto;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('admin/libs/sortable/sortable.min.js') }}"></script>
<script>
document.addEventListener('livewire:initialized', function() {
    // Debug fonksiyonu
    function showDebugInfo(message, data) {
        const debugInfo = document.getElementById('debug-info');
        const debugContent = document.getElementById('debug-content');
        
        if (debugInfo && debugContent) {
            debugInfo.style.display = 'block';
            
            const timestamp = new Date().toLocaleTimeString();
            const debugMessage = `<div>[${timestamp}] ${message}</div>`;
            const debugData = data ? `<pre>${JSON.stringify(data, null, 2)}</pre>` : '';
            
            debugContent.innerHTML += debugMessage + debugData;
            
            // Otomatik scroll
            debugInfo.scrollTop = debugInfo.scrollHeight;
        }
    }
    
    // Hata yakalama
    window.addEventListener('error', function(event) {
        showDebugInfo('HATA: ' + event.message + ' (' + event.filename + ':' + event.lineno + ')', {
            message: event.message,
            file: event.filename,
            line: event.lineno,
            column: event.colno
        });
    });
    
    // Aktivasyon
    document.getElementById('debug-info').style.display = 'block';
    showDebugInfo('Debug modu aktif', { time: new Date().toISOString() });
    
    initSortable();
    
    Livewire.hook('morph.updated', () => {
        showDebugInfo('DOM güncellendi, sortable yeniden başlatılıyor');
        initSortable();
    });
    
    function initSortable() {
        const container = document.getElementById('sortable-list')?.querySelector('tbody');
        if (!container) {
            showDebugInfo('Sortable container bulunamadı!');
            return;
        }
        
        // Mevcut sortable'ı temizle
        if (window.categorySortable) {
            showDebugInfo('Mevcut sortable temizleniyor');
            window.categorySortable.destroy();
            window.categorySortable = null;
        }
        
        showDebugInfo('Yeni sortable oluşturuluyor');
        
        // Yeni sortable oluştur
        window.categorySortable = new Sortable(container, {
            animation: 150,
            ghostClass: 'sortable-ghost',
            handle: '.cursor-move',
            onStart: function(evt) {
                showDebugInfo('Sıralama başladı', { 
                    item: evt.item.id,
                    oldIndex: evt.oldIndex
                });
            },
            onEnd: function(evt) {
                showDebugInfo('Sıralama tamamlandı', { 
                    item: evt.item.id,
                    oldIndex: evt.oldIndex,
                    newIndex: evt.newIndex,
                    from: evt.from.id,
                    to: evt.to.id
                });
                
                if (evt.oldIndex !== evt.newIndex || evt.from !== evt.to) {
                    const items = [];
                    const allRows = Array.from(container.querySelectorAll('tr.sortable-row'));
                    
                    allRows.forEach((row, index) => {
                        const id = row.getAttribute('data-id');
                        const parentId = row.getAttribute('data-parent-id') || null;
                        
                        items.push({
                            id: id,
                            order: index + 1,
                            parentId: parentId
                        });
                    });
                    
                    showDebugInfo('Sıralama verisi hazırlandı', { items: items });
                    
                    // Livewire'a sıralama verilerini gönder
                    if (items.length > 0) {
                        showDebugInfo('Livewire\'a veri gönderiliyor');
                        @this.updateOrder(items).then(function(response) {
                            showDebugInfo('Livewire yanıtı', response);
                        }).catch(function(error) {
                            showDebugInfo('Livewire hatası', error);
                        });
                    }
                }
            }
        });
        
        showDebugInfo('Sortable başarıyla oluşturuldu');
    }
});
</script>
@endpush