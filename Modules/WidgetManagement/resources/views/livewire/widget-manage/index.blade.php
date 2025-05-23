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
                            <a href="{{ route('admin.widgetmanagement.preview', $widgetId) }}" 
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
    
    <div wire:loading wire:target="setMode" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Yükleniyor...</span>
        </div>
        <div class="mt-2 text-muted">Sayfa yükleniyor...</div>
    </div>
    @endif
    
    <form wire:submit.prevent="saveBasicInfo">
        <div class="row">
            <div class="col-{{ $isNewWidget ? '12' : '8' }}">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-{{ $isNewWidget ? 'plus' : 'edit' }} me-2"></i>
                            {{ $isNewWidget ? 'Yeni Widget Ekle' : 'Temel Bilgiler' }}
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-{{ $isNewWidget ? 'md-6' : '12' }}">
                                <div class="mb-4">
                                    <label class="form-label required">Widget Adı</label>
                                    <input type="text" wire:model.live="widget.name" 
                                        class="form-control @error('widget.name') is-invalid @enderror"
                                        placeholder="Widget adını giriniz">
                                    @error('widget.name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label required">Benzersiz Tanımlayıcı (Slug)</label>
                                    <input type="text" wire:model="widget.slug" 
                                        class="form-control @error('widget.slug') is-invalid @enderror"
                                        placeholder="widget-slug">
                                    @error('widget.slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <small class="form-hint">Sadece küçük harfler, rakamlar ve tire (-) kullanın.</small>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label">Kategori</label>
                                    <select wire:model="widget.widget_category_id" class="form-select @error('widget.widget_category_id') is-invalid @enderror">
                                        <option value="">Kategori Seçiniz</option>
                                        @foreach($categories as $category)
                                        <option value="{{ $category->widget_category_id }}">{{ $category->title }}</option>
                                        @endforeach
                                    </select>
                                    @error('widget.widget_category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">Açıklama</label>
                                    <textarea wire:model="widget.description" 
                                        class="form-control @error('widget.description') is-invalid @enderror" 
                                        placeholder="Widget açıklaması"
                                        rows="4"></textarea>
                                    @error('widget.description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            
                            <div class="col-{{ $isNewWidget ? 'md-6' : '12' }}">
                                <div class="mb-4">
                                    <label class="form-label required">Widget Tipi</label>
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label class="form-selectgroup-item">
                                                <input type="radio" name="widget-type" value="static" wire:model="widget.type" class="form-selectgroup-input">
                                                <span class="form-selectgroup-label d-flex align-items-center p-3">
                                                    <span class="me-3">
                                                        <span class="form-selectgroup-check"></span>
                                                    </span>
                                                    <span class="form-selectgroup-label-content text-start">
                                                        <span class="form-selectgroup-title strong mb-1">Statik</span>
                                                        <span class="d-block text-muted">Sabit içerik</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-selectgroup-item">
                                                <input type="radio" name="widget-type" value="dynamic" wire:model="widget.type" class="form-selectgroup-input">
                                                <span class="form-selectgroup-label d-flex align-items-center p-3">
                                                    <span class="me-3">
                                                        <span class="form-selectgroup-check"></span>
                                                    </span>
                                                    <span class="form-selectgroup-label-content text-start">
                                                        <span class="form-selectgroup-title strong mb-1">Dinamik</span>
                                                        <span class="d-block text-muted">Eklenebilir içerik</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-selectgroup-item">
                                                <input type="radio" name="widget-type" value="module" wire:model="widget.type" class="form-selectgroup-input">
                                                <span class="form-selectgroup-label d-flex align-items-center p-3">
                                                    <span class="me-3">
                                                        <span class="form-selectgroup-check"></span>
                                                    </span>
                                                    <span class="form-selectgroup-label-content text-start">
                                                        <span class="form-selectgroup-title strong mb-1">Modül</span>
                                                        <span class="d-block text-muted">Özel modül</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-selectgroup-item">
                                                <input type="radio" name="widget-type" value="file" wire:model="widget.type" class="form-selectgroup-input">
                                                <span class="form-selectgroup-label d-flex align-items-center p-3">
                                                    <span class="me-3">
                                                        <span class="form-selectgroup-check"></span>
                                                    </span>
                                                    <span class="form-selectgroup-label-content text-start">
                                                        <span class="form-selectgroup-title strong mb-1">Dosya</span>
                                                        <span class="d-block text-muted">Hazır view</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                    @error('widget.type') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>

                                @if($widget['type'] === 'file' || $widget['type'] === 'module')
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    {{ $widget['type'] === 'file' ? 'Hazır dosya kullanımı için dosya yolunu belirtin.' : 'Modül dosya kullanımı için dosya yolunu belirtin.' }}
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label">{{ $widget['type'] === 'file' ? 'View' : 'Modül' }} Dosya Yolu</label>
                                    <input type="text" 
                                        wire:model="widget.file_path" 
                                        class="form-control font-monospace @error('widget.file_path') is-invalid @enderror"
                                        placeholder="Örnek: cards/basic">
                                    @error('widget.file_path') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                @endif
                                
                                <div class="mb-3">
                                    <label class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" wire:model.live="widget.has_items">
                                        <span class="form-check-label">İçerik Ekleme Özelliği</span>
                                    </label>
                                    <small class="form-hint ms-4">Kullanıcılar widgeta içerik ekleyebilirler</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" wire:model="widget.is_active">
                                        <span class="form-check-label">Widget Aktif</span>
                                    </label>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" wire:model="widget.is_core">
                                        <span class="form-check-label">Sistem Widget'ı</span>
                                    </label>
                                    <small class="form-hint ms-4">Sistem widget'ları tenant'lar tarafından silinemez</small>
                                </div>
                                
                                @if($isNewWidget)
                                <div class="mb-4">
                                    <label class="form-label">Önizleme Görseli (Opsiyonel)</label>
                                    <div class="form-control position-relative" 
                                        onclick="document.getElementById('thumbnail-upload').click()"
                                        style="height: auto; min-height: 120px; cursor: pointer; border: 2px dashed #ccc;">
                                        <input type="file" id="thumbnail-upload" wire:model="thumbnail" class="d-none" accept="image/*">
                                        
                                        @if($imagePreview)
                                            <img src="{{ url($imagePreview) }}" class="img-fluid rounded" alt="Önizleme">
                                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2" 
                                                wire:click.prevent="$set('thumbnail', null); $set('imagePreview', null)">
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
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            @if(!$isNewWidget)
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Önizleme Görseli</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <div class="form-control position-relative" 
                                onclick="document.getElementById('thumbnail-upload').click()"
                                style="height: auto; min-height: 200px; cursor: pointer; border: 2px dashed #ccc;">
                                <input type="file" id="thumbnail-upload" wire:model="thumbnail" class="d-none" accept="image/*">
                                
                                @if($imagePreview)
                                    <img src="{{ url($imagePreview) }}" class="img-fluid rounded" alt="Önizleme">
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

                <div class="card mt-4">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-cog me-2"></i>
                            İşlemler
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            @if($widget['type'] !== 'file' && $widget['type'] !== 'module')
                            <a href="{{ route('admin.widgetmanagement.code-editor', $widgetId) }}" 
                               class="btn btn-outline-dark">
                                <i class="fas fa-code me-2"></i>
                                Kod Editörü
                            </a>
                            @endif
                            
                            <a href="{{ route('admin.widgetmanagement.form-builder.edit', ['widgetId' => $widgetId, 'schemaType' => 'settings_schema']) }}" 
                               class="btn btn-outline-primary" target="_blank">
                                <i class="fas fa-sliders-h me-2"></i>
                                Özelleştirme Ayarları
                            </a>
                            
                            @if($widget['has_items'])
                            <a href="{{ route('admin.widgetmanagement.form-builder.edit', ['widgetId' => $widgetId, 'schemaType' => 'item_schema']) }}" 
                               class="btn btn-outline-success" target="_blank">
                                <i class="fas fa-layer-group me-2"></i>
                                İçerik Yapısı
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        
        <div class="card mt-4">
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('admin.widgetmanagement.index') }}" class="btn btn-link text-decoration-none">İptal</a>
                    
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove>
                            <i class="fas fa-save me-2"></i>
                            {{ $isNewWidget ? 'Widget Oluştur' : 'Temel Bilgileri Kaydet' }}
                        </span>
                        <span wire:loading>
                            <i class="fas fa-spinner fa-spin me-2"></i>
                            Kaydediliyor...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('styles')
<style>
.form-label.required:after {
    content: " *";
    color: red;
}
</style>
@endpush