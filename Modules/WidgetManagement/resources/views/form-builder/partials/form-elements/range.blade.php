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
                    <div class="form-range mb-2 text-primary" id="range-{{ $elementName }}" wire:ignore>
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
                    <div class="d-flex justify-content-between">
                        <span class="small text-muted">{{ $minValue }}</span>
                        <span class="badge bg-primary" id="rangeValue-{{ $elementName }}">{{ $fieldValue }}</span>
                        <span class="small text-muted">{{ $maxValue }}</span>
                    </div>
                </div>
                
                @if(!empty($helpText))
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