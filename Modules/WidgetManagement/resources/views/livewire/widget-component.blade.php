@include('widgetmanagement::helper')
<div>
    <div class="card">
        <div class="card-body">
            <!-- Görünüm Seçenekleri -->
            <div class="d-flex justify-content-between mb-4">
                <div class="btn-group">
                    <button type="button" class="btn {{ $viewMode == 'active' ? 'btn-primary' : 'btn-outline-primary' }}" 
                            wire:click="changeViewMode('active')">
                        <i class="fas fa-list me-2"></i> Aktif Bileşenler
                    </button>
                    <button type="button" class="btn {{ $viewMode == 'gallery' ? 'btn-primary' : 'btn-outline-primary' }}" 
                            wire:click="changeViewMode('gallery')">
                        <i class="fas fa-th-large me-2"></i> Bileşen Galerisi
                    </button>
                </div>
                
                <div>
                    @if(auth()->user()->hasRole('root') && $viewMode == 'gallery')
                    <a href="{{ route('admin.widgetmanagement.manage') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> Yeni Bileşen Ekle
                    </a>
                    @endif
                </div>
            </div>
            
            <!-- Header Bölümü -->
            <div class="row mb-3">
                <!-- Arama Kutusu -->
                <div class="col-md-4">
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control" 
                            placeholder="{{ $viewMode == 'active' ? 'Aktif bileşenlerde ara...' : 'Bileşen galerisinde ara...' }}">
                    </div>
                </div>
                
                <!-- Filtreler -->
                <div class="col-md-5">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width: 160px">
                            <select wire:model.live="typeFilter" class="form-select">
                                <option value="">Tüm Tipler</option>
                                @foreach($types as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div wire:loading
                            wire:target="search, typeFilter, changeViewMode, createInstance, deleteInstance" 
                            class="text-muted">
                            <i class="fas fa-spinner fa-spin me-1"></i> Yükleniyor...
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 text-end">
                    @if($viewMode == 'active')
                    <span class="text-muted">Toplam: {{ $entities->total() }} bileşen</span>
                    @endif
                </div>
            </div>
            
            @if($viewMode == 'active')
            <!-- AKTİF BİLEŞENLER GÖRÜNÜMÜ -->
            <div class="row row-cards">
                @forelse($entities as $instance)
                <div class="col-lg-4 col-xl-3 mb-3">
                    <div class="card h-100">
                        <div class="card-status-top {{ $instance->widget->is_active ? 'bg-green' : 'bg-red' }}"></div>
                        
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center mb-3">
                                @if($instance->widget->thumbnail)
                                <span class="avatar me-3" style="background-image: url({{ $instance->widget->getThumbnailUrl() }})"></span>
                                @else
                                <span class="avatar bg-blue-lt me-3">
                                    <i class="fas fa-puzzle-piece"></i>
                                </span>
                                @endif
                                <div>
                                    <h3 class="card-title mb-0">{{ $instance->settings['title'] ?? $instance->widget->name }}</h3>
                                    <span class="badge bg-blue-lt">{{ $types[$instance->widget->type] ?? $instance->widget->type }}</span>
                                    <span class="badge bg-green-lt">ID: {{ $instance->id }}</span>
                                </div>
                            </div>
                            
                            <p class="text-muted small mb-3 flex-grow-1">{{ Str::limit($instance->widget->description, 80) }}</p>
                            
                            <div class="row mt-auto">
                                <div class="col">
                                    <div class="dropdown">
                                        <button class="btn btn-outline-secondary dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-cog me-1"></i> İşlemler
                                        </button>
                                        <div class="dropdown-menu">
                                            <a href="{{ route('admin.widgetmanagement.settings', $instance->id) }}" class="dropdown-item">
                                                <i class="fas fa-sliders-h me-1"></i> Ayarlar
                                            </a>
                                            
                                            @if(auth()->user()->hasRole('root'))
                                            <a href="{{ route('admin.widgetmanagement.manage', $instance->widget->id) }}" class="dropdown-item">
                                                <i class="fas fa-tools me-1"></i> Şablonu Yapılandır
                                            </a>
                                            @endif
                                            
                                            <div class="dropdown-divider"></div>
                                            
                                            <a href="#" class="dropdown-item text-danger" wire:click.prevent="deleteInstance({{ $instance->id }})"
                                               onclick="return confirm('Bu bileşeni silmek istediğinize emin misiniz?')">
                                                <i class="fas fa-trash me-1"></i> Sil
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <a href="{{ route('admin.widgetmanagement.items', $instance->id) }}" class="btn btn-primary w-100">
                                        <i class="fas fa-layer-group me-1"></i> İçeriği Yönet
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="empty">
                        <div class="empty-img">
                            <i class="fas fa-puzzle-piece fa-5x text-muted"></i>
                        </div>
                        <p class="empty-title">Aktif bileşen bulunmuyor</p>
                        <p class="empty-subtitle text-muted mb-4">
                            Henüz oluşturulmuş bileşen bulunmuyor. Bileşen galerisinden yeni bileşenler oluşturabilirsiniz.
                        </p>
                        <button class="btn btn-primary" wire:click="changeViewMode('gallery')">
                            <i class="fas fa-th-large me-2"></i> Bileşen Galerisine Git
                        </button>
                    </div>
                </div>
                @endforelse
            </div>
            @else
            <!-- BİLEŞEN GALERİSİ GÖRÜNÜMÜ -->
            <div class="row row-cards">
                @forelse($entities as $template)
                <div class="col-lg-4 col-xl-3 mb-3">
                    <div class="card h-100">
                        <div class="card-status-top {{ $template->is_active ? 'bg-green' : 'bg-red' }}"></div>
                        
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center mb-3">
                                @if($template->thumbnail)
                                <span class="avatar me-3" style="background-image: url({{ $template->getThumbnailUrl() }})"></span>
                                @else
                                <span class="avatar bg-purple-lt me-3">
                                    <i class="fas fa-puzzle-piece"></i>
                                </span>
                                @endif
                                <div>
                                    <h3 class="card-title mb-0">{{ $template->name }}</h3>
                                    <span class="badge bg-blue-lt">{{ $types[$template->type] ?? $template->type }}</span>
                                </div>
                            </div>
                            
                            <p class="text-muted small mb-3 flex-grow-1">{{ Str::limit($template->description, 80) }}</p>
                            
                            <div class="mt-auto">
                                <button wire:click="createInstance({{ $template->id }})" class="btn btn-primary w-100">
                                    <i class="fas fa-plus me-1"></i> Bileşen Oluştur
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="empty">
                        <div class="empty-img">
                            <i class="fas fa-th-large fa-5x text-muted"></i>
                        </div>
                        <p class="empty-title">Bileşen şablonu bulunamadı</p>
                        <p class="empty-subtitle text-muted">
                            Filtrelemeye uygun bileşen şablonu bulunamadı.
                        </p>
                    </div>
                </div>
                @endforelse
            </div>
            @endif
            
            <!-- Sayfalama -->
            <div class="mt-4 d-flex justify-content-center">
                {{ $entities->links() }}
            </div>
        </div>
    </div>
</div>