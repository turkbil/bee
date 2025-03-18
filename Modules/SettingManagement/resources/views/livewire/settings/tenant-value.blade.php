@include('settingmanagement::helper')

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3 class="card-title">{{ $setting->label }}</h3>
                <p class="text-muted mb-0">
                    <code>{{ $setting->key }}</code>
                </p>
            </div>
            <label class="form-check form-switch m-0">
                <input class="form-check-input" type="checkbox" wire:model.live="useDefault"
                    wire:change="toggleDefault">
                <span class="form-check-label">Varsayılan Değeri Kullan</span>
            </label>
        </div>
    </div>
    <div class="card-body">
        <div class="mb-3">
            @if($setting->type === 'textarea')
            <textarea wire:model="value" class="form-control" rows="4" {{ $useDefault ? 'disabled' : '' }}></textarea>
            @elseif($setting->type === 'select' && is_array($setting->options))
            <select wire:model="value" class="form-select" {{ $useDefault ? 'disabled' : '' }}>
                @foreach($setting->options as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
            @elseif($setting->type === 'file')
            <div class="row align-items-end">
                <div class="col">
                    @if($value)
                    <div class="mb-2">
                        @if(Str::startsWith($value, ['jpg', 'jpeg', 'png', 'gif']))
                        <img src="{{ Storage::url($value) }}" alt="Current file" class="img-fluid"
                            style="max-height: 200px">
                        @else
                        <div class="d-flex align-items-center">
                            <i class="fas fa-file me-2"></i>
                            <span>{{ basename($value) }}</span>
                        </div>
                        @endif
                    </div>
                    @endif
                    <input type="file" wire:model="tempFile" class="form-control" {{ $useDefault ? 'disabled' : '' }}>
                </div>
            </div>
            @elseif($setting->type === 'checkbox')
            <label class="form-check">
                <input type="checkbox" class="form-check-input" wire:model="value" {{ $useDefault ? 'disabled' : '' }}>
                <span class="form-check-label">{{ $value ? 'Evet' : 'Hayır' }}</span>
            </label>
            @else
            <input type="{{ $setting->type }}" wire:model="value" class="form-control" {{ $useDefault ? 'disabled' : ''
                }}>
            @endif
        </div>

        <div class="row align-items-center mt-4">
            <div class="col">
                <div class="datagrid">
                    <div class="datagrid-item">
                        <div class="datagrid-title">Ayar Tipi</div>
                        <div class="datagrid-content">
                            <span class="badge bg-blue-lt">{{ $setting->type }}</span>
                        </div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Varsayılan Değer</div>
                        <div class="datagrid-content">
                            @if($setting->default_value)
                            {{ $setting->default_value }}
                            @else
                            <span class="text-muted">Tanımlanmamış</span>
                            @endif
                        </div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Durum</div>
                        <div class="datagrid-content">
                            @if($setting->is_active)
                            <span class="status status-green">Aktif</span>
                            @else
                            <span class="status status-red">Pasif</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <a href="{{ url()->previous() }}" class="btn">
            <i class="fas fa-arrow-left me-2"></i>
            Geri Dön
        </a>
        <div class="btn-list">
            @if($setting->type === 'file' && $value)
            <a href="{{ Storage::url($value) }}" target="_blank" class="btn btn-secondary">
                <i class="fas fa-eye me-2"></i>
                Dosyayı Görüntüle
            </a>
            @endif
            <button type="button" class="btn btn-primary" wire:click="save">
                <i class="fas fa-save me-2"></i>
                Kaydet
            </button>
        </div>
    </div>
</div>