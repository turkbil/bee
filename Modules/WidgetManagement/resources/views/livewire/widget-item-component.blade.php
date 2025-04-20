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
                <button class="btn btn-primary" wire:click="addItem">
                    <i class="fas fa-plus me-2"></i> Yeni İçerik Ekle
                </button>
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
                    <div class="col-12 mb-3">
                        <div class="form-group">
                            <label for="field-{{ $field['name'] }}" class="form-label{{ isset($field['required']) && $field['required'] ? ' required' : '' }}">
                                {{ $field['label'] }}
                                @if(isset($field['system']) && $field['system'] && $field['name'] != 'unique_id')
                                <span class="badge bg-primary ms-1">Sistem</span>
                                @endif
                            </label>
                            
                            @switch($field['type'])
                                @case('textarea')
                                    <div class="mb-3">
                                        <textarea wire:model="formData.{{ $field['name'] }}" class="form-control" rows="5" placeholder="Değeri buraya giriniz..."></textarea>
                                    </div>
                                    @break
                                
                                @case('select')
                                    @if(is_array($field['options']))
                                        <div class="mb-3">
                                            <select wire:model="formData.{{ $field['name'] }}" class="form-select">
                                                <option value="">Seçiniz</option>
                                                @foreach($field['options'] as $key => $label)
                                                    <option value="{{ $key }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif
                                    @break
                                
                                @case('file')
                                    <div class="form-group mb-3">
                                        @include('settingmanagement::livewire.partials.file-upload', [
                                            'fileKey' => $field['name'],
                                            'label' => 'Dosyayı sürükleyip bırakın veya tıklayın',
                                            'values' => $formData
                                        ])
                                    </div>
                                    @break

                                @case('image')
                                    <div class="form-group mb-3">
                                        @include('settingmanagement::livewire.partials.image-upload', [
                                            'imageKey' => $field['name'],
                                            'label' => 'Görseli sürükleyip bırakın veya tıklayın',
                                            'values' => $formData
                                        ])
                                    </div>
                                    @break
                                    
                                    @case('image_multiple')
                                    <div class="form-group mb-3">
                                        <!-- Mevcut Çoklu Resimler -->
                                        @php
                                            $fieldName = $field['name'];
                                            $currentImages = isset($formData[$fieldName]) && is_array($formData[$fieldName]) ? $formData[$fieldName] : [];
                                        @endphp
                                        
                                        <div class="mb-3">
                                            <label class="form-label">{{ $field['label'] }} - Yüklenen Görseller</label>
                                            <div class="row g-2">
                                                @foreach($currentImages as $imageIndex => $imagePath)
                                                    <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                                                        <div class="position-relative">
                                                            <div class="position-absolute top-0 end-0 p-1">
                                                                <button type="button" class="btn btn-danger btn-icon btn-sm"
                                                                        wire:click="removeExistingMultipleImage('{{ $fieldName }}', {{ $imageIndex }})"
                                                                        wire:confirm="Bu görseli silmek istediğinize emin misiniz?">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            </div>
                                                            
                                                            <a data-fslightbox="gallery-{{ $field['name'] }}" href="{{ cdn($imagePath) }}">
                                                                <div class="img-responsive img-responsive-1x1 rounded border" 
                                                                     style="background-image: url({{ cdn($imagePath) }})">
                                                                </div>
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endforeach
                                                
                                                <!-- Geçici yüklenen görseller -->
                                                @if(isset($photos[$fieldName]) && count($photos[$fieldName]) > 0)
                                                    @foreach($photos[$fieldName] as $index => $photo)
                                                        <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                                                            <div class="position-relative">
                                                                <div class="position-absolute top-0 end-0 p-1">
                                                                    <button type="button" class="btn btn-danger btn-icon btn-sm"
                                                                            wire:click="removePhoto('{{ $fieldName }}', {{ $index }})">
                                                                        <i class="fas fa-times"></i>
                                                                    </button>
                                                                </div>
                                                                <div class="img-responsive img-responsive-1x1 rounded border" 
                                                                     style="background-image: url({{ $photo->temporaryUrl() }})">
                                                                </div>
                                                                <!-- Progress bar'ı kaldırdık -->
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <!-- Yükleme Alanı -->
                                        <div class="card mt-3">
                                            <div class="card-body p-3">
                                                <form wire:submit="updatedTempPhoto">
                                                    <div class="dropzone p-4" onclick="document.getElementById('file-upload-{{ $fieldName }}').click()">
                                                        <input type="file" id="file-upload-{{ $fieldName }}" class="d-none" 
                                                            wire:model="tempPhoto" accept="image/*" multiple
                                                            wire:click="setPhotoField('{{ $fieldName }}')">
                                                            
                                                        <div class="text-center">
                                                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                                            <h4 class="text-muted">Görselleri sürükleyip bırakın veya tıklayın</h4>
                                                            <p class="text-muted small">PNG, JPG, WEBP, GIF - Maks 3MB - <strong>Toplu seçim yapabilirsiniz</strong></p>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @break

                                @case('checkbox')
                                    <div class="form-check form-switch">
                                        <input type="checkbox" id="field-{{ $field['name'] }}" class="form-check-input" wire:model.live="formData.{{ $field['name'] }}">
                                        <label class="form-check-label" for="field-{{ $field['name'] }}">
                                            {{ isset($formData[$field['name']]) && $formData[$field['name']] ? 'Evet' : 'Hayır' }}
                                        </label>
                                    </div>
                                    @break
                                
                                @case('color')
                                    <div class="mb-3">
                                        <label class="form-label">Renk seçimi</label>
                                        <input type="color" class="form-control form-control-color" 
                                            value="{{ $formData[$field['name']] ?? '#ffffff' }}" 
                                            wire:model.live="formData.{{ $field['name'] }}"
                                            title="Renk seçin">
                                    </div>
                                    @break
                                
                                @case('date')
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <i class="fas fa-calendar"></i>
                                        </span>
                                        <input type="date" class="form-control" wire:model.live="formData.{{ $field['name'] }}">
                                    </div>
                                    @break
                                
                                @case('time')
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <i class="fas fa-clock"></i>
                                        </span>
                                        <input type="time" class="form-control" wire:model.live="formData.{{ $field['name'] }}">
                                    </div>
                                    @break
                                
                                @case('number')
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <i class="fas fa-hashtag"></i>
                                        </span>
                                        <input type="number" class="form-control" wire:model.live="formData.{{ $field['name'] }}">
                                    </div>
                                    @break
                                
                                @case('email')
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <i class="fas fa-envelope"></i>
                                        </span>
                                        <input type="email" class="form-control" wire:model.live="formData.{{ $field['name'] }}">
                                    </div>
                                    @break
                                
                                @case('password')
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <i class="fas fa-key"></i>
                                        </span>
                                        <input type="password" class="form-control" wire:model.live="formData.{{ $field['name'] }}">
                                    </div>
                                    @break
                                
                                @case('tel')
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <i class="fas fa-phone"></i>
                                        </span>
                                        <input type="tel" class="form-control" wire:model.live="formData.{{ $field['name'] }}">
                                    </div>
                                    @break
                                
                                @case('url')
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <i class="fas fa-globe"></i>
                                        </span>
                                        <input type="url" class="form-control" wire:model.live="formData.{{ $field['name'] }}">
                                    </div>
                                    @break
                                
                                @default
                                    @if($field['name'] != 'unique_id')
                                        <div class="input-icon">
                                            <span class="input-icon-addon">
                                                <i class="fas fa-font"></i>
                                            </span>
                                            <input type="{{ $field['type'] }}" class="form-control" wire:model.live="formData.{{ $field['name'] }}">
                                        </div>
                                    @endif
                            @endswitch
                            
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

        <!-- İçerik Listesi -->
        <div class="row row-cards" id="sortable-list" data-sortable-id="items-container">
            @forelse($items as $item)
            <div class="col-12 col-sm-6 col-lg-4 widget-item-row" data-id="{{ $item->id }}" id="item-{{ $item->id }}">
                <div class="card mb-3">
                    <div class="card-status-top {{ isset($item->content['is_active']) && $item->content['is_active'] ? 'bg-primary' : 'bg-danger' }}"></div>
                    <div class="card-header widget-item-drag-handle cursor-move" wire:sortable.item="{{ $item->id }}">
                        <div class="d-flex align-items-center">
                            <div class="me-auto d-flex align-items-center">
                                <i class="fas fa-grip-vertical text-muted me-2"></i>
                                <h3 class="card-title mb-0">{{ $item->content['title'] ?? 'İçerik #' . $loop->iteration }}</h3>
                            </div>
                            <div class="dropdown position-absolute end-0 me-3">
                                <a href="#" class="btn btn-icon" data-bs-toggle="dropdown" aria-expanded="false">
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
                            <img src="{{ cdn($item->content['image']) }}" 
                                alt="{{ $item->content['title'] ?? 'İçerik görseli' }}" 
                                class="w-100 img-fluid"
                                style="max-height: 150px; object-fit: cover;">
                        </div>
                        @elseif(isset($item->content['image_url']) && $item->content['image_url'])
                        <div class="list-group-item p-0">
                            <img src="{{ cdn($item->content['image_url']) }}" 
                                alt="{{ $item->content['title'] ?? 'İçerik görseli' }}" 
                                class="w-100 img-fluid"
                                style="max-height: 150px; object-fit: cover;">
                        </div>
                        @else
                            @php
                                $multipleImageField = null;
                                $multipleImages = [];
                                
                                // İçeriğin tüm alanlarını kontrol et
                                foreach ($item->content as $fieldName => $fieldValue) {
                                    // Dizi olan ve boş olmayan alanları bul
                                    if (is_array($fieldValue) && !empty($fieldValue)) {
                                        // İlk elemanın string olup olmadığını kontrol et (görsel URL'si olmalı)
                                        $firstItem = reset($fieldValue);
                                        if (is_string($firstItem)) {
                                            $multipleImageField = $fieldName;
                                            $multipleImages = $fieldValue;
                                            break;
                                        }
                                    }
                                }
                            @endphp
                            
                            @if($multipleImageField && !empty($multipleImages))
                            <div class="list-group-item p-0">
                                <div class="d-flex overflow-auto">
                                    @foreach($multipleImages as $multipleImage)
                                        <img src="{{ cdn($multipleImage) }}" 
                                            alt="{{ $item->content['title'] ?? 'Çoklu görsel' }}" 
                                            class="img-fluid me-1"
                                            style="max-height: 150px; max-width: 150px; object-fit: cover;">
                                        @if($loop->iteration >= 3)
                                            <div class="d-flex align-items-center justify-content-center px-3">
                                                <span class="badge bg-blue">+{{ count($multipleImages) - 3 }} resim</span>
                                            </div>
                                            @break
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        @endif
                        
                        @if(isset($item->content['subtitle']) && $item->content['subtitle'])
                        <div class="list-group-item">
                            <div class="text-muted small">{{ Str::limit($item->content['subtitle'], 100) }}</div>
                        </div>
                        @elseif(isset($item->content['description']) && $item->content['description'])
                        <div class="list-group-item">
                            <div class="text-muted small">{{ Str::limit($item->content['description'], 100) }}</div>
                        </div>
                        @elseif(isset($item->content['uzun_metin']) && $item->content['uzun_metin'])
                        <div class="list-group-item">
                            <div class="text-muted small">{{ Str::limit($item->content['uzun_metin'], 100) }}</div>
                        </div>
                        @endif
                    </div>

                    <!-- Kart Footer -->
                    <div class="card-footer">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex gap-2">
                                <button class="btn btn-link text-body p-0" wire:click="editItem({{ $item->id }})">
                                    <i class="fas fa-edit me-1"></i> Düzenle
                                </button>
                            </div>
                            <div class="d-flex align-items-center">
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
                            </div>
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