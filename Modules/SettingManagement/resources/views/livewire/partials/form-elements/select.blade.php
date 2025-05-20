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
    <div class="mb-3">
        <label class="form-label">{{ $element['properties']['label'] ?? 'Seçim Listesi' }}</label>
        
        <select wire:model="values.{{ $settingId }}" class="form-select">
            <option value="">Seçiniz</option>
            @if(is_array($setting->options))
                @foreach($setting->options as $key => $label)
                    <option value="{{ $key }}">{{ is_string($label) ? $label : json_encode($label) }}</option>
                @endforeach
            @endif
        </select>
        
        @if(isset($element['properties']['help_text']))
            <div class="form-text text-muted">{{ $element['properties']['help_text'] }}</div>
        @endif
        
        @if(isset($originalValues[$settingId]) && $originalValues[$settingId] != $values[$settingId])
            <div class="mt-2 text-end">
                <span class="badge bg-yellow cursor-pointer" wire:click="resetToDefault({{ $settingId }})">
                    <i class="fas fa-undo me-1"></i> Varsayılana Döndür
                </span>
            </div>
        @endif
    </div>
@else
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle me-2"></i>
        Bu seçim listesi için ayar bulunamadı: {{ $element['properties']['name'] ?? 'Bilinmeyen' }}
    </div>
@endif