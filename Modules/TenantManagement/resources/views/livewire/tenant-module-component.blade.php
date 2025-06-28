<div>
    @if($modules && $modules->count() > 0)
    <div class="d-flex justify-content-between mb-3">
        <button type="button" class="btn btn-outline-primary" wire:click="toggleSelectAll">
            {{ count($selectedModules) === $modules->count() ? __('tenantmanagement::admin.deselect_all') : __('tenantmanagement::admin.select_all') }}
        </button>
    </div>

    @foreach($moduleGroups as $type => $modules)
    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">{{ ucfirst($type) }}</h3>
        </div>
        <div class="list-group list-group-flush">
            @foreach($modules as $module)
            <div class="list-group-item">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <label class="form-check">
                            <input type="checkbox" class="form-check-input" wire:model="selectedModules"
                                value="{{ $module->module_id }}" @if(in_array((string)$module->module_id,
                            $selectedModules)) checked @endif>
                        </label>
                    </div>
                    <div class="col">
                        <div class="d-flex align-items-center">
                            <div class="flex-fill">
                                <div class="font-weight-medium">{{ $module->display_name }}</div>
                                <div class="text-muted">{{ $module->description }}</div>
                            </div>
                            <div class="text-muted ms-3">
                                <span
                                    class="badge bg-{{ $module->type === 'system' ? 'red' : ($module->type === 'management' ? 'yellow' : 'green') }}-lt">
                                    {{ ucfirst($module->type) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach

    <div class="modal-footer">
        <div class="w-100">
            <div class="row">
                <div class="col">
                    <button type="button" class="btn w-100" data-bs-dismiss="modal">
                        {{ __('tenantmanagement::admin.cancel') }}
                    </button>
                </div>
                <div class="col">
                    <button type="button" class="btn btn-primary w-100" wire:click="save">
                        {{ __('tenantmanagement::admin.save') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="empty">
        <div class="empty-icon">
            <i class="fas fa-cube fa-3x text-muted"></i>
        </div>
        <p class="empty-title">{{ __('tenantmanagement::admin.no_modules_found') }}</p>
        <p class="empty-subtitle text-muted">
            {{ __('tenantmanagement::admin.no_active_modules_info') }}
        </p>
    </div>
    @endif
</div>