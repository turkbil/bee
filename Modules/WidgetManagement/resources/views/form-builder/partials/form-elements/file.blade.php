@php
    $fieldName = $element['name'] ?? '';
    $fieldType = $element['type'] ?? 'file';
    $fieldLabel = $element['label'] ?? '';
    $isRequired = isset($element['required']) && $element['required'];
    $placeholder = $element['placeholder'] ?? 'Dosyayı sürükleyip bırakın veya tıklayın';
    $helpText = $element['help_text'] ?? '';
    $isSystem = isset($element['system']) && $element['system'];
    $width = isset($element['properties']['width']) ? $element['properties']['width'] : 12;
    $allowedTypes = isset($element['properties']['allowed_types']) ? $element['properties']['allowed_types'] : '';
    $maxSize = isset($element['properties']['max_size']) ? $element['properties']['max_size'] : null;
    
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
                    <i class="fas fa-file me-2 text-primary"></i>
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
                    <div class="@error('formData.' . $fieldName) is-invalid @enderror">
                        @include('widgetmanagement::form-builder.partials.file-upload', [
                            'fileKey' => $fieldName,
                            'model' => 'formData',
                            'label' => $placeholder,
                            'isRequired' => $isRequired,
                            'allowedTypes' => $allowedTypes,
                            'maxSize' => $maxSize
                        ])
                    </div>
                    @error('formData.' . $fieldName)
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                @else
                    <div class="@error('settings.' . str_replace('widget.', '', $fieldName)) is-invalid @enderror">
                        @include('widgetmanagement::form-builder.partials.file-upload', [
                            'fileKey' => str_replace('widget.', '', $fieldName),
                            'model' => 'settings',
                            'label' => $placeholder,
                            'isRequired' => $isRequired,
                            'allowedTypes' => $allowedTypes,
                            'maxSize' => $maxSize
                        ])
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