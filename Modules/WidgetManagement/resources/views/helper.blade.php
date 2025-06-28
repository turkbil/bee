{{-- PreTitle --}}
@push('pretitle')
{{ __('common.components') }}
@endpush

{{-- Başlık --}}
@push('title')
{{ __('widgetmanagement.title') }}
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
                    {{ __('widgetmanagement.widget.menu') }}
                </button>
                <div class="dropdown-menu">
                    @hasmoduleaccess('widgetmanagement', 'view')
                    <a class="dropdown-item" href="{{ route('admin.widgetmanagement.index') }}">
                        {{ __('widgetmanagement.active_components') }}
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.widgetmanagement.gallery') }}">
                        {{ __('widgetmanagement.gallery') }}
                    </a>
                    @endhasmoduleaccess
                    
                    @role('root')
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">{{ __('widgetmanagement.special_components') }}</span>
                    </h6>
                    
                    <a class="dropdown-item" href="{{ route('admin.widgetmanagement.modules') }}">
                        {{ __('widgetmanagement.modules') }}
                    </a>
                    
                    <a class="dropdown-item" href="{{ route('admin.widgetmanagement.files') }}">
                        {{ __('widgetmanagement.ready_files') }}
                    </a>
                    
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">{{ __('widgetmanagement.configuration') }}</span>
                    </h6>
                    
                    <a class="dropdown-item" href="{{ route('admin.widgetmanagement.category.index') }}">
                        {{ __('widgetmanagement.category_management') }}
                    </a>

                    <a class="dropdown-item" href="{{ route('admin.widgetmanagement.manage') }}">
                        {{ __('widgetmanagement.add_component') }}
                    </a>                    
                    @endrole
                </div>
            </div>
            <a href="{{ route('admin.widgetmanagement.index') }}" class="dropdown-module-item btn {{ request()->routeIs('admin.widgetmanagement.index') ? 'btn-primary' : 'btn-outline-primary' }}">
                {{ __('widgetmanagement.active_components') }}
            </a>
            <a href="{{ route('admin.widgetmanagement.gallery') }}" class="dropdown-module-item btn {{ request()->routeIs('admin.widgetmanagement.gallery') ? 'btn-primary' : 'btn-outline-primary' }}">
                {{ __('widgetmanagement.gallery') }}
            </a>
        </div>
    </div>
</div>
@endpush