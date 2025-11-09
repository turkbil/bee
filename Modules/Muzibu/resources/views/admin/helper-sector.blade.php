{{-- Modules/Muzibu/resources/views/admin/helper-sector.blade.php --}}
{{-- PreTitle --}}
@section('pretitle')
    {{ __('muzibu::admin.sector_management') }}
@endsection

{{-- Başlık --}}
@section('title')
    {{ __('muzibu::admin.sectors') }}
@endsection

{{-- Modül Menüsü --}}
@push('module-menu')

    <div class="dropdown d-grid d-md-flex module-menu">
        <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none"
            data-bs-toggle="dropdown">{{ __('muzibu::admin.menu') }}</a>
        <div class="dropdown-menu dropdown-module-menu">
            <div class="module-menu-revert">
                @hasmoduleaccess('muzibu', 'view')
                    <a href="{{ route('admin.muzibu.sector.index') }}" class="dropdown-module-item btn btn-ghost-primary">
                        {{ __('muzibu::admin.sectors') }}
                    </a>
                @endhasmoduleaccess

                @hasmoduleaccess('muzibu', 'create')
                    <a href="{{ route('admin.muzibu.sector.manage') }}" class="dropdown-module-item btn btn-primary">
                        {{ __('muzibu::admin.new_sector') }}
                    </a>
                @endhasmoduleaccess
            </div>
        </div>
    </div>

@endpush
