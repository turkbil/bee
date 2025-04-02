@include('widgetmanagement::helper')
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
                        <i class="fas fa-info-circle me-2"></i>
                        Bu alanda henüz widget bulunmuyor. 'Widget Ekle' butonunu kullanarak widget ekleyebilirsiniz.
                    </div>
                @else
                    <div class="widget-list" data-position="{{ $position }}">
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th style="width: 40px"></th>
                                        <th>Widget</th>
                                        <th>Önizleme</th>
                                        <th style="width: 200px">İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($widgets as $widget)
                                        <tr class="widget-item" data-id="{{ $widget->id }}">
                                            <td class="widget-drag-handle text-center">
                                                <i class="fas fa-grip-vertical"></i>
                                            </td>
                                            <td>
                                                <div class="widget-title fw-bold">
                                                    {{ optional($widget->widget)->name ?? 'Özel Widget' }}
                                                </div>
                                                <div class="text-muted small">
                                                    {{ optional($widget->widget)->description ?? 'Özel widget açıklaması' }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="widget-preview-frame">
                                                    @if(optional($widget->widget)->thumbnail)
                                                        <img src="{{ optional($widget->widget)->getThumbnailUrl() }}" 
                                                             alt="{{ optional($widget->widget)->name }}"
                                                             style="max-height: 60px;"
                                                             class="img-thumbnail">
                                                    @else
                                                        <div class="no-preview text-center">
                                                            <i class="fas fa-puzzle-piece text-muted"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-list">
                                                    <button class="btn btn-sm btn-icon btn-primary" 
                                                            wire:click="openWidgetSettings({{ $widget->id }})"
                                                            title="Ayarlar">
                                                        <i class="fas fa-cog"></i>
                                                    </button>
                                                    
                                                    @if(optional($widget->widget)->has_items)
                                                        <a href="{{ route('admin.widgetmanagement.items', $widget->id) }}"
                                                           class="btn btn-sm btn-icon btn-success"
                                                           title="Öğeler">
                                                            <i class="fas fa-list"></i>
                                                        </a>
                                                    @endif
                                                    
                                                    <a href="{{ route('admin.widgetmanagement.preview', $widget->widget_id) }}" 
                                                       class="btn btn-sm btn-icon btn-info"
                                                       target="_blank"
                                                       title="Önizleme">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    
                                                    <button class="btn btn-sm btn-icon btn-danger" 
                                                            wire:click="removeWidget({{ $widget->id }})"
                                                            onclick="return confirm('Bu widget\'ı kaldırmak istediğinize emin misiniz?');"
                                                            title="Kaldır">
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
                    const tbody = el.querySelector('tbody');
                    
                    if (tbody) {
                        const sortable = new Sortable(tbody, {
                            handle: '.widget-drag-handle',
                            animation: 150,
                            ghostClass: 'widget-ghost',
                            onEnd: function() {
                                // Sıralamayı güncelle
                                const items = Array.from(tbody.querySelectorAll('.widget-item')).map(item => item.dataset.id);
                                Livewire.dispatch('widgetOrderUpdated', items);
                            }
                        });
                        
                        sortables.push(sortable);
                    }
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