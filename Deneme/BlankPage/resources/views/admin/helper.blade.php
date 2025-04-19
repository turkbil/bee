{{-- Modules/Page/resources/views/admin/helper.blade.php --}}
{{-- PreTitle --}}
@push('pretitle')
Sayfalar
@endpush

{{-- Başlık --}}
@push('title')
Sayfa Yönetimi
@endpush

{{-- Modül Menüsü --}}
@push('module-menu')

<div class="dropdown d-grid d-md-flex module-menu">
    <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none" data-bs-toggle="dropdown">Menü</a>
    <div class="dropdown-menu dropdown-module-menu">
        <div class="module-menu-revert">
            @hasmoduleaccess('page', 'view')
            <a href="{{ route('admin.page.index') }}" class="dropdown-module-item btn btn-ghost-secondary">
                Sayfalar
            </a>
            @endhasmoduleaccess

            @hasmoduleaccess('page', 'create')
            <a href="{{ route('admin.page.manage') }}" class="dropdown-module-item btn btn-primary">
                Yeni Sayfa
            </a>
            @endhasmoduleaccess
        </div>
    </div>
</div>

@endpush