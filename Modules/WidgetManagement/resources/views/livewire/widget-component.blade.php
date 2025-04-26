@include('widgetmanagement::helper')
<div>
    <div class="row g-4">
        <div class="col-md-3">
            <form class="sticky-top" style="top: 20px;">
                <div class="card">
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="input-icon">
                                <span class="input-icon-addon">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                                    placeholder="Bileşen ara...">
                            </div>
                        </div>
                        
                        <!-- Tip Filtresi -->
                        <div class="mb-4">
                            <div class="form-label">Bileşen Tipi</div>
                            <select wire:model.live="typeFilter" class="form-select">
                                <option value="">Tüm Tipler</option>
                                @foreach($types as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Ana Kategoriler -->
                        <div class="mb-4">
                            <div class="list-group list-group-transparent mb-3">
                                <a class="list-group-item list-group-item-action py-2 d-flex align-items-center {{ $parentCategoryFilter == '' ? 'active' : '' }}" 
                                wire:click.prevent="$set('parentCategoryFilter', '')" href="#">
                                 Tüm Kategoriler
                                 <small class="text-secondary ms-auto">{{ $entities->total() }}</small>
                                </a>
                                
                                @foreach($parentCategories as $category)
                                <a class="list-group-item list-group-item-action py-2 d-flex align-items-center {{ $parentCategoryFilter == $category->widget_category_id ? 'active' : '' }}" 
                                    wire:click.prevent="$set('parentCategoryFilter', '{{ $category->widget_category_id }}')" href="#">
                                    {{ $category->title }}
                                    <small class="text-secondary ms-auto">{{ $category->total_widgets_count }}</small>
                                </a>
                                                             
                                @if($category->children_count > 0)
                                    @foreach($category->children as $childCategory)
                                    <a class="list-group-item list-group-item-action py-2 d-flex align-items-center ps-5 {{ $categoryFilter == $childCategory->widget_category_id ? 'active' : '' }}" 
                                       wire:click.prevent="$set('categoryFilter', '{{ $childCategory->widget_category_id }}')" href="#">
                                        <i class="fas fa-angle-right me-2"></i> {{ $childCategory->title }}
                                        <small class="text-secondary ms-auto">{{ $childCategory->widgets_count }}</small>
                                    </a>
                                    @endforeach
                                @endif
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- Alt Kategoriler - Seçilen Ana Kategoriye göre -->
                        @if($childCategories->count() > 0 && $parentCategoryFilter && !$categoryFilter)
                        <div class="form-label">Alt Kategoriler</div>
                        <div class="mb-4">
                            <div class="list-group list-group-transparent mb-3">
                                <a class="list-group-item list-group-item-action py-2 d-flex align-items-center {{ $categoryFilter == '' ? 'active' : '' }}" 
                                   wire:click.prevent="$set('categoryFilter', '')" href="#">
                                    Tümünü Göster
                                </a>
                                
                                @foreach($childCategories as $childCategory)
                                <a class="list-group-item list-group-item-action py-2 d-flex align-items-center {{ $categoryFilter == $childCategory->widget_category_id ? 'active' : '' }}" 
                                   wire:click.prevent="$set('categoryFilter', '{{ $childCategory->widget_category_id }}')" href="#">
                                    {{ $childCategory->title }}
                                    <small class="text-secondary ms-auto">{{ $childCategory->widgets_count }}</small>
                                </a>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        
                        <!-- Sayfalama Ayarı -->
                        <div class="form-label">Sayfa Başına</div>
                        <div class="mb-4">
                            <select wire:model.live="perPage" class="form-select">
                                <option value="100">100 Bileşen</option>
                                <option value="200">200 Bileşen</option>
                                <option value="500">500 Bileşen</option>
                                <option value="1000">1000 Bileşen</option>
                            </select>
                        </div>
                        
                    </div>
                </div>
            </form>
        </div>
        
        <div class="col-md-9">
            <div class="card">
                <div class="card-body">
                    <!-- Üstteki Butonlar ve Başlık -->
                    <div class="d-flex justify-content-between mb-4">
                        <div>
                            <h3 class="card-title">Aktif Bileşenler</h3>
                            <p class="text-muted">Kullanmakta olduğunuz bileşenleri yönetin</p>
                        </div>
                        
                        <!-- Loading İndikatörü -->
                        <div class="position-relative d-flex justify-content-center align-items-center">
                            <div wire:loading
                                wire:target="render, search, perPage, gotoPage, previousPage, nextPage, createInstance, deleteInstance, toggleActive, categoryFilter, parentCategoryFilter"
                                class="text-center">
                                <div class="small text-muted me-2">Güncelleniyor...</div>
                                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Bileşenleri Kategoriye Göre Gruplandırma -->
                    @php
                        $groupedEntities = $entities->groupBy(function($item) {
                            return $item->widget->category->title ?? 'Kategori Atanmamış';
                        });
                    @endphp
                    
                    @forelse($groupedEntities as $categoryName => $categoryEntities)
                    <div class="mb-4">
                        <h4 class="border-bottom pb-2 mb-3">{{ $categoryName }}</h4>
                        <div class="row row-cards">
                            @foreach($categoryEntities as $instance)
                            <div class="col-12 col-sm-6 col-lg-6">
                                <div class="card h-100">
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
                                                @if($instance->widget->category->parent)
                                                <span>{{ $instance->widget->category->parent->title }} / {{ $instance->widget->category->title }}</span>
                                                @else
                                                <span>{{ $instance->widget->category->title }}</span>
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

                                                @if($instance->widget->type === 'static')
                                                    @php
                                                        $staticItem = $instance->items->first();
                                                        $itemId = $staticItem ? $staticItem->id : 0;
                                                    @endphp
                                                    <a href="{{ route('admin.widgetmanagement.item.manage', [$instance->id, $itemId]) }}" class="dropdown-item">
                                                        <i class="fas fa-layer-group me-2"></i> İçerik
                                                    </a>
                                                @else
                                                    <a href="{{ route('admin.widgetmanagement.items', $instance->id) }}" class="dropdown-item">
                                                        <i class="fas fa-layer-group me-2"></i> İçerik
                                                    </a>
                                                @endif
                                                
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

                                            @if($instance->widget->type === 'static')
                                                @php
                                                    $staticItem = $instance->items->first();
                                                    $itemId = $staticItem ? $staticItem->id : 0;
                                                @endphp
                                                <a href="{{ route('admin.widgetmanagement.item.manage', [$instance->id, $itemId]) }}" class="text-body">
                                                    <i class="fas fa-layer-group me-1"></i>
                                                    İçerik
                                                </a>
                                            @else
                                                <a href="{{ route('admin.widgetmanagement.items', $instance->id) }}" class="text-body">
                                                    <i class="fas fa-layer-group me-1"></i>
                                                    İçerikler
                                                </a>
                                            @endif

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
                            @endforeach
                        </div>
                    </div>
                    @empty
                    <div class="empty">
                        <div class="empty-img">
                            <img src="{{ asset('images/empty.svg') }}" height="128" alt="">
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
                    @endforelse
                </div>
                
                <!-- Pagination -->
                @if($entities->hasPages())
                <div class="card-footer d-flex align-items-center justify-content-end">
                    {{ $entities->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>