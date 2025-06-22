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
                        <i class="fas fa-hashtag me-2"></i>
                        {{ $element['properties']['label'] ?? t('settingmanagement::general.number_field_default') }}
                    </h3>
                </div>
            </div>
            <div class="card-body">
                <div class="form-group w-100">
                    <div class="input-group mb-2">
                        <span class="input-group-text">
                            <i class="fas fa-hashtag"></i>
                        </span>
                        <input 
                            type="number" 
                            wire:model="values.{{ $settingId }}" 
                            class="form-control" 
                            placeholder="{{ $element['properties']['placeholder'] ?? t('settingmanagement::general.number_field_placeholder') }}"
                            @if(isset($element['properties']['min'])) min="{{ $element['properties']['min'] }}" @endif
                            @if(isset($element['properties']['max'])) max="{{ $element['properties']['max'] }}" @endif
                            @if(isset($element['properties']['step'])) step="{{ $element['properties']['step'] }}" @endif
                        >
                        @if(isset($element['properties']['step']) && $element['properties']['step'] > 0)
                            <button class="btn btn-outline-secondary" type="button" wire:click="$set('values.{{ $settingId }}', {{ is_numeric($values[$settingId] ?? 0) ? (($values[$settingId] ?? 0) - ($element['properties']['step'] ?? 1)) : 0 }})">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button class="btn btn-outline-secondary" type="button" wire:click="$set('values.{{ $settingId }}', {{ is_numeric($values[$settingId] ?? 0) ? (($values[$settingId] ?? 0) + ($element['properties']['step'] ?? 1)) : ($element['properties']['step'] ?? 1) }})">
                                <i class="fas fa-plus"></i>
                            </button>
                        @endif
                    </div>
                    @if(isset($element['properties']['min']) || isset($element['properties']['max']) || isset($element['properties']['step']))
                        <div class="d-flex justify-content-between px-2 small text-muted mb-2">
                            @if(isset($element['properties']['min']))
                                <span>{{ t('settingmanagement::general.number_min') }}: {{ $element['properties']['min'] }}</span>
                            @endif
                            @if(isset($element['properties']['max']))
                                <span>{{ t('settingmanagement::general.number_max') }}: {{ $element['properties']['max'] }}</span>
                            @endif
                            @if(isset($element['properties']['step']))
                                <span>{{ t('settingmanagement::general.number_step') }}: {{ $element['properties']['step'] }}</span>
                            @endif
                        </div>
                    @endif
                    
                    @if(isset($element['properties']['help_text']) && !empty($element['properties']['help_text']))
                        <div class="form-text text-muted mt-2">
                            <i class="fas fa-info-circle me-1"></i>
                            {{ $element['properties']['help_text'] }}
                        </div>
                    @endif
                    
                    @if(isset($originalValues[$settingId]) && $originalValues[$settingId] != $values[$settingId])
                        <div class="mt-2 text-end">
                            <span class="badge bg-yellow cursor-pointer" wire:click="resetToDefault({{ $settingId }})">
                                <i class="fas fa-undo me-1"></i> {{ t('settingmanagement::general.reset_to_default_button') }}
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
            {{ t('settingmanagement::general.number_setting_not_found') }} {{ $element['properties']['name'] ?? 'Bilinmeyen' }}
        </div>
    </div>
@endif