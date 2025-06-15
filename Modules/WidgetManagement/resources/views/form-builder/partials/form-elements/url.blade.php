@php
    $fieldName = $element['name'] ?? '';
    $fieldLabel = $element['label'] ?? '';
    $isRequired = isset($element['required']) && $element['required'];
    $placeholder = $element['placeholder'] ?? '';
    $helpText = $element['help_text'] ?? '';
    $width = isset($element['properties']['width']) ? $element['properties']['width'] : 12;
    $defaultValue = isset($element['properties']['default_value']) ? $element['properties']['default_value'] : '';
    
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
                <input type="url" 
                    id="{{ $fieldName }}"
                    wire:model="formData.{{ $fieldName }}" 
                    class="form-control @error('formData.' . $fieldName) is-invalid @enderror" 
                    placeholder="{{ $placeholder }}"
                    @if($isRequired) required @endif>
                @error('formData.' . $fieldName)
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            @else
                <input type="url" 
                    id="{{ $fieldName }}"
                    wire:model="settings.{{ str_replace('widget.', '', $fieldName) }}" 
                    class="form-control @error('settings.' . str_replace('widget.', '', $fieldName)) is-invalid @enderror" 
                    placeholder="{{ $placeholder }}"
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