{{-- Modules/MediaManagement/resources/views/admin/helper.blade.php --}}
{{-- PreTitle --}}
@section('pretitle')
    {{ __('mediamanagement::admin.module_management') }}
@endsection

{{-- Başlık --}}
@section('title')
    {{ __('mediamanagement::admin.module_name') }}
@endsection

{{-- Modül Menüsü --}}
@push('module-menu')

    <div class="dropdown d-grid d-md-flex module-menu">
        <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none"
            data-bs-toggle="dropdown">{{ __('admin.menu') }}</a>
        <div class="dropdown-menu dropdown-module-menu">
            <div class="module-menu-revert">
                @hasmoduleaccess('mediamanagement', 'view')
                    <a href="{{ route('admin.mediamanagement.index') }}" class="dropdown-module-item btn btn-primary">
                        {{ __('mediamanagement::admin.about_module') }}
                    </a>
                @endhasmoduleaccess
            </div>
        </div>
    </div>

@endpush
