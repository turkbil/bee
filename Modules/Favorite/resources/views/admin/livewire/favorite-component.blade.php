@php
    View::share('pretitle', 'Sayfa Listesi');
@endphp

<div class="favorite-component-wrapper">
    <div class="card">
        @include('favorite::admin.helper')
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
                            placeholder="{{ __('favorite::admin.search_placeholder') }}">
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
                <!-- Sağ Taraf (Switch ve Select) -->
                <div class="col">
                    <div class="d-flex align-items-center justify-content-end gap-3">
                        <!-- Sayfa Adeti Seçimi -->
                        <div style="width: 80px; min-width: 80px">
                            <select wire:model.live="perPage" class="form-control listing-filter-select" data-choices
                                data-choices-search="false" data-choices-filter="true">
                                <option value="10">
                                    <nobr>10</nobr>
                                </option>
                                <option value="50">
                                    <nobr>50</nobr>
                                </option>
                                <option value="100">
                                    <nobr>100</nobr>
                                </option>
                                <option value="500">
                                    <nobr>500</nobr>
                                </option>
                                <option value="1000">
                                    <nobr>1000</nobr>
                                </option>
                            </select>
                        </div>
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
                                               selectedCount: @js(count($selectedItems ?? [])),
                                               selectAllVal: @js($selectAll ?? false)
                                           }"
                                           x-init="$el.indeterminate = (selectedCount > 0 && !selectAllVal)"
                                           x-effect="$el.indeterminate = ($wire.selectedItems.length > 0 && !$wire.selectAll)">
                                    <button
                                        class="table-sort {{ $sortField === 'favorite_id' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                        wire:click="sortBy('favorite_id')">
                                    </button>
                                </div>
                            </th>
                            <th>
                                <button
                                    class="table-sort {{ $sortField === 'title' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('title')">
                                    {{ __('favorite::admin.title_field') }}
                                </button>
                            </th>
                            <th class="text-center" style="width: 80px" data-bs-toggle="tooltip" data-bs-placement="top"
                                title="{{ __('favorite::admin.status') }}">
                                <button
                                    class="table-sort {{ $sortField === 'is_active' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('is_active')">
                                    {{ __('favorite::admin.status') }}
                                </button>
                            </th>
                            <th class="text-center" style="width: 160px">{{ __('admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="table-tbody">
                        @forelse($favorites as $favorite)
                            <tr class="hover-trigger" wire:key="row-{{ $favorite->favorite_id }}">
                                <td class="sort-id small">
                                    <div class="hover-toggle">
                                        <span class="hover-hide">{{ $favorite->favorite_id }}</span>
                                        <input type="checkbox"
                                               wire:model.live="selectedItems"
                                               value="{{ $favorite->favorite_id }}"
                                               class="form-check-input hover-show"
                                               id="checkbox-{{ $favorite->favorite_id }}"
                                               @checked(in_array($favorite->favorite_id, $selectedItems))>
                                    </div>
                                </td>
                                <td wire:key="title-{{ $favorite->favorite_id }}" class="position-relative">
                                    @if ($editingTitleId === $favorite->favorite_id)
                                        <div class="d-flex align-items-center gap-3" x-data
                                            @click.outside="$wire.updateTitleInline()">
                                            <div class="flexible-input-wrapper">
                                                <input type="text" wire:model.defer="newTitle"
                                                    class="form-control form-control-sm flexible-input"
                                                    placeholder="{{ __('favorite::admin.title_field') }}"
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
                                                class="editable-title pr-4">{{ $favorite->getTranslated('title', $currentSiteLocale) ?? $favorite->getTranslated('title', 'tr') }}</span>
                                            <button class="btn btn-sm px-2 py-1 edit-icon ms-4"
                                                wire:click="startEditingTitle({{ $favorite->favorite_id }}, '{{ addslashes($favorite->getTranslated('title', $currentSiteLocale) ?? $favorite->getTranslated('title', 'tr')) }}')">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center align-middle">
                                    <button wire:click="toggleActive({{ $favorite->favorite_id }})"
                                        class="btn btn-icon btn-sm {{ $favorite->is_active ? 'text-muted bg-transparent' : 'text-red bg-transparent' }}">
                                        <!-- Loading Durumu -->
                                        <div wire:loading wire:target="toggleActive({{ $favorite->favorite_id }})"
                                            class="spinner-border spinner-border-sm">
                                        </div>
                                        <!-- Normal Durum: Aktif/Pasif İkonları -->
                                        <div wire:loading.remove
                                            wire:target="toggleActive({{ $favorite->favorite_id }})">
                                            @if ($favorite->is_active)
                                                <i class="fas fa-check"></i>
                                            @else
                                                <i class="fas fa-times"></i>
                                            @endif
                                        </div>
                                    </button>
                                </td>
                                <td class="text-center align-middle">
                                    <div class="d-flex align-items-center gap-3 justify-content-center">
                                        <a href="{{ route('admin.favorite.manage', $favorite->favorite_id) }}"
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="{{ __('admin.edit') }}"
                                            style="min-height: 24px; display: inline-flex; align-items: center; text-decoration: none;">
                                            <i class="fa-solid fa-pen-to-square link-secondary fa-lg"></i>
                                        </a>
                                        @if(config('favorite.integrations.studio.enabled', false) && class_exists(config('favorite.integrations.studio.component', '')))
                                        <a href="{{ route('admin.studio.editor', ['module' => 'favorite', 'id' => $favorite->favorite_id]) }}"
                                            target="_blank" data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="{{ __('favorite::admin.studio.editor') }}"
                                            style="min-height: 24px; display: inline-flex; align-items: center; text-decoration: none;">
                                            <i class="fa-solid fa-wand-magic-sparkles link-secondary fa-lg"></i>
                                        </a>
                                        @endif
                                        <x-ai-translation :entity-type="'favorite'" :entity-id="$favorite->favorite_id"
                                            tooltip="{{ __('admin.ai_translate') }}" />
                                        @hasmoduleaccess('favorite', 'delete')
                                        <div class="dropdown">
                                            <a class="dropdown-toggle text-secondary" href="#" data-bs-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false"
                                                style="min-height: 24px; display: inline-flex; align-items: center; text-decoration: none;">
                                                <i class="fa-solid fa-bars-sort fa-flip-horizontal fa-lg"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a href="javascript:void(0);"
                                                    wire:click="$dispatch('showDeleteModal', {
                                                    module: 'favorite',
                                                    id: {{ $favorite->favorite_id }},
                                                    title: '{{ addslashes($favorite->getTranslated('title', app()->getLocale()) ?? $favorite->getTranslated('title', 'tr')) }}'
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
                                <td colspan="4" class="text-center py-4">
                                    <div class="empty">
                                        <p class="empty-title">{{ __('favorite::admin.no_favorites_found') }}</p>
                                        <p class="empty-subtitle text-muted">
                                            {{ __('favorite::admin.no_results') }}
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
            @if ($favorites->hasPages())
                {{ $favorites->links() }}
            @else
                <div class="d-flex justify-content-between align-items-center mb-0">
                    <p class="small text-muted mb-0">
                        Toplam <span class="fw-semibold">{{ $favorites->total() }}</span> sonuç
                    </p>
                </div>
            @endif
        </div>

        <!-- Bulk Actions -->
        @include('favorite::admin.partials.bulk-actions', ['moduleType' => 'favorite'])

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
