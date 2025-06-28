{{-- PreTitle --}}
@push('pretitle')
{{ __('modulemanagement::admin.modules') }}
@endpush

{{-- Başlık --}}
@push('title')
{{ __('modulemanagement::admin.module_list') }}
@endpush

{{-- Modül Menüsü --}}
@push('module-menu')
<div class="dropdown d-grid d-md-flex module-menu">
    <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none" data-bs-toggle="dropdown">{{ __('modulemanagement::admin.menu') }}</a>
    <div class="dropdown-menu dropdown-module-menu">
        <div class="module-menu-revert">
            @hasmoduleaccess('modulemanagement', 'view')
            <a href="{{ route('admin.modulemanagement.index') }}" class="dropdown-module-item btn btn-ghost-secondary">
                {{ __('modulemanagement::admin.modules') }}
            </a>
            @endhasmoduleaccess

            @hasmoduleaccess('modulemanagement', 'create')
            <a href="{{ route('admin.modulemanagement.manage') }}" class="dropdown-module-item btn btn-primary">
                {{ __('modulemanagement::admin.new_module') }}
            </a>
            @endhasmoduleaccess
        </div>
    </div>
</div>
@endpush