@php
    // Element dizisinin var olduğunu kontrol edelim
    if (!isset($element) || !is_array($element)) {
        $element = [];
    }
    
    // Temel alan özelliklerini al
    $elementName = isset($element['name']) ? $element['name'] : 'range_' . uniqid();
    $elementLabel = isset($element['label']) ? $element['label'] : 'Değer Aralığı';
    $isRequired = isset($element['required']) && $element['required'];
    $helpText = isset($element['help_text']) ? $element['help_text'] : null;
    
    // Diğer özellikleri al
    $width = isset($element['width']) ? $element['width'] : 12;
    $defaultValue = isset($element['default']) ? (int)$element['default'] : 50;
    $minValue = isset($element['min']) ? (int)$element['min'] : 0;
    $maxValue = isset($element['max']) ? (int)$element['max'] : 100;
    $step = isset($element['step']) ? (float)$element['step'] : 1;
    
    // formData ve originalData kontrolü
    if (!isset($formData) || !is_array($formData)) {
        $formData = [];
    }
    
    if (!isset($originalData) || !is_array($originalData)) {
        $originalData = [];
    }
    
    // Mevcut değeri belirle
    if(isset($formData[$elementName])) {
        $fieldValue = $formData[$elementName];
    } elseif(isset($settings) && is_object($settings)) {
        $cleanFieldName = str_replace('widget.', '', $elementName);
        $fieldValue = $settings[$cleanFieldName] ?? $defaultValue;
    } else {
        $fieldValue = $defaultValue;
    }
    
    // formData için varsayılan değeri ayarla
    if (!isset($formData[$elementName])) {
        $formData[$elementName] = $fieldValue;
    }
@endphp

<div class="col-{{ $width }}" wire:key="setting-{{ $elementName }}">
    <div class="mb-3">
        <label class="form-label">
            {{ $elementLabel }}
            @if($isRequired)
                <span class="text-danger">*</span>
            @endif
        </label>
        
        <div class="mb-2">
            <input 
                type="range" 
                wire:model="formData.{{ $elementName }}" 
                class="form-range" 
                min="{{ $minValue }}" 
                max="{{ $maxValue }}" 
                step="{{ $step }}" 
                onInput="document.getElementById('rangeValue-{{ $elementName }}').innerHTML = this.value"
            >
        </div>
        
        <div class="d-flex justify-content-between align-items-center">
            <span class="small text-muted">{{ $minValue }}</span>
            <span class="badge bg-primary" id="rangeValue-{{ $elementName }}">{{ $fieldValue }}</span>
            <span class="small text-muted">{{ $maxValue }}</span>
        </div>
        
        @if(!empty($helpText))
            <div class="form-text mt-2 ms-2">
                <i class="fas fa-info-circle me-1"></i>{{ $helpText }}
            </div>
        @endif
        
        @if(isset($originalData[$elementName]) && isset($formData[$elementName]) && $originalData[$elementName] != $formData[$elementName])
            <div class="mt-2">
                <button type="button" class="btn btn-sm btn-outline-warning" wire:click="resetToDefault('{{ $elementName }}')">
                    <i class="fas fa-sync-alt me-1"></i>
                    Varsayılana Döndür
                </button>
            </div>
        @endif
    </div>
</div>