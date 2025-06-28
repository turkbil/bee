{{-- Modules/ThemeManagement/resources/views/helper.blade.php --}}
{{-- PreTitle --}}
@push('pretitle')
{{ __('thememanagement::admin.themes') }}
@endpush

{{-- Başlık --}}
@push('title')
{{ __('thememanagement::admin.theme_management') }}
@endpush

{{-- Modül Menüsü --}}
@push('module-menu')

<div class="dropdown d-grid d-md-flex module-menu">
    <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none" data-bs-toggle="dropdown">{{ __('thememanagement::admin.menu') }}</a>
    <div class="dropdown-menu dropdown-module-menu">
        <div class="module-menu-revert">
            @hasmoduleaccess('thememanagement', 'view')
            <a href="{{ route('admin.thememanagement.index') }}" class="dropdown-module-item btn btn-ghost-secondary">
                {{ __('thememanagement::admin.themes') }}
            </a>
            @endhasmoduleaccess

            @hasmoduleaccess('thememanagement', 'create')
            <a href="{{ route('admin.thememanagement.manage') }}" class="dropdown-module-item btn btn-primary">
                {{ __('thememanagement::admin.new_theme') }}
            </a>
            @endhasmoduleaccess
        </div>
    </div>
</div>

@endpush