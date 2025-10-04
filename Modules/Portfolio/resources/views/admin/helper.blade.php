{{-- Modules/Portfolio/resources/views/admin/helper.blade.php --}}
{{-- PreTitle --}}
@section('pretitle')
    {{ __('portfolio::admin.portfolio_management') }}
@endsection

{{-- Başlık --}}
@section('title')
    {{ __('portfolio::admin.portfolios') }}
@endsection

{{-- Modül Menüsü --}}
@push('module-menu')

    <div class="dropdown d-grid d-md-flex module-menu">
        <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none"
            data-bs-toggle="dropdown">{{ __('portfolio::admin.menu') }}</a>
        <div class="dropdown-menu dropdown-module-menu">
            <div class="module-menu-revert">
                @hasmoduleaccess('portfolio', 'view')
                    <a href="{{ route('admin.portfolio.index') }}" class="dropdown-module-item btn btn-ghost-primary">
                        {{ __('portfolio::admin.portfolios') }}
                    </a>
                @endhasmoduleaccess

                @hasmoduleaccess('portfolio', 'view')
                    <a href="{{ route('admin.portfolio.category.index') }}" class="dropdown-module-item btn btn-ghost-primary">
                        {{ __('portfolio::admin.categories') }}
                    </a>
                @endhasmoduleaccess

                @hasmoduleaccess('portfolio', 'create')
                    <a href="{{ route('admin.portfolio.manage') }}" class="dropdown-module-item btn btn-primary">
                        {{ __('portfolio::admin.new_portfolio') }}
                    </a>
                @endhasmoduleaccess
            </div>
        </div>
    </div>

@endpush
