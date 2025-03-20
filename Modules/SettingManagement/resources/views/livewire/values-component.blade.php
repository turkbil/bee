@include('settingmanagement::helper')

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="card-title">
                <i class="fas fa-cogs me-2"></i>
                {{ $group->name }} - Ayar Değerleri
            </h3>
        </div>
    </div>
    <div class="card-body">
        <div class="row g-3">
            @foreach ($settings->chunk(2) as $chunk)
            @foreach ($chunk as $setting)
            <div class="col-md-6" wire:key="setting-{{ $setting->id }}">
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title d-flex align-items-center">
                            {{ $setting->label }}
                            <span class="ms-2 badge bg-blue-lt">{{ $setting->type }}</span>
                        </h3>
                        <div class="card-actions">
                            <button type="button" class="btn btn-sm" 
                                wire:click="resetToDefault({{ $setting->id }})" 
                                title="Varsayılan değere döndür">
                                <i class="fas fa-undo me-1"></i> Varsayılan
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-0">
                            <small class="text-muted d-block mb-2">
                                <code>{{ $setting->key }}</code>
                            </small>

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
                </div>
            </div>
            @endforeach
            @endforeach
        </div>
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <a href="{{ route('admin.settingmanagement.items', $groupId) }}" class="btn btn-link text-decoration-none">
            <i class="fas fa-arrow-left me-2"></i>
            Geri Dön
        </a>

        <div class="d-flex gap-2">
            <button type="button" class="btn" wire:click="save(false)" wire:loading.attr="disabled"
                wire:target="save">
                <span class="d-flex align-items-center">
                    <span class="ms-2" wire:loading.remove wire:target="save(false)">
                        <i class="fa-thin fa-plus me-2"></i> Kaydet ve Devam Et
                    </span>
                    <span class="ms-2" wire:loading wire:target="save(false)">
                        <i class="fa-duotone fa-solid fa-spinner fa-spin me-2"></i> Kaydet ve Devam Et
                    </span>
                </span>
            </button>

            <button type="button" class="btn btn-primary ms-4" wire:click="save(true)"
                wire:loading.attr="disabled" wire:target="save">
                <span class="d-flex align-items-center">
                    <span class="ms-2" wire:loading.remove wire:target="save(true)">
                        <i class="fa-thin fa-floppy-disk me-2"></i> Kaydet
                    </span>
                    <span class="ms-2" wire:loading wire:target="save(true)">
                        <i class="fa-duotone fa-solid fa-spinner fa-spin me-2"></i> Kaydet
                    </span>
                </span>
            </button>
        </div>
    </div>
</div>