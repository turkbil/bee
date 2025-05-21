@php
    $settingId = null;
    $settingKey = null;
    $width = isset($element['properties']['width']) ? $element['properties']['width'] : 12;
    $isRequired = isset($element['properties']['required']) && $element['properties']['required'] === true;
    $defaultValue = isset($element['properties']['default_value']) ? $element['properties']['default_value'] : null;
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

<div class="col-{{ $width }}" wire:key="element-{{ $element['properties']['name'] ?? 'radio' }}">
    <div class="card mb-3 w-100">
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">
                <h3 class="card-title d-flex align-items-center">
                    <i class="fa-regular fa-comment fa-flip-horizontal me-2 text-primary"></i>
                    {{ $element['properties']['label'] ?? 'Seçim Düğmeleri' }}
                </h3>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group w-100">
                <div class="form-selectgroup">
                    @foreach($options as $option)
                        <label class="form-selectgroup-item">
                            <input 
                                type="radio" 
                                name="radio_{{ $element['properties']['name'] }}" 
                                value="{{ $option['value'] }}" 
                                class="form-selectgroup-input" 
                                wire:model="formData.{{ $element['properties']['name'] }}"
                                @if($isRequired) required @endif
                                @if($defaultValue === $option['value'] || (isset($option['is_default']) && $option['is_default'])) checked @endif
                            >
                            <span class="form-selectgroup-label">{{ $option['label'] }}</span>
                        </label>
                    @endforeach
                </div>
                
                @if($helpText)
                    <div class="form-text text-muted mt-2">
                        <i class="fas fa-info-circle me-1"></i>
                        {{ $helpText }}
                    </div>
                @endif
                
                @if(isset($originalData[$element['properties']['name']]) && $originalData[$element['properties']['name']] != $formData[$element['properties']['name']])
                    <div class="mt-2 text-end">
                        <span class="badge bg-yellow cursor-pointer" wire:click="resetToDefault('{{ $element['properties']['name'] }}')">
                            <i class="fas fa-undo me-1"></i> Varsayılana Döndür
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>