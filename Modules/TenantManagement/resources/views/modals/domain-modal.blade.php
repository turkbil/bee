<div class="modal fade" id="modal-domain-management" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Domain Yönetimi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Yeni Domain Ekleme -->
                <div class="row mb-4">
                    <div class="col">
                        <div class="input-group">
                            <input type="text" class="form-control @error('newDomain') is-invalid @enderror" 
                                placeholder="Yeni domain adı" wire:model="newDomain" 
                                wire:keydown.enter="addDomain">
                            <button class="btn btn-primary" wire:click="addDomain">
                                <i class="fas fa-plus me-1"></i> Ekle
                            </button>
                        </div>
                        @error('newDomain') 
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
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
                                                wire:model.defer="editingDomainValue"
                                                wire:keydown.enter="updateDomain({{ $domain['id'] }})">
                                            <button class="btn btn-primary"
                                                wire:click="updateDomain({{ $domain['id'] }})">
                                                <i class="fas fa-check me-1"></i> Kaydet
                                            </button>
                                        </div>
                                        @else
                                        {{ $domain['domain'] }}
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-list">
                                            @if ($editingDomainId !== $domain['id'])
                                            <button class="btn btn-sm btn-outline-secondary"
                                                wire:click="startEditingDomain({{ $domain['id'] }}, '{{ $domain['domain'] }}')">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger"
                                                wire:click="$dispatch('showDeleteDomainModal', [{{ $domain['id'] }}, '{{ $domain['domain'] }}', {{ $tenantId }}])">
                                                <i class="fas fa-trash"></i>
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
                <div class="empty">
                    <div class="empty-img">
                        <img src="{{ asset('tabler/static/illustrations/undraw_empty_re_opql.svg') }}" height="128" alt="">
                    </div>
                    <p class="empty-title">Henüz domain eklenmemiş</p>
                    <p class="empty-subtitle text-muted">
                        Bu tenant için domain eklemek üzere yukarıdaki formu kullanabilirsiniz.
                    </p>
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tamam</button>
            </div>
        </div>
    </div>
</div>