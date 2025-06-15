@php
    // Element dizisinin var olduğunu kontrol edelim
    if (!isset($element) || !is_array($element)) {
        $element = [];
    }
    
    // Temel alan özelliklerini al
    $fieldName = $element['name'] ?? 'number_' . uniqid();
    $fieldLabel = $element['label'] ?? 'Sayı Alanı';
    $isRequired = isset($element['required']) && $element['required'];
    $placeholder = $element['placeholder'] ?? '';
    $helpText = $element['help_text'] ?? '';
    
    // Diğer özellikleri al
    $width = isset($element['width']) ? $element['width'] : 12;
    $defaultValue = isset($element['default']) ? $element['default'] : '';
    $min = isset($element['min']) ? $element['min'] : null;
    $max = isset($element['max']) ? $element['max'] : null;
    $step = isset($element['step']) ? $element['step'] : null;
    
    // formData ve originalData kontrolü
    if (!isset($formData) || !is_array($formData)) {
        $formData = [];
    }
    
    if (!isset($originalData) || !is_array($originalData)) {
        $originalData = [];
    }
    
    // Mevcut değeri belirle
    if(isset($formData[$fieldName])) {
        $fieldValue = $formData[$fieldName];
    } elseif(isset($settings) && is_object($settings)) {
        $cleanFieldName = str_replace('widget.', '', $fieldName);
        $fieldValue = $settings[$cleanFieldName] ?? $defaultValue;
    } else {
        $fieldValue = $defaultValue;
    }
    
    // formData için varsayılan değeri ayarla
    if (!isset($formData[$fieldName])) {
        $formData[$fieldName] = $fieldValue;
    }
@endphp

<div class="col-{{ $width }}">
    <div class="mb-3">
        
        @if(isset($originalData[$fieldName]) && isset($formData[$fieldName]) && $originalData[$fieldName] != $formData[$fieldName])
            <div class="mb-2">
                <span class="badge bg-yellow cursor-pointer" wire:click="resetToDefault('{{ $fieldName }}')">
                    <i class="ti ti-rotate-clockwise me-1"></i> Varsayılana Döndür
                </span>
            </div>
        @endif
        
        <div class="form-floating">
            @if(isset($formData))
                <input type="number" 
                    id="{{ $fieldName }}"
                    wire:model="formData.{{ $fieldName }}" 
                    class="form-control @error('formData.' . $fieldName) is-invalid @enderror" 
                    placeholder="{{ $placeholder }}"
                    @if($min !== null) min="{{ $min }}" @endif
                    @if($max !== null) max="{{ $max }}" @endif
                    @if($step !== null) step="{{ $step }}" @endif
                    @if($isRequired) required @endif>
                @error('formData.' . $fieldName)
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            @else
                <input type="number" 
                    id="{{ $fieldName }}"
                    wire:model="settings.{{ str_replace('widget.', '', $fieldName) }}" 
                    class="form-control @error('settings.' . str_replace('widget.', '', $fieldName)) is-invalid @enderror" 
                    placeholder="{{ $placeholder }}"
                    @if($min !== null) min="{{ $min }}" @endif
                    @if($max !== null) max="{{ $max }}" @endif
                    @if($step !== null) step="{{ $step }}" @endif
                    @if($isRequired) required @endif>
                @error('settings.' . str_replace('widget.', '', $fieldName))
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            @endif
            
            <label for="{{ $fieldName }}">
                {{ $fieldLabel }}
                @if($isRequired) 
                    <span class="text-danger">*</span> 
                @endif
            </label>
        </div>
        
        @if($helpText)
            <div class="form-text mt-2 ms-2">
                <i class="fas fa-info-circle me-1"></i>{{ $helpText }}
            </div>
        @endif
    </div>
</div>