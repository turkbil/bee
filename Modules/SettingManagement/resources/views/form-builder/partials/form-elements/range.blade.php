@php
    // Element dizisinin var olduğunu kontrol edelim
    if (!isset($element) || !is_array($element)) {
        $element = [];
    }
    
    // Temel alan özelliklerini al
    $fieldName = isset($element['name']) ? $element['name'] : (isset($element['properties']['name']) ? $element['properties']['name'] : 'range_' . uniqid());
    $fieldLabel = isset($element['label']) ? $element['label'] : (isset($element['properties']['label']) ? $element['properties']['label'] : 'Değer Aralığı');
    $isRequired = isset($element['required']) ? $element['required'] : (isset($element['properties']['required']) && $element['properties']['required']);
    $helpText = isset($element['help_text']) ? $element['help_text'] : (isset($element['properties']['help_text']) ? $element['properties']['help_text'] : '');
    
    // Range özel özellikleri
    $min = isset($element['min']) ? $element['min'] : (isset($element['properties']['min']) ? $element['properties']['min'] : 0);
    $max = isset($element['max']) ? $element['max'] : (isset($element['properties']['max']) ? $element['properties']['max'] : 100);
    $step = isset($element['step']) ? $element['step'] : (isset($element['properties']['step']) ? $element['properties']['step'] : 1);
    
    // Diğer özellikleri al
    $width = isset($element['width']) ? $element['width'] : (isset($element['properties']['width']) ? $element['properties']['width'] : 12);
    $defaultValue = isset($element['default']) ? $element['default'] : (isset($element['properties']['default_value']) ? $element['properties']['default_value'] : $min);
    
    // values ve originalValues kontrolü
    if (!isset($values) || !is_array($values)) {
        $values = [];
    }
    
    if (!isset($originalValues) || !is_array($originalValues)) {
        $originalValues = [];
    }
    
    // Mevcut değeri belirle
    if(isset($values[$fieldName])) {
        $fieldValue = $values[$fieldName];
    } elseif(isset($settings) && is_object($settings)) {
        $cleanFieldName = str_replace('setting.', '', $fieldName);
        $fieldValue = $settings[$cleanFieldName] ?? $defaultValue;
    } else {
        $fieldValue = $defaultValue;
    }
    
    // values için varsayılan değeri ayarla
    if (!isset($values[$fieldName])) {
        $values[$fieldName] = $fieldValue;
    }
@endphp

<div class="col-{{ $width }}">
    <div class="mb-3">
        <label class="form-label">
            {{ $fieldLabel }}
            @if($isRequired) 
                <span class="text-danger">*</span> 
            @endif
        </label>
        
        <div class="mb-2">
            <input type="range" 
                id="{{ $fieldName }}"
                wire:model.live="values.{{ $fieldName }}" 
                class="form-range" 
                min="{{ $min }}"
                max="{{ $max }}"
                step="{{ $step }}"
                @if($isRequired) required @endif
                oninput="document.getElementById('rangeValue-{{ $fieldName }}').innerHTML = this.value">
        </div>
        
        <div class="d-flex justify-content-between align-items-center">
            <span class="small text-muted">{{ $min }}</span>
            <span class="badge bg-primary" id="rangeValue-{{ $fieldName }}">{{ $values[$fieldName] ?? $defaultValue }}</span>
            <span class="small text-muted">{{ $max }}</span>
        </div>
        
        @if($helpText)
            <div class="form-text mt-2 ms-2">
                <i class="fas fa-info-circle me-1"></i>{{ $helpText }}
            </div>
        @endif
        
        @if(isset($originalValues[$fieldName]) && isset($values[$fieldName]) && $originalValues[$fieldName] != $values[$fieldName])
            <div class="mt-2 text-end">
                <button type="button" class="btn btn-sm btn-outline-warning" wire:click="resetToDefault('{{ $fieldName }}')">
                    <i class="fas fa-redo me-1"></i> Varsayılana Döndür
                </button>
            </div>
        @endif
    </div>
</div>