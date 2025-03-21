@include('settingmanagement::helper')

<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ $setting->label }}</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg-8">
                <!-- Değer Düzenleme -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Değer</h3>
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
                            @if($value && !$useDefault)
                            <div class="mb-3">
                                @if(Str::endsWith(strtolower($value), ['.jpg', '.jpeg', '.png', '.gif']))
                                <div class="mb-2">
                                    <img src="{{ Storage::url($value) }}" alt="Dosya önizleme" class="img-fluid border rounded mb-2" style="max-height: 150px">
                                    <div class="text-muted small">{{ basename($value) }}</div>
                                </div>
                                @else
                                <div class="border rounded p-2 mb-2">
                                    <i class="fas fa-file me-2"></i> {{ basename($value) }}
                                </div>
                                @endif
                            </div>
                            @endif
                            <input type="file" wire:model="tempFile" class="form-control" {{ $useDefault ? 'disabled' : '' }}>
                            
                            @elseif($setting->type === 'checkbox')
                            <label class="form-check form-switch">
                                <input type="checkbox" wire:model="value" class="form-check-input" {{ $useDefault ? 'disabled' : '' }}>
                                <span class="form-check-label">{{ $value ? 'Evet' : 'Hayır' }}</span>
                            </label>
                            
                            @elseif($setting->type === 'color')
                            <div class="row g-2 align-items-center">
                                <div class="col-auto">
                                    <input type="color" wire:model="value" class="form-control form-control-color" {{ $useDefault ? 'disabled' : '' }}>
                                </div>
                                <div class="col-auto">{{ $value }}</div>
                            </div>
                            
                            @else
                            <input type="{{ $setting->type }}" wire:model="value" class="form-control" {{ $useDefault ? 'disabled' : '' }}>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <!-- Ayar Detayları -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Ayar Bilgileri</h3>
                    </div>
                    <div class="card-body">
                        <div class="datagrid">
                            <div class="datagrid-item">
                                <div class="datagrid-title">Anahtar</div>
                                <div class="datagrid-content"><code>{{ $setting->key }}</code></div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Tip</div>
                                <div class="datagrid-content"><span class="badge bg-blue-lt">{{ $setting->type }}</span></div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Grup</div>
                                <div class="datagrid-content">{{ $setting->group->name }}</div>
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
                            <div class="datagrid-item">
                                <div class="datagrid-title">Varsayılan Değer</div>
                                <div class="datagrid-content d-flex align-items-center">
                                    <span class="text-truncate me-2" style="max-width: 120px;" title="{{ $setting->default_value }}">
                                        @if(empty($setting->default_value))
                                            <span class="text-muted">Boş</span>
                                        @elseif($setting->type === 'file')
                                            <i class="fas fa-file me-1"></i> Dosya
                                        @elseif($setting->type === 'checkbox')
                                            {{ $setting->default_value ? 'Evet' : 'Hayır' }}
                                        @else
                                            {{ $setting->default_value }}
                                        @endif
                                    </span>
                                    <button type="button" class="btn btn-sm {{ $useDefault ? 'btn-success' : 'btn-outline-secondary' }}" wire:click="toggleDefault">
                                        <i class="fas {{ $useDefault ? 'fa-check' : 'fa-undo' }}"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <a href="{{ route('admin.settingmanagement.tenant.settings') }}" class="btn">
            <i class="fas fa-arrow-left me-2"></i> Geri
        </a>
        <div>
            @if($setting->type === 'file' && $value && !$useDefault)
            <a href="{{ Storage::url($value) }}" target="_blank" class="btn btn-outline-primary me-2">
                <i class="fas fa-eye me-2"></i> Görüntüle
            </a>
            @endif
            <button type="button" class="btn btn-primary" wire:click="save">
                <span wire:loading.remove wire:target="save">
                    <i class="fas fa-save me-2"></i> Kaydet
                </span>
                <span wire:loading wire:target="save">
                    <i class="fas fa-spinner fa-spin me-2"></i> Kaydediliyor...
                </span>
            </button>
        </div>
    </div>
</div>