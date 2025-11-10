{{-- Modules/ReviewSystem/resources/views/admin/helper.blade.php --}}
{{-- PreTitle --}}
@section('pretitle')
    {{ __('reviewsystem::admin.reviewsystem_management') }}
@endsection

{{-- Başlık --}}
@section('title')
    {{ __('reviewsystem::admin.reviewsystems') }}
@endsection

{{-- Modül Menüsü --}}
@push('module-menu')

    <div class="dropdown d-grid d-md-flex module-menu">
        <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none"
            data-bs-toggle="dropdown">{{ __('reviewsystem::admin.menu') }}</a>
        <div class="dropdown-menu dropdown-module-menu">
            <div class="module-menu-revert">
                @hasmoduleaccess('reviewsystem', 'view')
                    <a href="{{ route('admin.reviewsystem.index') }}" class="dropdown-module-item btn btn-ghost-primary">
                        {{ __('reviewsystem::admin.reviewsystems') }}
                    </a>
                @endhasmoduleaccess

                @hasmoduleaccess('reviewsystem', 'create')
                    <a href="{{ route('admin.reviewsystem.manage') }}" class="dropdown-module-item btn btn-primary">
                        {{ __('reviewsystem::admin.new_reviewsystem') }}
                    </a>
                @endhasmoduleaccess
            </div>
        </div>
    </div>

@endpush
