@php
    View::share('pretitle', __('muzibu::admin.music_platform'));
@endphp

<div class="dashboard-component-wrapper">
    <!-- Dinleme İstatistikleri Kartları -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card bg-primary-lt h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <span class="bg-primary text-white avatar me-3">
                            <i class="fas fa-play"></i>
                        </span>
                        <div>
                            <div class="h2 mb-0">{{ number_format($this->listeningStats['total_plays']) }}</div>
                            <div class="text-muted small">Bugünkü Dinleme</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card bg-green-lt h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <span class="bg-green text-white avatar me-3">
                            <i class="fas fa-users"></i>
                        </span>
                        <div>
                            <div class="h2 mb-0">{{ number_format($this->listeningStats['unique_listeners']) }}</div>
                            <div class="text-muted small">Aktif Dinleyici</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card bg-purple-lt h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <span class="bg-purple text-white avatar me-3">
                            <i class="fas fa-clock"></i>
                        </span>
                        <div>
                            <div class="h2 mb-0">{{ $this->listeningStats['total_hours'] }}</div>
                            <div class="text-muted small">Saat Dinlendi</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card bg-azure-lt h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <span class="bg-azure text-white avatar me-3">
                            <i class="fas fa-music"></i>
                        </span>
                        <div>
                            <div class="h2 mb-0">{{ number_format($this->totalSongs) }}</div>
                            <div class="text-muted small">Toplam Şarkı</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dinleme Grafiği -->
    <div class="card mb-4">
        <div class="card-header">
            <div class="row align-items-center w-100">
                <div class="col">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-chart-bar text-primary me-2"></i>
                        Dinleme Dağılımı
                    </h3>
                </div>
                <div class="col-auto">
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <!-- View Mode Buttons -->
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" wire:click="setViewMode('hourly')"
                                class="btn btn-sm {{ $viewMode === 'hourly' ? 'btn-primary' : 'btn-outline-secondary' }}">
                                Saatlik
                            </button>
                            <button type="button" wire:click="setViewMode('daily')"
                                class="btn btn-sm {{ $viewMode === 'daily' ? 'btn-primary' : 'btn-outline-secondary' }}">
                                Günlük
                            </button>
                            <button type="button" wire:click="setViewMode('weekly')"
                                class="btn btn-sm {{ $viewMode === 'weekly' ? 'btn-primary' : 'btn-outline-secondary' }}">
                                Haftalık
                            </button>
                            <button type="button" wire:click="setViewMode('monthly')"
                                class="btn btn-sm {{ $viewMode === 'monthly' ? 'btn-primary' : 'btn-outline-secondary' }}">
                                Aylık
                            </button>
                        </div>

                        <!-- Date Navigation (Sadece hourly/daily mode'da) -->
                        @if(in_array($viewMode, ['hourly', 'daily']))
                            <div class="d-flex align-items-center gap-2">
                                <button wire:click="goToPreviousDay" class="btn btn-sm btn-icon btn-outline-secondary" data-bs-toggle="tooltip" title="Önceki Gün">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <input type="date" wire:model.live="selectedDate" class="form-control form-control-sm" style="width: 140px;" max="{{ now()->format('Y-m-d') }}">
                                <button wire:click="goToNextDay" class="btn btn-sm btn-icon btn-outline-secondary" data-bs-toggle="tooltip" title="Sonraki Gün" @if(Carbon\Carbon::parse($selectedDate)->isToday()) disabled @endif>
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                                <button wire:click="goToToday" class="btn btn-sm btn-outline-primary">
                                    Bugün
                                </button>
                            </div>
                        @endif

                        <button wire:click="refreshStats" class="btn btn-sm btn-icon btn-outline-success" data-bs-toggle="tooltip" title="Verileri Yenile">
                            <span wire:loading.remove wire:target="refreshStats"><i class="fas fa-sync-alt"></i></span>
                            <span wire:loading wire:target="refreshStats"><span class="spinner-border spinner-border-sm"></span></span>
                        </button>

                        <div wire:loading class="ms-1">
                            <span class="spinner-border spinner-border-sm text-primary"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- Hourly Chart -->
            @if($viewMode === 'hourly')
                @php
                    $stats = $this->hourlyStats;
                    $maxValue = (!empty($stats) && max($stats) > 0) ? max($stats) : 1;
                @endphp
                <div class="d-flex">
                    <!-- Y Axis -->
                    <div class="d-flex flex-column justify-content-between text-end pe-2 pt-2" style="width: 45px; height: 120px;">
                        <span class="text-muted small fw-medium">{{ number_format($maxValue) }}</span>
                        <span class="text-muted small">{{ number_format(intval($maxValue / 2)) }}</span>
                        <span class="text-muted small">0</span>
                    </div>
                    <!-- Bars -->
                    <div class="flex-fill d-flex align-items-end gap-1 pt-3" style="height: 140px;">
                        @foreach($stats as $hour => $plays)
                            <div class="flex-fill text-center">
                                @if($plays > 0)
                                    <div class="text-muted small mb-1" style="font-size: 9px;">{{ $plays > 999 ? number_format($plays/1000, 1).'k' : $plays }}</div>
                                @endif
                                <div class="bg-primary rounded-top transition-all" style="height: {{ ($plays / $maxValue) * 100 }}px; min-height: 2px;"></div>
                                <div class="text-muted small mt-1" style="font-size: 10px;">{{ sprintf('%02d', $hour) }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @if(Carbon\Carbon::parse($selectedDate)->isToday())
                    <div class="text-muted small mt-2 text-center">
                        <i class="fas fa-info-circle me-1"></i>
                        Son saat hesaplamalara dahil edilmedi (performans optimizasyonu)
                    </div>
                @endif
            @endif

            <!-- Daily Chart (Son 7 Gün) -->
            @if($viewMode === 'daily')
                @php
                    $stats = $this->dailyStats;
                    $maxValue = (!empty($stats) && max($stats) > 0) ? max($stats) : 1;
                @endphp
                <div class="d-flex">
                    <!-- Y Axis -->
                    <div class="d-flex flex-column justify-content-between text-end pe-2 pt-2" style="width: 45px; height: 120px;">
                        <span class="text-muted small fw-medium">{{ number_format($maxValue) }}</span>
                        <span class="text-muted small">{{ number_format(intval($maxValue / 2)) }}</span>
                        <span class="text-muted small">0</span>
                    </div>
                    <!-- Bars -->
                    <div class="flex-fill d-flex align-items-end justify-content-around gap-2 pt-3" style="height: 140px;">
                        @foreach($stats as $date => $plays)
                            @php
                                $carbonDate = Carbon\Carbon::parse($date);
                                $isToday = $carbonDate->isToday();
                            @endphp
                            <div class="flex-fill text-center" style="max-width: 80px;">
                                <div class="text-muted small mb-1 fw-medium" style="font-size: 10px;">{{ $plays > 999 ? number_format($plays/1000, 1).'k' : number_format($plays) }}</div>
                                <div class="{{ $isToday ? 'bg-green' : 'bg-primary' }} rounded-top transition-all" style="height: {{ ($plays / $maxValue) * 100 }}px; min-height: 2px;"></div>
                                <div class="text-muted small mt-1">{{ $carbonDate->translatedFormat('D') }}</div>
                                <div class="text-muted small" style="font-size: 10px;">{{ $carbonDate->format('d/m') }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Weekly Chart (Son 4 Hafta) -->
            @if($viewMode === 'weekly')
                @php
                    $stats = $this->weeklyStats;
                    $maxValue = (!empty($stats) && max($stats) > 0) ? max($stats) : 1;
                @endphp
                <div class="d-flex">
                    <!-- Y Axis -->
                    <div class="d-flex flex-column justify-content-between text-end pe-2 pt-2" style="width: 50px; height: 120px;">
                        <span class="text-muted small fw-medium">{{ number_format($maxValue) }}</span>
                        <span class="text-muted small">{{ number_format(intval($maxValue / 2)) }}</span>
                        <span class="text-muted small">0</span>
                    </div>
                    <!-- Bars -->
                    <div class="flex-fill d-flex align-items-end justify-content-around gap-3 pt-3" style="height: 140px;">
                        @foreach($stats as $label => $plays)
                            <div class="flex-fill text-center" style="max-width: 150px;">
                                <div class="text-muted small mb-1 fw-medium" style="font-size: 10px;">{{ $plays > 999 ? number_format($plays/1000, 1).'k' : number_format($plays) }}</div>
                                <div class="bg-purple rounded-top transition-all" style="height: {{ ($plays / $maxValue) * 100 }}px; min-height: 2px;"></div>
                                <div class="text-muted small mt-1" style="font-size: 11px;">{{ $label }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Monthly Chart (Son 6 Ay) -->
            @if($viewMode === 'monthly')
                @php
                    $stats = $this->monthlyStats;
                    $maxValue = (!empty($stats) && max($stats) > 0) ? max($stats) : 1;
                @endphp
                <div class="d-flex">
                    <!-- Y Axis -->
                    <div class="d-flex flex-column justify-content-between text-end pe-2 pt-2" style="width: 50px; height: 120px;">
                        <span class="text-muted small fw-medium">{{ number_format($maxValue) }}</span>
                        <span class="text-muted small">{{ number_format(intval($maxValue / 2)) }}</span>
                        <span class="text-muted small">0</span>
                    </div>
                    <!-- Bars -->
                    <div class="flex-fill d-flex align-items-end justify-content-around gap-2 pt-3" style="height: 140px;">
                        @foreach($stats as $label => $plays)
                            <div class="flex-fill text-center" style="max-width: 100px;">
                                <div class="text-muted small mb-1 fw-medium" style="font-size: 10px;">{{ $plays > 999 ? number_format($plays/1000, 1).'k' : number_format($plays) }}</div>
                                <div class="bg-azure rounded-top transition-all" style="height: {{ ($plays / $maxValue) * 100 }}px; min-height: 2px;"></div>
                                <div class="text-muted small mt-1" style="font-size: 11px;">{{ $label }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
        <div class="card-footer bg-transparent">
            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('admin.muzibu.listening-history.index') }}" class="text-muted">
                    <i class="fas fa-history me-1"></i> Detaylı Dinleme Geçmişi
                </a>
                <a href="{{ route('admin.muzibu.stats.index') }}" class="text-muted">
                    <i class="fas fa-chart-line me-1"></i> Tüm İstatistikler
                </a>
            </div>
        </div>
    </div>

    <!-- İçerik İstatistikleri -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-2">
            <a href="{{ route('admin.muzibu.song.index') }}" class="card card-link card-link-pop h-100">
                <div class="card-body text-center py-3">
                    <span class="avatar avatar-lg bg-primary-lt mb-2">
                        <i class="fas fa-music"></i>
                    </span>
                    <div class="h3 mb-0">{{ number_format($this->totalSongs) }}</div>
                    <div class="text-muted small">{{ __('muzibu::admin.songs') }}</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-lg-2">
            <a href="{{ route('admin.muzibu.album.index') }}" class="card card-link card-link-pop h-100">
                <div class="card-body text-center py-3">
                    <span class="avatar avatar-lg bg-green-lt mb-2">
                        <i class="fas fa-compact-disc"></i>
                    </span>
                    <div class="h3 mb-0">{{ number_format($this->totalAlbums) }}</div>
                    <div class="text-muted small">{{ __('muzibu::admin.albums') }}</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-lg-2">
            <a href="{{ route('admin.muzibu.artist.index') }}" class="card card-link card-link-pop h-100">
                <div class="card-body text-center py-3">
                    <span class="avatar avatar-lg bg-yellow-lt mb-2">
                        <i class="fas fa-user-music"></i>
                    </span>
                    <div class="h3 mb-0">{{ number_format($this->totalArtists) }}</div>
                    <div class="text-muted small">{{ __('muzibu::admin.artists') }}</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-lg-2">
            <a href="{{ route('admin.muzibu.playlist.index') }}" class="card card-link card-link-pop h-100">
                <div class="card-body text-center py-3">
                    <span class="avatar avatar-lg bg-cyan-lt mb-2">
                        <i class="fas fa-list-music"></i>
                    </span>
                    <div class="h3 mb-0">{{ number_format($this->totalPlaylists) }}</div>
                    <div class="text-muted small">{{ __('muzibu::admin.playlists') }}</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-lg-2">
            <a href="{{ route('admin.muzibu.genre.index') }}" class="card card-link card-link-pop h-100">
                <div class="card-body text-center py-3">
                    <span class="avatar avatar-lg bg-purple-lt mb-2">
                        <i class="fas fa-tags"></i>
                    </span>
                    <div class="h3 mb-0">{{ number_format($this->totalGenres) }}</div>
                    <div class="text-muted small">{{ __('muzibu::admin.genres') }}</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-lg-2">
            <a href="{{ route('admin.muzibu.radio.index') }}" class="card card-link card-link-pop h-100">
                <div class="card-body text-center py-3">
                    <span class="avatar avatar-lg bg-red-lt mb-2">
                        <i class="fas fa-broadcast-tower"></i>
                    </span>
                    <div class="h3 mb-0">{{ number_format($this->totalRadios) }}</div>
                    <div class="text-muted small">{{ __('muzibu::admin.radios') }}</div>
                </div>
            </a>
        </div>
    </div>

    <div class="row g-3">
        <!-- Son Eklenen Şarkılar -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clock me-2 text-azure"></i>
                        {{ __('muzibu::admin.dashboard.recent_songs') }}
                    </h3>
                    <div class="card-actions">
                        <a href="{{ route('admin.muzibu.song.index') }}" class="btn btn-sm btn-ghost-primary">
                            {{ __('admin.view_all') }} <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($this->recentSongs as $song)
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    @php
                                        $cover = $song->album?->getFirstMediaUrl('hero', 'thumb');
                                    @endphp
                                    @if($cover)
                                        <span class="avatar avatar-sm" style="background-image: url('{{ $cover }}')"></span>
                                    @else
                                        <span class="avatar avatar-sm bg-primary-lt">
                                            <i class="fas fa-music"></i>
                                        </span>
                                    @endif
                                </div>
                                <div class="col text-truncate">
                                    <a href="{{ route('admin.muzibu.song.manage', $song->song_id) }}" class="text-reset d-block text-truncate">
                                        <strong>{{ $song->getTranslated('title', app()->getLocale()) ?? $song->getTranslated('title', 'tr') }}</strong>
                                    </a>
                                    <div class="small text-truncate text-muted">
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
                            <i class="fas fa-music fa-2x text-muted mb-2 d-block"></i>
                            <p class="mb-0 text-muted">{{ __('muzibu::admin.no_songs_found') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- En Çok Dinlenenler (All Time) -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-trophy me-2 text-yellow"></i>
                        {{ __('muzibu::admin.dashboard.popular_songs') }}
                        <span class="badge bg-secondary-lt ms-2">Tüm Zamanlar</span>
                    </h3>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($this->popularSongs as $index => $song)
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="badge {{ $index === 0 ? 'bg-yellow' : ($index === 1 ? 'bg-secondary' : 'bg-secondary-lt') }}">
                                        {{ $index + 1 }}
                                    </span>
                                </div>
                                <div class="col-auto">
                                    @php
                                        $cover = $song->album?->getFirstMediaUrl('hero', 'thumb');
                                    @endphp
                                    @if($cover)
                                        <span class="avatar avatar-sm" style="background-image: url('{{ $cover }}')"></span>
                                    @else
                                        <span class="avatar avatar-sm bg-primary-lt">
                                            <i class="fas fa-music"></i>
                                        </span>
                                    @endif
                                </div>
                                <div class="col text-truncate">
                                    <a href="{{ route('admin.muzibu.song.manage', $song->song_id) }}" class="text-reset d-block text-truncate">
                                        <strong>{{ $song->getTranslated('title', app()->getLocale()) ?? $song->getTranslated('title', 'tr') }}</strong>
                                    </a>
                                    <div class="small text-muted">
                                        {{ $song->album?->artist?->getTranslated('title', app()->getLocale()) ?? __('admin.unknown') }}
                                    </div>
                                </div>
                                <div class="col-auto text-end">
                                    <div class="fw-bold">{{ number_format($song->play_count) }}</div>
                                    <div class="text-muted small">dinleme</div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="list-group-item text-center py-4">
                            <i class="fas fa-chart-line fa-2x text-muted mb-2 d-block"></i>
                            <p class="mb-0 text-muted">{{ __('muzibu::admin.dashboard.no_plays_yet') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- HLS Dönüşüm Durumu -->
    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-shield-alt me-2 text-green"></i>
                {{ __('muzibu::admin.dashboard.hls_status') }}
            </h3>
        </div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col">
                    <div class="h1 text-green mb-0">{{ number_format($this->hlsStats['completed']) }}</div>
                    <div class="text-muted">{{ __('muzibu::admin.dashboard.completed') }}</div>
                </div>
                <div class="col">
                    <div class="h1 text-yellow mb-0">{{ number_format($this->hlsStats['pending']) }}</div>
                    <div class="text-muted">{{ __('muzibu::admin.dashboard.pending') }}</div>
                </div>
                <div class="col">
                    <div class="h1 text-red mb-0">{{ number_format($this->hlsStats['failed']) }}</div>
                    <div class="text-muted">{{ __('muzibu::admin.dashboard.failed') }}</div>
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
    <div class="d-flex flex-wrap gap-2 mt-3">
        <a href="{{ route('admin.muzibu.song.manage') }}" class="btn btn-primary" data-bs-toggle="tooltip" title="{{ __('muzibu::admin.new_song') }}">
            <i class="fas fa-plus me-2"></i>{{ __('muzibu::admin.new_song') }}
        </a>
        <a href="{{ route('admin.muzibu.album.manage') }}" class="btn btn-outline-primary" data-bs-toggle="tooltip" title="{{ __('muzibu::admin.new_album') }}">
            <i class="fas fa-compact-disc me-2"></i>{{ __('muzibu::admin.new_album') }}
        </a>
        <a href="{{ route('admin.muzibu.artist.manage') }}" class="btn btn-outline-primary" data-bs-toggle="tooltip" title="{{ __('muzibu::admin.new_artist') }}">
            <i class="fas fa-user-music me-2"></i>{{ __('muzibu::admin.new_artist') }}
        </a>
        <a href="{{ route('admin.muzibu.playlist.manage') }}" class="btn btn-outline-primary" data-bs-toggle="tooltip" title="{{ __('muzibu::admin.new_playlist') }}">
            <i class="fas fa-list-music me-2"></i>{{ __('muzibu::admin.new_playlist') }}
        </a>
        <a href="{{ route('admin.muzibu.song.bulk-convert') }}" class="btn btn-outline-warning" data-bs-toggle="tooltip" title="Toplu HLS Dönüşüm">
            <i class="fas fa-exchange-alt me-2"></i>Toplu HLS Dönüşüm
        </a>
    </div>
</div>

