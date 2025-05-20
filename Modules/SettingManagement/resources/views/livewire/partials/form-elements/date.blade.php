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

@if($settingId)
    <div class="mb-3">
        <label class="form-label">{{ $element['properties']['label'] ?? 'Tarih' }}</label>
        <div class="input-icon">
            <span class="input-icon-addon">
                <i class="fas fa-calendar"></i>
            </span>
            <input 
                type="date" 
                wire:model="values.{{ $settingId }}" 
                class="form-control" 
                placeholder="{{ $element['properties']['placeholder'] ?? 'Tarih seçin' }}"
            >
        </div>
        
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
        Bu tarih alanı için ayar bulunamadı: {{ $element['properties']['name'] ?? 'Bilinmeyen' }}
    </div>
@endif