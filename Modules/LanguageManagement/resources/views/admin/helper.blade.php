{{-- Modules/LanguageManagement/resources/views/admin/helper.blade.php --}}
{{-- PreTitle --}}
@push('pretitle')
{{ t('languagemanagement::general.title') }}
@endpush

{{-- Başlık --}}
@push('title')
{{ t('languagemanagement::general.multilanguage_system') }}
@endpush

{{-- Modül Menüsü --}}
@push('module-menu')
<div class="dropdown d-grid d-md-flex module-menu">
    <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none" data-bs-toggle="dropdown">{{ t('languagemanagement::general.menu') }}</a>
    <div class="dropdown-menu dropdown-module-menu">
        <div class="module-menu-revert">
            <div class="dropdown">
                <button type="button" class="dropdown-module-item dropdown-toggle btn btn-ghost-secondary"
                    data-bs-toggle="dropdown">
                    {{ t('languagemanagement::general.language_operations') }}
                </button>
                <div class="dropdown-menu">
                    @hasmoduleaccess('languagemanagement', 'view')
                    <a class="dropdown-item" href="{{ route('admin.languagemanagement.index') }}">
                        {{ t('languagemanagement::general.dashboard') }}
                    </a>
                    @endhasmoduleaccess
                    
                    @if((auth()->check() && auth()->user()->hasRole('root')) || (function_exists('tenant') && tenant() && tenant()->central) || (!function_exists('tenant') || !tenant()))
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">{{ t('languagemanagement::general.system_languages_header') }}</span>
                    </h6>
                    
                    @hasmoduleaccess('languagemanagement', 'view')
                    <a class="dropdown-item" href="{{ route('admin.languagemanagement.system.index') }}">
                        {{ t('languagemanagement::general.system_languages') }}
                    </a>
                    @endhasmoduleaccess
                    
                    @hasmoduleaccess('languagemanagement', 'create')
                    <a class="dropdown-item" href="{{ route('admin.languagemanagement.system.manage') }}">
                        {{ t('languagemanagement::general.add_system_language') }}
                    </a>
                    @endhasmoduleaccess
                    @endif
                    
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">{{ t('languagemanagement::general.site_languages_header') }}</span>
                    </h6>
                    
                    @hasmoduleaccess('languagemanagement', 'view')
                    <a class="dropdown-item" href="{{ route('admin.languagemanagement.site.index') }}">
                        {{ t('languagemanagement::general.site_languages') }}
                    </a>
                    @endhasmoduleaccess
                    
                    @hasmoduleaccess('languagemanagement', 'create')
                    <a class="dropdown-item" href="{{ route('admin.languagemanagement.site.manage') }}">
                        {{ t('languagemanagement::general.add_site_language') }}
                    </a>
                    @endhasmoduleaccess
                    
                </div>
            </div>
            @hasmoduleaccess('languagemanagement', 'create')
            @if((auth()->check() && auth()->user()->hasRole('root')) || (function_exists('tenant') && tenant() && tenant()->central) || (!function_exists('tenant') || !tenant()))
            <a href="{{ route('admin.languagemanagement.system.manage') }}" class="btn btn-primary">
                {{ t('languagemanagement::general.new_system_language_btn') }}
            </a>
            @else
            <a href="{{ route('admin.languagemanagement.site.manage') }}" class="btn btn-primary">
                {{ t('languagemanagement::general.new_site_language_btn') }}
            </a>
            @endif
            @endhasmoduleaccess
        </div>
    </div>
</div>
@endpush