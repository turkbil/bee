{{-- Modules/Announcement/resources/views/admin/helper.blade.php --}}
{{-- PreTitle --}}
@push('pretitle')
Duyurular
@endpush

{{-- Başlık --}}
@push('title')
Duyuru Yönetimi
@endpush

{{-- Modül Menüsü --}}
@push('module-menu')

<div class="dropdown d-grid d-md-flex module-menu">
    <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none" data-bs-toggle="dropdown">Menü</a>
    <div class="dropdown-menu dropdown-module-menu">
        <div class="module-menu-revert">
            @hasmoduleaccess('announcement', 'view')
            <a href="{{ route('admin.announcement.index') }}" class="dropdown-module-item btn btn-ghost-secondary">
                Duyurular
            </a>
            @endhasmoduleaccess

            @hasmoduleaccess('announcement', 'create')
            <a href="{{ route('admin.announcement.manage') }}" class="dropdown-module-item btn btn-primary">
                Yeni Duyuru
            </a>
            @endhasmoduleaccess
        </div>
    </div>
</div>

@endpush