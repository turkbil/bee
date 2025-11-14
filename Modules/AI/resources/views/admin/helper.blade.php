{{-- Modules/AI/resources/views/admin/helper.blade.php --}}
{{-- PreTitle --}}
@section('pretitle')
    {{ __('admin.artificial_intelligence') }}
@endsection

{{-- Başlık --}}
@section('title')
    AI
@endsection

{{-- Modül Menüsü --}}
@push('module-menu')

    <div class="dropdown d-grid d-md-flex module-menu">
        <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none"
            data-bs-toggle="dropdown">{{ __('admin.menu') }}</a>
        <div class="dropdown-menu dropdown-module-menu">
            <div class="module-menu-revert">
                @hasmoduleaccess('ai', 'view')
                    <a href="{{ route('admin.ai.index') }}" class="dropdown-module-item btn btn-primary">
                        <i class="fa fa-comment me-2"></i> {{ __('ai::admin.conversations') }}
                    </a>
                @endhasmoduleaccess
            </div>
        </div>
    </div>

@endpush
