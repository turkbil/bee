<div>
    @if($modules && $modules->count() > 0)
    <div class="d-flex justify-content-between mb-3">
        <button type="button" class="btn btn-outline-primary" wire:click="toggleSelectAll">
            {{ count($selectedModules) === $modules->count() ? 'Tümünü Kaldır' : 'Tümünü Seç' }}
        </button>
        
        <span wire:loading wire:target="toggleSelectAll" class="spinner-border spinner-border-sm" role="status"></span>
    </div>

    @foreach($moduleGroups as $type => $modules)
    <div class="card mb-3">
        <div class="card-header d-flex align-items-center">
            @switch($type)
            @case('system')
            <i class="fas fa-shield-alt me-2 text-muted"></i>
            <h3 class="card-title mb-0">Sistem Modülleri</h3>
            @break
            @case('management')
            <i class="fas fa-cogs me-2 text-muted"></i>
            <h3 class="card-title mb-0">Yönetim Modülleri</h3>
            @break
            @case('content')
            <i class="fas fa-file-alt me-2 text-muted"></i>
            <h3 class="card-title mb-0">İçerik Modülleri</h3>
            @break
            @default
            <i class="fas fa-puzzle-piece me-2 text-muted"></i>
            <h3 class="card-title mb-0">{{ ucfirst($type) }} Modüller</h3>
            @endswitch
            <div class="ms-auto">
                <span class="badge bg-primary">
                    {{ $modules->count() }} modül
                </span>
            </div>
        </div>
        <div class="list-group list-group-flush">
            @foreach($modules as $module)
            <div class="list-group-item">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <label class="form-check">
                            <input type="checkbox" class="form-check-input" wire:model.live="selectedModules"
                                value="{{ $module->module_id }}">
                        </label>
                    </div>
                    <div class="col">
                        <div class="d-flex align-items-center">
                            <div class="flex-fill">
                                <div class="font-weight-medium">{{ $module->display_name }}</div>
                                <div class="text-muted small">{{ $module->description }}</div>
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
                        İptal
                    </button>
                </div>
                <div class="col">
                    <button type="button" class="btn btn-primary w-100" wire:click="save" wire:loading.attr="disabled">
                        <span wire:loading wire:target="save" class="spinner-border spinner-border-sm me-2" role="status"></span>
                        Kaydet
                    </button>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="empty">
        <div class="empty-img">
            <img src="{{ asset('tabler/static/illustrations/undraw_no_data_re_kwbl.svg') }}" 
                 height="128" alt="">
        </div>
        <p class="empty-title">Modül bulunamadı</p>
        <p class="empty-subtitle text-muted">
            Modül Yönetimi sayfasından yeni modüller ekleyebilirsiniz.
        </p>
    </div>
    @endif
</div>