@php
    // Element dizisinin var olduğunu kontrol edelim
    if (!isset($element) || !is_array($element)) {
        $element = [];
    }
    
    // Temel alan özelliklerini al
    $fieldName = $element['name'] ?? 'date_' . uniqid();
    $fieldLabel = $element['label'] ?? 'Tarih Alanı';
    $isRequired = isset($element['required']) && $element['required'];
    $helpText = $element['help_text'] ?? '';
    
    // Diğer özellikleri al
    $width = isset($element['properties']['width']) ? $element['properties']['width'] : 12;
    $defaultValue = isset($element['properties']['default_value']) ? $element['properties']['default_value'] : '';
    
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
        
        <div class="form-floating">
            @if(isset($formData))
                <input type="date" 
                    id="{{ $fieldName }}"
                    wire:model="formData.{{ $fieldName }}" 
                    class="form-control @error('formData.' . $fieldName) is-invalid @enderror"
                    placeholder="{{ $fieldLabel }}"
                    @if($isRequired) required @endif>
                <label for="{{ $fieldName }}">
                    <i class="ti ti-calendar me-1"></i>
                    {{ $fieldLabel }}
                    @if($isRequired) 
                        <span class="text-danger">*</span> 
                    @endif
                </label>
                @error('formData.' . $fieldName)
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            @else
                <input type="date" 
                    id="{{ $fieldName }}"
                    wire:model="settings.{{ str_replace('widget.', '', $fieldName) }}" 
                    class="form-control @error('settings.' . str_replace('widget.', '', $fieldName)) is-invalid @enderror"
                    placeholder="{{ $fieldLabel }}"
                    @if($isRequired) required @endif>
                <label for="{{ $fieldName }}">
                    <i class="ti ti-calendar me-1"></i>
                    {{ $fieldLabel }}
                    @if($isRequired) 
                        <span class="text-danger">*</span> 
                    @endif
                </label>
                @error('settings.' . str_replace('widget.', '', $fieldName))
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            @endif
        </div>
        
        @if($helpText)
            <div class="form-text mt-2 ms-2">
                <i class="fas fa-info-circle me-1"></i>{{ $helpText }}
            </div>
        @endif
        
        @if(isset($originalData[$fieldName]) && isset($formData[$fieldName]) && $originalData[$fieldName] != $formData[$fieldName])
            <div class="mt-2 text-end">
                <button type="button" class="btn btn-sm btn-outline-warning" wire:click="resetToDefault('{{ $fieldName }}')">
                    <i class="ti ti-rotate-clockwise me-1"></i> Varsayılana Döndür
                </button>
            </div>
        @endif
    </div>
</div>