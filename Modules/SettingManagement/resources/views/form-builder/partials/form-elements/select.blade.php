@php
    // Element dizisinin var olduğunu kontrol edelim
    if (!isset($element) || !is_array($element)) {
        $element = [];
    }
    
    // Temel alan özelliklerini al
    $fieldName = isset($element['name']) ? $element['name'] : (isset($element['properties']['name']) ? $element['properties']['name'] : 'select_' . uniqid());
    $fieldLabel = isset($element['label']) ? $element['label'] : (isset($element['properties']['label']) ? $element['properties']['label'] : 'Açılır Liste');
    $isRequired = isset($element['required']) ? $element['required'] : (isset($element['properties']['required']) && $element['properties']['required']);
    $placeholder = isset($element['placeholder']) ? $element['placeholder'] : (isset($element['properties']['placeholder']) ? $element['properties']['placeholder'] : 'Seçiniz');
    $helpText = isset($element['help_text']) ? $element['help_text'] : (isset($element['properties']['help_text']) ? $element['properties']['help_text'] : '');
    $options = isset($element['options']) ? $element['options'] : (isset($element['properties']['options']) ? $element['properties']['options'] : []);

    // Eğer options boşsa ve setting_id varsa, Setting modelinden options'ı çek
    if (empty($options) && (isset($element['properties']['setting_id']) || isset($element['setting_id']))) {
        $settingId = isset($element['properties']['setting_id']) ? $element['properties']['setting_id'] : $element['setting_id'];
        $setting = \Modules\SettingManagement\App\Models\Setting::find($settingId);
        if ($setting && $setting->options) {
            $options = $setting->options;
        }
    }

    // Eğer options boşsa ve fieldName varsa, Setting modelinden key ile çek
    if (empty($options) && !empty($fieldName)) {
        $setting = \Modules\SettingManagement\App\Models\Setting::where('key', $fieldName)->first();
        if ($setting && $setting->options) {
            $options = $setting->options;
        }
    }
    
    // Diğer özellikleri al
    $width = isset($element['width']) ? $element['width'] : (isset($element['properties']['width']) ? $element['properties']['width'] : 12);
    $defaultValue = isset($element['default']) ? $element['default'] : (isset($element['properties']['default_value']) ? $element['properties']['default_value'] : '');
    
    // values ve originalValues kontrolü
    if (!isset($values) || !is_array($values)) {
        $values = [];
    }
    
    if (!isset($originalValues) || !is_array($originalValues)) {
        $originalValues = [];
    }
    
    // Mevcut değeri belirle
    if(isset($values[$fieldName])) {
        $fieldValue = $values[$fieldName];
    } elseif(isset($settings) && is_object($settings)) {
        $cleanFieldName = str_replace('setting.', '', $fieldName);
        $fieldValue = $settings[$cleanFieldName] ?? $defaultValue;
    } else {
        $fieldValue = $defaultValue;
    }
    
    // values için varsayılan değeri ayarla
    if (!isset($values[$fieldName])) {
        $values[$fieldName] = $fieldValue;
    }
@endphp

<div class="col-{{ $width }}">
    <div class="mb-3">
        <div class="form-floating">
            <select
                id="{{ $fieldName }}"
                wire:model.defer="values.{{ $fieldName }}"
                class="form-select @error('values.' . $fieldName) is-invalid @enderror"
                @if($isRequired) required @endif>
                @if(!$isRequired)
                    <option value="">{{ $placeholder }}</option>
                @endif
                @foreach($options as $key => $option)
                    @php
                        // Option'ı array'e çevir (stdClass veya object olabilir)
                        if (is_object($option)) {
                            $option = (array) $option;
                        }

                        // Array of objects format: [['value' => '...', 'label' => '...'], ...]
                        if (is_array($option) && (isset($option['value']) || isset($option['label']))) {
                            $optionValue = $option['value'] ?? $key;
                            $optionLabel = $option['label'] ?? $option['value'] ?? $key;

                            // 🔧 FIX: label'ın kendisi de bir obje/array olabilir (iç içe yapı)
                            // Örn: {"value":"0","label":{"value":"option1","label":"1 blog/gün"}}
                            if (is_array($optionLabel) || is_object($optionLabel)) {
                                $labelData = is_object($optionLabel) ? (array)$optionLabel : $optionLabel;
                                // İç içe label varsa onu kullan
                                $optionLabel = $labelData['label'] ?? $labelData['value'] ?? json_encode($labelData);
                            }

                            // Value da iç içe olabilir
                            if (is_array($optionValue) || is_object($optionValue)) {
                                $valueData = is_object($optionValue) ? (array)$optionValue : $optionValue;
                                $optionValue = $valueData['value'] ?? json_encode($valueData);
                            }
                        } else {
                            // Associative array format: 'key' => 'label' veya basit string
                            $optionValue = $key;
                            $optionLabel = is_scalar($option) ? (string)$option : json_encode($option);
                        }

                        // Son güvenlik kontrolü - her durumda string olmalı
                        $optionLabel = is_scalar($optionLabel) ? (string)$optionLabel : json_encode($optionLabel);
                        $optionValue = is_scalar($optionValue) ? (string)$optionValue : json_encode($optionValue);
                    @endphp
                    <option
                        value="{{ $optionValue }}"
                        @if($defaultValue == $optionValue || (is_array($option) && isset($option['is_default']) && $option['is_default'])) selected @endif
                    >
                        {{ $optionLabel }}
                    </option>
                @endforeach
            </select>
            <label for="{{ $fieldName }}">
                {{ $fieldLabel }}
                @if($isRequired) 
                    <span class="text-danger">*</span> 
                @endif
            </label>
            @error('values.' . $fieldName)
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        @if($helpText)
            <div class="form-text mt-2 ms-2">
                <i class="fas fa-info-circle me-1"></i>{{ $helpText }}
            </div>
        @endif
        
        @if(isset($originalValues[$fieldName]) && isset($values[$fieldName]) && $originalValues[$fieldName] != $values[$fieldName])
            <div class="mt-2 text-end">
                <button type="button" class="btn btn-sm btn-outline-warning" wire:click="resetToDefault('{{ $fieldName }}')">
                    <i class="fas fa-redo me-1"></i> Varsayılana Döndür
                </button>
            </div>
        @endif
    </div>
</div>