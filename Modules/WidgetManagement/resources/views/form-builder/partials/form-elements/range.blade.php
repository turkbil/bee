@php
    // Element ve properties dizilerinin var olduğunu kontrol edelim
    if (!isset($element) || !is_array($element)) {
        $element = ['properties' => []];
    }
    
    if (!isset($element['properties']) || !is_array($element['properties'])) {
        $element['properties'] = [];
    }
    
    // Güvenli bir şekilde özellikleri alabiliriz
    $elementName = isset($element['properties']['name']) ? $element['properties']['name'] : 'range_' . uniqid();
    $elementLabel = isset($element['properties']['label']) ? $element['properties']['label'] : 'Değer Aralığı';
    $min = isset($element['properties']['min']) ? $element['properties']['min'] : 0;
    $max = isset($element['properties']['max']) ? $element['properties']['max'] : 100;
    $step = isset($element['properties']['step']) ? $element['properties']['step'] : 1;
    $helpText = isset($element['properties']['help_text']) ? $element['properties']['help_text'] : null;
    
    $settingId = null;
    $settingKey = null;
    
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
                $settingId = $settingName;
            }
        } else {
            $settingId = $settingName;
        }
    } else {
        $settingId = $elementName;
    }
    
    // Values kontrolü
    if (!isset($values) || !is_array($values)) {
        $values = [];
    }
    
    $currentValue = isset($values[$settingId]) ? $values[$settingId] : $min;
@endphp

<div class="col-12" wire:key="setting-{{ $settingId }}">
    <div class="card mb-3 w-100">
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">
                <h3 class="card-title d-flex align-items-center">
                    <i class="fas fa-sliders-h me-2 text-primary"></i>
                    {{ $elementLabel }}
                </h3>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group w-100">
                <div class="mb-3">
                    <div class="form-range mb-2 text-primary" id="range-{{ $settingId }}" wire:ignore>
                        <input 
                            type="range" 
                            wire:model="values.{{ $settingId }}" 
                            class="form-range" 
                            min="{{ $min }}" 
                            max="{{ $max }}" 
                            step="{{ $step }}" 
                            onInput="document.getElementById('rangeValue-{{ $settingId }}').innerHTML = this.value"
                        >
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="small text-muted">{{ $min }}</span>
                        <span class="badge bg-primary" id="rangeValue-{{ $settingId }}">{{ $currentValue }}</span>
                        <span class="small text-muted">{{ $max }}</span>
                    </div>
                </div>
                
                @if(!empty($helpText))
                    <div class="form-text text-muted mt-2">
                        <i class="fas fa-info-circle me-1"></i>
                        {{ $helpText }}
                    </div>
                @endif
                
                @if(isset($originalValues) && isset($originalValues[$settingId]) && isset($values[$settingId]) && $originalValues[$settingId] != $values[$settingId])
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