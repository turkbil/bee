{{-- PreTitle --}}
@section('pretitle')
{{ __('muzibu::admin.muzibu') }}
@endsection

@push('pretitle')
{{ __('muzibu::admin.muzibu') }}
@endpush

{{-- Başlık --}}
@push('title')
{{ __('muzibu::admin.muzibu_management') }}
@endpush

{{-- Modül Menüsü --}}
@push('module-menu')
<div class="dropdown d-grid d-md-flex module-menu">
    <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none" data-bs-toggle="dropdown">{{ __('muzibu::admin.menu') }}</a>
    <div class="dropdown-menu dropdown-module-menu">
        <div class="module-menu-revert">
            @hasmoduleaccess('muzibu', 'view')
            <a href="{{ route('admin.muzibu.artist.index') }}" class="dropdown-module-item btn btn-ghost-secondary">
                <i class="icon-menu fas fa-user-music"></i>{{ __('muzibu::admin.artists') }}
            </a>
            @endhasmoduleaccess

            @hasmoduleaccess('muzibu', 'view')
            <a href="{{ route('admin.muzibu.album.index') }}" class="dropdown-module-item btn btn-ghost-secondary">
                <i class="icon-menu fas fa-compact-disc"></i>{{ __('muzibu::admin.albums') }}
            </a>
            @endhasmoduleaccess

            @hasmoduleaccess('muzibu', 'view')
            <a href="{{ route('admin.muzibu.song.index') }}" class="dropdown-module-item btn btn-ghost-primary">
                <i class="icon-menu fas fa-music"></i>{{ __('muzibu::admin.songs') }}
            </a>
            @endhasmoduleaccess

            @hasmoduleaccess('muzibu', 'view')
            <a href="{{ route('admin.muzibu.genre.index') }}" class="dropdown-module-item btn btn-ghost-secondary">
                <i class="icon-menu fas fa-guitar"></i>{{ __('muzibu::admin.genres') }}
            </a>
            @endhasmoduleaccess

            @hasmoduleaccess('muzibu', 'view')
            <a href="{{ route('admin.muzibu.playlist.index') }}" class="dropdown-module-item btn btn-ghost-secondary">
                <i class="icon-menu fas fa-list-music"></i>{{ __('muzibu::admin.playlists') }}
            </a>
            @endhasmoduleaccess

            @hasmoduleaccess('muzibu', 'view')
            <a href="{{ route('admin.muzibu.radio.index') }}" class="dropdown-module-item btn btn-ghost-secondary">
                <i class="icon-menu fas fa-broadcast-tower"></i>{{ __('muzibu::admin.radios') }}
            </a>
            @endhasmoduleaccess

            @hasmoduleaccess('muzibu', 'view')
            <a href="{{ route('admin.muzibu.sector.index') }}" class="dropdown-module-item btn btn-ghost-secondary">
                <i class="icon-menu fas fa-layer-group"></i>{{ __('muzibu::admin.sectors') }}
            </a>
            @endhasmoduleaccess

            @hasmoduleaccess('muzibu', 'create')
            <a href="{{ route('admin.muzibu.song.manage') }}" class="dropdown-module-item btn btn-primary">
                <i class="icon-menu fas fa-plus"></i>{{ __('muzibu::admin.new_song') }}
            </a>
            @endhasmoduleaccess
        </div>
    </div>
</div>
@endpush
