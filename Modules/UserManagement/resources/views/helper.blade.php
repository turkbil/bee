{{-- PreTitle --}}
@push('pretitle')
{{ __('usermanagement::admin.users') }}
@endpush

{{-- Başlık --}}
@push('title')
{{ __('usermanagement::admin.user_list') }}
@endpush

{{-- Modül Menüsü --}}
{{-- Prensip olarak buradaki menüye yeni seçenekler ekleyeceğiz --}}
@push('module-menu')
<div class="dropdown d-grid d-md-flex module-menu">
    <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none" data-bs-toggle="dropdown">{{ __('usermanagement::admin.menu') }}</a>
    <div class="dropdown-menu dropdown-module-menu">
        <div class="module-menu-revert">
            <div class="dropdown">
                <button type="button" class="dropdown-module-item dropdown-toggle btn btn-ghost-secondary"
                    data-bs-toggle="dropdown">
                    {{ __('usermanagement::admin.user_menu') }}
                </button>
                <div class="dropdown-menu">
                    @hasmoduleaccess('usermanagement', 'view')
                    <a class="dropdown-item" href="{{ route('admin.usermanagement.index') }}">
                        {{ __('usermanagement::admin.users') }}
                    </a>
                    @endhasmoduleaccess
                    
                    @hasmoduleaccess('usermanagement', 'create')
                    <a class="dropdown-item" href="{{ route('admin.usermanagement.manage') }}">
                        {{ __('usermanagement::admin.add_user') }}
                    </a>
                    @endhasmoduleaccess
                    
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">{{ __('usermanagement::admin.activity_records') }}</span>
                    </h6>
                    
                    @hasmoduleaccess('usermanagement', 'view')
                    <a class="dropdown-item" href="{{ route('admin.usermanagement.activity.logs') }}">
                        {{ __('usermanagement::admin.activity_logs') }}
                    </a>
                    @endhasmoduleaccess
                    
                    @if(auth()->user()->hasRole('root') || auth()->user()->hasRole('admin'))
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">{{ __('usermanagement::admin.role_list') }}</span>
                    </h6>
                    
                    @hasmoduleaccess('usermanagement', 'view')
                    <a class="dropdown-item" href="{{ route('admin.usermanagement.role.index') }}">
                        {{ __('usermanagement::admin.roles') }}
                    </a>
                    @endhasmoduleaccess
                    
                    @hasmoduleaccess('usermanagement', 'create')
                    <a class="dropdown-item" href="{{ route('admin.usermanagement.role.manage') }}">
                        {{ __('usermanagement::admin.add_role') }}
                    </a>
                    @endhasmoduleaccess
                    
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">{{ __('usermanagement::admin.permission_list') }}</span>
                    </h6>
                    
                    @hasmoduleaccess('usermanagement', 'view')
                    <a class="dropdown-item" href="{{ route('admin.usermanagement.permission.index') }}">
                        {{ __('usermanagement::admin.permissions') }}
                    </a>
                    @endhasmoduleaccess
                    
                    @hasmoduleaccess('usermanagement', 'create')
                    <a class="dropdown-item" href="{{ route('admin.usermanagement.permission.manage') }}">
                        {{ __('usermanagement::admin.add_permission') }}
                    </a>
                    @endhasmoduleaccess
                    
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">{{ __('usermanagement::admin.module_permissions') }}</span>
                    </h6>
                    
                    @hasmoduleaccess('usermanagement', 'update')
                    <a class="dropdown-item" href="{{ route('admin.usermanagement.module.permissions') }}">
                        {{ __('usermanagement::admin.module_permissions') }}
                    </a>
                    @endhasmoduleaccess
                    @endif
                </div>
            </div>
            @hasmoduleaccess('usermanagement', 'create')
            <a href="{{ route('admin.usermanagement.manage') }}" class="dropdown-module-item btn btn-primary">
                {{ __('usermanagement::admin.new_user') }}
            </a>
            @endhasmoduleaccess
        </div>
    </div>
</div>
@endpush