@include('settingmanagement::helper')
@include('admin.partials.error_message')

<div class="card">
    <div class="card-body">
        <form wire:submit="save">
            <div class="row mb-4">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-cogs me-2"></i>
                                Ayar Bilgileri
                            </h3>
                        </div>
                        <div class="card-body">
                            <!-- Grup Seçimi -->
                            <div class="mb-3">
                                <label class="form-label required">Grup</label>
                                <select wire:model.live="inputs.group_id"
                                    class="form-select @error('inputs.group_id') is-invalid @enderror">
                                    <option value="">Grup Seçin</option>
                                    @foreach($parentGroups as $parentGroup)
                                    <optgroup label="{{ $parentGroup->name }}">
                                        @foreach($groups->where('parent_id', $parentGroup->id) as $subGroup)
                                        <option value="{{ $subGroup->id }}">{{ $subGroup->name }}</option>
                                        @endforeach
                                    </optgroup>
                                    @endforeach
                                </select>
                                @error('inputs.group_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label required">Başlık</label>
                                    <input type="text" wire:model.live="inputs.label"
                                        class="form-control @error('inputs.label') is-invalid @enderror"
                                        placeholder="Başlık">
                                    @error('inputs.label')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required">Anahtar</label>
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <i class="fas fa-key"></i>
                                        </span>
                                        <input type="text" wire:model="inputs.key"
                                            class="form-control @error('inputs.key') is-invalid @enderror"
                                            placeholder="sistem_icin_benzersiz_anahtar">
                                    </div>
                                    <small class="form-hint">Harf, rakam ve alt çizgi (_) kullanabilirsiniz.</small>
                                    @error('inputs.key')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label required">Tip</label>
                                <select wire:model.live="inputs.type"
                                    class="form-select @error('inputs.type') is-invalid @enderror">
                                    @foreach($availableTypes as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('inputs.type')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            @if($inputs['type'] === 'select')
                            <div class="mb-3">
                                <label class="form-label required">Seçenekler</label>
                                
                                <div class="card">
                                    <div class="card-body p-3">
                                        <div class="mb-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary" wire:click="addSelectOption">
                                                <i class="fas fa-plus me-2"></i> Seçenek Ekle
                                            </button>
                                        </div>
                                        
                                        @if(isset($inputs['options_array']) && is_array($inputs['options_array']) && count($inputs['options_array']) > 0)
                                            @foreach($inputs['options_array'] as $key => $value)
                                            <div class="row g-2 mb-2" wire:key="option-{{ $key }}">
                                                <div class="col-5">
                                                    <div class="input-group input-group-flat">
                                                        <span class="input-group-text bg-light">
                                                            <i class="fas fa-key text-muted"></i>
                                                        </span>
                                                        <input type="text" class="form-control" 
                                                            wire:model.defer="inputs.options_array.{{ $key }}" 
                                                            placeholder="Anahtar">
                                                    </div>
                                                </div>
                                                <div class="col-5">
                                                    <div class="input-group input-group-flat">
                                                        <span class="input-group-text bg-light">
                                                            <i class="fas fa-font text-muted"></i>
                                                        </span>
                                                        <input type="text" class="form-control"
                                                            wire:model.defer="inputs.options_array.{{ $key }}"
                                                            placeholder="Değer">
                                                    </div>
                                                </div>
                                                <div class="col-2">
                                                    <button type="button" class="btn btn-outline-danger"
                                                        wire:click="removeSelectOption('{{ $key }}')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            @endforeach
                                        @else
                                            <div class="text-muted text-center py-3">
                                                <i class="fas fa-info-circle me-2"></i>
                                                Henüz seçenek eklenmemiş
                                            </div>
                                        @endif
                                        
                                        <div class="mt-2 text-center text-muted">
                                            <small>veya her satıra bir seçenek olacak şekilde aşağıdaki alana yazın</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <textarea wire:model="inputs.options"
                                    class="form-control mt-2 @error('inputs.options') is-invalid @enderror" rows="4"
                                    placeholder="erkek=Erkek
kadin=Kadın
diger=Diğer

veya sadece:
Erkek
Kadın
Diğer"></textarea>
                                <small class="form-hint">
                                    Her satıra bir seçenek. Örnek: erkek=Erkek veya sadece Erkek
                                    yazabilirsiniz.</small>
                                @error('inputs.options')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div> 
                            @endif

                            <div class="mb-3">
                                <label class="form-label">Varsayılan Değer</label>
                                
                                @if($inputs['type'] === 'textarea')
                                <textarea wire:model="inputs.default_value"
                                    class="form-control @error('inputs.default_value') is-invalid @enderror"
                                    rows="4"></textarea>
                                
                                @elseif($inputs['type'] === 'file')
                                <div class="mb-3">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-control position-relative" 
                                                onclick="document.getElementById('file-upload').click()"
                                                style="height: auto; min-height: 100px; cursor: pointer; border: 2px dashed #ccc;">
                                                <input type="file" id="file-upload" wire:model="tempFile" class="d-none">
                                                <div class="text-center py-3">
                                                    <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-2"></i>
                                                    <p class="mb-0">Dosyayı sürükleyin veya seçmek için tıklayın</p>
                                                    <p class="text-muted small mb-0">Maksimum boyut: 2MB</p>
                                                </div>
                                            </div>
                                            <div wire:loading wire:target="tempFile">
                                                <div class="progress progress-sm mt-2">
                                                    <div class="progress-bar progress-bar-indeterminate bg-primary"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card">
                                                <div class="card-body p-2 text-center">
                                                @if($inputs['default_value'])
                                                    @if(Str::of($inputs['default_value'])->lower()->endsWith(['jpg', 'jpeg', 'png', 'gif']))
                                                        <img src="{{ Storage::url($inputs['default_value']) }}" alt="Current file"
                                                            class="img-fluid rounded" style="max-height: 80px">
                                                        <div class="mt-2 text-muted small">{{ basename($inputs['default_value']) }}</div>
                                                    @else
                                                        <div class="d-flex flex-column align-items-center">
                                                            <i class="fas fa-file fa-3x text-primary"></i>
                                                            <span class="mt-2 text-wrap small">{{ basename($inputs['default_value']) }}</span>
                                                        </div>
                                                    @endif
                                                @else
                                                    <div class="d-flex flex-column align-items-center justify-content-center" style="height: 80px">
                                                        <i class="far fa-file text-muted"></i>
                                                        <span class="mt-2 small text-muted">Dosya yok</span>
                                                    </div>
                                                @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @elseif($inputs['type'] === 'image')
                                <div class="mb-3">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-control position-relative" 
                                                onclick="document.getElementById('image-upload').click()"
                                                style="height: auto; min-height: 120px; cursor: pointer; border: 2px dashed #ccc;">
                                                <input type="file" id="image-upload" wire:model="tempImage" class="d-none" accept="image/*">
                                                <div class="text-center py-3">
                                                    <i class="fas fa-image fa-3x text-primary mb-2"></i>
                                                    <p class="mb-0">Resmi sürükleyin veya seçmek için tıklayın</p>
                                                    <p class="text-muted small mb-0">Desteklenen formatlar: JPG, JPEG, PNG, WEBP, GIF (Maksimum boyut: 2MB)</p>
                                                </div>
                                            </div>
                                            <div wire:loading wire:target="tempImage">
                                                <div class="progress progress-sm mt-2">
                                                    <div class="progress-bar progress-bar-indeterminate bg-primary"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card">
                                                <div class="card-body p-2 text-center">
                                                @if($imagePreview)
                                                    <img src="{{ $imagePreview }}" alt="Önizleme"
                                                        class="img-fluid rounded" style="max-height: 100px">
                                                    <div class="mt-2 text-muted small">Yeni Resim</div>
                                                @elseif($inputs['default_value'])
                                                    <img src="{{ Storage::url($inputs['default_value']) }}" alt="Mevcut resim"
                                                        class="img-fluid rounded" style="max-height: 100px">
                                                    <div class="mt-2 text-muted small">Mevcut Resim</div>
                                                @else
                                                    <div class="d-flex flex-column align-items-center justify-content-center" style="height: 100px">
                                                        <i class="far fa-image text-muted"></i>
                                                        <span class="mt-2 small text-muted">Resim yok</span>
                                                    </div>
                                                @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @elseif($inputs['type'] === 'checkbox')
                                <div class="form-check form-switch ps-0">
                                    <input type="checkbox" id="default-value-switch" class="form-check-input ms-auto" 
                                        wire:model="inputs.default_value">
                                    <label class="form-check-label" for="default-value-switch">
                                        {{ $inputs['default_value'] ? 'Evet' : 'Hayır' }}
                                    </label>
                                </div>
                                
                                @elseif($inputs['type'] === 'color')
                                <div class="row g-2 align-items-center">
                                    <div class="col-auto">
                                        <input type="color" wire:model="inputs.default_value"
                                            class="form-control form-control-color" title="Renk seçin">
                                    </div>
                                    <div class="col-auto">
                                        <span class="form-colorinput" style="--tblr-badge-color: {{ $inputs['default_value'] ?? '#ffffff' }}">
                                            <span class="form-colorinput-color bg-{{ $inputs['default_value'] ?? '#ffffff' }}"></span>
                                        </span>
                                    </div>
                                    <div class="col">
                                        <span class="text-muted">{{ $inputs['default_value'] ?? '#ffffff' }}</span>
                                    </div>
                                </div>
                                
                                @elseif($inputs['type'] === 'date')
                                <div class="input-icon">
                                    <span class="input-icon-addon">
                                        <i class="fas fa-calendar"></i>
                                    </span>
                                    <input type="date" wire:model="inputs.default_value"
                                        class="form-control @error('inputs.default_value') is-invalid @enderror">
                                </div>
                                
                                @elseif($inputs['type'] === 'time')
                                <div class="input-icon">
                                    <span class="input-icon-addon">
                                        <i class="fas fa-clock"></i>
                                    </span>
                                    <input type="time" wire:model="inputs.default_value"
                                        class="form-control @error('inputs.default_value') is-invalid @enderror">
                                </div>
                                
                                @elseif($inputs['type'] === 'select' && !empty($inputs['options']))
                                <select wire:model="inputs.default_value"
                                    class="form-select @error('inputs.default_value') is-invalid @enderror">
                                    <option value="">Seçiniz</option>
                                    @foreach((array)$inputs['options'] as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                                
                                @elseif($inputs['type'] === 'number')
                                <div class="input-icon">
                                    <span class="input-icon-addon">
                                        <i class="fas fa-hashtag"></i>
                                    </span>
                                    <input type="number" wire:model="inputs.default_value"
                                        class="form-control @error('inputs.default_value') is-invalid @enderror">
                                </div>
                                
                                @elseif($inputs['type'] === 'email')
                                <div class="input-icon">
                                    <span class="input-icon-addon">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email" wire:model="inputs.default_value"
                                        class="form-control @error('inputs.default_value') is-invalid @enderror">
                                </div>
                                
                                @elseif($inputs['type'] === 'password')
                                <div class="input-icon">
                                    <span class="input-icon-addon">
                                        <i class="fas fa-key"></i>
                                    </span>
                                    <input type="password" wire:model="inputs.default_value"
                                        class="form-control @error('inputs.default_value') is-invalid @enderror">
                                </div>
                                
                                @elseif($inputs['type'] === 'tel')
                                <div class="input-icon">
                                    <span class="input-icon-addon">
                                        <i class="fas fa-phone"></i>
                                    </span>
                                    <input type="tel" wire:model="inputs.default_value"
                                        class="form-control @error('inputs.default_value') is-invalid @enderror">
                                </div>
                                
                                @elseif($inputs['type'] === 'url')
                                <div class="input-icon">
                                    <span class="input-icon-addon">
                                        <i class="fas fa-globe"></i>
                                    </span>
                                    <input type="url" wire:model="inputs.default_value"
                                        class="form-control @error('inputs.default_value') is-invalid @enderror">
                                </div>
                                
                                @else
                                <div class="input-icon">
                                    <span class="input-icon-addon">
                                        <i class="fas fa-font"></i>
                                    </span>
                                    <input type="text" wire:model="inputs.default_value"
                                        class="form-control @error('inputs.default_value') is-invalid @enderror">
                                </div>
                                @endif
                                
                                @error('inputs.default_value')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-cog me-2"></i>
                                Ayarlar
                            </h3>
                        </div>
                        <div class="card-body">
                            <!-- Aktiflik Durumu -->
                            <div class="mb-3">
                                <div class="form-label">Durum</div>
                                <div class="form-check form-switch">
                                    <input type="checkbox" id="is_active" class="form-check-input" 
                                        wire:model="inputs.is_active" value="1">
                                    <label class="form-check-label" for="is_active">
                                        {{ $inputs['is_active'] ? 'Aktif' : 'Pasif' }}
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-label">Sıralama</div>
                                <input type="number" wire:model="inputs.sort_order" class="form-control" min="0" step="1">
                            </div>
                            
                            <!-- Input Type Preview -->
                            <div class="mb-3">
                                <div class="form-label">Önizleme</div>
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">{{ $inputs['label'] ?: 'Örnek Etiket' }}</h3>
                                    </div>
                                    <div class="card-body">
                                    @switch($inputs['type'])
                                        @case('text')
                                            <input type="text" class="form-control" value="{{ $inputs['default_value'] }}" readonly placeholder="Metin">
                                            @break
                                        @case('textarea')
                                            <textarea class="form-control" rows="3" readonly>{{ $inputs['default_value'] }}</textarea>
                                            @break
                                        @case('number')
                                            <input type="number" class="form-control" value="{{ $inputs['default_value'] }}" readonly>
                                            @break
                                        @case('email')
                                            <input type="email" class="form-control" value="{{ $inputs['default_value'] }}" readonly placeholder="E-posta">
                                            @break
                                        @case('password')
                                            <input type="password" class="form-control" value="{{ $inputs['default_value'] }}" readonly placeholder="●●●●●●">
                                            @break
                                        @case('tel')
                                            <input type="tel" class="form-control" value="{{ $inputs['default_value'] }}" readonly placeholder="Telefon">
                                            @break
                                        @case('url')
                                            <input type="url" class="form-control" value="{{ $inputs['default_value'] }}" readonly placeholder="URL">
                                            @break
                                        @case('color')
                                            <div class="d-flex gap-2 align-items-center">
                                                <input type="color" class="form-control form-control-color" value="{{ $inputs['default_value'] ?? '#ffffff' }}" readonly>
                                                <span>{{ $inputs['default_value'] ?? '#ffffff' }}</span>
                                            </div>
                                            @break
                                        @case('date')
                                            <div class="input-icon">
                                                <span class="input-icon-addon">
                                                    <i class="fas fa-calendar"></i>
                                                </span>
                                                <input type="text" class="form-control" value="{{ $inputs['default_value'] }}" readonly placeholder="Tarih">
                                            </div>
                                            @break
                                        @case('time')
                                            <div class="input-icon">
                                                <span class="input-icon-addon">
                                                    <i class="fas fa-clock"></i>
                                                </span>
                                                <input type="text" class="form-control" value="{{ $inputs['default_value'] }}" readonly placeholder="Saat">
                                            </div>
                                            @break
                                        @case('checkbox')
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input" disabled @checked($inputs['default_value'])>
                                                <label class="form-check-label">{{ $inputs['default_value'] ? 'Evet' : 'Hayır' }}</label>
                                            </div>
                                            @break
                                        @case('select')
                                            <select class="form-select" disabled>
                                                <option>Seçim Kutusu</option>
                                                @if(is_array($inputs['options']))
                                                    @foreach($inputs['options'] as $key => $value)
                                                        <option value="{{ $key }}" @selected($key === $inputs['default_value'])>{{ $value }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            @break
                                        @case('file')
                                            <div class="border rounded p-3 text-center">
                                                @if($inputs['default_value'])
                                                    @if(Str::of($inputs['default_value'])->lower()->endsWith(['jpg', 'jpeg', 'png', 'gif']))
                                                        <img src="{{ Storage::url($inputs['default_value']) }}" class="img-fluid" style="max-height: 100px">
                                                    @else
                                                        <div class="d-flex align-items-center">
                                                            <i class="fas fa-file me-2"></i>
                                                            <span>{{ basename($inputs['default_value']) }}</span>
                                                        </div>
                                                    @endif
                                                @else
                                                    <i class="fas fa-upload fa-2x text-muted"></i>
                                                    <div class="mt-2 text-muted">Dosya Seçilmedi</div>
                                                @endif
                                            </div>
                                            @break
                                        @case('image')
                                            <div class="border rounded p-3 text-center">
                                                @if($imagePreview)
                                                    <img src="{{ $imagePreview }}" class="img-fluid rounded" style="max-height: 100px">
                                                @elseif($inputs['default_value'])
                                                    <img src="{{ Storage::url($inputs['default_value']) }}" class="img-fluid rounded" style="max-height: 100px">
                                                @else
                                                    <i class="fas fa-image fa-2x text-muted"></i>
                                                    <div class="mt-2 text-muted">Resim Seçilmedi</div>
                                                @endif
                                            </div>
                                            @break
                                        @default
                                            <input type="text" class="form-control" value="{{ $inputs['default_value'] }}" readonly>
                                    @endswitch
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="card-footer d-flex justify-content-between align-items-center">
                <a href="{{ url()->previous() }}" class="btn btn-link text-decoration-none">
                    <i class="fas fa-arrow-left me-2"></i>İptal
                </a>

                <div class="d-flex gap-2">
                    @if($settingId)
                    <button type="button" class="btn" wire:click="save(false, false)" wire:loading.attr="disabled"
                        wire:target="save">
                        <span class="d-flex align-items-center">
                            <span class="ms-2" wire:loading.remove wire:target="save(false, false)">
                                <i class="fa-thin fa-plus me-2"></i> Kaydet ve Devam Et
                            </span>
                            <span class="ms-2" wire:loading wire:target="save(false, false)">
                                <i class="fa-duotone fa-solid fa-spinner fa-spin me-2"></i> Kaydet ve Devam Et
                            </span>
                        </span>
                    </button>
                    @else
                    <button type="button" class="btn" wire:click="save(false, true)" wire:loading.attr="disabled"
                        wire:target="save">
                        <span class="d-flex align-items-center">
                            <span class="ms-2" wire:loading.remove wire:target="save(false, true)">
                                <i class="fa-thin fa-plus me-2"></i> Kaydet ve Yeni Ekle
                            </span>
                            <span class="ms-2" wire:loading wire:target="save(false, true)">
                                <i class="fa-duotone fa-solid fa-spinner fa-spin me-2"></i> Kaydet ve Yeni Ekle
                            </span>
                        </span>
                    </button>
                    @endif

                    <button type="button" class="btn btn-primary ms-4" wire:click="save(true, false)"
                        wire:loading.attr="disabled" wire:target="save">
                        <span class="d-flex align-items-center">
                            <span class="ms-2" wire:loading.remove wire:target="save(true, false)">
                                <i class="fa-thin fa-floppy-disk me-2"></i> Kaydet
                            </span>
                            <span class="ms-2" wire:loading wire:target="save(true, false)">
                                <i class="fa-duotone fa-solid fa-spinner fa-spin me-2"></i> Kaydet
                            </span>
                        </span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
    .form-label.required:after {
        content: " *";
        color: red;
    }
    
    /* Sürükle-bırak dosya alanı stillemesi */
    .file-drop-area {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        border: 2px dashed #ccc;
        border-radius: 6px;
        background-color: #f8f9fa;
        transition: 0.2s;
    }
    
    .file-drop-area:hover,
    .file-drop-area.is-active {
        background-color: #eef2f7;
        border-color: #adb5bd;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', function() {
        // Dosya sürükle-bırak işlemleri
        const fileDropArea = document.querySelector('.file-drop-area');
        if (fileDropArea) {
            const fileInput = document.getElementById('file-upload');
            
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                fileDropArea.addEventListener(eventName, preventDefaults, false);
            });
            
            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            ['dragenter', 'dragover'].forEach(eventName => {
                fileDropArea.addEventListener(eventName, highlight, false);
            });
            
            ['dragleave', 'drop'].forEach(eventName => {
                fileDropArea.addEventListener(eventName, unhighlight, false);
            });
            
            function highlight() {
                fileDropArea.classList.add('is-active');
            }
            
            function unhighlight() {
                fileDropArea.classList.remove('is-active');
            }
            
            fileDropArea.addEventListener('drop', handleDrop, false);
            
            function handleDrop(e) {
                if (e.dataTransfer.files.length) {
                    fileInput.files = e.dataTransfer.files;
                    const event = new Event('change', { bubbles: true });
                    fileInput.dispatchEvent(event);
                }
            }
        }
    });
</script>
@endpush