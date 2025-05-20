@php
    $settingId = null;
    $settingKey = null;
    
    if(isset($element['properties']['setting_id'])) {
        $settingId = $element['properties']['setting_id'];
    } elseif(isset($element['properties']['name'])) {
        $settingName = $element['properties']['name'];
        
        // Ayarı adından bul
        $setting = $settings->firstWhere('key', $settingName);
        if($setting) {
            $settingId = $setting->id;
            $settingKey = $setting->key;
        }
    }
@endphp

@if($settingId)
    <div class="col-12" wire:key="setting-{{ $settingId }}">
        <div class="card mb-3 w-100">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <h3 class="card-title d-flex align-items-center">
                        <i class="fas fa-toggle-on me-2 text-primary"></i>
                        {{ $element['properties']['label'] ?? 'Anahtar' }}
                    </h3>
                </div>
            </div>
            <div class="card-body">
                <div class="form-group w-100">
                    <div class="form-check form-switch form-switch-lg">
                        <input type="checkbox" 
                            id="switch-{{ $settingId }}" 
                            class="form-check-input" 
                            wire:model="values.{{ $settingId }}"
                            value="1"
                        >
                        <label class="form-check-label" for="switch-{{ $settingId }}">
                            <span x-data="{ isChecked: {{ isset($values[$settingId]) && $values[$settingId] == 1 ? 'true' : 'false' }} }" x-init="$watch('values.{{ $settingId }}', value => isChecked = value == 1)">
                                <span x-show="isChecked" class="text-success">Açık</span>
                                <span x-show="!isChecked" class="text-muted">Kapalı</span>
                            </span>
                        </label>
                    </div>
                    
                    @if(isset($element['properties']['help_text']) && !empty($element['properties']['help_text']))
                        <div class="form-text text-muted mt-2">
                            <i class="fas fa-info-circle me-1"></i>
                            {{ $element['properties']['help_text'] }}
                        </div>
                    @endif
                    
                    @if(isset($originalValues[$settingId]) && $originalValues[$settingId] != $values[$settingId])
                        <div class="mt-2 text-end">
                            <span class="badge bg-yellow cursor-pointer" wire:click="resetToDefault({{ $settingId }})">
                                <i class="fas fa-undo me-1"></i> Varsayılana Döndür
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@else
    <div class="col-12">
        <div class="alert alert-danger mb-3 w-100">
            <i class="fas fa-exclamation-circle me-2"></i>
            Bu anahtar alanı için ayar bulunamadı: {{ $element['properties']['name'] ?? 'Bilinmeyen' }}
        </div>
    </div>
@endif