@php
    $fieldName = $element['name'] ?? '';
    $fieldType = $element['type'] ?? 'text';
    $fieldLabel = $element['properties']['label'] ?? '';
    $isRequired = isset($element['properties']['required']) && $element['properties']['required'];
    $placeholder = $element['properties']['placeholder'] ?? '';
    $helpText = $element['properties']['help_text'] ?? '';
    $isSystem = isset($element['properties']['system']) && $element['properties']['system'];
    $width = isset($element['properties']['width']) ? $element['properties']['width'] : 12;
    
    $settingId = null;
    $settingKey = null;
    
    if(isset($element['properties']['setting_id'])) {
        $settingId = $element['properties']['setting_id'];
    } elseif(isset($element['properties']['name'])) {
        $settingName = $element['properties']['name'];
        
        // Ayarı adından bul
        $setting = $settings->firstWhere('key', $settingName);
        if($setting) {
            $settingId = $setting->id;
            $settingKey = $setting->key;
        }
    }
    
    if(isset($formData)) {
        $fieldValue = $formData[$fieldName] ?? '';
    } elseif(isset($settings)) {
        $cleanFieldName = str_replace('widget.', '', $fieldName);
        $fieldValue = $settings[$cleanFieldName] ?? '';
    } else {
        $fieldValue = '';
    }
@endphp

@if($settingId)
    <div class="col-{{ $width }}">
        <div class="card mb-3 w-100">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <h3 class="card-title d-flex align-items-center">
                        <i class="fas fa-font me-2 text-primary"></i>
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
                            <i class="fas fa-font"></i>
                        </span>
                        <input 
                            type="text" 
                            wire:model="values.{{ $settingId }}" 
                            class="form-control w-100" 
                            placeholder="{{ $placeholder }}"
                            @if($isRequired) required @endif>
                    </div>
                    
                    @if($helpText)
                        <div class="form-text text-muted mt-2">
                            <i class="fas fa-info-circle me-1"></i>
                            {{ $helpText }}
                        </div>
                    @endif
                    
                    @if(isset($originalValues[$settingId]) && $originalValues[$settingId] != $values[$settingId])
                        <div class="mt-2 text-end">
                            <span class="badge bg-yellow cursor-pointer" wire:click="resetToDefault({{ $settingId }})">
                                <i class="fas fa-undo me-1"></i> Varsayılana Döndür
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@else
    <div class="col-{{ $width }}">
        <div class="alert alert-danger mb-3 w-100">
            <i class="fas fa-exclamation-circle me-2"></i>
            Bu metin alanı için ayar bulunamadı: {{ $element['properties']['name'] ?? 'Bilinmeyen' }}
        </div>
    </div>
@endif