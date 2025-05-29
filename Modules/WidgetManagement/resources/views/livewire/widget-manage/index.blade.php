@include('widgetmanagement::helper')
<div>
    @include('admin.partials.error_message')
    
    @if(!$isNewWidget)
    <div class="row mb-4" wire:loading.remove wire:target="setMode">
        <div class="col-12">
            <div class="card">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <h3 class="mb-0 me-4">{{ $widget['name'] }}</h3>
                            <span class="badge fs-6 px-3 py-2">{{ ucfirst($widget['type']) }}</span>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" 
                                class="btn {{ $currentMode === 'basic' ? 'btn-primary' : 'btn-outline-primary' }}" 
                                wire:click="setMode('basic')">
                                <i class="fas fa-info-circle me-2"></i>
                                Temel Bilgiler
                            </button>
                            @if($widget['type'] !== 'file' && $widget['type'] !== 'module')
                            <a href="{{ route('admin.widgetmanagement.code-editor', $widgetId) }}" 
                               class="btn btn-outline-secondary">
                                <i class="fas fa-code me-2"></i>
                                Kod Editörü
                            </a>
                            @endif
                            <a href="{{ route('admin.widgetmanagement.preview.template', $widgetId) }}" 
                               class="btn btn-outline-info" target="_blank">
                                <i class="fas fa-eye me-2"></i>
                                Önizleme
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row" wire:loading wire:target="setMode">
        <div class="col-12">
            <div class="progress mb-4">
                <div class="progress-bar progress-bar-indeterminate"></div>
            </div>
        </div>
    </div>
    @endif
    
    <form wire:submit.prevent="save(true)">
        <div class="row g-4">
            <div class="col-12 col-lg-8 order-2 order-lg-1">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-{{ $isNewWidget ? 'plus' : 'edit' }} me-2"></i>
                            {{ $isNewWidget ? 'Yeni Widget Ekle' : 'Temel Bilgiler' }}
                        </h3>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-12">
                                <div class="row g-3">
                                    <div class="col-12 col-md-6">
                                        <label class="form-label required">Widget Adı</label>
                                        <input type="text" id="widget-name" wire:model="widget.name" 
                                            class="form-control @error('widget.name') is-invalid @enderror"
                                            placeholder="Widget adını giriniz">
                                        @error('widget.name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    
                                    <div class="col-12 col-md-6">
                                        <label class="form-label required">Benzersiz Tanımlayıcı</label>
                                        <input type="text" id="widget-slug" wire:model="widget.slug" 
                                            class="form-control font-monospace @error('widget.slug') is-invalid @enderror"
                                            placeholder="widget-slug">
                                        @error('widget.slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        <small class="form-hint">Sadece küçük harfler, rakamlar ve tire (-) kullanın.</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label">Kategori</label>
                                <select wire:model="widget.widget_category_id" class="form-select @error('widget.widget_category_id') is-invalid @enderror">
                                    <option value="">Kategori Seçiniz</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->widget_category_id }}">{{ $category->title }}</option>
                                    @endforeach
                                </select>
                                @error('widget.widget_category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Açıklama</label>
                                <textarea wire:model="widget.description" 
                                    class="form-control @error('widget.description') is-invalid @enderror" 
                                    placeholder="Widget açıklaması"
                                    rows="4"></textarea>
                                @error('widget.description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label required mb-3">Widget Tipi</label>
                                <div class="row g-3">
                                    <div class="col-6 col-lg-3">
                                        <label class="form-selectgroup-item h-100">
                                            <input type="radio" name="widget-type" value="static" wire:model.live="widget.type" class="form-selectgroup-input">
                                            <span class="form-selectgroup-label d-flex flex-column align-items-center p-3 h-100">
                                                <div class="avatar avatar-lg bg-blue text-white mb-2">
                                                    <i class="fas fa-file-alt"></i>
                                                </div>
                                                <span class="form-selectgroup-title fw-semibold mb-1">Statik</span>
                                                <span class="text-muted small text-center">Sabit içerik widget'ı</span>
                                            </span>
                                        </label>
                                    </div>
                                    <div class="col-6 col-lg-3">
                                        <label class="form-selectgroup-item h-100">
                                            <input type="radio" name="widget-type" value="dynamic" wire:model.live="widget.type" class="form-selectgroup-input">
                                            <span class="form-selectgroup-label d-flex flex-column align-items-center p-3 h-100">
                                                <div class="avatar avatar-lg bg-green text-white mb-2">
                                                    <i class="fas fa-layer-group"></i>
                                                </div>
                                                <span class="form-selectgroup-title fw-semibold mb-1">Dinamik</span>
                                                <span class="text-muted small text-center">Eklenebilir içerik</span>
                                            </span>
                                        </label>
                                    </div>
                                    <div class="col-6 col-lg-3">
                                        <label class="form-selectgroup-item h-100">
                                            <input type="radio" name="widget-type" value="module" wire:model.live="widget.type" class="form-selectgroup-input">
                                            <span class="form-selectgroup-label d-flex flex-column align-items-center p-3 h-100">
                                                <div class="avatar avatar-lg bg-purple text-white mb-2">
                                                    <i class="fas fa-cubes"></i>
                                                </div>
                                                <span class="form-selectgroup-title fw-semibold mb-1">Modül</span>
                                                <span class="text-muted small text-center">Özel modül widget'ı</span>
                                            </span>
                                        </label>
                                    </div>
                                    <div class="col-6 col-lg-3">
                                        <label class="form-selectgroup-item h-100">
                                            <input type="radio" name="widget-type" value="file" wire:model.live="widget.type" class="form-selectgroup-input">
                                            <span class="form-selectgroup-label d-flex flex-column align-items-center p-3 h-100">
                                                <div class="avatar avatar-lg bg-orange text-white mb-2">
                                                    <i class="fas fa-file-code"></i>
                                                </div>
                                                <span class="form-selectgroup-title fw-semibold mb-1">Dosya</span>
                                                <span class="text-muted small text-center">Hazır view dosyası</span>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                                @error('widget.type') <div class="invalid-feedback d-block mt-2">{{ $message }}</div> @enderror
                            </div>

                            @if($widget['type'] === 'module')
                            <div class="col-12">
                                @if($this->hasAvailableModuleFiles())
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Modül widget'ı için view dosyasını seçin.
                                </div>
                                
                                <label class="form-label required">Modül View Dosyası</label>
                                
                                @if($widget['file_path'])
                                <div class="mb-3">
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="{{ ucwords(str_replace(['-', '_', '/'], ' ', str_replace(['modules/', '/view'], '', $widget['file_path']))) }}" readonly>
                                        <button type="button" class="btn btn-outline-danger" wire:click="$set('widget.file_path', '')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <small class="form-hint">Seçimi değiştirmek için X butonuna tıklayın</small>
                                </div>
                                @else
                                <select wire:model="widget.file_path" class="form-select @error('widget.file_path') is-invalid @enderror">
                                    <option value="">Dosya Seçiniz</option>
                                    @foreach($this->getModuleFiles() as $path => $name)
                                    <option value="{{ $path }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('widget.file_path') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                @endif
                                @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Tüm modül dosyaları zaten tanımlanmış. Yeni modül widget'ı oluşturulamaz.
                                </div>
                                
                                <label class="form-label required">Modül View Dosyası</label>
                                <select disabled class="form-select">
                                    <option>Uygun dosya bulunamadı</option>
                                </select>
                                @endif
                            </div>
                            @endif

                            @if($widget['type'] === 'file')
                            <div class="col-12">
                                @if($this->hasAvailableViewFiles())
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Hazır view dosyasını seçin.
                                </div>
                                
                                <label class="form-label required">View Dosyası</label>
                                
                                @if($widget['file_path'])
                                <div class="mb-3">
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="{{ ucwords(str_replace(['-', '_', '/'], ' ', str_replace('/view', '', $widget['file_path']))) }}" readonly>
                                        <button type="button" class="btn btn-outline-danger" wire:click="$set('widget.file_path', '')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <small class="form-hint">Seçimi değiştirmek için X butonuna tıklayın</small>
                                </div>
                                @else
                                <select wire:model="widget.file_path" class="form-select @error('widget.file_path') is-invalid @enderror">
                                    <option value="">Dosya Seçiniz</option>
                                    @foreach($this->getViewFiles() as $path => $name)
                                    <option value="{{ $path }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('widget.file_path') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                @endif
                                @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Tüm view dosyaları zaten tanımlanmış. Yeni file widget'ı oluşturulamaz.
                                </div>
                                
                                <label class="form-label required">View Dosyası</label>
                                <select disabled class="form-select">
                                    <option>Uygun dosya bulunamadı</option>
                                </select>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-12 col-lg-4 order-1 order-lg-2">
                <div class="row g-4">
                    @if(!$isNewWidget)
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Widget Ayarları</h3>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" wire:model.live="widget.has_items">
                                        <span class="form-check-label">İçerik Ekleme Özelliği</span>
                                    </label>
                                    <small class="form-hint ms-4">Kullanıcılar widgeta içerik ekleyebilirler</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" wire:model="widget.is_core">
                                        <span class="form-check-label">Sistem Widget'ı</span>
                                    </label>
                                    <small class="form-hint ms-4">Sistem widget'ları tenant'lar tarafından silinemez</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" wire:model="widget.is_active">
                                        <span class="form-check-label">Widget Aktif</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Önizleme Görseli</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-control position-relative" 
                                    onclick="document.getElementById('thumbnail-upload-existing').click()"
                                    style="height: auto; min-height: 200px; cursor: pointer; border: 2px dashed #ccc;">
                                    <input type="file" id="thumbnail-upload-existing" wire:model="thumbnail" class="d-none" accept="image/*">
                                    
                                    @if ($thumbnail && method_exists($thumbnail, 'temporaryUrl'))
                                        <img src="{{ $thumbnail->temporaryUrl() }}" class="img-fluid rounded" alt="Yeni Önizleme">
                                    @elseif ($imagePreview)
                                        <img src="{{ url($imagePreview) }}" class="img-fluid rounded" alt="Mevcut Önizleme">
                                    @endif

                                    @if (($thumbnail && method_exists($thumbnail, 'temporaryUrl')) || $imagePreview)
                                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2" 
                                            wire:click.prevent="$set('thumbnail', null); $set('imagePreview', null)">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-2"></i>
                                            <p class="mb-0">Görseli seçmek için tıklayın</p>
                                            <p class="text-muted small mb-0">PNG, JPG, GIF - Maks 3MB</p>
                                        </div>
                                    @endif
                                </div>
                                <div class="progress mt-2" wire:loading wire:target="thumbnail">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 100%"></div>
                                </div>
                                @error('thumbnail') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Widget Ayarları</h3>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" wire:model.live="widget.has_items">
                                        <span class="form-check-label">İçerik Ekleme Özelliği</span>
                                    </label>
                                    <small class="form-hint ms-4">Kullanıcılar widgeta içerik ekleyebilirler</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" wire:model="widget.is_core">
                                        <span class="form-check-label">Sistem Widget'ı</span>
                                    </label>
                                    <small class="form-hint ms-4">Sistem widget'ları tenant'lar tarafından silinemez</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" wire:model="widget.is_active">
                                        <span class="form-check-label">Widget Aktif</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Önizleme Görseli</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-control position-relative" 
                                    onclick="document.getElementById('thumbnail-upload-new').click()"
                                    style="height: auto; min-height: 120px; cursor: pointer; border: 2px dashed #ccc;">
                                    <input type="file" id="thumbnail-upload-new" wire:model="thumbnail" class="d-none" accept="image/*">
                                    @if($thumbnail && method_exists($thumbnail, 'temporaryUrl'))
                                        <img src="{{ $thumbnail->temporaryUrl() }}" class="img-fluid rounded" alt="Önizleme">
                                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2" 
                                            wire:click.prevent="$set('thumbnail', null)">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @else
                                        <div class="text-center py-3">
                                            <i class="fas fa-cloud-upload-alt fa-2x text-primary mb-2"></i>
                                            <p class="mb-0">Görseli seçmek için tıklayın</p>
                                            <p class="text-muted small mb-0">PNG, JPG, GIF - Maks 3MB</p>
                                        </div>
                                    @endif
                                </div>
                                <div class="progress mt-2" wire:loading wire:target="thumbnail">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 100%"></div>
                                </div>
                                @error('thumbnail') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="card mt-4">
            @include('components.form-footer', [
                'route' => 'admin.widgetmanagement',
                'modelId' => $widgetId
            ])
        </div>
    </form>
</div>

@push('styles')
<style>
.form-label.required:after {
    content: " *";
    color: red;
}

.form-selectgroup-item:hover .form-selectgroup-label {
    border-color: var(--tblr-primary);
}

.form-selectgroup-input:checked ~ .form-selectgroup-label {
    border-color: var(--tblr-primary);
    box-shadow: 0 0 0 0.25rem rgba(var(--tblr-primary-rgb), 0.25);
}

@media (max-width: 991.98px) {
    .form-selectgroup-item .form-selectgroup-label {
        padding: 1rem !important;
    }
    
    .avatar.avatar-lg {
        width: 3rem;
        height: 3rem;
    }
}

@media (max-width: 575.98px) {
    .form-selectgroup-item .form-selectgroup-label {
        padding: 0.75rem !important;
    }
    
    .form-selectgroup-title {
        font-size: 0.875rem;
    }
    
    .avatar.avatar-lg {
        width: 2.5rem;
        height: 2.5rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    function slugify(text) {
        // Türkçe karakter haritası
        const turkishMap = {
            'ç': 'c', 'Ç': 'C',
            'ğ': 'g', 'Ğ': 'G',
            'ı': 'i', 'I': 'I',
            'İ': 'I', 'i': 'i',
            'ö': 'o', 'Ö': 'O',
            'ş': 's', 'Ş': 'S',
            'ü': 'u', 'Ü': 'U'
        };
        
        return text
            .toString()
            .toLowerCase()
            .trim()
            // Türkçe karakterleri dönüştür
            .replace(/[çğıöşüÇĞIÖŞÜ]/g, function(match) {
                return turkishMap[match] || match;
            })
            // Boşluk ve alt çizgiyi tire yap
            .replace(/[\s_]/g, '-')
            // Alfanumerik olmayan karakterleri kaldır (tire hariç)
            .replace(/[^\w\-]+/g, '')
            // Çoklu tireleri tek tire yap
            .replace(/\-\-+/g, '-')
            // Başlangıç ve sondaki tireleri kaldır
            .replace(/^-+/, '')
            .replace(/-+$/, '');
    }
    
    $('#widget-name').on('input', function() {
        var name = $(this).val();
        var slugField = $('#widget-slug');
        
        if (slugField.val() === '' || slugField.data('auto-generated')) {
            var slug = slugify(name);
            slugField.val(slug).data('auto-generated', true);
            @this.set('widget.slug', slug);
        }
    });
    
    $('#widget-slug').on('input', function() {
        $(this).data('auto-generated', false);
        @this.set('widget.slug', $(this).val());
    });
});
</script>
@endpush