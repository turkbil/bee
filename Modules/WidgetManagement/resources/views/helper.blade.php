{{-- PreTitle --}}
@push('pretitle')
{{ t('widgetmanagement.title') }}
@endpush

{{-- Başlık --}}
@push('title')
{{ t('widgetmanagement.title') }}
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
                    {{ t('widgetmanagement.widget.menu') }}
                </button>
                <div class="dropdown-menu">
                    @hasmoduleaccess('widgetmanagement', 'view')
                    <a class="dropdown-item" href="{{ route('admin.widgetmanagement.index') }}">
                        {{ t('widgetmanagement.active_components') }}
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.widgetmanagement.gallery') }}">
                        {{ t('widgetmanagement.gallery') }}
                    </a>
                    @endhasmoduleaccess
                    
                    @role('root')
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">{{ t('widgetmanagement.special_components') }}</span>
                    </h6>
                    
                    <a class="dropdown-item" href="{{ route('admin.widgetmanagement.modules') }}">
                        {{ t('widgetmanagement.modules') }}
                    </a>
                    
                    <a class="dropdown-item" href="{{ route('admin.widgetmanagement.files') }}">
                        {{ t('widgetmanagement.ready_files') }}
                    </a>
                    
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">{{ t('widgetmanagement.configuration') }}</span>
                    </h6>
                    
                    <a class="dropdown-item" href="{{ route('admin.widgetmanagement.category.index') }}">
                        {{ t('widgetmanagement.category_management') }}
                    </a>

                    <a class="dropdown-item" href="{{ route('admin.widgetmanagement.manage') }}">
                        {{ t('widgetmanagement.add_component') }}
                    </a>                    
                    @endrole
                </div>
            </div>
            <a href="{{ route('admin.widgetmanagement.index') }}" class="dropdown-module-item btn {{ request()->routeIs('admin.widgetmanagement.index') ? 'btn-primary' : 'btn-outline-primary' }}">
                {{ t('widgetmanagement.active_components') }}
            </a>
            <a href="{{ route('admin.widgetmanagement.gallery') }}" class="dropdown-module-item btn {{ request()->routeIs('admin.widgetmanagement.gallery') ? 'btn-primary' : 'btn-outline-primary' }}">
                {{ t('widgetmanagement.gallery') }}
            </a>
        </div>
    </div>
</div>
@endpush