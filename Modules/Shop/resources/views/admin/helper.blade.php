{{-- Modules/Shop/resources/views/admin/helper.blade.php --}}
{{-- PreTitle --}}
@section('pretitle')
    {{ __('shop::admin.shop_management') }}
@endsection

{{-- Başlık --}}
@section('title')
    {{ __('shop::admin.products') }}
@endsection

{{-- Modül Menüsü --}}
@push('module-menu')
    <div class="dropdown d-grid d-md-flex module-menu">
        <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none"
           data-bs-toggle="dropdown">{{ __('shop::admin.menu') }}</a>
        <div class="dropdown-menu dropdown-module-menu">
            <div class="module-menu-revert">
                <div class="dropdown">
                    <button type="button" class="dropdown-module-item dropdown-toggle btn btn-ghost-secondary"
                        data-bs-toggle="dropdown">
                        {{ __('shop::admin.shop_menu') }}
                    </button>
                    <div class="dropdown-menu">
                        @hasmoduleaccess('shop', 'view')
                            <a class="dropdown-item" href="{{ route('admin.shop.index') }}">
                                <i class="icon-menu fas fa-shopping-bag"></i>{{ __('shop::admin.products') }}
                            </a>
                        @endhasmoduleaccess

                        @hasmoduleaccess('shop', 'view')
                            <a class="dropdown-item" href="{{ route('admin.shop.categories.index') }}">
                                <i class="icon-menu fas fa-folder"></i>{{ __('shop::admin.categories') }}
                            </a>
                        @endhasmoduleaccess

                        @hasmoduleaccess('shop', 'view')
                            <a class="dropdown-item" href="{{ route('admin.shop.brands.index') }}">
                                <i class="icon-menu fas fa-tag"></i>{{ __('shop::admin.brands') }}
                            </a>
                        @endhasmoduleaccess

                        <h6 class="dropdown-menu-header card-header-light">
                            <span class="dropdown-header">{{ __('shop::admin.page_settings') }}</span>
                        </h6>

                        @hasmoduleaccess('shop', 'update')
                            <a class="dropdown-item" href="{{ route('admin.shop.homepage-products') }}">
                                <i class="icon-menu fas fa-home"></i>{{ __('shop::admin.homepage_products') }}
                            </a>
                        @endhasmoduleaccess

                        @hasmoduleaccess('shop', 'view')
                            <a class="dropdown-item" href="{{ route('admin.shop.field-templates.index') }}">
                                <i class="icon-menu fas fa-th-list"></i>{{ __('shop::admin.field_templates') }}
                            </a>
                        @endhasmoduleaccess
                    </div>
                </div>

                @hasmoduleaccess('shop', 'create')
                    <a href="{{ route('admin.shop.manage') }}" class="dropdown-module-item btn btn-primary">
                        <i class="icon-menu fas fa-plus-circle"></i>{{ __('shop::admin.new_product') }}
                    </a>
                @endhasmoduleaccess
            </div>
        </div>
    </div>
@endpush
