@include('usermanagement::helper')
<div>
    <div class="container-xl">
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        {{ $user->name }} - Modül İzinleri
                    </h2>
                </div>
                <div class="col-auto ms-auto">
                    <div class="btn-list">
                        <a href="{{ route('admin.usermanagement.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-2"></i>
                            Kullanıcı Listesine Dön
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-3">
            <!-- Sol taraf - Modül listesi -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Modüller</h3>
                        <div class="card-actions">
                            <div class="input-icon">
                                <span class="input-icon-addon">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Modül ara...">
                            </div>
                        </div>
                    </div>
                    <div class="list-group list-group-flush overflow-auto" style="max-height: 600px;">
                        @forelse($modules as $module)
                        <a href="#" 
                            wire:click.prevent="selectModule('{{ $module->name }}')"
                            class="list-group-item list-group-item-action d-flex align-items-center {{ $selectedModule == $module->name ? 'active' : '' }}">
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-sm bg-{{ $module->is_active ? 'green' : 'red' }}-lt me-3">
                                    <i class="fas fa-puzzle-piece"></i>
                                </span>
                                <div>
                                    <strong>{{ $module->display_name }}</strong>
                                    <div class="text-muted small">{{ $module->name }}</div>
                                </div>
                            </div>
                        </a>
                        @empty
                        <div class="list-group-item">
                            <div class="text-muted">Modül bulunamadı</div>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
            
            <!-- Sağ taraf - İzin ayarları -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">
                            @if($selectedModuleData)
                            {{ $selectedModuleData->display_name }} İzinleri
                            @else
                            Modül İzinleri
                            @endif
                        </h3>
                    </div>
                    <div class="card-body">
                        @if($selectedModuleData)
                            <div class="row">
                                @forelse($userPermissions as $type => $isActive)
                                <div class="col-md-6 mb-3">
                                    <label class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" 
                                            @if($isActive) checked @endif
                                            wire:click="togglePermission('{{ $type }}')">
                                        <span class="form-check-label">
                                            <i class="fas fa-{{ $this->getPermissionIcon($type) }} me-2 text-blue"></i>
                                            {{ $permissionLabels[$type] ?? $type }}
                                        </span>
                                    </label>
                                </div>
                                @empty
                                <div class="col-12">
                                    <div class="alert alert-warning">
                                        Bu modül için kullanılabilir izin bulunamadı. Önce modül izinlerini tanımlayın.
                                    </div>
                                </div>
                                @endforelse
                            </div>
                        @else
                            <div class="empty">
                                <div class="empty-icon">
                                    <i class="fas fa-puzzle-piece"></i>
                                </div>
                                <p class="empty-title">Modül seçilmedi</p>
                                <p class="empty-subtitle text-muted">
                                    Lütfen izinlerini düzenlemek için sol taraftan bir modül seçin.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>