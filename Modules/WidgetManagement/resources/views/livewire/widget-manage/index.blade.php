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
                            <span class="badge bg-{{ $widget['type'] === 'static' ? 'blue' : ($widget['type'] === 'dynamic' ? 'green' : ($widget['type'] === 'module' ? 'purple' : 'orange')) }}-lt">
                                {{ ucfirst($widget['type']) }}
                            </span>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" 
                                class="btn {{ $currentMode === 'basic' ? 'btn-primary' : 'btn-outline-primary' }}" 
                                wire:click="setMode('basic')">
                                <i class="fas fa-info-circle me-2"></i>
                                Temel Bilgiler
                            </button>
                            @if($widget['type'] !== 'file' && $widget['type'] !== 'module')
                            <button type="button" 
                                class="btn {{ $currentMode === 'design' ? 'btn-primary' : 'btn-outline-primary' }}" 
                                wire:click="setMode('design')">
                                <i class="fas fa-palette me-2"></i>
                                Tasarım
                            </button>
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
    
    @if(($currentMode === 'basic' || $isNewWidget) && !$isLoading)
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
                                <div class="form-floating mb-4">
                                    <input type="text" wire:model.live="widget.name" 
                                        class="form-control @error('widget.name') is-invalid @enderror"
                                        placeholder="Widget adı" id="widget-name">
                                    <label for="widget-name">Widget Adı</label>
                                    @error('widget.name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                
                                <div class="form-floating mb-4">
                                    <input type="text" wire:model="widget.slug" 
                                        class="form-control @error('widget.slug') is-invalid @enderror"
                                        placeholder="Slug" id="widget-slug">
                                    <label for="widget-slug">Benzersiz Tanımlayıcı (Slug)</label>
                                    @error('widget.slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <div class="form-hint">
                                        <i class="fas fa-info-circle me-1 text-blue"></i>
                                        Sadece küçük harfler, rakamlar ve tire (-) kullanın.
                                    </div>
                                </div>
                                
                                <div class="form-floating mb-4">
                                    <select wire:model="widget.widget_category_id" class="form-select @error('widget.widget_category_id') is-invalid @enderror" id="widget-category">
                                        <option value="">Kategori Seçiniz</option>
                                        @foreach($categories as $category)
                                        <option value="{{ $category->widget_category_id }}">{{ $category->title }}</option>
                                        @endforeach
                                    </select>
                                    <label for="widget-category">Kategori</label>
                                    @error('widget.widget_category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="form-floating mb-4">
                                    <textarea wire:model="widget.description" 
                                        class="form-control @error('widget.description') is-invalid @enderror" 
                                        placeholder="Açıklama"
                                        id="widget-description"
                                        style="min-height: 120px"></textarea>
                                    <label for="widget-description">Açıklama</label>
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
                                
                                <div class="form-floating mb-4">
                                    <input type="text" 
                                        wire:model="widget.file_path" 
                                        class="form-control font-monospace @error('widget.file_path') is-invalid @enderror"
                                        id="file-path"
                                        placeholder="Örnek: cards/basic">
                                    <label for="file-path">{{ $widget['type'] === 'file' ? 'View' : 'Modül' }} Dosya Yolu</label>
                                    @error('widget.file_path') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                @endif
                                
                                <div class="mb-3">
                                    <label class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" wire:model.live="widget.has_items">
                                        <span class="form-check-label">İçerik Ekleme Özelliği</span>
                                    </label>
                                    <div class="form-hint ms-4">
                                        Kullanıcılar widgeta içerik ekleyebilirler
                                    </div>
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
                                    <div class="form-hint ms-4">
                                        Sistem widget'ları tenant'lar tarafından silinemez
                                    </div>
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
                            Form Yapıları
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
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
    @endif

    @if($currentMode === 'design' && !$isNewWidget && $widget['type'] !== 'file' && $widget['type'] !== 'module' && !$isLoading)
    <form wire:submit.prevent="saveDesign">
        <div class="col-12">
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <h3 class="mb-0">
                                    <i class="fas fa-palette me-2"></i>
                                    Tasarım ve İçerik
                                </h3>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.widgetmanagement.form-builder.edit', ['widgetId' => $widgetId, 'schemaType' => 'settings_schema']) }}" 
                                       class="btn btn-outline-primary btn-sm" target="_blank">
                                        <i class="fas fa-sliders-h me-2"></i>
                                        Özelleştirme Ayarları
                                    </a>
                                    
                                    @if($widget['has_items'])
                                    <a href="{{ route('admin.widgetmanagement.form-builder.edit', ['widgetId' => $widgetId, 'schemaType' => 'item_schema']) }}" 
                                       class="btn btn-outline-success btn-sm" target="_blank">
                                        <i class="fas fa-layer-group me-2"></i>
                                        İçerik Yapısı
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body">
                    @php
                        $variables = $this->getAvailableVariables();
                    @endphp
                    
                    @if(!empty($variables))
                    <div class="row mb-4">
                        @if(isset($variables['settings']))
                        <div class="col-md-6">
                            <div class="card border-primary">
                                <div class="card-header bg-primary-lt">
                                    <h4 class="card-title mb-0 text-primary">
                                        <i class="fas fa-sliders-h me-2"></i>
                                        Özelleştirme Değişkenleri
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-borderless mb-0">
                                            @foreach($variables['settings'] as $var)
                                            <tr>
                                                <td>
                                                    <code class="bg-light px-2 py-1 rounded">&#123;&#123; {{ $var['name'] }} &#125;&#125;</code>
                                                </td>
                                                <td class="text-muted">{{ $var['label'] }}</td>
                                            </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        @if(isset($variables['items']))
                        <div class="col-md-6">
                            <div class="card border-success">
                                <div class="card-header bg-success-lt">
                                    <h4 class="card-title mb-0 text-success">
                                        <i class="fas fa-layer-group me-2"></i>
                                        İçerik Değişkenleri
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <div class="mb-2">
                                        <code class="bg-light px-2 py-1 rounded">&#123;&#123; #each items &#125;&#125;</code>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-borderless mb-0">
                                            @foreach($variables['items'] as $var)
                                            <tr>
                                                <td>
                                                    <code class="bg-light px-2 py-1 rounded">&#123;&#123; {{ $var['name'] }} &#125;&#125;</code>
                                                </td>
                                                <td class="text-muted">{{ $var['label'] }}</td>
                                            </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                    <div class="mt-2">
                                        <code class="bg-light px-2 py-1 rounded">&#123;&#123; /each &#125;&#125;</code>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    @else
                    <div class="alert alert-info mb-4">
                        <div class="d-flex">
                            <div>
                                <i class="fas fa-info-circle text-blue me-3 mt-1"></i>
                            </div>
                            <div>
                                <h4 class="alert-title">Handlebars Şablon Değişkenleri</h4>
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Değişkenler:</strong><br>
                                        <code class="bg-light px-2 py-1 rounded">&#123;&#123; değişken_adı &#125;&#125;</code>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Dinamik içerikler:</strong><br>
                                        <code class="bg-light px-2 py-1 rounded">&#123;&#123; #each items &#125;&#125;...&#123;&#123; /each &#125;&#125;</code>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Koşullu içerik:</strong><br>
                                        <code class="bg-light px-2 py-1 rounded">&#123;&#123; #if değişken &#125;&#125;...&#123;&#123; /if &#125;&#125;</code>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <ul class="nav nav-tabs" data-bs-toggle="tabs">
                        <li class="nav-item">
                            <a href="#tabs-code" class="nav-link active" data-bs-toggle="tab">
                                <i class="fas fa-code me-2"></i>
                                Kod Editörü
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#tabs-files" class="nav-link" data-bs-toggle="tab">
                                <i class="fas fa-file-code me-2"></i>
                                Harici Dosyalar
                            </a>
                        </li>
                    </ul>
                    
                    <div class="tab-content">
                        <div class="tab-pane active show" id="tabs-code">
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i class="fab fa-html5 text-orange me-2"></i>
                                    HTML İçeriği
                                </label>
                                <div class="position-relative">
                                    <textarea 
                                        id="html-editor"
                                        wire:model="widget.content_html" 
                                        class="form-control font-monospace @error('widget.content_html') is-invalid @enderror" 
                                        rows="15"
                                        style="font-size: 14px; resize: vertical;"
                                        placeholder="HTML içeriğinizi buraya yazın..."></textarea>
                                    @error('widget.content_html') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i class="fab fa-css3-alt text-blue me-2"></i>
                                    CSS Kodu
                                </label>
                                <div class="position-relative">
                                    <textarea 
                                        id="css-editor"
                                        wire:model="widget.content_css" 
                                        class="form-control font-monospace @error('widget.content_css') is-invalid @enderror" 
                                        rows="12"
                                        style="font-size: 14px; resize: vertical;"
                                        placeholder="CSS kodlarınızı buraya yazın..."></textarea>
                                    @error('widget.content_css') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i class="fab fa-js-square text-yellow me-2"></i>
                                    JavaScript Kodu
                                </label>
                                <div class="position-relative">
                                    <textarea 
                                        id="js-editor"
                                        wire:model="widget.content_js" 
                                        class="form-control font-monospace @error('widget.content_js') is-invalid @enderror" 
                                        rows="12"
                                        style="font-size: 14px; resize: vertical;"
                                        placeholder="JavaScript kodlarınızı buraya yazın..."></textarea>
                                    @error('widget.content_js') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="tab-pane" id="tabs-files">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h4 class="card-title mb-0">
                                                    <i class="fab fa-css3-alt text-blue me-2"></i>
                                                    CSS Dosyaları
                                                </h4>
                                                <button type="button" class="btn btn-sm btn-primary" wire:click="addCssFile">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @if(empty($widget['css_files']) || count($widget['css_files']) === 0)
                                            <div class="text-center py-3 text-muted">
                                                <i class="fab fa-css3-alt fa-2x mb-2"></i>
                                                <p class="mb-0">Henüz CSS dosyası eklenmedi.</p>
                                            </div>
                                            @else
                                            @foreach($widget['css_files'] as $index => $cssFile)
                                            <div class="input-group mb-2">
                                                <span class="input-group-text">
                                                    <i class="fas fa-link"></i>
                                                </span>
                                                <input type="text" 
                                                    class="form-control" 
                                                    wire:model="widget.css_files.{{ $index }}" 
                                                    placeholder="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
                                                <button type="button" class="btn btn-outline-danger" wire:click="removeCssFile({{ $index }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                            @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h4 class="card-title mb-0">
                                                    <i class="fab fa-js-square text-yellow me-2"></i>
                                                    JavaScript Dosyaları
                                                </h4>
                                                <button type="button" class="btn btn-sm btn-primary" wire:click="addJsFile">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @if(empty($widget['js_files']) || count($widget['js_files']) === 0)
                                            <div class="text-center py-3 text-muted">
                                                <i class="fab fa-js-square fa-2x mb-2"></i>
                                                <p class="mb-0">Henüz JavaScript dosyası eklenmedi.</p>
                                            </div>
                                            @else
                                            @foreach($widget['js_files'] as $index => $jsFile)
                                            <div class="input-group mb-2">
                                                <span class="input-group-text">
                                                    <i class="fas fa-link"></i>
                                                </span>
                                                <input type="text" 
                                                    class="form-control" 
                                                    wire:model="widget.js_files.{{ $index }}" 
                                                    placeholder="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js">
                                                <button type="button" class="btn btn-outline-danger" wire:click="removeJsFile({{ $index }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                            @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('admin.widgetmanagement.index') }}" class="btn btn-link text-decoration-none">İptal</a>
                    
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove>
                            <i class="fas fa-save me-2"></i>
                            Tasarımı Kaydet
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
    @endif
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/theme/default.min.css">
<style>
.form-label.required:after {
    content: " *";
    color: red;
}

.code-editor {
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
    line-height: 1.4;
    tab-size: 2;
}

.tab-content {
    border: none;
    padding-top: 1rem;
}

.nav-tabs {
    border-bottom: 1px solid #dee2e6;
    margin-bottom: 0;
}

.card.border-primary {
    border-color: #0066cc !important;
}

.card.border-success {
    border-color: #28a745 !important;
}

.bg-primary-lt {
    background-color: rgba(0, 102, 204, 0.1) !important;
}

.bg-success-lt {
    background-color: rgba(40, 167, 69, 0.1) !important;
}

.CodeMirror {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    font-size: 14px;
}

.CodeMirror-focused {
    border-color: #86b7fe;
    outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/xml/xml.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/css/css.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/javascript/javascript.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/htmlmixed/htmlmixed.min.js"></script>

<script>
document.addEventListener('livewire:initialized', function() {
    let htmlEditor, cssEditor, jsEditor;
    
    function initializeEditors() {
        const htmlTextarea = document.getElementById('html-editor');
        const cssTextarea = document.getElementById('css-editor');
        const jsTextarea = document.getElementById('js-editor');
        
        if (htmlTextarea && !htmlEditor) {
            htmlEditor = CodeMirror.fromTextArea(htmlTextarea, {
                mode: 'htmlmixed',
                lineNumbers: true,
                theme: 'default',
                indentUnit: 2,
                lineWrapping: true,
                autoCloseTags: true,
                matchBrackets: true
            });
            
            htmlEditor.on('change', function() {
                Livewire.find('{{ $this->getId() }}').set('widget.content_html', htmlEditor.getValue());
            });
        }
        
        if (cssTextarea && !cssEditor) {
            cssEditor = CodeMirror.fromTextArea(cssTextarea, {
                mode: 'css',
                lineNumbers: true,
                theme: 'default',
                indentUnit: 2,
                lineWrapping: true,
                autoCloseBrackets: true,
                matchBrackets: true
            });
            
            cssEditor.on('change', function() {
                Livewire.find('{{ $this->getId() }}').set('widget.content_css', cssEditor.getValue());
            });
        }
        
        if (jsTextarea && !jsEditor) {
            jsEditor = CodeMirror.fromTextArea(jsTextarea, {
                mode: 'javascript',
                lineNumbers: true,
                theme: 'default',
                indentUnit: 2,
                lineWrapping: true,
                autoCloseBrackets: true,
                matchBrackets: true
            });
            
            jsEditor.on('change', function() {
                Livewire.find('{{ $this->getId() }}').set('widget.content_js', jsEditor.getValue());
            });
        }
    }
    
    setTimeout(() => {
        initializeEditors();
    }, 500);
    
    document.addEventListener('livewire:morph.updated', function() {
        setTimeout(() => {
            initializeEditors();
        }, 500);
    });
});
</script>
@endpush