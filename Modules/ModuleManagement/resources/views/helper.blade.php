{{-- PreTitle --}}
@push('pretitle')
{{ t('modulemanagement::general.modules') }}
@endpush

{{-- Başlık --}}
@push('title')
{{ t('modulemanagement::general.module_list') }}
@endpush

{{-- Modül Menüsü --}}
@push('module-menu')
<div class="dropdown d-grid d-md-flex module-menu">
    <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none" data-bs-toggle="dropdown">{{ t('modulemanagement::general.menu') }}</a>
    <div class="dropdown-menu dropdown-module-menu">
        <div class="module-menu-revert">
            @hasmoduleaccess('modulemanagement', 'view')
            <a href="{{ route('admin.modulemanagement.index') }}" class="dropdown-module-item btn btn-ghost-secondary">
                {{ t('modulemanagement::general.modules') }}
            </a>
            @endhasmoduleaccess

            @hasmoduleaccess('modulemanagement', 'create')
            <a href="{{ route('admin.modulemanagement.manage') }}" class="dropdown-module-item btn btn-primary">
                {{ t('modulemanagement::general.new_module') }}
            </a>
            @endhasmoduleaccess
        </div>
    </div>
</div>
@endpush