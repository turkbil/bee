{{-- Modules/TenantManagement/resources/views/helper.blade.php --}}
{{-- PreTitle --}}
@push('pretitle')
Tenantlar
@endpush

{{-- Başlık --}}
@push('title')
Tenant Yönetimi
@endpush

{{-- Modül Menüsü --}}
@push('module-menu')

<div class="dropdown d-grid d-md-flex module-menu">
    <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none" data-bs-toggle="dropdown">Menü</a>
    <div class="dropdown-menu dropdown-module-menu">
        <div class="module-menu-revert">
            @hasmoduleaccess('tenantmanagement', 'view')
            <a href="{{ route('admin.tenantmanagement.index') }}" class="dropdown-module-item btn btn-ghost-secondary">
                Tenantlar
            </a>
            @endhasmoduleaccess

            @hasmoduleaccess('tenantmanagement', 'create')
            <button type="button" class="dropdown-module-item btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-tenant-add"
                wire:click="resetForm">
                Yeni Tenant Ekle
            </button>
            @endhasmoduleaccess
        </div>
    </div>
</div>

@endpush