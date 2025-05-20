{{-- Modules/Portfolio/resources/views/admin/helper.blade.php --}}
{{-- PreTitle --}}
@push('pretitle')
Portfolyolar
@endpush

{{-- Başlık --}}
@push('title')
Portfolyo Yönetimi
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
                    Portfolyo İşlemleri
                </button>
                <div class="dropdown-menu">
                    @hasmoduleaccess('portfolio', 'view')
                    <a class="dropdown-item" href="{{ route('admin.portfolio.index') }}">
                        Portfolyolar
                    </a>
                    @endhasmoduleaccess
                    
                    @hasmoduleaccess('portfolio', 'create')
                    <a class="dropdown-item" href="{{ route('admin.portfolio.manage') }}">
                        Yeni Portfolyo Ekle
                    </a>
                    @endhasmoduleaccess
                    
                    @if(auth()->user()->hasModulePermission('portfolio', 'view'))
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">Kategori İşlemleri</span>
                    </h6>
                    
                    @hasmoduleaccess('portfolio', 'view')
                    <a class="dropdown-item" href="{{ route('admin.portfolio.category.index') }}">
                        Kategoriler
                    </a>
                    @endhasmoduleaccess
                    
                    @hasmoduleaccess('portfolio', 'create')
                    <a class="dropdown-item" href="{{ route('admin.portfolio.category.manage') }}">
                        Kategori Ekle
                    </a>
                    @endhasmoduleaccess
                    @endif
                </div>
            </div>
            @hasmoduleaccess('portfolio', 'create')
            <a href="{{ route('admin.portfolio.manage') }}" class="btn btn-primary">
                Yeni Portfolyo
            </a>
            @endhasmoduleaccess
        </div>
    </div>
</div>
@endpush