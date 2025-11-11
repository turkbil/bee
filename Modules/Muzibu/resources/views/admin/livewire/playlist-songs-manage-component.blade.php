@php
    View::share('pretitle', 'Playlist Şarkı Yönetimi');
@endphp

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
                        {{ $playlist->getTranslated('title', app()->getLocale()) ?? $playlist->getTranslated('title', 'tr') }}
                    </h2>
                </div>
                <div class="col-auto ms-auto">
                    <a href="{{ route('admin.muzibu.playlist') }}" class="btn btn-outline-secondary">
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
                                <i class="fas fa-music me-2 text-muted"></i>
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
                                                <div class="d-flex align-items-center">
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

                                                    <!-- Title + Artist -->
                                                    <div>
                                                        <strong class="d-block">
                                                            {{ $song->getTranslated('title', app()->getLocale()) ?? $song->getTranslated('title', 'tr') }}
                                                        </strong>
                                                        <small class="text-muted">
                                                            {{ $song->album?->artist?->getTranslated('title') ?? __('admin.unknown') }}
                                                            @if($song->duration)
                                                                · {{ $song->getFormattedDuration() }}
                                                            @endif
                                                        </small>
                                                    </div>
                                                </div>
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
                                                <i class="fas fa-search fa-3x text-muted"></i>
                                            </div>
                                            <p class="empty-title">{{ __('muzibu::admin.playlist.no_songs_found') }}</p>
                                            <p class="empty-subtitle text-muted">
                                                {{ __('muzibu::admin.playlist.try_different_search') }}
                                            </p>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Footer: Toplam şarkı sayısı -->
                        <div class="card-footer text-muted">
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
                                                <i class="fas fa-grip-vertical text-muted sortable-handle"
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
                                                        <small class="text-muted">
                                                            {{ $song->album?->artist?->getTranslated('title') ?? __('admin.unknown') }}
                                                            @if($song->duration)
                                                                · {{ $song->getFormattedDuration() }}
                                                            @endif
                                                        </small>
                                                    </div>
                                                </div>
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
                                                <i class="fas fa-music-slash fa-3x text-muted"></i>
                                            </div>
                                            <p class="empty-title">{{ __('muzibu::admin.playlist.no_songs_in_playlist') }}</p>
                                            <p class="empty-subtitle text-muted">
                                                {{ __('muzibu::admin.playlist.add_songs_from_left') }}
                                            </p>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Footer: Toplam süre -->
                        @if(count($this->playlistSongs) > 0)
                            <div class="card-footer text-muted">
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
