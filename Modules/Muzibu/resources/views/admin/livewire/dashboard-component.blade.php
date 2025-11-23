@php
    View::share('pretitle', __('muzibu::admin.music_platform'));
@endphp

<div class="dashboard-component-wrapper">
    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-primary text-white avatar">
                                <i class="fas fa-music"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium h3 mb-0">{{ $this->totalSongs }}</div>
                            <div class="">{{ __('muzibu::admin.songs') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-green text-white avatar">
                                <i class="fas fa-compact-disc"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium h3 mb-0">{{ $this->totalAlbums }}</div>
                            <div class="">{{ __('muzibu::admin.albums') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-yellow text-white avatar">
                                <i class="fas fa-user-music"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium h3 mb-0">{{ $this->totalArtists }}</div>
                            <div class="">{{ __('muzibu::admin.artists') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-cyan text-white avatar">
                                <i class="fas fa-list-music"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium h3 mb-0">{{ $this->totalPlaylists }}</div>
                            <div class="">{{ __('muzibu::admin.playlists') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hızlı Navigasyon Haritası -->
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-th-large me-2 text-primary"></i>
                {{ __('muzibu::admin.quick_navigation') }}
            </h3>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <!-- İçerik Yönetimi -->
                <div class="col-md-4">
                    <div class="card bg-primary-lt">
                        <div class="card-body p-3">
                            <h4 class="card-title mb-3">
                                <i class="fas fa-music me-2"></i>
                                {{ __('muzibu::admin.content_management') }}
                            </h4>
                            <div class="list-group list-group-flush">
                                <div class="list-group-item bg-transparent border-0 px-0 py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="{{ route('admin.muzibu.song.index') }}" class="text-reset">
                                            <i class="fas fa-music me-2"></i>{{ __('muzibu::admin.songs') }}
                                        </a>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-primary">{{ $this->totalSongs }}</span>
                                            <a href="{{ route('admin.muzibu.song.manage') }}" class="btn btn-sm btn-icon btn-primary" data-bs-toggle="tooltip" title="{{ __('muzibu::admin.new_song') }}">
                                                <i class="fas fa-plus"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="list-group-item bg-transparent border-0 px-0 py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="{{ route('admin.muzibu.album.index') }}" class="text-reset">
                                            <i class="fas fa-compact-disc me-2"></i>{{ __('muzibu::admin.albums') }}
                                        </a>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-green">{{ $this->totalAlbums }}</span>
                                            <a href="{{ route('admin.muzibu.album.manage') }}" class="btn btn-sm btn-icon btn-green" data-bs-toggle="tooltip" title="{{ __('muzibu::admin.new_album') }}">
                                                <i class="fas fa-plus"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="list-group-item bg-transparent border-0 px-0 py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="{{ route('admin.muzibu.artist.index') }}" class="text-reset">
                                            <i class="fas fa-user-music me-2"></i>{{ __('muzibu::admin.artists') }}
                                        </a>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-yellow">{{ $this->totalArtists }}</span>
                                            <a href="{{ route('admin.muzibu.artist.manage') }}" class="btn btn-sm btn-icon btn-yellow" data-bs-toggle="tooltip" title="{{ __('muzibu::admin.new_artist') }}">
                                                <i class="fas fa-plus"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Organizasyon -->
                <div class="col-md-4">
                    <div class="card bg-green-lt">
                        <div class="card-body p-3">
                            <h4 class="card-title mb-3">
                                <i class="fas fa-folder-tree me-2"></i>
                                {{ __('muzibu::admin.organization') }}
                            </h4>
                            <div class="list-group list-group-flush">
                                <div class="list-group-item bg-transparent border-0 px-0 py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="{{ route('admin.muzibu.genre.index') }}" class="text-reset">
                                            <i class="fas fa-tags me-2"></i>{{ __('muzibu::admin.genres') }}
                                        </a>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-purple">{{ $this->totalGenres }}</span>
                                            <a href="{{ route('admin.muzibu.genre.manage') }}" class="btn btn-sm btn-icon btn-purple" data-bs-toggle="tooltip" title="{{ __('muzibu::admin.new_genre') }}">
                                                <i class="fas fa-plus"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="list-group-item bg-transparent border-0 px-0 py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="{{ route('admin.muzibu.playlist.index') }}" class="text-reset">
                                            <i class="fas fa-list-music me-2"></i>{{ __('muzibu::admin.playlists') }}
                                        </a>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-cyan">{{ $this->totalPlaylists }}</span>
                                            <a href="{{ route('admin.muzibu.playlist.manage') }}" class="btn btn-sm btn-icon btn-cyan" data-bs-toggle="tooltip" title="{{ __('muzibu::admin.new_playlist') }}">
                                                <i class="fas fa-plus"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dağıtım -->
                <div class="col-md-4">
                    <div class="card bg-purple-lt">
                        <div class="card-body p-3">
                            <h4 class="card-title mb-3">
                                <i class="fas fa-broadcast-tower me-2"></i>
                                {{ __('muzibu::admin.distribution') }}
                            </h4>
                            <div class="list-group list-group-flush">
                                <div class="list-group-item bg-transparent border-0 px-0 py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="{{ route('admin.muzibu.sector.index') }}" class="text-reset">
                                            <i class="fas fa-industry me-2"></i>{{ __('muzibu::admin.sectors') }}
                                        </a>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-orange">{{ $this->totalSectors }}</span>
                                            <a href="{{ route('admin.muzibu.sector.manage') }}" class="btn btn-sm btn-icon btn-orange" data-bs-toggle="tooltip" title="{{ __('muzibu::admin.new_sector') }}">
                                                <i class="fas fa-plus"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="list-group-item bg-transparent border-0 px-0 py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="{{ route('admin.muzibu.radio.index') }}" class="text-reset">
                                            <i class="fas fa-radio me-2"></i>{{ __('muzibu::admin.radios') }}
                                        </a>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-red">{{ $this->totalRadios }}</span>
                                            <a href="{{ route('admin.muzibu.radio.manage') }}" class="btn btn-sm btn-icon btn-red" data-bs-toggle="tooltip" title="{{ __('muzibu::admin.new_radio') }}">
                                                <i class="fas fa-plus"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <!-- Son Eklenen Şarkılar -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clock me-2"></i>
                        {{ __('muzibu::admin.dashboard.recent_songs') }}
                    </h3>
                    <div class="card-actions">
                        <a href="{{ route('admin.muzibu.song.index') }}" class="btn btn-sm">
                            {{ __('admin.view_all') }}
                        </a>
                    </div>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($this->recentSongs as $song)
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="avatar bg-primary-lt">
                                        <i class="fas fa-music"></i>
                                    </span>
                                </div>
                                <div class="col text-truncate">
                                    <a href="{{ route('admin.muzibu.song.manage', $song->song_id) }}" class="text-reset d-block text-truncate">
                                        <strong>{{ $song->getTranslated('title', app()->getLocale()) ?? $song->getTranslated('title', 'tr') }}</strong>
                                    </a>
                                    <div class="small text-truncate">
                                        {{ $song->album?->artist?->getTranslated('title', app()->getLocale()) ?? __('admin.unknown') }}
                                        @if($song->duration)
                                            · {{ $song->getFormattedDuration() }}
                                        @endif
                                    </div>
                                </div>
                                <div class="col-auto">
                                    @if($song->hls_converted)
                                        <span class="badge bg-green" data-bs-toggle="tooltip" title="HLS Hazır">
                                            <i class="fas fa-shield-alt"></i>
                                        </span>
                                    @elseif($song->file_path)
                                        <span class="badge bg-yellow" data-bs-toggle="tooltip" title="Dönüşüm Bekliyor">
                                            <i class="fas fa-clock"></i>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="list-group-item text-center py-4">
                            <i class="fas fa-music fa-2x mb-2"></i>
                            <p class="mb-0">{{ __('muzibu::admin.no_songs_found') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- En Çok Dinlenenler -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-fire me-2 text-orange"></i>
                        {{ __('muzibu::admin.dashboard.popular_songs') }}
                    </h3>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($this->popularSongs as $index => $song)
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="badge {{ $index === 0 ? 'bg-primary' : 'bg-secondary' }}">
                                        {{ $index + 1 }}
                                    </span>
                                </div>
                                <div class="col text-truncate">
                                    <a href="{{ route('admin.muzibu.song.manage', $song->song_id) }}" class="text-reset d-block text-truncate">
                                        <strong>{{ $song->getTranslated('title', app()->getLocale()) ?? $song->getTranslated('title', 'tr') }}</strong>
                                    </a>
                                    <div class="small">
                                        {{ $song->album?->artist?->getTranslated('title', app()->getLocale()) ?? __('admin.unknown') }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <span class="">
                                        {{ number_format($song->play_count) }}
                                        <i class="fas fa-play ms-1"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="list-group-item text-center py-4">
                            <i class="fas fa-chart-line fa-2x mb-2"></i>
                            <p class="mb-0">{{ __('muzibu::admin.dashboard.no_plays_yet') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- HLS Dönüşüm Durumu -->
    <div class="card mt-3">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-shield-alt me-2 text-green"></i>
                {{ __('muzibu::admin.dashboard.hls_status') }}
            </h3>
        </div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col">
                    <div class="h1 text-green mb-0">{{ $this->hlsStats['completed'] }}</div>
                    <div class="">{{ __('muzibu::admin.dashboard.completed') }}</div>
                </div>
                <div class="col">
                    <div class="h1 text-yellow mb-0">{{ $this->hlsStats['pending'] }}</div>
                    <div class="">{{ __('muzibu::admin.dashboard.pending') }}</div>
                </div>
                <div class="col">
                    <div class="h1 text-red mb-0">{{ $this->hlsStats['failed'] }}</div>
                    <div class="">{{ __('muzibu::admin.dashboard.failed') }}</div>
                </div>
            </div>
            @if($this->hlsStats['pending'] > 0)
                <div class="progress mt-3">
                    @php
                        $total = $this->hlsStats['completed'] + $this->hlsStats['pending'];
                        $percentage = $total > 0 ? round(($this->hlsStats['completed'] / $total) * 100) : 0;
                    @endphp
                    <div class="progress-bar bg-green" style="width: {{ $percentage }}%" role="progressbar">
                        {{ $percentage }}%
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Hızlı Erişim Butonları -->
    <div class="row g-2 mt-3">
        <div class="col-auto">
            <a href="{{ route('admin.muzibu.song.manage') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>{{ __('muzibu::admin.new_song') }}
            </a>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.muzibu.album.manage') }}" class="btn btn-outline-primary">
                <i class="fas fa-compact-disc me-2"></i>{{ __('muzibu::admin.new_album') }}
            </a>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.muzibu.artist.manage') }}" class="btn btn-outline-primary">
                <i class="fas fa-user-music me-2"></i>{{ __('muzibu::admin.new_artist') }}
            </a>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.muzibu.playlist.manage') }}" class="btn btn-outline-primary">
                <i class="fas fa-list-music me-2"></i>{{ __('muzibu::admin.new_playlist') }}
            </a>
        </div>
    </div>
</div>
