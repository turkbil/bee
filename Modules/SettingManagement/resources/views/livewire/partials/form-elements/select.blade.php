@php
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
@endphp

@if($settingId && $setting)
    <div class="col-12" wire:key="setting-{{ $settingId }}">
        <div class="card mb-3 w-100">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <h3 class="card-title d-flex align-items-center">
                        <i class="fas fa-list me-2 text-primary"></i>
                        {{ $element['properties']['label'] ?? 'Seçim Listesi' }}
                    </h3>
                </div>
            </div>
            <div class="card-body">
                <div class="form-group w-100">
                    <select wire:model="values.{{ $settingId }}" class="form-select w-100">
                        <option value="">Seçiniz</option>
                        @if(is_array($setting->options))
                            @foreach($setting->options as $key => $option)
                                @php
                                    $optionLabel = '';
                                    if (is_string($option)) {
                                        $optionLabel = $option;
                                    } elseif (is_array($option)) {
                                        $optionLabel = $option['label'] ?? $key;
                                    } elseif (is_object($option)) {
                                        $optionData = json_decode(json_encode($option), true);
                                        $optionLabel = $optionData['label'] ?? $key;
                                    }
                                @endphp
                                <option value="{{ $key }}">{{ $optionLabel }}</option>
                            @endforeach
                        @endif
                    </select>
                    
                    @if(isset($element['properties']['help_text']) && !empty($element['properties']['help_text']))
                        <div class="form-text text-muted mt-2">
                            <i class="fas fa-info-circle me-1"></i>
                            {{ $element['properties']['help_text'] }}
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
    <div class="col-12">
        <div class="alert alert-danger mb-3 w-100">
            <i class="fas fa-exclamation-circle me-2"></i>
            Bu seçim listesi için ayar bulunamadı: {{ $element['properties']['name'] ?? 'Bilinmeyen' }}
        </div>
    </div>
@endif