{{-- Modules/Muzibu/resources/views/admin/helper-song.blade.php --}}
{{-- PreTitle --}}
@section('pretitle')
    {{ __('muzibu::admin.song_management') }}
@endsection

{{-- Başlık --}}
@section('title')
    {{ __('muzibu::admin.songs') }}
@endsection

{{-- Modül Menüsü --}}
@push('module-menu')

    <div class="dropdown d-grid d-md-flex module-menu">
        <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none"
            data-bs-toggle="dropdown">{{ __('muzibu::admin.menu') }}</a>
        <div class="dropdown-menu dropdown-module-menu">
            <div class="module-menu-revert">
                @hasmoduleaccess('muzibu', 'view')
                    <a href="{{ route('admin.muzibu.song.index') }}" class="dropdown-module-item btn btn-ghost-primary">
                        {{ __('muzibu::admin.songs') }}
                    </a>
                @endhasmoduleaccess

                @hasmoduleaccess('muzibu', 'create')
                    <a href="{{ route('admin.muzibu.song.manage') }}" class="dropdown-module-item btn btn-primary">
                        {{ __('muzibu::admin.new_song') }}
                    </a>
                @endhasmoduleaccess
            </div>
        </div>
    </div>

@endpush
