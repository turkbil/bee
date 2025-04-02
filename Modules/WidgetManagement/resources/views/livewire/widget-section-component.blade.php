@include('widgetmanagement::helper')
<div>
    <div class="card">
        <div class="card-body">
            <!-- Header Bölümü -->
            <div class="row mb-3">
                <!-- Arama Kutusu -->
                <div class="col">
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" wire:model.live="search" class="form-control" 
                            placeholder="Widget ara...">
                    </div>
                </div>
                <!-- Ortadaki Loading -->
                <div class="col position-relative">
                    <div wire:loading
                        wire:target="addWidget, removeWidget, refreshWidgets, updateOrder" 
                        class="position-absolute top-50 start-50 translate-middle text-center"
                        style="width: 100%; max-width: 250px;">
                        <div class="small text-muted mb-2">Güncelleniyor...</div>
                        <div class="progress mb-1">
                            <div class="progress-bar progress-bar-indeterminate"></div>
                        </div>
                    </div>
                </div>
                <!-- Sağ Taraf -->
                <div class="col">
                    <div class="d-flex align-items-center justify-content-end gap-3">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addWidgetModal">
                            <i class="fas fa-plus me-2"></i> Widget Ekle
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Alert Bölümü -->
            <div class="alert alert-info bg-light-subtle border-light mb-3">
                <i class="fas fa-info-circle me-2"></i>
                Widgetları sürükleyip bırakarak sıralayabilirsiniz. Sıralama otomatik olarak kaydedilecektir.
            </div>
            
            <!-- Widgetlar -->
            <div class="row row-cards" id="sortable-list">
                @forelse($widgets as $widget)
                <div class="col-lg-6 widget-item" id="item-{{ $widget->id }}" data-id="{{ $widget->id }}">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-3 widget-drag-handle cursor-move">
                                    @if(optional($widget->widget)->thumbnail)
                                        <img src="{{ optional($widget->widget)->getThumbnailUrl() }}" alt="{{ optional($widget->widget)->name }}" class="rounded img-fluid">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center bg-secondary-subtle text-secondary rounded" style="width: 100%; height: 80px;">
                                            <i class="fas fa-cube fa-2x"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="col">
                                    <h3 class="card-title mb-1">
                                        <a href="{{ route('admin.widgetmanagement.items', $widget->id) }}" class="text-reset">
                                            {{ optional($widget->widget)->name ?? 'Özel Widget' }}
                                        </a>
                                    </h3>
                                    <div class="text-secondary">{{ Str::limit(optional($widget->widget)->description, 60) }}</div>
                                    <div class="mt-3 badges">
                                        <span class="badge badge-outline text-secondary fw-normal badge-pill">{{ optional($widget->widget)->type }}</span>
                                        
                                        @if(optional($widget->widget)->is_core)
                                        <span class="badge badge-outline text-secondary fw-normal badge-pill">
                                            <i class="fas fa-shield-alt me-1"></i> Sistem
                                        </span>
                                        @endif
                                        
                                        @if(optional($widget->widget)->has_items)
                                        <span class="badge badge-outline text-secondary fw-normal badge-pill">
                                            <i class="fas fa-layer-group me-1"></i> Dinamik
                                        </span>
                                        @endif
                                        
                                        @if($widget->settings)
                                        <span class="badge badge-outline text-secondary fw-normal badge-pill">
                                            <i class="fas fa-cog me-1"></i> Özelleştirilmiş
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <div class="dropdown">
                                        <a href="#" class="btn-action" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a href="{{ route('admin.widgetmanagement.settings', $widget->id) }}" class="dropdown-item">
                                                <i class="fas fa-cog me-2"></i> Yapıyı Düzenle
                                            </a>
                                            
                                            @if(optional($widget->widget)->has_items)
                                            <a href="{{ route('admin.widgetmanagement.items', $widget->id) }}" class="dropdown-item">
                                                <i class="fas fa-layer-group me-2"></i> İçerik Yönet
                                            </a>
                                            @endif
                                            
                                            <a href="{{ route('admin.widgetmanagement.preview', $widget->widget_id) }}" 
                                                class="dropdown-item"
                                                target="_blank">
                                                <i class="fas fa-eye me-2"></i> Önizle
                                            </a>
                                            
                                            <div class="dropdown-divider"></div>
                                            
                                            <a href="#" class="dropdown-item text-danger"
                                                    wire:click.prevent="removeWidget({{ $widget->id }})"
                                                    onclick="return confirm('Bu widget\'ı kaldırmak istediğinize emin misiniz?');">
                                                <i class="fas fa-trash me-2"></i> Kaldır
                                            </a>
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
                            <i class="fas fa-cube fa-3x text-muted mb-3"></i>
                        </div>
                        <p class="empty-title">Bu alanda henüz widget bulunmuyor</p>
                        <p class="empty-subtitle text-muted mb-3">
                            "Widget Ekle" butonunu kullanarak bu alana widget ekleyebilirsiniz.
                        </p>
                        <div class="empty-action">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addWidgetModal">
                                <i class="fas fa-plus me-2"></i> Widget Ekle
                            </button>
                        </div>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>
    
    <!-- Widget Ekleme Modal -->
    <div class="modal modal-blur fade" id="addWidgetModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Widget Seçin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                </div>
                <div class="modal-body">
                    <!-- Arama Filtresi -->
                    <div class="mb-3">
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" class="form-control" placeholder="Widget ara...">
                        </div>
                    </div>
                    
                    <div class="row row-cards">
                        @foreach($availableWidgets as $widget)
                        <div class="col-lg-6 mb-3">
                            <div class="card card-sm h-100">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            @if($widget->thumbnail)
                                                <img src="{{ $widget->getThumbnailUrl() }}" alt="{{ $widget->name }}" class="rounded" style="width: 48px; height: 48px; object-fit: cover;">
                                            @else
                                                <div class="d-flex align-items-center justify-content-center bg-secondary-subtle text-secondary rounded" style="width: 48px; height: 48px;">
                                                    <i class="fas fa-cube"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col">
                                            <h4 class="card-title mb-1">{{ $widget->name }}</h4>
                                            <div class="text-muted small">{{ Str::limit($widget->description, 60) }}</div>
                                            <div class="mt-2">
                                                <span class="badge badge-outline text-secondary fw-normal badge-pill">{{ $widget->type }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('admin.widgetmanagement.preview', $widget->id) }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button" class="btn btn-primary btn-sm" wire:click="addWidget({{ $widget->id }})" data-bs-dismiss="modal">
                                            <i class="fas fa-plus me-1"></i> Ekle
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('admin/libs/sortable/sortable.min.js') }}"></script>
<script>
// Debug yardımcı fonksiyonları
function debugLog(message, data = null) {
    const style = 'background: #0366d6; color: white; padding: 2px 5px; border-radius: 3px;';
    if (data) {
        console.log('%c[Widget Debug]', style, message, data);
    } else {
        console.log('%c[Widget Debug]', style, message);
    }
}

