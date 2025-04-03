@include('widgetmanagement::helper')
<div>
    <div class="card">
        <div class="card-body">
            <!-- Header Bölümü -->
            <div class="row mb-3 align-items-center">
                <!-- Sayfa/Modül/Konum Bilgisi -->
                <div class="col-md-5">
                    <h3 class="card-title mb-0">
                        @if($page)
                            <i class="fas fa-file-alt me-2"></i> Sayfa: {{ $page->title }}
                        @elseif($module)
                            <i class="fas fa-puzzle-piece me-2"></i> Modül: {{ $module }}
                        @endif
                        <span class="badge bg-blue ms-2">{{ $positionLabels[$position] ?? $position }}</span>
                    </h3>
                </div>
                
                <!-- Ortadaki Loading -->
                <div class="col-md-2 position-relative">
                    <div wire:loading
                        wire:target="addWidget, removeWidget, refreshWidgets, updateOrder" 
                        class="position-absolute top-50 start-50 translate-middle text-center"
                        style="width: 100%; max-width: 250px;">
                        <div class="progress">
                            <div class="progress-bar progress-bar-indeterminate"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Sağ Taraf Button -->
                <div class="col-md-5 text-md-end">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addWidgetModal">
                        <i class="fas fa-plus me-2"></i> Widget Ekle
                    </button>
                    
                    @if($page)
                    <a href="{{ route('admin.page.edit', $page->id) }}" class="btn btn-outline-secondary ms-2">
                        <i class="fas fa-arrow-left me-2"></i> Sayfaya Dön
                    </a>
                    @endif
                </div>
            </div>
            
            <!-- Alert Bölümü -->
            <div class="alert alert-info mb-4">
                <div class="d-flex">
                    <div>
                        <i class="fas fa-info-circle me-2" style="margin-top: 3px"></i>
                    </div>
                    <div>
                        <h4 class="alert-title">Bölüm Düzenleme</h4>
                        <p class="mb-0">Widgetları sürükleyip bırakarak sıralayabilirsiniz. Sıralama otomatik olarak kaydedilecektir.</p>
                    </div>
                </div>
            </div>
            
            <!-- Widgetlar -->
            <div class="row" id="sortable-list">
                @forelse($widgets as $widget)
                <div class="col-md-6 mb-3 widget-item" id="item-{{ $widget->id }}" data-id="{{ $widget->id }}">
                    <div class="card">
                        <div class="card-status-top {{ $widget->widget && $widget->widget->is_active ? 'bg-green' : 'bg-red' }}"></div>
                        <div class="card-header widget-drag-handle cursor-move pb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-grip-vertical text-muted me-2"></i>
                                    <h3 class="card-title mb-0">{{ optional($widget->widget)->name ?? 'Özel Widget' }}</h3>
                                </div>
                                <div class="dropdown">
                                    <a href="#" class="btn-action" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a href="{{ route('admin.widgetmanagement.settings', $widget->id) }}" class="dropdown-item">
                                            <i class="fas fa-cog me-2"></i> Ayarları Düzenle
                                        </a>
                                        
                                        @if(optional($widget->widget)->has_items)
                                        <a href="{{ route('admin.widgetmanagement.items', $widget->id) }}" class="dropdown-item">
                                            <i class="fas fa-layer-group me-2"></i> İçerik Yönet
                                        </a>
                                        @endif

