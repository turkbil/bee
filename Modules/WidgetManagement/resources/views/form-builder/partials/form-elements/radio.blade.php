@php
    // Element ve properties dizilerinin var olduğunu kontrol edelim
    if (!isset($element) || !is_array($element)) {
        $element = ['properties' => []];
    }
    
    if (!isset($element['properties']) || !is_array($element['properties'])) {
        $element['properties'] = [];
    }
    
    // Güvenli bir şekilde özellikleri alabiliriz
    $elementName = isset($element['properties']['name']) ? $element['properties']['name'] : 'radio_' . uniqid();
    $elementLabel = isset($element['properties']['label']) ? $element['properties']['label'] : 'Seçim Düğmeleri';
    
    $settingId = null;
    $settingKey = null;
    $width = isset($element['properties']['width']) ? $element['properties']['width'] : 12;
    $isRequired = isset($element['properties']['required']) && $element['properties']['required'] === true;
    $defaultValue = isset($element['properties']['default_value']) ? $element['properties']['default_value'] : null;
    $helpText = isset($element['properties']['help_text']) ? $element['properties']['help_text'] : null;
    $options = isset($element['properties']['options']) ? $element['properties']['options'] : [];
    
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
    
    // formData ve originalData kontrolü
    if (!isset($formData) || !is_array($formData)) {
        $formData = [];
    }
    
    if (!isset($originalData) || !is_array($originalData)) {
        $originalData = [];
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
                <div class="form-selectgroup">
                    @foreach($options as $option)
                        <label class="form-selectgroup-item">
                            <input 
                                type="radio" 
                                name="radio_{{ $elementName }}" 
                                value="{{ isset($option['value']) ? $option['value'] : '' }}" 
                                class="form-selectgroup-input" 
                                wire:model="formData.{{ $elementName }}"
                                @if($isRequired) required @endif
                                @if($defaultValue === (isset($option['value']) ? $option['value'] : '') || (isset($option['is_default']) && $option['is_default'])) checked @endif
                            >
                            <span class="form-selectgroup-label">{{ isset($option['label']) ? $option['label'] : 'Seçenek' }}</span>
                        </label>
                    @endforeach
                </div>
                
                @if($helpText)
                    <div class="form-text text-muted mt-2">
                        <i class="fas fa-info-circle me-1"></i>
                        {{ $helpText }}
                    </div>
                @endif
                
                @if(isset($originalData[$elementName]) && isset($formData[$elementName]) && $originalData[$elementName] != $formData[$elementName])
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