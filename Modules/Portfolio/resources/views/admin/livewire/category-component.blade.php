@php
    View::share('pretitle', __('portfolio::admin.category_management'));
@endphp

<div wire:id="{{ $this->getId() }}" class="category-component-wrapper">
    <div class="card">
        @include('portfolio::admin.helper-category')
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
                            placeholder="{{ __('portfolio::admin.search_categories') }}">
                    </div>
                </div>
                <!-- Ortadaki Loading -->
                <div class="col position-relative">
                    <div wire:loading
                        wire:target="render, search, perPage, sortBy, gotoPage, previousPage, nextPage, toggleActive"
                        class="position-absolute top-50 start-50 translate-middle text-center"
                        style="width: 100%; max-width: 250px;">
                        <div class="small text-muted mb-2">{{ __('admin.updating') }}</div>
                        <div class="progress mb-1">
                            <div class="progress-bar progress-bar-indeterminate"></div>
                        </div>
                    </div>
                </div>
                <!-- Sağ Taraf (Sayfa Adeti) -->
                <div class="col">
                    <div class="d-flex align-items-center justify-content-end gap-3">
                        <!-- Sayfa Adeti Seçimi -->
                        <div style="width: 80px; min-width: 80px">
                            <select wire:model.live="perPage" class="form-control listing-filter-select"
                                    data-choices
                                    data-choices-search="false"
                                    data-choices-filter="true">
                                <option value="10"><nobr>10</nobr></option>
                                <option value="15"><nobr>15</nobr></option>
                                <option value="50"><nobr>50</nobr></option>
                                <option value="100"><nobr>100</nobr></option>
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
                                    <button
                                        class="table-sort {{ $sortField === 'category_id' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                        wire:click="sortBy('category_id')">
                                    </button>
                                </div>
                            </th>
                            <th>
                                <button
                                    class="table-sort {{ $sortField === 'name' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('name')">
                                    {{ __('portfolio::admin.category_name') }}
                                </button>
                            </th>
                            <th>
                                <button
                                    class="table-sort {{ $sortField === 'slug' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('slug')">
                                    {{ __('portfolio::admin.slug') }}
                                </button>
                            </th>
                            <th class="text-center" style="width: 100px">
                                <button
                                    class="table-sort {{ $sortField === 'sort_order' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('sort_order')">
                                    {{ __('portfolio::admin.sort_order') }}
                                </button>
                            </th>
                            <th class="text-center" style="width: 80px">
                                <button
                                    class="table-sort {{ $sortField === 'is_active' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('is_active')">
                                    {{ __('admin.status') }}
                                </button>
                            </th>
                            <th class="text-center" style="width: 160px">{{ __('admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="table-tbody">
                        @forelse($categories as $category)
                        <tr class="hover-trigger" wire:key="row-{{ $category->category_id }}">
                            <td class="sort-id small">
                                <span>{{ $category->category_id }}</span>
                            </td>
                            <td wire:key="name-{{ $category->category_id }}" class="position-relative">
                                @if($editingTitleId === $category->category_id)
                                <div class="d-flex align-items-center gap-3" x-data
                                    @click.outside="$wire.updateTitleInline()">
                                    <div class="flexible-input-wrapper">
                                        <input type="text" wire:model.defer="newTitle"
                                            class="form-control form-control-sm flexible-input"
                                            placeholder="{{ __('portfolio::admin.category_name') }}"
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
                                    <button class="btn px-2 py-1 btn-outline-success" wire:click="updateTitleInline">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn px-2 py-1 btn-outline-danger"
                                        wire:click="$set('editingTitleId', null)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                @else
                                <div class="d-flex align-items-center">
                                    <span class="editable-title pr-4">{{ $category->getTranslated('name', $currentSiteLocale) ?? $category->getTranslated('name', 'tr') }}</span>
                                    <button class="btn btn-sm px-2 py-1 edit-icon ms-4"
                                        wire:click="startEditingTitle({{ $category->category_id }}, '{{ addslashes($category->getTranslated('name', $currentSiteLocale) ?? $category->getTranslated('name', 'tr')) }}')">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                </div>
                                @endif
                            </td>
                            <td>
                                <span class="text-muted">{{ $category->getTranslated('slug', $currentSiteLocale) ?? $category->getTranslated('slug', 'tr') }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-outline text-secondary">{{ $category->sort_order }}</span>
                            </td>
                            <td wire:key="status-{{ $category->category_id }}" class="text-center align-middle">
                                <button wire:click="toggleActive({{ $category->category_id }})"
                                    class="btn btn-icon btn-sm {{ $category->is_active ? 'text-muted bg-transparent' : 'text-red bg-transparent' }}">
                                    <!-- Loading Durumu -->
                                    <div wire:loading wire:target="toggleActive({{ $category->category_id }})"
                                        class="spinner-border spinner-border-sm">
                                    </div>
                                    <!-- Normal Durum -->
                                    <div wire:loading.remove wire:target="toggleActive({{ $category->category_id }})">
                                        @if($category->is_active)
                                        <i class="fas fa-check"></i>
                                        @else
                                        <i class="fas fa-times"></i>
                                        @endif
                                    </div>
                                </button>
                            </td>
                            <td class="text-center align-middle">
                                <div class="d-flex align-items-center gap-3 justify-content-center">
                                    <a href="{{ route('admin.portfolio.category.manage', $category->category_id) }}"
                                       data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('admin.edit') }}"
                                       style="min-height: 24px; display: inline-flex; align-items: center; text-decoration: none;">
                                        <i class="fa-solid fa-pen-to-square link-secondary fa-lg"></i>
                                    </a>
                                    @hasmoduleaccess('portfolio', 'delete')
                                    <div class="dropdown">
                                        <a class="dropdown-toggle text-secondary" href="#" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false"
                                            style="min-height: 24px; display: inline-flex; align-items: center; text-decoration: none;">
                                            <i class="fa-solid fa-bars-sort fa-flip-horizontal fa-lg"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a href="javascript:void(0);" wire:click="$dispatch('showDeleteModal', {
                                                module: 'portfolio_category',
                                                id: {{ $category->category_id }},
                                                title: '{{ addslashes($category->getTranslated('name', app()->getLocale()) ?? $category->getTranslated('name', 'tr')) }}'
                                            })" class="dropdown-item link-danger">
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
                            <td colspan="6" class="text-center py-4">
                                <div class="empty">
                                    <p class="empty-title">{{ __('portfolio::admin.no_categories_found') }}</p>
                                    <p class="empty-subtitle text-muted">
                                        {{ __('portfolio::admin.no_results') }}
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
            @if($categories->hasPages())
                {{ $categories->links() }}
            @else
                <div class="d-flex justify-content-between align-items-center mb-0">
                    <p class="small text-muted mb-0">
                        Toplam <span class="fw-semibold">{{ $categories->total() }}</span> sonuç
                    </p>
                </div>
            @endif
        </div>

        <livewire:modals.delete-modal />
    </div>
</div>
