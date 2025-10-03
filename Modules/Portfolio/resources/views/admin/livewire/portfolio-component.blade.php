@php
    View::share('pretitle', 'Portfolio Listesi');
@endphp

<div wire:id="{{ $this->getId() }}" class="portfolio-component-wrapper">
    <div class="card">
        @include('portfolio::admin.helper')
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
                                        placeholder="{{ __('admin.search_placeholder') }}">
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
                                        <select wire:model.live="perPage" class="form-control listing-filter-select"
                                                data-choices
                                                data-choices-search="false"
                                                data-choices-filter="true">
                                            <option value="10"><nobr>10</nobr></option>
                                            <option value="50"><nobr>50</nobr></option>
                                            <option value="100"><nobr>100</nobr></option>
                                            <option value="500"><nobr>500</nobr></option>
                                            <option value="1000"><nobr>1000</nobr></option>
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
                                <input type="checkbox" wire:model.live="selectAll" class="form-check-input">
                                <button
                                    class="table-sort {{ $sortField === 'portfolio_id' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('portfolio_id')">
                                </button>
                            </div>
                        </th>
                        <th>
                            <button
                                class="table-sort {{ $sortField === 'title' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                wire:click="sortBy('title')">
                                {{ __('admin.title') }}
                            </button>
                        </th>
                        <th class="text-center" style="width: 80px" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('admin.status') }}">
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
                    @forelse($portfolios as $portfolio)
                    <tr class="hover-trigger" wire:key="row-{{ $portfolio->portfolio_id }}">
                        <td class="sort-id small">
                            <div class="hover-toggle">
                                <span class="hover-hide">{{ $portfolio->portfolio_id }}</span>
                                <input type="checkbox" wire:model.live="selectedItems" value="{{ $portfolio->portfolio_id }}"
                                    class="form-check-input hover-show" @if(in_array($portfolio->portfolio_id, $selectedItems))
                                checked @endif>
                            </div>
                        </td>
                        <td wire:key="title-{{ $portfolio->portfolio_id }}" class="position-relative">
                            <div class="d-flex align-items-center">
                                <span class="editable-title pr-4">{{ $portfolio->getTranslated('title', $currentSiteLocale) ?? $portfolio->getTranslated('title', 'tr') }}</span>
                            </div>
                        </td>
                        <td wire:key="status-{{ $portfolio->portfolio_id }}" class="text-center align-middle">
                            <button wire:click="toggleActive({{ $portfolio->portfolio_id }})"
                                class="btn btn-icon btn-sm {{ $portfolio->is_active ? 'text-muted bg-transparent' : 'text-red bg-transparent' }}">
                                <!-- Loading Durumu -->
                                <div wire:loading wire:target="toggleActive({{ $portfolio->portfolio_id }})"
                                    class="spinner-border spinner-border-sm">
                                </div>
                                <!-- Normal Durum: Aktif/Pasif İkonları -->
                                <div wire:loading.remove wire:target="toggleActive({{ $portfolio->portfolio_id }})">
                                    @if($portfolio->is_active)
                                    <i class="fas fa-check"></i>
                                    @else
                                    <i class="fas fa-times"></i>
                                    @endif
                                </div>
                            </button>
                        </td>
                        <td class="text-center align-middle">
                            <div class="d-flex align-items-center gap-3 justify-content-center">
                                <a href="{{ route('admin.portfolio.manage', $portfolio->portfolio_id) }}"
                                   data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('admin.edit') }}"
                                   style="min-height: 24px; display: inline-flex; align-items: center; text-decoration: none;">
                                    <i class="fa-solid fa-pen-to-square link-secondary fa-lg"></i>
                                </a>
                                <x-ai-translation
                                    :entity-type="'portfolio'"
                                    :entity-id="$portfolio->portfolio_id"
                                    tooltip="{{ __('admin.ai_translate') }}" />
                                @hasmoduleaccess('portfolio', 'delete')
                                <div class="dropdown">
                                    <a class="dropdown-toggle text-secondary" href="#" data-bs-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false"
                                        style="min-height: 24px; display: inline-flex; align-items: center; text-decoration: none;">
                                        <i class="fa-solid fa-bars-sort fa-flip-horizontal fa-lg"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a href="javascript:void(0);" wire:click="$dispatch('showDeleteModal', {
                                            module: 'portfolio',
                                            id: {{ $portfolio->portfolio_id }},
                                            title: '{{ addslashes($portfolio->getTranslated('title', app()->getLocale()) ?? $portfolio->getTranslated('title', 'tr')) }}'
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
                        <td colspan="4" class="text-center py-4">
                            <div class="empty">
                                <p class="empty-title">{{ __('admin.no_portfolios_found') }}</p>
                                <p class="empty-subtitle text-muted">
                                    {{ __('admin.no_results') }}
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
            @if($portfolios->hasPages())
                {{ $portfolios->links() }}
            @else
                <div class="d-flex justify-content-between align-items-center mb-0">
                    <p class="small text-muted mb-0">
                        Toplam <span class="fw-semibold">{{ $portfolios->total() }}</span> sonuç
                    </p>
                </div>
            @endif
        </div>

        <!-- Bulk Actions -->
        @include('portfolio::admin.partials.bulk-actions', ['moduleType' => 'portfolio'])

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
