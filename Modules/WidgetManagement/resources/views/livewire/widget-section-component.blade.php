@include('widgetmanagement::helper')
<div>
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="card-title">
                        <i class="fas fa-columns me-2"></i>
                        {{ $positionLabels[$position] ?? 'Widget Alanı' }}
                    </h3>
                    @if($page)
                    <div class="text-muted">
                        Sayfa: {{ $page->title }}
                    </div>
                    @elseif($module)
                    <div class="text-muted">
                        Modül: {{ $module }}
                    </div>
                    @endif
                </div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addWidgetModal">
                    <i class="fas fa-plus me-2"></i> Widget Ekle
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="widget-container" id="widget-container-{{ $position }}">
                @if($widgets->isEmpty())
                    <div class="empty">
                        <div class="empty-img">
                            <i class="fas fa-puzzle-piece fa-4x text-muted"></i>
                        </div>
                        <p class="empty-title">Bu alanda henüz widget bulunmuyor</p>
                        <p class="empty-subtitle text-muted">
                            "Widget Ekle" butonunu kullanarak bu alana widget ekleyebilirsiniz.
                        </p>
                        <div class="empty-action">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addWidgetModal">
                                <i class="fas fa-plus me-2"></i> Widget Ekle
                            </button>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info mb-3">
                        <div class="d-flex">
                            <div>
                                <i class="fas fa-info-circle me-2" style="margin-top: 3px"></i>
                            </div>
                            <div>
                                Widgetları sürükleyip bırakarak sıralayabilirsiniz. Sıralama otomatik olarak kaydedilecektir.
                            </div>
                        </div>
                    </div>
                    
                    <div class="row row-cards" id="sortable-list">
                        @foreach($widgets as $widget)
                        <div class="col-md-6 widget-item" id="item-{{ $widget->id }}" data-id="{{ $widget->id }}">
                            <div class="card mb-3">
                                <div class="card-status-start bg-primary"></div>
                                <div class="widget-drag-handle card-header cursor-move">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-grip-vertical text-muted me-2"></i>
                                            <span class="fw-bold">{{ optional($widget->widget)->name ?? 'Özel Widget' }}</span>
                                        </div>
                                        <div class="widget-actions">
                                            <div class="btn-list">
                                                <button class="btn btn-sm btn-primary" 
                                                        wire:click="$dispatch('openWidgetSettings', {{ $widget->id }})"
                                                        title="Ayarlar">
                                                    <i class="fas fa-cog"></i>
                                                </button>
                                                
                                                @if(optional($widget->widget)->has_items)
                                                <a href="{{ route('admin.widgetmanagement.items', $widget->id) }}"
                                                    class="btn btn-sm btn-success"
                                                    title="İçerik Yönetimi">
                                                    <i class="fas fa-list"></i>
                                                </a>
                                                @endif
                                                
                                                <a href="{{ route('admin.widgetmanagement.preview', $widget->widget_id) }}" 
                                                    class="btn btn-sm btn-info"
                                                    target="_blank"
                                                    title="Önizleme">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                
                                                <button class="btn btn-sm btn-danger" 
                                                        wire:click="removeWidget({{ $widget->id }})"
                                                        onclick="return confirm('Bu widget\'ı kaldırmak istediğinize emin misiniz?');"
                                                        title="Kaldır">
                                                    <i class="fas fa-trash"></i>
                                                </button>
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
                                                    class="avatar avatar-lg"
                                                    style="object-fit: cover;">
                                            @else
                                                <span class="avatar avatar-lg bg-blue-lt">
                                                    <i class="fas fa-puzzle-piece"></i>
                                                </span>
                                            @endif
                                        </div>
                                        <div class="col">
                                            <div class="text-muted small">
                                                {{ optional($widget->widget)->description ?? 'Özel widget açıklaması' }}
                                            </div>
                                            
                                            <div class="mt-2">
                                                <span class="badge bg-blue">{{ optional($widget->widget)->type }}</span>
                                                @if(optional($widget->widget)->is_core)
                                                <span class="badge bg-purple">Sistem</span>
                                                @endif
                                                @if(optional($widget->widget)->has_items)
                                                <span class="badge bg-orange">Dinamik İçerik</span>
                                                @endif
                                                
                                                @if($widget->settings)
                                                <span class="badge bg-green">Özelleştirilmiş</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer d-flex justify-content-between align-items-center">
                                    <a href="{{ route('admin.widgetmanagement.settings', $widget->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-cog me-1"></i> Özellikleri Düzenle
                                    </a>
                                    
                                    @if(optional($widget->widget)->has_items)
                                    <a href="{{ route('admin.widgetmanagement.items', $widget->id) }}" class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-layer-group me-1"></i> İçerik Yönet
                                    </a>
                                    @endif
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
    <div class="modal modal-blur fade" id="addWidgetModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
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
                            <div class="col-sm-6 col-lg-4 mb-3">
                                <div class="card card-sm widget-select-card h-100">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-auto">
                                                <img src="{{ $widget->getThumbnailUrl() }}" 
                                                    alt="{{ $widget->name }}" 
                                                    class="rounded avatar avatar-md"
                                                    style="object-fit: cover;">
                                            </div>
                                            <div class="col">
                                                <h4 class="card-title mb-1">{{ $widget->name }}</h4>
                                                <div class="text-muted small">{{ Str::limit($widget->description, 80) }}</div>
                                                
                                                <div class="mt-2 d-flex gap-1">
                                                    <span class="badge bg-blue">{{ $types[$widget->type] ?? $widget->type }}</span>
                                                    @if($widget->is_core)
                                                    <span class="badge bg-purple">Sistem</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <button class="btn btn-primary w-100" wire:click="addWidget({{ $widget->id }})" data-bs-dismiss="modal">
                                            <i class="fas fa-plus me-1"></i> Bu Widget'ı Ekle
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
</div>

@push('scripts')
<script src="{{ asset('admin/libs/sortable/sortable.min.js') }}?v={{ filemtime(public_path('admin/libs/sortable/sortable.min.js')) }}"></script>
<script src="{{ asset('admin/libs/sortable/sortable-settings.js') }}?v={{ filemtime(public_path('admin/libs/sortable/sortable-settings.js')) }}"></script>
@endpush