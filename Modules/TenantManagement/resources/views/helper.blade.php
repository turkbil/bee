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
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-tenant-manage"
                wire:click="resetForm">
                Yeni Tenant Ekle
            </button>
        </div>
    </div>
</div>
@endpush