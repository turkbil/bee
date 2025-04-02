@include('widgetmanagement::helper')
<div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-{{ $widgetId ? 'edit' : 'plus' }} me-2"></i>
                {{ $widgetId ? 'Widget Düzenle: ' . $widget['name'] : 'Yeni Widget Ekle' }}
            </h3>
        </div>
        <div class="card-body">
            <div class="d-flex mb-3">
                <div class="nav-tabs-container w-100">
                    <ul class="nav nav-tabs nav-fill" data-bs-toggle="tabs">
                        <li class="nav-item">
                            <a class="nav-link {{ $formMode === 'base' ? 'active' : '' }}" href="#" wire:click.prevent="setFormMode('base')">
                                <i class="fas fa-info-circle me-2"></i>
                                Temel Bilgiler
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $formMode === 'design' ? 'active' : '' }}" href="#" wire:click.prevent="setFormMode('design')">
                                <i class="fas fa-palette me-2"></i>
                                İçerik ve Tasarım
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $formMode === 'items' ? 'active' : '' }}" href="#" wire:click.prevent="setFormMode('items')">
                                <i class="fas fa-layer-group me-2"></i>
                                İçerik Yapısı
                                <span class="badge bg-blue ms-1" data-bs-toggle="tooltip" title="İçerik Yapısı, widgetınızın içereceği dinamik verilerin şablonunu belirler">?</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $formMode === 'settings' ? 'active' : '' }}" href="#" wire:click.prevent="setFormMode('settings')">
                                <i class="fas fa-sliders-h me-2"></i>
                                Özelleştirme Seçenekleri
                                <span class="badge bg-blue ms-1" data-bs-toggle="tooltip" title="Özelleştirme Seçenekleri, kullanıcıların widgetı kişiselleştirebilmesi için ayarları belirler">?</span>
                            </a>
                        </li>
                        @if($widgetId)
                        <li class="nav-item">
                            <a class="nav-link {{ $formMode === 'preview' ? 'active' : '' }}" href="#" wire:click.prevent="setFormMode('preview')">
                                <i class="fas fa-eye me-2"></i>
                                Önizleme
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
            
            <!-- Temel Bilgiler -->
            @if($formMode === 'base')
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Widget Bilgileri</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label required">Widget Adı</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon">
                                        <i class="fas fa-tag"></i>
                                    </span>
                                    <input type="text" wire:model.live="widget.name" 
                                        placeholder="Örn: Başlık Banner" 
                                        class="form-control @error('widget.name') is-invalid @enderror">
                                </div>
                                @error('widget.name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label required">Benzersiz Tanımlayıcı (Slug)</label>
                                <div class="input-icon">
                                    <span class="input-icon-addon">
                                        <i class="fas fa-link"></i>
                                    </span>
                                    <input type="text" wire:model="widget.slug" 
                                        placeholder="Örn: baslik-banner"
                                        class="form-control @error('widget.slug') is-invalid @enderror">
                                </div>
                                @error('widget.slug') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                <div class="form-hint">
                                    <i class="fas fa-info-circle me-1 text-blue"></i>
                                    Sadece küçük harfler, rakamlar ve tire (-) kullanın. Boşluk kullanmayın.
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Açıklama</label>
                                <textarea wire:model="widget.description" 
                                    class="form-control @error('widget.description') is-invalid @enderror" 
                                    placeholder="Bu widget ne işe yarar? Kısaca açıklayın."
                                    rows="3"></textarea>
                                @error('widget.description') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label required">Widget Tipi</label>
                                <div class="row g-2">
                                    <div class="col-md-6 col-xl-3">
                                        <label class="form-selectgroup-item">
                                            <input type="radio" name="widget-type" value="static" wire:model="widget.type" class="form-selectgroup-input">
                                            <span class="form-selectgroup-label d-flex align-items-center p-3">
                                                <span class="me-3">
                                                    <span class="form-selectgroup-check"></span>
                                                </span>
                                                <span class="form-selectgroup-label-content">
                                                    <span class="form-selectgroup-title strong mb-1">Statik</span>
                                                    <span class="d-block text-muted">Sabit içerikli, değişmeyen widget</span>
                                                </span>
                                            </span>
                                        </label>
                                    </div>
                                    <div class="col-md-6 col-xl-3">
                                        <label class="form-selectgroup-item">
                                            <input type="radio" name="widget-type" value="dynamic" wire:model="widget.type" class="form-selectgroup-input">
                                            <span class="form-selectgroup-label d-flex align-items-center p-3">
                                                <span class="me-3">
                                                    <span class="form-selectgroup-check"></span>
                                                </span>
                                                <span class="form-selectgroup-label-content">
                                                    <span class="form-selectgroup-title strong mb-1">Dinamik</span>
                                                    <span class="d-block text-muted">Eklenebilir içeriklerle değişken widget</span>
                                                </span>
                                            </span>
                                        </label>
                                    </div>
                                    <div class="col-md-6 col-xl-3">
                                        <label class="form-selectgroup-item">
                                            <input type="radio" name="widget-type" value="module" wire:model="widget.type" class="form-selectgroup-input">
                                            <span class="form-selectgroup-label d-flex align-items-center p-3">
                                                <span class="me-3">
                                                    <span class="form-selectgroup-check"></span>
                                                </span>
                                                <span class="form-selectgroup-label-content">
                                                    <span class="form-selectgroup-title strong mb-1">Modül</span>
                                                    <span class="d-block text-muted">Belirli modüllere özel widget</span>
                                                </span>
                                            </span>
                                        </label>
                                    </div>
                                    <div class="col-md-6 col-xl-3">
                                        <label class="form-selectgroup-item">
                                            <input type="radio" name="widget-type" value="content" wire:model="widget.type" class="form-selectgroup-input">
                                            <span class="form-selectgroup-label d-flex align-items-center p-3">
                                                <span class="me-3">
                                                    <span class="form-selectgroup-check"></span>
                                                </span>
                                                <span class="form-selectgroup-label-content">
                                                    <span class="form-selectgroup-title strong mb-1">İçerik</span>
                                                    <span class="d-block text-muted">Sayfa içeriği olarak kullanılan widget</span>
                                                </span>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                                @error('widget.type') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Modüller</label>
                                <select wire:model="widget.module_ids" class="form-select @error('widget.module_ids') is-invalid @enderror" multiple>
                                    <option value="">Tüm modüllerde kullanılabilir</option>
                                    @foreach($modules as $module)
                                    <option value="{{ $module->get('id') }}">{{ $module->get('name') }}</option>
                                    @endforeach
                                </select>
                                @error('widget.module_ids') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                <div class="form-hint">
                                    <i class="fas fa-info-circle me-1 text-blue"></i>
                                    Hiçbir modül seçilmezse widget tüm modüllerde kullanılabilir olacaktır.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Önizleme Görseli</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="form-control position-relative" 
                                    onclick="document.getElementById('thumbnail-upload').click()"
                                    style="height: auto; min-height: 150px; cursor: pointer; border: 2px dashed #ccc;">
                                    <input type="file" id="thumbnail-upload" wire:model="thumbnail" class="d-none" accept="image/*">
                                    
                                    @if($thumbnail)
                                        <img src="{{ $thumbnail->temporaryUrl() }}" class="img-fluid rounded" alt="Önizleme">
                                    @elseif($widgetId && isset($widget['thumbnail']))
                                        <img src="{{ asset('storage/widgets/' . $widget['slug'] . '/' . $widget['thumbnail']) }}" class="img-fluid rounded" alt="Mevcut görsel">
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-2"></i>
                                            <p class="mb-0">Görseli sürükleyin veya seçmek için tıklayın</p>
                                            <p class="text-muted small mb-0">PNG, JPG, GIF - Maks 1MB</p>
                                        </div>
                                    @endif
                                </div>
                                @error('thumbnail') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            
                            <div class="mt-4">
                                <div class="form-label">Widget Seçenekleri</div>
                                
                                <div class="mb-2">
                                    <label class="form-check form-switch">
                                        <input type="checkbox" id="has_items" class="form-check-input" wire:model="widget.has_items">
                                        <span class="form-check-label">İçerik Ekleme Özelliği</span>
                                    </label>
                                    <div class="form-hint ms-4">
                                        <i class="fas fa-info-circle me-1 text-blue"></i>
                                        Bu özellik ile kullanıcılar widgeta içerik ekleyebilirler (örn. slider için slaytlar)
                                    </div>
                                </div>
                                
                                <div class="mb-2">
                                    <label class="form-check form-switch">
                                        <input type="checkbox" id="is_active" class="form-check-input" wire:model="widget.is_active">
                                        <span class="form-check-label">Widget Aktif</span>
                                    </label>
                                </div>
                                
                                <div class="mb-2">
                                    <label class="form-check form-switch">
                                        <input type="checkbox" id="is_core" class="form-check-input" wire:model="widget.is_core">
                                        <span class="form-check-label">Sistem Widget'ı</span>
                                    </label>
                                    <div class="form-hint ms-4">
                                        <i class="fas fa-info-circle me-1 text-blue"></i>
                                        Sistem widget'ları tenant'lar tarafından silinemez veya devre dışı bırakılamaz
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- İçerik ve Tasarım -->
            @if($formMode === 'design')
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title d-flex align-items-center">
                                <i class="fas fa-code me-2"></i>
                                Widget İçeriği ve Tasarımı
                                <span class="badge bg-blue ms-2">HTML, CSS, JavaScript</span>
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <div class="d-flex">
                                    <div>
                                        <i class="fas fa-info-circle text-blue me-2" style="margin-top: 3px"></i>
                                    </div>
                                    <div>
                                        <h4 class="alert-title">Şablon Değişkenleri</h4>
                                        <div class="text-muted">
                                            <strong>Değişkenler:</strong> <code>&lbrace;&lbrace; değişken_adı &rbrace;&rbrace;</code> şeklinde kullanın<br>
                                            <strong>Dinamik içerikler:</strong> <code>&lbrace;&lbrace; #each items &rbrace;&rbrace;...&lbrace;&lbrace; /each &rbrace;&rbrace;</code> blokları içinde tanımlayın<br>
                                            <strong>Koşullu içerik:</strong> <code>&lbrace;&lbrace; #if değişken &rbrace;&rbrace;...&lbrace;&lbrace; else &rbrace;&rbrace;...&lbrace;&lbrace; /if &rbrace;&rbrace;</code> şeklinde kullanın
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="form-label mb-0">
                                                <i class="fas fa-html5 text-primary me-1"></i> HTML İçeriği
                                            </label>
                                            <span class="text-muted small">Widgetın ana yapısı</span>
                                        </div>
                                        <textarea 
                                            wire:model="widget.content_html" 
                                            class="form-control font-monospace" 
                                            rows="12"
                                            style="font-size: 14px;"
                                            placeholder="<div class=&quot;my-widget&quot;>Widget içeriği buraya...</div>">{{ $widget['content_html'] }}</textarea>
                                    </div>
                                    
                                    <!-- CSS Yönetimi -->
                                    <div class="card mb-3">
                                        <div class="card-header">
                                            <h4 class="card-title">
                                                <i class="fas fa-css3-alt text-info me-1"></i> CSS Yönetimi
                                            </h4>
                                        </div>
                                        <div class="card-body">
                                            <!-- CSS Dosya Bağlantıları -->
                                            <div class="mb-3">
                                                <label class="form-label d-flex justify-content-between">
                                                    <span>CSS Dosya Bağlantıları</span>
                                                    <button type="button" class="btn btn-sm btn-outline-primary" wire:click="addCssFile">
                                                        <i class="fas fa-plus me-1"></i> Bağlantı Ekle
                                                    </button>
                                                </label>
                                                
                                                @if(empty($widget['css_files']))
                                                    <div class="text-muted text-center py-3">
                                                        <i class="fas fa-info-circle me-1"></i>
                                                        Henüz CSS dosya bağlantısı eklenmemiş. Yukarıdaki butonu kullanarak ekleyebilirsiniz.
                                                    </div>
                                                @else
                                                    @foreach($widget['css_files'] as $index => $cssFile)
                                                        <div class="input-group mb-2">
                                                            <span class="input-group-text">
                                                                <i class="fas fa-link"></i>
                                                            </span>
                                                            <input type="text" 
                                                                class="form-control" 
                                                                placeholder="https://örnek.com/style.css" 
                                                                value="{{ $cssFile }}" 
                                                                wire:change="updateCssFile({{ $index }}, $event.target.value)">
                                                            <button type="button" class="btn btn-outline-danger" wire:click="removeCssFile({{ $index }})">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    @endforeach
                                                @endif
                                                
                                                <div class="form-hint">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    CSS dosyalarına tam URL ile bağlantı verin. Öneri: versiyonlama için <code>?v=123456</code> gibi bir query parametresi ekleyin.
                                                </div>
                                            </div>
                                            
                                            <!-- Manuel CSS Kodu -->
                                            <div class="mb-3">
                                                <label class="form-label">Manuel CSS Kodu</label>
                                                <textarea 
                                                wire:model="widget.content_css" 
                                                class="form-control font-monospace" 
                                                rows="8"
                                                style="font-size: 14px;"
                                                placeholder=".my-widget {
                                              padding: 20px;
                                              background-color: &lbrace;&lbrace;background_color&rbrace;&rbrace;;
                                            }">{{ $widget['content_css'] }}</textarea>
                                                <div class="form-hint">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    Hem CSS dosya bağlantıları hem de manuel CSS kodu kullanabilirsiniz.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- JavaScript Yönetimi -->
                                    <div class="card mb-3">
                                        <div class="card-header">
                                            <h4 class="card-title">
                                                <i class="fab fa-js-square text-warning me-1"></i> JavaScript Yönetimi
                                            </h4>
                                        </div>
                                        <div class="card-body">
                                            <!-- JS Dosya Bağlantıları -->
                                            <div class="mb-3">
                                                <label class="form-label d-flex justify-content-between">
                                                    <span>JavaScript Dosya Bağlantıları</span>
                                                    <button type="button" class="btn btn-sm btn-outline-primary" wire:click="addJsFile">
                                                        <i class="fas fa-plus me-1"></i> Bağlantı Ekle
                                                    </button>
                                                </label>
                                                
                                                @if(empty($widget['js_files']))
                                                    <div class="text-muted text-center py-3">
                                                        <i class="fas fa-info-circle me-1"></i>
                                                        Henüz JavaScript dosya bağlantısı eklenmemiş. Yukarıdaki butonu kullanarak ekleyebilirsiniz.
                                                    </div>
                                                @else
                                                    @foreach($widget['js_files'] as $index => $jsFile)
                                                        <div class="input-group mb-2">
                                                            <span class="input-group-text">
                                                                <i class="fas fa-link"></i>
                                                            </span>
                                                            <input type="text" 
                                                                class="form-control" 
                                                                placeholder="https://örnek.com/script.js" 
                                                                value="{{ $jsFile }}" 
                                                                wire:change="updateJsFile({{ $index }}, $event.target.value)">
                                                            <button type="button" class="btn btn-outline-danger" wire:click="removeJsFile({{ $index }})">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    @endforeach
                                                @endif
                                                
                                                <div class="form-hint">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    JavaScript dosyalarına tam URL ile bağlantı verin. Öneri: versiyonlama için <code>?v=123456</code> gibi bir query parametresi ekleyin.
                                                </div>
                                            </div>
                                            
                                            <!-- Manuel JS Kodu -->
                                            <div class="mb-3">
                                                <label class="form-label">Manuel JavaScript Kodu</label>
                                                <textarea 
                                                wire:model="widget.content_js" 
                                                class="form-control font-monospace" 
                                                rows="8"
                                                style="font-size: 14px;"
                                                placeholder="document.addEventListener('DOMContentLoaded', function() {
                                              // Widget JavaScript kodu
                                              console.log('Widget yüklendi: ' + &lbrace;&lbrace;id&rbrace;&rbrace;);
                                            });">{{ $widget['content_js'] }}</textarea>
                                                <div class="form-hint">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    Hem JavaScript dosya bağlantıları hem de manuel JavaScript kodu kullanabilirsiniz.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- İçerik Yapısı (Öğe Şeması) -->
            @if($formMode === 'items')
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title d-flex align-items-center">
                                <i class="fas fa-layer-group me-2"></i>
                                İçerik Yapısı
                                <span class="badge bg-purple ms-2">Dinamik Veriler</span>
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <div class="d-flex">
                                    <div>
                                        <i class="fas fa-lightbulb text-blue me-2" style="margin-top: 3px"></i>
                                    </div>
                                    <div>
                                        <h4 class="alert-title">İçerik Yapısı Nedir?</h4>
                                        <div class="text-muted">
                                            İçerik yapısı, widgetınızın içerebileceği dinamik verilerin şablonunu belirler. Örneğin:<br>
                                            <ul class="mb-0">
                                                <li>Slider widget'ı için slaytların başlık, görsel ve açıklama alanları</li>
                                                <li>SSS widget'ı için soru ve cevap alanları</li>
                                                <li>Galeri widget'ı için resimler ve açıklamaları</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            @if(!$widget['has_items'])
                            <div class="alert alert-warning">
                                <div class="d-flex">
                                    <div>
                                        <i class="fas fa-exclamation-triangle text-warning me-2" style="margin-top: 3px"></i>
                                    </div>
                                    <div>
                                        <h4 class="alert-title">İçerik Yapısı Tanımlanamıyor</h4>
                                        <div class="text-muted">
                                            İçerik yapısı tanımlamak için önce "Temel Bilgiler" sekmesinden "İçerik Ekleme Özelliği" seçeneğini etkinleştirmelisiniz.
                                        </div>
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-warning" wire:click="$set('widget.has_items', true); setFormMode('base')">
                                                <i class="fas fa-check me-1"></i> İçerik Ekleme Özelliğini Etkinleştir
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @else
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <h3 class="card-title">Yeni İçerik Alanı Ekle</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-3 mb-3">
                                                <div class="col-md-3">
                                                    <div class="form-floating">
                                                        <input type="text" wire:model="newField.name" 
                                                            class="form-control @error('newField.name') is-invalid @enderror" 
                                                            placeholder="title"
                                                            id="field-name">
                                                        <label for="field-name">Alan Adı <span class="text-danger">*</span></label>
                                                    </div>
                                                    @error('newField.name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                                    <div class="form-hint">
                                                        <i class="fas fa-code me-1 text-blue"></i>
                                                        Sadece harfler, rakamlar ve alt çizgi (_) kullanın.
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-floating">
                                                        <input type="text" wire:model="newField.label" 
                                                            class="form-control @error('newField.label') is-invalid @enderror" 
                                                            placeholder="Başlık"
                                                            id="field-label">
                                                        <label for="field-label">Etiket <span class="text-danger">*</span></label>
                                                    </div>
                                                    @error('newField.label') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                                    <div class="form-hint">
                                                        <i class="fas fa-eye me-1 text-blue"></i>
                                                        Kullanıcıların göreceği alan adı.
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-floating">
                                                        <select wire:model.live="newField.type" 
                                                            class="form-select @error('newField.type') is-invalid @enderror"
                                                            id="field-type">
                                                            <option value="text">Metin</option>
                                                            <option value="textarea">Uzun Metin</option>
                                                            <option value="number">Sayı</option>
                                                            <option value="select">Seçim Kutusu</option>
                                                            <option value="checkbox">Onay Kutusu</option>
                                                            <option value="image">Resim</option>
                                                            <option value="url">URL</option>
                                                        </select>
                                                        <label for="field-type">Alan Tipi <span class="text-danger">*</span></label>
                                                    </div>
                                                    @error('newField.type') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-check form-switch pt-4">
                                                        <input type="checkbox" id="required" class="form-check-input" wire:model="newField.required">
                                                        <label class="form-check-label" for="required">
                                                            Zorunlu Alan
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <button type="button" class="btn btn-primary w-100 h-100" wire:click="addItemSchemaField">
                                                        <i class="fas fa-plus"></i>
                                                        <span class="d-none d-lg-inline ms-1">Ekle</span>
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            @if($newField['type'] === 'select')
                                            <div class="card mt-3">
                                                <div class="card-status-top bg-primary"></div>
                                                <div class="card-header">
                                                    <h3 class="card-title">Seçim Kutusu Seçenekleri</h3>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row g-2 mb-2">
                                                        <div class="col-md-4">
                                                            <label class="form-label">Seçenek Değeri</label>
                                                            <input type="text" wire:model="newOption.key" 
                                                                class="form-control" 
                                                                placeholder="Örn: red">
                                                            <div class="form-hint">Sistemin kullanacağı değer</div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label">Seçenek Etiketi</label>
                                                            <input type="text" wire:model="newOption.value" 
                                                                class="form-control" 
                                                                placeholder="Örn: Kırmızı">
                                                            <div class="form-hint">Kullanıcının göreceği metin</div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label class="form-label d-block">&nbsp;</label>
                                                            <button type="button" class="btn btn-success" wire:click="addFieldOption">
                                                                <i class="fas fa-plus me-1"></i> Seçenek Ekle
                                                            </button>
                                                        </div>
                                                    </div>
                                                    
                                                    @if(!empty($newField['options']))
                                                    <div class="table-responsive mt-3">
                                                        <table class="table table-vcenter card-table">
                                                            <thead>
                                                                <tr>
                                                                    <th>Değer</th>
                                                                    <th>Etiket</th>
                                                                    <th width="100"></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($newField['options'] as $key => $value)
                                                                <tr>
                                                                    <td><code>{{ $key }}</code></td>
                                                                    <td>{{ $value }}</td>
                                                                    <td>
                                                                        <button type="button" class="btn btn-sm btn-danger" wire:click="removeFieldOption('{{ $key }}')">
                                                                            <i class="fas fa-trash"></i>
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title">Tanımlı İçerik Alanları</h3>
                                        </div>
                                        <div class="card-body">
                                            @if(empty($widget['item_schema']))
                                            <div class="empty">
                                                <div class="empty-img">
                                                    <i class="fas fa-database fa-4x text-muted"></i>
                                                </div>
                                                <p class="empty-title">Henüz içerik alanı tanımlanmadı</p>
                                                <p class="empty-subtitle text-muted">
                                                    Yukarıdaki formu kullanarak widget içerikleri için veri alanları tanımlayabilirsiniz.
                                                </p>
                                            </div>
                                            @else
                                            <div class="table-responsive">
                                                <table class="table table-vcenter card-table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Alan Adı</th>
                                                            <th>Etiket</th>
                                                            <th>Tip</th>
                                                            <th>Zorunlu</th>
                                                            <th>Seçenekler</th>
                                                            <th width="100"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($widget['item_schema'] as $index => $field)
                                                        <tr>
                                                            <td><code>{{ $field['name'] }}</code></td>
                                                            <td>{{ $field['label'] }}</td>
                                                            <td>
                                                                <span class="badge bg-blue-lt">
                                                                    @switch($field['type'])
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
                                                                            <i class="fas fa-list me-1"></i> Seçim Kutusu
                                                                            @break
                                                                        @case('checkbox')
                                                                            <i class="fas fa-check-square me-1"></i> Onay Kutusu
                                                                            @break
                                                                        @case('image')
                                                                            <i class="fas fa-image me-1"></i> Resim
                                                                            @break
                                                                        @case('url')
                                                                            <i class="fas fa-link me-1"></i> URL
                                                                            @break
                                                                        @default
                                                                            {{ $field['type'] }}
                                                                    @endswitch
                                                                </span>
                                                            </td>
                                                            <td>
                                                                @if(isset($field['required']) && $field['required'])
                                                                <span class="badge bg-green">
                                                                    <i class="fas fa-check me-1"></i> Evet
                                                                </span>
                                                                @else
                                                                <span class="badge bg-gray">
                                                                    <i class="fas fa-minus me-1"></i> Hayır
                                                                </span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if(isset($field['options']) && is_array($field['options']))
                                                                <div class="d-flex flex-wrap gap-1">
                                                                    @foreach($field['options'] as $key => $value)
                                                                    <span class="badge bg-blue-lt">{{ $key }} = {{ $value }}</span>
                                                                    @endforeach
                                                                </div>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <button type="button" class="btn btn-sm btn-outline-danger" wire:click="removeItemSchemaField({{ $index }})">
                                                                    <i class="fas fa-trash me-1"></i> Sil
                                                                </button>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Özelleştirme Seçenekleri (Ayar Şeması) -->
            @if($formMode === 'settings')
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title d-flex align-items-center">
                                <i class="fas fa-sliders-h me-2"></i>
                                Özelleştirme Seçenekleri
                                <span class="badge bg-green ms-2">Ayarlar</span>
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <div class="d-flex">
                                    <div>
                                        <i class="fas fa-lightbulb text-blue me-2" style="margin-top: 3px"></i>
                                    </div>
                                    <div>
                                        <h4 class="alert-title">Özelleştirme Seçenekleri Nedir?</h4>
                                        <div class="text-muted">
                                            Özelleştirme seçenekleri, kullanıcıların widget'ı kişiselleştirebilmesi için ayarları belirler. Örneğin:<br>
                                            <ul class="mb-0">
                                                <li>Arkaplan rengi, yazı rengi, buton rengi gibi görsel ayarlar</li>
                                                <li>Başlık ve alt başlık gösterme/gizleme seçenekleri</li>
                                                <li>Slider hızı, otomatik oynatma gibi davranış ayarları</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <h3 class="card-title">Yeni Özelleştirme Seçeneği Ekle</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-3 mb-3">
                                                <div class="col-md-3">
                                                    <div class="form-floating">
                                                        <input type="text" wire:model="newField.name" 
                                                            class="form-control @error('newField.name') is-invalid @enderror" 
                                                            placeholder="background_color"
                                                            id="setting-name">
                                                        <label for="setting-name">Ayar Adı <span class="text-danger">*</span></label>
                                                    </div>
                                                    @error('newField.name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                                    <div class="form-hint">
                                                        <i class="fas fa-code me-1 text-blue"></i>
                                                        Sadece harfler, rakamlar ve alt çizgi (_) kullanın.
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-floating">
                                                        <input type="text" wire:model="newField.label" 
                                                            class="form-control @error('newField.label') is-invalid @enderror" 
                                                            placeholder="Arkaplan Rengi"
                                                            id="setting-label">
                                                        <label for="setting-label">Etiket <span class="text-danger">*</span></label>
                                                    </div>
                                                    @error('newField.label') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                                    <div class="form-hint">
                                                        <i class="fas fa-eye me-1 text-blue"></i>
                                                        Kullanıcıların göreceği ayar adı.
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-floating">
                                                        <select wire:model.live="newField.type" 
                                                            class="form-select @error('newField.type') is-invalid @enderror"
                                                            id="setting-type">
                                                            <option value="text">Metin</option>
                                                            <option value="textarea">Uzun Metin</option>
                                                            <option value="number">Sayı</option>
                                                            <option value="select">Seçim Kutusu</option>
                                                            <option value="checkbox">Onay Kutusu</option>
                                                            <option value="image">Resim</option>
                                                            <option value="url">URL</option>
                                                            <option value="color">Renk</option>
                                                        </select>
                                                        <label for="setting-type">Ayar Tipi <span class="text-danger">*</span></label>
                                                    </div>
                                                    @error('newField.type') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-check form-switch pt-4">
                                                        <input type="checkbox" id="setting-required" class="form-check-input" wire:model="newField.required">
                                                        <label class="form-check-label" for="setting-required">
                                                            Zorunlu Ayar
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <button type="button" class="btn btn-primary w-100 h-100" wire:click="addSettingsSchemaField">
                                                        <i class="fas fa-plus"></i>
                                                        <span class="d-none d-lg-inline ms-1">Ekle</span>
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            @if($newField['type'] === 'select')
                                            <div class="card mt-3">
                                                <div class="card-status-top bg-green"></div>
                                                <div class="card-header">
                                                    <h3 class="card-title">Seçim Kutusu Seçenekleri</h3>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row g-2 mb-2">
                                                        <div class="col-md-4">
                                                            <label class="form-label">Seçenek Değeri</label>
                                                            <input type="text" wire:model="newOption.key" 
                                                                class="form-control" 
                                                                placeholder="Örn: left">
                                                            <div class="form-hint">Sistemin kullanacağı değer</div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label">Seçenek Etiketi</label>
                                                            <input type="text" wire:model="newOption.value" 
                                                                class="form-control" 
                                                                placeholder="Örn: Sol Taraf">
                                                            <div class="form-hint">Kullanıcının göreceği metin</div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label class="form-label d-block">&nbsp;</label>
                                                            <button type="button" class="btn btn-success" wire:click="addFieldOption">
                                                                <i class="fas fa-plus me-1"></i> Seçenek Ekle
                                                            </button>
                                                        </div>
                                                    </div>
                                                    
                                                    @if(!empty($newField['options']))
                                                    <div class="table-responsive mt-3">
                                                        <table class="table table-vcenter card-table">
                                                            <thead>
                                                                <tr>
                                                                    <th>Değer</th>
                                                                    <th>Etiket</th>
                                                                    <th width="100"></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($newField['options'] as $key => $value)
                                                                <tr>
                                                                    <td><code>{{ $key }}</code></td>
                                                                    <td>{{ $value }}</td>
                                                                    <td>
                                                                        <button type="button" class="btn btn-sm btn-danger" wire:click="removeFieldOption('{{ $key }}')">
                                                                            <i class="fas fa-trash"></i>
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title">Tanımlı Özelleştirme Seçenekleri</h3>
                                        </div>
                                        <div class="card-body">
                                            @if(empty($widget['settings_schema']))
                                            <div class="empty">
                                                <div class="empty-img">
                                                    <i class="fas fa-sliders-h fa-4x text-muted"></i>
                                                </div>
                                                <p class="empty-title">Henüz özelleştirme seçeneği tanımlanmadı</p>
                                                <p class="empty-subtitle text-muted">
                                                    Yukarıdaki formu kullanarak widget için özelleştirme seçenekleri tanımlayabilirsiniz.
                                                </p>
                                            </div>
                                            @else
                                            <div class="table-responsive">
                                                <table class="table table-vcenter card-table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Ayar Adı</th>
                                                            <th>Etiket</th>
                                                            <th>Tip</th>
                                                            <th>Zorunlu</th>
                                                            <th>Seçenekler</th>
                                                            <th width="100"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($widget['settings_schema'] as $index => $field)
                                                        <tr>
                                                            <td><code>{{ $field['name'] }}</code></td>
                                                            <td>{{ $field['label'] }}</td>
                                                            <td>
                                                                <span class="badge bg-green-lt">
                                                                    @switch($field['type'])
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
                                                                            <i class="fas fa-list me-1"></i> Seçim Kutusu
                                                                            @break
                                                                        @case('checkbox')
                                                                            <i class="fas fa-check-square me-1"></i> Onay Kutusu
                                                                            @break
                                                                        @case('image')
                                                                            <i class="fas fa-image me-1"></i> Resim
                                                                            @break
                                                                        @case('url')
                                                                            <i class="fas fa-link me-1"></i> URL
                                                                            @break
                                                                        @case('color')
                                                                            <i class="fas fa-palette me-1"></i> Renk
                                                                            @break
                                                                        @default
                                                                            {{ $field['type'] }}
                                                                    @endswitch
                                                                </span>
                                                            </td>
                                                            <td>
                                                                @if(isset($field['required']) && $field['required'])
                                                                <span class="badge bg-green">
                                                                    <i class="fas fa-check me-1"></i> Evet
                                                                </span>
                                                                @else
                                                                <span class="badge bg-gray">
                                                                    <i class="fas fa-minus me-1"></i> Hayır
                                                                </span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if(isset($field['options']) && is_array($field['options']))
                                                                <div class="d-flex flex-wrap gap-1">
                                                                    @foreach($field['options'] as $key => $value)
                                                                    <span class="badge bg-green-lt">{{ $key }} = {{ $value }}</span>
                                                                    @endforeach
                                                                </div>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <button type="button" class="btn btn-sm btn-outline-danger" wire:click="removeSettingsSchemaField({{ $index }})">
                                                                    <i class="fas fa-trash me-1"></i> Sil
                                                                </button>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Önizleme -->
            @if($formMode === 'preview' && $widgetId)
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title d-flex align-items-center">
                                <i class="fas fa-eye me-2"></i>
                                Widget Önizleme
                            </h3>
                            <div class="card-actions">
                                <a href="{{ route('admin.widgetmanagement.preview', $widgetId) }}" class="btn btn-primary" target="_blank">
                                    <i class="fas fa-external-link-alt me-1"></i> Yeni Pencerede Aç
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info mb-3">
                                <div class="d-flex">
                                    <div>
                                        <i class="fas fa-info-circle text-blue me-2" style="margin-top: 3px"></i>
                                    </div>
                                    <div>
                                        <h4 class="alert-title">Önizleme Hakkında</h4>
                                        <div class="text-muted">
                                            Değişiklikleri görmek için önce kaydetmelisiniz. Önizleme, widgetın gerçek bir ortamdaki görünümünden farklılık gösterebilir.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex mb-3">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-outline-primary active">
                                        <i class="fas fa-desktop me-1"></i> Masaüstü
                                    </button>
                                    <button type="button" class="btn btn-outline-primary">
                                        <i class="fas fa-tablet-alt me-1"></i> Tablet
                                    </button>
                                    <button type="button" class="btn btn-outline-primary">
                                        <i class="fas fa-mobile-alt me-1"></i> Mobil
                                    </button>
                                </div>
                            </div>
                            
                            <div class="card">
                                <div class="card-body">
                                    <div class="widget-preview-frame border p-3 bg-light">
                                        <div class="preview-container">
                                            <style>
                                                {{ $widget['content_css'] }}
                                            </style>
                                            
                                            {!! $widget['content_html'] !!}
                                            
                                            <script>
                                                {{ $widget['content_js'] }}
                                            </script>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        <div class="card-footer d-flex justify-content-between">
            <a href="{{ route('admin.widgetmanagement.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Listeye Dön
            </a>
            <button type="button" class="btn btn-primary" wire:click="save" wire:loading.attr="disabled" wire:target="save">
                <div wire:loading.remove wire:target="save">
                    <i class="fas fa-save me-2"></i>
                    Kaydet
                </div>
                <div wire:loading wire:target="save">
                    <i class="fas fa-spinner fa-spin me-2"></i>
                    Kaydediliyor...
                </div>
            </button>
        </div>
    </div>
</div>