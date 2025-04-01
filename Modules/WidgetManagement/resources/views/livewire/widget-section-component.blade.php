<div>
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title">{{ $positionLabels[$position] ?? 'Widget Alanı' }}</h3>
            <div class="card-actions">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addWidgetModal">
                    <i class="fas fa-plus me-1"></i> Widget Ekle
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="widget-container" id="widget-container-{{ $position }}">
                @if($widgets->isEmpty())
                    <div class="alert alert-info">
                        Bu alanda henüz widget bulunmuyor. 'Widget Ekle' butonunu kullanarak widget ekleyebilirsiniz.
                    </div>
                @else
                    <div class="widget-list" data-position="{{ $position }}">
                        @foreach($widgets as $widget)
                            <div class="widget-item" data-id="{{ $widget->id }}">
                                <div class="widget-item-inner">
                                    <div class="widget-item-header">
                                        <div class="widget-drag-handle">
                                            <i class="fas fa-grip-vertical"></i>
                                        </div>
                                        <div class="widget-title">
                                            {{ optional($widget->widget)->name ?? 'Özel Widget' }}
                                        </div>
                                        <div class="widget-actions">
                                            <button class="btn btn-sm btn-icon" wire:click="openWidgetSettings({{ $widget->id }})">
                                                <i class="fas fa-cog"></i>
                                            </button>
                                            @if(optional($widget->widget)->has_items)
                                                <button class="btn btn-sm btn-icon" data-bs-toggle="modal" data-bs-target="#widgetItemsModal" 
                                                    onclick="Livewire.dispatch('openWidgetItems', {{ $widget->id }})">
                                                    <i class="fas fa-list"></i>
                                                </button>
                                            @endif
                                            <button class="btn btn-sm btn-icon btn-danger" wire:click="removeWidget({{ $widget->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="widget-item-preview">
                                        <div class="widget-preview-frame">
                                            @if(optional($widget->widget)->thumbnail)
                                                <img src="{{ optional($widget->widget)->getThumbnailUrl() }}" alt="{{ optional($widget->widget)->name }}">
                                            @else
                                                <div class="no-preview">
                                                    <i class="fas fa-puzzle-piece"></i>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Widget Ekleme Modal -->
    <div class="modal fade" id="addWidgetModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Widget Ekle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                </div>
                <div class="modal-body">
                    <div class="row row-cards">
                        @foreach($availableWidgets as $widget)
                            <div class="col-md-4 col-sm-6">
                                <div class="card widget-card">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $widget->name }}</h5>
                                        <p class="card-text small">{{ $widget->description }}</p>
                                        <div class="widget-preview mb-3">
                                            <img src="{{ $widget->getThumbnailUrl() }}" alt="{{ $widget->name }}" class="img-fluid">
                                        </div>
                                        <button class="btn btn-primary w-100" wire:click="addWidget({{ $widget->id }})" data-bs-dismiss="modal">
                                            Ekle
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Widget JS -->
    @push('scripts')
    <script>
        document.addEventListener('livewire:init', function() {
            // Sortable.js kütüphanesi
            let sortables = [];
            
            function initSortable() {
                // Önceki sortable'ları temizle
                sortables.forEach(sortable => sortable.destroy());
                sortables = [];
                
                // Widget listelerinde sürükle-bırak
                document.querySelectorAll('.widget-list').forEach(el => {
                    const position = el.dataset.position;
                    
                    const sortable = new Sortable(el, {
                        handle: '.widget-drag-handle',
                        animation: 150,
                        ghostClass: 'widget-ghost',
                        onEnd: function() {
                            // Sıralamayı güncelle
                            const items = Array.from(el.querySelectorAll('.widget-item')).map(item => item.dataset.id);
                            Livewire.dispatch('widgetOrderUpdated', items);
                        }
                    });
                    
                    sortables.push(sortable);
                });
            }
            
            // İlk yükleme
            initSortable();
            
            // Sayfa güncellendiğinde yeniden başlat
            Livewire.hook('element.updated', (el, component) => {
                if (el.id === 'widget-container-{{ $position }}') {
                    initSortable();
                }
            });
        });
    </script>
    @endpush
</div>