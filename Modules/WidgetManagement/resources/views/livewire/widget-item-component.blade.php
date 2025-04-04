@include('widgetmanagement::helper')
<div class="card">
    <div class="card-body">
        <div class="row mb-3">
            <!-- Sol Taraf (Başlık ve Kontroller) -->
            <div class="col-md-8">
                <h3 class="card-title d-flex align-items-center mb-0">
                    <i class="fas fa-layer-group me-2"></i>
                    {{ $tenantWidget->settings['title'] ?? $tenantWidget->widget->name }} - İçerik Yönetimi
                </h3>
            </div>
            
            <!-- Ortadaki Loading -->
            <div class="col-md-2 position-relative d-flex justify-content-center align-items-center">
                <div wire:loading
                    wire:target="render, addItem, editItem, deleteItem, toggleItemActive, cancelForm, saveItem"
                    class="position-absolute top-50 start-50 translate-middle text-center"
                    style="width: 100%; max-width: 250px;">
                    <div class="small text-muted mb-2">Güncelleniyor...</div>
                    <div class="progress mb-1">
                        <div class="progress-bar progress-bar-indeterminate"></div>
                    </div>
                </div>
            </div>
            
            <!-- Sağ Taraf (Geri Dön) -->
            <div class="col-md-2 text-md-end">
                <a href="{{ route('admin.widgetmanagement.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Bileşenlere Dön
                </a>
            </div>
        </div>
        
        @if($formMode)
        <!-- İçerik Düzenleme Formu -->
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
                            <div class="pretty p-default p-curve p-toggle p-smooth">
                                <input type="checkbox" 
                                    wire:model.live="formData.{{ $field['name'] }}" 
                                    id="field-{{ $field['name'] }}" 
                                    value="1">
                                <div class="state p-success p-on ms-2">
                                    <label>Aktif</label>
                                </div>
                                <div class="state p-danger p-off ms-2">
                                    <label>Aktif Değil</label>
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
        @else
        <!-- İçerik Listesi (Dinamik widget'lar için) -->
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
        <div class="row row-cards" id="sortable-list" data-sortable-id="items-container">
            @forelse($items as $item)
            <div class="col-12 col-sm-6 col-lg-4 widget-item-row" data-id="{{ $item->id }}" id="item-{{ $item->id }}">
                <div class="card mb-3">
                    <div class="card-status-top {{ isset($item->content['is_active']) && $item->content['is_active'] ? 'bg-primary' : 'bg-danger' }}"></div>
                    <div class="card-header widget-item-drag-handle cursor-move">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-grip-vertical text-muted me-2"></i>
                                <h3 class="card-title mb-0">{{ $item->content['title'] ?? 'İçerik #' . $loop->iteration }}</h3>
                            </div>
                            <div class="dropdown">
                                <a href="#" class="btn-action" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <button class="dropdown-item" wire:click="editItem({{ $item->id }})">
                                        <i class="fas fa-edit me-2"></i> Düzenle
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
                    
                    <div class="list-group list-group-flush">
                        @if(isset($item->content['image']) && $item->content['image'])
                        <div class="list-group-item p-0">
                            <img src="{{ $item->content['image'] }}" 
                                alt="{{ $item->content['title'] ?? 'İçerik görseli' }}" 
                                class="w-100 img-fluid"
                                style="max-height: 150px; object-fit: cover;">
                        </div>
                        @elseif(isset($item->content['image_url']) && $item->content['image_url'])
                        <div class="list-group-item p-0">
                            <img src="{{ $item->content['image_url'] }}" 
                                alt="{{ $item->content['title'] ?? 'İçerik görseli' }}" 
                                class="w-100 img-fluid"
                                style="max-height: 150px; object-fit: cover;">
                        </div>
                        @endif
                        
                        @if(isset($item->content['subtitle']) && $item->content['subtitle'])
                        <div class="list-group-item">
                            <div class="text-muted small">{{ Str::limit($item->content['subtitle'], 100) }}</div>
                        </div>
                        @elseif(isset($item->content['description']) && $item->content['description'])
                        <div class="list-group-item">
                            <div class="text-muted small">{{ Str::limit($item->content['description'], 100) }}</div>
                        </div>
                        @endif
                    </div>
                    
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="pretty p-default p-curve p-toggle p-smooth">
                                <input type="checkbox" wire:click="toggleItemActive({{ $item->id }})"
                                    {{ isset($item->content['is_active']) && $item->content['is_active'] ? 'checked' : '' }} value="1" />
                                <div class="state p-success p-on ms-2">
                                    <label>Aktif</label>
                                </div>
                                <div class="state p-danger p-off ms-2">
                                    <label>Aktif Değil</label>
                                </div>
                            </div>
                            <button class="btn btn-primary btn-sm" wire:click="editItem({{ $item->id }})">
                                <i class="fas fa-edit me-1"></i> Düzenle
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="empty">
                    <div class="empty-img">
                        <img src="{{ asset('tabler/static/illustrations/undraw_quitting_time_dm8t.svg') }}"
                            height="128" alt="">
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