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
    <div class="col-12" wire:key="setting-{{ $settingId }}">
        <div class="card mb-3 w-100">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <h3 class="card-title d-flex align-items-center">
                        <i class="fas fa-sliders-h me-2"></i>
                        {{ $element['properties']['label'] ?? 'Değer Aralığı' }}
                    </h3>
                </div>
            </div>
            <div class="card-body">
                <div class="form-group w-100">
                    <div class="mb-3">
                        <div class="form-range mb-2 text-primary" id="range-{{ $settingId }}" wire:ignore>
                            <input 
                                type="range" 
                                wire:model="values.{{ $settingId }}" 
                                class="form-range" 
                                @if(isset($element['properties']['min'])) min="{{ $element['properties']['min'] }}" @else min="0" @endif
                                @if(isset($element['properties']['max'])) max="{{ $element['properties']['max'] }}" @else max="100" @endif
                                @if(isset($element['properties']['step'])) step="{{ $element['properties']['step'] }}" @else step="1" @endif
                                onInput="document.getElementById('rangeValue-{{ $settingId }}').innerHTML = this.value"
                            >
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="small text-muted">{{ isset($element['properties']['min']) ? $element['properties']['min'] : '0' }}</span>
                            <span class="badge bg-primary" id="rangeValue-{{ $settingId }}">{{ $values[$settingId] ?? 0 }}</span>
                            <span class="small text-muted">{{ isset($element['properties']['max']) ? $element['properties']['max'] : '100' }}</span>
                        </div>
                    </div>
                    
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
            Bu değer aralığı alanı için ayar bulunamadı: {{ $element['properties']['name'] ?? 'Bilinmeyen' }}
        </div>
    </div>
@endif