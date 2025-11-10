{{-- Modules/Favorite/resources/views/admin/helper.blade.php --}}
{{-- PreTitle --}}
@section('pretitle')
    {{ __('favorite::admin.favorite_management') }}
@endsection

{{-- Başlık --}}
@section('title')
    {{ __('favorite::admin.favorites') }}
@endsection

{{-- Modül Menüsü --}}
@push('module-menu')

    <div class="dropdown d-grid d-md-flex module-menu">
        <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none"
            data-bs-toggle="dropdown">{{ __('favorite::admin.menu') }}</a>
        <div class="dropdown-menu dropdown-module-menu">
            <div class="module-menu-revert">
                @hasmoduleaccess('favorite', 'view')
                    <a href="{{ route('admin.favorite.index') }}" class="dropdown-module-item btn btn-ghost-primary">
                        {{ __('favorite::admin.favorites') }}
                    </a>
                @endhasmoduleaccess

                @hasmoduleaccess('favorite', 'create')
                    <a href="{{ route('admin.favorite.manage') }}" class="dropdown-module-item btn btn-primary">
                        {{ __('favorite::admin.new_favorite') }}
                    </a>
                @endhasmoduleaccess
            </div>
        </div>
    </div>

@endpush
