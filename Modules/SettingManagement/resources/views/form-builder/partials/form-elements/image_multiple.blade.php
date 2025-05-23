@php
    // Element dizisinin var olduğunu kontrol edelim
    if (!isset($element) || !is_array($element)) {
        $element = [];
    }
    
    // Temel alan özelliklerini al
    $fieldName = isset($element['properties']['name']) ? $element['properties']['name'] : 'image_multiple_' . uniqid();
    $fieldLabel = isset($element['properties']['label']) ? $element['properties']['label'] : 'Çoklu Resim';
    $helpText = isset($element['properties']['help_text']) ? $element['properties']['help_text'] : null;
    
    // Diğer özellikleri al
    $width = isset($element['properties']['width']) ? $element['properties']['width'] : 12;
    
    // formData ve originalData kontrolü
    if (!isset($values) || !is_array($values)) {
        $values = [];
    }
    
    if (!isset($originalValues) || !is_array($originalValues)) {
        $originalValues = [];
    }
    
    // Setting kontrolleri
    $settingId = null;
    $settingKey = null;
    
    // Eğer setting id verilmişse doğrudan kullan
    if (isset($element['properties']['setting_id'])) {
        $settingId = $element['properties']['setting_id'];
    } elseif (isset($element['properties']['name'])) {
        // Eğer settings varsa ve obje ise
        if (isset($settings) && is_object($settings)) {
            $setting = $settings->firstWhere('key', $element['properties']['name']);
            if ($setting) {
                $settingId = $setting->id;
                $settingKey = $setting->key;
            } else {
                // Setting bulunamadı, alan adını kullanalım
                $settingId = $fieldName;
            }
        } else {
            // Settings mevcut değilse, field name kullanalım
            $settingId = $fieldName;
        }
    } else {
        // Hiçbir tanımlayıcı yoksa benzersiz bir ID oluştur
        $settingId = 'img_multiple_' . uniqid();
    }
@endphp

@if($settingId)
    <div class="col-{{ $width }}" wire:key="setting-{{ $settingId }}">
        <div class="card mb-3 w-100">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <h3 class="card-title d-flex align-items-center">
                        <i class="fas fa-images me-2 text-primary"></i>
                        {{ $fieldLabel }}
                    </h3>
                </div>
            </div>
            <div class="card-body">
                <div class="form-group w-100">
                    <!-- Mevcut Çoklu Resimler -->
                    @php
                        $currentImages = isset($multipleImagesArrays[$settingId]) ? $multipleImagesArrays[$settingId] : [];
                    @endphp
                    
                    @include('settingmanagement::form-builder.partials.existing-multiple-images', [
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
                    
                    @if(!empty($helpText))
                        <div class="form-text text-muted mt-2">
                            <i class="fas fa-info-circle me-1"></i>
                            {{ $helpText }}
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
    <div class="col-{{ $width }}">
        <div class="alert alert-danger mb-3 w-100">
            <i class="fas fa-exclamation-circle me-2"></i>
            Bu çoklu resim alanı için ayar bulunamadı: {{ $fieldName ?? 'Bilinmeyen' }}
        </div>
    </div>
@endif