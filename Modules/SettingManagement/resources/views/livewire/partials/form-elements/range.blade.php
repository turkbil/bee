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
        <label class="form-label">{{ $element['properties']['label'] ?? 'Değer Aralığı' }}</label>
        
        <div class="row g-2 align-items-center">
            <div class="col">
                <input 
                    type="range" 
                    wire:model="values.{{ $settingId }}" 
                    class="form-range" 
                    @if(isset($element['properties']['min'])) min="{{ $element['properties']['min'] }}" @else min="0" @endif
                    @if(isset($element['properties']['max'])) max="{{ $element['properties']['max'] }}" @else max="100" @endif
                    @if(isset($element['properties']['step'])) step="{{ $element['properties']['step'] }}" @else step="1" @endif
                >
            </div>
            <div class="col-auto">
                <span class="form-colorinput-color text-center" style="width: 3rem;">
                    {{ $values[$settingId] ?? 0 }}
                </span>
            </div>
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
        Bu değer aralığı alanı için ayar bulunamadı: {{ $element['properties']['name'] ?? 'Bilinmeyen' }}
    </div>
@endif