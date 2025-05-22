@php
    $fieldName = $element['name'] ?? '';
    $fieldLabel = $element['label'] ?? '';
    $isRequired = isset($element['required']) && $element['required'];
    $placeholder = $element['placeholder'] ?? '';
    $helpText = $element['help_text'] ?? '';
    $isSystem = isset($element['system']) && $element['system'];
    $width = isset($element['properties']['width']) ? $element['properties']['width'] : 12;
    $defaultValue = isset($element['properties']['default_value']) ? $element['properties']['default_value'] : '';
    $options = isset($element['properties']['options']) && is_array($element['properties']['options']) ? $element['properties']['options'] : [];
    
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
    <div class="card mb-3 w-100">
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">
                <h3 class="card-title d-flex align-items-center">
                    <i class="fas fa-caret-square-down me-2 text-primary"></i>
                    {{ $fieldLabel }}
                    @if($isSystem)
                        <span class="badge bg-orange ms-2">Sistem</span>
                    @endif
                </h3>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group w-100">
                @if(isset($formData))
                    <div class="mb-2">
                        <select wire:model="formData.{{ $fieldName }}" 
                            class="form-select @error('formData.' . $fieldName) is-invalid @enderror"
                            @if($isRequired) required @endif>
                            @if($placeholder)
                                <option value="">{{ $placeholder }}</option>
                            @endif
                            @foreach($options as $option)
                                <option value="{{ $option['value'] }}" {{ ($fieldValue == $option['value'] || (isset($option['is_default']) && $option['is_default'])) ? 'selected' : '' }}>
                                    {{ $option['label'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('formData.' . $fieldName)
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                @else
                    <div class="mb-2">
                        <select wire:model="settings.{{ str_replace('widget.', '', $fieldName) }}" 
                            class="form-select @error('settings.' . str_replace('widget.', '', $fieldName)) is-invalid @enderror"
                            @if($isRequired) required @endif>
                            @if($placeholder)
                                <option value="">{{ $placeholder }}</option>
                            @endif
                            @foreach($options as $option)
                                <option value="{{ $option['value'] }}" {{ ($fieldValue == $option['value'] || (isset($option['is_default']) && $option['is_default'])) ? 'selected' : '' }}>
                                    {{ $option['label'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('settings.' . str_replace('widget.', '', $fieldName))
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                @endif
                
                @if($helpText)
                    <div class="form-text text-muted mt-2">
                        <i class="fas fa-info-circle me-1"></i>
                        {{ $helpText }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>