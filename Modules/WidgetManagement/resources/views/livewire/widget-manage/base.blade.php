<!-- Temel Bilgiler -->
<div class="tab-pane fade {{ $formMode === 'base' ? 'active show' : '' }}" id="tab-base">
    <div class="row">
        <div class="col-md-8">
            <div class="form-floating mb-3">
                <input type="text" wire:model.live="widget.name" 
                    class="form-control @error('widget.name') is-invalid @enderror"
                    placeholder="Widget adı" id="widget-name">
                <label for="widget-name">Widget Adı</label>
                @error('widget.name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            
            <div class="form-floating mb-3">
                <input type="text" wire:model="widget.slug" 
                    class="form-control @error('widget.slug') is-invalid @enderror"
                    placeholder="Slug" id="widget-slug">
                <label for="widget-slug">Benzersiz Tanımlayıcı (Slug)</label>
                @error('widget.slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <div class="form-hint">
                    <i class="fas fa-info-circle me-1 text-blue"></i>
                    Sadece küçük harfler, rakamlar ve tire (-) kullanın. Boşluk kullanmayın.
                </div>
            </div>
            
            <div class="form-floating mb-3">
                <select wire:model="widget.widget_category_id" class="form-select" id="widget-category">
                    <option value="">Kategori Seçiniz</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->widget_category_id }}">{{ $category->title }}</option>
                    @endforeach
                </select>
                <label for="widget-category">Kategori</label>
            </div>

            <div class="form-floating mb-3">
                <textarea wire:model="widget.description" 
                    class="form-control @error('widget.description') is-invalid @enderror" 
                    placeholder="Bu widget ne işe yarar? Kısaca açıklayın."
                    id="widget-description"
                    style="min-height: 120px"></textarea>
                <label for="widget-description">Açıklama</label>
                @error('widget.description') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            
            <div class="mb-3">
                <label class="form-label required">Widget Tipi</label>
                <div class="row g-3">
                    <div class="col-md-4 col-xl-4">
                        <label class="form-selectgroup-item">
                            <input type="radio" name="widget-type" value="static" wire:model="widget.type" class="form-selectgroup-input">
                            <span class="form-selectgroup-label d-flex align-items-center p-3">
                                <span class="me-3">
                                    <span class="form-selectgroup-check"></span>
                                </span>
                                <span class="form-selectgroup-label-content text-start">
                                    <span class="form-selectgroup-title strong mb-1">Statik</span>
                                    <span class="d-block text-muted">Sabit içerikli</span>
                                </span>
                            </span>
                        </label>
                    </div>
                    <div class="col-md-4 col-xl-4">
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
                    <div class="col-md-4 col-xl-4">
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
                    <div class="col-md-4 col-xl-4">
                        <label class="form-selectgroup-item">
                            <input type="radio" name="widget-type" value="file" wire:model="widget.type" class="form-selectgroup-input">
                            <span class="form-selectgroup-label d-flex align-items-center p-3">
                                <span class="me-3">
                                    <span class="form-selectgroup-check"></span>
                                </span>
                                <span class="form-selectgroup-label-content text-start">
                                    <span class="form-selectgroup-title strong mb-1">Dosya</span>
                                    <span class="d-block text-muted">Hazır view dosyası</span>
                                </span>
                            </span>
                        </label>
                    </div>
                </div>
                @error('widget.type') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
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
                            style="height: auto; min-height: 200px; cursor: pointer; border: 2px dashed #ccc;">
                            <input type="file" id="thumbnail-upload" wire:model="thumbnail" class="d-none" accept="image/*">
                            
                            @if($imagePreview)
                                <img src="{{ url($imagePreview) }}" class="img-fluid rounded" alt="Önizleme">
                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2" 
                                    wire:click.prevent="$set('thumbnail', null); $set('imagePreview', null)">
                                    <i class="fas fa-times"></i>
                                </button>
                            @elseif($widgetId && isset($widget['thumbnail']))
                                <img src="{{ url($widget['thumbnail']) }}" class="img-fluid rounded" alt="Mevcut görsel">
                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2" 
                                    wire:click.prevent="$set('widget.thumbnail', null); $set('imagePreview', null)">
                                    <i class="fas fa-times"></i>
                                </button>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-2"></i>
                                    <p class="mb-0">Görseli sürükleyin veya seçmek için tıklayın</p>
                                    <p class="text-muted small mb-0">PNG, JPG, GIF - Maks 3MB</p>
                                </div>
                            @endif
                        </div>
                        <div class="progress mt-2" wire:loading wire:target="thumbnail">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 100%"></div>
                        </div>
                        @error('thumbnail') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="mt-4">
                        <div class="form-label">Widget Seçenekleri</div>
                        
                        <div class="mb-2">
                            <label class="form-check form-switch">
                                <input type="checkbox" id="has_items" class="form-check-input" wire:model.live="widget.has_items">
                                <span class="form-check-label">İçerik Ekleme Özelliği</span>
                            </label>
                            <div class="form-hint ms-4">
                                <i class="fas fa-info-circle me-1 text-blue"></i>
                                Bu özellik ile kullanıcılar widgeta içerik ekleyebilirler
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
                                Sistem widget'ları tenant'lar tarafından silinemez
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>