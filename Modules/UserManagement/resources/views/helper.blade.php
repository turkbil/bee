{{-- PreTitle --}}
@push('pretitle')
{{ __('usermanagement::general.users') }}
@endpush

{{-- Başlık --}}
@push('title')
{{ __('usermanagement::general.user_list') }}
@endpush

{{-- Modül Menüsü --}}
{{-- Prensip olarak buradaki menüye yeni seçenekler ekleyeceğiz --}}
@push('module-menu')
<div class="dropdown d-grid d-md-flex module-menu">
    <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none" data-bs-toggle="dropdown">{{ __('usermanagement::general.menu') }}</a>
    <div class="dropdown-menu dropdown-module-menu">
        <div class="module-menu-revert">
            <div class="dropdown">
                <button type="button" class="dropdown-module-item dropdown-toggle btn btn-ghost-secondary"
                    data-bs-toggle="dropdown">
                    {{ __('usermanagement::general.user_menu') }}
                </button>
                <div class="dropdown-menu">
                    @hasmoduleaccess('usermanagement', 'view')
                    <a class="dropdown-item" href="{{ route('admin.usermanagement.index') }}">
                        {{ __('usermanagement::general.users') }}
                    </a>
                    @endhasmoduleaccess
                    
                    @hasmoduleaccess('usermanagement', 'create')
                    <a class="dropdown-item" href="{{ route('admin.usermanagement.manage') }}">
                        {{ __('usermanagement::general.add_user') }}
                    </a>
                    @endhasmoduleaccess
                    
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">{{ __('usermanagement::general.activity_records') }}</span>
                    </h6>
                    
                    @hasmoduleaccess('usermanagement', 'view')
                    <a class="dropdown-item" href="{{ route('admin.usermanagement.activity.logs') }}">
                        {{ __('usermanagement::general.activity_logs') }}
                    </a>
                    @endhasmoduleaccess
                    
                    @if(auth()->user()->hasRole('root') || auth()->user()->hasRole('admin'))
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">{{ __('usermanagement::general.role_list') }}</span>
                    </h6>
                    
                    @hasmoduleaccess('usermanagement', 'view')
                    <a class="dropdown-item" href="{{ route('admin.usermanagement.role.index') }}">
                        {{ __('usermanagement::general.roles') }}
                    </a>
                    @endhasmoduleaccess
                    
                    @hasmoduleaccess('usermanagement', 'create')
                    <a class="dropdown-item" href="{{ route('admin.usermanagement.role.manage') }}">
                        {{ __('usermanagement::general.add_role') }}
                    </a>
                    @endhasmoduleaccess
                    
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">{{ __('usermanagement::general.permission_list') }}</span>
                    </h6>
                    
                    @hasmoduleaccess('usermanagement', 'view')
                    <a class="dropdown-item" href="{{ route('admin.usermanagement.permission.index') }}">
                        {{ __('usermanagement::general.permissions') }}
                    </a>
                    @endhasmoduleaccess
                    
                    @hasmoduleaccess('usermanagement', 'create')
                    <a class="dropdown-item" href="{{ route('admin.usermanagement.permission.manage') }}">
                        {{ __('usermanagement::general.add_permission') }}
                    </a>
                    @endhasmoduleaccess
                    
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">{{ __('usermanagement::general.module_permissions') }}</span>
                    </h6>
                    
                    @hasmoduleaccess('usermanagement', 'update')
                    <a class="dropdown-item" href="{{ route('admin.usermanagement.module.permissions') }}">
                        {{ __('usermanagement::general.module_permissions') }}
                    </a>
                    @endhasmoduleaccess
                    @endif
                </div>
            </div>
            @hasmoduleaccess('usermanagement', 'create')
            <a href="{{ route('admin.usermanagement.manage') }}" class="dropdown-module-item btn btn-primary">
                {{ __('usermanagement::general.new_user') }}
            </a>
            @endhasmoduleaccess
        </div>
    </div>
</div>
@endpush