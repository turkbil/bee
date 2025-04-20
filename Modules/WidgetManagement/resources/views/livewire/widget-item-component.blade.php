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
                    wire:target="render, deleteItem, toggleItemActive, updateItemOrder"
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
                <a href="{{ route('admin.widgetmanagement.content.create', $tenantWidgetId) }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> Yeni İçerik Ekle
                </a>
            </div>
        </div>
        
        <!-- İçerik Listesi -->
        <div class="row row-cards" id="sortable-list" data-sortable-id="items-container">
            @forelse($items as $item)
            <div class="col-12 widget-item-row" data-id="{{ $item->id }}" id="item-{{ $item->id }}">
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
                                    <a href="{{ route('admin.widgetmanagement.content.edit', $item->id) }}" class="dropdown-item">
                                        <i class="fas fa-edit me-2"></i> Düzenle
                                    </a>
                                    <button class="dropdown-item text-danger"
                                            wire:click="deleteItem({{ $item->id }})"
                                            onclick="return confirm('Bu içeriği silmek istediğinize emin misiniz?');">
                                        <i class="fas fa-trash me-2"></i> Sil
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                @if(isset($item->content['image']) && $item->content['image'])
                                <img src="{{ cdn($item->content['image']) }}" 
                                    alt="{{ $item->content['title'] ?? 'İçerik görseli' }}" 
                                    class="w-100 img-fluid rounded"
                                    style="max-height: 150px; object-fit: cover;">
                                @elseif(isset($item->content['image_url']) && $item->content['image_url'])
                                <img src="{{ cdn($item->content['image_url']) }}" 
                                    alt="{{ $item->content['title'] ?? 'İçerik görseli' }}" 
                                    class="w-100 img-fluid rounded"
                                    style="max-height: 150px; object-fit: cover;">
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
                                    <div class="d-flex overflow-auto">
                                        @foreach($multipleImages as $multipleImage)
                                            <img src="{{ cdn($multipleImage) }}" 
                                                alt="{{ $item->content['title'] ?? 'Çoklu görsel' }}" 
                                                class="img-fluid me-1 rounded"
                                                style="max-height: 150px; max-width: 150px; object-fit: cover;">
                                            @if($loop->iteration >= 3)
                                                <div class="d-flex align-items-center justify-content-center px-3">
                                                    <span class="badge bg-blue">+{{ count($multipleImages) - 3 }} resim</span>
                                                </div>
                                                @break
                                            @endif
                                        @endforeach
                                    </div>
                                    @else
                                    <div class="placeholder-image d-flex align-items-center justify-content-center bg-light rounded" style="height: 150px;">
                                        <i class="fas fa-image text-muted fa-3x"></i>
                                    </div>
                                    @endif
                                @endif
                            </div>
                            
                            <div class="col-md-9">
                                <div class="d-flex flex-column h-100">
                                    <div class="mb-2">
                                        <h4 class="mb-0">{{ $item->content['title'] ?? 'Başlıksız İçerik' }}</h4>
                                        
                                        @if(isset($item->content['subtitle']) && $item->content['subtitle'])
                                        <div class="text-muted small">{{ $item->content['subtitle'] }}</div>
                                        @endif
                                    </div>
                                    
                                    <div class="mb-3 flex-grow-1">
                                        @if(isset($item->content['description']) && $item->content['description'])
                                        <div class="text-muted">{{ Str::limit($item->content['description'], 200) }}</div>
                                        @elseif(isset($item->content['uzun_metin']) && $item->content['uzun_metin'])
                                        <div class="text-muted">{{ Str::limit($item->content['uzun_metin'], 200) }}</div>
                                        @endif
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center mt-auto">
                                        <div>
                                            <a href="{{ route('admin.widgetmanagement.content.edit', $item->id) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-edit me-1"></i> Düzenle
                                            </a>
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
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="empty">
                    <div class="empty-img">
                        <img src="{{ asset('images/empty.svg') }}"
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