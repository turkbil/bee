@php
    // Element dizisinin var olduğunu kontrol edelim
    if (!isset($element) || !is_array($element)) {
        $element = [];
    }
    
    // Name değerini doğrudan al, properties içinde olmayabilir
    $elementName = isset($element['name']) ? $element['name'] : 'switch_' . uniqid();
    $elementLabel = isset($element['label']) ? $element['label'] : 'Anahtar';
    
    // Diğer özellikleri al
    $width = isset($element['width']) ? $element['width'] : 12;
    $isRequired = isset($element['required']) && $element['required'] === true;
    
    // Default değeri boolean olarak işle
    $defaultValue = false;
    if (isset($element['default'])) {
        if (is_string($element['default'])) {
            $defaultValue = ($element['default'] === 'true' || $element['default'] === '1');
        } elseif (is_bool($element['default'])) {
            $defaultValue = $element['default'];
        } elseif (is_numeric($element['default'])) {
            $defaultValue = (bool)$element['default'];
        }
    }
    
    // Diğer özellikler
    $activeLabel = isset($element['active_label']) ? $element['active_label'] : 'Aktif';
    $inactiveLabel = isset($element['inactive_label']) ? $element['inactive_label'] : 'Pasif';
    $helpText = isset($element['help_text']) ? $element['help_text'] : null;
    
    // formData ve originalData kontrolü
    if (!isset($formData) || !is_array($formData)) {
        $formData = [];
    }
    
    if (!isset($originalData) || !is_array($originalData)) {
        $originalData = [];
    }
    
    // formData için varsayılan değeri ayarla
    if (!isset($formData[$elementName])) {
        $formData[$elementName] = $defaultValue;
    }
@endphp

<div class="col-{{ $width }}" wire:key="element-{{ $elementName }}">
    <div class="card mb-3 w-100">
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">
                <h3 class="card-title d-flex align-items-center">
                    <i class="fa-solid fa-toggle-on me-2 text-primary"></i>
                    {{ $elementLabel }}
                </h3>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group w-100">
                <div class="mb-3">
                    <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                        <input type="checkbox" 
                            id="switch-{{ $elementName }}" 
                            name="{{ $elementName }}" 
                            wire:model="formData.{{ $elementName }}"
                            value="1"
                            @if($isRequired) required @endif
                            @if($defaultValue) checked @endif
                        >
                        <div class="state p-success p-on ms-2">
                            <label>{{ $activeLabel }}</label>
                        </div>
                        <div class="state p-danger p-off ms-2">
                            <label>{{ $inactiveLabel }}</label>
                        </div>
                    </div>
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