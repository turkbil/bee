@php
    $fieldName = $element['name'] ?? '';
    $fieldLabel = $element['label'] ?? '';
    $isRequired = isset($element['required']) && $element['required'];
    $helpText = $element['help_text'] ?? '';
    $isSystem = isset($element['system']) && $element['system'];
    $width = isset($element['properties']['width']) ? $element['properties']['width'] : 12;
    $defaultValue = isset($element['properties']['default_value']) ? $element['properties']['default_value'] : false;
    $activeLabel = isset($element['properties']['active_label']) ? $element['properties']['active_label'] : 'Evet';
    $inactiveLabel = isset($element['properties']['inactive_label']) ? $element['properties']['inactive_label'] : 'Hayır';
    
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
                    <i class="fas fa-toggle-on me-2 text-primary"></i>
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
                    <div class="mb-3">
                        <label class="form-check form-switch">
                            <input class="form-check-input @error('formData.' . $fieldName) is-invalid @enderror" 
                                type="checkbox" 
                                wire:model="formData.{{ $fieldName }}"
                                value="1"
                                @if($isRequired) required @endif>
                            <span class="form-check-label">
                                {{ $fieldValue ? $activeLabel : $inactiveLabel }}
                            </span>
                        </label>
                        @error('formData.' . $fieldName)
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                @else
                    <div class="mb-3">
                        <label class="form-check form-switch">
                            <input class="form-check-input @error('settings.' . str_replace('widget.', '', $fieldName)) is-invalid @enderror" 
                                type="checkbox" 
                                wire:model="settings.{{ str_replace('widget.', '', $fieldName) }}"
                                value="1"
                                @if($isRequired) required @endif>
                            <span class="form-check-label">
                                {{ $fieldValue ? $activeLabel : $inactiveLabel }}
                            </span>
                        </label>
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