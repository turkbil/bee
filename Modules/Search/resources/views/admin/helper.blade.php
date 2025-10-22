{{-- Modules/Search/resources/views/admin/helper.blade.php --}}
{{-- PreTitle --}}
@section('pretitle')
    Arama Sistemi
@endsection

{{-- Başlık --}}
@section('title')
    Arama Yönetimi
@endsection

{{-- Modül Menüsü --}}
@push('module-menu')
    <div class="dropdown d-grid d-md-flex module-menu">
        <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none" data-bs-toggle="dropdown">
            <i class="fa-solid fa-magnifying-glass me-2"></i>Arama Menüsü
        </a>
        <div class="dropdown-menu dropdown-module-menu">
            <div class="module-menu-revert">
                @hasmoduleaccess('search', 'view')
                    <a href="{{ route('admin.search.index') }}" class="dropdown-module-item btn btn-ghost-primary">
                        <i class="fa-solid fa-list me-2"></i>Arama Sorguları
                    </a>
                @endhasmoduleaccess

                @hasmoduleaccess('search', 'view')
                    <a href="{{ route('admin.search.analytics') }}" class="dropdown-module-item btn btn-ghost-primary">
                        <i class="fa-solid fa-chart-bar me-2"></i>Analytics
                    </a>
                @endhasmoduleaccess
            </div>
        </div>
    </div>
@endpush
