@php
    $fieldName = $element['name'] ?? '';
    $fieldLabel = $element['label'] ?? '';
    $isRequired = isset($element['required']) && $element['required'];
    $helpText = $element['help_text'] ?? '';
    $isSystem = isset($element['system']) && $element['system'];
    $width = isset($element['properties']['width']) ? $element['properties']['width'] : 12;
    $defaultValue = isset($element['properties']['default_value']) ? $element['properties']['default_value'] : 50;
    $min = isset($element['properties']['min']) ? $element['properties']['min'] : 0;
    $max = isset($element['properties']['max']) ? $element['properties']['max'] : 100;
    $step = isset($element['properties']['step']) ? $element['properties']['step'] : 1;
    
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
                    <i class="fas fa-sliders-h me-2 text-primary"></i>
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
                        <div class="d-flex align-items-center">
                            <span class="me-2">{{ $min }}</span>
                            <input type="range" 
                                wire:model="formData.{{ $fieldName }}" 
                                class="form-range flex-fill @error('formData.' . $fieldName) is-invalid @enderror"
                                min="{{ $min }}" 
                                max="{{ $max }}" 
                                step="{{ $step }}"
                                @if($isRequired) required @endif>
                            <span class="ms-2">{{ $max }}</span>
                        </div>
                        <div class="text-center mt-2">
                            <small class="text-muted">Seçilen değer: <strong>{{ $fieldValue }}</strong></small>
                        </div>
                        @error('formData.' . $fieldName)
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                @else
                    <div class="mb-2">
                        <div class="d-flex align-items-center">
                            <span class="me-2">{{ $min }}</span>
                            <input type="range" 
                                wire:model="settings.{{ str_replace('widget.', '', $fieldName) }}" 
                                class="form-range flex-fill @error('settings.' . str_replace('widget.', '', $fieldName)) is-invalid @enderror"
                                min="{{ $min }}" 
                                max="{{ $max }}" 
                                step="{{ $step }}"
                                @if($isRequired) required @endif>
                            <span class="ms-2">{{ $max }}</span>
                        </div>
                        <div class="text-center mt-2">
                            <small class="text-muted">Seçilen değer: <strong>{{ $fieldValue }}</strong></small>
                        </div>
                        @error('settings.' . str_replace('widget.', '', $fieldName))
                            <div class="invalid-feedback d-block">{{ $message }}</div>
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