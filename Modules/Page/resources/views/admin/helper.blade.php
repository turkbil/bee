{{-- SOLID Page Management Helper --}}
@section('pretitle')
{{ __('page::admin.pages') }}
@endsection

@push('title')
{{ __('page::admin.page_management') }}
@endpush

@push('module-menu')
<div class="dropdown d-grid d-md-flex module-menu">
    <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none" data-bs-toggle="dropdown">{{ __('page::admin.menu') }}</a>
    <div class="dropdown-menu dropdown-module-menu">
        <div class="module-menu-revert">
            @hasmoduleaccess('page', 'view')
            <a href="{{ route('admin.page.index') }}" class="dropdown-module-item btn btn-ghost-secondary">
                <i class="icon-menu fas fa-file-alt"></i>{{ __('page::admin.pages') }}
            </a>
            @endhasmoduleaccess

            @hasmoduleaccess('page', 'create')
            <a href="{{ route('admin.page.manage') }}" class="dropdown-module-item btn btn-primary">
                <i class="icon-menu fas fa-plus"></i>{{ __('page::admin.new_page') }}
            </a>
            @endhasmoduleaccess
        </div>
    </div>
</div>
@endpush