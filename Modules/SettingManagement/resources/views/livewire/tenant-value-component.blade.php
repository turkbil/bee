@include('settingmanagement::helper')
<div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-edit me-2"></i>
                {{ $setting->label }} Değerini Düzenle
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-8">
                    <!-- Değer Düzenleme -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Değer</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                @switch($setting->type)
                                    @case('textarea')
                                        <div class="mb-3">
                                            <textarea wire:model="value" class="form-control" rows="5" 
                                                {{ $useDefault ? 'disabled' : '' }}
                                                placeholder="Değeri buraya giriniz..."></textarea>
                                        </div>
                                        @break
                                    
                                    @case('select')
                                        @if(is_array($setting->options))
                                            <div class="mb-3">
                                                <select wire:model="value" class="form-select" {{ $useDefault ? 'disabled' : '' }}>
                                                    <option value="">Seçiniz</option>
                                                    @foreach($setting->options as $key => $label)
                                                        <option value="{{ $key }}">{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endif
                                        @break
                                    
                                    @case('file')
                                        <div class="form-group mb-3">
                                            @if($useDefault)
                                                <div class="alert alert-info">
                                                    <i class="fas fa-info-circle me-2"></i>
                                                    Varsayılan değer kullanılıyor.
                                                </div>
                                            @else
                                                <div x-data="{ 
                                                        isDropping: false,
                                                        handleDrop(event) {
                                                            event.preventDefault();
                                                            if (event.dataTransfer.files.length > 0) {
                                                                @this.upload('temporaryImages.file', event.dataTransfer.files[0]);
                                                            }
                                                            this.isDropping = false;
                                                        }
                                                    }" 
                                                    x-on:dragover.prevent="isDropping = true" 
                                                    x-on:dragleave.prevent="isDropping = false"
                                                    x-on:drop="handleDrop($event)">
                                                    <div class="row align-items-center g-3">
                                                        <div class="col-12 col-md-9">
                                                            <div class="card" :class="{ 'border-primary': isDropping }">
                                                                <div class="card-body">
                                                                    <div class="dropzone" onclick="document.getElementById('fileInput_file').click()">
                                                                        <div class="dropzone-files"></div>
                                                                        <div class="d-flex flex-column align-items-center justify-content-center p-4">
                                                                            <i class="fa-solid fa-cloud-arrow-up fa-2x mb-2 text-muted"></i>
                                                                            <div class="text-muted">
                                                                                <span x-show="!isDropping">Dosyayı sürükleyip bırakın veya tıklayın</span>
                                                                                <span x-show="isDropping" class="text-primary">Bırakın!</span>
                                                                            </div>
                                                                        </div>
                                                                        <input type="file" id="fileInput_file"
                                                                        wire:model="temporaryImages.file" class="d-none"
                                                                        accept="*/*" />
                                                                </div>

                                                                <!-- Progress Bar Alanı -->
                                                                <div class="progress-container" style="height: 10px;">
                                                                    <div class="progress progress-sm mt-2" wire:loading
                                                                        wire:target="temporaryImages.file">
                                                                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                                                            style="width: 100%"></div>
                                                                    </div>
                                                                </div>

                                                                @error('temporaryImages.file')
                                                                <div class="text-danger small mt-2">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-3">
                                                        <div class="card">
                                                            <div class="card-body p-3">
                                                                @if (isset($temporaryImages['file']))
                                                                <div class="position-relative" style="height: 100px;">
                                                                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2"
                                                                        wire:click="deleteFile" wire:loading.attr="disabled">
                                                                        <i class="fa-solid fa-xmark"></i>
                                                                    </button>
                                                                    <div class="d-flex align-items-center justify-content-center h-100">
                                                                        <div class="text-center">
                                                                            <i class="fa-solid fa-file fa-3x text-muted"></i>
                                                                            <p class="mb-0 mt-2">{{ $temporaryImages['file']->getClientOriginalName() }}</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @elseif ($previewing && $previewUrl)
                                                                <div class="position-relative" style="height: 100px;">
                                                                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2"
                                                                        wire:click="deleteFile" wire:loading.attr="disabled">
                                                                        <i class="fa-solid fa-xmark"></i>
                                                                    </button>
                                                                    <div class="d-flex align-items-center justify-content-center h-100">
                                                                        <div class="text-center">
                                                                            <i class="fa-solid fa-file fa-3x text-muted"></i>
                                                                            <p class="mb-0 mt-2">{{ basename($value) }}</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @else
                                                                <div class="d-flex align-items-center justify-content-center text-muted" style="height: 100px;">
                                                                    <i class="fa-solid fa-file-circle-xmark fa-2x"></i>
                                                                </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    @break

                                    @case('image')
                                    <div class="form-group mb-3">
                                        @if($useDefault)
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle me-2"></i>
                                                Varsayılan değer kullanılıyor.
                                            </div>
                                        @else
                                            @include('settingmanagement::livewire.partials.image-upload', [
                                                'imageKey' => 'image',
                                                'label' => 'Görseli sürükleyip bırakın veya tıklayın',
                                                'setting' => $setting
                                            ])
                                        @endif
                                    </div>
                                    @break

                                @case('checkbox')
                                    <div class="form-check form-switch">
                                        <input type="checkbox" id="value-check" class="form-check-input" 
                                            wire:model="checkboxValue" 
                                            {{ $useDefault ? 'disabled' : '' }}>
                                        <label class="form-check-label" for="value-check">
                                            {{ $checkboxValue ? 'Evet' : 'Hayır' }}
                                        </label>
                                    </div>
                                    @break
                                
                                @case('color')
                                    <div class="mb-3">
                                        <label class="form-label">Renk seçimi</label>
                                        <input type="color" class="form-control form-control-color" 
                                            value="{{ $colorValue }}" 
                                            wire:model="colorValue"
                                            {{ $useDefault ? 'disabled' : '' }}
                                            title="Renk seçin">
                                    </div>
                                    @break
                                
                                @case('date')
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <i class="fas fa-calendar"></i>
                                        </span>
                                        <input type="date" class="form-control" 
                                            wire:model="dateValue" 
                                            {{ $useDefault ? 'disabled' : '' }}>
                                    </div>
                                    @break
                                
                                @case('time')
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <i class="fas fa-clock"></i>
                                        </span>
                                        <input type="time" class="form-control" 
                                            wire:model="timeValue" 
                                            {{ $useDefault ? 'disabled' : '' }}>
                                    </div>
                                    @break
                                
                                @case('number')
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <i class="fas fa-hashtag"></i>
                                        </span>
                                        <input type="number" class="form-control" 
                                            wire:model="value" 
                                            {{ $useDefault ? 'disabled' : '' }}>
                                    </div>
                                    @break
                                
                                @case('email')
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <i class="fas fa-envelope"></i>
                                        </span>
                                        <input type="email" class="form-control" 
                                            wire:model="value" 
                                            {{ $useDefault ? 'disabled' : '' }}>
                                    </div>
                                    @break
                                
                                @case('password')
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <i class="fas fa-key"></i>
                                        </span>
                                        <input type="password" class="form-control" 
                                            wire:model="value" 
                                            {{ $useDefault ? 'disabled' : '' }}>
                                    </div>
                                    @break
                                
                                @case('tel')
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <i class="fas fa-phone"></i>
                                        </span>
                                        <input type="tel" class="form-control" 
                                            wire:model="value" 
                                            {{ $useDefault ? 'disabled' : '' }}>
                                    </div>
                                    @break
                                
                                @case('url')
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <i class="fas fa-globe"></i>
                                        </span>
                                        <input type="url" class="form-control" 
                                            wire:model="value" 
                                            {{ $useDefault ? 'disabled' : '' }}>
                                    </div>
                                    @break
                                
                                @default
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <i class="fas fa-font"></i>
                                        </span>
                                        <input type="{{ $setting->type }}" class="form-control" 
                                            wire:model="value" 
                                            {{ $useDefault ? 'disabled' : '' }}>
                                    </div>
                            @endswitch
                            
                            @if($useDefault)
                                <div class="alert alert-info mt-3">
                                    <div class="d-flex">
                                        <div>
                                            <i class="fas fa-info-circle me-2"></i>
                                        </div>
                                        <div>
                                            <h4 class="alert-title">Varsayılan değer kullanılıyor</h4>
                                            <div class="text-muted">
                                                Özel bir değer tanımlamak için sağdaki "Varsayılan Değer Kullan" 
                                                düğmesini kapatın.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <!-- Ayar Detayları -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Ayar Bilgileri</h3>
                    </div>
                    <div class="card-body">
                        <div class="datagrid">
                            <div class="datagrid-item">
                                <div class="datagrid-title">Anahtar</div>
                                <div class="datagrid-content"><code>{{ $setting->key }}</code></div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Tip</div>
                                <div class="datagrid-content">
                                    <span class="badge bg-blue-lt">
                                        @switch($setting->type)
                                            @case('text')
                                                <i class="fas fa-font me-1"></i> Metin
                                                @break
                                            @case('textarea')
                                                <i class="fas fa-align-left me-1"></i> Uzun Metin
                                                @break
                                            @case('number')
                                                <i class="fas fa-hashtag me-1"></i> Sayı
                                                @break
                                            @case('select')
                                                <i class="fas fa-list me-1"></i> Liste
                                                @break
                                            @case('checkbox')
                                                <i class="fas fa-check-square me-1"></i> Onay Kutusu
                                                @break
                                            @case('file')
                                                <i class="fas fa-file me-1"></i> Dosya
                                                @break
                                            @case('image')
                                                <i class="fas fa-image me-1"></i> Resim
                                                @break
                                            @case('color')
                                                <i class="fas fa-palette me-1"></i> Renk
                                                @break
                                            @case('date')
                                                <i class="fas fa-calendar me-1"></i> Tarih
                                                @break
                                            @case('email')
                                                <i class="fas fa-envelope me-1"></i> E-posta
                                                @break
                                            @case('password')
                                                <i class="fas fa-key me-1"></i> Şifre
                                                @break
                                            @case('tel')
                                                <i class="fas fa-phone me-1"></i> Telefon
                                                @break
                                            @case('url')
                                                <i class="fas fa-globe me-1"></i> URL
                                                @break
                                            @case('time')
                                                <i class="fas fa-clock me-1"></i> Saat
                                                @break
                                            @default
                                                <i class="fas fa-font me-1"></i> {{ $setting->type }}
                                        @endswitch
                                    </span>
                                </div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Grup</div>
                                <div class="datagrid-content">
                                    <span class="badge bg-primary-lt">
                                        {{ $setting->group->name }}
                                    </span>
                                </div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Durum</div>
                                <div class="datagrid-content">
                                    @if($setting->is_active)
                                    <span class="status status-green">
                                        <span class="status-dot status-dot-animated"></span>
                                        Aktif
                                    </span>
                                    @else
                                    <span class="status status-red">
                                        <span class="status-dot"></span>
                                        Pasif
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Varsayılan Değer</div>
                                <div class="datagrid-content">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="text-truncate" style="max-width: 150px;" title="{{ is_string($setting->default_value) ? $setting->default_value : '' }}">
                                            @if(empty($setting->default_value))
                                                <span class="text-muted fst-italic">Boş</span>
                                            @elseif($setting->type === 'file')
                                                <i class="fas fa-file me-1"></i> Dosya
                                            @elseif($setting->type === 'checkbox')
                                                {{ $setting->default_value ? 'Evet' : 'Hayır' }}
                                            @elseif($setting->type === 'color')
                                                <div class="d-flex align-items-center">
                                                    <span class="avatar avatar-xs me-2" style="background-color: {{ $setting->default_value }}"></span>
                                                    {{ $setting->default_value }}
                                                </div>
                                            @elseif($setting->type === 'password')
                                                <span class="text-muted">••••••••</span>
                                            @else
                                                {{ $setting->default_value }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <div class="form-label">Varsayılan Değer Kullan</div>
                            <label class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" wire:model.live="useDefault">
                                <span class="form-check-label">{{ $useDefault ? 'Evet' : 'Hayır' }}</span>
                            </label>
                            <div class="text-muted small">
                                Varsayılan değeri kullanmak için aktif edin.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <a href="{{ route('admin.settingmanagement.tenant.settings') }}" class="btn">
            <i class="fas fa-arrow-left me-2"></i> Geri
        </a>
        <div>
            @if($setting->type === 'file' && $value && !$useDefault && $previewUrl)
            <a href="{{ $previewUrl }}" target="_blank" class="btn btn-outline-primary me-2">
                <i class="fas fa-eye me-2"></i> Görüntüle
            </a>
            @endif
            <button type="button" class="btn btn-primary" wire:click="save">
                <span wire:loading.remove wire:target="save">
                    <i class="fas fa-save me-2"></i> Kaydet
                </span>
                <span wire:loading wire:target="save">
                    <i class="fas fa-spinner fa-spin me-2"></i> Kaydediliyor...
                </span>
            </button>
        </div>
    </div>
</div>