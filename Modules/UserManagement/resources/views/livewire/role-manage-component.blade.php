@include('usermanagement::helper')
<div>
    @include('admin.partials.error_message')
    <form wire:submit.prevent="save">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                    <li class="nav-item">
                        <a href="#tabs-1" class="nav-link active" data-bs-toggle="tab">
                            <i class="fas fa-edit me-2"></i>{{ __('usermanagement::admin.basic_info') }}
                        </a>
                    </li>
                    @if($roleId)
                    <li class="nav-item">
                        <a href="#tabs-2" class="nav-link" data-bs-toggle="tab">
                            <i class="fas fa-key me-2"></i>{{ __('usermanagement::admin.permissions') }}
                        </a>
                    </li>
                    @endif
                </ul>
                
                @if($roleId)
                <div class="card-actions">
                    <span class="badge {{ $role->isBaseRole() ? 'bg-red' : 'bg-green' }}">
                        <i class="fas {{ $role->isBaseRole() ? 'fa-lock' : 'fa-unlock' }} me-1"></i>
                        {{ $role->isBaseRole() ? __('usermanagement::admin.protected_role') : __('usermanagement::admin.editable_role') }}
                    </span>
                </div>
                @endif
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <!-- Temel Bilgiler -->
                    <div class="tab-pane fade active show" id="tabs-1">
                        <div class="form-floating mb-3">
                            <input type="text" wire:model="inputs.name"
                                class="form-control @error('inputs.name') is-invalid @enderror"
                                placeholder="Rol adı"
                                {{ $roleId && $role->isBaseRole() ? 'readonly' : '' }}>
                            <label>{{ __('usermanagement::admin.role_name') }}</label>
                            @error('inputs.name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <input type="text" wire:model="inputs.guard_name"
                                class="form-control @error('inputs.guard_name') is-invalid @enderror"
                                placeholder="Guard adı"
                                {{ $roleId && $role->isBaseRole() ? 'readonly' : '' }}>
                            <label>{{ __('usermanagement::admin.guard_name') }}</label>
                            @error('inputs.guard_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <textarea wire:model="inputs.description" 
                                class="form-control @error('inputs.description') is-invalid @enderror" 
                                data-bs-toggle="autosize"
                                placeholder="Rol açıklaması"></textarea>
                            <label>{{ __('usermanagement::admin.description') }}</label>
                            @error('inputs.description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if(!($roleId && $role->isBaseRole()))
                        <div class="mb-3">
                            <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                <input type="checkbox" id="is_active" name="is_active" wire:model="inputs.is_active"
                                    value="1" {{ (!isset($inputs['is_active']) || $inputs['is_active']) ? 'checked' : '' }} />

                                <div class="state p-success p-on ms-2">
                                    <label>{{ __('usermanagement::admin.active') }}</label>
                                </div>
                                <div class="state p-danger p-off ms-2">
                                    <label>{{ __('usermanagement::admin.inactive') }}</label>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    @if($roleId)
                    <!-- İzinler -->
                    <div class="tab-pane fade" id="tabs-2">
                        <h4 class="mb-3">Rol İzinleri</h4>

                        @if(!$role->isBaseRole())
                        @if($shouldShowPermissions && count($groupedPermissions) > 0)
                        <div class="mb-3">
                            <input type="text"
                                wire:model.live="permissionSearch"
                                class="form-control"
                                placeholder="İzin ara...">
                        </div>

                        @foreach($groupedPermissions as $group => $permissions)
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="card-title d-flex align-items-center mb-0">
                                    <label class="form-check me-2">
                                        <input type="checkbox"
                                            class="form-check-input"
                                            wire:click="toggleGroupPermissions('{{ $group }}')"
                                            @if($this->isGroupSelected($group)) checked @endif>
                                    </label>
                                    <span class="text-capitalize">{{ $group }}</span>
                                    <span class="badge bg-blue ms-2">{{ count($permissions) }}</span>
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach($permissions as $permission)
                                    <div class="col-md-4 mb-2">
                                        <label class="form-check">
                                            <input type="checkbox"
                                                wire:model="inputs.permissions"
                                                value="{{ $permission->name }}"
                                                class="form-check-input">
                                            <span class="form-check-label">{{ str_replace($group . '.', '', $permission->name) }}</span>
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            İzin bulunamadı.
                        </div>
                        @endif
                        @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Bu rol korumalıdır ve izinleri değiştirilemez.
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <x-form-footer route="admin.usermanagement.role" :model-id="$roleId" />

        </div>
    </form>
</div>