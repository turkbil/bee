@php
    // Dinamik başlık oluştur
    $pretitle = __('muzibu::admin.album_list');
    $subtitle = '';

    if ($filterArtist) {
        $artistModel = $this->artists->firstWhere('artist_id', $filterArtist);
        if ($artistModel) {
            $subtitle = $artistModel->getTranslated('title', app()->getLocale()) ?? $artistModel->getTranslated('title', 'tr');
        }
    }

    View::share('pretitle', $pretitle);
    View::share('subtitle', $subtitle);
@endphp

<div class="album-component-wrapper">
    <div class="card">
        <div class="card-body p-0">
            <!-- Aktif Filtre Bağlamı -->
            @if($filterArtist)
                <div class="card-header bg-azure-lt">
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-filter"></i>
                            <span class="fw-medium">Gösterilen:</span>
                        </div>
                        @php $artistModel = $this->artists->firstWhere('artist_id', $filterArtist); @endphp
                        @if($artistModel)
                            <span class="badge bg-purple-lt fs-6">
                                {{ $artistModel->getTranslated('title', app()->getLocale()) ?? $artistModel->getTranslated('title', 'tr') }}
                            </span>
                        @endif
                        <a href="{{ route('admin.muzibu.album.index') }}" class="btn btn-outline-secondary ms-auto">
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
                                placeholder="{{ __('muzibu::admin.album.search_placeholder') }}">
                        </div>

                        <!-- Sanatçı Filtresi -->
                        <select wire:model.live="filterArtist" class="form-select" style="width: 150px;">
                            <option value="">{{ __('muzibu::admin.album.all_artists') }}</option>
                            @foreach($this->artists as $artist)
                                <option value="{{ $artist->artist_id }}">
                                    {{ $artist->getTranslated('title', app()->getLocale()) ?? $artist->getTranslated('title', 'tr') }}
                                </option>
                            @endforeach
                        </select>

                        <!-- Temizle -->
                        @if($search || $filterArtist)
                            <button wire:click="clearFilters" class="btn btn-icon btn-ghost-secondary" title="{{ __('admin.clear_filters') }}">
                                <i class="fas fa-times"></i>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Ortadaki Loading -->
                <div class="col position-relative">
                    <div wire:loading
                        wire:target="render, search, perPage, sortBy, gotoPage, previousPage, nextPage, delete, selectedItems, selectAll, bulkDelete, bulkToggleActive, filterArtist"
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

                        <select wire:model.live="perPage" class="form-select" style="width: 75px;">
                            <option value="10">10</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>

                        <!-- Yeni Albüm Ekle -->
                        @hasmoduleaccess('muzibu', 'create')
                        <a href="{{ route('admin.muzibu.album.manage') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>{{ __('muzibu::admin.add_album') }}
                        </a>
                        @endhasmoduleaccess
                    </div>
                </div>
            </div>
            <!-- Tablo Bölümü -->
            <div id="table-default" class="table-responsive">
                <table class="table table-vcenter card-table table-hover text-nowrap datatable">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px">
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
                            </th>
                            <th class="text-center" style="width: 60px">
                                <button
                                    class="table-sort {{ ($sortField ?? '') === 'album_id' ? (($sortDirection ?? 'desc') === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('album_id')">
                                    ID
                                </button>
                            </th>
                            <th style="min-width: 200px">
                                <button
                                    class="table-sort {{ ($sortField ?? '') === 'title' ? (($sortDirection ?? 'desc') === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('title')">
                                    {{ __('muzibu::admin.album.title_field') }}
                                </button>
                            </th>
                            <th class="text-center" style="width: 80px">{{ __('muzibu::admin.songs') }}</th>
                            <th class="text-center" style="width: 80px">{{ __('muzibu::admin.duration') }}</th>
                            @if($detailedView)
                                <th style="min-width: 120px">{{ __('muzibu::admin.artists') }}</th>
                                <th class="text-center" style="width: 80px">{{ __('muzibu::admin.album.year') }}</th>
                            @endif
                            <th class="text-center" style="width: 70px">
                                {{ __('muzibu::admin.album.status') }}
                            </th>
                            <th class="text-center" style="width: 120px">{{ __('admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="table-tbody">
                        @forelse($albums as $album)
                            <tr class="hover-trigger" wire:key="row-{{ $album->album_id }}">
                                <td class="text-center">
                                    <input type="checkbox"
                                           wire:model.live="selectedItems"
                                           value="{{ $album->album_id }}"
                                           class="form-check-input"
                                           id="checkbox-{{ $album->album_id }}"
                                           @checked(in_array($album->album_id, $selectedItems))>
                                </td>
                                <td class="text-center small text-muted">
                                    {{ $album->album_id }}
                                </td>
                                <td wire:key="title-{{ $album->album_id }}">
                                    @if ($editingTitleId === $album->album_id)
                                        <div class="d-flex align-items-center gap-3" x-data
                                            @click.outside="$wire.updateTitleInline()">
                                            <div class="flexible-input-wrapper">
                                                <input type="text" wire:model.defer="newTitle"
                                                    class="form-control form-control-sm flexible-input"
                                                    placeholder="{{ __('muzibu::admin.album.title_field') }}"
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
                                            <span class="avatar avatar-sm me-2 bg-green-lt">
                                                <i class="fas fa-compact-disc"></i>
                                            </span>
                                            <span class="editable-title pr-4">{{ $album->getTranslated('title', $currentSiteLocale) ?? $album->getTranslated('title', 'tr') }}</span>
                                            <button class="btn btn-sm px-2 py-1 edit-icon ms-2"
                                                wire:click="startEditingTitle({{ $album->album_id }}, '{{ addslashes($album->getTranslated('title', $currentSiteLocale) ?? $album->getTranslated('title', 'tr')) }}')">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @php
                                        $songCount = $album->songs_count ?? $album->songs->count() ?? 0;
                                    @endphp
                                    @if($songCount > 0)
                                        <a href="{{ route('admin.muzibu.song.index') }}?filterAlbum={{ $album->album_id }}"
                                           class="badge bg-blue-lt text-decoration-none">
                                            {{ $songCount }}
                                        </a>
                                    @else
                                        <span class="badge bg-secondary-lt">0</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="small text-muted">{{ $album->getFormattedTotalDuration() }}</span>
                                </td>
                                @if($detailedView)
                                    <td>
                                        @if($album->artist)
                                            <a href="{{ route('admin.muzibu.album.index') }}?filterArtist={{ $album->artist->artist_id }}"
                                               class="small text-decoration-none">
                                                {{ $album->artist->getTranslated('title', $currentSiteLocale) ?? '-' }}
                                            </a>
                                        @else
                                            <span class="small">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="small">
                                            {{ $album->release_year ?? '-' }}
                                        </span>
                                    </td>
                                @endif
                                <td class="text-center align-middle">
                                    <button wire:click="toggleActive({{ $album->album_id }})"
                                        class="btn btn-icon btn-sm ps-1 pe-2 {{ $album->is_active ? 'bg-transparent' : 'text-red bg-transparent' }}">
                                        <div wire:loading wire:target="toggleActive({{ $album->album_id }})"
                                            class="spinner-border spinner-border-sm">
                                        </div>
                                        <div wire:loading.remove
                                            wire:target="toggleActive({{ $album->album_id }})">
                                            @if ($album->is_active)
                                                <i class="fas fa-check"></i>
                                            @else
                                                <i class="fas fa-times"></i>
                                            @endif
                                        </div>
                                    </button>
                                </td>
                                <td class="text-center align-middle">
                                    <div class="d-flex align-items-center gap-3 justify-content-center">
                                        <a href="{{ route('admin.muzibu.album.bulk-upload', $album->album_id) }}"
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="{{ __('muzibu::admin.bulk_upload.button') }}"
                                            style="min-height: 24px; display: inline-flex; align-items: center; text-decoration: none;">
                                            <i class="fa-solid fa-cloud-arrow-up link-secondary fa-lg"></i>
                                        </a>
                                        <a href="{{ route('admin.muzibu.album.manage', $album->album_id) }}"
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
                                                    module: 'album',
                                                    id: {{ $album->album_id }},
                                                    title: '{{ addslashes($album->getTranslated('title', app()->getLocale()) ?? $album->getTranslated('title', 'tr')) }}'
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
                                <td colspan="{{ $detailedView ? 9 : 7 }}" class="text-center py-4">
                                    <div class="empty">
                                        <p class="empty-title">{{ __('muzibu::admin.album.no_albums_found') }}</p>
                                        <p class="empty-subtitle">
                                            {{ __('muzibu::admin.album.no_results') }}
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
            @if ($albums->hasPages())
                {{ $albums->links() }}
            @else
                <div class="d-flex justify-content-between align-items-center mb-0">
                    <p class="small mb-0">
                        {{ __('admin.total') }} <span class="fw-semibold">{{ $albums->total() }}</span> {{ __('admin.results') }}
                    </p>
                </div>
            @endif
        </div>

        <!-- Bulk Actions -->
        @include('muzibu::admin.partials.bulk-actions', ['moduleType' => 'album'])

        <livewire:modals.bulk-delete-modal />
        <livewire:modals.delete-modal />

    </div>
</div>

@push('scripts')
    <script src="{{ asset('assets/js/simple-translation-modal.js') }}?v={{ time() }}"></script>
@endpush
