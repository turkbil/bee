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
                                placeholder="Yetkilerde ara...">
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="{{ route('admin.usermanagement.permission.manage') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i> Yeni Yetki Ekle
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Yetki Grupları -->
        <div class="row row-cards">
            @forelse($groupedPermissions as $module => $permissions)
            <div class="col-md-6 col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title text-capitalize">
                            <i class="fas fa-folder me-2 text-blue"></i>
                            <a href="{{ route('admin.usermanagement.permission.manage') }}?module={{ $module }}"
                                class="text-decoration-none">
                                {{ $module }}
                            </a>
                        </h3>
                    </div>
                    <div class="list-group list-group-flush">
                        @foreach($permissions as $permission)
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="d-flex align-items-center">
                                        @if($editingPermissionId === $permission->id)
                                        <input type="text" wire:model.defer="editingPermissionName"
                                            class="form-control form-control-sm">
                                        @else
                                        <span class="avatar avatar-xs bg-blue-lt me-2">
                                            <i class="fas fa-check"></i>
                                        </span>
                                        <div>
                                            <div class="fw-bold">{{ Str::title(Str::after($permission->name, '.')) }}
                                            </div>
                                            <div class="text-muted small">{{ $permission->name }}</div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-auto">
                                    @if($editingPermissionId === $permission->id)
                                    <button type="button" class="btn btn-ghost-success btn-icon"
                                        wire:click="saveEditPermission">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button type="button" class="btn btn-ghost-danger btn-icon"
                                        wire:click="cancelEditPermission">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    @else
                                    <button type="button" class="btn btn-ghost-primary btn-icon"
                                        wire:click="startEditPermission({{ $permission->id }}, '{{ $permission->name }}')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-ghost-danger btn-icon"
                                        wire:click="confirmDeletePermission({{ $permission->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="card-footer">
                        <div class="d-flex align-items-center">
                            <div class="text-muted">
                                <i class="fas fa-key me-1"></i> {{ $permissions->count() }} yetki
                            </div>
                            <div class="ms-auto">
                                <a href="{{ route('admin.usermanagement.permission.manage') }}?module={{ $module }}"
                                    class="btn btn-link btn-sm">
                                    <i class="fas fa-plus me-1"></i> Yeni Yetki Ekle
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="empty">
                    <div class="empty-icon">
                        <i class="fas fa-key fa-3x text-muted"></i>
                    </div>
                    <p class="empty-title">Yetki bulunamadı</p>
                    <p class="empty-subtitle text-muted">
                        Arama kriterlerinize uygun yetki bulunmamaktadır.
                    </p>
                    <div class="empty-action">
                        <a href="{{ route('admin.usermanagement.permission.manage') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i> Yeni Yetki Ekle
                        </a>
                    </div>
                </div>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Silme Onay Modal'ı -->
    <div class="modal fade" id="deletePermissionModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Yetki Sil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Bu yetkiyi silmek istediğinizden emin misiniz?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="button" class="btn btn-danger" wire:click="deletePermission">Sil</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
        const modal = document.getElementById('deletePermissionModal');
        
        @this.on('openDeleteModal', () => {
            new bootstrap.Modal(modal).show();
        });
        
        @this.on('closeDeleteModal', () => {
            bootstrap.Modal.getInstance(modal).hide();
        });
    });
    </script>
    @endpush
</div>