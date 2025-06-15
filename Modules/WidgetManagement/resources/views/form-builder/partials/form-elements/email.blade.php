@php
    $fieldName = $element['name'] ?? '';
    $fieldLabel = $element['label'] ?? '';
    $isRequired = isset($element['required']) && $element['required'];
    $placeholder = $element['placeholder'] ?? '';
    $helpText = $element['help_text'] ?? '';
    $width = isset($element['width']) ? $element['width'] : (isset($element['properties']['width']) ? $element['properties']['width'] : 12);
    $defaultValue = isset($element['default']) ? $element['default'] : (isset($element['properties']['default_value']) ? $element['properties']['default_value'] : '');
    
    if(isset($formData)) {
        $fieldValue = $formData[$fieldName] ?? $defaultValue;
    } elseif(isset($settings)) {
        $cleanFieldName = str_replace('widget.', '', $fieldName);
        $fieldValue = $settings[$cleanFieldName] ?? $defaultValue;
    } else {
        $fieldValue = $defaultValue;
    }
@endphp

<div class="col-{{ $width }}">
    <div class="mb-3">
        
        <div class="form-floating">
@if(isset($formData))
                <input type="email" 
                    id="{{ $fieldName }}"
                    wire:model="formData.{{ $fieldName }}" 
                    class="form-control @error('formData.' . $fieldName) is-invalid @enderror" 
                    placeholder="{{ $placeholder ?: $fieldLabel }}"
                    @if($isRequired) required @endif>
                <label for="{{ $fieldName }}">
                    {{ $fieldLabel }}
                    @if($isRequired) 
                        <span class="text-danger">*</span> 
                    @endif
                </label>
                @error('formData.' . $fieldName)
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            @else
                <input type="email" 
                    id="{{ $fieldName }}"
                    wire:model="settings.{{ str_replace('widget.', '', $fieldName) }}" 
                    class="form-control @error('settings.' . str_replace('widget.', '', $fieldName)) is-invalid @enderror" 
                    placeholder="{{ $placeholder ?: $fieldLabel }}"
                    @if($isRequired) required @endif>
                <label for="{{ $fieldName }}">
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
    </div>
</div>