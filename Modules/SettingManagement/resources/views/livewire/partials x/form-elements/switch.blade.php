@php
    $settingId = null;
    $settingKey = null;
    $width = isset($element['properties']['width']) ? $element['properties']['width'] : 12;
    $isRequired = isset($element['properties']['required']) && $element['properties']['required'] === true;
    $defaultValue = isset($element['properties']['default_value']) ? $element['properties']['default_value'] : null;
    // $switchLabel değişkeni kaldırıldı
    $activeLabel = isset($element['properties']['active_label']) ? $element['properties']['active_label'] : 'Aktif';
    $inactiveLabel = isset($element['properties']['inactive_label']) ? $element['properties']['inactive_label'] : 'Aktif Değil';
    // $defaultValueText değişkeni kaldırıldı
    $helpText = isset($element['properties']['help_text']) ? $element['properties']['help_text'] : null;
    
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

<div class="col-{{ $width }}" wire:key="element-{{ $element['properties']['name'] ?? 'switch' }}">
    <div class="card mb-3 w-100">
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">
                <h3 class="card-title d-flex align-items-center">
                    <i class="fa-regular fa-comment fa-flip-horizontal me-2 text-primary"></i>
                    {{ $element['properties']['label'] ?? 'Anahtar' }}
                </h3>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group w-100">
                <div class="mb-3">
                    <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                        <input type="checkbox" 
                            id="switch-{{ $element['properties']['name'] }}" 
                            name="{{ $element['properties']['name'] }}" 
                            wire:model="formData.{{ $element['properties']['name'] }}"
                            value="1"
                            @if($isRequired) required @endif
                            @if($defaultValue === 'true') checked @endif
                        >
                        <div class="state p-success p-on ms-2">
                            <label>{{ $activeLabel }}</label>
                        </div>
                        <div class="state p-danger p-off ms-2">
                            <label>{{ $inactiveLabel }}</label>
                        </div>
                    </div>
                    {{-- switchLabel kısmı kaldırıldı --}}
                </div>
                
                {{-- defaultValueText kısmı kaldırıldı --}}
                
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