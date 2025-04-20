@include('widgetmanagement::helper')
<div>
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
                                    placeholder="Hazır dosya ara...">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Ortadaki Loading -->
                <div class="col-md-4 position-relative d-flex justify-content-center align-items-center">
                    <div wire:loading
                        wire:target="render, search, perPage, gotoPage, previousPage, nextPage, categoryFilter"
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
                            <option value="12">12</option>
                            <option value="24">24</option>
                            <option value="48">48</option>
                            <option value="96">96</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Üstteki Butonlar -->
            <div class="d-flex justify-content-between mb-4">
                <div>
                    <h3 class="card-title">Hazır Dosya Bileşenleri</h3>
                    <p class="text-muted">Hazır view dosyalarına dayalı bileşenleri görüntüleyin</p>
                </div>
                <div>
                    <a href="{{ route('admin.widgetmanagement.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-list me-2"></i> Aktif Bileşenler
                    </a>
                </div>
            </div>
            
            <!-- Kategori Filtresi -->
            @if($categories->count() > 0)
            <div class="mb-3">
                <div class="d-flex flex-wrap gap-2">
                    <button class="btn {{ $categoryFilter == '' ? 'btn-primary' : 'btn-outline-secondary' }}" 
                        wire:click="$set('categoryFilter', '')">
                        Tümü
                    </button>
                    @foreach($categories as $category)
                    <button class="btn {{ $categoryFilter == $category->widget_category_id ? 'btn-primary' : 'btn-outline-secondary' }}" 
                        wire:click="$set('categoryFilter', '{{ $category->widget_category_id }}')">
                        {{ $category->title }}
                    </button>
                    @endforeach
                </div>
            </div>
            @endif
            
            <!-- Bileşen Listesi (Kategoriye Göre Gruplandırılmış) -->
            <div class="row g-3">
                @php
                    $currentCategory = null;
                @endphp
                
                @forelse($widgets as $widget)
                    @if($currentCategory != $widget->widget_category_id)
                        @php $currentCategory = $widget->widget_category_id; @endphp
                        <div class="col-12">
                            <h3 class="mt-4 mb-3 border-bottom pb-2">
                                {{ $widget->category ? $widget->category->title : 'Diğer' }}
                            </h3>
                        </div>
                    @endif
                    
                    <div class="col-12 col-sm-6 col-lg-4 mb-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-status-top {{ $widget->is_active ? 'bg-primary' : 'bg-danger' }}"></div>
                            
                            <!-- Kart Header -->
                            <div class="card-header d-flex align-items-center">
                                <div class="me-auto">
                                    <h3 class="card-title mb-0">
                                        <a href="{{ route('admin.widgetmanagement.items', $widget->id) }}">
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
                                        <a href="{{ route('admin.widgetmanagement.file.preview', $widget->id) }}" class="dropdown-item" target="_blank">
                                            <i class="fas fa-eye me-2"></i> Önizleme
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
                            
                            <!-- Kart Footer -->
                            <div class="card-footer">
                                <div class="d-flex align-items-center justify-content-between">
                                    <a href="{{ route('admin.widgetmanagement.file.preview', $widget->id) }}" class="btn btn-outline-primary w-100" target="_blank">
                                        <i class="fas fa-eye me-1"></i> Önizleme
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
        </div>
        
        <!-- Pagination -->
        @if($widgets->hasPages())
        <div class="card-footer d-flex align-items-center justify-content-end">
            {{ $widgets->links() }}
        </div>
        @endif
    </div>
</div>