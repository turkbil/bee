{{-- PreTitle --}}
@push('pretitle')
{{ __('admin.components') }}
@endpush

{{-- Başlık --}}
@push('title')
{{ __('widgetmanagement::admin.widget_management') }}
@endpush

{{-- Modül Menüsü --}}
@push('module-menu')
<div class="dropdown d-grid d-md-flex module-menu">
    <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none" data-bs-toggle="dropdown">{{ __('admin.menu') }}</a>
    <div class="dropdown-menu dropdown-module-menu">
        <div class="module-menu-revert">
            <div class="dropdown">
                <button type="button" class="dropdown-module-item dropdown-toggle btn btn-ghost-secondary"
                    data-bs-toggle="dropdown">
                    {{ __('widgetmanagement::admin.widget_menu') }}
                </button>
                <div class="dropdown-menu">
                    @hasmoduleaccess('widgetmanagement', 'view')
                    <a class="dropdown-item" href="{{ route('admin.widgetmanagement.index') }}">
                        {{ __('widgetmanagement::admin.active_components') }}
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.widgetmanagement.gallery') }}">
                        {{ __('widgetmanagement::admin.gallery') }}
                    </a>
                    @endhasmoduleaccess
                    
                    @role('root')
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">{{ __('widgetmanagement::admin.special_components') }}</span>
                    </h6>
                    
                    <a class="dropdown-item" href="{{ route('admin.widgetmanagement.modules') }}">
                        {{ __('widgetmanagement::admin.modules') }}
                    </a>
                    
                    <a class="dropdown-item" href="{{ route('admin.widgetmanagement.files') }}">
                        {{ __('widgetmanagement::admin.ready_files') }}
                    </a>
                    
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">{{ __('widgetmanagement::admin.configuration') }}</span>
                    </h6>
                    
                    <a class="dropdown-item" href="{{ route('admin.widgetmanagement.category.index') }}">
                        {{ __('widgetmanagement::admin.category_management') }}
                    </a>

                    <a class="dropdown-item" href="{{ route('admin.widgetmanagement.manage') }}">
                        {{ __('widgetmanagement::admin.add_component') }}
                    </a>                    
                    @endrole
                </div>
            </div>
            <a href="{{ route('admin.widgetmanagement.index') }}" class="dropdown-module-item btn {{ request()->routeIs('admin.widgetmanagement.index') ? 'btn-primary' : 'btn-outline-primary' }}">
                {{ __('widgetmanagement::admin.active_components') }}
            </a>
            <a href="{{ route('admin.widgetmanagement.gallery') }}" class="dropdown-module-item btn {{ request()->routeIs('admin.widgetmanagement.gallery') ? 'btn-primary' : 'btn-outline-primary' }}">
                {{ __('widgetmanagement::admin.gallery') }}
            </a>
        </div>
    </div>
</div>
@endpush