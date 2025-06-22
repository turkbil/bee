{{-- Modules/SettingManagement/resources/views/helper.blade.php --}}
{{-- PreTitle --}}
@push('pretitle')
{{ t('settingmanagement.title') }}
@endpush

{{-- Başlık --}}
@push('title')
{{ t('settingmanagement.title') }}
@endpush

{{-- Modül Menüsü --}}
@push('module-menu')
<div class="dropdown d-grid d-md-flex module-menu">
    <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none" data-bs-toggle="dropdown">{{ t('common.menu') }}</a>
    <div class="dropdown-menu dropdown-module-menu">
        <div class="module-menu-revert">
            <div class="dropdown">
                <button type="button" class="dropdown-module-item dropdown-toggle btn btn-ghost-secondary"
                    data-bs-toggle="dropdown">
                    {{ t('settingmanagement.operations') }}
                </button>
                <div class="dropdown-menu">
                    @hasmoduleaccess('settingmanagement', 'view')
                    <a class="dropdown-item" href="{{ route('admin.settingmanagement.index') }}">
                        {{ t('settingmanagement.list') }}
                    </a>
                    @endhasmoduleaccess
                    
                    @if(auth()->user()->hasRole('root'))
                    <a class="dropdown-item" href="{{ route('admin.settingmanagement.group.manage') }}">
                        {{ t('settingmanagement.group.create') }}
                    </a>
                    
                    <a class="dropdown-item" href="{{ route('admin.settingmanagement.tenant.settings') }}">
                        {{ t('settingmanagement.tenant_settings') }}
                    </a>
                    @endif
                </div>
            </div>
            @hasmoduleaccess('settingmanagement', 'view')
            <a href="{{ route('admin.settingmanagement.index') }}" class="btn btn-primary">
                {{ t('settingmanagement.list') }}
            </a>
            @endhasmoduleaccess
        </div>
    </div>
</div>
@endpush