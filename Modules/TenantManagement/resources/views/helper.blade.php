{{-- Modules/TenantManagement/resources/views/helper.blade.php --}}

@section('pretitle', 'Tenants')
@section('title', 'Tenant Listesi')
@section('module-menu')
<ul class="nav">
    <li class="nav-item">
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modal-tenant-add" wire:click="reset">
            Yeni Tenant Ekle
        </button>
    </li>
</ul>
@endsection
