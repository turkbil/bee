@php
    $fieldName = $element['name'] ?? '';
    $fieldType = $element['type'] ?? 'color';
    $fieldLabel = $element['label'] ?? '';
    $isRequired = isset($element['required']) && $element['required'];
    $helpText = $element['help_text'] ?? '';
    $width = isset($element['properties']['width']) ? $element['properties']['width'] : 12;
    $defaultColor = isset($element['properties']['default_value']) ? $element['properties']['default_value'] : '#066fd1';
    
    if(isset($formData)) {
        $fieldValue = $formData[$fieldName] ?? $defaultColor;
    } elseif(isset($settings)) {
        $cleanFieldName = str_replace('widget.', '', $fieldName);
        $fieldValue = $settings[$cleanFieldName] ?? $defaultColor;
    } else {
        $fieldValue = $defaultColor;
    }
@endphp

<div class="col-{{ $width }}">
    <div class="card mb-3 w-100">
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">
                <h3 class="card-title d-flex align-items-center">
                    <i class="fas fa-palette me-2"></i>
                    {{ $fieldLabel }}
                </h3>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group w-100">
                @if(isset($formData))
                    <div class="mb-2">
                        <input type="color" 
                            wire:model="formData.{{ $fieldName }}" 
                            class="form-control form-control-color @error('formData.' . $fieldName) is-invalid @enderror" 
                            value="{{ $fieldValue }}" 
                            title="Renginizi seçin"
                            @if($isRequired) required @endif>
                        @error('formData.' . $fieldName)
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                @else
                    <div class="mb-2">
                        <input type="color" 
                            wire:model="settings.{{ str_replace('widget.', '', $fieldName) }}" 
                            class="form-control form-control-color @error('settings.' . str_replace('widget.', '', $fieldName)) is-invalid @enderror" 
                            value="{{ $fieldValue }}" 
                            title="Renginizi seçin"
                            @if($isRequired) required @endif>
                        @error('settings.' . str_replace('widget.', '', $fieldName))
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                @endif
                
                @if($helpText)
                    <div class="form-text mt-2 ms-2">
                        <i class="fas fa-info-circle me-1"></i>{{ $helpText }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>