@include('widgetmanagement::helper')
<div class="card">
    <div class="card-body">
        <div class="row mb-3">
            <!-- Sol Taraf (Başlık ve Kontroller) -->
            <div class="col-md-8">
                <h3 class="card-title d-flex align-items-center mb-0">
                    <i class="fas fa-layer-group me-2"></i>
                    {{ $itemId ? 'İçerik Düzenle' : 'Yeni İçerik Ekle' }}
                </h3>
            </div>
            
            <!-- Ortadaki Loading -->
            <div class="col-md-2 position-relative d-flex justify-content-center align-items-center">
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
            
            <!-- Sağ Taraf (Geri Dön) -->
            <div class="col-md-2 text-md-end">
                <a href="{{ route('admin.widgetmanagement.items', $tenantWidgetId) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Geri Dön
                </a>
            </div>
        </div>
        
        <!-- Form İçeriği -->
        <div class="card">
            <div class="card-status-start bg-primary"></div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach($schema as $field)
                    @if($field['name'] !== 'unique_id')
                    <div class="col-12 mb-3">
                        <div class="form-group">
                            <label for="field-{{ $field['name'] }}" class="form-label{{ isset($field['required']) && $field['required'] ? ' required' : '' }}">
                                {{ $field['label'] }}
                                @if(isset($field['system']) && $field['system'] && $field['name'] != 'unique_id')
                                <span class="badge bg-primary ms-1">Sistem</span>
                                @endif
                            </label>
                            
                            @switch($field['type'])
                                @case('textarea')
                                    <div class="mb-3">
                                        <textarea wire:model="formData.{{ $field['name'] }}" class="form-control" rows="5" placeholder="Değeri buraya giriniz..."></textarea>
                                    </div>
                                    @break
                                
                                @case('select')
                                    @if(is_array($field['options']))
                                        <div class="mb-3">
                                            <select wire:model="formData.{{ $field['name'] }}" class="form-select">
                                                <option value="">Seçiniz</option>
                                                @foreach($field['options'] as $key => $label)
                                                    <option value="{{ $key }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif
                                    @break
                                
                                @case('file')
                                    <div class="form-group mb-3">
                                        @include('settingmanagement::livewire.partials.file-upload', [
                                            'fileKey' => $field['name'],
                                            'label' => 'Dosyayı sürükleyip bırakın veya tıklayın (Maks 3MB)',
                                            'values' => $formData
                                        ])
                                    </div>
                                    @break

                                @case('image')
                                    <div class="form-group mb-3">
                                        @include('settingmanagement::livewire.partials.image-upload', [
                                            'imageKey' => $field['name'],
                                            'label' => 'Görseli sürükleyip bırakın veya tıklayın (Maks 3MB)',
                                            'values' => $formData
                                        ])
                                    </div>
                                    @break
                                    
                                    @case('image_multiple')
                                    <div class="form-group mb-3">
                                        <!-- Mevcut Çoklu Resimler -->
                                        @php
                                            $fieldName = $field['name'];
                                            $currentImages = isset($formData[$fieldName]) && is_array($formData[$fieldName]) ? $formData[$fieldName] : [];
                                        @endphp
                                        
                                        <div class="mb-3">
                                            <label class="form-label">{{ $field['label'] }} - Yüklenen Görseller</label>
                                            <div class="row g-2">
                                                @foreach($currentImages as $imageIndex => $imagePath)
                                                    <div class="col-6 col-sm-4 col-md-3 col-xl-2">
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
                                                
                                                <!-- Geçici yüklenen görseller -->
                                                @if(isset($photos[$fieldName]) && count($photos[$fieldName]) > 0)
                                                    @foreach($photos[$fieldName] as $index => $photo)
                                                        <div class="col-6 col-sm-4 col-md-3 col-xl-2">
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
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <!-- Yükleme Alanı -->
                                        <div class="card mt-3">
                                            <div class="card-body p-3">
                                                <form wire:submit="updatedTempPhoto">
                                                    <div class="dropzone p-4" onclick="document.getElementById('file-upload-{{ $fieldName }}').click()">
                                                        <input type="file" id="file-upload-{{ $fieldName }}" class="d-none" 
                                                            wire:model="tempPhoto" accept="image/*" multiple
                                                            wire:click="setPhotoField('{{ $fieldName }}')">
                                                            
                                                        <div class="text-center">
                                                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                                            <h4 class="text-muted">Görselleri sürükleyip bırakın veya tıklayın</h4>
                                                            <p class="text-muted small">PNG, JPG, WEBP, GIF - Maks 3MB - <strong>Toplu seçim yapabilirsiniz</strong></p>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @break

                                @case('checkbox')
                                    <div class="form-check form-switch">
                                        <input type="checkbox" id="field-{{ $field['name'] }}" class="form-check-input" wire:model.live="formData.{{ $field['name'] }}">
                                        <label class="form-check-label" for="field-{{ $field['name'] }}">
                                            {{ isset($formData[$field['name']]) && $formData[$field['name']] ? 'Evet' : 'Hayır' }}
                                        </label>
                                    </div>
                                    @break
                                
                                @case('color')
                                    <div class="mb-3">
                                        <label class="form-label">Renk seçimi</label>
                                        <input type="color" class="form-control form-control-color" 
                                            value="{{ $formData[$field['name']] ?? '#ffffff' }}" 
                                            wire:model.live="formData.{{ $field['name'] }}"
                                            title="Renk seçin">
                                    </div>
                                    @break
                                
                                @case('date')
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <i class="fas fa-calendar"></i>
                                        </span>
                                        <input type="date" class="form-control" wire:model.live="formData.{{ $field['name'] }}">
                                    </div>
                                    @break
                                
                                @case('time')
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <i class="fas fa-clock"></i>
                                        </span>
                                        <input type="time" class="form-control" wire:model.live="formData.{{ $field['name'] }}">
                                    </div>
                                    @break
                                
                                @case('number')
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <i class="fas fa-hashtag"></i>
                                        </span>
                                        <input type="number" class="form-control" wire:model.live="formData.{{ $field['name'] }}">
                                    </div>
                                    @break
                                
                                @case('email')
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <i class="fas fa-envelope"></i>
                                        </span>
                                        <input type="email" class="form-control" wire:model.live="formData.{{ $field['name'] }}">
                                    </div>
                                    @break
                                
                                @case('password')
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <i class="fas fa-key"></i>
                                        </span>
                                        <input type="password" class="form-control" wire:model.live="formData.{{ $field['name'] }}">
                                    </div>
                                    @break
                                
                                @case('tel')
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <i class="fas fa-phone"></i>
                                        </span>
                                        <input type="tel" class="form-control" wire:model.live="formData.{{ $field['name'] }}">
                                    </div>
                                    @break
                                
                                @case('url')
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <i class="fas fa-globe"></i>
                                        </span>
                                        <input type="url" class="form-control" wire:model.live="formData.{{ $field['name'] }}">
                                    </div>
                                    @break
                                
                                @default
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <i class="fas fa-font"></i>
                                        </span>
                                        <input type="{{ $field['type'] }}" class="form-control" wire:model.live="formData.{{ $field['name'] }}">
                                    </div>
                            @endswitch
                            
                            @error('formData.' . $field['name'])
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
            <div class="card-footer d-flex justify-content-between">
                <a href="{{ route('admin.widgetmanagement.items', $tenantWidgetId) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-1"></i> İptal
                </a>
                <button type="button" class="btn btn-primary" wire:click="saveItem">
                    <div wire:loading.remove wire:target="saveItem">
                        <i class="fas fa-save me-1"></i> Kaydet
                    </div>
                    <div wire:loading wire:target="saveItem">
                        <i class="fas fa-spinner fa-spin me-1"></i> Kaydediliyor...
                    </div>
                </button>
            </div>
        </div>
    </div>
</div>