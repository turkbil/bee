<div>
    @include('admin.partials.error_message')
    <form wire:submit.prevent="save">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-robot me-2"></i>AI Feature Yönetimi
                </h3>
            </div>
            <div class="card-body">
                <!-- Temel Bilgiler -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" wire:model="inputs.name" class="form-control @error('inputs.name') is-invalid @enderror" 
                                           placeholder="Özellik Adı" required>
                                    <label>Özellik Adı *</label>
                                    @error('inputs.name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" wire:model="inputs.slug" class="form-control @error('inputs.slug') is-invalid @enderror" 
                                           placeholder="URL Slug" required>
                                    <label>URL Slug *</label>
                                    @error('inputs.slug')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-floating mb-3">
                            <textarea wire:model="inputs.description" class="form-control @error('inputs.description') is-invalid @enderror" 
                                      placeholder="Açıklama" rows="3" required></textarea>
                            <label>Açıklama *</label>
                            @error('inputs.description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-floating mb-3">
                                    <input type="text" wire:model="inputs.emoji" class="form-control" placeholder="Emoji">
                                    <label>Emoji</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <input type="text" wire:model="inputs.icon" class="form-control" placeholder="FontAwesome Icon">
                                    <label>İkon</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating mb-3">
                                    <select wire:model="inputs.category" class="form-select @error('inputs.category') is-invalid @enderror" required>
                                        <option value="">Seçin</option>
                                        @foreach($this->getCategories() as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    <label>Kategori *</label>
                                    @error('inputs.category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating mb-3">
                                    <select wire:model="inputs.complexity_level" class="form-select">
                                        @foreach($this->getComplexityLevels() as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    <label>Zorluk Seviyesi</label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-floating mb-3">
                                    <select wire:model="inputs.status" class="form-select">
                                        <option value="active">Aktif</option>
                                        <option value="inactive">Pasif</option>
                                        <option value="beta">Beta</option>
                                        <option value="planned">Planlı</option>
                                    </select>
                                    <label>Durum</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating mb-3">
                                    <input type="number" wire:model="inputs.sort_order" class="form-control" min="1">
                                    <label>Sıralama</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating mb-3">
                                    <select wire:model="inputs.response_length" class="form-select">
                                        <option value="short">Kısa</option>
                                        <option value="medium">Orta</option>
                                        <option value="long">Uzun</option>
                                        <option value="variable">Değişken</option>
                                    </select>
                                    <label>Yanıt Uzunluğu</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating mb-3">
                                    <select wire:model="inputs.response_format" class="form-select">
                                        <option value="text">Metin</option>
                                        <option value="markdown">Markdown</option>
                                        <option value="structured">Yapılandırılmış</option>
                                        <option value="code">Kod</option>
                                        <option value="list">Liste</option>
                                        <option value="json">JSON</option>
                                    </select>
                                    <label>Yanıt Formatı</label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-check form-switch form-check-lg mb-3">
                                    <input class="form-check-input" type="checkbox" wire:model="inputs.is_featured">
                                    <label class="form-check-label">Öne Çıkan</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check form-switch form-check-lg mb-3">
                                    <input class="form-check-input" type="checkbox" wire:model="inputs.show_in_examples">
                                    <label class="form-check-label">Örneklerde Göster</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check form-switch form-check-lg mb-3">
                                    <input class="form-check-input" type="checkbox" wire:model="inputs.requires_input">
                                    <label class="form-check-label">Girdi Gerektirir</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check form-switch form-check-lg mb-3">
                                    <input class="form-check-input" type="checkbox" wire:model="inputs.is_system">
                                    <label class="form-check-label">Sistem Özelliği</label>
                                </div>
                            </div>
                        </div>

                <!-- Helper Sistemi -->
                <div class="row mt-4">
                    <div class="col-12">
                        <h5 class="text-primary border-bottom pb-2 mb-3">
                            <i class="fas fa-cog me-2"></i>Helper Sistemi
                        </h5>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <input type="text" wire:model="inputs.helper_function" class="form-control" 
                                   placeholder="ai_seo_content_generation">
                            <label>Helper Fonksiyonu</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <input type="text" wire:model="inputs.button_text" class="form-control">
                            <label>Buton Metni</label>
                        </div>
                    </div>
                </div>

                <div class="form-floating mb-3">
                    <textarea wire:model="helperDescription" class="form-control" rows="3" 
                              placeholder="Helper fonksiyonu açıklaması..."></textarea>
                    <label>Helper Açıklaması</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="text" wire:model="inputs.input_placeholder" class="form-control" 
                           placeholder="Placeholder metni...">
                    <label>Input Placeholder</label>
                </div>

                <!-- Prompt Yönetimi -->
                <div class="row mt-4">
                    <div class="col-12">
                        <h5 class="text-primary border-bottom pb-2 mb-3">
                            <i class="fas fa-comments me-2"></i>Prompt Yönetimi
                        </h5>
                    </div>
                </div>

                <div class="form-floating mb-3">
                    <textarea wire:model="quickPrompt" class="form-control" rows="3" 
                              placeholder="Bu özelliğin ne yapacağını kısaca açıklayın..."></textarea>
                    <label>Hızlı Prompt</label>
                    <small class="form-hint">AI'ya özelliğin ne yapacağını söyleyen kısa prompt</small>
                </div>

                <div class="form-floating mb-3">
                    <textarea wire:model="customPrompt" class="form-control" rows="4" 
                              placeholder="AI için detaylı yönergeler..."></textarea>
                    <label>Özel Prompt</label>
                    <small class="form-hint">Bu özellik için özelleştirilmiş detaylı prompt</small>
                </div>

                <!-- JSON Alanları -->
                <div class="row mt-4">
                    <div class="col-12">
                        <h5 class="text-primary border-bottom pb-2 mb-3">
                            <i class="fas fa-database me-2"></i>JSON Alanları
                        </h5>
                    </div>
                </div>

                        @php
                            $jsonFieldsToShow = [
                                'example_inputs' => 'Örnek Girdiler',
                                'helper_examples' => 'Helper Örnekleri', 
                                'helper_parameters' => 'Helper Parametreleri',
                                'helper_returns' => 'Helper Dönüş Değerleri',
                                'response_template' => 'Yanıt Şablonu',
                                'settings' => 'Özellik Ayarları',
                                'usage_examples' => 'Kullanım Örnekleri',
                                'additional_config' => 'Ek Konfigürasyon',
                                'input_validation' => 'Input Doğrulama',
                                'error_messages' => 'Hata Mesajları',
                                'success_messages' => 'Başarı Mesajları',
                                'token_cost' => 'Token Maliyeti'
                            ];
                        @endphp

                        @foreach($jsonFieldsToShow as $fieldKey => $fieldName)
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h3 class="card-title">{{ $fieldName }}</h3>
                                </div>
                                <div class="card-body">
                                    @php
                                        // JSON data'yı decode et
                                        $data = $jsonFields[$fieldKey] ?? [];
                                        
                                        // Eğer string ise decode et
                                        if (is_string($data)) {
                                            $decoded = json_decode($data, true);
                                            if (json_last_error() === JSON_ERROR_NONE) {
                                                $data = $decoded;
                                            } else {
                                                $data = [];
                                            }
                                        }
                                        
                                        if (!is_array($data)) {
                                            $data = [];
                                        }
                                    @endphp

                                    {{-- Düzenlenebilir Input Alanları --}}
                                    <div class="mb-4">
                                        <h5 class="text-primary">Düzenlenebilir Alanlar:</h5>
                                        
                                        @if(count($data) > 0)
                                            <div class="row sortable-container" id="editable-{{ $fieldKey }}" data-field="{{ $fieldKey }}">
                                                @foreach($data as $key => $value)
                                                    <div class="col-md-6 mb-3" data-item-key="{{ $key }}">
                                                        <div class="card">
                                                            <div class="card-header d-flex justify-content-between align-items-center">
                                                                <div class="d-flex align-items-center">
                                                                    <i class="fas fa-grip-vertical text-muted me-2 sortable-handle" style="cursor: grab;" title="Sürükleyip sıralayın"></i>
                                                                    <strong>{{ is_numeric($key) ? 'Öğe ' . ($key + 1) : ucfirst(str_replace('_', ' ', $key)) }}</strong>
                                                                </div>
                                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeItem('{{ $fieldKey }}', '{{ $key }}')">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </div>
                                                            <div class="card-body">
                                                                @if(is_array($value))
                                                                    @foreach($value as $subKey => $subValue)
                                                                        <div class="form-floating mb-3">
                                                                            @if(is_array($subValue))
                                                                                <textarea class="form-control" name="item_{{ $fieldKey }}_{{ $key }}_{{ $subKey }}" rows="3">{{ json_encode($subValue, JSON_UNESCAPED_UNICODE) }}</textarea>
                                                                            @else
                                                                                @if(strlen($subValue) > 100)
                                                                                    <textarea class="form-control" name="item_{{ $fieldKey }}_{{ $key }}_{{ $subKey }}" rows="3">{{ $subValue }}</textarea>
                                                                                @else
                                                                                    <input type="text" class="form-control" name="item_{{ $fieldKey }}_{{ $key }}_{{ $subKey }}" value="{{ $subValue }}">
                                                                                @endif
                                                                            @endif
                                                                            <label>{{ ucfirst(str_replace('_', ' ', $subKey)) }}</label>
                                                                        </div>
                                                                    @endforeach
                                                                @else
                                                                    <div class="form-floating mb-3">
                                                                        @if(strlen($value) > 100)
                                                                            <textarea class="form-control" name="item_{{ $fieldKey }}_{{ $key }}_value" rows="3">{{ $value }}</textarea>
                                                                        @else
                                                                            <input type="text" class="form-control" name="item_{{ $fieldKey }}_{{ $key }}_value" value="{{ $value }}">
                                                                        @endif
                                                                        <label>Değer</label>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            
                                            <div class="d-flex gap-2 mb-3">
                                                <button type="button" class="btn btn-outline-success" onclick="addNewItem('{{ $fieldKey }}')">
                                                    <i class="fas fa-plus me-2"></i>Yeni Öğe Ekle
                                                </button>
                                                <button type="button" class="btn btn-outline-info" onclick="generateFromTemplate('{{ $fieldKey }}')">
                                                    <i class="fas fa-magic me-2"></i>Şablondan Oluştur
                                                </button>
                                            </div>
                                        @else
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle me-2"></i>
                                                Bu alan için henüz veri eklenmemiş. 
                                                <button type="button" class="btn btn-sm btn-primary ms-2" onclick="addNewItem('{{ $fieldKey }}')">
                                                    İlk Öğeyi Ekle
                                                </button>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- JSONEditor (Gelişmiş Kullanıcılar İçin) --}}
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="form-label mb-0">Gelişmiş JSON Düzenleyici</label>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleJsonEditor('{{ $fieldKey }}')">
                                                <i class="fas fa-code me-1"></i>JSON Editör
                                            </button>
                                        </div>
                                        <div id="json-editor-{{ $fieldKey }}" style="display: none;">
                                            <div id="jsoneditor-{{ $fieldKey }}" style="width: 100%; height: 400px; border: 1px solid #ccc;"></div>
                                            <textarea class="form-control mt-2" name="json_{{ $fieldKey }}" rows="4" style="display: none;">{{ json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</textarea>
                                            <div class="mt-2">
                                                <button type="button" class="btn btn-sm btn-success" onclick="applyJsonChanges('{{ $fieldKey }}')">
                                                    <i class="fas fa-check me-1"></i>Değişiklikleri Uygula
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-warning" onclick="resetJsonEditor('{{ $fieldKey }}')">
                                                    <i class="fas fa-undo me-1"></i>Sıfırla
                                                </button>
                                            </div>
                                            <small class="form-hint">Gelişmiş JSON editörü - Tree view ile kolay düzenleme</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
            </div>

            <x-form-footer route="admin.ai.features" :model-id="$featureId" />
        </div>
    </form>
</div>

{{-- Push assets to head --}}
@push('styles')
    <link href="{{ asset('admin-assets/libs/jsoneditor/jsoneditor.min.css') }}" rel="stylesheet">
    <style>
        /* Sortable styles */
        .sortable-handle {
            cursor: grab !important;
        }
        .sortable-handle:active {
            cursor: grabbing !important;
        }
        .sortable-ghost {
            opacity: 0.4;
            background: #f8f9fa;
        }
        .sortable-chosen {
            transform: scale(1.02);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .sortable-drag {
            transform: rotate(5deg);
            opacity: 0.8;
        }
        .sortable-container {
            min-height: 60px;
            border: 2px dashed transparent;
            transition: border-color 0.3s ease;
        }
        .sortable-container.sortable-active {
            border-color: #007bff;
            background-color: rgba(0, 123, 255, 0.05);
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('admin-assets/libs/jsoneditor/jsoneditor.min.js') }}"></script>
    <script src="{{ asset('admin-assets/libs/sortable/sortable.min.js') }}"></script>
@endpush

{{-- JavaScript for the component --}}
<script>
        function deleteFeature() {
            if (confirm('Bu özelliği silmek istediğinizden emin misiniz?')) {
                // Livewire delete method call
                @this.call('delete');
            }
        }

        function previewFeature() {
            alert('Önizleme özelliği henüz hazır değil');
        }

        function resetForm() {
            if (confirm('Formu sıfırlamak istediğinizden emin misiniz?')) {
                // JSONEditor instance'larını temizle
                Object.keys(jsonEditors).forEach(fieldKey => {
                    if (jsonEditors[fieldKey]) {
                        jsonEditors[fieldKey].destroy();
                        delete jsonEditors[fieldKey];
                    }
                });
                location.reload();
            }
        }

        // JSONEditor instances storage
        const jsonEditors = {};
        
        // Sortable instances storage
        const sortableInstances = {};

        // Initialize Sortable for a container
        function initializeSortable(container) {
            if (!container) return;
            
            const fieldKey = container.dataset.field;
            if (!fieldKey) return;
            
            // Destroy existing sortable instance
            if (sortableInstances[fieldKey]) {
                sortableInstances[fieldKey].destroy();
            }
            
            // Create new sortable instance
            sortableInstances[fieldKey] = new Sortable(container, {
                handle: '.sortable-handle',
                animation: 150,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',
                onEnd: function(evt) {
                    // Update item order and re-number items
                    updateItemOrder(fieldKey, container);
                    showToast('Sıralama Güncellendi', `${fieldKey} öğeleri yeniden sıralandı`, 'info');
                }
            });
        }

        // Update item order after sorting
        function updateItemOrder(fieldKey, container) {
            const items = container.querySelectorAll('[data-item-key]');
            items.forEach((item, index) => {
                const titleElement = item.querySelector('.card-header strong');
                if (titleElement) {
                    const itemKey = item.dataset.itemKey;
                    const isNumeric = !isNaN(itemKey) || itemKey.startsWith('new_item_');
                    if (isNumeric) {
                        titleElement.textContent = `Öğe ${index + 1}`;
                    }
                }
            });
        }

        // Initialize all sortable containers on page load
        function initializeAllSortables() {
            const containers = document.querySelectorAll('.sortable-container');
            containers.forEach(container => {
                initializeSortable(container);
            });
        }

        // Toggle JSON editor visibility
        function toggleJsonEditor(fieldKey) {
            const editor = document.getElementById('json-editor-' + fieldKey);
            if (editor.style.display === 'none') {
                editor.style.display = 'block';
                initializeJsonEditor(fieldKey);
            } else {
                editor.style.display = 'none';
                if (jsonEditors[fieldKey]) {
                    jsonEditors[fieldKey].destroy();
                    delete jsonEditors[fieldKey];
                }
            }
        }

        // Initialize JSONEditor for a field
        function initializeJsonEditor(fieldKey) {
            if (jsonEditors[fieldKey]) {
                return; // Already initialized
            }

            const container = document.getElementById('jsoneditor-' + fieldKey);
            const textarea = document.querySelector(`textarea[name="json_${fieldKey}"]`);
            
            let initialJson;
            try {
                initialJson = JSON.parse(textarea.value || '{}');
            } catch (e) {
                initialJson = {};
            }

            const options = {
                mode: 'tree',
                modes: ['code', 'form', 'text', 'tree', 'view'],
                search: true,
                indentation: 2,
                onChange: function() {
                    // Auto-sync changes to textarea
                    try {
                        const json = jsonEditors[fieldKey].get();
                        textarea.value = JSON.stringify(json, null, 2);
                    } catch (e) {
                        console.warn('JSONEditor sync error:', e);
                    }
                }
            };

            jsonEditors[fieldKey] = new JSONEditor(container, options);
            jsonEditors[fieldKey].set(initialJson);
        }

        // Apply JSON changes from editor to form
        function applyJsonChanges(fieldKey) {
            if (!jsonEditors[fieldKey]) return;
            
            try {
                const json = jsonEditors[fieldKey].get();
                const textarea = document.querySelector(`textarea[name="json_${fieldKey}"]`);
                textarea.value = JSON.stringify(json, null, 2);
                
                // Show success message
                showToast('Başarılı', 'JSON değişiklikleri uygulandı', 'success');
            } catch (e) {
                showToast('Hata', 'Geçersiz JSON formatı: ' + e.message, 'error');
            }
        }

        // Reset JSON editor to original value
        function resetJsonEditor(fieldKey) {
            if (!jsonEditors[fieldKey]) return;
            
            const textarea = document.querySelector(`textarea[name="json_${fieldKey}"]`);
            try {
                const originalJson = JSON.parse(textarea.value || '{}');
                jsonEditors[fieldKey].set(originalJson);
                showToast('Sıfırlandı', 'JSON editör orijinal haline döndü', 'info');
            } catch (e) {
                jsonEditors[fieldKey].set({});
                showToast('Sıfırlandı', 'JSON editör boş obje ile sıfırlandı', 'info');
            }
        }

        // Simple toast notification
        function showToast(title, message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible position-fixed`;
            toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            toast.innerHTML = `
                <strong>${title}</strong> ${message}
                <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
            `;
            document.body.appendChild(toast);
            
            // Auto remove after 3 seconds
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.remove();
                }
            }, 3000);
        }

        // Add new item to field
        function addNewItem(fieldKey) {
            const container = document.getElementById('editable-' + fieldKey);
            if (!container) {
                // İlk öğe ise container'ı oluştur
                const parentDiv = container?.parentElement || document.querySelector(`[data-field="${fieldKey}"]`);
                if (parentDiv) {
                    const newContainer = document.createElement('div');
                    newContainer.className = 'row sortable-container';
                    newContainer.id = 'editable-' + fieldKey;
                    newContainer.setAttribute('data-field', fieldKey);
                    parentDiv.appendChild(newContainer);
                }
            }

            const itemCount = container?.children.length || 0;
            const newKey = 'new_item_' + Date.now();
            
            const itemHtml = `
                <div class="col-md-6 mb-3" data-item-key="${newKey}">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-grip-vertical text-muted me-2 sortable-handle" style="cursor: grab;" title="Sürükleyip sıralayın"></i>
                                <strong>Yeni Öğe ${itemCount + 1}</strong>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeItem('${fieldKey}', '${newKey}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" name="item_${fieldKey}_${newKey}_key" placeholder="Anahtar adı">
                                <label>Anahtar</label>
                            </div>
                            <div class="form-floating mb-3">
                                <textarea class="form-control" name="item_${fieldKey}_${newKey}_value" rows="2" placeholder="Değer girin"></textarea>
                                <label>Değer</label>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            if (container) {
                container.insertAdjacentHTML('beforeend', itemHtml);
                // Re-initialize sortable for the container
                initializeSortable(container);
            }
        }

        // Remove item
        function removeItem(fieldKey, itemKey) {
            if (confirm('Bu öğeyi silmek istediğinizden emin misiniz?')) {
                const item = document.querySelector(`[data-item-key="${itemKey}"]`);
                if (item) {
                    item.remove();
                }
            }
        }

        // Generate from template based on field type
        function generateFromTemplate(fieldKey) {
            const templates = {
                'helper_examples': {
                    'basic': {
                        'code': `ai_${fieldKey}_function('example_parameter')`,
                        'description': 'Temel kullanım örneği',
                        'estimated_tokens': 200
                    }
                },
                'helper_parameters': {
                    'input': 'Ana girdi parametresi',
                    'options': {
                        'language': 'Dil seçimi (tr, en)',
                        'format': 'Çıktı formatı'
                    }
                },
                'example_inputs': [
                    {
                        'text': 'Örnek girdi metni',
                        'label': 'Örnek Label'
                    }
                ],
                'response_template': {
                    'format': 'structured_text',
                    'sections': ['BAŞLIK:', 'İÇERİK:', 'SONUÇ:'],
                    'scoring': false
                }
            };

            const template = templates[fieldKey];
            if (template) {
                const jsonEditor = document.querySelector(`textarea[name="json_${fieldKey}"]`);
                if (jsonEditor) {
                    jsonEditor.value = JSON.stringify(template, null, 2);
                    document.getElementById('json-editor-' + fieldKey).style.display = 'block';
                }
            } else {
                alert('Bu alan için şablon mevcut değil');
            }
        }

        // Collect dynamic form data
        function collectDynamicData() {
            const dynamicData = {};
            
            // Her JSON field için item'ları topla
            const jsonFields = ['example_inputs', 'helper_examples', 'helper_parameters', 'helper_returns', 
                               'response_template', 'settings', 'usage_examples', 'additional_config',
                               'input_validation', 'error_messages', 'success_messages', 'token_cost'];
            
            jsonFields.forEach(fieldKey => {
                const container = document.getElementById('editable-' + fieldKey);
                const jsonEditorTextarea = document.querySelector(`textarea[name="json_${fieldKey}"]`);
                
                // Önce JSONEditor instance'dan kontrol et
                if (jsonEditors[fieldKey]) {
                    try {
                        dynamicData[fieldKey] = jsonEditors[fieldKey].get();
                        return;
                    } catch (e) {
                        console.warn('JSONEditor get error for', fieldKey, e);
                    }
                }
                
                // Sonra textarea'dan kontrol et
                if (jsonEditorTextarea && jsonEditorTextarea.value.trim()) {
                    try {
                        dynamicData[fieldKey] = JSON.parse(jsonEditorTextarea.value);
                        return;
                    } catch (e) {
                        console.warn('JSON parse error for', fieldKey, e);
                    }
                }
                
                // Dynamic item'ları topla (sortable order'a göre)
                if (container) {
                    const items = [];
                    const itemElements = container.querySelectorAll('[data-item-key]');
                    
                    itemElements.forEach((item, index) => {
                        const itemKey = item.dataset.itemKey;
                        const inputs = item.querySelectorAll('input, textarea, select');
                        const itemData = {};
                        
                        inputs.forEach(input => {
                            const name = input.name;
                            const match = name.match(new RegExp(`item_${fieldKey}_${itemKey}_(.*)`));
                            if (match) {
                                const subKey = match[1];
                                itemData[subKey] = input.value;
                            }
                        });
                        
                        if (Object.keys(itemData).length > 0) {
                            // Sort order'ı koru
                            itemData._sort_order = index;
                            items.push(itemData);
                        }
                    });
                    
                    dynamicData[fieldKey] = items;
                } else {
                    dynamicData[fieldKey] = {};
                }
            });
            
            return dynamicData;
        }

        // Form submit handler
        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Collect basic form data
            const formData = new FormData(this);
            const data = {};
            
            // Basic fields
            for (let [key, value] of formData.entries()) {
                if (!key.startsWith('json_') && !key.startsWith('item_')) {
                    // Checkbox'lar için özel işlem
                    if (this.querySelector(`input[name="${key}"][type="checkbox"]`)) {
                        data[key] = value === '1';
                    } else {
                        data[key] = value;
                    }
                }
            }
            
            // Collect dynamic JSON data
            const dynamicData = collectDynamicData();
            Object.assign(data, dynamicData);
            
            console.log('Form data prepared:', data);
            
            // Call Livewire save method
            @this.call('save', data, formData.has('save_and_return'));
        });

        // Initialize sortables when page loads
        document.addEventListener('DOMContentLoaded', function() {
            initializeAllSortables();
        });

        // Also initialize when Livewire updates the page
        document.addEventListener('livewire:load', function() {
            initializeAllSortables();
        });
</script>