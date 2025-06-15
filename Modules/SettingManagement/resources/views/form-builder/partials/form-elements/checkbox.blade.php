@php
    // Element dizisinin var olduğunu kontrol edelim
    if (!isset($element) || !is_array($element)) {
        $element = [];
    }
    
    // Temel alan özelliklerini al
    $fieldName = isset($element['name']) ? $element['name'] : (isset($element['properties']['name']) ? $element['properties']['name'] : 'checkbox_' . uniqid());
    $fieldLabel = isset($element['label']) ? $element['label'] : (isset($element['properties']['label']) ? $element['properties']['label'] : 'Onay Kutusu');
    $isRequired = isset($element['required']) ? $element['required'] : (isset($element['properties']['required']) && $element['properties']['required']);
    $checkboxLabel = isset($element['checkbox_label']) ? $element['checkbox_label'] : (isset($element['properties']['checkbox_label']) ? $element['properties']['checkbox_label'] : $fieldLabel);
    $helpText = isset($element['help_text']) ? $element['help_text'] : (isset($element['properties']['help_text']) ? $element['properties']['help_text'] : '');
    
    // Diğer özellikleri al
    $width = isset($element['width']) ? $element['width'] : (isset($element['properties']['width']) ? $element['properties']['width'] : 12);
    $defaultValue = isset($element['default']) ? $element['default'] : (isset($element['properties']['default_value']) ? $element['properties']['default_value'] : false);
    
    // Boolean değeri düzelt
    if (is_string($defaultValue)) {
        $defaultValue = ($defaultValue === 'true' || $defaultValue === '1');
    }
    
    // values ve originalValues kontrolü
    if (!isset($values) || !is_array($values)) {
        $values = [];
    }
    
    if (!isset($originalValues) || !is_array($originalValues)) {
        $originalValues = [];
    }
    
    // values için varsayılan değeri ayarla
    if (!isset($values[$fieldName])) {
        $values[$fieldName] = $defaultValue;
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
        <div class="form-check">
            <input class="form-check-input" 
                type="checkbox" 
                id="{{ $fieldName }}" 
                wire:model="values.{{ $fieldName }}"
                value="1"
                @if($isRequired) required @endif
                @if($defaultValue) checked @endif>
            <label class="form-check-label" for="{{ $fieldName }}">
                {{ $checkboxLabel }}
            </label>
            @error('values.' . $fieldName)
                <div class="invalid-feedback d-block">{{ $message }}</div>
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
                    <i class="ti ti-rotate-clockwise me-1"></i> Varsayılana Döndür
                </button>
            </div>
        @endif
    </div>
</div>