<div>
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
            
            <!-- Konum Seçim Menüsü -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Konumlar</h3>
                        <p class="text-muted mb-0">Widget'ları eklemek istediğiniz konumu seçin</p>
                    </div>
                    <div class="mt-3">
                        <div class="btn-group w-100" role="group">
                            <!-- Genel Bakış Görünümü Butonu -->
                            <a href="{{ route('admin.widgetmanagement.section.overview') }}" 
                               class="btn {{ $showAllPositions ? 'btn-primary' : 'btn-outline-secondary' }}">
                               <i class="fas fa-th-large me-1"></i>
                                Genel Bakış
                            </a>
                            
                            <!-- Konum Butonları -->
                            @foreach($positionLabels as $posKey => $posLabel)
                                <a href="{{ route('admin.widgetmanagement.section.position', ['position' => $posKey]) }}" 
                                   class="btn {{ !$showAllPositions && $position == $posKey ? 'btn-primary' : 'btn-outline-secondary' }}">
                                   <i class="fas fa-{{ 
                                        $posKey == 'top' ? 'arrow-up' : 
                                        ($posKey == 'bottom' ? 'arrow-down' : 
                                        ($posKey == 'left' ? 'arrow-left' : 
                                        ($posKey == 'right' ? 'arrow-right' : 
                                        ($posKey == 'center-top' ? 'chevron-up' : 
                                        ($posKey == 'center-bottom' ? 'chevron-down' : 'dot-circle'))))) 
                                    }} me-1"></i>
                                    {{ $posLabel }}
                                </a>
                            @endforeach
                        </div>
                    </div>
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
            @if(!$showAllPositions)
                <!-- Tek Konum Görünümü -->
                <div class="row" id="sortable-list-{{ $position }}" data-position="{{ $position }}">
                    @forelse($widgets as $widget)
                    <div class="col-md-6 mb-3 widget-item" id="item-{{ $widget->id }}" data-id="{{ $widget->id }}" wire:key="widget-{{ $widget->id }}">
                        <div class="card">
                            <div class="card-status-top {{ $widget->widget && $widget->widget->is_active ? 'bg-green' : 'bg-red' }}"></div>
                            <div class="card-header widget-drag-handle cursor-move pb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-grip-vertical text-muted me-2"></i>
                                        <h3 class="card-title mb-0">{{ $widget->settings['title'] ?? (optional($widget->widget)->name ?? 'Bileşen') }}</h3>
                                    </div>
                                    <div class="dropdown">
                                        <a href="#" class="btn-action" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a href="{{ route('admin.widgetmanagement.settings', $widget->id) }}" class="dropdown-item">
                                                <i class="fas fa-cog me-2"></i> Ayarları Düzenle
                                            </a>
                                            
                                            <a href="{{ route('admin.widgetmanagement.items', $widget->id) }}" class="dropdown-item">
                                                <i class="fas fa-layer-group me-2"></i> İçerik Yönet
                                            </a>
                                        
                                            @if(auth()->user()->hasRole('root'))
                                            <a href="{{ route('admin.widgetmanagement.manage', optional($widget->widget)->id) }}" 
                                            class="dropdown-item">
                                                <i class="fas fa-tools me-2"></i> Yapılandır
                                            </a>
                                            @endif
                                                                        
                                            <div class="dropdown-divider"></div>
                                            
                                            <a href="#" class="dropdown-item text-danger" 
                                                    wire:click.prevent="removeWidget({{ $widget->id }})"
                                                    onclick="return confirm('Bu bileşeni kaldırmak istediğinize emin misiniz?');">
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
                                                style="width: 48px; height: 48px; object-fit: cover;">
                                        @else
                                            <div class="avatar bg-secondary-subtle text-secondary">
                                                <i class="fas fa-cube"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col">
                                        <div class="text-muted">{{ Str::limit(optional($widget->widget)->description, 60) }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('admin.widgetmanagement.settings', $widget->id) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-cog me-1"></i> Ayarlar
                                    </a>
                                    
                                    <a href="{{ route('admin.widgetmanagement.items', $widget->id) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-layer-group me-1"></i> İçerik Yönet
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12">
                        <div class="empty">
                            <p class="empty-title">Bu bölümde bileşen bulunmuyor</p>
                            <p class="empty-subtitle text-muted">
                                Bu bölüme henüz bileşen eklenmemiş. Bileşen eklemek için yukarıdaki "Bileşen Ekle" butonunu kullanabilirsiniz.
                            </p>
                        </div>
                    </div>
                    @endforelse
                </div>
            @else
            <!-- Genel Bakış Görünümü -->
            <div class="container-fluid px-0 my-4">
                <!-- Üst Alan -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-status-start bg-blue"></div>
                            <div class="card-body">
                                <h3 class="card-title d-flex align-items-center">
                                    <i class="fas fa-arrow-up me-2"></i>
                                    {{ $positionLabels['top'] ?? 'Üst Alan' }}
                                    <div class="ms-auto">
                                        <div class="dropdown d-inline">
                                            <a class="btn btn-sm btn-outline-primary dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                                <i class="fas fa-cog me-1"></i> İşlemler
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a href="{{ route('admin.widgetmanagement.section.position', ['position' => 'top']) }}" class="dropdown-item">
                                                    <i class="fas fa-external-link-alt me-1"></i> Detaylı Düzenle
                                                </a>
                                                <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#addWidgetModal"
                                                   onclick="window.widgetPositionToAdd = 'top'">
                                                    <i class="fas fa-plus me-1"></i> Widget Ekle
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </h3>
                                <div class="row" id="sortable-list-top" data-position="top">
                                    @forelse($allPositionsWidgets['top'] ?? [] as $widget)
                                    <div class="col-md-4 mb-2 widget-item" data-id="{{ $widget->id }}" wire:key="widget-overview-{{ $widget->id }}">
                                        <div class="card">
                                            <div class="card-body p-2">
                                                <div class="d-flex">
                                                    <div class="avatar avatar-sm me-2">
                                                        @if(optional($widget->widget)->thumbnail)
                                                        <span class="avatar-img rounded" style="background-image: url({{ $widget->widget->getThumbnailUrl() }})"></span>
                                                        @else
                                                        <span class="avatar-img bg-purple rounded">
                                                            <i class="fas fa-cube"></i>
                                                        </span>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <h6 class="m-0">{{ $widget->settings['title'] ?? (optional($widget->widget)->name ?? 'Widget') }}</h6>
                                                        <div class="text-muted small">{{ optional($widget->widget)->type ?? '' }}</div>
                                                    </div>
                                                    <div class="dropdown ms-auto">
                                                        <button class="btn btn-sm btn-ghost-secondary widget-drag-handle cursor-move" type="button">
                                                            <i class="fas fa-grip-vertical"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-ghost-secondary" type="button" data-bs-toggle="dropdown">
                                                            <i class="fas fa-ellipsis-v"></i>
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <a href="{{ route('admin.widgetmanagement.items', $widget->id) }}" class="dropdown-item">
                                                                <i class="fas fa-pen me-1"></i> Öğeleri Düzenle
                                                            </a>
                                                            <a href="#" class="dropdown-item" wire:click.prevent="deleteWidget('{{ $widget->id }}')" onclick="return confirm('Bu widget'ı silmek istediğinize emin misiniz?')">
                                                                <i class="fas fa-trash me-1 text-danger"></i> Sil
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @empty
                                    <div class="col-12">
                                        <div class="alert alert-info mb-0">
                                            <i class="fas fa-info-circle me-2"></i> Bu alanda widget bulunmuyor
                                        </div>
                                    </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                

                
                <!-- Orta Bölüm (Sol-Merkez-Sağ) -->
                <div class="row mb-4">
                    <!-- Sol Alan -->
                    <div class="col-md-3">
                        <div class="card h-100">
                            <div class="card-status-start bg-blue"></div>
                            <div class="card-body">
                                <h3 class="card-title d-flex align-items-center">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    {{ $positionLabels['left'] ?? 'Sol Alan' }}
                                    <div class="ms-auto">
                                        <div class="dropdown d-inline">
                                            <a class="btn btn-sm btn-outline-primary dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                                <i class="fas fa-cog me-1"></i> İşlemler
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a href="{{ route('admin.widgetmanagement.section.position', ['position' => 'left']) }}" class="dropdown-item">
                                                    <i class="fas fa-external-link-alt me-1"></i> Detaylı Düzenle
                                                </a>
                                                <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#addWidgetModal"
                                                   onclick="window.widgetPositionToAdd = 'left'">
                                                    <i class="fas fa-plus me-1"></i> Widget Ekle
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </h3>
                                <div class="row" id="sortable-list-left" data-position="left">
                                    @forelse($allPositionsWidgets['left'] ?? [] as $widget)
                                    <div class="col-12 mb-3 widget-item" data-id="{{ $widget->id }}" wire:key="widget-overview-{{ $widget->id }}">
                                        <div class="card">
                                            <div class="card-body p-3">
                                                <div class="d-flex">
                                                    <div class="avatar me-2">
                                                        @if(optional($widget->widget)->thumbnail)
                                                        <span class="avatar-img rounded" style="background-image: url({{ $widget->widget->getThumbnailUrl() }})"></span>
                                                        @else
                                                        <span class="avatar-img bg-blue-lt rounded">
                                                            <i class="fas fa-cube"></i>
                                                        </span>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <h4 class="m-0">{{ $widget->settings['title'] ?? (optional($widget->widget)->name ?? 'Widget') }}</h4>
                                                        <div class="text-muted small">{{ optional($widget->widget)->type ?? '' }}</div>
                                                    </div>
                                                    <div class="dropdown ms-auto">
                                                        <button class="btn btn-sm btn-ghost-secondary widget-drag-handle cursor-move" type="button">
                                                            <i class="fas fa-grip-vertical"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-ghost-secondary" type="button" data-bs-toggle="dropdown">
                                                            <i class="fas fa-ellipsis-v"></i>
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <a href="{{ route('admin.widgetmanagement.items', $widget->id) }}" class="dropdown-item">
                                                                <i class="fas fa-pen me-1"></i> Öğeleri Düzenle
                                                            </a>
                                                            <a href="#" class="dropdown-item" wire:click.prevent="deleteWidget('{{ $widget->id }}')" onclick="return confirm('Bu widget'ı silmek istediğinize emin misiniz?')">
                                                                <i class="fas fa-trash me-1 text-danger"></i> Sil
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @empty
                                    <div class="alert alert-info mb-0">
                                        <i class="fas fa-info-circle me-2"></i> Bu alanda widget bulunmuyor
                                    </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Merkez Alan (Tüm Merkez Bölümleri) -->
                    <div class="col-md-6">
                        <div class="card h-100 bg-light-lt">
                            <div class="card-status-top bg-purple"></div>
                            <div class="card-body p-3">
                                <h3 class="card-title d-flex align-items-center">
                                    <i class="fas fa-th-large me-2"></i>
                                    Merkez Bölgesi
                                </h3>
                                
                                <!-- Merkez-Üst Alan -->
                                <div class="mb-4 mt-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h4 class="mb-0">
                                            <i class="fas fa-chevron-up me-2 text-purple"></i>
                                            {{ $positionLabels['center-top'] ?? 'Merkez-Üst Alan' }}
                                        </h4>
                                        <div>
                                            <div class="dropdown d-inline">
                                                <a class="btn btn-sm btn-outline-purple dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                                    <i class="fas fa-cog me-1"></i> İşlemler
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a href="{{ route('admin.widgetmanagement.section.position', ['position' => 'center-top']) }}" class="dropdown-item">
                                                        <i class="fas fa-external-link-alt me-1"></i> Detaylı Düzenle
                                                    </a>
                                                    <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#addWidgetModal"
                                                       onclick="window.widgetPositionToAdd = 'center-top'">
                                                        <i class="fas fa-plus me-1"></i> Widget Ekle
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card card-body bg-purple-lt p-2">
                                        <div class="row" id="sortable-list-center-top" data-position="center-top">
                                            @forelse($allPositionsWidgets['center-top'] ?? [] as $widget)
                                            <div class="col-md-4 mb-2 widget-item" data-id="{{ $widget->id }}" wire:key="widget-overview-{{ $widget->id }}">
                                                <div class="card">
                                                    <div class="card-body p-2">
                                                        <div class="d-flex">
                                                            <div class="avatar avatar-sm me-2">
                                                                @if(optional($widget->widget)->thumbnail)
                                                                <span class="avatar-img rounded" style="background-image: url({{ $widget->widget->getThumbnailUrl() }})"></span>
                                                                @else
                                                                <span class="avatar-img bg-purple rounded">
                                                                    <i class="fas fa-cube"></i>
                                                                </span>
                                                                @endif
                                                            </div>
                                                            <div>
                                                                <h6 class="m-0">{{ $widget->settings['title'] ?? (optional($widget->widget)->name ?? 'Widget') }}</h6>
                                                                <div class="text-muted small">{{ optional($widget->widget)->type ?? '' }}</div>
                                                            </div>
                                                            <div class="dropdown ms-auto">
                                                                <button class="btn btn-sm btn-ghost-secondary widget-drag-handle cursor-move" type="button">
                                                                    <i class="fas fa-grip-vertical"></i>
                                                                </button>
                                                                <button class="btn btn-sm btn-ghost-secondary" type="button" data-bs-toggle="dropdown">
                                                                    <i class="fas fa-ellipsis-v"></i>
                                                                </button>
                                                                <div class="dropdown-menu dropdown-menu-end">
                                                                    <a href="{{ route('admin.widgetmanagement.items', $widget->id) }}" class="dropdown-item">
                                                                        <i class="fas fa-pen me-1"></i> Öğeleri Düzenle
                                                                    </a>
                                                                    <a href="#" class="dropdown-item" wire:click.prevent="deleteWidget('{{ $widget->id }}')" onclick="return confirm('Bu widget'ı silmek istediğinize emin misiniz?')">
                                                                        <i class="fas fa-trash me-1 text-danger"></i> Sil
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @empty
                                            <div class="col-12">
                                                <div class="alert alert-info py-2 mb-0">
                                                    <i class="fas fa-info-circle me-2"></i> Bu alanda widget bulunmuyor
                                                </div>
                                            </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Merkez-Alt Alan -->
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h4 class="mb-0">
                                            <i class="fas fa-chevron-down me-2 text-purple"></i>
                                            {{ $positionLabels['center-bottom'] ?? 'Merkez-Alt Alan' }}
                                        </h4>
                                        <div>
                                            <div class="dropdown d-inline">
                                                <a class="btn btn-sm btn-outline-purple dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                                    <i class="fas fa-cog me-1"></i> İşlemler
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a href="{{ route('admin.widgetmanagement.section.position', ['position' => 'center-bottom']) }}" class="dropdown-item">
                                                        <i class="fas fa-external-link-alt me-1"></i> Detaylı Düzenle
                                                    </a>
                                                    <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#addWidgetModal"
                                                       onclick="window.widgetPositionToAdd = 'center-bottom'">
                                                        <i class="fas fa-plus me-1"></i> Widget Ekle
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card card-body bg-purple-lt p-2">
                                        <div class="row" id="sortable-list-center-bottom" data-position="center-bottom">
                                            @forelse($allPositionsWidgets['center-bottom'] ?? [] as $widget)
                                            <div class="col-md-4 mb-2 widget-item" data-id="{{ $widget->id }}" wire:key="widget-overview-{{ $widget->id }}">
                                                <div class="card">
                                                    <div class="card-body p-2">
                                                        <div class="d-flex">
                                                            <div class="avatar avatar-sm me-2">
                                                                @if(optional($widget->widget)->thumbnail)
                                                                <span class="avatar-img rounded" style="background-image: url({{ $widget->widget->getThumbnailUrl() }})"></span>
                                                                @else
                                                                <span class="avatar-img bg-purple rounded">
                                                                    <i class="fas fa-cube"></i>
                                                                </span>
                                                                @endif
                                                            </div>
                                                            <div>
                                                                <h6 class="m-0">{{ $widget->settings['title'] ?? (optional($widget->widget)->name ?? 'Widget') }}</h6>
                                                                <div class="text-muted small">{{ optional($widget->widget)->type ?? '' }}</div>
                                                            </div>
                                                            <div class="dropdown ms-auto">
                                                                <button class="btn btn-sm btn-ghost-secondary widget-drag-handle cursor-move" type="button">
                                                                    <i class="fas fa-grip-vertical"></i>
                                                                </button>
                                                                <button class="btn btn-sm btn-ghost-secondary" type="button" data-bs-toggle="dropdown">
                                                                    <i class="fas fa-ellipsis-v"></i>
                                                                </button>
                                                                <div class="dropdown-menu dropdown-menu-end">
                                                                    <a href="{{ route('admin.widgetmanagement.items', $widget->id) }}" class="dropdown-item">
                                                                        <i class="fas fa-pen me-1"></i> Öğeleri Düzenle
                                                                    </a>
                                                                    <a href="#" class="dropdown-item" wire:click.prevent="deleteWidget('{{ $widget->id }}')" onclick="return confirm('Bu widget'ı silmek istediğinize emin misiniz?')">
                                                                        <i class="fas fa-trash me-1 text-danger"></i> Sil
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @empty
                                            <div class="col-12">
                                                <div class="alert alert-info py-2 mb-0">
                                                    <i class="fas fa-info-circle me-2"></i> Bu alanda widget bulunmuyor
                                                </div>
                                            </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sağ Alan -->
                    <div class="col-md-3">
                        <div class="card h-100">
                            <div class="card-status-start bg-orange"></div>
                            <div class="card-body">
                                <h3 class="card-title d-flex align-items-center">
                                    <i class="fas fa-arrow-right me-2"></i>
                                    {{ $positionLabels['right'] ?? 'Sağ Alan' }}
                                    <div class="ms-auto">
                                        <div class="dropdown d-inline">
                                            <a class="btn btn-sm btn-outline-primary dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                                <i class="fas fa-cog me-1"></i> İşlemler
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a href="{{ route('admin.widgetmanagement.section.position', ['position' => 'right']) }}" class="dropdown-item">
                                                    <i class="fas fa-external-link-alt me-1"></i> Detaylı Düzenle
                                                </a>
                                                <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#addWidgetModal"
                                                   onclick="window.widgetPositionToAdd = 'right'">
                                                    <i class="fas fa-plus me-1"></i> Widget Ekle
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </h3>
                                <div class="widgets-container" id="sortable-list-right" data-position="right">
                                    @forelse($allPositionsWidgets['right'] ?? [] as $widget)
                                    <div class="card mb-3 widget-item" data-id="{{ $widget->id }}" wire:key="widget-overview-{{ $widget->id }}">
                                        <div class="card-body p-3">
                                            <div class="d-flex">
                                                <div class="avatar me-2">
                                                    @if(optional($widget->widget)->thumbnail)
                                                    <span class="avatar-img rounded" style="background-image: url({{ $widget->widget->getThumbnailUrl() }})"></span>
                                                    @else
                                                    <span class="avatar-img bg-orange-lt rounded">
                                                        <i class="fas fa-cube"></i>
                                                    </span>
                                                    @endif
                                                </div>
                                                <div>
                                                    <h4 class="m-0">{{ $widget->settings['title'] ?? (optional($widget->widget)->name ?? 'Widget') }}</h4>
                                                    <div class="text-muted small">{{ optional($widget->widget)->type ?? '' }}</div>
                                                </div>
                                                <div class="dropdown ms-auto">
                                                    <button class="btn btn-sm btn-ghost-secondary widget-drag-handle cursor-move" type="button">
                                                        <i class="fas fa-grip-vertical"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-ghost-secondary" type="button" data-bs-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a href="{{ route('admin.widgetmanagement.items', $widget->id) }}" class="dropdown-item">
                                                            <i class="fas fa-pen me-1"></i> Öğeleri Düzenle
                                                        </a>
                                                        <a href="#" class="dropdown-item" wire:click.prevent="deleteWidget('{{ $widget->id }}')" onclick="return confirm('Bu widget'ı silmek istediğinize emin misiniz?')">
                                                            <i class="fas fa-trash me-1 text-danger"></i> Sil
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @empty
                                    <div class="alert alert-info mb-0">
                                        <i class="fas fa-info-circle me-2"></i> Bu alanda widget bulunmuyor
                                    </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                

                
                <!-- Alt Alan -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-status-start bg-red"></div>
                            <div class="card-body">
                                <h3 class="card-title d-flex align-items-center">
                                    <i class="fas fa-arrow-down me-2"></i>
                                    {{ $positionLabels['bottom'] ?? 'Alt Alan' }}
                                    <div class="ms-auto">
                                        <div class="dropdown d-inline">
                                            <a class="btn btn-sm btn-outline-primary dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                                <i class="fas fa-cog me-1"></i> İşlemler
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a href="{{ route('admin.widgetmanagement.section.position', ['position' => 'bottom']) }}" class="dropdown-item">
                                                    <i class="fas fa-external-link-alt me-1"></i> Detaylı Düzenle
                                                </a>
                                                <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#addWidgetModal"
                                                   onclick="window.widgetPositionToAdd = 'bottom'">
                                                    <i class="fas fa-plus me-1"></i> Widget Ekle
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </h3>
                                <div class="row" id="sortable-list-bottom" data-position="bottom">
                                    @forelse($allPositionsWidgets['bottom'] ?? [] as $widget)
                                    <div class="col-md-4 mb-3 widget-item" data-id="{{ $widget->id }}" wire:key="widget-overview-{{ $widget->id }}">
                                        <div class="card">
                                            <div class="card-body p-3">
                                                <div class="d-flex">
                                                    <div class="avatar me-2">
                                                        @if(optional($widget->widget)->thumbnail)
                                                        <span class="avatar-img rounded" style="background-image: url({{ $widget->widget->getThumbnailUrl() }})"></span>
                                                        @else
                                                        <span class="avatar-img bg-red-lt rounded">
                                                            <i class="fas fa-cube"></i>
                                                        </span>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <h4 class="m-0">{{ $widget->settings['title'] ?? (optional($widget->widget)->name ?? 'Widget') }}</h4>
                                                        <div class="text-muted small">{{ optional($widget->widget)->type ?? '' }}</div>
                                                    </div>
                                                    <div class="dropdown ms-auto">
                                                        <button class="btn btn-sm btn-ghost-secondary widget-drag-handle cursor-move" type="button">
                                                            <i class="fas fa-grip-vertical"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-ghost-secondary" type="button" data-bs-toggle="dropdown">
                                                            <i class="fas fa-ellipsis-v"></i>
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <a href="{{ route('admin.widgetmanagement.items', $widget->id) }}" class="dropdown-item">
                                                                <i class="fas fa-pen me-1"></i> Öğeleri Düzenle
                                                            </a>
                                                            <a href="#" class="dropdown-item" wire:click.prevent="deleteWidget('{{ $widget->id }}')" onclick="return confirm('Bu widget'ı silmek istediğinize emin misiniz?')">
                                                                <i class="fas fa-trash me-1 text-danger"></i> Sil
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @empty
                                    <div class="col-12">
                                        <div class="alert alert-info mb-0">
                                            <i class="fas fa-info-circle me-2"></i> Bu alanda widget bulunmuyor
                                        </div>
                                    </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
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
                                                <span class="badge bg-blue-lt">
                                                    @switch($widget->type)
                                                        @case('static')
                                                            Statik
                                                            @break
                                                        @case('dynamic')
                                                            Dinamik
                                                            @break
                                                        @case('module')
                                                            Modül
                                                            @break
                                                        @case('content')
                                                            İçerik
                                                            @break
                                                        @default
                                                            {{ $widget->type }}
                                                    @endswitch
                                                </span>
                                                @if($widget->has_items)
                                                <span class="badge bg-orange-lt">Dinamik İçerik</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="button" class="btn btn-primary btn-sm w-100" 
                                            wire:click="addWidget({{ $widget->id }})" 
                                            data-bs-dismiss="modal">
                                        <i class="fas fa-plus me-1"></i> Ekle
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
<script src="{{ asset('admin/libs/sortable/sortable.min.js') }}"></script>
<script>
document.addEventListener('livewire:initialized', function() {
    // Widget seçme modalını açma eventi
    Livewire.on('openAddWidgetModal', function(data) {
        const modal = new bootstrap.Modal(document.getElementById('addWidgetModal'));
        modal.show();
        
        // Eğer preSelectedWidgetId varsa, o widget'ı otomatik olarak seç
        if (data && data.preSelectedWidgetId) {
            // Burada preSelectedWidgetId'yi kullanabilirsiniz
            // Örneğin: İlgili widget butonunu vurgulayabilir veya otomatik seçebilirsiniz
        }
    });
    
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
    
    // Tüm widget konteynerleri için sürükle-bırak özelliği
    const positions = ['top', 'center-top', 'left', 'center', 'right', 'center-bottom', 'bottom'];
    
    // Her pozisyon için sürüklenebilir özelliği ekle
    positions.forEach(position => {
        const el = document.getElementById(`sortable-list-${position}`);
        if (el) {
            // Her konteynera data-position özelliği ekle
            el.setAttribute('data-position', position);
            
            new Sortable(el, {
                group: 'widgets', // Tüm bölümleri tek bir grup olarak tanımla
                animation: 150,
                fallbackTolerance: 5,
                dragClass: 'sortable-drag',
                ghostClass: 'sortable-ghost',
                draggable: '.widget-item', // Sadece widget-item sınıfına sahip öğeleri sürüklenebilir yap
                handle: '.widget-drag-handle', // Sadece tutamaçtan sürüklenebilir
                onStart: function(evt) {
                    // Sürükleme başladığında kaynağı işaretle
                    evt.from.dataset.isEmpty = 'false';
                    // Sürükleme sırasında kartı vurgula
                    evt.item.classList.add('is-dragging');
                    document.body.classList.add('dragging');
                },
                onEnd: function(evt) {
                    evt.item.classList.remove('is-dragging');
                    document.body.classList.remove('dragging');
                    
                    const itemId = evt.item.getAttribute('data-id');
                    const newPosition = evt.to.getAttribute('data-position');
                    const oldPosition = evt.from.getAttribute('data-position');
                    
                    // Yeni sırayı al
                    const widgetIds = Array.from(evt.to.querySelectorAll('.widget-item')).map(el => el.getAttribute('data-id'));
                    
                    console.log(`Widget ${itemId} taşındı: ${oldPosition} -> ${newPosition}`);
                    console.log('Yeni sıra:', widgetIds);
                    
                    // Hedef boş mu kontrol edelim
                    const isTargetEmpty = evt.to.querySelectorAll('.widget-item').length <= 1; // sadece taşınan öğe var
                    
                    // Kaynak boş mu kontrol edelim
                    const isSourceEmpty = evt.from.querySelectorAll('.widget-item').length === 0;
                    
                    // Kaynak boşsa, "bu alanda widget bulunmuyor" mesajını göster
                    if (isSourceEmpty) {
                        const emptyMessage = document.createElement('div');
                        emptyMessage.className = 'col-12';
                        emptyMessage.innerHTML = `
                            <div class="alert alert-info py-2 mb-0">
                                <i class="fas fa-info-circle me-2"></i> Bu alanda widget bulunmuyor
                            </div>
                        `;
                        evt.from.appendChild(emptyMessage);
                    }
                    
                    // Hedef boş değilse, "bu alanda widget bulunmuyor" mesajını gizle
                    if (!isTargetEmpty) {
                        const emptyMessages = evt.to.querySelectorAll('.alert-info');
                        emptyMessages.forEach(msg => {
                            if (msg.textContent.includes('Bu alanda widget bulunmuyor')) {
                                msg.closest('.col-12').remove();
                            }
                        });
                    }
                    
                    // Pozisyon değiştiyse Livewire'a bildir
                    if (newPosition !== oldPosition) {
                        console.log(`Widget ${itemId} taşındı: ${oldPosition} -> ${newPosition}`);
                        @this.call('moveWidgetToPosition', itemId, newPosition);
                    } else {
                        // Aynı pozisyon içinde sıra değişikliği
                        @this.call('updateOrder', widgetIds, newPosition);
                    }
                }
            });
        }
    });
    
    // Sayfa yüklendiğinde boş konteynerlar için özel sınıf ekle
    positions.forEach(position => {
        const container = document.getElementById(`sortable-list-${position}`);
        if (container) {
            const hasWidgets = container.querySelectorAll('.widget-item').length > 0;
            container.dataset.isEmpty = hasWidgets ? 'false' : 'true';
            
            // Eğer widget yoksa, boş mesajı göster
            if (!hasWidgets && !container.querySelector('.alert-info')) {
                const emptyMessage = document.createElement('div');
                emptyMessage.className = 'col-12';
                emptyMessage.innerHTML = `
                    <div class="alert alert-info py-2 mb-0">
                        <i class="fas fa-info-circle me-2"></i> Bu alanda widget bulunmuyor
                    </div>
                `;
                container.appendChild(emptyMessage);
            }
        }
    });
});
</script>
@endpush
</div>