{{-- PreTitle --}}
@push('pretitle')
{{ t('usermanagement::general.users') }}
@endpush

{{-- Başlık --}}
@push('title')
{{ t('usermanagement::general.user_list') }}
@endpush

{{-- Modül Menüsü --}}
{{-- Prensip olarak buradaki menüye yeni seçenekler ekleyeceğiz --}}
@push('module-menu')
<div class="dropdown d-grid d-md-flex module-menu">
    <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none" data-bs-toggle="dropdown">{{ t('usermanagement::general.menu') }}</a>
    <div class="dropdown-menu dropdown-module-menu">
        <div class="module-menu-revert">
            <div class="dropdown">
                <button type="button" class="dropdown-module-item dropdown-toggle btn btn-ghost-secondary"
                    data-bs-toggle="dropdown">
                    {{ t('usermanagement::general.user_menu') }}
                </button>
                <div class="dropdown-menu">
                    @hasmoduleaccess('usermanagement', 'view')
                    <a class="dropdown-item" href="{{ route('admin.usermanagement.index') }}">
                        {{ t('usermanagement::general.users') }}
                    </a>
                    @endhasmoduleaccess
                    
                    @hasmoduleaccess('usermanagement', 'create')
                    <a class="dropdown-item" href="{{ route('admin.usermanagement.manage') }}">
                        {{ t('usermanagement::general.add_user') }}
                    </a>
                    @endhasmoduleaccess
                    
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">{{ t('usermanagement::general.activity_records') }}</span>
                    </h6>
                    
                    @hasmoduleaccess('usermanagement', 'view')
                    <a class="dropdown-item" href="{{ route('admin.usermanagement.activity.logs') }}">
                        {{ t('usermanagement::general.activity_logs') }}
                    </a>
                    @endhasmoduleaccess
                    
                    @if(auth()->user()->hasRole('root') || auth()->user()->hasRole('admin'))
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">{{ t('usermanagement::general.role_list') }}</span>
                    </h6>
                    
                    @hasmoduleaccess('usermanagement', 'view')
                    <a class="dropdown-item" href="{{ route('admin.usermanagement.role.index') }}">
                        {{ t('usermanagement::general.roles') }}
                    </a>
                    @endhasmoduleaccess
                    
                    @hasmoduleaccess('usermanagement', 'create')
                    <a class="dropdown-item" href="{{ route('admin.usermanagement.role.manage') }}">
                        {{ t('usermanagement::general.add_role') }}
                    </a>
                    @endhasmoduleaccess
                    
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">{{ t('usermanagement::general.permission_list') }}</span>
                    </h6>
                    
                    @hasmoduleaccess('usermanagement', 'view')
                    <a class="dropdown-item" href="{{ route('admin.usermanagement.permission.index') }}">
                        {{ t('usermanagement::general.permissions') }}
                    </a>
                    @endhasmoduleaccess
                    
                    @hasmoduleaccess('usermanagement', 'create')
                    <a class="dropdown-item" href="{{ route('admin.usermanagement.permission.manage') }}">
                        {{ t('usermanagement::general.add_permission') }}
                    </a>
                    @endhasmoduleaccess
                    
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">{{ t('usermanagement::general.module_permissions') }}</span>
                    </h6>
                    
                    @hasmoduleaccess('usermanagement', 'update')
                    <a class="dropdown-item" href="{{ route('admin.usermanagement.module.permissions') }}">
                        {{ t('usermanagement::general.module_permissions') }}
                    </a>
                    @endhasmoduleaccess
                    @endif
                </div>
            </div>
            @hasmoduleaccess('usermanagement', 'create')
            <a href="{{ route('admin.usermanagement.manage') }}" class="dropdown-module-item btn btn-primary">
                {{ t('usermanagement::general.new_user') }}
            </a>
            @endhasmoduleaccess
        </div>
    </div>
</div>
@endpush