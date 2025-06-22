{{-- Modules/Page/resources/views/admin/helper.blade.php --}}
{{-- PreTitle --}}
@push('pretitle')
{{ t('page::general.pages') }}
@endpush

{{-- Başlık --}}
@push('title')
{{ t('page::general.page_management') }}
@endpush

{{-- Modül Menüsü --}}
@push('module-menu')

<div class="dropdown d-grid d-md-flex module-menu">
    <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none" data-bs-toggle="dropdown">{{ t('common.menu') }}</a>
    <div class="dropdown-menu dropdown-module-menu">
        <div class="module-menu-revert">
            @hasmoduleaccess('page', 'view')
            <a href="{{ route('admin.page.index') }}" class="dropdown-module-item btn btn-ghost-secondary">
                {{ t('page::general.pages') }}
            </a>
            @endhasmoduleaccess

            @hasmoduleaccess('page', 'create')
            <a href="{{ route('admin.page.manage') }}" class="dropdown-module-item btn btn-primary">
                {{ t('page::general.new_page') }}
            </a>
            @endhasmoduleaccess
        </div>
    </div>
</div>

@endpush