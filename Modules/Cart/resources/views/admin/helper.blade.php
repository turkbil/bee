{{-- Modules/Cart/resources/views/admin/helper.blade.php --}}
{{-- PreTitle --}}
@section('pretitle')
    {{ __('cart::admin.cart_management') }}
@endsection

{{-- Başlık --}}
@section('title')
    {{ __('cart::admin.carts') }}
@endsection

{{-- Modül Menüsü --}}
@push('module-menu')

    <div class="dropdown d-grid d-md-flex module-menu">
        <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none"
            data-bs-toggle="dropdown">{{ __('cart::admin.menu') }}</a>
        <div class="dropdown-menu dropdown-module-menu">
            <div class="module-menu-revert">
                @hasmoduleaccess('cart', 'view')
                    <a href="{{ route('admin.cart.index') }}" class="dropdown-module-item btn btn-ghost-primary">
                        {{ __('cart::admin.all_carts') }}
                    </a>
                @endhasmoduleaccess

                @hasmoduleaccess('cart', 'view')
                    <a href="{{ route('admin.cart.index', ['status' => 'active']) }}" class="dropdown-module-item btn btn-ghost-primary">
                        {{ __('cart::admin.active_carts') }}
                    </a>
                @endhasmoduleaccess

                @hasmoduleaccess('cart', 'view')
                    <a href="{{ route('admin.cart.index', ['status' => 'abandoned']) }}" class="dropdown-module-item btn btn-ghost-primary">
                        {{ __('cart::admin.abandoned_carts') }}
                    </a>
                @endhasmoduleaccess
            </div>
        </div>
    </div>

@endpush