<!-- Yapılandırma kısmı - sadece root erişebilir -->
                                        @if(auth()->user()->hasRole('root'))
                                        <a href="{{ route('admin.widgetmanagement.manage', optional($widget->widget)->id) }}" 
                                           class="dropdown-item" target="_blank">
                                            <i class="fas fa-cog me-2"></i> Yapılandır
                                        </a>
                                        @endif
                                                                                
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
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    @if(optional($widget->widget)->thumbnail)
                                        <img src="{{ optional($widget->widget)->getThumbnailUrl() }}" 
                                             alt="{{ optional($widget->widget)->name }}" 
                                             class="rounded" 
                                             style="width: 64px; height: 64px; object-fit: cover;">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center bg-secondary-subtle text-secondary rounded" 
                                             style="width: 64px; height: 64px;">
                                            <i class="fas fa-cube fa-2x"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="col">
                                    <div class="text-muted">{{ Str::limit(optional($widget->widget)->description, 100) }}</div>
                                    
                                    <div class="mt-2">
                                        @if(!empty($widget->settings['title']))
                                        <div><strong>Başlık:</strong> {{ $widget->settings['title'] }}</div>
                                        @endif
                                        
                                        @if(!empty($widget->items) && $widget->items->count() > 0)
                                        <div><strong>İçerik Sayısı:</strong> {{ $widget->items->count() }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="btn-list">
                                <a href="{{ route('admin.widgetmanagement.settings', $widget->id) }}" class="btn btn-outline-primary">
                                    <i class="fas fa-cog me-1"></i> Ayarlar
                                </a>
                                
                                @if(optional($widget->widget)->has_items)
                                <a href="{{ route('admin.widgetmanagement.items', $widget->id) }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-layer-group me-1"></i> İçerik
                                </a>
                                @endif
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
                            <input type="text" class="form-control" placeholder="Widget ara..." id="widget-search">
                        </div>
                    </div>
                    
                    <div class="row row-cards" id="widget-list">
                        @foreach($availableWidgets as $widget)
                        <div class="col-lg-6 mb-3 widget-list-item" data-name="{{ strtolower($widget->name) }}">
                            <div class="card h-100 widget-select-card">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            @if($widget->thumbnail)
                                                <img src="{{ $widget->getThumbnailUrl() }}" alt="{{ $widget->name }}" 
                                                     class="rounded" style="width: 48px; height: 48px; object-fit: cover;">
                                            @else
                                                <div class="d-flex align-items-center justify-content-center bg-secondary-subtle text-secondary rounded" 
                                                     style="width: 48px; height: 48px;">
                                                    <i class="fas fa-cube"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col">
                                            <h4 class="card-title mb-1">{{ $widget->name }}</h4>
                                            <div class="text-muted small">{{ Str::limit($widget->description, 60) }}</div>
                                            <div class="mt-2">
                                                <span class="badge bg-blue-lt">{{ $types[$widget->type] ?? $widget->type }}</span>
                                                @if($widget->has_items)
                                                <span class="badge bg-orange-lt">Dinamik İçerik</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('admin.widgetmanagement.preview', $widget->id) }}" 
                                           target="_blank" class="btn btn-outline-secondary btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button" class="btn btn-primary btn-sm" 
                                                wire:click="addWidget({{ $widget->id }})" 
                                                data-bs-dismiss="modal">
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
document.addEventListener('livewire:initialized', function() {
    // Widget Arama Filtrelemesi
    const searchInput = document.getElementById('widget-search');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const widgetItems = document.querySelectorAll('.widget-list-item');
            
            widgetItems.forEach(item => {
                const widgetName = item.dataset.name;
                if (widgetName.includes(searchTerm)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
    
    // Sürükle-Bırak Sıralama
    const sortableList = document.getElementById('sortable-list');
    if (sortableList) {
        new Sortable(sortableList, {
            handle: '.widget-drag-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            onEnd: function(evt) {
                // Değişim olup olmadığını kontrol et
                if (evt.oldIndex === evt.newIndex) {
                    return;
                }
                
                // Tüm öğelerin sırasını al
                const items = Array.from(sortableList.querySelectorAll('.widget-item')).map((item, index) => {
                    return {
                        value: item.getAttribute('data-id'),
                        order: index + 1
                    };
                });
                
                // Livewire'a gönder
                @this.updateOrder(items);
            }
        });
    }
});
</script>
@endpush