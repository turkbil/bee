@include('widgetmanagement::helper')
<div class="card">
    <div class="card-body">
        <div class="row mb-3">
            <!-- Sol Taraf (Arama ve Filtreler) -->
            <div class="col-md-6">
                <div class="row g-2">
                    <!-- Arama Kutusu -->
                    <div class="col-md-8">
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                                placeholder="Bileşen ara...">
                        </div>
                    </div>
                    <!-- Tip Filtresi -->
                    <div class="col-md-4">
                        <select wire:model.live="typeFilter" class="form-select">
                            <option value="">Tüm Tipler</option>
                            @foreach($types as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Ortadaki Loading -->
            <div class="col-md-4 position-relative d-flex justify-content-center align-items-center">
                <div wire:loading
                    wire:target="render, search, perPage, sortBy, gotoPage, previousPage, nextPage, createInstance, deleteInstance, toggleActive, categoryFilter, parentCategoryFilter"
                    class="position-absolute top-50 start-50 translate-middle text-center"
                    style="width: 100%; max-width: 250px;">
                    <div class="small text-muted mb-2">Güncelleniyor...</div>
                    <div class="progress mb-1">
                        <div class="progress-bar progress-bar-indeterminate"></div>
                    </div>
                </div>
            </div>
            
            <!-- Sağ Taraf (Sayfalama) -->
            <div class="col-md-2">
                <div class="d-flex align-items-center justify-content-end gap-3">
                    <select wire:model.live="perPage" class="form-select" style="width: 80px">
                        <option value="10">10</option>
                        <option value="40">40</option>
                        <option value="100">100</option>
                        <option value="200">200</option>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Üstteki Butonlar -->
        <div class="d-flex justify-content-between mb-4">
            <div>
                <h3 class="card-title">Aktif Bileşenler</h3>
                <p class="text-muted">Kullanmakta olduğunuz bileşenleri yönetin</p>
            </div>
        </div>
        
        <!-- Ana Kategori Filtresi -->
        @if($parentCategories->count() > 0)
        <div class="mb-3">
            <div class="d-flex flex-wrap gap-2">
                <button class="btn {{ $parentCategoryFilter == '' ? 'btn-primary' : 'btn-outline-secondary' }}" 
                    wire:click="$set('parentCategoryFilter', '')">
                    Tümü
                </button>
                @foreach($parentCategories as $category)
                <button class="btn {{ $parentCategoryFilter == $category->widget_category_id ? 'btn-primary' : 'btn-outline-secondary' }}" 
                    wire:click="$set('parentCategoryFilter', '{{ $category->widget_category_id }}')">
                    {{ $category->title }} 
                    <span class="badge ms-1">{{ $category->widgets_count + $category->children->sum('widgets_count') }}</span>
                    @if($category->children_count > 0)
                    <span class="badge ms-1" title="{{ $category->children_count }} alt kategori">
                        <i class="fas fa-sitemap fa-xs"></i>
                    </span>
                    @endif
                </button>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Alt Kategori Filtresi - Sadece bir ana kategori seçildiğinde görünür -->
        @if($childCategories->count() > 0)
        <div class="mb-3 ms-4 border-start ps-2">
            <div class="d-flex flex-wrap gap-2">
                <button class="btn {{ $categoryFilter == '' ? 'btn-info' : 'btn-outline-info' }}" 
                    wire:click="$set('categoryFilter', '')">
                    Tüm Alt Kategoriler
                </button>
                @foreach($childCategories as $childCategory)
                <button class="btn {{ $categoryFilter == $childCategory->widget_category_id ? 'btn-info' : 'btn-outline-info' }}" 
                    wire:click="$set('categoryFilter', '{{ $childCategory->widget_category_id }}')">
                    {{ $childCategory->title }}
                    <span class="badge bg-secondary ms-1">{{ $childCategory->widgets_count }}</span>
                </button>
                @endforeach
            </div>
        </div>
        @endif
        
        <!-- Bileşen Listesi -->
        <div class="row row-cards">
            @forelse($entities as $instance)
            <div class="col-12 col-sm-6 col-lg-4 col-xl-4">
                <div class="card">
                    <div class="card-status-top {{ $instance->widget && $instance->widget->is_active ? 'bg-primary' : 'bg-danger' }}"></div>
                    
                    <!-- Kart Header -->
                    <div class="card-header d-flex align-items-center">
                        <div class="me-auto">
                            <h3 class="card-title mb-0">
                                <a href="{{ route('admin.widgetmanagement.items', $instance->id) }}">
                                    {{ $instance->settings['title'] ?? $instance->widget->name }}
                                </a>
                            </h3>
                            @if($instance->widget->category)
                            <div class="text-muted small">
                                Kategori: {{ $instance->widget->category->title }}
                                @if($instance->widget->category->parent)
                                <span class="text-muted"> / {{ $instance->widget->category->parent->title }}</span>
                                @endif
                            </div>
                            @endif
                        </div>
                        <div class="dropdown">
                            <a href="#" class="btn btn-icon" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </a>
                            
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="{{ route('admin.widgetmanagement.settings', $instance->id) }}" class="dropdown-item">
                                    <i class="fas fa-sliders-h me-2"></i> Ayarlar
                                </a>
                                <a href="{{ route('admin.widgetmanagement.items', $instance->id) }}" class="dropdown-item">
                                    <i class="fas fa-layer-group me-2"></i> İçerik
                                </a>
                                <div class="dropdown-divider"></div>
                                @if($hasRootPermission)
                                <a href="{{ route('admin.widgetmanagement.manage', $instance->widget->id) }}" class="dropdown-item">
                                    <i class="fas fa-tools me-2"></i> Yapılandır
                                </a>
                                @endif
                                <button class="dropdown-item text-danger" wire:click="deleteInstance({{ $instance->id }})"
                                onclick="return confirm('Bu bileşeni silmek istediğinize emin misiniz?')">
                                    <i class="fas fa-trash me-2"></i>Sil
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Kart Footer -->
                    <div class="card-footer">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.widgetmanagement.items', $instance->id) }}" class="text-body">
                                    <i class="fas fa-layer-group me-1"></i>
                                    İçerikler
                                </a>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                    <input type="checkbox" wire:click="toggleActive({{ $instance->id }})"
                                        {{ $instance->is_active ? 'checked' : '' }} value="1" />
                                    <div class="state p-success p-on ms-2">
                                        <label>Aktif</label>
                                    </div>
                                    <div class="state p-danger p-off ms-2">
                                        <label>Aktif Değil</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="empty">
                    <div class="empty-img">
                        <img src="{{ asset('images/empty.svg') }}"
                            height="128" alt="">
                    </div>
                    <p class="empty-title">Hiç bileşen bulunamadı</p>
                    <p class="empty-subtitle text-muted">
                        Yeni bir bileşen eklemek için "Bileşen Galerisi" sayfasına geçebilirsiniz
                    </p>
                    <div class="empty-action">
                        <a href="{{ route('admin.widgetmanagement.gallery') }}" class="btn btn-primary">
                            <i class="fas fa-th-large me-2"></i> Bileşen Galerisine Git
                        </a>
                    </div>
                </div>
            </div>
            @endforelse
        </div>
    </div>
    <!-- Pagination -->
    @if($entities->hasPages())
    <div class="card-footer d-flex align-items-center justify-content-end">
        {{ $entities->links() }}
    </div>
    @endif
</div>