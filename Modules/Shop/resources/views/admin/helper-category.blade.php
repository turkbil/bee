{{-- Modules/Shop/resources/views/admin/helper-category.blade.php --}}
{{-- PreTitle --}}
@section('pretitle')
    {{ __('shop::admin.category_management') }}
@endsection

{{-- Başlık --}}
@section('title')
    {{ __('shop::admin.categories') }}
@endsection

{{-- Modül Menüsü --}}
@push('module-menu')

    <div class="dropdown d-grid d-md-flex module-menu">
        <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none"
            data-bs-toggle="dropdown">{{ __('shop::admin.menu') }}</a>
        <div class="dropdown-menu dropdown-module-menu">
            <div class="module-menu-revert">
                @hasmoduleaccess('shop', 'view')
                    <a href="{{ route('admin.shop.categories.index') }}" class="dropdown-module-item btn btn-ghost-primary">
                        {{ __('shop::admin.categories') }}
                    </a>
                @endhasmoduleaccess

                @hasmoduleaccess('shop', 'create')
                    <a href="{{ route('admin.shop.categories.manage') }}" class="dropdown-module-item btn btn-primary">
                        {{ __('shop::admin.new_category') }}
                    </a>
                @endhasmoduleaccess
            </div>
        </div>
    </div>

@endpush
