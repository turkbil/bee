@php
    // Önce element ve properties dizilerinin var olduğunu kontrol edelim
    if (!isset($element) || !is_array($element)) {
        $element = ['properties' => []];
    }
    
    if (!isset($element['properties']) || !is_array($element['properties'])) {
        $element['properties'] = [];
    }
    
    // Şimdi güvenli bir şekilde özellikleri alabiliriz
    $elementName = isset($element['properties']['name']) ? $element['properties']['name'] : 'checkbox_' . uniqid();
    $elementLabel = isset($element['properties']['label']) ? $element['properties']['label'] : 'Onay Kutusu';
    
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
        if (isset($settings) && is_object($settings)) {
            $setting = $settings->firstWhere('key', $settingName);
            if($setting) {
                $settingId = $setting->id;
                $settingKey = $setting->key;
            } else {
                // Ayar yoksa oluştur
                $settingId = $settingName;
            }
        } else {
            $settingId = $settingName;
        }
    }
@endphp

<div class="col-{{ $width }}" wire:key="element-{{ $elementName }}">
    <div class="card mb-3 w-100">
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">
                <h3 class="card-title d-flex align-items-center">
                    <i class="fa-regular fa-comment fa-flip-horizontal me-2 text-primary"></i>
                    {{ $elementLabel }}
                </h3>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group w-100">
                <div class="mb-3">
                    <label class="form-check">
                        <input class="form-check-input" type="checkbox" 
                            id="value-{{ $elementName }}" 
                            name="{{ $elementName }}" 
                            wire:model="formData.{{ $elementName }}"
                            value="1"
                            @if($isRequired) required @endif
                            @if($defaultValue === 'true') checked @endif
                        >
                        <span class="form-check-label">{{ $checkboxLabel ?? $elementLabel ?? 'Onay' }}</span>
                    </label>
                </div>
                
                @if($helpText)
                    <div class="form-text text-muted mt-2">
                        <i class="fas fa-info-circle me-1"></i>
                        {{ $helpText }}
                    </div>
                @endif
                
                @if(isset($originalData) && isset($originalData[$elementName]) && isset($formData) && isset($formData[$elementName]) && $originalData[$elementName] != $formData[$elementName])
                    <div class="mt-2 text-end">
                        <span class="badge bg-yellow cursor-pointer" wire:click="resetToDefault('{{ $elementName }}')">
                            <i class="fas fa-undo me-1"></i> Varsayılana Döndür
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>