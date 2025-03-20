<div>
    @if($moduleGroups)
    <div class="d-flex justify-content-between mb-3">
        <button type="button" class="btn btn-outline-primary" wire:click="toggleSelectAll">
            {{ count($selectedModules) === $modules->count() ? 'Tümünü Kaldır' : 'Tümünü Seç' }}
        </button>
    </div>

    @foreach($moduleGroups as $group => $modules)
    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">{{ $group ?: 'Genel' }}</h3>
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
        <button type="button" class="btn btn-primary" wire:click="save">
            Kaydet
        </button>
    </div>
    @endif
</div>