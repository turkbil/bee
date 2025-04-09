<div>
    @include('admin.partials.page-header', ['title' => 'Widget Yöneticisi'])
    
    <div class="row">
        <!-- Widget Listesi -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Widgetlar</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <input type="text" class="form-control" placeholder="Widget ara..." wire:model.debounce.300ms="search">
                    </div>
                    
                    <div class="list-group">
                        @foreach($widgets as $widget)
                            <a href="#" 
                               class="list-group-item list-group-item-action {{ $selectedWidget && $selectedWidget->id == $widget['id'] ? 'active' : '' }}"
                               wire:click.prevent="selectWidget({{ $widget['id'] }})">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $widget['name'] }}</strong>
                                        <div class="text-muted small">{{ $widget['description'] }}</div>
                                    </div>
                                    <span class="badge bg-primary">{{ $widget['type'] }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Widget Düzenleyici -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Widget Düzenleyici</h3>
                </div>
                <div class="card-body">
                    @if($selectedWidget)
                        <ul class="nav nav-tabs" data-bs-toggle="tabs">
                            <li class="nav-item">
                                <a href="#tabs-html" class="nav-link active" data-bs-toggle="tab">HTML</a>
                            </li>
                            <li class="nav-item">
                                <a href="#tabs-css" class="nav-link" data-bs-toggle="tab">CSS</a>
                            </li>
                            <li class="nav-item">
                                <a href="#tabs-js" class="nav-link" data-bs-toggle="tab">JavaScript</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active show" id="tabs-html">
                                <div class="my-3">
                                    <textarea class="form-control" rows="15" wire:model.defer="widgetContent"></textarea>
                                </div>
                            </div>
                            <div class="tab-pane" id="tabs-css">
                                <div class="my-3">
                                    <textarea class="form-control" rows="15" wire:model.defer="widgetCss"></textarea>
                                </div>
                            </div>
                            <div class="tab-pane" id="tabs-js">
                                <div class="my-3">
                                    <textarea class="form-control" rows="15" wire:model.defer="widgetJs"></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <button type="button" class="btn btn-primary" wire:click="saveWidget">
                                <i class="fas fa-save me-2"></i> Kaydet
                            </button>
                        </div>
                    @else
                        <div class="empty">
                            <div class="empty-icon">
                                <i class="fas fa-puzzle-piece fa-3x text-muted"></i>
                            </div>
                            <p class="empty-title">Widget seçilmedi</p>
                            <p class="empty-subtitle text-muted">
                                Düzenlemek için soldaki listeden bir widget seçin.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>