@php
    $fieldName = $element['name'] ?? '';
    $fieldType = $element['type'] ?? 'textarea';
    $fieldLabel = $element['label'] ?? '';
    $isRequired = isset($element['required']) && $element['required'];
    $placeholder = $element['placeholder'] ?? '';
    $helpText = $element['help_text'] ?? '';
    $isSystem = isset($element['system']) && $element['system'];
    $width = isset($element['properties']['width']) ? $element['properties']['width'] : 12;
    $rows = isset($element['properties']['rows']) ? $element['properties']['rows'] : 4;
    
    if(isset($formData)) {
        $fieldValue = $formData[$fieldName] ?? '';
    } elseif(isset($settings)) {
        $cleanFieldName = str_replace('widget.', '', $fieldName);
        $fieldValue = $settings[$cleanFieldName] ?? '';
    } else {
        $fieldValue = '';
    }
@endphp

<div class="col-{{ $width }}">
    <div class="card mb-3 w-100">
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">
                <h3 class="card-title d-flex align-items-center">
                    <i class="fas fa-align-left me-2 text-primary"></i>
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
                    <textarea
                        wire:model="formData.{{ $fieldName }}"
                        class="form-control @error('formData.' . $fieldName) is-invalid @enderror"
                        placeholder="{{ $placeholder }}"
                        rows="{{ $rows }}"
                        @if($isRequired) required @endif></textarea>
                    @error('formData.' . $fieldName)
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                @else
                    <textarea
                        wire:model="settings.{{ str_replace('widget.', '', $fieldName) }}"
                        class="form-control @error('settings.' . str_replace('widget.', '', $fieldName)) is-invalid @enderror"
                        placeholder="{{ $placeholder }}"
                        rows="{{ $rows }}"
                        @if($isRequired) required @endif></textarea>
                    @error('settings.' . str_replace('widget.', '', $fieldName))
                        <div class="invalid-feedback">{{ $message }}</div>
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