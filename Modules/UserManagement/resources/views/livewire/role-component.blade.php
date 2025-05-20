@include('usermanagement::helper')
<div>
    <div class="container-xl">
        <!-- Arama ve Filtre -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row g-3 align-items-center">
                    <div class="col-md-6">
                        <div class="input-icon">
                            <span class="input-icon-addon"><i class="fas fa-search"></i></span>
                            <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                                placeholder="Rollerde ara...">
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="{{ route('admin.usermanagement.role.manage') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i> Yeni Rol Ekle
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Roller -->
        <div class="row row-cards">
            @foreach($roles as $role)
            <div class="col-md-6 col-lg-4">
                <div class="card">
                    <!-- Rol Başlığı -->
                    <div class="card-header d-flex align-items-center">
                        <div class="avatar bg-{{ $role->name == 'Super Admin' ? 'red' : 'blue' }}-lt me-3">
                            {{ strtoupper(substr($role->name, 0, 2)) }}
                        </div>
                        <div>
                            <h3 class="card-title mb-0">{{ $role->name }}</h3>
                            <div class="small text-muted">
                                {{ $role->users_count }} kullanıcı, {{ $role->permissions_count }} yetki
                            </div>
                        </div>
                        <div class="ms-auto">
                            <div class="dropdown">
                                <button class="btn btn-ghost-secondary dropdown-toggle align-text-top"
                                    data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a href="{{ route('admin.usermanagement.role.manage', $role->id) }}" class="dropdown-item">
                                        Düzenle
                                    </a>
                                    <button type="button" class="dropdown-item text-danger"
                                        wire:click="confirmDelete({{ $role->id }})">
                                        Sil
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Yetkiler -->
                    <div class="card-body">
                        <div class="mb-2">
                            <label class="form-label">Yetkiler</label>
                            @foreach($role->permissions->groupBy(fn($p) => Str::before($p->name, '.')) as $module =>
                            $permissions)
                            <div class="mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="text-capitalize fw-bold">{{ $module }}</span>
                                    <small class="text-muted ms-2">({{ $permissions->count() }})</small>
                                </div>
                                <div>
                                    @foreach($permissions as $permission)
                                    <span class="badge bg-blue-lt me-1 mb-1">
                                        {{ Str::after($permission->name, '.') }}
                                    </span>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

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