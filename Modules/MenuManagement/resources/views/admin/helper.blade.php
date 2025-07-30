{{-- Modules/MenuManagement/resources/views/admin/helper.blade.php --}}
{{-- PreTitle --}}
@section('pretitle')
    {{ __('menumanagement::admin.menu_management') }}
@endsection

{{-- Başlık --}}
@section('title')
    {{ __('menumanagement::admin.menus') }}
@endsection

{{-- Modül Menüsü --}}
@push('module-menu')
<div class="dropdown d-grid d-md-flex module-menu">
    <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none" data-bs-toggle="dropdown">{{ __('menumanagement::admin.menu') }}</a>
    <div class="dropdown-menu dropdown-module-menu">
        <div class="module-menu-revert">
            <div class="dropdown">
                <button type="button" class="dropdown-module-item dropdown-toggle btn btn-ghost-secondary"
                    data-bs-toggle="dropdown">
                    {{ __('menumanagement::admin.menu_operations') }}
                </button>
                <div class="dropdown-menu">
                    @hasmoduleaccess('menumanagement', 'view')
                    <a class="dropdown-item" href="{{ route('admin.menumanagement.index') }}">
                        <i class="icon-menu fas fa-bars"></i>{{ __('menumanagement::admin.menus') }}
                    </a>
                    @endhasmoduleaccess
                    
                    @hasmoduleaccess('menumanagement', 'create')
                    <a class="dropdown-item" href="{{ route('admin.menumanagement.menu.manage') }}">
                        <i class="icon-menu fas fa-plus"></i>{{ __('menumanagement::admin.new_menu') }}
                    </a>
                    @endhasmoduleaccess
                </div>
            </div>
            @hasmoduleaccess('menumanagement', 'create')
            <a href="{{ route('admin.menumanagement.menu.manage') }}" class="btn btn-primary">
                <i class="icon-menu fas fa-plus"></i>{{ __('menumanagement::admin.new_menu') }}
            </a>
            @endhasmoduleaccess
        </div>
    </div>
</div>
@endpush
