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
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control" 
                            placeholder="Widget ara...">
                    </div>
                </div>
                
                <!-- Ortadaki Loading -->
                <div class="col position-relative">
                    <div wire:loading
                        wire:target="toggleActive, search, typeFilter, activeOnly" 
                        class="position-absolute top-50 start-50 translate-middle text-center"
                        style="width: 100%; max-width: 250px;">
                        <div class="small text-muted mb-2">Güncelleniyor...</div>
                        <div class="progress mb-1">
                            <div class="progress-bar progress-bar-indeterminate"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Sağ Taraf Filtreler -->
                <div class="col">
                    <div class="d-flex align-items-center justify-content-end gap-3">
                        <div style="width: 160px">
                            <select wire:model.live="typeFilter" class="form-select">
                                <option value="">Tüm Tipler</option>
                                @foreach($types as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-check form-switch">
                            <input type="checkbox" wire:model.live="activeOnly" class="form-check-input">
                            <span class="form-check-label">Sadece Aktif</span>
                        </div>
                        @if(auth()->user()->isRoot() || auth()->user()->hasRole('root'))
                        <a href="{{ route('admin.widgetmanagement.manage') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i> Yeni Widget
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Widget Kartları -->
            <div class="row row-cards">
                @forelse($widgets as $widget)
                <div class="col-md-6 col-lg-4 col-xl-3 mb-4">
                    <div class="card h-100 {{ in_array($widget->id, $usedWidgetIds) ? 'border-primary' : '' }}">
                        <div class="card-status-top {{ $widget->is_active ? 'bg-green' : 'bg-red' }}"></div>
                        
                        <div class="card-img-top img-responsive img-responsive-16x9 overflow-hidden" style="height: 160px;">
                            <img src="{{ $widget->getThumbnailUrl() }}" class="w-100 h-100 object-fit-cover" alt="{{ $widget->name }}">
                            
                            @if(in_array($widget->id, $usedWidgetIds))
                            <div class="ribbon bg-primary">
                                <i class="fas fa-star me-1"></i> Kullanımda
                            </div>
                            @endif
                        </div>
                        
                        <div class="card-body d-flex flex-column">
                            <h3 class="card-title mb-1">{{ $widget->name }}</h3>
                            <p class="text-muted small mb-3">{{ Str::limit($widget->description, 80) }}</p>
                            
                            <div class="d-flex flex-wrap gap-1 mb-3">
                                <span class="badge bg-blue">{{ $types[$widget->type] ?? $widget->type }}</span>
                                @if($widget->is_core)
                                <span class="badge bg-purple">Sistem</span>
                                @endif
                                @if($widget->has_items)
                                <span class="badge bg-orange">Dinamik İçerik</span>
                                @endif
                            </div>
                            
                            <div class="mt-auto">
                                <div class="btn-list">
                                    <a href="{{ route('admin.widgetmanagement.section', ['widgetId' => $widget->id]) }}" class="btn btn-outline-primary w-100">
                                        <i class="fas fa-puzzle-piece me-1"></i> Bölümlerde Kullan
                                    </a>
                                    
                                    @if(auth()->user()->isRoot() || auth()->user()->hasRole('root'))
                                    <button class="btn btn-outline-{{ $widget->is_active ? 'danger' : 'success' }} w-100" 
                                        wire:click="toggleActive({{ $widget->id }})" 
                                        wire:loading.attr="disabled">
                                        <i class="fas fa-{{ $widget->is_active ? 'ban' : 'check' }} me-1"></i>
                                        {{ $widget->is_active ? 'Pasif Yap' : 'Aktif Yap' }}
                                    </button>
                                    
                                    <a href="{{ route('admin.widgetmanagement.manage', $widget->id) }}" class="btn btn-outline-secondary w-100">
                                        <i class="fas fa-tools me-1"></i> Yapılandır
                                    </a>
                                    @endif
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
                            Aramanıza uygun widget bulunamadı.
                        </p>
                        @if(auth()->user()->hasRole('root'))
                        <div class="empty-action">
                            <a href="{{ route('admin.widgetmanagement.manage') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i> Yeni Widget Ekle
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
                @endforelse
            </div>
            
            <!-- Sayfalama -->
            <div class="mt-4 d-flex justify-content-center">
                {{ $widgets->links() }}
            </div>
        </div>
    </div>
</div>