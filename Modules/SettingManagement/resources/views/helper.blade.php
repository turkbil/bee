{{-- Modules/SettingManagement/resources/views/helper.blade.php --}}
{{-- PreTitle --}}
@push('pretitle')
{{ __('common.settings') }}
@endpush

{{-- Başlık --}}
@push('title')
{{ __('settingmanagement.title') }}
@endpush

{{-- Modül Menüsü --}}
@push('module-menu')
<div class="dropdown d-grid d-md-flex module-menu">
    <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none" data-bs-toggle="dropdown">{{ __('common.menu') }}</a>
    <div class="dropdown-menu dropdown-module-menu">
        <div class="module-menu-revert">
            <div class="dropdown">
                <button type="button" class="dropdown-module-item dropdown-toggle btn btn-ghost-secondary"
                    data-bs-toggle="dropdown">
                    {{ __('settingmanagement.operations') }}
                </button>
                <div class="dropdown-menu">
                    @hasmoduleaccess('settingmanagement', 'view')
                    <a class="dropdown-item" href="{{ route('admin.settingmanagement.index') }}">
                        {{ __('settingmanagement.list') }}
                    </a>
                    @endhasmoduleaccess
                    
                    @if(auth()->user()->hasRole('root'))
                    <a class="dropdown-item" href="{{ route('admin.settingmanagement.group.manage') }}">
                        {{ __('settingmanagement.group.create') }}
                    </a>
                    
                    <a class="dropdown-item" href="{{ route('admin.settingmanagement.tenant.settings') }}">
                        {{ __('settingmanagement.tenant_settings') }}
                    </a>
                    @endif
                </div>
            </div>
            @hasmoduleaccess('settingmanagement', 'view')
            <a href="{{ route('admin.settingmanagement.index') }}" class="btn btn-primary">
                {{ __('settingmanagement.list') }}
            </a>
            @endhasmoduleaccess
        </div>
    </div>
</div>
@endpush