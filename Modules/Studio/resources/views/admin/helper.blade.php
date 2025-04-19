{{-- Modules/Studio/resources/views/admin/helper.blade.php --}}
{{-- PreTitle --}}
@push('pretitle')
Studio
@endpush

{{-- Başlık --}}
@push('title')
Studio Görsel Editör
@endpush

{{-- Modül Menüsü --}}
@push('module-menu')
<div class="dropdown d-grid d-md-flex module-menu">
    <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none" data-bs-toggle="dropdown">Menü</a>
    <div class="dropdown-menu dropdown-module-menu">
        <div class="module-menu-revert">
            @hasmoduleaccess('studio', 'view')
            <a href="{{ route('admin.studio.index') }}" class="dropdown-module-item btn btn-ghost-secondary">
                Studio Editör
            </a>
            @endhasmoduleaccess

            @hasmoduleaccess('studio', 'view')
            <a href="{{ route('admin.studio.widgets') }}" class="dropdown-module-item btn btn-ghost-secondary">
                Widget Yönetimi
            </a>
            @endhasmoduleaccess
        </div>
    </div>
</div>
@endpush