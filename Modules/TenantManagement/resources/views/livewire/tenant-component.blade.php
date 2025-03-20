@include('tenantmanagement::helper')
<div>
    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col">
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                            placeholder="Aramak için yazmaya başlayın...">
                    </div>
                </div>
                <div class="col position-relative">
                    <div wire:loading
                        wire:target="render, search, perPage, sortBy, gotoPage, previousPage, nextPage, saveTenant, loadDomains, addDomain, updateDomain"
                        class="position-absolute top-50 start-50 translate-middle text-center"
                        style="width: 100%; max-width: 250px;">
                        <div class="small text-muted mb-2">Güncelleniyor...</div>
                        <div class="progress mb-1">
                            <div class="progress-bar progress-bar-indeterminate"></div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="d-flex align-items-center justify-content-end gap-3">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-tenant-manage"
                            wire:click="resetForm">
                            <i class="fas fa-plus me-2"></i>Yeni Tenant
                        </button>
                        <div style="min-width: 70px">
                            <select wire:model.live="perPage" class="form-select">
                                <option value="10">10</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                                <option value="500">500</option>
                                <option value="1000">1000</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row row-cards">
                @foreach ($tenants as $tenant)
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="row g-4 align-items-center">
                                <div class="col-auto">
                                    <div class="avatar avatar-lg bg-blue text-white text-center">
                                        <span class="avatar avatar-xl px-3 rounded">{{ $tenant->id }}</span>
                                    </div>
                                </div>
                                <div class="col">
                                    <h4 class="card-title m-0">
                                        <a href="#">{{ $tenant->data['name'] ?? $tenant->title ?? 'Bilinmeyen Ad' }}</a>
                                    </h4>
                                    <div class="text-secondary">
                                        @if ($tenant->domains->count() > 0)
                                        @php
                                        $domainCount = $tenant->domains->count();
                                        $firstDomain = $tenant->domains->first()->domain;
                                        @endphp
                                        {{ $firstDomain }}
                                        @if ($domainCount > 1)
                                        +{{ $domainCount - 1 }}
                                        @endif
                                        @else
                                        <span class="text-muted fst-italic">Domain Tanımlanmamış</span>
                                        @endif
                                    </div>
                                    <div class="small mt-1">
                                        @if($tenant->is_active)
                                        <span class="badge bg-green fa-fade"></span> Online
                                        @else
                                        <span class="badge bg-red fa-fade"></span> Offline
                                        @endif
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <div class="btn-list">
                                        <a href="javascript:void(0);" class="btn btn-outline-primary btn-open-module-modal"
                                            data-bs-toggle="modal" data-bs-target="#modal-module-management"
                                            wire:click="$set('tenantId', '{{ $tenant->id }}')">
                                            <i class="fas fa-cubes me-1"></i> Modüller
                                        </a>
                                        <a href="javascript:void(0);" class="btn btn-outline-info btn-open-domain-modal"
                                            data-bs-toggle="modal" data-bs-target="#modal-domain-management"
                                            wire:click="loadDomains('{{ $tenant->id }}')">
                                            <i class="fas fa-globe me-1"></i> Domainler
                                        </a>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <div class="dropdown">
                                        <a href="#" class="btn-action" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a href="javascript:void(0);" class="dropdown-item"
                                                wire:click="editTenant('{{ $tenant->id }}')" data-bs-toggle="modal"
                                                data-bs-target="#modal-tenant-manage">
                                                <i class="fas fa-edit me-2"></i> Düzenle
                                            </a>
                                            <a href="javascript:void(0);" class="dropdown-item text-danger"
                                                wire:click="$dispatch('showDeleteTenantModal', { 
                                                    id: {{ $tenant->id }}, 
                                                    title: '{{ addslashes($tenant->data['name'] ?? $tenant->title) }}'
                                                })">
                                                <i class="fas fa-trash me-2"></i> Sil
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach

                @if($tenants->count() === 0)
                <div class="col-12">
                    <div class="empty">
                        <div class="empty-img">
                            <img src="{{ asset('tabler/static/illustrations/undraw_no_data_re_kwbl.svg') }}" height="128" alt="">
                        </div>
                        <p class="empty-title">Kayıt Bulunamadı</p>
                        <p class="empty-subtitle text-muted">
                            Henüz tenant eklenmemiş veya arama kriterlerinize uygun tenant bulunmuyor.
                        </p>
                        <div class="empty-action">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-tenant-manage" wire:click="resetForm">
                                <i class="fas fa-plus me-2"></i> Yeni Tenant Ekle
                            </button>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
        
        {{ $tenants->links() }}
    </div>
    
    @include('tenantmanagement::modals.tenant-modal')
    @include('tenantmanagement::modals.domain-modal')
    @include('tenantmanagement::modals.module-modal')
    
    <livewire:tenantmanagement::modals.delete-tenant-modal />
    <livewire:tenantmanagement::modals.delete-domain-modal />

    @push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('hideModal', ({ id }) => {
                const modal = document.getElementById(id);
                const modalInstance = bootstrap.Modal.getInstance(modal);
                if (modalInstance) {
                    modalInstance.hide();
                }
            });
            
            Livewire.on('refreshList', () => {
                Livewire.dispatch('$refresh');
                
                // Modal arka planını temizle
                setTimeout(() => {
                    const modalBackdrops = document.querySelectorAll('.modal-backdrop');
                    modalBackdrops.forEach(backdrop => backdrop.remove());
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';
                }, 300);
            });
            
            // Tenant modalı kapatıldığında form temizleyelim
            const tenantModal = document.getElementById('modal-tenant-manage');
            if (tenantModal) {
                tenantModal.addEventListener('hidden.bs.modal', function () {
                    Livewire.dispatch('resetTenantForm');
                });
            }
            
            // Domain modalı kapatıldığında güncelleyelim
            const domainModal = document.getElementById('modal-domain-management');
            if (domainModal) {
                domainModal.addEventListener('hidden.bs.modal', function () {
                    if (@this.tenantId) {
                        Livewire.dispatch('refreshDomains', @this.tenantId);
                    }
                });
            }
            
            // Modül modalı kapatıldığında listeyi güncelleyelim
            const moduleModal = document.getElementById('modal-module-management');
            if (moduleModal) {
                moduleModal.addEventListener('hidden.bs.modal', function () {
                    Livewire.dispatch('$refresh');
                });
                
                moduleModal.addEventListener('shown.bs.modal', function () {
                    // Modül komponenti yüklendikten sonra tenantId güncelleme olayını tetikle
                    if (@this.tenantId) {
                        // TenantModuleComponent'e tenantId değiştiğini bildir
                        Livewire.dispatch('tenantIdUpdated', @this.tenantId);
                    }
                });
            }
        });
    </script>
    @endpush
</div>