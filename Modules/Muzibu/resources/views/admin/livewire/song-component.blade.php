@php
    // Dinamik başlık oluştur
    $pretitle = __('muzibu::admin.song_list');
    $subtitle = '';
    $filterDetails = [];

    if ($filterArtist) {
        $artistModel = $this->artists->firstWhere('artist_id', $filterArtist);
        if ($artistModel) {
            $filterDetails[] = $artistModel->getTranslated('title', app()->getLocale()) ?? $artistModel->getTranslated('title', 'tr');
        }
    }

    if ($filterAlbum) {
        $albumModel = $this->albums->firstWhere('album_id', $filterAlbum);
        if ($albumModel) {
            $filterDetails[] = $albumModel->getTranslated('title', app()->getLocale()) ?? $albumModel->getTranslated('title', 'tr');
        }
    }

    if ($filterGenre) {
        $genreModel = $this->genres->firstWhere('genre_id', $filterGenre);
        if ($genreModel) {
            $filterDetails[] = $genreModel->getTranslated('title', app()->getLocale()) ?? $genreModel->getTranslated('title', 'tr');
        }
    }

    if (count($filterDetails) > 0) {
        $subtitle = implode(' · ', $filterDetails);
    }

    View::share('pretitle', $pretitle);
    View::share('subtitle', $subtitle);
@endphp

