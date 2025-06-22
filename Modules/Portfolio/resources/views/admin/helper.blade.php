{{-- Modules/Portfolio/resources/views/admin/helper.blade.php --}}
{{-- PreTitle --}}
@push('pretitle')
{{ t('portfolio::general.portfolios') }}
@endpush

{{-- Başlık --}}
@push('title')
{{ t('portfolio::general.portfolio_management') }}
@endpush

{{-- Modül Menüsü --}}
@push('module-menu')
<div class="dropdown d-grid d-md-flex module-menu">
    <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none" data-bs-toggle="dropdown">{{ t('portfolio::general.menu') }}</a>
    <div class="dropdown-menu dropdown-module-menu">
        <div class="module-menu-revert">
            <div class="dropdown">
                <button type="button" class="dropdown-module-item dropdown-toggle btn btn-ghost-secondary"
                    data-bs-toggle="dropdown">
                    {{ t('portfolio::general.portfolio_operations') }}
                </button>
                <div class="dropdown-menu">
                    @hasmoduleaccess('portfolio', 'view')
                    <a class="dropdown-item" href="{{ route('admin.portfolio.index') }}">
                        {{ t('portfolio::general.portfolios') }}
                    </a>
                    @endhasmoduleaccess
                    
                    @hasmoduleaccess('portfolio', 'create')
                    <a class="dropdown-item" href="{{ route('admin.portfolio.manage') }}">
                        {{ t('portfolio::general.add_new_portfolio') }}
                    </a>
                    @endhasmoduleaccess
                    
                    @if(auth()->user()->hasModulePermission('portfolio', 'view'))
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">{{ t('portfolio::general.category_operations') }}</span>
                    </h6>
                    
                    @hasmoduleaccess('portfolio', 'view')
                    <a class="dropdown-item" href="{{ route('admin.portfolio.category.index') }}">
                        {{ t('portfolio::general.categories') }}
                    </a>
                    @endhasmoduleaccess
                    
                    @hasmoduleaccess('portfolio', 'create')
                    <a class="dropdown-item" href="{{ route('admin.portfolio.category.manage') }}">
                        {{ t('portfolio::general.add_category') }}
                    </a>
                    @endhasmoduleaccess
                    @endif
                </div>
            </div>
            @hasmoduleaccess('portfolio', 'create')
            <a href="{{ route('admin.portfolio.manage') }}" class="btn btn-primary">
                {{ t('portfolio::general.new_portfolio') }}
            </a>
            @endhasmoduleaccess
        </div>
    </div>
</div>
@endpush