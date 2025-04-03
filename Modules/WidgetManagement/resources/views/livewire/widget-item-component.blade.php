@include('widgetmanagement::helper')
<div>
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title d-flex align-items-center">
                    <i class="fas fa-layer-group me-2"></i>
                    {{ $tenantWidget->settings['title'] ?? $tenantWidget->widget->name }} - İçerik Yönetimi
                </h3>
                <div>
                    <a href="{{ route('admin.widgetmanagement.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Bileşenlere Dön
                    </a>
                </div>
            </div>
        </div>
        
        <div class="card-body p-4">
            @if($formMode)
            <!-- İçerik Düzenleme Formu -->
            <div class="widget-item-form">
                <div class="card">
                    <div class="card-status-start bg-primary"></div>
                    <div class="card-header">
                        <h3 class="card-title">{{ $isStaticWidget ? 'İçerik Düzenle' : ($currentItemId ? 'İçerik Düzenle' : 'Yeni İçerik Ekle') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach($schema as $field)
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="field-{{ $field['name'] }}" class="form-label{{ isset($field['required']) && $field['required'] ? ' required' : '' }}">
                                        {{ $field['label'] }}
                                        @if(isset($field['system']) && $field['system'])
                                        <span class="badge bg-primary ms-1">Sistem</span>
                                        @endif
                                    </label>
                                    
                                    @if($field['type'] === 'text')
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <i class="fas fa-font"></i>
                                        </span>
                                        <input type="text" 
                                            wire:model="formData.{{ $field['name'] }}" 
                                            id="field-{{ $field['name'] }}" 
                                            class="form-control @error('formData.' . $field['name']) is-invalid @enderror"
                                            placeholder="{{ $field['label'] }}">
                                    </div>
                                    
                                    @elseif($field['type'] === 'textarea')
                                    <textarea 
                                        wire:model="formData.{{ $field['name'] }}" 
                                        id="field-{{ $field['name'] }}" 
                                        class="form-control @error('formData.' . $field['name']) is-invalid @enderror"
                                        rows="4"
                                        placeholder="{{ $field['label'] }}"></textarea>
                                    
                                    @elseif($field['type'] === 'image')
                                    <div class="form-control p-3" style="height: auto;"
                                        onclick="document.getElementById('field-{{ $field['name'] }}').click()">
                                        <input type="file" 
                                            wire:model="formData.{{ $field['name'] }}" 
                                            id="field-{{ $field['name'] }}" 
                                            class="d-none"
                                            accept="image/*">
                                        
                                        @if(isset($formData[$field['name']]) && is_string($formData[$field['name']]))
                                            <img src="{{ $formData[$field['name']] }}" 
                                                class="img-fluid rounded mx-auto d-block mb-2" 
                                                style="max-height: 120px;">
                                        @elseif(isset($formData[$field['name']]) && !is_string($formData[$field['name']]))
                                            <img src="{{ $formData[$field['name']]->temporaryUrl() }}" 
                                                class="img-fluid rounded mx-auto d-block mb-2" 
                                                style="max-height: 120px;">
                                        @endif
                                        
                                        <div class="text-center">
                                            <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                            <p class="mb-0">Görseli sürükleyip bırakın veya seçmek için tıklayın</p>
                                            <p class="text-muted small">PNG, JPG, WEBP, GIF - Maks 1MB</p>
                                        </div>
                                    </div>
                                    
                                    @elseif($field['type'] === 'checkbox')
                                    <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                        <input type="checkbox" 
                                            wire:model.live="formData.{{ $field['name'] }}" 
                                            id="field-{{ $field['name'] }}" 
                                            value="1" {{ isset($formData[$field['name']]) && $formData[$field['name']] ? 'checked' : '' }}>
                                        <div class="state p-success p-on ms-2">
                                            <label>Aktif</label>
                                        </div>
                                        <div class="state p-danger p-off ms-2">
                                            <label>Pasif</label>
                                        </div>
                                    </div>
                                    
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
                        @if(!$isStaticWidget)
                        <button type="button" class="btn btn-outline-secondary" wire:click="cancelForm">
                            <i class="fas fa-times me-1"></i> İptal
                        </button>
                        @else
                        <div></div> <!-- Statik widget için boş div, solda buton olmasın -->
                        @endif
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
            <!-- İçerik Listesi (Dinamik widget'lar için) -->
            <!-- Bilgi Uyarısı -->
            <div class="alert alert-info mb-4">
                <div class="d-flex">
                    <div>
                        <i class="fas fa-info-circle me-2" style="margin-top: 3px"></i>
                    </div>
                    <div>
                        İçerikleri sürükleyip bırakarak sıralayabilirsiniz. Sıralama otomatik olarak kaydedilecektir.
                    </div>
                </div>
            </div>

            <!-- Yeni İçerik Ekleme Butonu -->
            <div class="mb-4">
                <button class="btn btn-primary" wire:click="addItem">
                    <i class="fas fa-plus me-2"></i> Yeni İçerik Ekle
                </button>
            </div>

            <!-- İçerik Listesi -->
            <div class="row g-4" id="sortable-list" data-sortable-id="items-container">
                @forelse($items as $item)
                <div class="col-md-6 col-lg-4 widget-item-row" data-id="{{ $item->id }}" id="item-{{ $item->id }}">
                    <div class="card h-100">
                        <div class="card-status-top {{ isset($item->content['is_active']) && $item->content['is_active'] ? 'bg-primary' : 'bg-danger' }}"></div>
                        <div class="card-header widget-item-drag-handle cursor-move py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-grip-vertical text-muted me-2"></i>
                                    <span class="fw-bold">{{ $item->content['title'] ?? 'İçerik #' . $loop->iteration }}</span>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-ghost-secondary" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <button class="dropdown-item" wire:click="editItem({{ $item->id }})">
                                            <i class="fas fa-edit me-2 text-primary"></i> Düzenle
                                        </button>
                                        <div class="dropdown-divider"></div>
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
                            @if(isset($item->content['image_url']) && $item->content['image_url'])
                            <div class="text-center mb-3">
                                <img src="{{ $item->content['image_url'] }}" 
                                    alt="Önizleme" 
                                    class="img-fluid rounded"
                                    style="max-height: 120px;">
                            </div>
                            @endif
                            
                            @if(isset($item->content['subtitle']) && $item->content['subtitle'])
                            <p class="text-muted mb-3">{{ Str::limit($item->content['subtitle'], 100) }}</p>
                            @endif
                        </div>
                        <div class="card-footer d-flex justify-content-between align-items-center">
                            <button 
                                class="btn {{ isset($item->content['is_active']) && $item->content['is_active'] ? 'btn-outline-success' : 'btn-outline-danger' }}"
                                wire:click="toggleItemActive({{ $item->id }})"
                                wire:loading.attr="disabled"
                                wire:target="toggleItemActive">
                                <div wire:loading wire:target="toggleItemActive({{ $item->id }})">
                                    <i class="fas fa-spinner fa-spin me-1"></i>
                                </div>
                                <div wire:loading.remove wire:target="toggleItemActive({{ $item->id }})">
                                    <i class="fas {{ isset($item->content['is_active']) && $item->content['is_active'] ? 'fa-check me-1' : 'fa-times me-1' }}"></i>
                                </div>
                                {{ isset($item->content['is_active']) && $item->content['is_active'] ? 'Aktif' : 'Pasif' }}
                            </button>
                            <button class="btn btn-primary" wire:click="editItem({{ $item->id }})">
                                <i class="fas fa-edit me-1"></i> Düzenle
                            </button>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="empty">
                        <div class="empty-img">
                            <i class="fas fa-layer-group fa-4x text-muted"></i>
                        </div>
                        <p class="empty-title">Henüz içerik bulunmuyor</p>
                        <p class="empty-subtitle text-muted">
                            "Yeni İçerik Ekle" butonunu kullanarak bileşen içeriklerinizi oluşturun.
                        </p>
                    </div>
                </div>
                @endforelse
            </div>
            @endif
        </div>
    </div>
    
    @push('scripts')
    <script src="{{ asset('admin/libs/sortable/sortable.min.js') }}"></script>
    <script>
        document.addEventListener('livewire:initialized', function() {
            let itemsSortable;
            
            function initItemsSortable() {
                const container = document.getElementById('sortable-list');
                const isStaticWidget = @json($isStaticWidget);
                
                if (container && !isStaticWidget) { // Statik widget ise sıralama özelliğini devre dışı bırak
                    // Mevcut sıralayıcıyı temizle (eğer varsa)
                    if (itemsSortable) {
                        itemsSortable.destroy();
                        itemsSortable = null;
                    }
                
                    itemsSortable = new Sortable(container, {
                        handle: '.widget-item-drag-handle',
                        animation: 150,
                        ghostClass: 'sortable-ghost',
                        onEnd: function(evt) {
                            // Değişim olup olmadığını kontrol et
                            if (evt.oldIndex === evt.newIndex) {
                                return;
                            }
                            
                            // TÜM öğeleri al, sadece taşınanı değil
                            const allItems = Array.from(container.querySelectorAll('.widget-item-row'))
                                .map(item => item.getAttribute('data-id'));
                            
                            if (allItems.length > 0) {
                                // Komple diziyi doğrudan Livewire component metoduna gönder
                                @this.updateItemOrder(allItems);
                            }
                        }
                    });
                }
            }
            
            // İlk yükleme
            initItemsSortable();
            
            // Sayfa güncellendiğinde yeniden başlat
            document.addEventListener('livewire:initialized', function() {
                initItemsSortable();
            });
            
            Livewire.hook('element.updated', () => {
                setTimeout(initItemsSortable, 100);
            });
        });
    </script>
    @endpush
</div>