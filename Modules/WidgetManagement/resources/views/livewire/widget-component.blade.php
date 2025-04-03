@include('widgetmanagement::helper')
<div>
    <div class="card">
        <div class="card-body">
            <!-- Header Bölümü -->
            <div class="row mb-3">
                <!-- Sol Taraf (Arama) -->
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
                <div class="col-md-4 position-relative">
                    <div wire:loading
                        wire:target="search, typeFilter, changeViewMode, createInstance, deleteInstance" 
                        class="position-absolute top-50 start-50 translate-middle text-center"
                        style="width: 100%; max-width: 250px;">
                        <div class="small text-muted mb-2">Güncelleniyor...</div>
                        <div class="progress mb-1">
                            <div class="progress-bar progress-bar-indeterminate"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Sağ Taraf (Sayfalama ve Görünüm Seçimi) -->
                <div class="col-md-2">
                    <div class="d-flex align-items-center justify-content-end gap-3">
                        <select wire:model.live="perPage" class="form-select" style="width: 80px">
                            <option value="10">10</option>
                            <option value="40">40</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Görünüm Seçenekleri -->
            <div class="d-flex mb-4">
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
                
                <div class="ms-auto">
                    @if($hasRootPermission && $viewMode == 'gallery')
                    <a href="{{ route('admin.widgetmanagement.manage') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> Yeni Bileşen Ekle
                    </a>
                    @endif
                </div>
            </div>
            
            @if($viewMode == 'active')
            <!-- AKTİF BİLEŞENLER GÖRÜNÜMÜ -->
            <div class="row row-cards">
                @forelse($entities as $instance)
                <div class="col-lg-4 col-xl-3 mb-3">
                    <div class="card">
                        <div class="card-status-top {{ $instance->widget->is_active ? 'bg-primary' : 'bg-danger' }}"></div>
                        
                        <!-- Kart Header -->
                        <div class="card-header d-flex align-items-center">
                            <div class="me-auto">
                                <h3 class="card-title mb-0">{{ $instance->settings['title'] ?? $instance->widget->name }}</h3>
                            </div>
                            <div class="dropdown">
                                <a href="#" class="btn-action" data-bs-toggle="dropdown" aria-expanded="false">
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
                                    <!-- Aktif/Pasif yapma butonu -->
                                    <button class="dropdown-item {{ $instance->widget->is_active ? 'text-danger' : 'text-success' }}" wire:click="toggleActive({{ $instance->id }})">
                                        <i class="fas {{ $instance->widget->is_active ? 'fa-toggle-off' : 'fa-toggle-on' }} me-2"></i> 
                                        {{ $instance->widget->is_active ? 'Pasif Yap' : 'Aktif Yap' }}
                                    </button>
                                    @if($hasRootPermission)
                                    <a href="{{ route('admin.widgetmanagement.manage', $instance->widget->id) }}" class="dropdown-item">
                                        <i class="fas fa-tools me-2"></i> Yapılandır
                                    </a>
                                    @endif
                                    <button class="dropdown-item text-danger" 
                                        wire:click="deleteInstance({{ $instance->id }})"
                                        onclick="return confirm('Bu bileşeni silmek istediğinize emin misiniz?')">
                                        <i class="fas fa-trash me-2"></i> Sil
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Kart Gövdesi -->
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div class="mb-3">
                                <span class="badge bg-{{ $instance->widget->type === 'static' ? 'green' : ($instance->widget->type === 'dynamic' ? 'blue' : 'purple') }}-lt">
                                    {{ $types[$instance->widget->type] ?? $instance->widget->type }}
                                </span>
                            </div>

                            <a href="{{ route('admin.widgetmanagement.items', $instance->id) }}" class="btn btn-primary w-100">
                                <i class="fas fa-layer-group me-1"></i> İçerik
                            </a>
                        </div>
                        
                        <!-- Kart Footer -->
                        <div class="card-footer">
                            <div class="d-flex align-items-center justify-content-between">
                                <a href="{{ route('admin.widgetmanagement.settings', $instance->id) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-sliders-h me-1"></i> Ayarlar
                                </a>
                                <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                    <input type="checkbox" wire:click="toggleActive({{ $instance->id }})"
                                        {{ $instance->widget->is_active ? 'checked' : '' }} value="1" />
                                    <div class="state p-success p-on ms-2">
                                        <label>Aktif</label>
                                    </div>
                                    <div class="state p-danger p-off ms-2">
                                        <label>Pasif</label>
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
                    <div class="card">
                        <div class="card-status-top {{ $template->is_active ? 'bg-primary' : 'bg-danger' }}"></div>
                        
                        <div class="card-header d-flex align-items-center">
                            <div class="me-auto">
                                <h3 class="card-title mb-0">{{ $template->name }}</h3>
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
                        
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div class="mb-3">
                                <span class="badge bg-{{ $template->type === 'static' ? 'green' : ($template->type === 'dynamic' ? 'blue' : 'purple') }}-lt">
                                    {{ $types[$template->type] ?? $template->type }}
                                </span>
                                <p class="text-muted small mt-2">{{ Str::limit($template->description, 80) }}</p>
                            </div>
                            
                            <button wire:click="createInstance({{ $template->id }})" class="btn btn-primary w-100">
                                <i class="fas fa-plus me-1"></i> Bileşen Oluştur
                            </button>
                        </div>
                        
                        <div class="card-footer">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="badge bg-{{ $template->is_active ? 'success' : 'danger' }}">
                                        {{ $template->is_active ? 'Aktif' : 'Pasif' }}
                                    </span>
                                </div>
                                @if($hasRootPermission)
                                <div>
                                    <a href="{{ route('admin.widgetmanagement.manage', $template->id) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-edit me-1"></i> Düzenle
                                    </a>
                                </div>
                                @endif
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