<div class="song-component-wrapper">
    <div class="card">
        <div class="card-body p-0">
            <!-- Aktif Filtre Bağlamı -->
            @if($filterArtist || $filterAlbum || $filterGenre)
                <div class="card-header bg-azure-lt">
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-filter"></i>
                            <span class="fw-medium">Gösterilen:</span>
                        </div>
                        @if($filterArtist)
                            @php $artistModel = $this->artists->firstWhere('artist_id', $filterArtist); @endphp
                            @if($artistModel)
                                <span class="badge bg-purple-lt fs-6">
                                    {{ $artistModel->getTranslated('title', app()->getLocale()) ?? $artistModel->getTranslated('title', 'tr') }}
                                </span>
                            @endif
                        @endif
                        @if($filterAlbum)
                            @php $albumModel = $this->albums->firstWhere('album_id', $filterAlbum); @endphp
                            @if($albumModel)
                                <span class="badge bg-green-lt fs-6">
                                    {{ $albumModel->getTranslated('title', app()->getLocale()) ?? $albumModel->getTranslated('title', 'tr') }}
                                </span>
                            @endif
                        @endif
                        @if($filterGenre)
                            @php $genreModel = $this->genres->firstWhere('genre_id', $filterGenre); @endphp
                            @if($genreModel)
                                <span class="badge bg-orange-lt fs-6">
                                    {{ $genreModel->getTranslated('title', app()->getLocale()) ?? $genreModel->getTranslated('title', 'tr') }}
                                </span>
                            @endif
                        @endif
                        <a href="{{ route('admin.muzibu.song.index') }}" class="btn btn-outline-secondary ms-auto">
                            <i class="fas fa-times me-1"></i>Tümünü Göster
                        </a>
                    </div>
                </div>
            @endif

            <!-- Filtre Bölümü -->
            <div class="row mx-2 my-3">
                <!-- Sol Taraf - Arama ve Filtreler -->
                <div class="col-auto">
                    <div class="d-flex align-items-center gap-2">
                        <!-- Arama -->
                        <div class="input-icon" style="width: 200px;">
                            <span class="input-icon-addon">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" wire:model.live="search" class="form-control"
                                placeholder="{{ __('muzibu::admin.song.search_placeholder') }}">
                        </div>

                        <!-- Filtreler -->
                        <select wire:model.live="filterArtist" class="form-select" style="width: 130px;">
                            <option value="">{{ __('muzibu::admin.song.all_artists') }}</option>
                            @foreach($this->artists as $artist)
                                <option value="{{ $artist->artist_id }}">
                                    {{ $artist->getTranslated('title', app()->getLocale()) ?? $artist->getTranslated('title', 'tr') }}
                                </option>
                            @endforeach
                        </select>

                        <select wire:model.live="filterGenre" class="form-select" style="width: 110px;">
                            <option value="">{{ __('muzibu::admin.song.all_genres') }}</option>
                            @foreach($this->genres as $genre)
                                <option value="{{ $genre->genre_id }}">
                                    {{ $genre->getTranslated('title', app()->getLocale()) ?? $genre->getTranslated('title', 'tr') }}
                                </option>
                            @endforeach
                        </select>

                        <select wire:model.live="filterAlbum" class="form-select" style="width: 130px;">
                            <option value="">{{ __('muzibu::admin.song.all_albums') }}</option>
                            @foreach($this->albums as $album)
                                <option value="{{ $album->album_id }}">
                                    {{ $album->getTranslated('title', app()->getLocale()) ?? $album->getTranslated('title', 'tr') }}
                                </option>
                            @endforeach
                        </select>

                        <!-- Temizle -->
                        @if($search || $filterArtist || $filterGenre || $filterAlbum)
                            <button wire:click="clearFilters" class="btn btn-icon btn-ghost-secondary" title="{{ __('admin.clear_filters') }}">
                                <i class="fas fa-times"></i>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Ortadaki Loading -->
                <div class="col position-relative">
                    <div wire:loading
                        wire:target="render, search, perPage, sortBy, gotoPage, previousPage, nextPage, delete, selectedItems, selectAll, bulkDelete, bulkToggleActive, filterArtist, filterGenre, filterAlbum, filterHls, detailedView"
                        class="position-absolute top-50 start-50 translate-middle text-center"
                        style="width: 100%; max-width: 250px;">
                        <div class="small text-muted mb-2">{{ __('admin.updating') }}</div>
                        <div class="progress mb-1">
                            <div class="progress-bar progress-bar-indeterminate"></div>
                        </div>
                    </div>
                </div>

                <!-- Sağ Taraf -->
                <div class="col-auto">
                    <div class="d-flex align-items-center justify-content-end gap-2">
                        <!-- Görünüm Toggle -->
                        <div class="btn-group" role="group">
                            <button type="button"
                                wire:click="$set('detailedView', false)"
                                class="btn btn-icon {{ !$detailedView ? 'btn-primary' : 'btn-ghost-secondary' }}"
                                title="Minimal">
                                <i class="fas fa-th-list"></i>
                            </button>
                            <button type="button"
                                wire:click="$set('detailedView', true)"
                                class="btn btn-icon {{ $detailedView ? 'btn-primary' : 'btn-ghost-secondary' }}"
                                title="Detaylı">
                                <i class="fas fa-table"></i>
                            </button>
                        </div>

                        <!-- Sayfa Adeti -->
                        <select wire:model.live="perPage" class="form-select" style="width: 75px;">
                            <option value="10">10</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tablo Bölümü -->
            <div id="table-default" class="table-responsive">
                <table class="table table-vcenter card-table table-hover text-nowrap datatable">
                    <thead>
                        <tr>
                            <th style="width: 50px">
                                <div class="d-flex align-items-center gap-2">
                                    <input type="checkbox"
                                           wire:model.live="selectAll"
                                           class="form-check-input"
                                           id="selectAllCheckbox"
                                           x-data="{
                                               indeterminate: {{ count($selectedItems ?? []) > 0 && !($selectAll ?? false) ? 'true' : 'false' }}
                                           }"
                                           x-init="$el.indeterminate = indeterminate"
                                           x-effect="$el.indeterminate = ({{ count($selectedItems ?? []) }} > 0 && !{{ ($selectAll ?? false) ? 'true' : 'false' }})"
                                           @checked($selectAll ?? false)>
                                    <button
                                        class="table-sort {{ ($sortField ?? '') === 'song_id' ? (($sortDirection ?? 'desc') === 'asc' ? 'asc' : 'desc') : '' }}"
                                        wire:click="sortBy('song_id')">
                                    </button>
                                </div>
                            </th>
                            <th style="min-width: 200px">
                                <button
                                    class="table-sort {{ ($sortField ?? '') === 'title' ? (($sortDirection ?? 'desc') === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('title')">
                                    {{ __('muzibu::admin.song.title_field') }}
                                </button>
                            </th>
                            @if($detailedView)
                                <th style="min-width: 120px">{{ __('muzibu::admin.artists') }}</th>
                                <th style="min-width: 120px">{{ __('muzibu::admin.song.album') }}</th>
                                <th style="width: 100px">{{ __('muzibu::admin.genres') }}</th>
                                <th class="text-center" style="width: 80px">{{ __('muzibu::admin.song.duration') }}</th>
                                <th class="text-center" style="width: 60px">HLS</th>
                            @endif
                            <th class="text-center" style="width: 90px">
                                <button
                                    class="table-sort {{ ($sortField ?? '') === 'play_count' ? (($sortDirection ?? 'desc') === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('play_count')">
                                    {{ __('muzibu::admin.song.play_count') }}
                                </button>
                            </th>
                            <th class="text-center" style="width: 70px">
                                {{ __('muzibu::admin.song.status') }}
                            </th>
                            <th class="text-center" style="width: 110px">{{ __('admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="table-tbody">
                        @forelse($songs as $song)
                            <tr class="hover-trigger" wire:key="row-{{ $song->song_id }}">
                                <td class="sort-id small">
                                    <div class="hover-toggle">
                                        <span class="hover-hide">{{ $song->song_id }}</span>
                                        <input type="checkbox"
                                               wire:model.live="selectedItems"
                                               value="{{ $song->song_id }}"
                                               class="form-check-input hover-show"
                                               id="checkbox-{{ $song->song_id }}"
                                               @checked(in_array($song->song_id, $selectedItems))>
                                    </div>
                                </td>
                                <td wire:key="title-{{ $song->song_id }}" class="position-relative">
                                    @if ($editingTitleId === $song->song_id)
                                        <div class="d-flex align-items-center gap-3" x-data
                                            @click.outside="$wire.updateTitleInline()">
                                            <div class="flexible-input-wrapper">
                                                <input type="text" wire:model.defer="newTitle"
                                                    class="form-control form-control-sm flexible-input"
                                                    placeholder="{{ __('muzibu::admin.song.title_field') }}"
                                                    wire:keydown.enter="updateTitleInline"
                                                    wire:keydown.escape="$set('editingTitleId', null)"
                                                    x-init="$nextTick(() => {
                                                        $el.focus();
                                                        $el.style.width = '20px';
                                                        $el.style.width = ($el.scrollWidth + 2) + 'px';
                                                    })"
                                                    x-on:input="
                                                $el.style.width = '20px';
                                                $el.style.width = ($el.scrollWidth + 2) + 'px'
                                            "
                                                    style="min-width: 60px; max-width: 100%;">
                                            </div>
                                            <button class="btn px-2 py-1 btn-outline-success"
                                                wire:click="updateTitleInline">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button class="btn px-2 py-1 btn-outline-danger"
                                                wire:click="$set('editingTitleId', null)">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    @else
                                        <div class="d-flex align-items-center">
                                            <span class="editable-title pr-4">{{ $song->getTranslated('title', $currentSiteLocale) ?? $song->getTranslated('title', 'tr') }}</span>
                                            <button class="btn btn-sm px-2 py-1 edit-icon ms-2"
                                                wire:click="startEditingTitle({{ $song->song_id }}, '{{ addslashes($song->getTranslated('title', $currentSiteLocale) ?? $song->getTranslated('title', 'tr')) }}')">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                        </div>
                                    @endif
                                </td>
                                @if($detailedView)
                                    <td>
                                        <span class="small">
                                            {{ $song->album?->artist?->getTranslated('title', $currentSiteLocale) ?? '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="small">
                                            {{ $song->album?->getTranslated('title', $currentSiteLocale) ?? '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($song->genre)
                                            <span class="badge bg-blue-lt">
                                                {{ $song->genre->getTranslated('title', $currentSiteLocale) ?? $song->genre->getTranslated('title', 'tr') }}
                                            </span>
                                        @else
                                            <span>-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="small">
                                            {{ $song->duration ? $song->getFormattedDuration() : '-' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($song->hls_converted)
                                            <span class="badge bg-green" data-bs-toggle="tooltip" title="{{ __('muzibu::admin.song.hls_ready') }}">
                                                <i class="fas fa-shield-alt"></i>
                                            </span>
                                        @elseif($song->file_path)
                                            <span class="badge bg-yellow" data-bs-toggle="tooltip" title="{{ __('muzibu::admin.song.hls_pending') }}">
                                                <i class="fas fa-clock"></i>
                                            </span>
                                        @else
                                            <span>-</span>
                                        @endif
                                    </td>
                                @endif
                                <td class="text-center">
                                    <span class="small">{{ $song->play_count ?? 0 }}</span>
                                </td>
                                <td class="text-center align-middle">
                                    <button wire:click="toggleActive({{ $song->song_id }})"
                                        class="btn btn-icon btn-sm ps-1 pe-2 {{ $song->is_active ? 'bg-transparent' : 'text-red bg-transparent' }}">
                                        <div wire:loading wire:target="toggleActive({{ $song->song_id }})"
                                            class="spinner-border spinner-border-sm">
                                        </div>
                                        <div wire:loading.remove
                                            wire:target="toggleActive({{ $song->song_id }})">
                                            @if ($song->is_active)
                                                <i class="fas fa-check"></i>
                                            @else
                                                <i class="fas fa-times"></i>
                                            @endif
                                        </div>
                                    </button>
                                </td>
                                <td class="text-end align-middle">
                                    <div class="d-flex align-items-center gap-3 justify-content-end">
                                        {{-- Play Button --}}
                                        @if($song->hls_path || $song->file_path)
                                            @php
                                                // hls_path'ten hash çıkar: "muzibu/hls/HASH/playlist.m3u8"
                                                $hlsHash = null;
                                                if ($song->hls_path && preg_match('/hls\/([^\/]+)\//', $song->hls_path, $matches)) {
                                                    $hlsHash = $matches[1];
                                                }
                                            @endphp
                                            <button type="button"
                                                onclick="playAdminSong({
                                                    id: {{ $song->song_id }},
                                                    title: '{{ addslashes($song->getTranslated('title', $currentSiteLocale) ?? $song->getTranslated('title', 'tr')) }}',
                                                    artist: '{{ addslashes($song->album?->artist?->getTranslated('title', $currentSiteLocale) ?? '') }}',
                                                    hls_hash: '{{ $hlsHash ?? '' }}',
                                                    file_path: '{{ basename($song->file_path ?? '') }}',
                                                    tenant_id: {{ tenant()->id ?? 1001 }},
                                                    is_hls: {{ $hlsHash ? 'true' : 'false' }}
                                                })"
                                                class="btn btn-icon btn-sm ps-1 pe-2"
                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                title="Dinle"
                                                style="min-height: 24px; display: inline-flex; align-items: center;">
                                                <i class="fas fa-play link-primary"></i>
                                            </button>
                                        @endif
                                        <a href="{{ route('admin.muzibu.song.manage', $song->song_id) }}"
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="{{ __('admin.edit') }}"
                                            style="min-height: 24px; display: inline-flex; align-items: center; text-decoration: none;">
                                            <i class="fa-solid fa-pen-to-square link-secondary fa-lg"></i>
                                        </a>
                                        @hasmoduleaccess('muzibu', 'delete')
                                        <div class="dropdown">
                                            <a class="dropdown-toggle" href="#" data-bs-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false"
                                                style="min-height: 24px; display: inline-flex; align-items: center; text-decoration: none;">
                                                <i class="fa-solid fa-bars-sort fa-flip-horizontal link-secondary fa-lg"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a href="javascript:void(0);"
                                                    wire:click="$dispatch('showDeleteModal', {
                                                    module: 'song',
                                                    id: {{ $song->song_id }},
                                                    title: '{{ addslashes($song->getTranslated('title', app()->getLocale()) ?? $song->getTranslated('title', 'tr')) }}'
                                                })"
                                                    class="dropdown-item link-danger">
                                                    {{ __('admin.delete') }}
                                                </a>
                                            </div>
                                        </div>
                                        @endhasmoduleaccess
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $detailedView ? 9 : 5 }}" class="text-center py-4">
                                    <div class="empty">
                                        <p class="empty-title">{{ __('muzibu::admin.song.no_songs_found') }}</p>
                                        <p class="empty-subtitle">
                                            {{ __('muzibu::admin.song.no_results') }}
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="card-footer">
            @if ($songs->hasPages())
                {{ $songs->links() }}
            @else
                <div class="d-flex justify-content-between align-items-center mb-0">
                    <p class="small mb-0">
                        {{ __('admin.total') }} <span class="fw-semibold">{{ $songs->total() }}</span> {{ __('admin.results') }}
                    </p>
                </div>
            @endif
        </div>

        <!-- Bulk Actions -->
        @include('muzibu::admin.partials.bulk-actions', ['moduleType' => 'song'])

        <livewire:modals.bulk-delete-modal />
        <livewire:modals.delete-modal />

    </div>

    {{-- Mini Player --}}
    @include('muzibu::admin.partials.mini-player')

    {{-- HLS Dokümantasyon Linki --}}
    <div class="card mt-3">
        <div class="card-body py-2">
            <div class="d-flex align-items-center justify-content-between">
                <span class="small text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Şarkı çalma sistemi hakkında teknik bilgi
                </span>
                <a href="{{ route('admin.muzibu.docs.hls-streaming') }}"
                   class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-book me-1"></i>
                    HLS Streaming Dokümantasyonu
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('assets/js/simple-translation-modal.js') }}?v={{ time() }}"></script>
@endpush
