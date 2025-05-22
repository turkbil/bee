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
                        <i class="fas fa-images me-2 text-primary"></i>
                        {{ $element['properties']['label'] ?? 'Çoklu Resim' }}
                    </h3>
                </div>
            </div>
            <div class="card-body">
                <div class="form-group w-100">
                    <!-- Mevcut Çoklu Resimler -->
                    @php
                        $currentImages = isset($multipleImagesArrays[$settingId]) ? $multipleImagesArrays[$settingId] : [];
                    @endphp
                    
                    @include('widgetmanagement::form-builder.partials.existing-multiple-images', [
                        'settingId' => $settingId,
                        'images' => $currentImages
                    ])
                    
                    <!-- Yükleme Alanı -->
                    <div class="card mt-3">
                        <div class="card-body p-3">
                            <form wire:submit="updatedTempPhoto">
                                <div class="dropzone p-4" onclick="document.getElementById('file-upload-{{ $settingId }}').click()">
                                    <input type="file" id="file-upload-{{ $settingId }}" class="d-none" 
                                        wire:model="tempPhoto" accept="image/*" multiple
                                        wire:click="setPhotoField('{{ $settingId }}')">
                                        
                                    <div class="text-center">
                                        <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                        <h4 class="text-muted">Görselleri sürükleyip bırakın veya tıklayın</h4>
                                        <p class="text-muted small">PNG, JPG, WEBP, GIF - Maks 2MB - <strong>Toplu seçim yapabilirsiniz</strong></p>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Geçici yüklenen görseller -->
                    @if(isset($temporaryMultipleImages[$settingId]) && is_array($temporaryMultipleImages[$settingId]) && count($temporaryMultipleImages[$settingId]) > 0)
                        <div class="mt-3">
                            <label class="form-label">Yeni Yüklenen Görseller</label>
                            <div class="row g-2">
                                @foreach($temporaryMultipleImages[$settingId] as $index => $photo)
                                    @if($photo)
                                    <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                                        <div class="position-relative">
                                            <div class="position-absolute top-0 end-0 p-1">
                                                <button type="button" class="btn btn-danger btn-icon btn-sm"
                                                        wire:click="removeMultipleImageField({{ $settingId }}, {{ $index }})">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                            <div class="img-responsive img-responsive-1x1 rounded border" 
                                                style="background-image: url({{ $photo->temporaryUrl() }})">
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
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
            Bu çoklu resim alanı için ayar bulunamadı: {{ $element['properties']['name'] ?? 'Bilinmeyen' }}
        </div>
    </div>
@endif