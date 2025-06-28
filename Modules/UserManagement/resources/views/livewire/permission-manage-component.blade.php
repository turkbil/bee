@include('usermanagement::helper')
<div>
    <form wire:submit.prevent="save">
        <div class="row">
            <!-- Sol Kolon - Temel Ayarlar -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-shield-alt text-blue me-2"></i>
                            {{ __('usermanagement::general.basic_permission_settings') }}
                        </h3>
                    </div>
                    <div class="card-body">
                        <!-- Guard Name -->
                        <div class="mb-4">
                            <div class="form-floating">
                                <select wire:model.defer="inputs.guard_name"
                                    class="form-select @error('inputs.guard_name') is-invalid @enderror"
                                    data-choices
                                    data-choices-search="false"
                                    data-choices-placeholder="{{ __('usermanagement::general.select_guard_type') }}">
                                    <option value="admin" selected>{{ __('usermanagement::general.admin_guard') }}</option>
                                    <option value="web">{{ __('usermanagement::general.web_guard') }}</option>
                                    <option value="api">{{ __('usermanagement::general.api_guard') }}</option>
                                </select>
                                <label>{{ __('usermanagement::general.permission_guard_type') }}</label>
                                @error('inputs.guard_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">{{ __('usermanagement::general.guard_area_text') }}</small>
                        </div>

                        <!-- Modül Adı -->
                        <div class="mb-4">
                            <div class="form-floating">
                                <input type="text" wire:model.defer="inputs.module_name"
                                    class="form-control @error('inputs.module_name') is-invalid @enderror"
                                    placeholder="{{ __('usermanagement::general.module_name_placeholder') }}">
                                <label>{{ __('usermanagement::general.module_name') }}</label>
                                @error('inputs.module_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">{{ __('usermanagement::general.module_permission_text') }}</small>
                        </div>

                        <!-- Yetki Tipleri -->
                        <div class="mb-4">
                            <label class="form-label d-block">
                                <i class="fas fa-tasks me-1 text-blue"></i>
                                {{ __('usermanagement::general.crud_operations') }}
                            </label>
                            <div class="row g-2">
                                @foreach([
                                'view' => ['icon' => 'eye', 'color' => 'info', 'text' => __('usermanagement::general.view')],
                                'create' => ['icon' => 'plus', 'color' => 'success', 'text' => __('usermanagement::general.create')],
                                'update' => ['icon' => 'edit', 'color' => 'warning', 'text' => __('usermanagement::general.update')],
                                'delete' => ['icon' => 'trash', 'color' => 'danger', 'text' => __('usermanagement::general.delete')]
                                ] as $type => $attrs)
<div class="col-6 mb-2">
    <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
        <input type="checkbox"
               wire:model.defer="inputs.permission_types"
               value="{{ $type }}"
               {{ in_array($type, $inputs['permission_types'] ?? []) ? 'checked' : '' }}>
        <div class="state p-success p-on ms-2">
            <label>
                <i class="fas fa-{{ $attrs['icon'] }} me-2"></i>
                {{ $attrs['text'] }}
            </label>
        </div>
        <div class="state p-danger p-off ms-2">
            <label>
                <i class="fas fa-{{ $attrs['icon'] }} me-2"></i>
                {{ $attrs['text'] }}
            </label>
        </div>
    </div>
</div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Yetki Oluşturma Butonu -->
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-primary w-100" wire:loading.attr="disabled"
                                wire:click="generatePermissions">
                                <i class="fas fa-magic me-2"></i>
                                {{ __('usermanagement::general.generate_permissions') }}
                                <div wire:loading wire:target="generatePermissions">
                                    <span class="spinner-border spinner-border-sm ms-2" role="status"></span>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sağ Kolon - {{ __('usermanagement::general.generated_permissions') }} -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-list-check text-green me-2"></i>
                            {{ __('usermanagement::general.generated_permissions') }}
                        </h3>
                        <div class="card-actions">
                            <span class="badge bg-blue">{{ __('usermanagement::general.permissions_count', ['count' => count($generatedPermissions)]) }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Manuel Yetki Ekleme -->
                        <div class="mb-4">
                            <div class="form-floating mb-2">
                                <input type="text" wire:model.defer="manualPermission"
                                    class="form-control @error('manualPermission') is-invalid @enderror"
                                    placeholder="{{ __('usermanagement::general.custom_permission_placeholder') }}">
                                <label>{{ __('usermanagement::general.add_custom_permission') }}</label>
                                @error('manualPermission')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm w-100"
                                wire:click="addManualPermission">
                                <i class="fas fa-plus me-1"></i>
                                {{ __('usermanagement::general.add_custom_permission') }}
                            </button>
                        </div>

                        <!-- Yetki Listesi -->
                        @if(count($generatedPermissions) > 0)
                        <div class="list-group">
                            @foreach($generatedPermissions as $index => $permission)
                            <div class="list-group-item d-flex align-items-center">
                                <div class="d-flex align-items-center flex-grow-1">
                                    @if($editingIndex === $index)
                                    <input type="text" wire:model.defer="generatedPermissions.{{ $index }}"
                                        class="form-control form-control-sm">
                                    @else
                                    <span class="avatar avatar-xs bg-blue-lt me-2">
                                        <i class="fas fa-check"></i>
                                    </span>
                                    <div>
                                        <div class="font-weight-medium">{{ $permission }}</div>
                                        <div class="text-muted small">{{ $inputs['guard_name'] }} guard</div>
                                    </div>
                                    @endif
                                </div>
                                <div class="col-auto">
                                    @if($editingIndex === $index)
                                    <button type="button" class="btn btn-ghost-success btn-icon"
                                        wire:click="saveEdit({{ $index }})">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button type="button" class="btn btn-ghost-danger btn-icon" wire:click="cancelEdit">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    @else
                                    <button type="button" class="btn btn-ghost-primary btn-icon"
                                        wire:click="startEdit({{ $index }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-ghost-danger btn-icon"
                                        wire:click="deletePermission('{{ $permission }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="empty">
                            <div class="empty-icon">
                                <i class="fas fa-shield-alt text-muted" style="font-size: 2rem;"></i>
                            </div>
                            <p class="empty-title">{{ __('usermanagement::general.no_permissions_yet') }}</p>
                            <p class="empty-subtitle text-muted">
                                {{ __('usermanagement::general.module_permission_create_text') }}
                            </p>
                        </div>
                        @endif
                    </div>

                    @if(count($generatedPermissions) > 0)
                    <div class="card-footer d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            {{ __('usermanagement::general.total_permissions_created', ['count' => count($generatedPermissions)]) }}
                        </div>
                        <div>
                            <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                                <i class="fas fa-save me-2"></i>
                                {{ __('usermanagement::general.save_permissions') }}
                                <div wire:loading wire:target="save">
                                    <span class="spinner-border spinner-border-sm ms-2" role="status"></span>
                                </div>
                            </button>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </form>
</div>