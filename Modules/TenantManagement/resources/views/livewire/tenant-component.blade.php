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
                                        -
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
                                    <a href="javascript:void(0);" class="btn btn-outline-info btn-open-domain-modal"
                                        data-bs-toggle="modal" data-bs-target="#modal-domain-management"
                                        wire:click="loadDomains('{{ $tenant->id }}')">
                                        Domainler
                                    </a>
                                </div>
                                <div class="col-auto">
                                    <a href="javascript:void(0);" class="btn btn-outline-primary btn-open-module-modal"
                                        data-bs-toggle="modal" data-bs-target="#modal-module-management"
                                        wire:click="$set('tenantId', '{{ $tenant->id }}')">
                                        Modüller
                                    </a>
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
                                                Düzenle
                                            </a>
                                            <a href="javascript:void(0);" class="dropdown-item text-danger"
                                                wire:click="$dispatch('showDeleteModal', { 
                                                    type: 'tenant', 
                                                    id: {{ $tenant->id }}, 
                                                    title: '{{ addslashes($tenant->data['name'] ?? $tenant->title) }}'
                                                })">
                                                <i class="fas fa-trash me-2"></i>Sil
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        
        {{ $tenants->links() }}
    </div>
    
    @include('tenantmanagement::modals.tenant-modal')
    @include('tenantmanagement::modals.domain-modal')
    @include('tenantmanagement::modals.module-modal')
    
    <livewire:modals.delete-modal />

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
            
            Livewire.on('itemDeleted', () => {
                setTimeout(() => {
                    const modalBackdrops = document.querySelectorAll('.modal-backdrop');
                    modalBackdrops.forEach(backdrop => backdrop.remove());
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                }, 300);
            });
        });
    </script>
    @endpush
</div>