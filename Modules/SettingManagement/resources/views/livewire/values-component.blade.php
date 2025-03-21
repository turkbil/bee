@include('settingmanagement::helper')

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="card-title">
                <i class="fas fa-cogs me-2"></i>
                {{ $group->name }} - Ayar Değerleri
            </h3>
            <div>
                <a href="{{ route('admin.settingmanagement.items', $groupId) }}" class="btn btn-outline-primary">
                    <i class="fas fa-list me-2"></i> Ayarları Yönet
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <div class="d-flex">
                <div>
                    <i class="fas fa-info-circle me-2" style="margin-top: 3px"></i>
                </div>
                <div>
                    <h4 class="alert-title">Ayarları Toplu Düzenleme</h4>
                    <div class="text-muted">
                        Bu sayfa, <strong>{{ $group->name }}</strong> grubu için tüm ayarları tek bir sayfa üzerinden 
                        değiştirmenizi sağlar. Değer değişikliklerini kaydetmek için sayfanın altındaki 
                        "Kaydet" düğmesini kullanın.
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row g-3">
            @foreach($settings as $setting)
            <div class="col-md-6" wire:key="setting-{{ $setting->id }}">
                <div class="card mb-3">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h3 class="card-title d-flex align-items-center">
                                @switch($setting->type)
                                    @case('text')
                                        <i class="fas fa-font me-2 text-primary"></i>
                                        @break
                                    @case('textarea')
                                        <i class="fas fa-align-left me-2 text-primary"></i>
                                        @break
                                    @case('number')
                                        <i class="fas fa-hashtag me-2 text-primary"></i>
                                        @break
                                    @case('select')
                                        <i class="fas fa-list me-2 text-primary"></i>
                                        @break
                                    @case('checkbox')
                                        <i class="fas fa-check-square me-2 text-primary"></i>
                                        @break
                                    @case('file')
                                        <i class="fas fa-file me-2 text-primary"></i>
                                        @break
                                    @case('image')
                                        <i class="fas fa-image me-2 text-primary"></i>
                                        @break
                                    @case('color')
                                        <i class="fas fa-palette me-2 text-primary"></i>
                                        @break
                                    @case('date')
                                        <i class="fas fa-calendar me-2 text-primary"></i>
                                        @break
                                    @case('email')
                                        <i class="fas fa-envelope me-2 text-primary"></i>
                                        @break
                                    @case('password')
                                        <i class="fas fa-key me-2 text-primary"></i>
                                        @break
                                    @case('tel')
                                        <i class="fas fa-phone me-2 text-primary"></i>
                                        @break
                                    @case('url')
                                        <i class="fas fa-globe me-2 text-primary"></i>
                                        @break
                                    @case('time')
                                        <i class="fas fa-clock me-2 text-primary"></i>
                                        @break
                                    @default
                                        <i class="fas fa-cog me-2 text-primary"></i>
                                @endswitch
                                {{ $setting->label }}
                            </h3>
                            <div>
                                <span class="badge bg-blue-lt">{{ $setting->type }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <code>{{ $setting->key }}</code>
                        </div>
                        
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
                                                <option value="{{ $key }}">{{ $label }}</option>
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
                                
                                    @case('file')
                                    <div class="form-group mb-3">
                                        <div x-data="{ 
                                                isDropping: false,
                                                handleDrop(event) {
                                                    event.preventDefault();
                                                    if (event.dataTransfer.files.length > 0) {
                                                        @this.upload('temporaryImages.{{ $setting->id }}', event.dataTransfer.files[0]);
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
                                                            <div class="dropzone" onclick="document.getElementById('fileInput_{{ $setting->id }}').click()">
                                                                <div class="dropzone-files"></div>
                                                                <div class="d-flex flex-column align-items-center justify-content-center p-4">
                                                                    <i class="fa-solid fa-cloud-arrow-up fa-2x mb-2 text-muted"></i>
                                                                    <div class="text-muted">
                                                                        <span x-show="!isDropping">Dosyayı sürükleyip bırakın veya tıklayın</span>
                                                                        <span x-show="isDropping" class="text-primary">Bırakın!</span>
                                                                    </div>
                                                                </div>
                                                                <input type="file" id="fileInput_{{ $setting->id }}"
                                                                    wire:model="temporaryImages.{{ $setting->id }}" class="d-none"
                                                                    accept="*/*" />
                                                            </div>
                                
                                                            <!-- Progress Bar Alanı -->
                                                            <div class="progress-container" style="height: 10px;">
                                                                <div class="progress progress-sm mt-2" wire:loading
                                                                    wire:target="temporaryImages.{{ $setting->id }}">
                                                                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                                                        style="width: 100%"></div>
                                                                </div>
                                                            </div>
                                
                                                            @error('temporaryImages.' . $setting->id)
                                                            <div class="text-danger small mt-2">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-3">
                                                    <div class="card">
                                                        <div class="card-body p-3">
                                                            @if (isset($temporaryImages[$setting->id]))
                                                            <div class="position-relative" style="height: 100px;">
                                                                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2"
                                                                    wire:click="deleteMedia({{ $setting->id }})" wire:loading.attr="disabled">
                                                                    <i class="fa-solid fa-xmark"></i>
                                                                </button>
                                                                <div class="d-flex align-items-center justify-content-center h-100">
                                                                    <div class="text-center">
                                                                        <i class="fa-solid fa-file fa-3x text-muted"></i>
                                                                        <p class="mb-0 mt-2">{{ $temporaryImages[$setting->id]->getClientOriginalName() }}</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @elseif ($setting->getFirstMedia('files'))
                                                            <div class="position-relative" style="height: 100px;">
                                                                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2"
                                                                    wire:click="deleteMedia({{ $setting->id }})" wire:loading.attr="disabled">
                                                                    <i class="fa-solid fa-xmark"></i>
                                                                </button>
                                                                <div class="d-flex align-items-center justify-content-center h-100">
                                                                    <div class="text-center">
                                                                        <i class="fa-solid fa-file fa-3x text-muted"></i>
                                                                        <p class="mb-0 mt-2">{{ $setting->getFirstMedia('files')->file_name }}</p>
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
                                    </div>
                                    @break
                                
                                    @case('image')
                                    <div class="form-group mb-3">
                                        @include('settingmanagement::livewire.partials.image-upload', [
                                            'imageKey' => $setting->id,
                                            'label' => 'Görseli sürükleyip bırakın veya tıklayın',
                                            'setting' => $setting
                                        ])
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
                            
                            @if($originalValues[$setting->id] != $values[$setting->id])
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
        <a href="{{ route('admin.settingmanagement.items', $groupId) }}" class="btn btn-link text-decoration-none">
            <i class="fas fa-arrow-left me-2"></i>
            Geri Dön
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
</style>
@endpush