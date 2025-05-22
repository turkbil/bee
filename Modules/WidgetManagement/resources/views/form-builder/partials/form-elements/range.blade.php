@php
    $fieldName = $element['name'] ?? '';
    $fieldType = $element['type'] ?? 'range';
    $fieldLabel = $element['label'] ?? '';
    $isRequired = isset($element['required']) && $element['required'];
    $helpText = $element['help_text'] ?? '';
    $isSystem = isset($element['system']) && $element['system'];
    $width = isset($element['properties']['width']) ? $element['properties']['width'] : 12;
    $min = isset($element['properties']['min']) ? $element['properties']['min'] : 0;
    $max = isset($element['properties']['max']) ? $element['properties']['max'] : 100;
    $step = isset($element['properties']['step']) ? $element['properties']['step'] : 1;
    
    if(isset($formData)) {
        $fieldValue = $formData[$fieldName] ?? $min;
    } elseif(isset($settings)) {
        $cleanFieldName = str_replace('widget.', '', $fieldName);
        $fieldValue = $settings[$cleanFieldName] ?? $min;
    } else {
        $fieldValue = $min;
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
                    <div class="mb-3 @error('formData.' . $fieldName) is-invalid @enderror">
                        <div class="form-range mb-2 text-primary" id="range-{{ $fieldName }}" wire:ignore>
                            <input 
                                type="range" 
                                wire:model="formData.{{ $fieldName }}" 
                                class="form-range" 
                                min="{{ $min }}"
                                max="{{ $max }}"
                                step="{{ $step }}"
                                onInput="document.getElementById('rangeValue-{{ $fieldName }}').innerHTML = this.value"
                                @if($isRequired) required @endif
                            >
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="small text-muted">{{ $min }}</span>
                            <span class="badge bg-primary" id="rangeValue-{{ $fieldName }}">{{ $fieldValue }}</span>
                            <span class="small text-muted">{{ $max }}</span>
                        </div>
                        @error('formData.' . $fieldName)
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                @else
                    <div class="mb-3 @error('settings.' . str_replace('widget.', '', $fieldName)) is-invalid @enderror">
                        <div class="form-range mb-2 text-primary" id="range-{{ str_replace('widget.', '', $fieldName) }}" wire:ignore>
                            <input 
                                type="range" 
                                wire:model="settings.{{ str_replace('widget.', '', $fieldName) }}" 
                                class="form-range" 
                                min="{{ $min }}"
                                max="{{ $max }}"
                                step="{{ $step }}"
                                onInput="document.getElementById('rangeValue-{{ str_replace('widget.', '', $fieldName) }}').innerHTML = this.value"
                                @if($isRequired) required @endif
                            >
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="small text-muted">{{ $min }}</span>
                            <span class="badge bg-primary" id="rangeValue-{{ str_replace('widget.', '', $fieldName) }}">{{ $fieldValue }}</span>
                            <span class="small text-muted">{{ $max }}</span>
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