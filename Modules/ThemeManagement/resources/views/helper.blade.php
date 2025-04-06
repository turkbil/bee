{{-- Modules/ThemeManagement/resources/views/helper.blade.php --}}
{{-- PreTitle --}}
@push('pretitle')
Temalar
@endpush

{{-- Başlık --}}
@push('title')
Tema Yönetimi
@endpush

{{-- Modül Menüsü --}}
@push('module-menu')

<div class="dropdown d-grid d-md-flex module-menu">
    <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none" data-bs-toggle="dropdown">Menü</a>
    <div class="dropdown-menu dropdown-module-menu">
        <div class="module-menu-revert">
            @hasmoduleaccess('thememanagement', 'view')
            <a href="{{ route('admin.thememanagement.index') }}" class="dropdown-module-item btn btn-ghost-secondary">
                Temalar
            </a>
            @endhasmoduleaccess

            @hasmoduleaccess('thememanagement', 'create')
            <a href="{{ route('admin.thememanagement.manage') }}" class="dropdown-module-item btn btn-primary">
                Yeni Tema
            </a>
            @endhasmoduleaccess
        </div>
    </div>
</div>

@endpush