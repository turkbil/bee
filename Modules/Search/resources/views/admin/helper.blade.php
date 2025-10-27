{{-- PreTitle --}}
@section('pretitle')
Arama Sistemi
@endsection

@push('pretitle')
Arama Sistemi
@endpush

{{-- Başlık --}}
@push('title')
Arama Yönetimi
@endpush

{{-- Modül Menüsü --}}
@push('module-menu')
<div class="dropdown d-grid d-md-flex module-menu">
    <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none" data-bs-toggle="dropdown">
        <i class="fa-solid fa-magnifying-glass me-2"></i>Arama Menüsü
    </a>
    <div class="dropdown-menu dropdown-module-menu">
        <div class="module-menu-revert">
            <div class="dropdown">
                <button type="button" class="dropdown-module-item dropdown-toggle btn btn-ghost-secondary"
                    data-bs-toggle="dropdown">
                    <i class="fa-solid fa-magnifying-glass me-2"></i>Arama Yönetimi
                </button>
                <div class="dropdown-menu">
                    @hasmoduleaccess('search', 'view')
                    <a class="dropdown-item" href="{{ route('admin.search.index') }}">
                        <i class="icon-menu fa-solid fa-list"></i>Arama Sorguları
                    </a>
                    @endhasmoduleaccess

                    @hasmoduleaccess('search', 'view')
                    <a class="dropdown-item" href="{{ route('admin.search.analytics') }}">
                        <i class="icon-menu fa-solid fa-chart-bar"></i>Arama Analytics
                    </a>
                    @endhasmoduleaccess
                </div>
            </div>
        </div>
    </div>
</div>
@endpush
