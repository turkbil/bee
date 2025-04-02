@include('widgetmanagement::helper')
<div>
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">
                    <i class="fas fa-puzzle-piece me-2"></i>
                    Widget Yönetimi
                </h3>
                <a href="{{ route('admin.widgetmanagement.manage') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    Yeni Widget Ekle
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="input-icon mb-3">
                        <span class="input-icon-addon">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Widget ara...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select wire:model.live="typeFilter" class="form-select mb-3">
                        <option value="">Tüm Widget Tipleri</option>
                        @foreach($types as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-check form-switch">
                        <input type="checkbox" wire:model.live="activeOnly" class="form-check-input">
                        <span class="form-check-label">Sadece Aktif Widgetlar</span>
                    </label>
                </div>
            </div>
            
            <div class="row row-cards">
                @forelse($widgets as $widget)
                <div class="col-md-4 col-lg-3 mb-4">
                    <div class="card h-100">
                        <div class="card-status-top {{ $widget->is_active ? 'bg-green' : 'bg-red' }}"></div>
                        
                        <div class="card-img-top img-responsive img-responsive-16x9 overflow-hidden" style="max-height: 160px;">
                            <img src="{{ $widget->getThumbnailUrl() }}" class="w-100 h-100 object-cover" alt="{{ $widget->name }}">
                        </div>
                        
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <h3 class="card-title mb-0">{{ $widget->name }}</h3>
                                <div class="ms-auto">
                                    <span class="badge {{ $widget->is_active ? 'bg-green' : 'bg-red' }}">
                                        {{ $widget->is_active ? 'Aktif' : 'Pasif' }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="text-muted small mb-3" style="min-height: 40px;">
                                {{ Str::limit($widget->description, 100) ?: 'Widget açıklaması bulunmuyor.' }}
                            </div>
                            
                            <div class="d-flex flex-wrap gap-1 mb-3">
                                <span class="badge bg-blue">{{ $types[$widget->type] ?? $widget->type }}</span>
                                @if($widget->is_core)
                                <span class="badge bg-purple">Sistem</span>
                                @endif
                                @if($widget->has_items)
                                <span class="badge bg-orange">Dinamik İçerik</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="card-footer d-flex justify-content-between align-items-center">
                            <div class="btn-list">
                                <a href="{{ route('admin.widgetmanagement.manage', $widget->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit me-1"></i> Düzenle
                                </a>
                                
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a href="{{ route('admin.widgetmanagement.preview', $widget->id) }}" 
                                            class="dropdown-item" 
                                            target="_blank">
                                            <i class="fas fa-eye me-2 text-info"></i> Önizleme
                                        </a>
                                        
                                        <button class="dropdown-item" 
                                                wire:click="toggleActive({{ $widget->id }})" 
                                                wire:loading.attr="disabled">
                                            @if($widget->is_active)
                                            <i class="fas fa-ban me-2 text-danger"></i> Pasif Yap
                                            @else
                                            <i class="fas fa-check me-2 text-success"></i> Aktif Yap
                                            @endif
                                        </button>
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
                            <i class="fas fa-puzzle-piece fa-5x text-muted"></i>
                        </div>
                        <p class="empty-title">Widget bulunamadı</p>
                        <p class="empty-subtitle text-muted">
                            Henüz widget bulunmuyor veya aramanıza uygun sonuç yok.
                        </p>
                        <div class="empty-action">
                            <a href="{{ route('admin.widgetmanagement.manage') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>
                                Yeni Widget Ekle
                            </a>
                        </div>
                    </div>
                </div>
                @endforelse
            </div>
            
            <div class="mt-4 d-flex justify-content-center">
                {{ $widgets->links() }}
            </div>
        </div>
    </div>
</div>