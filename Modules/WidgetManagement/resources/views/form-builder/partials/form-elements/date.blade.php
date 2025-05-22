@php
    $fieldName = $element['name'] ?? '';
    $fieldType = $element['type'] ?? 'date';
    $fieldLabel = $element['label'] ?? '';
    $isRequired = isset($element['required']) && $element['required'];
    $placeholder = $element['placeholder'] ?? 'Tarih seçin';
    $helpText = $element['help_text'] ?? '';
    $isSystem = isset($element['system']) && $element['system'];
    $width = isset($element['properties']['width']) ? $element['properties']['width'] : 12;
    $minDate = isset($element['properties']['min_date']) ? $element['properties']['min_date'] : null;
    $maxDate = isset($element['properties']['max_date']) ? $element['properties']['max_date'] : null;
    
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
                    <i class="fas fa-calendar me-2 text-primary"></i>
                    {{ $fieldLabel }}
                    @if($isSystem)
                        <span class="badge bg-orange ms-2">Sistem</span>
                    @endif
                </h3>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group w-100">
                <div class="input-icon w-100">
                    <span class="input-icon-addon">
                        <i class="fas fa-calendar"></i>
                    </span>
                    @if(isset($formData))
                        <input type="date" 
                            wire:model="formData.{{ $fieldName }}" 
                            class="form-control w-100 @error('formData.' . $fieldName) is-invalid @enderror" 
                            @if($minDate) min="{{ $minDate }}" @endif
                            @if($maxDate) max="{{ $maxDate }}" @endif
                            @if($isRequired) required @endif>
                        @error('formData.' . $fieldName)
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    @else
                        <input type="date" 
                            wire:model="settings.{{ str_replace('widget.', '', $fieldName) }}" 
                            class="form-control w-100 @error('settings.' . str_replace('widget.', '', $fieldName)) is-invalid @enderror" 
                            @if($minDate) min="{{ $minDate }}" @endif
                            @if($maxDate) max="{{ $maxDate }}" @endif
                            @if($isRequired) required @endif>
                        @error('settings.' . str_replace('widget.', '', $fieldName))
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    @endif
                </div>
                
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