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
                                            @if ($editingDomainId !== $domain['id'])
                                            <button class="btn btn-outline-secondary"
                                                wire:click="startEditingDomain({{ $domain['id'] }}, '{{ $domain['domain'] }}')">Düzenle</button>
                                            <button class="btn btn-outline-danger"
                                                wire:click="$dispatch('showDeleteModal', 'domain', {{ $domain['id'] }}, '{{ $domain['domain'] }}', '{{ $tenantId }}')">
                                                Sil
                                            </button>
                                            @endif
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
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
            </div>
        </div>
    </div>
</div>