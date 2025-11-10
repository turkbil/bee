{{-- Modules/Favorite/resources/views/admin/helper.blade.php --}}
{{-- PreTitle --}}
@section('pretitle')
    Favori Sistemi
@endsection

{{-- Başlık --}}
@section('title')
    Favoriler
@endsection

{{-- Modül Menüsü --}}
@push('module-menu')

    <div class="dropdown d-grid d-md-flex module-menu">
        <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none"
            data-bs-toggle="dropdown">Menü</a>
        <div class="dropdown-menu dropdown-module-menu">
            <div class="module-menu-revert">
                <a href="{{ route('admin.favorite.index') }}" class="dropdown-module-item btn btn-ghost-primary">
                    Favoriler
                </a>
                <a href="{{ route('admin.favorite.statistics') }}" class="dropdown-module-item btn btn-ghost-primary">
                    İstatistikler
                </a>
            </div>
        </div>
    </div>

@endpush
