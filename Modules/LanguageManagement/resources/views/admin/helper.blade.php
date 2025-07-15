{{-- Modules/LanguageManagement/resources/views/admin/helper.blade.php --}}
{{-- PreTitle --}}
@section('pretitle')
{{ __('languagemanagement::admin.title') }}
@endsection

{{-- Başlık --}}
@section('title')
{{ __('languagemanagement::admin.multilanguage_system') }}
@endsection

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
                        <span class="dropdown-header">{{ __('languagemanagement::admin.admin_languages_header') }}</span>
                    </h6>
                    
                    @hasmoduleaccess('languagemanagement', 'view')
                    <a class="dropdown-item" href="{{ route('admin.languagemanagement.system.index') }}">
                        {{ __('languagemanagement::admin.admin_languages') }}
                    </a>
                    @endhasmoduleaccess
                    
                    @hasmoduleaccess('languagemanagement', 'create')
                    <a class="dropdown-item" href="{{ route('admin.languagemanagement.system.manage') }}">
                        {{ __('languagemanagement::admin.add_admin_language') }}
                    </a>
                    @endhasmoduleaccess
                    @endif
                    
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">{{ __('languagemanagement::admin.tenant_languages_header') }}</span>
                    </h6>
                    
                    @hasmoduleaccess('languagemanagement', 'view')
                    <a class="dropdown-item" href="{{ route('admin.languagemanagement.site.index') }}">
                        {{ __('languagemanagement::admin.tenant_languages') }}
                    </a>
                    @endhasmoduleaccess
                    
                    @hasmoduleaccess('languagemanagement', 'create')
                    <a class="dropdown-item" href="{{ route('admin.languagemanagement.site.manage') }}">
                        {{ __('languagemanagement::admin.add_tenant_language') }}
                    </a>
                    @endhasmoduleaccess
                    
                    <div class="dropdown-divider"></div>
                    
                    @hasmoduleaccess('languagemanagement', 'view')
                    <a class="dropdown-item" href="{{ route('admin.languagemanagement.translation-checker') }}">
                        {{ __('languagemanagement::admin.translation_checker') }}
                    </a>
                    @endhasmoduleaccess
                    
                </div>
            </div>
            @hasmoduleaccess('languagemanagement', 'create')
            @if((auth()->check() && auth()->user()->hasRole('root')) || (function_exists('tenant') && tenant() && tenant()->central) || (!function_exists('tenant') || !tenant()))
            <a href="{{ route('admin.languagemanagement.system.manage') }}" class="btn btn-primary">
                {{ __('languagemanagement::admin.new_admin_language_btn') }}
            </a>
            @else
            <a href="{{ route('admin.languagemanagement.site.manage') }}" class="btn btn-primary">
                {{ __('languagemanagement::admin.new_tenant_language_btn') }}
            </a>
            @endif
            @endhasmoduleaccess
        </div>
    </div>
</div>
@endpush