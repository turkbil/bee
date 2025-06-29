{{-- Modules/LanguageManagement/resources/views/admin/helper.blade.php --}}
{{-- PreTitle --}}
@push('pretitle')
{{ __('languagemanagement::admin.title') }}
@endpush

{{-- Başlık --}}
@push('title')
{{ __('languagemanagement::admin.multilanguage_system') }}
@endpush

{{-- Modül Menüsü --}}
@push('module-menu')
<div class="dropdown d-grid d-md-flex module-menu">
    <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none" data-bs-toggle="dropdown">{{ __('languagemanagement::admin.menu') }}</a>
    <div class="dropdown-menu dropdown-module-menu">
        <div class="module-menu-revert">
            <div class="dropdown">
                <button type="button" class="dropdown-module-item dropdown-toggle btn btn-ghost-secondary"
                    data-bs-toggle="dropdown">
                    {{ __('languagemanagement::admin.language_operations') }}
                </button>
                <div class="dropdown-menu">
                    @hasmoduleaccess('languagemanagement', 'view')
                    <a class="dropdown-item" href="{{ route('admin.languagemanagement.index') }}">
                        {{ __('languagemanagement::admin.dashboard') }}
                    </a>
                    @endhasmoduleaccess
                    
                    @if((auth()->check() && auth()->user()->hasRole('root')) || (function_exists('tenant') && tenant() && tenant()->central) || (!function_exists('tenant') || !tenant()))
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">{{ __('languagemanagement::admin.system_languages_header') }}</span>
                    </h6>
                    
                    @hasmoduleaccess('languagemanagement', 'view')
                    <a class="dropdown-item" href="{{ route('admin.languagemanagement.system.index') }}">
                        {{ __('languagemanagement::admin.system_languages') }}
                    </a>
                    @endhasmoduleaccess
                    
                    @hasmoduleaccess('languagemanagement', 'create')
                    <a class="dropdown-item" href="{{ route('admin.languagemanagement.system.manage') }}">
                        {{ __('languagemanagement::admin.add_system_language') }}
                    </a>
                    @endhasmoduleaccess
                    @endif
                    
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">{{ __('languagemanagement::admin.site_languages_header') }}</span>
                    </h6>
                    
                    @hasmoduleaccess('languagemanagement', 'view')
                    <a class="dropdown-item" href="{{ route('admin.languagemanagement.site.index') }}">
                        {{ __('languagemanagement::admin.site_languages') }}
                    </a>
                    @endhasmoduleaccess
                    
                    @hasmoduleaccess('languagemanagement', 'create')
                    <a class="dropdown-item" href="{{ route('admin.languagemanagement.site.manage') }}">
                        {{ __('languagemanagement::admin.add_site_language') }}
                    </a>
                    @endhasmoduleaccess
                    
                </div>
            </div>
            @hasmoduleaccess('languagemanagement', 'create')
            @if((auth()->check() && auth()->user()->hasRole('root')) || (function_exists('tenant') && tenant() && tenant()->central) || (!function_exists('tenant') || !tenant()))
            <a href="{{ route('admin.languagemanagement.system.manage') }}" class="btn btn-primary">
                {{ __('languagemanagement::admin.new_system_language_btn') }}
            </a>
            @else
            <a href="{{ route('admin.languagemanagement.site.manage') }}" class="btn btn-primary">
                {{ __('languagemanagement::admin.new_site_language_btn') }}
            </a>
            @endif
            @endhasmoduleaccess
        </div>
    </div>
</div>
@endpush