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
                    @hasmoduleaccess('muzibu', 'view')
                    <a class="dropdown-item" href="{{ route('admin.muzibu.artist.index') }}">
                        <i class="icon-menu fas fa-user-music"></i>{{ __('muzibu::admin.artists') }}
                    </a>
                    @endhasmoduleaccess

                    @hasmoduleaccess('muzibu', 'view')
                    <a class="dropdown-item" href="{{ route('admin.muzibu.album.index') }}">
                        <i class="icon-menu fas fa-compact-disc"></i>{{ __('muzibu::admin.albums') }}
                    </a>
                    @endhasmoduleaccess

                    @hasmoduleaccess('muzibu', 'view')
                    <a class="dropdown-item" href="{{ route('admin.muzibu.song.index') }}">
                        <i class="icon-menu fas fa-music"></i>{{ __('muzibu::admin.songs') }}
                    </a>
                    @endhasmoduleaccess

                    @hasmoduleaccess('muzibu', 'view')
                    <a class="dropdown-item" href="{{ route('admin.muzibu.genre.index') }}">
                        <i class="icon-menu fas fa-guitar"></i>{{ __('muzibu::admin.genres') }}
                    </a>
                    @endhasmoduleaccess

                    @hasmoduleaccess('muzibu', 'view')
                    <a class="dropdown-item" href="{{ route('admin.muzibu.playlist.index') }}">
                        <i class="icon-menu fas fa-list-music"></i>{{ __('muzibu::admin.playlists') }}
                    </a>
                    @endhasmoduleaccess

                    @hasmoduleaccess('muzibu', 'view')
                    <a class="dropdown-item" href="{{ route('admin.muzibu.radio.index') }}">
                        <i class="icon-menu fas fa-broadcast-tower"></i>{{ __('muzibu::admin.radios') }}
                    </a>
                    @endhasmoduleaccess

                    @hasmoduleaccess('muzibu', 'view')
                    <a class="dropdown-item" href="{{ route('admin.muzibu.sector.index') }}">
                        <i class="icon-menu fas fa-layer-group"></i>{{ __('muzibu::admin.sectors') }}
                    </a>
                    @endhasmoduleaccess
                </div>
            </div>

            <div class="dropdown">
                <button type="button" class="dropdown-module-item dropdown-toggle btn btn-ghost-secondary"
                    data-bs-toggle="dropdown">
                    <i class="icon-menu fas fa-plus-circle"></i>{{ __('muzibu::admin.quick_add') }}
                </button>
                <div class="dropdown-menu">
                    @hasmoduleaccess('muzibu', 'create')
                    <a class="dropdown-item" href="{{ route('admin.muzibu.artist.manage') }}">
                        <i class="icon-menu fas fa-user-music"></i>{{ __('muzibu::admin.add_artist') }}
                    </a>
                    @endhasmoduleaccess

                    @hasmoduleaccess('muzibu', 'create')
                    <a class="dropdown-item" href="{{ route('admin.muzibu.album.manage') }}">
                        <i class="icon-menu fas fa-compact-disc"></i>{{ __('muzibu::admin.add_album') }}
                    </a>
                    @endhasmoduleaccess

                    @hasmoduleaccess('muzibu', 'create')
                    <a class="dropdown-item" href="{{ route('admin.muzibu.song.manage') }}">
                        <i class="icon-menu fas fa-music"></i>{{ __('muzibu::admin.add_song') }}
                    </a>
                    @endhasmoduleaccess

                    @hasmoduleaccess('muzibu', 'create')
                    <a class="dropdown-item" href="{{ route('admin.muzibu.genre.manage') }}">
                        <i class="icon-menu fas fa-guitar"></i>{{ __('muzibu::admin.add_genre') }}
                    </a>
                    @endhasmoduleaccess

                    @hasmoduleaccess('muzibu', 'create')
                    <a class="dropdown-item" href="{{ route('admin.muzibu.playlist.manage') }}">
                        <i class="icon-menu fas fa-list-music"></i>{{ __('muzibu::admin.add_playlist') }}
                    </a>
                    @endhasmoduleaccess

                    @hasmoduleaccess('muzibu', 'create')
                    <a class="dropdown-item" href="{{ route('admin.muzibu.radio.manage') }}">
                        <i class="icon-menu fas fa-broadcast-tower"></i>{{ __('muzibu::admin.add_radio') }}
                    </a>
                    @endhasmoduleaccess

                    @hasmoduleaccess('muzibu', 'create')
                    <a class="dropdown-item" href="{{ route('admin.muzibu.sector.manage') }}">
                        <i class="icon-menu fas fa-layer-group"></i>{{ __('muzibu::admin.add_sector') }}
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
