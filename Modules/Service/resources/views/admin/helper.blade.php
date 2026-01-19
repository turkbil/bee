{{-- Modules/Service/resources/views/admin/helper.blade.php --}}
{{-- PreTitle --}}
@section('pretitle')
    {{ __('service::admin.service_management') }}
@endsection

{{-- Başlık --}}
@section('title')
    {{ __('service::admin.services') }}
@endsection

{{-- Modül Menüsü --}}
@push('module-menu')

    <div class="dropdown d-grid d-md-flex module-menu">
        <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none"
            data-bs-toggle="dropdown">{{ __('service::admin.menu') }}</a>
        <div class="dropdown-menu dropdown-module-menu">
            <div class="module-menu-revert">
                @hasmoduleaccess('service', 'view')
                    <a href="{{ route('admin.service.index') }}" class="dropdown-module-item btn btn-ghost-primary">
                        {{ __('service::admin.services') }}
                    </a>
                @endhasmoduleaccess

                @hasmoduleaccess('service', 'view')
                    <a href="{{ route('admin.service.category.index') }}" class="dropdown-module-item btn btn-ghost-primary">
                        {{ __('service::admin.categories') }}
                    </a>
                @endhasmoduleaccess

                @hasmoduleaccess('service', 'create')
                    <a href="{{ route('admin.service.manage') }}" class="dropdown-module-item btn btn-primary">
                        {{ __('service::admin.new_service') }}
                    </a>
                @endhasmoduleaccess
            </div>
        </div>
    </div>

@endpush
