{{-- Modules/Search/resources/views/admin/helper.blade.php --}}
{{-- PreTitle --}}
@section('pretitle')
    Arama Sistemi
@endsection

{{-- Başlık --}}
@section('title')
    Arama Analytics
@endsection

{{-- Modül Menüsü --}}
@push('module-menu')
    <div class="dropdown d-grid d-md-flex module-menu">
        <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none" data-bs-toggle="dropdown">Arama Menüsü</a>
        <div class="dropdown-menu dropdown-module-menu">
            <div class="module-menu-revert">
                @hasmoduleaccess('search', 'view')
                    <a href="{{ route('admin.search.analytics') }}" class="dropdown-module-item btn btn-primary">
                        Arama Analytics
                    </a>
                @endhasmoduleaccess
            </div>
        </div>
    </div>
@endpush
