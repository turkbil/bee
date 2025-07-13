@php
    // Element dizisinin var olduğunu kontrol edelim
    if (!isset($element) || !is_array($element)) {
        $element = [];
    }
    
    // Name değerini doğrudan al
    $elementName = isset($element['name']) ? $element['name'] : (isset($element['properties']['name']) ? $element['properties']['name'] : 'switch_' . uniqid());
    $elementLabel = isset($element['label']) ? $element['label'] : (isset($element['properties']['label']) ? $element['properties']['label'] : 'Anahtar');
    
    // Diğer özellikleri al
    $width = isset($element['width']) ? $element['width'] : (isset($element['properties']['width']) ? $element['properties']['width'] : 12);
    $isRequired = isset($element['required']) ? $element['required'] : (isset($element['properties']['required']) && $element['properties']['required'] === true);
    
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
    } elseif (isset($element['properties']['default_value'])) {
        if (is_string($element['properties']['default_value'])) {
            $defaultValue = ($element['properties']['default_value'] === 'true' || $element['properties']['default_value'] === '1');
        } elseif (is_bool($element['properties']['default_value'])) {
            $defaultValue = $element['properties']['default_value'];
        } elseif (is_numeric($element['properties']['default_value'])) {
            $defaultValue = (bool)$element['properties']['default_value'];
        }
    }
    
    // Diğer özellikler
    $activeLabel = isset($element['active_label']) ? $element['active_label'] : (isset($element['properties']['active_label']) ? $element['properties']['active_label'] : 'Aktif');
    $inactiveLabel = isset($element['inactive_label']) ? $element['inactive_label'] : (isset($element['properties']['inactive_label']) ? $element['properties']['inactive_label'] : 'Pasif');
    $helpText = isset($element['help_text']) ? $element['help_text'] : (isset($element['properties']['help_text']) ? $element['properties']['help_text'] : null);
    
    // values ve originalValues kontrolü
    if (!isset($values) || !is_array($values)) {
        $values = [];
    }
    
    if (!isset($originalValues) || !is_array($originalValues)) {
        $originalValues = [];
    }
    
    // values için varsayılan değeri ayarla
    if (!isset($values[$elementName])) {
        $values[$elementName] = $defaultValue;
    }
@endphp

<div class="col-{{ $width }}" wire:key="element-{{ $elementName }}">
    <div class="mb-3">
        <label class="form-label">
            {{ $elementLabel }}
            @if($isRequired) 
                <span class="text-danger">*</span> 
            @endif
        </label>
        <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
            <input type="checkbox" 
                id="switch-{{ $elementName }}" 
                name="{{ $elementName }}" 
                wire:model.live="values.{{ $elementName }}"
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
        
        @error('values.' . $elementName)
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
        
        @if($helpText)
            <div class="form-text mt-2 ms-2">
                <i class="fas fa-info-circle me-1"></i>{{ $helpText }}
            </div>
        @endif
        
        @if(isset($originalValues[$elementName]) && isset($values[$elementName]) && $originalValues[$elementName] != $values[$elementName])
            <div class="mt-2 text-end">
                <button type="button" class="btn btn-sm btn-outline-warning" wire:click="resetToDefault('{{ $elementName }}')">
                    <i class="ti ti-rotate-clockwise me-1"></i> Varsayılana Döndür
                </button>
            </div>
        @endif
    </div>
</div>