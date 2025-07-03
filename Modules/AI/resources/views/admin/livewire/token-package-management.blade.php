<div>
@include('ai::admin.helper')
<div class="card">
    <div class="card-body">
        <!-- Header Bölümü -->
        <div class="row mb-3">
            <!-- Arama Kutusu -->
            <div class="col">
                <div class="input-icon">
                    <span class="input-icon-addon">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" wire:model.live="search" class="form-control"
                        placeholder="Token paketi ara...">
                </div>
            </div>
            <!-- Ortadaki Loading -->
            <div class="col position-relative">
                <div wire:loading
                    wire:target="render, search, perPage, sortBy, gotoPage, previousPage, nextPage, delete, selectedItems, selectAll, bulkDelete, bulkToggleActive"
                    class="position-absolute top-50 start-50 translate-middle text-center"
                    style="width: 100%; max-width: 250px;">
                    <div class="small text-muted mb-2">Güncelleniyor...</div>
                    <div class="progress mb-1">
                        <div class="progress-bar progress-bar-indeterminate"></div>
                    </div>
                </div>
            </div>
            <!-- Sağ Taraf (Switch ve Select) -->
            <div class="col">
                <div class="d-flex align-items-center justify-content-end gap-3">
                    <!-- Online/Offline Toggle -->
                    <label class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" wire:model.live="showOnlineOnly">
                        <span class="form-check-label">
                            <span class="form-check-description">Sadece aktif paketler</span>
                        </span>
                    </label>
                    
                    <!-- Per Page Select -->
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted">Sayfa başına:</span>
                        <select wire:model.live="perPage" class="form-select form-select-sm" style="width: auto;">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        @if($packages->count() > 0)
        <!-- Pricing Cards Grid -->
        <div class="row row-cards">
            @foreach($packages as $package)
            <div class="col-sm-6 col-lg-3" wire:key="package-{{ $package->id }}">
                <div class="card card-md">
                    @if($package->is_popular)
                    <div class="ribbon ribbon-top ribbon-bookmark bg-green">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" 
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" 
                             stroke-linejoin="round" class="icon icon-3">
                            <path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" />
                        </svg>
                    </div>
                    @endif
                    
                    <div class="card-body text-center">
                        <div class="text-uppercase text-secondary font-weight-medium">{{ $package->name }}</div>
                        <div class="display-5 fw-bold my-3">{{ number_format($package->price, 2) }} {{ $package->currency }}</div>
                        
                        <ul class="list-unstyled lh-lg">
                            <li><strong>{{ \App\Helpers\TokenHelper::format($package->token_amount) }}</strong> Token</li>
                            
                            @if($package->features && count($package->features) > 0)
                                @foreach($package->features as $feature)
                                <li>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" 
                                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" 
                                         stroke-linejoin="round" class="icon me-1 text-success icon-2">
                                        <path d="M5 12l5 5l10 -10" />
                                    </svg>
                                    {{ $feature }}
                                </li>
                                @endforeach
                            @endif
                        </ul>
                        
                        @if($package->description)
                        <p class="text-muted small mt-3">{{ $package->description }}</p>
                        @endif
                        
                        @php
                            $dailyUsage = $package->token_amount / 20; // 20 günlük kullanım varsayalım
                            $formattedDaily = \App\Helpers\TokenHelper::format($dailyUsage);
                        @endphp
                        <div class="small text-center mt-2">
                            <span class="badge badge-outline text-blue">
                                Günlük {{ $formattedDaily }} token • 20 gün kullanım
                            </span>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="d-flex align-items-center">
                                    <span class="badge badge-outline me-2">Sıra: {{ $package->sort_order }}</span>
                                    <span class="badge {{ $package->is_active ? 'badge-outline text-green' : 'badge-outline text-red' }}">
                                        {{ $package->is_active ? 'Aktif' : 'Pasif' }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="btn-list">
                                    <button wire:click="toggleActive({{ $package->id }})" 
                                            class="btn btn-sm btn-{{ $package->is_active ? 'success' : 'warning' }}"
                                            title="{{ $package->is_active ? 'Pasif Yap' : 'Aktif Yap' }}">
                                        <i class="fas fa-{{ $package->is_active ? 'toggle-on' : 'toggle-off' }}"></i>
                                    </button>
                                    
                                    <button onclick="editPackage({{ $package->id }})" 
                                            class="btn btn-sm btn-primary" title="Düzenle">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    
                                    <button wire:click="togglePopular({{ $package->id }})" 
                                            class="btn btn-sm btn-{{ $package->is_popular ? 'warning' : 'outline-warning' }}"
                                            title="{{ $package->is_popular ? 'Popülerlikten Çıkar' : 'Popüler Yap' }}">
                                        <i class="fas fa-star"></i>
                                    </button>
                                    
                                    <button wire:click="confirmDelete({{ $package->id }})" 
                                            class="btn btn-sm btn-outline-danger" title="Sil">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <!-- Empty State -->
        <div class="empty">
            <div class="empty-img">
                <i class="fas fa-box text-muted" style="font-size: 64px;"></i>
            </div>
            <p class="empty-title">Henüz paket tanımlanmamış</p>
            <p class="empty-subtitle text-muted">
                İlk token paketinizi oluşturmak için yeni paket oluşturun.
            </p>
            <div class="empty-action">
                <button onclick="openCreateModal()" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>İlk Paketi Oluştur
                </button>
            </div>
        </div>
        @endif
    </div>
    
    @if($packages->hasPages())
    <!-- Pagination -->
    <div class="card-footer">
        {{ $packages->links() }}
    </div>
    @endif
</div>

@push('scripts')
<!-- SortableJS for drag & drop -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    function initializeSortable() {
        const tbody = document.querySelector('#sortable-table');
        if (tbody) {
            new Sortable(tbody, {
                animation: 150,
                handle: '.sort-id',
                ghostClass: 'opacity-50',
                onEnd: function(evt) {
                    const items = Array.from(tbody.querySelectorAll('tr')).map((tr, index) => ({
                        id: tr.dataset.packageId,
                        order: index + 1
                    }));
                    
                    // Livewire'a sıralama değişikliğini bildir
                    Livewire.dispatch('update-package-order', { packages: items });
                }
            });
        }
    }
    
    // İlk yükleme
    initializeSortable();
    
    // Livewire güncellemeleri sonrası yeniden başlat
    Livewire.hook('message.processed', () => {
        initializeSortable();
    });
});
</script>
@endpush
</div>
