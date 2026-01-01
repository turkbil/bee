@php
    View::share('pretitle', 'Playlist Şarkı Yönetimi');
@endphp

<!-- TEST-2026-01-01-CACHE-CHECK -->
<div class="playlist-songs-manage-wrapper">
    <!-- Header: Playlist Title + Back Button -->
    <div class="page-header d-print-none mb-4">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">
                        {{ __('muzibu::admin.playlist.songs_management') }}
                    </div>
                    <h2 class="page-title">
                        @php
                            $title = $playlist->getTranslated('title', app()->getLocale()) ?? $playlist->getTranslated('title', 'tr');
                            $safeTitle = is_string($title) ? $title : (is_array($title) ? ($title[app()->getLocale()] ?? $title['tr'] ?? $title['en'] ?? reset($title) ?? 'Unknown') : 'Unknown');
                        @endphp
                        {{ $safeTitle }}
                    </h2>
                </div>
                <div class="col-auto ms-auto">
                    <a href="{{ route('admin.muzibu.playlist.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>
                        {{ __('admin.back') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Dual-List Container -->
    <div class="page-body">
        <div class="container-xl">
            <div class="row g-4">
                <!-- SOL KOLON: TÜM ŞARKILAR -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-music me-2"></i>
                                {{ __('muzibu::admin.playlist.all_songs') }}
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <!-- Arama Input -->
                            <div class="p-3 border-bottom">
                                <div class="input-icon">
                                    <span class="input-icon-addon">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text"
                                           wire:model.live.debounce.300ms="search"
                                           class="form-control"
                                           placeholder="{{ __('muzibu::admin.playlist.search_songs_placeholder') }}">
                                </div>
                                <!-- Loading indicator for search -->
                                <div wire:loading wire:target="search" class="mt-2">
                                    <div class="progress progress-sm">
                                        <div class="progress-bar progress-bar-indeterminate"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Şarkı Listesi -->
                            <div class="list-group list-group-flush" style="max-height: 600px; overflow-y: auto;">
                                @forelse($this->availableSongs as $song)
                                    <div class="list-group-item" wire:key="available-{{ $song->song_id }}">
                                        <div class="row align-items-center">
                                            <!-- Şarkı Bilgisi -->
                                            <div class="col">
                                                <div class="d-flex align-items-start">
                                                    <!-- Cover Image -->
                                                    @if($song->media_id)
                                                        <img src="{{ thumb($song->coverMedia, 40, 40) }}"
                                                             alt="{{ $song->getTranslated('title') }}"
                                                             class="rounded me-2"
                                                             style="width: 40px; height: 40px; object-fit: cover;">
                                                    @else
                                                        <div class="avatar avatar-sm me-2 bg-secondary-lt">
                                                            <i class="fas fa-music"></i>
                                                        </div>
                                                    @endif

                                                    <!-- Title + Details -->
                                                    <div>
                                                        {{-- 1. Satır: Şarkı Adı --}}
                                                        <div class="fw-bold">
                                                            {{ $song->getTranslated('title', app()->getLocale()) ?? $song->getTranslated('title', 'tr') }}
                                                        </div>
                                                        {{-- 2. Satır: Sanatçı --}}
                                                        <div class="text-muted" style="font-size: 12px;">
                                                            <i class="fas fa-user fa-xs text-green"></i> {{ $song->album?->artist?->getTranslated('title') ?? '-' }}
                                                        </div>
                                                        {{-- 3. Satır: Albüm + Tür --}}
                                                        <div class="text-muted" style="font-size: 11px;">
                                                            @if($song->album)
                                                                <i class="fas fa-compact-disc fa-xs text-azure"></i> {{ $song->album->getTranslated('title') }}
                                                            @endif
                                                            @if($song->genre)
                                                                <span class="mx-1">·</span>
                                                                <i class="fas fa-tag fa-xs text-purple"></i> {{ $song->genre->getTranslated('title') }}
                                                            @endif
                                                            @if($song->duration)
                                                                <span class="mx-1">·</span>
                                                                {{ $song->getFormattedDuration() }}
                                                            @endif
                                                        </div>

                                                        {{-- 3. Satır: Arama eşleşme bilgisi --}}
                                                        @if($search)
                                                            @php
                                                                $searchLower = mb_strtolower($search);
                                                                $matchBadges = [];

                                                                // Şarkı sözlerinde eşleşme
                                                                $lyrics = $song->getTranslated('lyrics');
                                                                if ($lyrics && str_contains(mb_strtolower(strip_tags($lyrics)), $searchLower)) {
                                                                    $matchBadges[] = '<span class="badge bg-yellow-lt text-yellow"><i class="fas fa-file-alt me-1"></i>Şarkı sözünde</span>';
                                                                }

                                                                // Albümde eşleşme (şarkı adında yoksa göster)
                                                                $songTitle = $song->getTranslated('title') ?? '';
                                                                $albumTitle = $song->album?->getTranslated('title') ?? '';
                                                                if ($albumTitle && str_contains(mb_strtolower($albumTitle), $searchLower) && !str_contains(mb_strtolower($songTitle), $searchLower)) {
                                                                    $matchBadges[] = '<span class="badge bg-azure-lt text-azure"><i class="fas fa-compact-disc me-1"></i>Albüm eşleşmesi</span>';
                                                                }

                                                                // Türde eşleşme
                                                                $genreTitle = $song->genre?->getTranslated('title') ?? '';
                                                                if ($genreTitle && str_contains(mb_strtolower($genreTitle), $searchLower) && !str_contains(mb_strtolower($songTitle), $searchLower)) {
                                                                    $matchBadges[] = '<span class="badge bg-purple-lt text-purple"><i class="fas fa-tag me-1"></i>Tür eşleşmesi</span>';
                                                                }

                                                                // Sanatçıda eşleşme
                                                                $artistTitle = $song->album?->artist?->getTranslated('title') ?? '';
                                                                if ($artistTitle && str_contains(mb_strtolower($artistTitle), $searchLower) && !str_contains(mb_strtolower($songTitle), $searchLower)) {
                                                                    $matchBadges[] = '<span class="badge bg-green-lt text-green"><i class="fas fa-user me-1"></i>Sanatçı eşleşmesi</span>';
                                                                }
                                                            @endphp
                                                            @if(count($matchBadges) > 0)
                                                                <div class="mt-1">
                                                                    {!! implode(' ', $matchBadges) !!}
                                                                </div>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Play Butonu -->
                                            <div class="col-auto">
                                                @if($song->hls_path || $song->file_path)
                                                    <button type="button"
                                                        onclick="playAdminSong({
                                                            id: {{ $song->song_id }},
                                                            title: '{{ addslashes($song->getTranslated('title', app()->getLocale()) ?? $song->getTranslated('title', 'tr')) }}',
                                                            artist: '{{ addslashes($song->album?->artist?->getTranslated('title', app()->getLocale()) ?? '') }}',
                                                            @if($song->hls_path)
                                                            url: '{{ asset('storage/' . $song->hls_path) }}',
                                                            is_hls: true
                                                            @else
                                                            url: '{{ asset('storage/muzibu/songs/' . $song->file_path) }}',
                                                            is_hls: false
                                                            @endif
                                                        })"
                                                        class="btn btn-primary btn-icon"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-placement="top"
                                                        title="Dinle">
                                                        <i class="fas fa-play"></i>
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-secondary btn-icon" disabled
                                                        data-bs-toggle="tooltip"
                                                        data-bs-placement="top"
                                                        title="Dosya yok">
                                                        <i class="fas fa-play"></i>
                                                    </button>
                                                @endif
                                            </div>

                                            <!-- Ekle Butonu -->
                                            <div class="col-auto">
                                                @if(in_array($song->song_id, $this->selectedSongIds))
                                                    <button class="btn btn-sm btn-success disabled" disabled>
                                                        <i class="fas fa-check me-1"></i>
                                                        {{ __('muzibu::admin.playlist.added') }}
                                                    </button>
                                                @else
                                                    <button wire:click="addSong({{ $song->song_id }})"
                                                            class="btn btn-sm btn-success"
                                                            wire:loading.attr="disabled"
                                                            wire:target="addSong({{ $song->song_id }})">
                                                        <span wire:loading.remove wire:target="addSong({{ $song->song_id }})">
                                                            <i class="fas fa-plus me-1"></i>
                                                            {{ __('admin.add') }}
                                                        </span>
                                                        <span wire:loading wire:target="addSong({{ $song->song_id }})">
                                                            <span class="spinner-border spinner-border-sm me-1"></span>
                                                            {{ __('admin.loading') }}
                                                        </span>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="list-group-item">
                                        <div class="empty py-4">
                                            <div class="empty-icon">
                                                <i class="fas fa-search fa-3x"></i>
                                            </div>
                                            <p class="empty-title">{{ __('muzibu::admin.playlist.no_songs_found') }}</p>
                                            <p class="empty-subtitle">
                                                {{ __('muzibu::admin.playlist.try_different_search') }}
                                            </p>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Footer: Toplam şarkı sayısı -->
                        <div class="card-footer">
                            <small>
                                {{ __('muzibu::admin.playlist.showing_songs', ['count' => count($this->availableSongs)]) }}
                            </small>
                        </div>
                    </div>
                </div>

                <!-- SAĞ KOLON: PLAYLIST ŞARKILARI -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-list-ol me-2 text-primary"></i>
                                {{ __('muzibu::admin.playlist.playlist_songs') }}
                                <span class="badge bg-primary ms-2">{{ count($this->playlistSongs) }}</span>
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <!-- Playlist Şarkı Listesi (Sortable) -->
                            <div id="sortable-playlist"
                                 class="list-group list-group-flush"
                                 style="max-height: 600px; overflow-y: auto;">
                                @forelse($this->playlistSongs as $index => $song)
                                    <div class="list-group-item sortable-item"
                                         data-song-id="{{ $song->song_id }}"
                                         wire:key="playlist-{{ $song->song_id }}">
                                        <div class="row align-items-center">
                                            <!-- Drag Handle -->
                                            <div class="col-auto">
                                                <i class="fas fa-grip-vertical sortable-handle"
                                                   style="cursor: grab; font-size: 1.2rem;"></i>
                                            </div>

                                            <!-- Sıra Numarası -->
                                            <div class="col-auto">
                                                <span class="badge bg-secondary">{{ $index + 1 }}</span>
                                            </div>

                                            <!-- Şarkı Bilgisi -->
                                            <div class="col">
                                                <div class="d-flex align-items-center">
                                                    <!-- Cover Image -->
                                                    @if($song->media_id)
                                                        <img src="{{ thumb($song->coverMedia, 40, 40) }}"
                                                             alt="{{ $song->getTranslated('title') }}"
                                                             class="rounded me-2"
                                                             style="width: 40px; height: 40px; object-fit: cover;">
                                                    @else
                                                        <div class="avatar avatar-sm me-2 bg-primary-lt">
                                                            <i class="fas fa-music"></i>
                                                        </div>
                                                    @endif

                                                    <!-- Title + Artist -->
                                                    <div>
                                                        <strong class="d-block">
                                                            {{ $song->getTranslated('title', app()->getLocale()) ?? $song->getTranslated('title', 'tr') }}
                                                        </strong>
                                                        <small class="">
                                                            {{ $song->album?->artist?->getTranslated('title') ?? __('admin.unknown') }}
                                                            @if($song->duration)
                                                                · {{ $song->getFormattedDuration() }}
                                                            @endif
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Play Butonu -->
                                            <div class="col-auto">
                                                @if($song->hls_path || $song->file_path)
                                                    <button type="button"
                                                        onclick="playAdminSong({
                                                            id: {{ $song->song_id }},
                                                            title: '{{ addslashes($song->getTranslated('title', app()->getLocale()) ?? $song->getTranslated('title', 'tr')) }}',
                                                            artist: '{{ addslashes($song->album?->artist?->getTranslated('title', app()->getLocale()) ?? '') }}',
                                                            @if($song->hls_path)
                                                            url: '{{ asset('storage/' . $song->hls_path) }}',
                                                            is_hls: true
                                                            @else
                                                            url: '{{ asset('storage/muzibu/songs/' . $song->file_path) }}',
                                                            is_hls: false
                                                            @endif
                                                        })"
                                                        class="btn btn-primary btn-icon"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-placement="top"
                                                        title="Dinle">
                                                        <i class="fas fa-play"></i>
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-secondary btn-icon" disabled
                                                        data-bs-toggle="tooltip"
                                                        data-bs-placement="top"
                                                        title="Dosya yok">
                                                        <i class="fas fa-play"></i>
                                                    </button>
                                                @endif
                                            </div>

                                            <!-- Çıkar Butonu -->
                                            <div class="col-auto">
                                                <button wire:click="removeSong({{ $song->song_id }})"
                                                        class="btn btn-sm btn-outline-danger"
                                                        wire:loading.attr="disabled"
                                                        wire:target="removeSong({{ $song->song_id }})"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-placement="top"
                                                        title="{{ __('admin.remove') }}">
                                                    <span wire:loading.remove wire:target="removeSong({{ $song->song_id }})">
                                                        <i class="fas fa-times"></i>
                                                    </span>
                                                    <span wire:loading wire:target="removeSong({{ $song->song_id }})">
                                                        <span class="spinner-border spinner-border-sm"></span>
                                                    </span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="list-group-item">
                                        <div class="empty py-5">
                                            <div class="empty-icon">
                                                <i class="fas fa-music-slash fa-3x"></i>
                                            </div>
                                            <p class="empty-title">{{ __('muzibu::admin.playlist.no_songs_in_playlist') }}</p>
                                            <p class="empty-subtitle">
                                                {{ __('muzibu::admin.playlist.add_songs_from_left') }}
                                            </p>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Footer: Toplam süre -->
                        @if(count($this->playlistSongs) > 0)
                            <div class="card-footer">
                                <small>
                                    <i class="fas fa-clock me-1"></i>
                                    {{ __('muzibu::admin.playlist.total_duration') }}:
                                    <strong>{{ $playlist->getFormattedTotalDuration() }}</strong>
                                </small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Mini Player --}}
    @include('muzibu::admin.partials.mini-player')
</div>

@push('scripts')
<script src="{{ asset('admin-assets/libs/sortable/sortable.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sortableEl = document.getElementById('sortable-playlist');

    if (sortableEl) {
        const sortable = Sortable.create(sortableEl, {
            handle: '.sortable-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            onEnd: function(evt) {
                // Yeni sıralamayı topla
                const items = sortableEl.querySelectorAll('.sortable-item');
                const newOrder = {};

                items.forEach((item, index) => {
                    const songId = parseInt(item.dataset.songId);
                    newOrder[songId] = index;
                });

                // Livewire'a gönder
                @this.reorderSongs(newOrder);
            }
        });
    }
});
</script>
@endpush

@push('styles')
<style>
/* Sortable ghost (placeholder) */
.sortable-ghost {
    opacity: 0.4;
    background-color: #f8f9fa;
}

/* Sortable chosen (being dragged) */
.sortable-chosen {
    background-color: #e7f3ff;
}

/* Sortable handle hover */
.sortable-handle:hover {
    color: #206bc4 !important;
}

/* Sortable handle active (grabbing) */
.sortable-handle:active {
    cursor: grabbing !important;
}
</style>
@endpush
