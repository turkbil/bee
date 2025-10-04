@php
    // Element ve properties dizilerinin var olduğunu kontrol edelim
    if (!isset($element) || !is_array($element)) {
        $element = [];
    }
    
    // Name değerini doğrudan al, properties içinde olmayabilir
    $elementName = isset($element['name']) ? $element['name'] : 'radio_' . uniqid();
    $elementLabel = isset($element['label']) ? $element['label'] : 'Seçim Düğmeleri';
    
    // Diğer özellikleri al
    $width = isset($element['width']) ? $element['width'] : 12;
    $isRequired = isset($element['required']) && $element['required'] === true;
    $defaultValue = isset($element['default']) ? $element['default'] : null;
    $helpText = isset($element['help_text']) ? $element['help_text'] : null;
    
    // Yeni options formatı (JSON'daki gibi)
    $optionsArray = [];
    if (isset($element['options']) && is_array($element['options'])) {
        $optionsArray = $element['options'];
    } elseif (isset($element['options']) && is_object($element['options'])) {
        // { "option1": "Seçenek 1", "option2": "Seçenek 2" } formatını dönüştür
        foreach ($element['options'] as $value => $label) {
            $optionsArray[] = ['value' => $value, 'label' => $label];
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
    <div class="mb-3">
        <label class="form-label">
            {{ $elementLabel }}
            @if($isRequired) 
                <span class="text-danger">*</span> 
            @endif
        </label>
        
        <div class="form-selectgroup @error('formData.' . $elementName) is-invalid @enderror">
            @if(is_array($optionsArray))
                @foreach($optionsArray as $value => $label)
                    @php
                        // Değerin bir dizi veya obje olma durumuna karşı kontrol
                        $optionValue = is_array($label) ? $value : $value;
                        $optionLabel = is_array($label) ? $label : $label;
                        if (is_object($label)) {
                            $optionValue = $value;
                            $optionLabel = (string)$label;
                        }
                    @endphp
                    <label class="form-selectgroup-item">
                        <input 
                            type="radio" 
                            name="radio_{{ $elementName }}" 
                            value="{{ $optionValue }}" 
                            class="form-selectgroup-input" 
                            wire:model="formData.{{ $elementName }}"
                            @if($isRequired) required @endif
                            @if($defaultValue === $optionValue) checked @endif
                        >
                        <span class="form-selectgroup-label">{{ $optionLabel }}</span>
                    </label>
                @endforeach
            @endif
        </div>
        
        @error('formData.' . $elementName)
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
        
        @if($helpText)
            <div class="form-text mt-2 ms-2">
                <i class="fas fa-info-circle me-1"></i>{{ $helpText }}
            </div>
        @endif
        
        @if(isset($originalData[$elementName]) && isset($formData[$elementName]) && $originalData[$elementName] != $formData[$elementName])
            <div class="mt-2 text-end">
                <button type="button" class="btn btn-sm btn-outline-warning" wire:click="resetToDefault('{{ $elementName }}')">
                    <i class="fas fa-redo me-1"></i> Varsayılana Döndür
                </button>
            </div>
        @endif
    </div>
</div>