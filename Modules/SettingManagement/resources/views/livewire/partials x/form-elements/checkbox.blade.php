@php
    $settingId = null;
    $settingKey = null;
    $width = isset($element['properties']['width']) ? $element['properties']['width'] : 12;
    $isRequired = isset($element['properties']['required']) && $element['properties']['required'] === true;
    $defaultValue = isset($element['properties']['default_value']) ? $element['properties']['default_value'] : null;
    $checkboxLabel = isset($element['properties']['checkbox_label']) ? $element['properties']['checkbox_label'] : null;
    $helpText = isset($element['properties']['help_text']) ? $element['properties']['help_text'] : null;
    
    if(isset($element['properties']['setting_id'])) {
        $settingId = $element['properties']['setting_id'];
    } elseif(isset($element['properties']['name'])) {
        $settingName = $element['properties']['name'];
        
        // Ayarı adından bul
        $setting = $settings->firstWhere('key', $settingName);
        if($setting) {
            $settingId = $setting->id;
            $settingKey = $setting->key;
        } else {
            // Ayar yoksa oluştur
            // Bu kısım gerçek uygulamada ayar oluşturma mantığına göre değişebilir
            $settingId = $settingName;
        }
    }
@endphp

<div class="col-{{ $width }}" wire:key="element-{{ $element['properties']['name'] ?? 'checkbox' }}">
    <div class="card mb-3 w-100">
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">
                <h3 class="card-title d-flex align-items-center">
                    <i class="fa-regular fa-comment fa-flip-horizontal me-2"></i>
                    {{ $element['properties']['label'] ?? __('settingmanagement::general.checkbox_field_default') }}
                </h3>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group w-100">
                <div class="mb-3">
                    <label class="form-check">
                        <input class="form-check-input" type="checkbox" 
                            id="value-{{ $element['properties']['name'] }}" 
                            name="{{ $element['properties']['name'] }}" 
                            wire:model="formData.{{ $element['properties']['name'] }}"
                            value="1"
                            @if($isRequired) required @endif
                            @if($defaultValue === 'true') checked @endif
                        >
                        <span class="form-check-label">{{ $checkboxLabel ?? ($element['properties']['label'] ?? __('settingmanagement::general.checkbox_field_label')) }}</span>
                    </label>
                </div>
                
                @if($helpText)
                    <div class="form-text text-muted mt-2">
                        <i class="fas fa-info-circle me-1"></i>
                        {{ $helpText }}
                    </div>
                @endif
                
                @if(isset($originalData[$element['properties']['name']]) && $originalData[$element['properties']['name']] != $formData[$element['properties']['name']])
                    <div class="mt-2 text-end">
                        <span class="badge bg-yellow cursor-pointer" wire:click="resetToDefault('{{ $element['properties']['name'] }}')">
                            <i class="fas fa-undo me-1"></i> {{ __('settingmanagement::general.reset_to_default_button') }}
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>