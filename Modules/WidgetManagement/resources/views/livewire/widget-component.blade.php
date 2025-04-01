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
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Widget ara...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select wire:model.live="typeFilter" class="form-select">
                        <option value="">Tüm Tipler</option>
                        @foreach($types as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="form-check form-switch">
                        <input type="checkbox" id="activeOnly" class="form-check-input" wire:model.live="activeOnly">
                        <label class="form-check-label" for="activeOnly">Sadece Aktif</label>
                    </div>
                </div>
            </div>
            
            <div class="row">
                @forelse($widgets as $widget)
                <div class="col-md-4 col-lg-3 mb-4">
                    <div class="card h-100">
                        <div class="card-img-top img-responsive img-responsive-16x9" style="background-image: url('{{ $widget->getThumbnailUrl() }}')"></div>
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <h3 class="card-title mb-0">{{ $widget->name }}</h3>
                                <div class="ms-auto">
                                    <span class="badge {{ $widget->is_active ? 'bg-green' : 'bg-red' }}">
                                        {{ $widget->is_active ? 'Aktif' : 'Pasif' }}
                                    </span>
                                </div>
                            </div>
                            <div class="text-muted mt-2 small">{{ Str::limit($widget->description, 100) }}</div>
                            <div class="mt-2">
                                <span class="badge bg-blue-lt">{{ $types[$widget->type] ?? $widget->type }}</span>
                                @if($widget->is_core)
                                <span class="badge bg-purple-lt">Sistem</span>
                                @endif
                                @if($widget->has_items)
                                <span class="badge bg-orange-lt">Dinamik Öğeler</span>
                                @endif
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-between align-items-center">
                            <div class="btn-list">
                                <a href="{{ route('admin.widgetmanagement.manage', $widget->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm {{ $widget->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}" 
                                        wire:click="toggleActive({{ $widget->id }})" 
                                        wire:loading.attr="disabled" 
                                        title="{{ $widget->is_active ? 'Pasif Yap' : 'Aktif Yap' }}">
                                    <i class="fas fa-{{ $widget->is_active ? 'ban' : 'check' }}"></i>
                                </button>
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
            
            <div class="mt-4">
                {{ $widgets->links() }}
            </div>
        </div>
    </div>
</div>