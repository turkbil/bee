@include('widgetmanagement::helper')
<div>
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title d-flex align-items-center">
                    <i class="fas fa-layer-group me-2"></i>
                    {{ $tenantWidget->widget->name }} - İçerik Yönetimi
                </h3>
                <button class="btn btn-primary" wire:click="addNew">
                    <i class="fas fa-plus me-2"></i> Yeni İçerik Ekle
                </button>
            </div>
        </div>
        
        <div class="card-body">
            <!-- İçerik Ekleme Formu -->
            @if($formMode)
            <div class="widget-item-form">
                <div class="card">
                    <div class="card-status-start bg-primary"></div>
                    <div class="card-header">
                        <h3 class="card-title">{{ $currentItemId ? 'İçerik Düzenle' : 'Yeni İçerik Ekle' }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($schema as $field)
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="field-{{ $field['name'] }}" class="form-label{{ isset($field['required']) && $field['required'] ? ' required' : '' }}">
                                        {{ $field['label'] }}
                                    </label>
                                    
                                    @if($field['type'] === 'text')
                                    <input type="text" 
                                        wire:model="formData.{{ $field['name'] }}" 
                                        id="field-{{ $field['name'] }}" 
                                        class="form-control @error('formData.' . $field['name']) is-invalid @enderror"
                                        placeholder="{{ $field['label'] }}">
                                    
                                    @elseif($field['type'] === 'textarea')
                                    <textarea 
                                        wire:model="formData.{{ $field['name'] }}" 
                                        id="field-{{ $field['name'] }}" 
                                        class="form-control @error('formData.' . $field['name']) is-invalid @enderror"
                                        rows="4"
                                        placeholder="{{ $field['label'] }}"></textarea>
                                    
                                    @elseif($field['type'] === 'image')
                                    <div x-data="{ isUploading: false, progress: 0 }" 
                                        x-on:livewire-upload-start="isUploading = true"
                                        x-on:livewire-upload-finish="isUploading = false"
                                        x-on:livewire-upload-error="isUploading = false"
                                        x-on:livewire-upload-progress="progress = $event.detail.progress">
                                        
                                        <div class="form-control position-relative" 
                                            onclick="document.getElementById('field-{{ $field['name'] }}').click()"
                                            style="height: auto; min-height: 120px; cursor: pointer; border: 2px dashed #ccc;">
                                            
                                            <input type="file" 
                                                wire:model="formData.{{ $field['name'] }}" 
                                                id="field-{{ $field['name'] }}" 
                                                class="d-none"
                                                accept="image/*">
                                            
                                            @if(isset($formData[$field['name']]) && is_string($formData[$field['name']]))
                                                <img src="{{ $formData[$field['name']] }}" 
                                                    class="img-fluid rounded mx-auto d-block" 
                                                    style="max-height: 150px;">
                                            @elseif(isset($formData[$field['name']]) && !is_string($formData[$field['name']]))
                                                <img src="{{ $formData[$field['name']]->temporaryUrl() }}" 
                                                    class="img-fluid rounded mx-auto d-block" 
                                                    style="max-height: 150px;">
                                            @else
                                                <div class="text-center py-4">
                                                    <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-2"></i>
                                                    <p class="mb-0">Görseli sürükleyin veya seçmek için tıklayın</p>
                                                    <p class="text-muted small mb-0">PNG, JPG, WEBP, GIF - Maks 1MB</p>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <!-- Yükleme İlerleme Çubuğu -->
                                        <div x-show="isUploading" class="mt-2">
                                            <div class="progress">
                                                <div class="progress-bar progress-bar-indeterminate bg-primary"></div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    @elseif($field['type'] === 'url')
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <i class="fas fa-link"></i>
                                        </span>
                                        <input type="url" 
                                            wire:model="formData.{{ $field['name'] }}" 
                                            id="field-{{ $field['name'] }}" 
                                            class="form-control @error('formData.' . $field['name']) is-invalid @enderror"
                                            placeholder="https://...">
                                    </div>
                                    
                                    @elseif($field['type'] === 'checkbox')
                                    <label class="form-check form-switch">
                                        <input type="checkbox" 
                                            wire:model="formData.{{ $field['name'] }}" 
                                            id="field-{{ $field['name'] }}" 
                                            class="form-check-input @error('formData.' . $field['name']) is-invalid @enderror">
                                        <span class="form-check-label">{{ isset($formData[$field['name']]) && $formData[$field['name']] ? 'Evet' : 'Hayır' }}</span>
                                    </label>
                                    
                                    @elseif($field['type'] === 'select')
                                    <select 
                                        wire:model="formData.{{ $field['name'] }}" 
                                        id="field-{{ $field['name'] }}" 
                                        class="form-select @error('formData.' . $field['name']) is-invalid @enderror">
                                        <option value="">Seçiniz</option>
                                        @foreach($field['options'] ?? [] as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    
                                    @elseif($field['type'] === 'number')
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <i class="fas fa-hashtag"></i>
                                        </span>
                                        <input type="number" 
                                            wire:model="formData.{{ $field['name'] }}" 
                                            id="field-{{ $field['name'] }}" 
                                            class="form-control @error('formData.' . $field['name']) is-invalid @enderror">
                                    </div>
                                    @endif
                                    
                                    @error('formData.' . $field['name'])
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary" wire:click="cancelForm">
                            <i class="fas fa-times me-1"></i> İptal
                        </button>
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
            @else
            <!-- İçerik Listesi -->
            @if($items->isEmpty())
                <div class="empty">
                    <div class="empty-img">
                        <i class="fas fa-layer-group fa-4x text-muted"></i>
                    </div>
                    <p class="empty-title">Henüz içerik bulunmuyor</p>
                    <p class="empty-subtitle text-muted">
                        "Yeni İçerik Ekle" butonunu kullanarak widget içeriklerinizi oluşturun.
                    </p>
                    <div class="empty-action">
                        <button class="btn btn-primary" wire:click="addNew">
                            <i class="fas fa-plus me-2"></i> Yeni İçerik Ekle
                        </button>
                    </div>
                </div>
            @else
                <div class="alert alert-info mb-3">
                    <div class="d-flex">
                        <div>
                            <i class="fas fa-info-circle me-2" style="margin-top: 3px"></i>
                        </div>
                        <div>
                            İçerikleri sürükleyip bırakarak sıralayabilirsiniz. Sıralama otomatik olarak kaydedilecektir.
                        </div>
                    </div>
                </div>
                
                <div class="widget-items-list" id="widget-items-container">
                    <div class="row row-cards" data-sortable-id="items-container">
                        @foreach($items as $item)
                        <div class="col-md-6 col-xl-4 widget-item-row" data-id="{{ $item->id }}">
                            <div class="card">
                                <div class="card-status-top bg-primary"></div>
                                <div class="widget-item-drag-handle card-header cursor-move">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-grip-vertical text-muted me-2"></i>
                                            <span class="fw-bold">İçerik #{{ $loop->iteration }}</span>
                                        </div>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-ghost-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <button class="dropdown-item" wire:click="editItem({{ $item->id }})">
                                                    <i class="fas fa-edit me-2 text-primary"></i> Düzenle
                                                </button>
                                                <button class="dropdown-item text-danger" 
                                                    wire:click="deleteItem({{ $item->id }})"
                                                    onclick="return confirm('Bu içeriği silmek istediğinize emin misiniz?');">
                                                    <i class="fas fa-trash me-2"></i> Sil
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-3">
                                    <div class="widget-item-details">
                                        @if(isset($item->content['image']) && $item->content['image'])
                                        <div class="widget-item-preview text-center mb-3">
                                            <img src="{{ $item->content['image'] }}" 
                                                alt="Önizleme" 
                                                class="img-fluid rounded"
                                                style="max-height: 120px;">
                                        </div>
                                        @endif
                                        
                                        @if(isset($item->content['title']) && $item->content['title'])
                                        <h4 class="card-title mb-1">{{ $item->content['title'] }}</h4>
                                        @endif
                                        
                                        @if(isset($item->content['description']) && $item->content['description'])
                                        <p class="text-muted small mb-0">{{ Str::limit($item->content['description'], 100) }}</p>
                                        @endif
                                        
                                        <!-- Diğer içerik alanları -->
                                        <div class="widget-item-fields mt-2">
                                            @foreach($item->content as $key => $value)
                                                @if(!in_array($key, ['title', 'description', 'image']) && $value)
                                                    <div class="badge bg-blue-lt me-1 mb-1">
                                                        {{ $key }}: {{ is_string($value) ? Str::limit($value, 20) : ($value ? 'Evet' : 'Hayır') }}
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer d-flex justify-content-between">
                                    <button class="btn btn-sm btn-primary" wire:click="editItem({{ $item->id }})">
                                        <i class="fas fa-edit me-1"></i> Düzenle
                                    </button>
                                    <button class="btn btn-sm btn-danger" 
                                        wire:click="deleteItem({{ $item->id }})"
                                        onclick="return confirm('Bu içeriği silmek istediğinize emin misiniz?');">
                                        <i class="fas fa-trash me-1"></i> Sil
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            @endif
            @endif
        </div>
    </div>
    
    <!-- İçerik Sıralama JS -->
    @push('scripts')
    <script>
        document.addEventListener('livewire:init', function() {
            let itemsSortable;
            
            function initItemsSortable() {
                const container = document.querySelector('[data-sortable-id="items-container"]');
                
                if (container) {
                    itemsSortable = new Sortable(container, {
                        handle: '.widget-item-drag-handle',
                        animation: 150,
                        ghostClass: 'sortable-ghost',
                        onEnd: function() {
                            // Sıralamayı güncelle
                            const items = Array.from(container.querySelectorAll('.widget-item-row')).map(item => item.dataset.id);
                            Livewire.dispatch('itemOrderUpdated', items);
                        }
                    });
                }
            }
            
            // İlk yükleme
            if (document.querySelector('[data-sortable-id="items-container"]')) {
                initItemsSortable();
            }
            
            // Sayfa güncellendiğinde yeniden başlat
            Livewire.hook('element.updated', () => {
                if (document.querySelector('[data-sortable-id="items-container"]')) {
                    initItemsSortable();
                }
            });
        });
    </script>
    @endpush
</div>