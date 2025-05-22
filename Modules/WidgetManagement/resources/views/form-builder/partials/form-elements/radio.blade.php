@php
    $fieldName = $element['name'] ?? '';
    $fieldType = $element['type'] ?? 'radio';
    $fieldLabel = $element['label'] ?? '';
    $isRequired = isset($element['required']) && $element['required'];
    $helpText = $element['help_text'] ?? '';
    $isSystem = isset($element['system']) && $element['system'];
    $width = isset($element['properties']['width']) ? $element['properties']['width'] : 12;
    $options = isset($element['properties']['options']) ? $element['properties']['options'] : [];
    $defaultValue = isset($element['properties']['default_value']) ? $element['properties']['default_value'] : null;
    
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
                    <i class="fas fa-dot-circle me-2 text-primary"></i>
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
                    <div class="form-selectgroup @error('formData.' . $fieldName) is-invalid @enderror">
                        @foreach($options as $option)
                            <label class="form-selectgroup-item">
                                <input 
                                    type="radio" 
                                    name="radio_{{ $fieldName }}" 
                                    value="{{ $option['value'] }}" 
                                    class="form-selectgroup-input" 
                                    wire:model="formData.{{ $fieldName }}"
                                    @if($isRequired) required @endif
                                    @if($defaultValue === $option['value'] || (isset($option['is_default']) && $option['is_default'])) checked @endif
                                >
                                <span class="form-selectgroup-label">{{ $option['label'] }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('formData.' . $fieldName)
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                @else
                    <div class="form-selectgroup @error('settings.' . str_replace('widget.', '', $fieldName)) is-invalid @enderror">
                        @foreach($options as $option)
                            <label class="form-selectgroup-item">
                                <input 
                                    type="radio" 
                                    name="radio_{{ str_replace('widget.', '', $fieldName) }}" 
                                    value="{{ $option['value'] }}" 
                                    class="form-selectgroup-input" 
                                    wire:model="settings.{{ str_replace('widget.', '', $fieldName) }}"
                                    @if($isRequired) required @endif
                                    @if($defaultValue === $option['value'] || (isset($option['is_default']) && $option['is_default'])) checked @endif
                                >
                                <span class="form-selectgroup-label">{{ $option['label'] }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('settings.' . str_replace('widget.', '', $fieldName))
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
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