{{-- PreTitle --}}
@push('pretitle')
Modüller
@endpush

{{-- Başlık --}}
@push('title')
Modül Listesi
@endpush

{{-- Modül Menüsü --}}
@push('module-menu')
<div class="dropdown d-grid d-md-flex module-menu">
    <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none" data-bs-toggle="dropdown">Menü</a>
    <div class="dropdown-menu dropdown-module-menu">
        <div class="module-menu-revert">
            @hasmoduleaccess('modulemanagement', 'view')
            <a href="{{ route('admin.modulemanagement.index') }}" class="dropdown-module-item btn btn-ghost-secondary">
                Modüller
            </a>
            @endhasmoduleaccess

            @hasmoduleaccess('modulemanagement', 'create')
            <a href="{{ route('admin.modulemanagement.manage') }}" class="dropdown-module-item btn btn-primary">
                Yeni Modül
            </a>
            @endhasmoduleaccess
        </div>
    </div>
</div>
@endpush