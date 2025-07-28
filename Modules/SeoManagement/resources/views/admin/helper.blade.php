{{-- Modules/SeoManagement/resources/views/admin/helper.blade.php --}}
{{-- PreTitle --}}
@section('pretitle')
    {{ __('admin.content_management') }}
@endsection

{{-- Başlık --}}
@section('title')
    {{ __('admin.seo_management_title') }}
@endsection

{{-- Modül Menüsü --}}
@push('module-menu')

    <div class="dropdown d-grid d-md-flex module-menu">
        <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none"
            data-bs-toggle="dropdown">{{ __('admin.menu') }}</a>
        <div class="dropdown-menu dropdown-module-menu">
            <div class="module-menu-revert">
                @hasmoduleaccess('seomanagement', 'view')
                    <a href="{{ route('admin.seomanagement.index') }}" class="dropdown-module-item btn btn-ghost-primary">
                        {{ __('admin.seo_management_title') }}
                    </a>
                @endhasmoduleaccess
            </div>
        </div>
    </div>

@endpush