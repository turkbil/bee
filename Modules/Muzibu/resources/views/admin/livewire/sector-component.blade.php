@php
    View::share('pretitle', __('muzibu::admin.sector_list'));
@endphp

<div class="sector-component-wrapper">
    <div class="card">
        <div class="card-body p-0">
            <!-- Filtre Bölümü -->
            <div class="row mx-2 my-3">
                <!-- Sol Taraf - Arama -->
                <div class="col-auto">
                    <div class="input-icon" style="width: 250px;">
                        <span class="input-icon-addon">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" wire:model.live="search" class="form-control"
                            placeholder="{{ __('muzibu::admin.sector.search_placeholder') }}">
                    </div>
                </div>

                <!-- Ortadaki Loading -->
                <div class="col position-relative">
                    <div wire:loading
                        wire:target="render, search, perPage, sortBy, gotoPage, previousPage, nextPage, delete, selectedItems, selectAll, bulkDelete, bulkToggleActive"
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

                        <!-- Yeni Sektör Ekle -->
                        @hasmoduleaccess('muzibu', 'create')
                        <a href="{{ route('admin.muzibu.sector.manage') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>{{ __('muzibu::admin.add_sector') }}
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
                                    class="table-sort {{ ($sortField ?? '') === 'sector_id' ? (($sortDirection ?? 'desc') === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('sector_id')">
                                    ID
                                </button>
                            </th>
                            <th>
                                <button
                                    class="table-sort {{ ($sortField ?? '') === 'title' ? (($sortDirection ?? 'desc') === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('title')">
                                    {{ __('muzibu::admin.sector.title_field') }}
                                </button>
                            </th>
                            @if($detailedView)
                            <th class="text-center" style="width: 100px">{{ __('muzibu::admin.playlists') }}</th>
                            <th class="text-center" style="width: 100px">{{ __('muzibu::admin.radios') }}</th>
                            @endif
                            <th class="text-center" style="width: 80px">
                                {{ __('muzibu::admin.sector.status') }}
                            </th>
                            <th class="text-center" style="width: 160px">{{ __('admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="table-tbody">
                        @forelse($sectors as $sector)
                            <tr class="hover-trigger" wire:key="row-{{ $sector->sector_id }}">
                                <td class="text-center">
                                    <input type="checkbox"
                                           wire:model.live="selectedItems"
                                           value="{{ $sector->sector_id }}"
                                           class="form-check-input"
                                           id="checkbox-{{ $sector->sector_id }}"
                                           @checked(in_array($sector->sector_id, $selectedItems))>
                                </td>
                                <td class="text-center small text-muted">
                                    {{ $sector->sector_id }}
                                </td>
                                <td wire:key="title-{{ $sector->sector_id }}">
                                    @if ($editingTitleId === $sector->sector_id)
                                        <div class="d-flex align-items-center gap-3" x-data
                                            @click.outside="$wire.updateTitleInline()">
                                            <div class="flexible-input-wrapper">
                                                <input type="text" wire:model.defer="newTitle"
                                                    class="form-control form-control-sm flexible-input"
                                                    placeholder="{{ __('muzibu::admin.sector.title_field') }}"
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
                                                class="editable-title pr-4">{{ $sector->getTranslated('title', $currentSiteLocale) ?? $sector->getTranslated('title', 'tr') }}</span>
                                            <button class="btn btn-sm px-2 py-1 edit-icon ms-4"
                                                wire:click="startEditingTitle({{ $sector->sector_id }}, '{{ addslashes($sector->getTranslated('title', $currentSiteLocale) ?? $sector->getTranslated('title', 'tr')) }}')">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                        </div>
                                    @endif
                                </td>
                                @if($detailedView)
                                <td class="text-center">
                                    @if(($sector->playlists_count ?? 0) > 0)
                                        <a href="{{ route('admin.muzibu.playlist.index') }}?filterSector={{ $sector->sector_id }}"
                                           class="badge bg-blue-lt text-decoration-none">
                                            {{ $sector->playlists_count }}
                                        </a>
                                    @else
                                        <span class="badge bg-secondary-lt">0</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if(($sector->radios_count ?? 0) > 0)
                                        <a href="{{ route('admin.muzibu.radio.index') }}?filterSector={{ $sector->sector_id }}"
                                           class="badge bg-green-lt text-decoration-none">
                                            {{ $sector->radios_count }}
                                        </a>
                                    @else
                                        <span class="badge bg-secondary-lt">0</span>
                                    @endif
                                </td>
                                @endif
                                <td class="text-center align-middle">
                                    <button wire:click="toggleActive({{ $sector->sector_id }})"
                                        class="btn btn-icon btn-sm ps-1 pe-2 {{ $sector->is_active ? 'bg-transparent' : 'text-red bg-transparent' }}">
                                        <!-- Loading Durumu -->
                                        <div wire:loading wire:target="toggleActive({{ $sector->sector_id }})"
                                            class="spinner-border spinner-border-sm">
                                        </div>
                                        <!-- Normal Durum: Aktif/Pasif İkonları -->
                                        <div wire:loading.remove
                                            wire:target="toggleActive({{ $sector->sector_id }})">
                                            @if ($sector->is_active)
                                                <i class="fas fa-check"></i>
                                            @else
                                                <i class="fas fa-times"></i>
                                            @endif
                                        </div>
                                    </button>
                                </td>
                                <td class="text-center align-middle">
                                    <div class="d-flex align-items-center gap-3 justify-content-center">
                                        <a href="{{ route('admin.muzibu.sector.manage', $sector->sector_id) }}"
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
                                                    module: 'sector',
                                                    id: {{ $sector->sector_id }},
                                                    title: '{{ addslashes($sector->getTranslated('title', app()->getLocale()) ?? $sector->getTranslated('title', 'tr')) }}'
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
                                <td colspan="{{ $detailedView ? 7 : 5 }}" class="text-center py-4">
                                    <div class="empty">
                                        <p class="empty-title">{{ __('muzibu::admin.sector.no_sectors_found') }}</p>
                                        <p class="empty-subtitle">
                                            {{ __('muzibu::admin.sector.no_results') }}
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
            @if ($sectors->hasPages())
                {{ $sectors->links() }}
            @else
                <div class="d-flex justify-content-between align-items-center mb-0">
                    <p class="small mb-0">
                        Toplam <span class="fw-semibold">{{ $sectors->total() }}</span> sonuç
                    </p>
                </div>
            @endif
        </div>

        <!-- Bulk Actions -->
        @include('muzibu::admin.partials.bulk-actions', ['moduleType' => 'sector'])

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
