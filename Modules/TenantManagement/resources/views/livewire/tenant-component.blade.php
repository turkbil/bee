@include('tenantmanagement::helper')
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
                        placeholder="Aramak için yazmaya başlayın...">
                </div>
            </div>
            <!-- Ortadaki Loading -->
            <div class="col position-relative">
                <div wire:loading
                    wire:target="render, search, perPage, sortBy, gotoPage, previousPage, nextPage, saveTenant, deleteTenant, loadDomains, addDomain, updateDomain, deleteDomain"
                    class="position-absolute top-50 start-50 translate-middle text-center"
                    style="width: 100%; max-width: 250px;">
                    <div class="small text-muted mb-2">Güncelleniyor...</div>
                    <div class="progress mb-1">
                        <div class="progress-bar progress-bar-indeterminate"></div>
                    </div>
                </div>
            </div>
            <!-- Sağ Taraf -->
            <div class="col">
                <div class="d-flex align-items-center justify-content-end gap-3">
                    <!-- Sayfa Adeti Seçimi -->
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

        <!-- Tenant Listesi -->
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
                                    <a href="javascript:void(0);" class="text-reset" wire:click.prevent="editTenant('{{ $tenant->id }}')" data-bs-toggle="modal" data-bs-target="#modal-tenant-edit">{{ $tenant->title ?? 'Bilinmeyen Ad' }}</a>
                                </h4>
                                <div class="text-secondary">
                                    @if (method_exists($tenant, 'domains') && $tenant->domains && $tenant->domains->count() > 0)
                                    @php
                                    $domainCount = $tenant->domains->count();
                                    $firstDomain = $tenant->domains->first()->domain;
                                    @endphp
                                    <a href="http://{{ $firstDomain }}" class="text-muted" target="_blank">{{ $firstDomain }}</a>
                                    @if ($domainCount > 1)
                                    +{{ $domainCount - 1 }}
                                    @endif
                                    @else
                                    -
                                    @endif
                                </div>
                                <div class="small mt-1">
                                    <a href="javascript:void(0);" class="text-decoration-none" wire:click.prevent="toggleActive('{{ $tenant->id }}')">
                                        @if($tenant->is_active)
                                        <span class="badge bg-green fa-fade"></span> <span class="text-muted">Online</span>
                                        @else
                                        <span class="badge bg-red fa-fade"></span> <span class="text-muted">Offline</span>
                                        @endif
                                    </a>
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
                                <div class="dropdown">
                                    <a href="#" class="btn-action" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a href="javascript:void(0);" class="dropdown-item"
                                            wire:click.prevent="manageModules('{{ $tenant->id }}')" data-bs-toggle="modal"
                                            data-bs-target="#modal-module-management">
                                            Modülleri Yönet
                                        </a>
                                        <a href="javascript:void(0);" class="dropdown-item"
                                            wire:click.prevent="editTenant('{{ $tenant->id }}')" data-bs-toggle="modal"
                                            data-bs-target="#modal-tenant-edit">
                                            Düzenle
                                        </a>
                                        <a href="javascript:void(0);" class="dropdown-item text-danger"
                                            wire:click.prevent="deleteTenant('{{ $tenant->id }}')">
                                            Sil
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
    
    <!-- Tenant Düzenleme Modal -->
    <div class="modal fade" id="modal-tenant-edit" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tenant Güncelleme</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="resetForm"></button>
                </div>
                <form wire:submit.prevent="saveTenant('close')">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" wire:model="name">
                            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Yetkili Adı Soyadı</label>
                            <input type="text" class="form-control" wire:model="fullname">
                            @error('fullname') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email Adresi</label>
                            <input type="email" class="form-control" wire:model="email">
                            @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Telefon Numarası</label>
                            <input type="text" class="form-control" wire:model="phone">
                            @error('phone') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <div class="pretty p-default p-curve p-toggle p-smooth">
                                <input type="checkbox" id="is_active" name="is_active" wire:model="is_active" />
                                <div class="state p-success p-on">
                                    <label>Aktif</label>
                                </div>
                                <div class="state p-danger p-off">
                                    <label>Aktif Değil</label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tema</label>
                            <select class="form-select" wire:model="theme_id">
                                @foreach($themes as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('theme_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        
                        @if($tenantId && $editingTenant)
                        <div class="card bg-light">
                            <div class="card-body p-3">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <div class="form-text mb-1">
                                            <span class="text-secondary">
                                                <i class="fas fa-database me-1"></i>
                                                <strong>Tenant ID:</strong> {{ $tenantId }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-text mb-1">
                                            <span class="text-secondary">
                                                <i class="fas fa-server me-1"></i>
                                                <strong>Veritabanı:</strong> {{ $editingTenant->tenancy_db_name }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <div class="w-100">
                            <div class="row">
                                <div class="col">
                                    <button type="button" class="btn w-100" data-bs-dismiss="modal" wire:click="resetForm">
                                        İptal
                                    </button>
                                </div>
                                <div class="col">
                                    <button type="submit" class="btn btn-primary w-100">Kaydet</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Tenant Ekleme Modal -->
    <div class="modal fade" id="modal-tenant-add" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Yeni Tenant Ekleme</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="resetForm"></button>
                </div>
                <form wire:submit.prevent="saveTenant('close')">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" wire:model="name">
                            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Yetkili Adı Soyadı</label>
                            <input type="text" class="form-control" wire:model="fullname">
                            @error('fullname') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email Adresi</label>
                            <input type="email" class="form-control" wire:model="email">
                            @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Telefon Numarası</label>
                            <input type="text" class="form-control" wire:model="phone">
                            @error('phone') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <div class="pretty p-icon p-toggle p-plain">
                                <input type="checkbox" id="is_active" name="is_active" wire:model="is_active"
                                    value="1" />
                                <div class="state p-on">
                                    <i class="icon fa-regular fa-square-check"></i>
                                    <label>Aktif / Online</label>
                                </div>
                                <div class="state p-off">
                                    <i class="icon fa-regular fa-square"></i>
                                    <label>Aktif Değil / Offline</label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tema</label>
                            <select class="form-select" wire:model="theme_id">
                                @foreach($themes as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('theme_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="w-100">
                            <div class="row">
                                <div class="col">
                                    <button type="button" class="btn w-100" data-bs-dismiss="modal" wire:click="resetForm">
                                        İptal
                                    </button>
                                </div>
                                <div class="col">
                                    <button type="submit" class="btn btn-primary w-100">Kaydet</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modül Yönetimi Modal -->
    <div class="modal fade" id="modal-module-management" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modül Yönetimi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if($tenantId)
                    <livewire:tenant-module-component :tenant-id="$tenantId" :key="'tm-'.$tenantId.'-'.$refreshModuleKey" />
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Domain Yönetimi Modal -->
    <div class="modal fade" id="modal-domain-management" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Domain Yönetimi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Yeni Domain Ekleme -->
                    <div class="input-group mb-4">
                        <input type="text" class="form-control" placeholder="Yeni domain ekle" wire:model="newDomain">
                        <button class="btn btn-primary" wire:click="addDomain">Ekle</button>
                    </div>
                    <!-- Ekli Domainler Tablosu -->
                    @if (count($domains) > 0)
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Ekli Domainler</h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>Domain</th>
                                        <th class="w-1">İşlem</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($domains as $domain)
                                    <tr>
                                        <td>
                                            @if ($editingDomainId === $domain['id'])
                                            <div class="input-group">
                                                <input type="text" class="form-control"
                                                    wire:model.defer="editingDomainValue">
                                                <button class="btn btn-primary"
                                                    wire:click="updateDomain({{ $domain['id'] }})">Kaydet</button>
                                            </div>
                                            @else
                                            {{ $domain['domain'] }}
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2 justify-content-end">
                                                <button class="btn btn-outline-secondary"
                                                    wire:click="startEditingDomain({{ $domain['id'] }}, '{{ $domain['domain'] }}')">Düzenle</button>
                                                <button class="btn btn-outline-danger"
                                                    wire:click="deleteDomain({{ $domain['id'] }})">Sil</button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @else
                    <div class="alert alert-secondary text-center">
                        Ekli domain bulunmamaktadır.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            const modalEdit = document.getElementById('modal-tenant-edit');
            const modalAdd = document.getElementById('modal-tenant-add');
            const modalModule = document.getElementById('modal-module-management');
            const modalDomain = document.getElementById('modal-domain-management');
            
            // Modal kapanırken form resetleme
            modalEdit.addEventListener('hidden.bs.modal', () => {
                @this.resetForm();
            });
            
            modalAdd.addEventListener('hidden.bs.modal', () => {
                @this.resetForm();
            });
            
            Livewire.on('hideModal', ({ id }) => {
                const modal = document.getElementById(id);
                const modalInstance = bootstrap.Modal.getInstance(modal);
                if (modalInstance) {
                    modalInstance.hide();
                }
            });
        });
    </script>
    @endpush
</div>