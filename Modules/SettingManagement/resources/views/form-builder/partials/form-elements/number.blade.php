@php
    // Element dizisinin var olduğunu kontrol edelim
    if (!isset($element) || !is_array($element)) {
        $element = [];
    }
    
    // Temel alan özelliklerini al
    $fieldName = isset($element['name']) ? $element['name'] : (isset($element['properties']['name']) ? $element['properties']['name'] : 'number_' . uniqid());
    $fieldLabel = isset($element['label']) ? $element['label'] : (isset($element['properties']['label']) ? $element['properties']['label'] : 'Sayı');
    $isRequired = isset($element['required']) ? $element['required'] : (isset($element['properties']['required']) && $element['properties']['required']);
    $placeholder = isset($element['placeholder']) ? $element['placeholder'] : (isset($element['properties']['placeholder']) ? $element['properties']['placeholder'] : '');
    $helpText = isset($element['help_text']) ? $element['help_text'] : (isset($element['properties']['help_text']) ? $element['properties']['help_text'] : '');
    
    // Sayı alanı özel özellikleri
    $min = isset($element['min']) ? $element['min'] : (isset($element['properties']['min']) ? $element['properties']['min'] : null);
    $max = isset($element['max']) ? $element['max'] : (isset($element['properties']['max']) ? $element['properties']['max'] : null);
    $step = isset($element['step']) ? $element['step'] : (isset($element['properties']['step']) ? $element['properties']['step'] : null);
    
    // Diğer özellikleri al
    $width = isset($element['width']) ? $element['width'] : (isset($element['properties']['width']) ? $element['properties']['width'] : 12);
    $defaultValue = isset($element['default']) ? $element['default'] : (isset($element['properties']['default_value']) ? $element['properties']['default_value'] : '');
    
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
        <div class="form-floating">
            <input type="number" 
                id="{{ $fieldName }}"
                wire:model.defer="values.{{ $fieldName }}" 
                class="form-control @error('values.' . $fieldName) is-invalid @enderror" 
                placeholder="{{ $placeholder ?: $fieldLabel }}"
                @if($min !== null) min="{{ $min }}" @endif
                @if($max !== null) max="{{ $max }}" @endif
                @if($step !== null) step="{{ $step }}" @endif
                @if($isRequired) required @endif>
            <label for="{{ $fieldName }}">
                {{ $fieldLabel }}
                @if($isRequired) 
                    <span class="text-danger">*</span> 
                @endif
            </label>
            @error('values.' . $fieldName)
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
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