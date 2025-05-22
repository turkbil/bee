@php
    $fieldName = $element['name'] ?? '';
    $fieldLabel = $element['label'] ?? '';
    $isRequired = isset($element['required']) && $element['required'];
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
                    <i class="fas fa-circle me-2 text-primary"></i>
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
                    @foreach($options as $index => $option)
                        <div class="form-check mb-2">
                            <input class="form-check-input @error('formData.' . $fieldName) is-invalid @enderror" 
                                type="radio" 
                                wire:model="formData.{{ $fieldName }}" 
                                value="{{ $option['value'] }}" 
                                id="{{ $fieldName }}_{{ $index }}"
                                @if($isRequired) required @endif>
                            <label class="form-check-label" for="{{ $fieldName }}_{{ $index }}">
                                {{ $option['label'] }}
                            </label>
                        </div>
                    @endforeach
                    @error('formData.' . $fieldName)
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                @else
                    @foreach($options as $index => $option)
                        <div class="form-check mb-2">
                            <input class="form-check-input @error('settings.' . str_replace('widget.', '', $fieldName)) is-invalid @enderror" 
                                type="radio" 
                                wire:model="settings.{{ str_replace('widget.', '', $fieldName) }}" 
                                value="{{ $option['value'] }}" 
                                id="{{ str_replace('widget.', '', $fieldName) }}_{{ $index }}"
                                @if($isRequired) required @endif>
                            <label class="form-check-label" for="{{ str_replace('widget.', '', $fieldName) }}_{{ $index }}">
                                {{ $option['label'] }}
                            </label>
                        </div>
                    @endforeach
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