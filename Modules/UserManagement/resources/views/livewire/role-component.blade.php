@include('usermanagement::helper')
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
                    <!-- Yeni Rol Butonu -->
                    <a href="{{ route('admin.usermanagement.role.manage') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> Yeni Rol
                    </a>
                    <!-- Sayfa Adeti Seçimi -->
                    <div style="min-width: 60px">
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

        <!-- Roller Listesi (Card Tasarım) -->
        <div class="row row-cards">
            @forelse($roles as $role)
            <div class="col-12 col-sm-6 col-lg-4 col-xl-3" wire:key="role-{{ $role->id }}">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <div class="me-auto d-flex align-items-center">
                                <div class="avatar bg-{{ $role->name == 'root' ? 'red' : 'blue' }}-lt me-3">
                                    {{ strtoupper(substr($role->name, 0, 2)) }}
                                </div>
                                <h3 class="card-title mb-0">{{ $role->name }}</h3>
                            </div>
                            <div class="dropdown">
                                <a href="#" class="btn-action" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a href="{{ route('admin.usermanagement.role.manage', $role->id) }}" class="dropdown-item">
                                        <i class="fas fa-edit me-2"></i> Düzenle
                                    </a>
                                    @if($role->name !== 'root')
                                    <button class="dropdown-item text-danger"
                                            wire:click="confirmDelete({{ $role->id }})"
                                            onclick="return confirm('Bu rolü silmek istediğinize emin misiniz?');">
                                        <i class="fas fa-trash me-2"></i> Sil
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="h3 mb-1">{{ $role->users_count ?? 0 }}</div>
                                    <div class="text-muted small">Kullanıcı</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="h3 mb-1">{{ $role->permissions_count ?? 0 }}</div>
                                    <div class="text-muted small">Yetki</div>
                                </div>
                            </div>
                        </div>
                        
                        @if($role->permissions && $role->permissions->count() > 0)
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($role->permissions->take(4) as $permission)
                            <span class="badge bg-blue-lt">
                                {{ Str::after($permission->name, '.') }}
                            </span>
                            @endforeach
                            @if($role->permissions->count() > 4)
                            <span class="badge bg-gray-lt">+{{ $role->permissions->count() - 4 }}</span>
                            @endif
                        </div>
                        @else
                        <div class="text-muted text-center">
                            <i class="fas fa-plus me-1"></i>
                            Yetki eklenebilir
                        </div>
                        @endif
                    </div>
                    
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted small">
                                ID: {{ $role->id }}
                            </div>
                            <a href="{{ route('admin.usermanagement.role.manage', $role->id) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit me-1"></i> Düzenle
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="empty">
                    <div class="empty-img">
                        <i class="fas fa-user-shield fa-4x text-muted"></i>
                    </div>
                    <p class="empty-title">Henüz rol bulunmuyor</p>
                    <p class="empty-subtitle text-muted">
                        "Yeni Rol" butonunu kullanarak ilk rolünüzü oluşturun.
                    </p>
                    <div class="empty-action">
                        <a href="{{ route('admin.usermanagement.role.manage') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i> Yeni Rol
                        </a>
                    </div>
                </div>
            </div>
            @endforelse
        </div>
    </div>

    {{ $roles->links() }}

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" 
        aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Rolü Sil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        wire:click="cancelDelete"></button>
                </div>
                <div class="modal-body">
                    Bu rolü silmek istediğinize emin misiniz?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                        wire:click="cancelDelete">İptal</button>
                    <button type="button" class="btn btn-danger" wire:click="delete">Sil</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            let deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            
            @this.on('showDeleteModal', () => {
                deleteModal.show();
            });

            @this.on('hideDeleteModal', () => {
                deleteModal.hide();
            });

            // Modal kapandığında
            document.getElementById('deleteModal').addEventListener('hidden.bs.modal', () => {
                @this.dispatch('cancelDelete');
            });
        });
    </script>
    @endpush
</div>