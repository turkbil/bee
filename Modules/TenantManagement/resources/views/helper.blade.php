{{-- Modules/TenantManagement/resources/views/helper.blade.php --}}
{{-- PreTitle --}}
@push('pretitle')
{{ t('tenantmanagement::general.tenants') }}
@endpush

{{-- Başlık --}}
@push('title')
{{ t('tenantmanagement::general.tenant_management') }}
@endpush

{{-- Modül Menüsü --}}
@push('module-menu')

<div class="dropdown d-grid d-md-flex module-menu">
    <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none" data-bs-toggle="dropdown">{{ t('tenantmanagement::general.menu') }}</a>
    <div class="dropdown-menu dropdown-module-menu">
        <div class="module-menu-revert">
            @hasmoduleaccess('tenantmanagement', 'view')
            <a href="{{ route('admin.tenantmanagement.index') }}" class="dropdown-module-item btn btn-ghost-secondary">
                {{ t('tenantmanagement::general.tenants') }}
            </a>
            @endhasmoduleaccess

            @hasmoduleaccess('tenantmanagement', 'create')
            <button type="button" class="dropdown-module-item btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-tenant-add"
                wire:click="resetForm">
                {{ t('tenantmanagement::general.add_new_tenant') }}
            </button>
            @endhasmoduleaccess
        </div>
    </div>
</div>

@endpush