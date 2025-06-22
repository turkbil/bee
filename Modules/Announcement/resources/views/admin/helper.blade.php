{{-- Modules/Announcement/resources/views/admin/helper.blade.php --}}
{{-- PreTitle --}}
@push('pretitle')
{{ t('announcement::general.announcements') }}
@endpush

{{-- Başlık --}}
@push('title')
{{ t('announcement::general.announcement_management') }}
@endpush

{{-- Modül Menüsü --}}
@push('module-menu')

<div class="dropdown d-grid d-md-flex module-menu">
    <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none" data-bs-toggle="dropdown">{{ t('announcement::general.menu') }}</a>
    <div class="dropdown-menu dropdown-module-menu">
        <div class="module-menu-revert">
            @hasmoduleaccess('announcement', 'view')
            <a href="{{ route('admin.announcement.index') }}" class="dropdown-module-item btn btn-ghost-secondary">
                {{ t('announcement::general.announcements') }}
            </a>
            @endhasmoduleaccess

            @hasmoduleaccess('announcement', 'create')
            <a href="{{ route('admin.announcement.manage') }}" class="dropdown-module-item btn btn-primary">
                {{ t('announcement::general.new_announcement') }}
            </a>
            @endhasmoduleaccess
        </div>
    </div>
</div>

@endpush