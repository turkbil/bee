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
                                    placeholder="Hazır dosya ara...">
                            </div>
                        </div>
                        
                        
                        <div class="mb-4">
                            <div class="list-group list-group-transparent mb-3">
                                <a class="list-group-item list-group-item-action py-2 d-flex align-items-center {{ $parentCategoryFilter == '' ? 'active' : '' }}" 
                                wire:click.prevent="$set('parentCategoryFilter', '')" href="#">
                                 Tüm Kategoriler
                                 <small class="text-secondary ms-auto">{{ $widgets->total() }}</small>
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
                    
                    <div class="d-flex justify-content-between mb-4">
                        <div>
                            <h3 class="card-title">Hazır Dosya Bileşenleri</h3>
                            <p class="text-muted">Hazır view dosyalarına dayalı bileşenleri görüntüleyin</p>
                        </div>
                        
                        
                        <div class="position-relative d-flex justify-content-center align-items-center">
                            <div wire:loading
                                wire:target="render, search, perPage, gotoPage, previousPage, nextPage, categoryFilter, parentCategoryFilter"
                                class="text-center">
                                <div class="small text-muted me-2">Güncelleniyor...</div>
                                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                            </div>
                        </div>
                    </div>
                    
                    
                    @php
                        $groupedWidgets = $widgets->groupBy(function($item) {
                            return $item->category->title ?? 'Kategori Atanmamış';
                        });
                    @endphp
                    
                    @forelse($groupedWidgets as $categoryName => $categoryWidgets)
                    <div class="mb-4">
                        <h4 class="border-bottom pb-2 mb-3">{{ $categoryName }}</h4>
                        <div class="row row-cards">
                            @foreach($categoryWidgets as $widget)
                            <div class="col-12 col-sm-6">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-status-top {{ $widget->is_active ? 'bg-primary' : 'bg-danger' }}"></div>
                                    
                                    
                                    <div class="card-header d-flex align-items-center">
                                        <div class="me-auto">
                                            <h3 class="card-title mb-0">
                                                <a href="{{ route('admin.widgetmanagement.manage', $widget->id) }}">
                                                    {{ $widget->name }}
                                                </a>
                                            </h3>

                                            <div class="text-muted small mt-1">
                                                 <code>{{ $widget->file_path }}</code>
                                            </div>
                                        </div>
                                        <div class="dropdown">
                                            <a href="#" class="btn btn-icon" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a href="{{ route('admin.widgetmanagement.manage', $widget->id) }}" class="dropdown-item">
                                                    <i class="fas fa-tools me-2"></i> Yapılandır
                                                </a>
                                                <a href="{{ route('admin.widgetmanagement.preview.template', $widget->id) }}" class="dropdown-item" target="_blank">
                                                    <i class="ti ti-eye me-2"></i> Önizleme
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="card-body pt-2">
                                        @if($widget->description)
                                        <p class="text-muted mt-2">{{ $widget->description }}</p>
                                        @endif
                                        
                                        <div class="mt-2">
                                            @if($widget->thumbnail)
                                                <img src="{{ $widget->thumbnail }}" class="img-fluid rounded" alt="{{ $widget->name }}">
                                            @else
                                                <div class="alert alert-light text-center p-2">
                                                    <i class="fas fa-image fa-2x text-muted my-2"></i>
                                                    <p class="text-muted small mb-0">Önizleme görseli yok</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    
                                    <div class="card-footer">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <a href="{{ route('admin.widgetmanagement.manage', $widget->id) }}" class="btn btn-outline-primary">
                                                <i class="fas fa-tools me-1"></i> Yapılandır
                                            </a>
                                            <a href="{{ route('admin.widgetmanagement.preview.template', $widget->id) }}" class="btn btn-outline-secondary" target="_blank">
                                                <i class="ti ti-eye me-1"></i> Önizle
                                            </a>
                                        </div>
                                        
                                        <div class="d-flex align-items-center justify-content-between mt-2">
                                            <span class="badge bg-blue-lt">
                                                <i class="fas fa-folder me-1"></i> {{ $widget->category ? $widget->category->title : 'Kategorilendirilmemiş' }}
                                            </span>
                                            
                                            <span class="badge bg-primary">Hazır Dosya</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @empty
                    <div class="col-12">
                        <div class="empty">
                            <div class="empty-img">
                                <img src="{{ asset('images/empty.svg') }}" height="128" alt="">
                            </div>
                            <p class="empty-title">Hiç hazır dosya bileşeni bulunamadı</p>
                            <p class="empty-subtitle text-muted">
                                Filtrelemeye uygun hazır dosya bulunamadı.
                            </p>
                        </div>
                    </div>
                    @endforelse
                </div>
                
                
                @if($widgets->hasPages())
                <div class="card-footer d-flex align-items-center justify-content-end">
                    {{ $widgets->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>