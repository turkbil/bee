@php
    View::share('pretitle', __('muzibu::admin.music_platform'));
@endphp

<div class="stats-component-wrapper">
    <!-- Period Selector & Cache Controls -->
    <div class="card mb-4">
        <div class="card-body py-3">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="d-flex align-items-center gap-3">
                        <span class="text-muted fw-medium">Dönem:</span>
                        <div class="d-flex align-items-center gap-2">
                            <button wire:click="setPeriod('7')" class="btn btn-sm {{ $period === '7' ? 'btn-primary' : 'btn-outline-secondary' }}">
                                7 Gün
                            </button>
                            <button wire:click="setPeriod('30')" class="btn btn-sm {{ $period === '30' ? 'btn-primary' : 'btn-outline-secondary' }}">
                                30 Gün
                            </button>
                            <button wire:click="setPeriod('90')" class="btn btn-sm {{ $period === '90' ? 'btn-primary' : 'btn-outline-secondary' }}">
                                90 Gün
                            </button>
                            <button wire:click="setPeriod('365')" class="btn btn-sm {{ $period === '365' ? 'btn-primary' : 'btn-outline-secondary' }}">
                                1 Yıl
                            </button>
                        </div>
                        <div wire:loading class="ms-2">
                            <span class="spinner-border spinner-border-sm text-primary"></span>
                            <span class="text-muted small ms-1">Yükleniyor...</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mt-3 mt-lg-0">
                    <div class="d-flex align-items-center justify-content-lg-end gap-3">
                        <span class="text-muted small">
                            <i class="fas fa-clock me-1"></i>
                            Son güncelleme: <strong>{{ $this->lastCacheTime }}</strong>
                        </span>
                        <button wire:click="refreshStats" class="btn btn-sm btn-outline-warning">
                            <span wire:loading.remove wire:target="refreshStats">
                                <i class="fas fa-sync-alt me-1"></i> Cache Temizle
                            </span>
                            <span wire:loading wire:target="refreshStats">
                                <span class="spinner-border spinner-border-sm me-1"></span> Yenileniyor...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card bg-primary-lt h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <span class="bg-primary text-white avatar me-3">
                            <i class="fas fa-play"></i>
                        </span>
                        <div>
                            <div class="h2 mb-0">{{ number_format($this->totalPlays) }}</div>
                            <div class="text-muted">Toplam Dinleme</div>
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
                            <div class="h2 mb-0">{{ number_format($this->uniqueListeners) }}</div>
                            <div class="text-muted">Benzersiz Dinleyici</div>
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
                            <i class="fas fa-clock"></i>
                        </span>
                        <div>
                            <div class="h2 mb-0">{{ $this->totalListeningHours }}</div>
                            <div class="text-muted">Toplam Saat</div>
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
                            <i class="fas fa-chart-bar"></i>
                        </span>
                        <div>
                            <div class="h2 mb-0">{{ $this->avgPlaysPerUser }}</div>
                            <div class="text-muted">Ort. Dinleme/Kişi</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card h-100">
                <div class="card-body text-center py-4">
                    <div class="h2 mb-1 text-primary">{{ number_format($this->userStats['total_users']) }}</div>
                    <div class="text-muted small">Toplam Üye</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card h-100">
                <div class="card-body text-center py-4">
                    <div class="h2 mb-1 text-green">{{ number_format($this->userStats['new_users']) }}</div>
                    <div class="text-muted small">Yeni Üye</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card h-100">
                <div class="card-body text-center py-4">
                    <div class="h2 mb-1 text-azure">{{ number_format($this->userStats['active_listeners']) }}</div>
                    <div class="text-muted small">Aktif Dinleyici</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card h-100">
                <div class="card-body text-center py-4">
                    <div class="h2 mb-1 text-orange">%{{ $this->userStats['activity_rate'] }}</div>
                    <div class="text-muted small">Aktivite Oranı</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card h-100">
                <div class="card-body text-center py-4">
                    <div class="h4 mb-1 text-cyan">{{ $this->userStats['peak_day'] }}</div>
                    <div class="text-muted small">Zirve Gün</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card h-100">
                <div class="card-body text-center py-4">
                    <div class="h2 mb-1 text-pink">{{ number_format($this->userStats['peak_plays']) }}</div>
                    <div class="text-muted small">Zirve Dinleme</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Songs & Albums -->
    <div class="row g-4 mb-4">
        <!-- Top Songs -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-fire text-orange me-2"></i>
                        En Çok Dinlenen Şarkılar
                    </h3>
                </div>
                <div class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;">
                    @forelse($this->topSongs as $index => $song)
                        <div class="list-group-item py-3">
                            <div class="d-flex align-items-center gap-3">
                                <span class="badge {{ $index < 3 ? 'bg-orange' : 'bg-secondary' }}" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; font-size: 14px;">
                                    {{ $index + 1 }}
                                </span>
                                <div class="flex-fill text-truncate">
                                    <div class="fw-medium">{{ $song->title }}</div>
                                    <div class="text-muted small">{{ $song->artist_title }}</div>
                                </div>
                                <span class="badge bg-primary-lt">
                                    {{ number_format($song->play_count_period) }}
                                    <i class="fas fa-play ms-1"></i>
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="list-group-item text-center py-5">
                            <i class="fas fa-music fa-2x text-muted mb-3"></i>
                            <p class="text-muted mb-0">Henüz dinleme verisi yok</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Top Albums -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-compact-disc text-info me-2"></i>
                        En Çok Dinlenen Albümler
                    </h3>
                </div>
                <div class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;">
                    @forelse($this->topAlbums as $index => $album)
                        <div class="list-group-item py-3">
                            <div class="d-flex align-items-center gap-3">
                                <span class="badge {{ $index < 3 ? 'bg-info' : 'bg-secondary' }}" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; font-size: 14px;">
                                    {{ $index + 1 }}
                                </span>
                                @if($album->cover)
                                    <span class="avatar" style="background-image: url('{{ $album->cover }}')"></span>
                                @else
                                    <span class="avatar bg-info-lt">
                                        <i class="fas fa-compact-disc"></i>
                                    </span>
                                @endif
                                <div class="flex-fill text-truncate">
                                    <div class="fw-medium">{{ $album->title }}</div>
                                    <div class="text-muted small">{{ $album->artist_title }}</div>
                                </div>
                                <span class="badge bg-info-lt">
                                    {{ number_format($album->total_plays) }}
                                    <i class="fas fa-play ms-1"></i>
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="list-group-item text-center py-5">
                            <i class="fas fa-compact-disc fa-2x text-muted mb-3"></i>
                            <p class="text-muted mb-0">Henüz dinleme verisi yok</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Top Artists & Listeners -->
    <div class="row g-4 mb-4">
        <!-- Top Artists -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-star text-yellow me-2"></i>
                        En Çok Dinlenen Sanatçılar
                    </h3>
                </div>
                <div class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;">
                    @forelse($this->topArtists as $index => $artist)
                        <div class="list-group-item py-3">
                            <div class="d-flex align-items-center gap-3">
                                <span class="badge {{ $index < 3 ? 'bg-yellow text-dark' : 'bg-secondary' }}" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; font-size: 14px;">
                                    {{ $index + 1 }}
                                </span>
                                @if($artist->cover)
                                    <span class="avatar rounded-circle" style="background-image: url('{{ $artist->cover }}')"></span>
                                @else
                                    <span class="avatar rounded-circle bg-yellow-lt">
                                        <i class="fas fa-user"></i>
                                    </span>
                                @endif
                                <div class="flex-fill text-truncate">
                                    <div class="fw-medium">{{ $artist->title }}</div>
                                </div>
                                <span class="badge bg-yellow-lt text-dark">
                                    {{ number_format($artist->total_plays) }}
                                    <i class="fas fa-play ms-1"></i>
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="list-group-item text-center py-5">
                            <i class="fas fa-user fa-2x text-muted mb-3"></i>
                            <p class="text-muted mb-0">Henüz dinleme verisi yok</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Top Listeners -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-headphones text-purple me-2"></i>
                        En Aktif Dinleyiciler
                    </h3>
                </div>
                <div class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;">
                    @forelse($this->topListeners as $index => $listener)
                        <div class="list-group-item py-3">
                            <div class="d-flex align-items-center gap-3">
                                <span class="badge {{ $index < 3 ? 'bg-purple' : 'bg-secondary' }}" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; font-size: 14px;">
                                    {{ $index + 1 }}
                                </span>
                                <span class="avatar bg-purple-lt">
                                    {{ $listener->avatar }}
                                </span>
                                <div class="flex-fill text-truncate">
                                    <div class="fw-medium">{{ $listener->name }}</div>
                                    <div class="text-muted small">{{ $listener->email }}</div>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-purple-lt d-block mb-1">
                                        {{ number_format($listener->total_plays) }} dinleme
                                    </span>
                                    <span class="text-muted small">{{ $listener->total_hours }} saat</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="list-group-item text-center py-5">
                            <i class="fas fa-headphones fa-2x text-muted mb-3"></i>
                            <p class="text-muted mb-0">Henüz dinleme verisi yok</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Stats Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-chart-line text-primary me-2"></i>
                Günlük İstatistikler
            </h3>
        </div>
        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
            <table class="table table-vcenter table-striped mb-0">
                <thead class="sticky-top bg-white">
                    <tr>
                        <th class="ps-3">Tarih</th>
                        <th class="text-end">Dinleme</th>
                        <th class="text-end">Dinleyici</th>
                        <th class="pe-3" style="width: 40%">Grafik</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $maxPlays = $this->dailyStats->max('plays') ?: 1;
                    @endphp
                    @forelse($this->dailyStats as $stat)
                        <tr>
                            <td class="ps-3">
                                <span class="fw-medium">{{ \Carbon\Carbon::parse($stat->date)->format('d.m.Y') }}</span>
                                <span class="text-muted small ms-2">({{ \Carbon\Carbon::parse($stat->date)->translatedFormat('l') }})</span>
                            </td>
                            <td class="text-end fw-medium">{{ number_format($stat->plays) }}</td>
                            <td class="text-end text-muted">{{ number_format($stat->listeners) }}</td>
                            <td class="pe-3">
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar bg-primary" style="width: {{ ($stat->plays / $maxPlays) * 100 }}%"></div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-5">
                                <i class="fas fa-chart-line fa-2x mb-3 d-block"></i>
                                Veri bulunamadı
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
