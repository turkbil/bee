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
                    wire:target="render, search, perPage, sortBy, gotoPage, previousPage, nextPage, createInstance, deleteInstance, toggleActive"
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
        
        <!-- Görünüm Seçenekleri (Tam Metin Butonları) -->
        <div class="mb-4">
            <div class="btn-group w-100">
                <button type="button" class="btn {{ $viewMode == 'active' ? 'btn-primary' : 'btn-outline-primary' }}" 
                        wire:click="changeViewMode('active')">
                    <i class="fas fa-list me-2"></i> Aktif Bileşenler
                </button>
                <button type="button" class="btn {{ $viewMode == 'gallery' ? 'btn-primary' : 'btn-outline-primary' }}" 
                        wire:click="changeViewMode('gallery')">
                    <i class="fas fa-th-large me-2"></i> Bileşen Galerisi
                </button>
            </div>
        </div>
        
        <!-- Bileşen Listesi -->
        <div class="row row-cards">
            @if($viewMode == 'active')
                @forelse($entities as $instance)
                <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
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
                                    <a href="{{ route('admin.widgetmanagement.items', $instance->id) }}" class="btn btn-outline">
                                        <i class="fas fa-layer-group me-1"></i> İçerikler
                                    </a>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                        <input type="checkbox" wire:click="toggleActive({{ $instance->id }})"
                                            {{ $instance->widget->is_active ? 'checked' : '' }} value="1" />
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
                            <img src="{{ asset('tabler/static/illustrations/undraw_quitting_time_dm8t.svg') }}"
                                height="128" alt="">
                        </div>
                        <p class="empty-title">Hiç bileşen bulunamadı</p>
                        <p class="empty-subtitle text-muted">
                            Yeni bir bileşen eklemek için "Bileşen Galerisi" sekmesine geçebilirsiniz
                        </p>
                        <div class="empty-action">
                            <button class="btn btn-primary" wire:click="changeViewMode('gallery')">
                                <i class="fas fa-th-large me-2"></i> Bileşen Galerisine Git
                            </button>
                        </div>
                    </div>
                </div>
                @endforelse
            @else
                <!-- GALERİ GÖRÜNÜMÜ - AYNI KART YAPISIYLA -->
                @forelse($entities as $template)
                <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                    <div class="card">
                        <div class="card-status-top {{ $template->is_active ? 'bg-primary' : 'bg-danger' }}"></div>
                        
                        <!-- Kart Header - AKTİF GÖRÜNÜMLE AYNI OLSUN -->
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

                        <div class="list-group list-group-flush">
                            <div class="list-group-item py-2 bg-muted-lt">
                                <div class="d-flex align-items-center">
                                    <div class="flex-fill small text-muted">

                                        <div class="mt-1" style="height: 40px; overflow: hidden;">
                                            {{ $template->description ? Str::limit($template->description, 80) : 'Açıklama yok' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Kart Footer - FOOTER YAPISI AYNI OLSUN -->
                        <div class="card-footer">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex gap-2">
                                    <button wire:click="createInstance({{ $template->id }})" class="btn btn-outline">
                                        <i class="fas fa-plus me-1"></i> Oluştur
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
                @empty
                <div class="col-12">
                    <div class="empty">
                        <div class="empty-img">
                            <img src="{{ asset('tabler/static/illustrations/undraw_quitting_time_dm8t.svg') }}"
                                height="128" alt="">
                        </div>
                        <p class="empty-title">Hiç bileşen şablonu bulunamadı</p>
                        <p class="empty-subtitle text-muted">
                            Filtrelemeye uygun bileşen şablonu bulunamadı.
                        </p>
                    </div>
                </div>
                @endforelse
            @endif
        </div>
    </div>
    <!-- Pagination -->
    @if($entities->hasPages())
    <div class="card-footer d-flex align-items-center justify-content-end">
        {{ $entities->links() }}
    </div>
    @endif
</div>