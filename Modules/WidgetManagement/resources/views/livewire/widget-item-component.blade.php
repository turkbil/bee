@include('widgetmanagement::helper')
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
                                    class="form-control @error('formData.' . $field['name']) is-invalid @enderror"
                                    rows="3"></textarea>
                            
                            @elseif($field['type'] === 'image')
                                <div class="image-upload-container">
                                    @if(isset($formData[$field['name']]) && is_string($formData[$field['name']]))
                                        <div class="current-image mb-2">
                                            <img src="{{ $formData[$field['name']] }}" alt="Current Image" class="img-thumbnail" style="max-height: 150px;">
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
                    
                    <div class="form-actions mt-4">
                        <button type="button" class="btn btn-primary" wire:click="saveItem">
                            <i class="fas fa-save me-1"></i> Kaydet
                        </button>
                        <button type="button" class="btn btn-secondary ms-2" wire:click="cancelForm">
                            <i class="fas fa-times me-1"></i> İptal
                        </button>
                    </div>
                </div>
            @else
                <!-- Öğe Listesi -->
                @if($items->isEmpty())
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Henüz öğe bulunmuyor. 'Yeni Öğe Ekle' butonunu kullanarak öğe ekleyebilirsiniz.
                    </div>
                @else
                    <div class="widget-items-list" id="widget-items-container">
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th style="width: 40px"></th>
                                        <th>Önizleme</th>
                                        <th>İçerik</th>
                                        <th style="width: 100px">İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $item)
                                        <tr class="widget-item-row" data-id="{{ $item->id }}">
                                            <td class="widget-item-drag-handle text-center">
                                                <i class="fas fa-grip-vertical"></i>
                                            </td>
                                            <td>
                                                <div class="widget-item-preview">
                                                    @if(isset($item->content['image']) && $item->content['image'])
                                                        <img src="{{ $item->content['image'] }}" alt="Preview" class="img-thumbnail" style="max-height: 80px;">
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="widget-item-details">
                                                    @if(isset($item->content['title']) && $item->content['title'])
                                                        <h5 class="mb-1">{{ $item->content['title'] }}</h5>
                                                    @endif
                                                    
                                                    @if(isset($item->content['description']) && $item->content['description'])
                                                        <p class="text-muted small mb-0">{{ Str::limit($item->content['description'], 100) }}</p>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-list">
                                                    <button class="btn btn-sm btn-icon btn-primary" wire:click="editItem({{ $item->id }})">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-icon btn-danger" wire:click="deleteItem({{ $item->id }})" onclick="return confirm('Bu öğeyi silmek istediğinize emin misiniz?');">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
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
                    itemsSortable = new Sortable(container.querySelector('tbody'), {
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
            if (document.getElementById('widget-items-container')) {
                initItemsSortable();
            }
            
            // Sayfa güncellendiğinde yeniden başlat
            Livewire.hook('element.updated', () => {
                if (document.getElementById('widget-items-container')) {
                    initItemsSortable();
                }
            });
        });
    </script>
    @endpush
</div>