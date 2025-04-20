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
                        wire:target="render, search, perPage, sortBy, gotoPage, previousPage, nextPage, createInstance"
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
                    <h3 class="card-title">Bileşen Galerisi</h3>
                    <p class="text-muted">Kullanmak istediğiniz bileşeni seçin ve kuruluma başlayın</p>
                </div>
                <div>
                    <a href="{{ route('admin.widgetmanagement.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-list me-2"></i> Aktif Bileşenler
                    </a>
                </div>
            </div>
            
            <!-- Bileşen Listesi -->
            <div class="row row-cards">
                @forelse($templates as $template)
                <div class="col-12 col-sm-6 col-lg-4 col-xl-4">
                    <div class="card">
                        <div class="card-status-top {{ $template->is_active ? 'bg-primary' : 'bg-danger' }}"></div>
                        
                        <!-- Kart Header -->
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
                        
                        <!-- Kart Footer -->
                        <div class="card-footer">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex gap-2">
                                    <button wire:click="createInstance({{ $template->id }})" class="btn btn-outline">
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
                @empty
                <div class="col-12">
                    <div class="empty">
                        <div class="empty-img">
                            <img src="{{ asset('images/empty.svg') }}"
                                height="128" alt="">
                        </div>
                        <p class="empty-title">Hiç bileşen şablonu bulunamadı</p>
                        <p class="empty-subtitle text-muted">
                            Filtrelemeye uygun bileşen şablonu bulunamadı.
                        </p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
        
        <!-- Pagination -->
        @if($templates->hasPages())
        <div class="card-footer d-flex align-items-center justify-content-end">
            {{ $templates->links() }}
        </div>
        @endif
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