@php
    View::share('pretitle', __('muzibu::admin.radio_list'));
@endphp

<div class="radio-component-wrapper">
    <div class="card">
        <div class="card-body p-0">
            <!-- Header Bölümü -->
            <div class="row mx-2 my-3">
                <!-- Arama Kutusu -->
                <div class="col">
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" wire:model.live="search" class="form-control"
                            placeholder="{{ __('muzibu::admin.radio.search_placeholder') }}">
                    </div>
                </div>
                <!-- Ortadaki Loading -->
                <div class="col position-relative">
                    <div wire:loading
                        wire:target="render, search, perPage, sortBy, gotoPage, previousPage, nextPage, delete, selectedItems, selectAll, bulkDelete, bulkToggleActive, toggleFeatured"
                        class="position-absolute top-50 start-50 translate-middle text-center"
                        style="width: 100%; max-width: 250px;">
                        <div class="small mb-2">{{ __('admin.updating') }}</div>
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

                        <!-- Yeni Radyo Ekle -->
                        @hasmoduleaccess('muzibu', 'create')
                        <a href="{{ route('admin.muzibu.radio.manage') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>{{ __('muzibu::admin.add_radio') }}
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
                                    class="table-sort {{ ($sortField ?? '') === 'radio_id' ? (($sortDirection ?? 'desc') === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('radio_id')">
                                    ID
                                </button>
                            </th>
                            <th style="min-width: 200px">
                                <button
                                    class="table-sort {{ ($sortField ?? '') === 'title' ? (($sortDirection ?? 'desc') === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('title')">
                                    {{ __('muzibu::admin.radio.title_field') }}
                                </button>
                            </th>
                            <th class="text-center" style="width: 80px">{{ __('muzibu::admin.playlists') }}</th>
                            <th class="text-center" style="width: 80px">{{ __('muzibu::admin.songs') }}</th>
                            <th class="text-center" style="width: 80px">{{ __('muzibu::admin.duration') }}</th>
                            @if($detailedView)
                            <th class="text-center" style="min-width: 150px">{{ __('muzibu::admin.sectors') }}</th>
                            @endif
                            <th class="text-center" style="width: 70px">
                                <i class="fas fa-star text-warning" title="Öne Çıkan"></i>
                            </th>
                            <th class="text-center" style="width: 70px">
                                {{ __('muzibu::admin.radio.status') }}
                            </th>
                            <th class="text-center" style="width: 120px">{{ __('admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="table-tbody">
                        @forelse($radios as $radio)
                            <tr class="hover-trigger" wire:key="row-{{ $radio->radio_id }}">
                                <td class="text-center">
                                    <input type="checkbox"
                                           wire:model.live="selectedItems"
                                           value="{{ $radio->radio_id }}"
                                           class="form-check-input"
                                           id="checkbox-{{ $radio->radio_id }}"
                                           @checked(in_array($radio->radio_id, $selectedItems))>
                                </td>
                                <td class="text-center small text-muted">
                                    {{ $radio->radio_id }}
                                </td>
                                <td wire:key="title-{{ $radio->radio_id }}">
                                    @if ($editingTitleId === $radio->radio_id)
                                        <div class="d-flex align-items-center gap-3" x-data
                                            @click.outside="$wire.updateTitleInline()">
                                            <div class="flexible-input-wrapper">
                                                <input type="text" wire:model.defer="newTitle"
                                                    class="form-control form-control-sm flexible-input"
                                                    placeholder="{{ __('muzibu::admin.radio.title_field') }}"
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
                                                class="editable-title pr-4">{{ $radio->getTranslated('title', $currentSiteLocale) ?? $radio->getTranslated('title', 'tr') }}</span>
                                            <button class="btn btn-sm px-2 py-1 edit-icon ms-4"
                                                wire:click="startEditingTitle({{ $radio->radio_id }}, '{{ addslashes($radio->getTranslated('title', $currentSiteLocale) ?? $radio->getTranslated('title', 'tr')) }}')">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @php $playlistCount = $radio->playlists->count(); @endphp
                                    @if($playlistCount > 0)
                                        <span class="badge bg-cyan-lt">{{ $playlistCount }}</span>
                                    @else
                                        <span class="badge bg-secondary-lt">0</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @php $songsCount = $radio->getTotalSongsCount(); @endphp
                                    @if($songsCount > 0)
                                        <span class="badge bg-blue-lt">{{ $songsCount }}</span>
                                    @else
                                        <span class="badge bg-secondary-lt">0</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="small text-muted">{{ $radio->getFormattedTotalDuration() }}</span>
                                </td>
                                @if($detailedView)
                                <td class="text-center">
                                    @if($radio->sectors && $radio->sectors->count() > 0)
                                        <div class="d-flex flex-wrap gap-1 justify-content-center">
                                            @foreach($radio->sectors as $sector)
                                                <span class="badge bg-purple-lt" style="font-size: 0.7rem;">{{ $sector->getTranslated('title', $currentSiteLocale) ?? $sector->getTranslated('title', 'tr') }}</span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                @endif
                                <td class="text-center align-middle">
                                    <button wire:click="toggleFeatured({{ $radio->radio_id }})"
                                        class="btn btn-icon btn-sm ps-1 pe-2 bg-transparent"
                                        data-bs-toggle="tooltip"
                                        title="{{ $radio->is_featured ? __('muzibu::admin.radio.featured') : __('muzibu::admin.radio.not_featured') }}">
                                        <!-- Loading Durumu -->
                                        <div wire:loading wire:target="toggleFeatured({{ $radio->radio_id }})"
                                            class="spinner-border spinner-border-sm">
                                        </div>
                                        <!-- Normal Durum: Featured/Not Featured İkonları -->
                                        <div wire:loading.remove
                                            wire:target="toggleFeatured({{ $radio->radio_id }})">
                                            @if ($radio->is_featured)
                                                <i class="fas fa-star text-warning"></i>
                                            @else
                                                <i class="far fa-star text-muted"></i>
                                            @endif
                                        </div>
                                    </button>
                                </td>
                                <td class="text-center align-middle">
                                    <button wire:click="toggleActive({{ $radio->radio_id }})"
                                        class="btn btn-icon btn-sm ps-1 pe-2 {{ $radio->is_active ? 'bg-transparent' : 'text-red bg-transparent' }}">
                                        <!-- Loading Durumu -->
                                        <div wire:loading wire:target="toggleActive({{ $radio->radio_id }})"
                                            class="spinner-border spinner-border-sm">
                                        </div>
                                        <!-- Normal Durum: Aktif/Pasif İkonları -->
                                        <div wire:loading.remove
                                            wire:target="toggleActive({{ $radio->radio_id }})">
                                            @if ($radio->is_active)
                                                <i class="fas fa-check"></i>
                                            @else
                                                <i class="fas fa-times"></i>
                                            @endif
                                        </div>
                                    </button>
                                </td>
                                <td class="text-center align-middle">
                                    <div class="d-flex align-items-center gap-3 justify-content-center">
                                        <a href="{{ route('admin.muzibu.radio.manage', $radio->radio_id) }}"
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
                                                    module: 'radio',
                                                    id: {{ $radio->radio_id }},
                                                    title: '{{ addslashes($radio->getTranslated('title', app()->getLocale()) ?? $radio->getTranslated('title', 'tr')) }}'
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
                                <td colspan="{{ $detailedView ? 10 : 9 }}" class="text-center py-4">
                                    <div class="empty">
                                        <p class="empty-title">{{ __('muzibu::admin.radio.no_radios_found') }}</p>
                                        <p class="empty-subtitle">
                                            {{ __('muzibu::admin.radio.no_results') }}
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
            @if ($radios->hasPages())
                {{ $radios->links() }}
            @else
                <div class="d-flex justify-content-between align-items-center mb-0">
                    <p class="small mb-0">
                        Toplam <span class="fw-semibold">{{ $radios->total() }}</span> sonuç
                    </p>
                </div>
            @endif
        </div>

        <!-- Bulk Actions -->
        @include('muzibu::admin.partials.bulk-actions', ['moduleType' => 'radio'])

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
