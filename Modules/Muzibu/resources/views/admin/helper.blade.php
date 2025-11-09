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
            <div class="dropdown">
                <button type="button" class="dropdown-module-item dropdown-toggle btn btn-ghost-secondary"
                    data-bs-toggle="dropdown">
                    {{ __('muzibu::admin.content_menu') }}
                </button>
                <div class="dropdown-menu">
                    {{-- Artists Section --}}
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">{{ __('muzibu::admin.artists') }}</span>
                    </h6>

                    @hasmoduleaccess('muzibu', 'view')
                    <a class="dropdown-item" href="{{ route('admin.muzibu.artist.index') }}">
                        <i class="icon-menu fas fa-user-music"></i>{{ __('muzibu::admin.artist_list') }}
                    </a>
                    @endhasmoduleaccess

                    @hasmoduleaccess('muzibu', 'create')
                    <a class="dropdown-item" href="{{ route('admin.muzibu.artist.manage') }}">
                        <i class="icon-menu fas fa-plus-circle"></i>{{ __('muzibu::admin.add_artist') }}
                    </a>
                    @endhasmoduleaccess

                    {{-- Albums Section --}}
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">{{ __('muzibu::admin.albums') }}</span>
                    </h6>

                    @hasmoduleaccess('muzibu', 'view')
                    <a class="dropdown-item" href="{{ route('admin.muzibu.album.index') }}">
                        <i class="icon-menu fas fa-compact-disc"></i>{{ __('muzibu::admin.album_list') }}
                    </a>
                    @endhasmoduleaccess

                    @hasmoduleaccess('muzibu', 'create')
                    <a class="dropdown-item" href="{{ route('admin.muzibu.album.manage') }}">
                        <i class="icon-menu fas fa-plus-circle"></i>{{ __('muzibu::admin.add_album') }}
                    </a>
                    @endhasmoduleaccess

                    {{-- Songs Section --}}
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">{{ __('muzibu::admin.songs') }}</span>
                    </h6>

                    @hasmoduleaccess('muzibu', 'view')
                    <a class="dropdown-item" href="{{ route('admin.muzibu.song.index') }}">
                        <i class="icon-menu fas fa-music"></i>{{ __('muzibu::admin.song_list') }}
                    </a>
                    @endhasmoduleaccess

                    @hasmoduleaccess('muzibu', 'create')
                    <a class="dropdown-item" href="{{ route('admin.muzibu.song.manage') }}">
                        <i class="icon-menu fas fa-plus-circle"></i>{{ __('muzibu::admin.add_song') }}
                    </a>
                    @endhasmoduleaccess

                    {{-- Genres Section --}}
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">{{ __('muzibu::admin.genres') }}</span>
                    </h6>

                    @hasmoduleaccess('muzibu', 'view')
                    <a class="dropdown-item" href="{{ route('admin.muzibu.genre.index') }}">
                        <i class="icon-menu fas fa-guitar"></i>{{ __('muzibu::admin.genre_list') }}
                    </a>
                    @endhasmoduleaccess

                    @hasmoduleaccess('muzibu', 'create')
                    <a class="dropdown-item" href="{{ route('admin.muzibu.genre.manage') }}">
                        <i class="icon-menu fas fa-plus-circle"></i>{{ __('muzibu::admin.add_genre') }}
                    </a>
                    @endhasmoduleaccess

                    {{-- Playlists Section --}}
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">{{ __('muzibu::admin.playlists') }}</span>
                    </h6>

                    @hasmoduleaccess('muzibu', 'view')
                    <a class="dropdown-item" href="{{ route('admin.muzibu.playlist.index') }}">
                        <i class="icon-menu fas fa-list-music"></i>{{ __('muzibu::admin.playlist_list') }}
                    </a>
                    @endhasmoduleaccess

                    @hasmoduleaccess('muzibu', 'create')
                    <a class="dropdown-item" href="{{ route('admin.muzibu.playlist.manage') }}">
                        <i class="icon-menu fas fa-plus-circle"></i>{{ __('muzibu::admin.add_playlist') }}
                    </a>
                    @endhasmoduleaccess

                    {{-- Radios Section --}}
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">{{ __('muzibu::admin.radios') }}</span>
                    </h6>

                    @hasmoduleaccess('muzibu', 'view')
                    <a class="dropdown-item" href="{{ route('admin.muzibu.radio.index') }}">
                        <i class="icon-menu fas fa-broadcast-tower"></i>{{ __('muzibu::admin.radio_list') }}
                    </a>
                    @endhasmoduleaccess

                    @hasmoduleaccess('muzibu', 'create')
                    <a class="dropdown-item" href="{{ route('admin.muzibu.radio.manage') }}">
                        <i class="icon-menu fas fa-plus-circle"></i>{{ __('muzibu::admin.add_radio') }}
                    </a>
                    @endhasmoduleaccess

                    {{-- Sectors Section --}}
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">{{ __('muzibu::admin.sectors') }}</span>
                    </h6>

                    @hasmoduleaccess('muzibu', 'view')
                    <a class="dropdown-item" href="{{ route('admin.muzibu.sector.index') }}">
                        <i class="icon-menu fas fa-layer-group"></i>{{ __('muzibu::admin.sector_list') }}
                    </a>
                    @endhasmoduleaccess

                    @hasmoduleaccess('muzibu', 'create')
                    <a class="dropdown-item" href="{{ route('admin.muzibu.sector.manage') }}">
                        <i class="icon-menu fas fa-plus-circle"></i>{{ __('muzibu::admin.add_sector') }}
                    </a>
                    @endhasmoduleaccess
                </div>
            </div>
            @hasmoduleaccess('muzibu', 'create')
            <a href="{{ route('admin.muzibu.song.manage') }}" class="dropdown-module-item btn btn-primary">
                <i class="icon-menu fas fa-plus"></i>{{ __('muzibu::admin.new_song') }}
            </a>
            @endhasmoduleaccess
        </div>
    </div>
</div>
@endpush
