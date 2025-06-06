@php
    // Element dizisinin var olduğunu kontrol edelim
    if (!isset($element) || !is_array($element)) {
        $element = [];
    }
    
    // Temel alan özelliklerini al
    $fieldName = $element['name'] ?? 'textarea_' . uniqid();
    $fieldLabel = $element['label'] ?? 'Uzun Metin Alanı';
    $isRequired = isset($element['required']) && $element['required'];
    $placeholder = $element['placeholder'] ?? '';
    $helpText = $element['help_text'] ?? '';
    $isSystem = isset($element['system']) && $element['system'];
    
    // Diğer özellikleri al
    $width = isset($element['width']) ? $element['width'] : 12;
    $defaultValue = isset($element['default']) ? $element['default'] : '';
    $rows = isset($element['rows']) ? $element['rows'] : 4;
    
    // formData ve originalData kontrolü
    if (!isset($formData) || !is_array($formData)) {
        $formData = [];
    }
    
    if (!isset($originalData) || !is_array($originalData)) {
        $originalData = [];
    }
    
    // Mevcut değeri belirle
    if(isset($formData[$fieldName])) {
        $fieldValue = $formData[$fieldName];
    } elseif(isset($settings) && is_object($settings)) {
        $cleanFieldName = str_replace('widget.', '', $fieldName);
        $fieldValue = $settings[$cleanFieldName] ?? $defaultValue;
    } else {
        $fieldValue = $defaultValue;
    }
    
    // formData için varsayılan değeri ayarla
    if (!isset($formData[$fieldName])) {
        $formData[$fieldName] = $fieldValue;
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
                    <div class="mb-2">
                        <textarea wire:model="formData.{{ $fieldName }}" 
                            class="form-control @error('formData.' . $fieldName) is-invalid @enderror" 
                            rows="4"
                            placeholder="{{ $placeholder }}"
                            @if($isRequired) required @endif></textarea>
                        @error('formData.' . $fieldName)
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                @else
                    <div class="mb-2">
                        <textarea wire:model="settings.{{ str_replace('widget.', '', $fieldName) }}" 
                            class="form-control @error('settings.' . str_replace('widget.', '', $fieldName)) is-invalid @enderror" 
                            rows="4"
                            placeholder="{{ $placeholder }}"
                            @if($isRequired) required @endif></textarea>
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
                
                @if(isset($originalData[$fieldName]) && isset($formData[$fieldName]) && $originalData[$fieldName] != $formData[$fieldName])
                    <div class="mt-2 text-end">
                        <span class="badge bg-yellow cursor-pointer" wire:click="resetToDefault('{{ $fieldName }}')">
                            <i class="fas fa-undo me-1"></i> Varsayılana Döndür
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>