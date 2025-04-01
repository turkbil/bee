<div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ $tenantWidget->widget->name }} - Öğeler</h3>
            <div class="card-actions">
                <button class="btn btn-primary" wire:click="addNew">
                    <i class="fas fa-plus me-1"></i> Yeni Öğe Ekle
                </button>
            </div>
        </div>
        <div class="card-body">
            @if($formMode)
                <!-- Öğe Formu -->
                <div class="widget-item-form">
                    <h4>{{ $currentItemId ? 'Öğe Düzenle' : 'Yeni Öğe Ekle' }}</h4>
                    
                    @foreach($schema as $field)
                        <div class="form-group mb-3">
                            <label for="field-{{ $field['name'] }}">{{ $field['label'] }}</label>
                            
                            @if($field['type'] === 'text')
                                <input type="text" 
                                    wire:model="formData.{{ $field['name'] }}" 
                                    id="field-{{ $field['name'] }}" 
                                    class="form-control @error('formData.' . $field['name']) is-invalid @enderror">
                            
                            @elseif($field['type'] === 'textarea')
                                <textarea 
                                    wire:model="formData.{{ $field['name'] }}" 
                                    id="field-{{ $field['name'] }}" 
                                    class="form-control @error('formData.' . $field['name']) is-invalid @enderror"></textarea>
                            
                            @elseif($field['type'] === 'image')
                                <div class="image-upload-container">
                                    @if(isset($formData[$field['name']]) && is_string($formData[$field['name']]))
                                        <div class="current-image mb-2">
                                            <img src="{{ $formData[$field['name']] }}" alt="Current Image" class="img-thumbnail">
                                        </div>
                                    @endif
                                    
                                    <input type="file" 
                                        wire:model="formData.{{ $field['name'] }}" 
                                        id="field-{{ $field['name'] }}" 
                                        class="form-control @error('formData.' . $field['name']) is-invalid @enderror"
                                        accept="image/*">
                                </div>
                                
                                <div wire:loading wire:target="formData.{{ $field['name'] }}">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                    <span class="ms-1">Yükleniyor...</span>
                                </div>
                            
                            @elseif($field['type'] === 'url')
                                <input type="url" 
                                    wire:model="formData.{{ $field['name'] }}" 
                                    id="field-{{ $field['name'] }}" 
                                    class="form-control @error('formData.' . $field['name']) is-invalid @enderror">
                            
                            @elseif($field['type'] === 'checkbox')
                                <div class="form-check">
                                    <input type="checkbox" 
                                        wire:model="formData.{{ $field['name'] }}" 
                                        id="field-{{ $field['name'] }}" 
                                        class="form-check-input @error('formData.' . $field['name']) is-invalid @enderror">
                                    <label class="form-check-label" for="field-{{ $field['name'] }}">{{ $field['label'] }}</label>
                                </div>
                            
                            @elseif($field['type'] === 'select')
                                <select 
                                    wire:model="formData.{{ $field['name'] }}" 
                                    id="field-{{ $field['name'] }}" 
                                    class="form-select @error('formData.' . $field['name']) is-invalid @enderror">
                                    <option value="">Seçiniz</option>
                                    @foreach($field['options'] ?? [] as $option)
                                        <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                                    @endforeach
                                </select>
                            @endif
                            
                            @error('formData.' . $field['name'])
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @endforeach
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-primary" wire:click="saveItem">Kaydet</button>
                        <button type="button" class="btn btn-secondary" wire:click="cancelForm">İptal</button>
                    </div>
                </div>
            @else
                <!-- Öğe Listesi -->
                @if($items->isEmpty())
                    <div class="alert alert-info">
                        Henüz öğe bulunmuyor. 'Yeni Öğe Ekle' butonunu kullanarak öğe ekleyebilirsiniz.
                    </div>
                @else
                    <div class="widget-items-list" id="widget-items-container">
                        @foreach($items as $item)
                            <div class="widget-item-row" data-id="{{ $item->id }}">
                                <div class="widget-item-drag-handle">
                                    <i class="fas fa-grip-vertical"></i>
                                </div>
                                <div class="widget-item-content">
                                    <div class="widget-item-preview">
                                        @if(isset($item->content['image']) && $item->content['image'])
                                            <img src="{{ $item->content['image'] }}" alt="Preview" class="img-thumbnail">
                                        @endif
                                        
                                        <div class="widget-item-details">
                                            @if(isset($item->content['title']) && $item->content['title'])
                                                <h5>{{ $item->content['title'] }}</h5>
                                            @endif
                                            
                                            @if(isset($item->content['description']) && $item->content['description'])
                                                <p>{{ Str::limit($item->content['description'], 100) }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="widget-item-actions">
                                    <button class="btn btn-sm btn-icon" wire:click="editItem({{ $item->id }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-icon btn-danger" wire:click="deleteItem({{ $item->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif
        </div>
    </div>
    
    <!-- Widget Öğeleri JS -->
    @push('scripts')
    <script>
        document.addEventListener('livewire:init', function() {
            let itemsSortable;
            
            function initItemsSortable() {
                const container = document.getElementById('widget-items-container');
                
                if (container) {
                    itemsSortable = new Sortable(container, {
                        handle: '.widget-item-drag-handle',
                        animation: 150,
                        ghostClass: 'widget-item-ghost',
                        onEnd: function() {
                            // Sıralamayı güncelle
                            const items = Array.from(container.querySelectorAll('.widget-item-row')).map(item => item.dataset.id);
                            Livewire.dispatch('itemOrderUpdated', items);
                        }
                    });
                }
            }
            
            // İlk yükleme
            initItemsSortable();
            
            // Sayfa güncellendiğinde yeniden başlat
            Livewire.hook('element.updated', () => {
                initItemsSortable();
            });
        });
    </script>
    @endpush
</div>