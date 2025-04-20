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
            
            <!-- Bileşen Listesi -->
            <div class="row row-cards">
                @forelse($widgets as $widget)
                <div class="col-12 col-sm-6 col-lg-4 col-xl-4">
                    <div class="card">
                        <div class="card-status-top {{ $widget->is_active ? 'bg-primary' : 'bg-danger' }}"></div>
                        
                        <!-- Kart Header -->
                        <div class="card-header d-flex align-items-center">
                            <div class="me-auto">
                                <h3 class="card-title mb-0">{{ $widget->name }}</h3>
                                @if($widget->category)
                                <div class="text-muted small">
                                    Kategori: {{ $widget->category->title }}
                                </div>
                                @endif
                            </div>
                        </div>
    
                        <div class="list-group list-group-flush">
                            <div class="list-group-item py-2 bg-muted-lt">
                                <div class="d-flex align-items-center">
                                    <div class="flex-fill small text-muted">
                                        <div class="mt-1">
                                            <strong>Dosya Yolu:</strong> <code>{{ $widget->file_path }}</code>
                                        </div>
                                        <div class="mt-1" style="height: 40px; overflow: hidden;">
                                            {{ $widget->description ? Str::limit($widget->description, 80) : 'Açıklama yok' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Kart Footer -->
                        <div class="card-footer">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.widgetmanagement.file.preview', $widget->id) }}" class="btn btn-outline-primary" target="_blank">
                                        <i class="fas fa-eye me-1"></i> Önizleme
                                    </a>
                                </div>
                                <div class="d-flex gap-2">
                                    <div class="badge bg-blue-lt me-2">
                                        Hazır Dosya
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