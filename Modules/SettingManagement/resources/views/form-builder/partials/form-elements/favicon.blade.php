@php
    $setting = null;
    $settingId = null;

    if(isset($element['properties']['setting_id'])) {
        $settingId = $element['properties']['setting_id'];
        $setting = $settings->firstWhere('id', $settingId);
    } elseif(isset($element['properties']['name'])) {
        $settingName = $element['properties']['name'];
        $setting = $settings->firstWhere('key', $settingName);
        if($setting) {
            $settingId = $setting->id;
        }
    }
@endphp

@if($setting)
    <div class="col-12" wire:key="setting-{{ $setting->id }}">
        <div class="card mb-3 w-100">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <h3 class="card-title d-flex align-items-center">
                        <i class="fas fa-star me-2"></i>
                        {{ $element['properties']['label'] ?? $setting->label }}
                    </h3>
                </div>
            </div>
            <div class="card-body">
                <div class="form-group w-100">
                    {{-- Universal MediaManagement Component (label gösterme - card-header'da zaten var) --}}
                    @livewire('mediamanagement::universal-media', [
                        'modelId' => $setting->id,
                        'modelType' => 'setting',
                        'modelClass' => 'Modules\SettingManagement\App\Models\Setting',
                        'collections' => ['featured_image'],
                        'maxGalleryItems' => 1,
                        'sortable' => false,
                        'setFeaturedFromGallery' => false,
                        'hideLabel' => true,
                        'acceptedFileTypes' => '.ico,.png,image/x-icon,image/vnd.microsoft.icon,image/png'
                    ], key('setting-media-fb-favicon-' . $setting->id))

                    @if(isset($element['properties']['help_text']) && !empty($element['properties']['help_text']))
                        <div class="form-text text-muted mt-2">
                            <i class="fas fa-info-circle me-1"></i>
                            {{ $element['properties']['help_text'] }}
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
            Bu favicon alanı için ayar bulunamadı: {{ $element['properties']['name'] ?? 'Bilinmeyen' }}
        </div>
    </div>
@endif
