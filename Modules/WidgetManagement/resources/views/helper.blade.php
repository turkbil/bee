{{-- Modules/WidgetManagement/resources/views/helper.blade.php --}}
{{-- PreTitle --}}
@push('pretitle')
Bileşen Yönetimi
@endpush

{{-- Başlık --}}
@push('title')
Bileşen Yönetimi
@endpush

{{-- Modül Menüsü --}}
@push('module-menu')
<div class="dropdown d-grid d-md-flex module-menu">
    <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none" data-bs-toggle="dropdown">Menü</a>
    <div class="dropdown-menu dropdown-module-menu">
        <div class="module-menu-revert">
            <div class="dropdown">
                <button type="button" class="dropdown-module-item dropdown-toggle btn btn-ghost-secondary"
                    data-bs-toggle="dropdown">
                    Bileşen Menüsü
                </button>
                <div class="dropdown-menu">
                    @hasmoduleaccess('widgetmanagement', 'view')
                    <a class="dropdown-item {{ request()->routeIs('admin.widgetmanagement.index') ? 'active' : '' }}" 
                       href="{{ route('admin.widgetmanagement.index') }}">
                        <i class="fas fa-puzzle-piece me-2"></i> Bileşenler
                    </a>
                    @endhasmoduleaccess
                    
                    @hasmoduleaccess('widgetmanagement', 'view')
                    <a class="dropdown-item {{ request()->routeIs('admin.widgetmanagement.section*') ? 'active' : '' }}" 
                       href="{{ route('admin.widgetmanagement.section') }}">
                        <i class="fas fa-th-large me-2"></i> Bölüm Yönetimi
                    </a>
                    @endhasmoduleaccess
                    
                    @if(auth()->user()->hasRole('root'))
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item {{ request()->routeIs('admin.widgetmanagement.manage*') ? 'active' : '' }}" 
                       href="{{ route('admin.widgetmanagement.manage') }}">
                        <i class="fas fa-tools me-2"></i> Bileşen Şablonları
                    </a>
                    @endif
                    
                    @if(request()->routeIs('admin.widgetmanagement.items*'))
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item active" href="{{ url()->current() }}">
                        <i class="fas fa-layer-group me-2"></i> İçerik Düzenle
                    </a>
                    @endif
                    
                    @if(request()->routeIs('admin.widgetmanagement.settings*'))
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item active" href="{{ url()->current() }}">
                        <i class="fas fa-sliders-h me-2"></i> Bileşen Ayarları
                    </a>
                    @endif
                </div>
            </div>
            
            @if(request()->routeIs('admin.widgetmanagement.index'))
            <a href="#" class="btn btn-primary" onclick="window.location.href='{{ route('admin.widgetmanagement.manage') }}'; Livewire.dispatch('changeViewMode', { mode: 'gallery' })">
                <i class="fas fa-plus me-2"></i> Yeni Bileşen Oluştur
            </a>
            @endif
            
            @if(request()->routeIs('admin.widgetmanagement.section*'))
            <a href="{{ route('admin.widgetmanagement.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Bileşenlere Dön
            </a>
            @endif
            
            @if(request()->routeIs('admin.widgetmanagement.items*') || request()->routeIs('admin.widgetmanagement.settings*'))
            <a href="{{ route('admin.widgetmanagement.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Bileşenlere Dön
            </a>
            @endif
        </div>
    </div>
</div>
@endpush