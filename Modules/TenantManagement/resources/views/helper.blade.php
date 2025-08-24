{{-- PreTitle --}}
@section('pretitle')
{{ __('tenantmanagement::admin.tenant_management') }}
@endsection

@push('pretitle')
{{ __('tenantmanagement::admin.tenant_management') }}
@endpush

{{-- Başlık --}}
@push('title')
{{ __('tenantmanagement::admin.tenants') }}
@endpush

{{-- Modül Menüsü --}}
{{-- Prensip olarak buradaki menüye yeni seçenekler ekleyeceğiz --}}
@push('module-menu')
<div class="dropdown d-grid d-md-flex module-menu">
    <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none" data-bs-toggle="dropdown">{{ __('tenantmanagement::admin.menu') }}</a>
    <div class="dropdown-menu dropdown-module-menu">
        <div class="module-menu-revert">
            <div class="dropdown">
                <button type="button" class="dropdown-module-item dropdown-toggle btn btn-ghost-secondary"
                    data-bs-toggle="dropdown">
                    {{ __('tenantmanagement::admin.tenant_management') }}
                </button>
                <div class="dropdown-menu">
                    {{-- Temel Kiracı Yönetimi --}}
                    @hasmoduleaccess('tenantmanagement', 'view')
                    <a class="dropdown-item" href="{{ route('admin.tenantmanagement.index') }}">
                        <i class="icon-menu fas fa-users"></i>{{ __('tenantmanagement::admin.tenants') }}
                    </a>
                    @endhasmoduleaccess
                    
@if(request()->routeIs('admin.tenantmanagement.index'))
                    @hasmoduleaccess('tenantmanagement', 'create')
                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modal-tenant-add">
                        <i class="icon-menu fas fa-user-plus"></i>{{ __('tenantmanagement::admin.add_new_tenant') }}
                    </a>
                    @endhasmoduleaccess
@endif
                    
                    {{-- Kaynak İzleme ve Yönetim --}}
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">{{ __('tenantmanagement::admin.resource_monitoring') }}</span>
                    </h6>
                    
                    @hasmoduleaccess('tenantmanagement', 'view')
                    <a class="dropdown-item" href="{{ route('admin.tenantmanagement.monitoring') }}">
                        <i class="icon-menu fas fa-chart-line"></i>{{ __('tenantmanagement::admin.resource_monitoring') }}
                    </a>
                    @endhasmoduleaccess
                    
                    @hasmoduleaccess('tenantmanagement', 'view')
                    <a class="dropdown-item" href="{{ route('admin.tenantmanagement.limits') }}">
                        <i class="icon-menu fas fa-tachometer-alt"></i>{{ __('tenantmanagement::admin.resource_limits') }}
                    </a>
                    @endhasmoduleaccess
                    
                    @hasmoduleaccess('tenantmanagement', 'view')
                    <a class="dropdown-item" href="{{ route('admin.tenantmanagement.rate-limits') }}">
                        <i class="icon-menu fas fa-stopwatch"></i>{{ __('tenantmanagement::admin.rate_limits') }}
                    </a>
                    @endhasmoduleaccess
                    
                    {{-- Cache Yönetimi --}}
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">{{ __('tenantmanagement::admin.cache_management') }}</span>
                    </h6>
                    
                    @hasmoduleaccess('tenantmanagement', 'view')
                    <a class="dropdown-item" href="{{ route('admin.tenantmanagement.cache') }}">
                        <i class="icon-menu fas fa-database"></i>{{ __('tenantmanagement::admin.cache_management') }}
                    </a>
                    @endhasmoduleaccess
                    
                    @hasmoduleaccess('tenantmanagement', 'view')
                    <a class="dropdown-item" href="{{ route('admin.tenantmanagement.pool-monitoring') }}">
                        <i class="icon-menu fas fa-server"></i>{{ __('tenantmanagement::admin.pool_monitoring') }}
                    </a>
                    @endhasmoduleaccess
                    
                    {{-- Otomatik Ölçeklendirme --}}
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">{{ __('tenantmanagement::admin.auto_scaling') }}</span>
                    </h6>
                    
                    @hasmoduleaccess('tenantmanagement', 'view')
                    <a class="dropdown-item" href="{{ route('admin.tenantmanagement.auto-scaling') }}">
                        <i class="icon-menu fas fa-expand-arrows-alt"></i>{{ __('tenantmanagement::admin.auto_scaling') }}
                    </a>
                    @endhasmoduleaccess
                    
                    @hasmoduleaccess('tenantmanagement', 'view')
                    <a class="dropdown-item" href="{{ route('admin.tenantmanagement.health-check') }}">
                        <i class="icon-menu fas fa-heartbeat"></i>{{ __('tenantmanagement::admin.health_check') }}
                    </a>
                    @endhasmoduleaccess
                    
                </div>
            </div>
@if(request()->routeIs('admin.tenantmanagement.index'))
            @hasmoduleaccess('tenantmanagement', 'create')
            <a href="#" class="dropdown-module-item btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-tenant-add">
                <i class="icon-menu fas fa-user-plus"></i>{{ __('tenantmanagement::admin.add_new_tenant') }}
            </a>
            @endhasmoduleaccess
@else
            @hasmoduleaccess('tenantmanagement', 'view')
            <a href="{{ route('admin.tenantmanagement.index') }}" class="dropdown-module-item btn btn-secondary">
                <i class="icon-menu fas fa-users"></i>{{ __('tenantmanagement::admin.tenants') }}
            </a>
            @endhasmoduleaccess
@endif
        </div>
    </div>
</div>
@endpush