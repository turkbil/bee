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
        <label class="form-label">{{ $element['properties']['label'] ?? 'Resim' }}</label>
        
        @include('settingmanagement::livewire.partials.image-upload', [
            'imageKey' => $settingId,
            'label' => $element['properties']['placeholder'] ?? 'Görseli sürükleyip bırakın veya tıklayın',
            'values' => $values
        ])
        
        @if(isset($element['properties']['help_text']))
            <div class="form-text text-muted">{{ $element['properties']['help_text'] }}</div>
        @endif
    </div>
@else
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle me-2"></i>
        Bu resim alanı için ayar bulunamadı: {{ $element['properties']['name'] ?? 'Bilinmeyen' }}
    </div>
@endif