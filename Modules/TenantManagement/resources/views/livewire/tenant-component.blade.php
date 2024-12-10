<div> // Modules/TenantManagement/resources/views/livewire/tenant-component.blade.php
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
                                <a href="#">{{ $tenant->name }}</a>
                            </h4>
                            <div class="text-secondary">
                                @if (isset($tenant->domains) && count($tenant->domains) > 0)
                                @php
                                $domainCount = count($tenant->domains);
                                $firstDomain = $tenant->domains[0]['domain'];
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
                            <a href="javascript:void(0);" class="btn btn-outline-info btn-open-domain-modal" data-bs-toggle="modal" data-bs-target="#modal-domain-management" wire:click="loadDomains({{ $tenant->id }})">
                                Domainler
                            </a>
                        </div>
                        <div class="col-auto">
                            <div class="dropdown">
                                <a href="#" class="btn-action" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a href="javascript:void(0);" class="dropdown-item" wire:click.prevent="editTenant({{ $tenant->id }})" data-bs-toggle="modal" data-bs-target="#modal-tenant-edit">
                                        Düzenle
                                    </a>
                                    <a href="javascript:void(0);" class="dropdown-item text-danger" wire:click.prevent="deleteTenant({{ $tenant->id }})">
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
    <!-- Tenant Düzenleme Modal -->
    <div class="modal fade" id="modal-tenant-edit" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tenant Güncelleme</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="tenant-form">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" wire:model="name">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Yetkili Adı Soyadı</label>
                            <input type="text" class="form-control" wire:model="fullname">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email Adresi</label>
                            <input type="email" class="form-control" wire:model="email">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Telefon Numarası</label>
                            <input type="text" class="form-control" wire:model="phone">
                        </div>
                        <div class="mb-3">
                            <div class="pretty p-icon p-toggle p-plain">
                                <input type="checkbox" id="is_active" name="is_active" wire:model="is_active" value="1" />
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
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" wire:click="saveTenant('close')" data-bs-dismiss="modal">Kaydet</button>
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
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="tenant-form">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" wire:model="name">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Yetkili Adı Soyadı</label>
                            <input type="text" class="form-control" wire:model="fullname">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email Adresi</label>
                            <input type="email" class="form-control" wire:model="email">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Telefon Numarası</label>
                            <input type="text" class="form-control" wire:model="phone">
                        </div>
                        <div class="mb-3">
                            <div class="pretty p-icon p-toggle p-plain">
                                <input type="checkbox" id="is_active" name="is_active" wire:model="is_active" value="1" />
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
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" wire:click="saveTenant('close')" data-bs-dismiss="modal">Kaydet</button>
                    </div>
                </form>
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
                                                <input type="text" class="form-control" wire:model.defer="editingDomainValue">
                                                <button class="btn btn-primary" wire:click="updateDomain({{ $domain['id'] }})">Kaydet</button>
                                            </div>
                                            @else
                                            {{ $domain['domain'] }}
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2 justify-content-end">
                                                <button class="btn btn-outline-secondary" wire:click="startEditingDomain({{ $domain['id'] }}, '{{ $domain['domain'] }}')">Düzenle</button>
                                                <button class="btn btn-outline-danger" wire:click="deleteDomain({{ $domain['id'] }})">Sil</button>
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
</div>
