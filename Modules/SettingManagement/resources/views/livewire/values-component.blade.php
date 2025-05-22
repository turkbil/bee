@include('settingmanagement::helper')

<div class="card">
    <div class="card-header">
        <div class="d-flex w-100 align-items-center justify-content-between">
            <h3 class="card-title m-0">
                <i class="fas fa-cogs me-2"></i>
                {{ $group->name }} - Ayar Değerleri
            </h3>
        </div>
    </div>
    
    <div class="card-body">
        @if(isset($group->layout) && !empty($group->layout) && is_array($group->layout))

            @if(isset($group->layout['elements']) && is_array($group->layout['elements']))
                @foreach($group->layout['elements'] as $element)
                    @include('settingmanagement::form-builder.partials.form-elements.' . $element['type'], [
                        'element' => $element,
                        'values' => $values,
                        'settings' => $settings,
                        'temporaryImages' => $temporaryImages ?? [],
                        'temporaryMultipleImages' => $temporaryMultipleImages ?? [],
                        'multipleImagesArrays' => $multipleImagesArrays ?? [],
                        'originalValues' => $originalValues ?? []
                    ])
                @endforeach
            @else
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Form yapısı bulunamadı veya geçersiz. Lütfen Form Builder'ı kullanarak form yapısını düzenleyin.
                </div>
            @endif
        @else
            <div class="row g-3">
                <!-- Responsive form elemanları - mobilde tam genişlik, PC'de yarım genişlik -->
                @foreach($settings as $setting)
                <div class="col-12 col-md-6" wire:key="setting-{{ $setting->id }}">
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="d-flex align-items-center justify-content-between">
                                <h3 class="card-title d-flex align-items-center">
                                    <i class="fa-regular fa-comment fa-flip-horizontal me-2 text-primary"></i>
                                    {{ $setting->label }}
                                </h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                @switch($setting->type)
                                    @case('textarea')
                                        <textarea wire:model="values.{{ $setting->id }}" class="form-control" rows="3" 
                                            placeholder="Değeri buraya giriniz..."></textarea>
                                        @break
                                    
                                    @case('select')
                                        @if(is_array($setting->options))
                                            <select wire:model="values.{{ $setting->id }}" class="form-select">
                                                <option value="">Seçiniz</option>
                                                @foreach($setting->options as $key => $label)
                                                    <option value="{{ $key }}">{{ is_string($label) ? $label : json_encode($label) }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                        @break
                                    
                                    @case('checkbox')
                                        <div class="form-check form-switch">
                                            <input type="checkbox" id="value-{{ $setting->id }}" class="form-check-input" 
                                                wire:model="values.{{ $setting->id }}"
                                                @if(isset($values[$setting->id]) && $values[$setting->id] == 1) checked @endif>
                                            <label class="form-check-label" for="value-{{ $setting->id }}">
                                                {{ isset($values[$setting->id]) && $values[$setting->id] == 1 ? 'Evet' : 'Hayır' }}
                                            </label>
                                        </div>
                                        @break
                                    
                                    @case('file')
                                        <div class="form-group mb-3">
                                            @include('settingmanagement::form-builder.partials.file-upload', [
                                                'fileKey' => $setting->id,
                                                'label' => 'Dosyayı sürükleyip bırakın veya tıklayın',
                                                'values' => $values
                                            ])
                                        </div>
                                        @break
                                    
                                    @case('image')
                                        <div class="form-group mb-3">
                                            @include('settingmanagement::form-builder.partials.image-upload', [
                                                'imageKey' => $setting->id,
                                                'label' => 'Görseli sürükleyip bırakın veya tıklayın',
                                                'values' => $values
                                            ])
                                        </div>
                                        @break
                                        
                                    @case('image_multiple')
                                        <div class="form-group mb-3">
                                            <!-- Mevcut Çoklu Resimler -->
                                            @php
                                                $currentImages = isset($multipleImagesArrays[$setting->id]) ? $multipleImagesArrays[$setting->id] : [];
                                            @endphp
                                            
                                            @include('settingmanagement::form-builder.partials.existing-multiple-images', [
                                                'settingId' => $setting->id,
                                                'images' => $currentImages
                                            ])
                                            
                                            <!-- Yükleme Alanı -->
                                            <div class="card mt-3">
                                                <div class="card-body p-3">
                                                    <form wire:submit="updatedTempPhoto">
                                                        <div class="dropzone p-4" onclick="document.getElementById('file-upload-{{ $setting->id }}').click()">
                                                            <input type="file" id="file-upload-{{ $setting->id }}" class="d-none" 
                                                                wire:model="tempPhoto" accept="image/*" multiple
                                                                wire:click="setPhotoField('{{ $setting->id }}')">
                                                                
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
                                            @if(isset($temporaryMultipleImages[$setting->id]) && is_array($temporaryMultipleImages[$setting->id]) && count($temporaryMultipleImages[$setting->id]) > 0)
                                                <div class="mt-3">
                                                    <label class="form-label">Yeni Yüklenen Görseller</label>
                                                    <div class="row g-2">
                                                        @foreach($temporaryMultipleImages[$setting->id] as $index => $photo)
                                                            @if($photo)
                                                            <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                                                                <div class="position-relative">
                                                                    <div class="position-absolute top-0 end-0 p-1">
                                                                        <button type="button" class="btn btn-danger btn-icon btn-sm"
                                                                                wire:click="removeMultipleImageField({{ $setting->id }}, {{ $index }})">
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
                                        </div>
                                        @break

                                    @case('color')
                                        <div class="mb-3">
                                            <label class="form-label">Renk seçimi</label>
                                            <input type="color" class="form-control form-control-color" 
                                                value="{{ $values[$setting->id] ?? '#ffffff' }}" 
                                                wire:model="values.{{ $setting->id }}"
                                                title="Renk seçin">
                                        </div>
                                        @break
                                    
                                    @case('date')
                                        <div class="input-icon">
                                            <span class="input-icon-addon">
                                                <i class="fas fa-calendar"></i>
                                            </span>
                                            <input type="date" wire:model="values.{{ $setting->id }}" class="form-control">
                                        </div>
                                        @break
                                    
                                    @case('time')
                                        <div class="input-icon">
                                            <span class="input-icon-addon">
                                                <i class="fas fa-clock"></i>
                                            </span>
                                            <input type="time" wire:model="values.{{ $setting->id }}" class="form-control">
                                        </div>
                                        @break
                                    
                                    @case('number')
                                        <div class="input-icon">
                                            <span class="input-icon-addon">
                                                <i class="fas fa-hashtag"></i>
                                            </span>
                                            <input type="number" wire:model="values.{{ $setting->id }}" class="form-control">
                                        </div>
                                        @break
                                    
                                    @case('email')
                                        <div class="input-icon">
                                            <span class="input-icon-addon">
                                                <i class="fas fa-envelope"></i>
                                            </span>
                                            <input type="email" wire:model="values.{{ $setting->id }}" class="form-control">
                                        </div>
                                        @break
                                    
                                    @case('password')
                                        <div class="input-icon">
                                            <span class="input-icon-addon">
                                                <i class="fas fa-key"></i>
                                            </span>
                                            <input type="password" wire:model="values.{{ $setting->id }}" class="form-control">
                                        </div>
                                        @break
                                    
                                    @case('tel')
                                        <div class="input-icon">
                                            <span class="input-icon-addon">
                                                <i class="fas fa-phone"></i>
                                            </span>
                                            <input type="tel" wire:model="values.{{ $setting->id }}" class="form-control">
                                        </div>
                                        @break
                                    
                                    @case('url')
                                        <div class="input-icon">
                                            <span class="input-icon-addon">
                                                <i class="fas fa-globe"></i>
                                            </span>
                                            <input type="url" wire:model="values.{{ $setting->id }}" class="form-control">
                                        </div>
                                        @break
                                    
                                    @default
                                        <div class="input-icon">
                                            <span class="input-icon-addon">
                                                <i class="fas fa-font"></i>
                                            </span>
                                            <input type="text" wire:model="values.{{ $setting->id }}" class="form-control">
                                        </div>
                                @endswitch
                                
                                @if(isset($originalValues[$setting->id]) && $originalValues[$setting->id] != $values[$setting->id])
                                    <div class="mt-2 text-end">
                                        <span class="badge bg-yellow cursor-pointer" wire:click="resetToDefault({{ $setting->id }})">
                                            <i class="fas fa-undo me-1"></i> Varsayılana Döndür
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
        
        @if(count($changes) > 0)
            <div class="alert alert-success">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-info-circle me-2"></i>
                        {{ count($changes) }} adet değişiklik yapıldı. Lütfen değişiklikleri kaydedin.
                    </div>
                    <div>
                        <button type="button" class="btn btn-success" wire:click="save(false)">
                            <i class="fas fa-save me-2"></i> 
                            <span wire:loading.remove wire:target="save">Değişiklikleri Kaydet</span>
                            <span wire:loading wire:target="save">Kaydediliyor...</span>
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <a href="{{ route('admin.settingmanagement.index') }}" class="btn btn-link text-decoration-none">
            <i class="fas fa-arrow-left me-2"></i>
            Grup Listesine Dön
        </a>

        <div class="d-flex gap-2">
            <button type="button" class="btn" wire:click="save(false)" wire:loading.attr="disabled"
                wire:target="save">
                <span class="d-flex align-items-center">
                    <span class="ms-2" wire:loading.remove wire:target="save(false)">
                        <i class="fa-thin fa-plus me-2"></i> Kaydet ve Devam Et
                    </span>
                    <span class="ms-2" wire:loading wire:target="save(false)">
                        <i class="fa-duotone fa-solid fa-spinner fa-spin me-2"></i> Kaydet ve Devam Et
                    </span>
                </span>
            </button>

            <button type="button" class="btn btn-primary ms-4" wire:click="save(true)"
                wire:loading.attr="disabled" wire:target="save">
                <span class="d-flex align-items-center">
                    <span class="ms-2" wire:loading.remove wire:target="save(true)">
                        <i class="fa-thin fa-floppy-disk me-2"></i> Kaydet
                    </span>
                    <span class="ms-2" wire:loading wire:target="save(true)">
                        <i class="fa-duotone fa-solid fa-spinner fa-spin me-2"></i> Kaydet
                    </span>
                </span>
            </button>
        </div>
    </div>
</div>

@push('styles')
<style>
    .cursor-pointer {
        cursor: pointer;
    }
    
    .form-renderer {
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .form-container {
        padding: 1rem;
        border-radius: 0.25rem;
    }
    
    .form-title {
        font-weight: 600;
        border-bottom: 1px solid rgba(98, 105, 118, 0.16);
        padding-bottom: 0.75rem;
        margin-bottom: 1.5rem;
    }
</style>
@endpush