<div>
    @include('admin.partials.page-header', ['title' => 'Widget Yöneticisi'])
    
    <div class="row">
        <!-- Widget Listesi -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h3 class="card-title">
                        <i class="fas fa-puzzle-piece text-primary me-2"></i>
                        Widgetlar
                    </h3>
                </div>
                <div class="card-body p-0">
                    <div class="p-3 border-bottom">
                        <div class="input-group input-group-flat">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" class="form-control" placeholder="Widget ara..." wire:model.debounce.300ms="search">
                        </div>
                    </div>
                    
                    <div class="list-group list-group-flush">
                        @forelse($filteredWidgets as $widget)
                            <a href="#" 
                               class="list-group-item list-group-item-action d-flex align-items-center {{ $selectedWidgetId == $widget['id'] ? 'active' : '' }}"
                               wire:click.prevent="selectWidget({{ $widget['id'] }})">
                                <div class="me-auto">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-puzzle-piece {{ $selectedWidgetId == $widget['id'] ? 'text-white' : 'text-primary' }} me-2"></i>
                                        <div>
                                            <strong>{{ $widget['name'] }}</strong>
                                            @if(!empty($widget['description']))
                                                <div class="text-muted small">{{ $widget['description'] }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <span class="badge bg-primary rounded-pill">{{ $widget['type'] }}</span>
                            </a>
                        @empty
                            <div class="p-4 text-center text-muted">
                                <i class="fas fa-puzzle-piece fa-2x mb-3 opacity-50"></i>
                                <p>Widget bulunamadı</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Widget Düzenleyici -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h3 class="card-title">
                        <i class="fas fa-edit text-primary me-2"></i>
                        Widget Düzenleyici
                    </h3>
                </div>
                <div class="card-body">
                    @if($selectedWidgetId)
                        <ul class="nav nav-tabs mb-3" data-bs-toggle="tabs">
                            <li class="nav-item">
                                <a href="#tabs-html" class="nav-link active" data-bs-toggle="tab">
                                    <i class="fas fa-code me-2"></i>HTML
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#tabs-css" class="nav-link" data-bs-toggle="tab">
                                    <i class="fas fa-paint-brush me-2"></i>CSS
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#tabs-js" class="nav-link" data-bs-toggle="tab">
                                    <i class="fas fa-file-code me-2"></i>JavaScript
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active show" id="tabs-html">
                                <div class="form-group">
                                    <textarea class="form-control font-monospace" rows="15" wire:model.defer="widgetContent"></textarea>
                                </div>
                            </div>
                            <div class="tab-pane" id="tabs-css">
                                <div class="form-group">
                                    <textarea class="form-control font-monospace" rows="15" wire:model.defer="widgetCss"></textarea>
                                </div>
                            </div>
                            <div class="tab-pane" id="tabs-js">
                                <div class="form-group">
                                    <textarea class="form-control font-monospace" rows="15" wire:model.defer="widgetJs"></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4 d-flex justify-content-end">
                            <button type="button" class="btn btn-primary" wire:click="saveWidget">
                                <i class="fas fa-save me-2"></i> Kaydet
                            </button>
                        </div>
                    @else
                        <div class="empty">
                            <div class="empty-icon">
                                <i class="fas fa-puzzle-piece fa-3x text-muted"></i>
                            </div>
                            <p class="empty-title h4">Widget seçilmedi</p>
                            <p class="empty-subtitle text-muted mt-2">
                                Düzenlemek için soldaki listeden bir widget seçin.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>