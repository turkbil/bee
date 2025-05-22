@php
    $fieldName = $element['name'] ?? '';
    $fieldType = $element['type'] ?? 'switch';
    $fieldLabel = $element['label'] ?? '';
    $isRequired = isset($element['required']) && $element['required'];
    $helpText = $element['help_text'] ?? '';
    $isSystem = isset($element['system']) && $element['system'];
    $width = isset($element['properties']['width']) ? $element['properties']['width'] : 12;
    $defaultValue = isset($element['properties']['default_value']) ? filter_var($element['properties']['default_value'], FILTER_VALIDATE_BOOLEAN) : false;
    $activeLabel = isset($element['properties']['active_label']) ? $element['properties']['active_label'] : 'Aktif';
    $inactiveLabel = isset($element['properties']['inactive_label']) ? $element['properties']['inactive_label'] : 'Aktif Değil';
    
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
                    <div class="mb-3 @error('formData.' . $fieldName) is-invalid @enderror">
                        <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                            <input type="checkbox" 
                                id="switch-{{ $fieldName }}" 
                                name="{{ $fieldName }}" 
                                wire:model="formData.{{ $fieldName }}"
                                value="1"
                                @if($isRequired) required @endif>
                            <div class="state p-success p-on ms-2">
                                <label>{{ $activeLabel }}</label>
                            </div>
                            <div class="state p-danger p-off ms-2">
                                <label>{{ $inactiveLabel }}</label>
                            </div>
                        </div>
                        @error('formData.' . $fieldName)
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                @else
                    <div class="mb-3 @error('settings.' . str_replace('widget.', '', $fieldName)) is-invalid @enderror">
                        <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                            <input type="checkbox" 
                                id="switch-{{ str_replace('widget.', '', $fieldName) }}" 
                                name="{{ str_replace('widget.', '', $fieldName) }}" 
                                wire:model="settings.{{ str_replace('widget.', '', $fieldName) }}"
                                value="1"
                                @if($isRequired) required @endif>
                            <div class="state p-success p-on ms-2">
                                <label>{{ $activeLabel }}</label>
                            </div>
                            <div class="state p-danger p-off ms-2">
                                <label>{{ $inactiveLabel }}</label>
                            </div>
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