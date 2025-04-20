@include('widgetmanagement::helper')
<div>
    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <h3 class="card-title d-flex align-items-center mb-0">
                        <i class="fas fa-layer-group me-2"></i>
                        @if($itemId)
                            İçerik Düzenle - {{ $tenantWidget->settings['title'] ?? $tenantWidget->widget->name }}
                        @else
                            Yeni İçerik Ekle - {{ $tenantWidget->settings['title'] ?? $tenantWidget->widget->name }}
                        @endif
                    </h3>
                </div>
                <div class="col-md-3 position-relative d-flex justify-content-center align-items-center">
                    <div wire:loading
                        wire:target="render, saveItem"
                        class="position-absolute top-50 start-50 translate-middle text-center"
                        style="width: 100%; max-width: 250px;">
                        <div class="small text-muted mb-2">Güncelleniyor...</div>
                        <div class="progress mb-1">
                            <div class="progress-bar progress-bar-indeterminate"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 text-md-end">
                    <a href="{{ route('admin.widgetmanagement.items', $tenantWidgetId) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Geri Dön
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form wire:submit.prevent="save(true)">
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-3">
                    <div class="card-status-start bg-primary"></div>
                    <div class="card-header">
                        <h3 class="card-title">Temel Bilgiler</h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach($schema as $field)
                                @if($field['name'] !== 'unique_id' && ($field['type'] === 'text' || $field['type'] === 'textarea' || $field['type'] === 'select' || $field['type'] === 'number' || $field['type'] === 'email' || $field['type'] === 'tel' || $field['type'] === 'url' || $field['type'] === 'color' || $field['type'] === 'date' || $field['type'] === 'time'))
                                    <div class="col-12 mb-3">
                                        <label for="field-{{ $field['name'] }}" class="form-label{{ isset($field['required']) && $field['required'] ? ' required' : '' }}">
                                            {{ $field['label'] }}
                                            @if(isset($field['system']) && $field['system'] && $field['name'] != 'unique_id')
                                                <span class="badge bg-primary ms-1">Sistem</span>
                                            @endif
                                        </label>
                                        
                                        @switch($field['type'])
                                            @case('textarea')
                                                <textarea wire:model="formData.{{ $field['name'] }}" 
                                                    id="field-{{ $field['name'] }}" 
                                                    class="form-control" 
                                                    rows="5" 
                                                    placeholder="Değeri buraya giriniz..."></textarea>
                                                @break
                                            
                                            @case('select')
                                                @if(is_array($field['options']))
                                                    <select wire:model="formData.{{ $field['name'] }}" 
                                                        id="field-{{ $field['name'] }}" 
                                                        class="form-select">
                                                        <option value="">Seçiniz</option>
                                                        @foreach($field['options'] as $key => $label)
                                                            <option value="{{ $key }}">{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                @endif
                                                @break
                                            
                                            @case('number')
                                                <div class="input-icon">
                                                    <span class="input-icon-addon">
                                                        <i class="fas fa-hashtag"></i>
                                                    </span>
                                                    <input type="number" 
                                                        wire:model="formData.{{ $field['name'] }}" 
                                                        id="field-{{ $field['name'] }}" 
                                                        class="form-control">
                                                </div>
                                                @break
                                            
                                            @case('email')
                                                <div class="input-icon">
                                                    <span class="input-icon-addon">
                                                        <i class="fas fa-envelope"></i>
                                                    </span>
                                                    <input type="email" 
                                                        wire:model="formData.{{ $field['name'] }}" 
                                                        id="field-{{ $field['name'] }}" 
                                                        class="form-control">
                                                </div>
                                                @break
                                            
                                            @case('tel')
                                                <div class="input-icon">
                                                    <span class="input-icon-addon">
                                                        <i class="fas fa-phone"></i>
                                                    </span>
                                                    <input type="tel" 
                                                        wire:model="formData.{{ $field['name'] }}" 
                                                        id="field-{{ $field['name'] }}" 
                                                        class="form-control">
                                                </div>
                                                @break
                                            
                                            @case('url')
                                                <div class="input-icon">
                                                    <span class="input-icon-addon">
                                                        <i class="fas fa-globe"></i>
                                                    </span>
                                                    <input type="url" 
                                                        wire:model="formData.{{ $field['name'] }}" 
                                                        id="field-{{ $field['name'] }}" 
                                                        class="form-control">
                                                </div>
                                                @break
                                            
                                            @case('color')
                                                <div class="d-flex align-items-center gap-2">
                                                    <input type="color" 
                                                        wire:model="formData.{{ $field['name'] }}" 
                                                        id="field-{{ $field['name'] }}" 
                                                        class="form-control form-control-color">
                                                    <span class="text-muted">{{ $formData[$field['name']] ?? '#000000' }}</span>
                                                </div>
                                                @break
                                            
                                            @case('date')
                                                <div class="input-icon">
                                                    <span class="input-icon-addon">
                                                        <i class="fas fa-calendar"></i>
                                                    </span>
                                                    <input type="date" 
                                                        wire:model="formData.{{ $field['name'] }}" 
                                                        id="field-{{ $field['name'] }}" 
                                                        class="form-control">
                                                </div>
                                                @break
                                            
                                            @case('time')
                                                <div class="input-icon">
                                                    <span class="input-icon-addon">
                                                        <i class="fas fa-clock"></i>
                                                    </span>
                                                    <input type="time" 
                                                        wire:model="formData.{{ $field['name'] }}" 
                                                        id="field-{{ $field['name'] }}" 
                                                        class="form-control">
                                                </div>
                                                @break
                                            
                                            @default
                                                <div class="input-icon">
                                                    <span class="input-icon-addon">
                                                        <i class="fas fa-font"></i>
                                                    </span>
                                                    <input type="{{ $field['type'] }}" 
                                                        wire:model="formData.{{ $field['name'] }}" 
                                                        id="field-{{ $field['name'] }}" 
                                                        class="form-control">
                                                </div>
                                        @endswitch
                                        
                                        @error('formData.' . $field['name'])
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Yayın Ayarları -->
                <div class="card mb-3">
                    <div class="card-status-start bg-green"></div>
                    <div class="card-header">
                        <h3 class="card-title">Yayın Ayarları</h3>
                    </div>
                    <div class="card-body">
                        @foreach($schema as $field)
                            @if($field['type'] === 'checkbox')
                                <div class="mb-3">
                                    <label class="form-check form-switch">
                                        <input type="checkbox" 
                                            wire:model="formData.{{ $field['name'] }}" 
                                            class="form-check-input">
                                        <span class="form-check-label">{{ $field['label'] }}</span>
                                    </label>
                                    @error('formData.' . $field['name'])
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                
                <!-- Görseller ve Medya Alanları -->
                <div class="card mb-3">
                    <div class="card-status-start bg-purple"></div>
                    <div class="card-header">
                        <h3 class="card-title">Görseller ve Medya</h3>
                    </div>
                    <div class="card-body">
                        @php $hasMediaFields = false; @endphp
                        
                        @foreach($schema as $field)
                            @if($field['type'] === 'image')
                                @php $hasMediaFields = true; @endphp
                                <div class="mb-4">
                                    <label class="form-label{{ isset($field['required']) && $field['required'] ? ' required' : '' }}">
                                        {{ $field['label'] }}
                                    </label>
                                    
                                    @include('widgetmanagement::partials.image-upload', [
                                        'imageKey' => $field['name'],
                                        'label' => 'Görseli sürükleyip bırakın veya tıklayın',
                                        'values' => $formData
                                    ])
                                </div>
                            @elseif($field['type'] === 'file')
                                @php $hasMediaFields = true; @endphp
                                <div class="mb-4">
                                    <label class="form-label{{ isset($field['required']) && $field['required'] ? ' required' : '' }}">
                                        {{ $field['label'] }}
                                    </label>
                                    
                                    @include('widgetmanagement::partials.file-upload', [
                                        'fileKey' => $field['name'],
                                        'label' => 'Dosyayı sürükleyip bırakın veya tıklayın',
                                        'values' => $formData
                                    ])
                                </div>
                            @elseif($field['type'] === 'image_multiple')
                                @php $hasMediaFields = true; @endphp
                                <div class="mb-4">
                                    <label class="form-label{{ isset($field['required']) && $field['required'] ? ' required' : '' }}">
                                        {{ $field['label'] }}
                                    </label>
                                    
                                    <!-- Mevcut Çoklu Resimler -->
                                    @php
                                        $fieldName = $field['name'];
                                        $currentImages = isset($formData[$fieldName]) && is_array($formData[$fieldName]) ? $formData[$fieldName] : [];
                                    @endphp
                                    
                                    <div class="mb-3">
                                        @if(count($currentImages) > 0)
                                            <div class="row g-2">
                                                @foreach($currentImages as $imageIndex => $imagePath)
                                                    <div class="col-6 col-sm-4 col-lg-3">
                                                        <div class="position-relative">
                                                            <div class="position-absolute top-0 end-0 p-1">
                                                                <button type="button" class="btn btn-danger btn-icon btn-sm"
                                                                        wire:click="removeExistingMultipleImage('{{ $fieldName }}', {{ $imageIndex }})"
                                                                        wire:confirm="Bu görseli silmek istediğinize emin misiniz?">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            </div>
                                                            
                                                            <a data-fslightbox="gallery-{{ $field['name'] }}" href="{{ cdn($imagePath) }}">
                                                                <div class="img-responsive img-responsive-1x1 rounded border" 
                                                                     style="background-image: url({{ cdn($imagePath) }})">
                                                                </div>
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-muted text-center p-3 mb-3 border rounded">
                                                <i class="fas fa-images me-2"></i> Henüz görsel eklenmemiş
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Yükleme Alanı -->
                                    <div class="card">
                                        <div class="card-body p-3">
                                            <form wire:submit="updatedTempPhoto">
                                                <div class="dropzone p-4" onclick="document.getElementById('file-upload-{{ $fieldName }}').click()">
                                                    <input type="file" id="file-upload-{{ $fieldName }}" class="d-none" 
                                                        wire:model="tempPhoto" accept="image/*" multiple
                                                        wire:click="setPhotoField('{{ $fieldName }}')">
                                                        
                                                    <div class="text-center">
                                                        <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                                        <h5 class="text-muted">Görselleri sürükleyip bırakın veya tıklayın</h5>
                                                        <p class="text-muted small">PNG, JPG, WEBP, GIF - Maks 3MB - <strong>Toplu seçim yapabilirsiniz</strong></p>
                                                    </div>
                                                </div>
                                            </form>

                                            <!-- Geçici yüklenen görseller -->
                                            @if(isset($photos[$fieldName]) && count($photos[$fieldName]) > 0)
                                                <div class="mt-3">
                                                    <h6 class="mb-2">Yüklemeye Hazır Görseller:</h6>
                                                    <div class="row g-2">
                                                        @foreach($photos[$fieldName] as $index => $photo)
                                                            <div class="col-6 col-sm-4 col-lg-3">
                                                                <div class="position-relative">
                                                                    <div class="position-absolute top-0 end-0 p-1">
                                                                        <button type="button" class="btn btn-danger btn-icon btn-sm"
                                                                                wire:click="removePhoto('{{ $fieldName }}', {{ $index }})">
                                                                            <i class="fas fa-times"></i>
                                                                        </button>
                                                                    </div>
                                                                    <div class="img-responsive img-responsive-1x1 rounded border" 
                                                                         style="background-image: url({{ $photo->temporaryUrl() }})">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                        
                        @if(!$hasMediaFields)
                            <div class="text-center text-muted p-4">
                                <i class="fas fa-info-circle fa-2x mb-2"></i>
                                <p>Bu içerik türü için görsel veya medya alanı tanımlanmamış.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-footer d-flex justify-content-between">
                <a href="{{ route('admin.widgetmanagement.items', $tenantWidgetId) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-1"></i> İptal
                </a>
                <div>
                    @if(!$itemId)
                        <button type="submit" class="btn" wire:click="save(false, true)" wire:loading.attr="disabled" wire:target="save">
                            <span wire:loading.remove wire:target="save(false, true)">
                                <i class="fas fa-plus me-1"></i> Kaydet ve Yeni Ekle
                            </span>
                            <span wire:loading wire:target="save(false, true)">
                                <i class="fas fa-spinner fa-spin me-1"></i> Kaydediliyor...
                            </span>
                        </button>
                    @else
                        <button type="submit" class="btn" wire:click="save(false, false)" wire:loading.attr="disabled" wire:target="save">
                            <span wire:loading.remove wire:target="save(false, false)">
                                <i class="fas fa-save me-1"></i> Kaydet ve Düzenlemeye Devam Et
                            </span>
                            <span wire:loading wire:target="save(false, false)">
                                <i class="fas fa-spinner fa-spin me-1"></i> Kaydediliyor...
                            </span>
                        </button>
                    @endif
                    
                    <button type="submit" class="btn btn-primary ms-2" wire:loading.attr="disabled" wire:target="save">
                        <span wire:loading.remove wire:target="save">
                            <i class="fas fa-save me-1"></i> Kaydet
                        </span>
                        <span wire:loading wire:target="save">
                            <i class="fas fa-spinner fa-spin me-1"></i> Kaydediliyor...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>