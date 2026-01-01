@php
    // Dinamik başlık oluştur
    $pretitle = __('muzibu::admin.playlist_list');
    $subtitle = '';

    if ($filterSector) {
        $sectorModel = $this->sectors->firstWhere('sector_id', $filterSector);
        if ($sectorModel) {
            $subtitle = $sectorModel->getTranslated('title', app()->getLocale()) ?? $sectorModel->getTranslated('title', 'tr');
        }
    }

    View::share('pretitle', $pretitle);
    View::share('subtitle', $subtitle);
@endphp

<div class="playlist-component-wrapper">
    <div class="card">
        <div class="card-body p-0">
            <!-- Aktif Filtre Bağlamı -->
            @if($filterSector)
                <div class="card-header bg-azure-lt">
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-filter"></i>
                            <span class="fw-medium">Gösterilen:</span>
                        </div>
                        @php $sectorModel = $this->sectors->firstWhere('sector_id', $filterSector); @endphp
                        @if($sectorModel)
                            <span class="badge bg-cyan-lt fs-6">
                                {{ $sectorModel->getTranslated('title', app()->getLocale()) ?? $sectorModel->getTranslated('title', 'tr') }}
                            </span>
                        @endif
                        <a href="{{ route('admin.muzibu.playlist.index') }}" class="btn btn-outline-secondary ms-auto">
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
                                placeholder="{{ __('muzibu::admin.playlist.search_placeholder') }}">
                        </div>

                        <!-- Sistem/Özel Filtresi -->
                        <select wire:model.live="showSystemPlaylists" class="form-select" style="width: 120px;">
                            <option value="1">Sistem</option>
                            <option value="0">Özel</option>
                        </select>

                        <!-- Sektör Filtresi -->
                        <select wire:model.live="filterSector" class="form-select" style="width: 150px;">
                            <option value="">{{ __('muzibu::admin.playlist.all_sectors') }}</option>
                            @foreach($this->sectors as $sector)
                                <option value="{{ $sector->sector_id }}">
                                    {{ $sector->getTranslated('title', app()->getLocale()) ?? $sector->getTranslated('title', 'tr') }}
                                </option>
                            @endforeach
                        </select>

                        <!-- Temizle -->
                        @if($search || $filterSector)
                            <button wire:click="clearFilters" class="btn btn-icon btn-ghost-secondary" title="{{ __('admin.clear_filters') }}">
                                <i class="fas fa-times"></i>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Ortadaki Loading -->
                <div class="col position-relative">
                    <div wire:loading
                        wire:target="render, search, perPage, sortBy, gotoPage, previousPage, nextPage, delete, selectedItems, selectAll, bulkDelete, bulkToggleActive, filterSector, showSystemPlaylists"
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
                        <div class="d-flex align-items-center gap-2">
                            <button type="button"
                                wire:click="$set('detailedView', false)"
                                class="btn btn-icon {{ !$detailedView ? 'btn-primary' : 'btn-outline-secondary' }}"
                                data-bs-toggle="tooltip" title="Minimal">
                                <i class="fas fa-th-list"></i>
                            </button>
                            <button type="button"
                                wire:click="$set('detailedView', true)"
                                class="btn btn-icon {{ $detailedView ? 'btn-primary' : 'btn-outline-secondary' }}"
                                data-bs-toggle="tooltip" title="Detaylı">
                                <i class="fas fa-table"></i>
                            </button>
                        </div>

                        <select wire:model.live="perPage" class="form-select" style="width: 75px;">
                            <option value="10">10</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>

                        <!-- Yeni Playlist Ekle -->
                        @hasmoduleaccess('muzibu', 'create')
                        <a href="{{ route('admin.muzibu.playlist.manage') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>{{ __('muzibu::admin.add_playlist') }}
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
                                    class="table-sort {{ ($sortField ?? '') === 'playlist_id' ? (($sortDirection ?? 'desc') === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('playlist_id')">
                                    ID
                                </button>
                            </th>
                            <th style="min-width: 200px">
                                <button
                                    class="table-sort {{ ($sortField ?? '') === 'title' ? (($sortDirection ?? 'desc') === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('title')">
                                    {{ __('muzibu::admin.playlist.title_field') }}
                                </button>
                            </th>
                            <th class="text-center" style="width: 80px">{{ __('muzibu::admin.songs') }}</th>
                            <th class="text-center" style="width: 80px">{{ __('muzibu::admin.duration') }}</th>
                            @if($detailedView)
                                <th class="text-center" style="min-width: 150px">{{ __('muzibu::admin.sectors') }}</th>
                            @endif
                            <th class="text-center" style="width: 70px">
                                {{ __('muzibu::admin.playlist.status') }}
                            </th>
                            <th class="text-center" style="width: 130px">{{ __('admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="table-tbody">
                        @forelse($playlists as $playlist)
                            <tr class="hover-trigger" wire:key="row-{{ $playlist->playlist_id }}">
                                <td class="text-center">
                                    <input type="checkbox"
                                           wire:model.live="selectedItems"
                                           value="{{ $playlist->playlist_id }}"
                                           class="form-check-input"
                                           id="checkbox-{{ $playlist->playlist_id }}"
                                           @checked(in_array($playlist->playlist_id, $selectedItems))>
                                </td>
                                <td class="text-center small text-muted">
                                    {{ $playlist->playlist_id }}
                                </td>
                                <td wire:key="title-{{ $playlist->playlist_id }}">
                                    @if ($editingTitleId === $playlist->playlist_id)
                                        <div class="d-flex align-items-center gap-3" x-data
                                            @click.outside="$wire.updateTitleInline()">
                                            <div class="flexible-input-wrapper">
                                                <input type="text" wire:model.defer="newTitle"
                                                    class="form-control form-control-sm flexible-input"
                                                    placeholder="{{ __('muzibu::admin.playlist.title_field') }}"
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
                                            <span
                                                class="editable-title pr-4">{{ $playlist->getTranslated('title', $currentSiteLocale) ?? $playlist->getTranslated('title', 'tr') }}</span>
                                            <button class="btn btn-sm px-2 py-1 edit-icon ms-4"
                                                wire:click="startEditingTitle({{ $playlist->playlist_id }}, '{{ addslashes($playlist->getTranslated('title', $currentSiteLocale) ?? $playlist->getTranslated('title', 'tr')) }}')">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if(($playlist->songs_count ?? 0) > 0)
                                        <a href="{{ route('admin.muzibu.playlist.songs', $playlist->playlist_id) }}"
                                           class="badge bg-blue-lt text-decoration-none">
                                            {{ $playlist->songs_count }}
                                        </a>
                                    @else
                                        <span class="badge bg-secondary-lt">0</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="small text-muted">{{ $playlist->getFormattedTotalDuration() }}</span>
                                </td>
                                @if($detailedView)
                                    <td class="text-center">
                                        @if($playlist->sectors->count() > 0)
                                            <div class="d-flex flex-wrap gap-1 justify-content-center">
                                                @foreach($playlist->sectors as $sector)
                                                    <a href="{{ route('admin.muzibu.playlist.index') }}?filterSector={{ $sector->sector_id }}"
                                                       class="badge bg-purple-lt text-decoration-none"
                                                       style="font-size: 0.7rem;">
                                                        {{ $sector->getTranslated('title', app()->getLocale()) ?? $sector->getTranslated('title', 'tr') }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @else
                                            <span>-</span>
                                        @endif
                                    </td>
                                @endif
                                <td class="text-center align-middle">
                                    <button wire:click="toggleActive({{ $playlist->playlist_id }})"
                                        class="btn btn-icon btn-sm ps-1 pe-2 {{ $playlist->is_active ? 'bg-transparent' : 'text-red bg-transparent' }}">
                                        <!-- Loading Durumu -->
                                        <div wire:loading wire:target="toggleActive({{ $playlist->playlist_id }})"
                                            class="spinner-border spinner-border-sm">
                                        </div>
                                        <!-- Normal Durum: Aktif/Pasif İkonları -->
                                        <div wire:loading.remove
                                            wire:target="toggleActive({{ $playlist->playlist_id }})">
                                            @if ($playlist->is_active)
                                                <i class="fas fa-check"></i>
                                            @else
                                                <i class="fas fa-times"></i>
                                            @endif
                                        </div>
                                    </button>
                                </td>
                                <td class="text-center align-middle">
                                    <div class="d-flex align-items-center gap-3 justify-content-center">
                                        <a href="{{ route('admin.muzibu.playlist.manage', $playlist->playlist_id) }}"
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="{{ __('admin.edit') }}"
                                            style="min-height: 24px; display: inline-flex; align-items: center; text-decoration: none;">
                                            <i class="fa-solid fa-pen-to-square link-secondary fa-lg"></i>
                                        </a>
                                        <a href="{{ route('admin.muzibu.playlist.songs', $playlist->playlist_id) }}"
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="{{ __('muzibu::admin.playlist.manage_songs') }}"
                                            style="min-height: 24px; display: inline-flex; align-items: center; text-decoration: none;">
                                            <i class="fas fa-music link-secondary fa-lg"></i>
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
                                                    module: 'playlist',
                                                    id: {{ $playlist->playlist_id }},
                                                    title: '{{ addslashes($playlist->getTranslated('title', app()->getLocale()) ?? $playlist->getTranslated('title', 'tr')) }}'
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
                                <td colspan="{{ $detailedView ? 8 : 7 }}" class="text-center py-4">
                                    <div class="empty">
                                        <p class="empty-title">{{ __('muzibu::admin.playlist.no_playlists_found') }}</p>
                                        <p class="empty-subtitle">
                                            {{ __('muzibu::admin.playlist.no_results') }}
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
            @if ($playlists->hasPages())
                {{ $playlists->links() }}
            @else
                <div class="d-flex justify-content-between align-items-center mb-0">
                    <p class="small mb-0">
                        Toplam <span class="fw-semibold">{{ $playlists->total() }}</span> sonuç
                    </p>
                </div>
            @endif
        </div>

        <!-- Bulk Actions -->
        @include('muzibu::admin.partials.bulk-actions', ['moduleType' => 'playlist'])

        <livewire:modals.bulk-delete-modal />
        <livewire:modals.delete-modal />

    </div>
</div>

@push('styles')
    {{-- Preload removed to prevent warning --}}
@endpush

@push('scripts')
    <script src="{{ asset('assets/js/simple-translation-modal.js') }}?v={{ time() }}"></script>
@endpush
