@include('widgetmanagement::helper')
<div>
    <div class="row g-4">
        <div class="col-md-3">
            <form class="sticky-top" style="top: 20px;">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <div>
                                <h3 class="card-title">Kategoriler</h3>
                            </div>
                        </div>
                        
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
                        <div class="form-label">Ana Kategoriler</div>
                        <div class="mb-4">
                            <div class="list-group list-group-transparent mb-3">
                                <a class="list-group-item list-group-item-action d-flex align-items-center {{ $parentCategoryFilter == '' ? 'active' : '' }}" 
                                wire:click.prevent="$set('parentCategoryFilter', '')" href="#">
                                 Tüm Kategoriler
                                 <small class="text-secondary ms-auto">{{ $templates->total() }}</small>
                                </a>
                                
                                @foreach($parentCategories as $category)
                                <a class="list-group-item list-group-item-action d-flex align-items-center {{ $parentCategoryFilter == $category->widget_category_id ? 'active' : '' }}" 
                                    wire:click.prevent="$set('parentCategoryFilter', '{{ $category->widget_category_id }}')" href="#">
                                    {{ $category->title }}
                                    <small class="text-secondary ms-auto">{{ $category->total_widgets_count }}</small>
                                </a>
                                                             
                                @if($category->children_count > 0)
                                    @foreach($category->children as $childCategory)
                                    <a class="list-group-item list-group-item-action d-flex align-items-center ps-5 {{ $categoryFilter == $childCategory->widget_category_id ? 'active' : '' }}" 
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
                                <a class="list-group-item list-group-item-action d-flex align-items-center {{ $categoryFilter == '' ? 'active' : '' }}" 
                                   wire:click.prevent="$set('categoryFilter', '')" href="#">
                                    Tümünü Göster
                                </a>
                                
                                @foreach($childCategories as $childCategory)
                                <a class="list-group-item list-group-item-action d-flex align-items-center {{ $categoryFilter == $childCategory->widget_category_id ? 'active' : '' }}" 
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
                                <option value="12">12 Bileşen</option>
                                <option value="24">24 Bileşen</option>
                                <option value="48">48 Bileşen</option>
                                <option value="96">96 Bileşen</option>
                            </select>
                        </div>
                        
                        <div class="mt-4">
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.widgetmanagement.index') }}" class="btn btn-outline-secondary w-100 {{ request()->routeIs('admin.widgetmanagement.index') ? 'active' : '' }}">
                                    <i class="fas fa-th-list me-1"></i> Aktif Bileşenler
                                </a>
                            </div>
                            <div class="d-flex gap-2 mt-2">
                                <a href="{{ route('admin.widgetmanagement.gallery') }}" class="btn btn-outline-primary w-100 {{ request()->routeIs('admin.widgetmanagement.gallery') ? 'active' : '' }}">
                                    <i class="fas fa-th-large me-1"></i> Bileşen Galerisi
                                </a>
                            </div>
                            @if($hasRootPermission)
                            <div class="d-flex gap-2 mt-2">
                                <a href="{{ route('admin.widgetmanagement.modules') }}" class="btn btn-outline-secondary w-100 {{ request()->routeIs('admin.widgetmanagement.modules') ? 'active' : '' }}">
                                    <i class="fas fa-puzzle-piece me-1"></i> Modül Bileşenleri
                                </a>
                            </div>
                            <div class="d-flex gap-2 mt-2">
                                <a href="{{ route('admin.widgetmanagement.files') }}" class="btn btn-outline-secondary w-100 {{ request()->routeIs('admin.widgetmanagement.files') ? 'active' : '' }}">
                                    <i class="fas fa-file-code me-1"></i> Hazır Dosyalar
                                </a>
                            </div>
                            @endif
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
                            <h3 class="card-title">Bileşen Galerisi</h3>
                            <p class="text-muted">Kullanmak istediğiniz bileşeni seçin ve kuruluma başlayın</p>
                        </div>
                        
                        <!-- Loading İndikatörü -->
                        <div class="position-relative d-flex justify-content-center align-items-center">
                            <div wire:loading
                                wire:target="render, search, perPage, gotoPage, previousPage, nextPage, createInstance, categoryFilter, parentCategoryFilter"
                                class="text-center">
                                <div class="small text-muted me-2">Güncelleniyor...</div>
                                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Bileşenleri Kategoriye Göre Gruplandırma -->
                    @php
                        $groupedTemplates = $templates->groupBy(function($item) {
                            return $item->category->title ?? 'Kategori Atanmamış';
                        });
                    @endphp
                    
                    @forelse($groupedTemplates as $categoryName => $categoryTemplates)
                    <div class="mb-4">
                        <h4 class="border-bottom pb-2 mb-3">{{ $categoryName }}</h4>
                        <div class="row row-cards">
                            @foreach($categoryTemplates as $template)
                            <div class="col-12 col-sm-6">
                                <div class="card h-100">
                                    <div class="card-status-top {{ $template->is_active ? 'bg-primary' : 'bg-danger' }}"></div>
                                    
                                    <!-- Kart Header -->
                                    <div class="card-header d-flex align-items-center">
                                        <div class="me-auto">
                                            <h3 class="card-title mb-0">{{ $template->name }}</h3>
                                            @if($template->category)
                                            <div class="text-muted small">
                                                @if($template->category->parent)
                                                <span>{{ $template->category->parent->title }} / {{ $template->category->title }}</span>
                                                @else
                                                <span>{{ $template->category->title }}</span>
                                                @endif
                                            </div>
                                            @endif
                                        </div>
                                        <div class="dropdown">
                                            <a href="#" class="btn-action" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </a>
                                            
                                            <div class="dropdown-menu dropdown-menu-end">
                                                @if($hasRootPermission)
                                                <a href="{{ route('admin.widgetmanagement.manage', $template->id) }}" class="dropdown-item">
                                                    <i class="fas fa-tools me-2"></i> Düzenle
                                                </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="card-body">
                                        @if($template->description)
                                        <p class="text-muted">{{ $template->description }}</p>
                                        @endif
                                        
                                        @if($template->thumbnail)
                                        <div class="mt-3 text-center">
                                            <img src="{{ $template->thumbnail }}" class="img-fluid rounded" alt="{{ $template->name }}">
                                        </div>
                                        @endif
                                    </div>
            
                                    <!-- Kart Footer -->
                                    <div class="card-footer">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="d-flex gap-2">
                                                <button wire:click="createInstance({{ $template->id }})" class="btn btn-outline-primary">
                                                    <i class="fas fa-plus me-1"></i> Kullanmaya Başla
                                                </button>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <div class="badge badge-outline-primary me-2">
                                                    {{ $types[$template->type] ?? $template->type }}
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
                        <p class="empty-title">Hiç bileşen şablonu bulunamadı</p>
                        <p class="empty-subtitle text-muted">
                            Filtrelemeye uygun bileşen şablonu bulunamadı.
                        </p>
                    </div>
                    @endforelse
                </div>
                
                <!-- Pagination -->
                @if($templates->hasPages())
                <div class="card-footer d-flex align-items-center justify-content-end">
                    {{ $templates->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- İsim Belirleme Modal -->
    <div class="modal @if($showNameModal) show @endif" tabindex="-1" style="display: @if($showNameModal) block @else none @endif; background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Bileşen Adı Belirle</h5>
                    <button type="button" class="btn-close" wire:click="resetModal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="newWidgetName" class="form-label">Bileşen Adı</label>
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <i class="fas fa-tag"></i>
                            </span>
                            <input type="text" id="newWidgetName" class="form-control" wire:model.live="newWidgetName"
                                placeholder="Bileşen adını girin">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="resetModal">İptal</button>
                    <button type="button" class="btn btn-primary" wire:click="createInstanceWithName">Oluştur</button>
                </div>
            </div>
        </div>
    </div>
</div>