{{-- PreTitle --}}
@push('pretitle')
Bileşenler
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
                    <a class="dropdown-item" href="{{ route('admin.widgetmanagement.index') }}">
                        Aktif Bileşenler
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.widgetmanagement.gallery') }}">
                        Bileşen Galerisi
                    </a>
                    @endhasmoduleaccess
                    
                    @role('root')
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">Özel Bileşenler</span>
                    </h6>
                    
                    <a class="dropdown-item" href="{{ route('admin.widgetmanagement.modules') }}">
                        Modül Bileşenleri
                    </a>
                    
                    <a class="dropdown-item" href="{{ route('admin.widgetmanagement.files') }}">
                        Hazır Dosyalar
                    </a>
                    
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">Bileşen Yapılandırması</span>
                    </h6>
                    
                    <a class="dropdown-item" href="{{ route('admin.widgetmanagement.category.index') }}">
                        Kategori Yönetimi
                    </a>
                    @endrole
                </div>
            </div>
            @role('root')
            <a href="{{ route('admin.widgetmanagement.category.index') }}" class="dropdown-module-item btn btn-primary">
                Kategoriler
            </a>
            @endrole
        </div>
    </div>
</div>
@endpush