function debugError(message, error = null) {
    const style = 'background: #d73a49; color: white; padding: 2px 5px; border-radius: 3px;';
    if (error) {
        console.error('%c[Widget Error]', style, message, error);
    } else {
        console.error('%c[Widget Error]', style, message);
    }
}

document.addEventListener('livewire:initialized', function() {
    try {
        debugLog('Livewire initialized, setting up sortable...');
        initSortable();
        
        Livewire.on('refreshWidgets', () => {
            debugLog('Refreshing widgets...');
            setTimeout(() => {
                initSortable();
            }, 300);
        });
    } catch (error) {
        debugError('Error in initialization:', error);
    }
});

function initSortable() {
    try {
        const sortableList = document.getElementById('sortable-list');
        debugLog('Found sortable-list element:', sortableList);
        
        if (sortableList) {
            // Eğer zaten Sortable eklenmişse önce onu temizleyelim
            if (sortableList.sortable) {
                debugLog('Destroying existing sortable instance');
                sortableList.sortable.destroy();
            }
            
            const sortable = new Sortable(sortableList, {
                handle: '.widget-drag-handle',
                animation: 150,
                ghostClass: 'sortable-ghost',
                onStart: function(evt) {
                    try {
                        const item = evt.item;
                        const id = item.getAttribute('data-id');
                        debugLog('Drag started', { id, oldIndex: evt.oldIndex });
                    } catch (error) {
                        debugError('Error in onStart:', error);
                    }
                },
                onEnd: function(evt) {
                    try {
                        debugLog('Drag ended', { 
                            oldIndex: evt.oldIndex,
                            newIndex: evt.newIndex,
                            item: evt.item
                        });
                        
                        // Değişim olup olmadığını kontrol et
                        if (evt.oldIndex === evt.newIndex) {
                            debugLog('No change in order, skipping update');
                            return;
                        }
                        
                        // Tüm öğelerin sırasını al
                        const items = Array.from(sortableList.querySelectorAll('.widget-item')).map((item, index) => {
                            const id = item.getAttribute('data-id');
                            debugLog(`Processing item ${index + 1}`, { id, element: item });
                            
                            if (!id) {
                                debugError(`Missing data-id attribute for item at index ${index}`, item);
                            }
                            
                            return {
                                value: id,
                                order: index + 1
                            };
                        });
                        
                        debugLog('Final items array:', items);
                        
                        // Gönderilen yapıyı doğrulamak için log ekle
                        console.log('--- Sending to Livewire ---');
                        console.log(JSON.stringify(items, null, 2));
                        console.log('---------------------------');

                        if (items.length > 0) {
                            debugLog('Dispatching updateOrder event with items:', items);
                            // items dizisini 'list' anahtarıyla gönder
                            Livewire.dispatch('updateOrder', { list: items });
                        } else {
                            debugError('No items found to update order');
                        }
                    } catch (error) {
                        debugError('Error in onEnd:', error);
                    }
                }
            });
            
            // Referansı sakla
            sortableList.sortable = sortable;
            debugLog('Sortable initialized successfully');
        } else {
            debugError('Could not find sortable-list element');
        }
    } catch (error) {
        debugError('Error in initSortable:', error);
    }
}
</script>
@endpush