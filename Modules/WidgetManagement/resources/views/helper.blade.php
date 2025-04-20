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
                    Bileşen İşlemleri
                </button>
                <div class="dropdown-menu">
                    @hasmoduleaccess('widgetmanagement', 'view')
                    <a class="dropdown-item {{ request()->routeIs('admin.widgetmanagement.index') ? 'active' : '' }}" 
                       href="{{ route('admin.widgetmanagement.index') }}">
                        <i class="fas fa-puzzle-piece me-2"></i> Aktif Bileşenler
                    </a>
                    @endhasmoduleaccess
                    
                    @hasmoduleaccess('widgetmanagement', 'view')
                    <a class="dropdown-item {{ request()->routeIs('admin.widgetmanagement.gallery') ? 'active' : '' }}" 
                       href="{{ route('admin.widgetmanagement.gallery') }}">
                        <i class="fas fa-th-large me-2"></i> Bileşen Galerisi
                    </a>
                    @endhasmoduleaccess
                    
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">Kategori İşlemleri</span>
                    </h6>
                    
                    @hasmoduleaccess('widgetmanagement', 'view')
                    <a class="dropdown-item {{ request()->routeIs('admin.widgetmanagement.category.index') ? 'active' : '' }}" 
                       href="{{ route('admin.widgetmanagement.category.index') }}">
                        <i class="fas fa-folder me-2"></i> Kategoriler
                    </a>
                    @endhasmoduleaccess
                    
                    @hasmoduleaccess('widgetmanagement', 'create')
                    <a class="dropdown-item {{ request()->routeIs('admin.widgetmanagement.category.manage') && !request()->route('id') ? 'active' : '' }}" 
                       href="{{ route('admin.widgetmanagement.category.manage') }}">
                        <i class="fas fa-folder-plus me-2"></i> Kategori Ekle
                    </a>
                    @endhasmoduleaccess
                    
                    @if(auth()->user()->hasRole('root'))
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item {{ request()->routeIs('admin.widgetmanagement.manage*') ? 'active' : '' }}" 
                       href="{{ route('admin.widgetmanagement.manage') }}">
                        <i class="fas fa-tools me-2"></i> Bileşen Şablonları
                    </a>
                    @endif
                </div>
            </div>
            
            @if(request()->routeIs('admin.widgetmanagement.index'))
            <a href="{{ route('admin.widgetmanagement.gallery') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Yeni Bileşen Ekle
            </a>
            @endif
            
            @if(request()->routeIs('admin.widgetmanagement.gallery'))
            <a href="{{ route('admin.widgetmanagement.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-list me-2"></i> Aktif Bileşenler
            </a>
            @endif
            
            @if(request()->routeIs('admin.widgetmanagement.category.index'))
            <a href="{{ route('admin.widgetmanagement.category.manage') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Yeni Kategori
            </a>
            @endif
            
            @if(request()->routeIs('admin.widgetmanagement.content*') || request()->routeIs('admin.widgetmanagement.items*') || request()->routeIs('admin.widgetmanagement.settings*'))
            <a href="{{ route('admin.widgetmanagement.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Bileşenlere Dön
            </a>
            @endif
        </div>
    </div>
</div>
@endpush