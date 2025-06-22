@php
    $settingId = null;
    $settingKey = null;
    $width = isset($element['properties']['width']) ? $element['properties']['width'] : 12;
    $isRequired = isset($element['properties']['required']) && $element['properties']['required'] === true;
    $defaultValue = isset($element['properties']['default_value']) ? $element['properties']['default_value'] : null;
    $placeholder = isset($element['properties']['placeholder']) ? $element['properties']['placeholder'] : t('settingmanagement::general.select_field_placeholder');
    $helpText = isset($element['properties']['help_text']) ? $element['properties']['help_text'] : null;
    $options = isset($element['properties']['options']) ? $element['properties']['options'] : [];
    
    if(isset($element['properties']['setting_id'])) {
        $settingId = $element['properties']['setting_id'];
    } elseif(isset($element['properties']['name'])) {
        $settingName = $element['properties']['name'];
        
        // Ayarı adından bul
        $setting = $settings->firstWhere('key', $settingName);
        if($setting) {
            $settingId = $setting->id;
            $settingKey = $setting->key;
        } else {
            // Ayar yoksa oluştur
            // Bu kısım gerçek uygulamada ayar oluşturma mantığına göre değişebilir
            $settingId = $settingName;
        }
    }
@endphp

<div class="col-{{ $width }}" wire:key="element-{{ $element['properties']['name'] ?? 'select' }}">
    <div class="card mb-3 w-100">
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">
                <h3 class="card-title d-flex align-items-center">
                    <i class="fa-regular fa-comment fa-flip-horizontal me-2"></i>
                    {{ $element['properties']['label'] ?? t('settingmanagement::general.select_field_default') }}
                </h3>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group w-100">
                <select 
                    wire:model="formData.{{ $element['properties']['name'] }}" 
                    class="form-select w-100"
                    @if($isRequired) required @endif
                >
                    <option value="">{{ $placeholder }}</option>
                    @foreach($options as $option)
                        <option 
                            value="{{ $option['value'] }}" 
                            @if($defaultValue === $option['value'] || (isset($option['is_default']) && $option['is_default'])) selected @endif
                        >
                            {{ $option['label'] }}
                        </option>
                    @endforeach
                </select>
                
                @if($helpText)
                    <div class="form-text text-muted mt-2">
                        <i class="fas fa-info-circle me-1"></i>
                        {{ $helpText }}
                    </div>
                @endif
                
                @if(isset($originalData[$element['properties']['name']]) && $originalData[$element['properties']['name']] != $formData[$element['properties']['name']])
                    <div class="mt-2 text-end">
                        <span class="badge bg-yellow cursor-pointer" wire:click="resetToDefault('{{ $element['properties']['name'] }}')">
                            <i class="fas fa-undo me-1"></i> {{ t('settingmanagement::general.reset_to_default_button') }}
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>