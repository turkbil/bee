@include('settingmanagement::helper')

<div class="card">
    <div class="card-body">
        <div class="row g-3">
            @foreach ($settings->chunk(2) as $chunk)
            @foreach ($chunk as $setting)
            <div class="col-md-6" wire:key="setting-{{ $setting->id }}">
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label">{{ $setting->label }}
                            <span class="ms-2 text-muted small"><code>{{ $setting->key }}</code></span>
                        </label>
                        <button type="button" class="btn btn-link btn-icon p-0 text-muted text-decoration-none"
                            wire:click="resetToDefault({{ $setting->id }})" data-bs-toggle="tooltip"
                            title="Varsayılan değere döndür">
                            <i class="fas fa-undo"></i>
                        </button>
                    </div>

                    @if($setting->type === 'textarea')
                    <textarea wire:model="values.{{ $setting->id }}" class="form-control" rows="3"></textarea>
                    @elseif($setting->type === 'select' && is_array($setting->options))
                    <select wire:model="values.{{ $setting->id }}" class="form-select">
                        @foreach($setting->options as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @elseif($setting->type === 'checkbox')
                    <div class="pretty p-default p-curve p-toggle p-smooth">
                        <input type="checkbox" class="form-check-input" wire:model="values.{{ $setting->id }}">
                        <div class="state p-success p-on">
                            <label>Evet</label>
                        </div>
                        <div class="state p-danger p-off">
                            <label>Hayır</label>
                        </div>
                    </div>
                    @else
                    <input type="{{ $setting->type }}" wire:model="values.{{ $setting->id }}" class="form-control">
                    @endif

                    @if($setting->default_value !== $values[$setting->id])
                    <div class="mt-1">
                        <span class="text-muted small">
                            <i class="fas fa-info-circle me-1"></i>
                            Varsayılan değer: {{ $setting->default_value }}
                        </span>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
            @endforeach
        </div>
    </div>
    <div class="card-footer d-flex align-items-center justify-content-between">
        <a href="{{ route('admin.settingmanagement.items', $groupId) }}" class="btn">
            <i class="fas fa-arrow-left me-2"></i>
            Geri Dön
        </a>

        <button type="button" class="btn btn-primary" wire:click="save">
            <span class="d-flex align-items-center">
                <span wire:loading.remove wire:target="save">
                    <i class="fas fa-save me-2"></i>
                    Kaydet
                </span>
                <span wire:loading wire:target="save">
                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                    Kaydediliyor...
                </span>
            </span>
        </button>
    </div>
</div>