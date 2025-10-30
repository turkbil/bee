@php
    View::share('pretitle', __('shop::admin.cart_management'));
@endphp

<div class="cart-component-wrapper">
    <div class="card">
        @include('shop::admin.helper')
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
                            placeholder="{{ __('shop::admin.search_carts') }}">
                    </div>
                </div>
                <!-- Ortadaki Loading -->
                <div class="col position-relative">
                    <div wire:loading
                        wire:target="render, search, perPage, sortBy, status, gotoPage, previousPage, nextPage, deleteCart, markAsAbandoned, selectedItems, selectAll, bulkDeleteSelected, cleanOldCarts"
                        class="position-absolute top-50 start-50 translate-middle text-center"
                        style="width: 100%; max-width: 250px;">
                        <div class="small text-muted mb-2">{{ __('admin.updating') }}</div>
                        <div class="progress mb-1">
                            <div class="progress-bar progress-bar-indeterminate"></div>
                        </div>
                    </div>
                </div>
                <!-- Sağ Taraf (Status Filter ve Select) -->
                <div class="col">
                    <div class="d-flex align-items-center justify-content-end gap-3">
                        <!-- Status Filter -->
                        <div style="width: 140px; min-width: 140px">
                            <select wire:model.live="status" class="form-control listing-filter-select" data-choices
                                data-choices-search="false" data-choices-filter="true">
                                <option value="">{{ __('shop::admin.all_status') }}</option>
                                <option value="active">{{ __('shop::admin.status_active') }}</option>
                                <option value="abandoned">{{ __('shop::admin.status_abandoned') }}</option>
                                <option value="completed">{{ __('shop::admin.status_completed') }}</option>
                            </select>
                        </div>
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
                                               indeterminate: {{ count($selectedItems) > 0 && !$selectAll ? 'true' : 'false' }}
                                           }"
                                           x-init="$el.indeterminate = indeterminate"
                                           x-effect="$el.indeterminate = ({{ count($selectedItems) }} > 0 && !{{ $selectAll ? 'true' : 'false' }})"
                                           @checked($selectAll)>
                                    <button
                                        class="table-sort {{ $sortField === 'cart_id' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                        wire:click="sortBy('cart_id')">
                                    </button>
                                </div>
                            </th>
                            <th>
                                <button
                                    class="table-sort {{ $sortField === 'session_id' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('session_id')">
                                    {{ __('shop::admin.session_info') }}
                                </button>
                            </th>
                            <th class="text-center" style="width: 80px">{{ __('shop::admin.items_count') }}</th>
                            <th>
                                <button
                                    class="table-sort {{ $sortField === 'total' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('total')">
                                    {{ __('shop::admin.cart_total') }}
                                </button>
                            </th>
                            <th class="text-center" style="width: 100px">
                                <button
                                    class="table-sort {{ $sortField === 'status' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('status')">
                                    {{ __('shop::admin.cart_status') }}
                                </button>
                            </th>
                            <th class="text-center" style="width: 120px">
                                <button
                                    class="table-sort {{ $sortField === 'created_at' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('created_at')">
                                    {{ __('shop::admin.cart_created') }}
                                </button>
                            </th>
                            <th class="text-center" style="width: 120px">
                                <button
                                    class="table-sort {{ $sortField === 'last_activity_at' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('last_activity_at')">
                                    {{ __('shop::admin.last_activity') }}
                                </button>
                            </th>
                            <th class="text-center" style="width: 120px">{{ __('admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="table-tbody">
                        @forelse($carts as $cart)
                            <tr class="hover-trigger" wire:key="row-{{ $cart->cart_id }}">
                                <td class="sort-id small">
                                    <div class="hover-toggle">
                                        <span class="hover-hide">{{ $cart->cart_id }}</span>
                                        <input type="checkbox"
                                               wire:model.live="selectedItems"
                                               value="{{ $cart->cart_id }}"
                                               class="form-check-input hover-show"
                                               id="checkbox-{{ $cart->cart_id }}"
                                               @checked(in_array($cart->cart_id, $selectedItems))>
                                    </div>
                                </td>
                                <td>
                                    <div class="small text-muted">{{ Str::limit($cart->session_id, 20) }}</div>
                                    @if($cart->ip_address)
                                        <div class="small text-secondary">{{ $cart->ip_address }}</div>
                                    @endif
                                </td>
                                <td class="text-center align-middle">
                                    <span class="badge bg-blue-lt">{{ $cart->items_count }}</span>
                                </td>
                                <td class="align-middle">
                                    @if($cart->currency)
                                        <span class="fw-semibold">{{ $cart->currency->formatPrice($cart->total) }}</span>
                                    @else
                                        <span class="fw-semibold">{{ number_format($cart->total, 2) }} {{ $cart->currency_code ?? 'TRY' }}</span>
                                    @endif
                                </td>
                                <td class="text-center align-middle">
                                    @php
                                        $statusColors = [
                                            'active' => 'success',
                                            'abandoned' => 'warning',
                                            'completed' => 'info',
                                        ];
                                        $color = $statusColors[$cart->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $color }}">
                                        {{ ucfirst($cart->status) }}
                                    </span>
                                </td>
                                <td class="text-center align-middle">
                                    <span class="small text-muted">{{ $cart->created_at->diffForHumans() }}</span>
                                </td>
                                <td class="text-center align-middle">
                                    @if($cart->last_activity_at)
                                        <span class="small text-muted">{{ $cart->last_activity_at->diffForHumans() }}</span>
                                    @else
                                        <span class="small text-muted">{{ __('shop::admin.never') }}</span>
                                    @endif
                                </td>
                                <td class="text-center align-middle">
                                    <div class="d-flex align-items-center gap-3 justify-content-center">
                                        @if($cart->status === 'active')
                                            <a href="javascript:void(0);"
                                                wire:click="markAsAbandoned({{ $cart->cart_id }})"
                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                data-bs-html="true"
                                                title="{{ __('shop::admin.mark_as_abandoned') }}<br><small>{{ __('shop::admin.mark_as_abandoned_desc') }}</small>"
                                                style="min-height: 24px; display: inline-flex; align-items: center; text-decoration: none;">
                                                <i class="fa-solid fa-exclamation-triangle link-warning fa-lg"></i>
                                            </a>
                                        @endif
                                        <div class="dropdown">
                                            <a class="dropdown-toggle text-secondary" href="#" data-bs-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false"
                                                style="min-height: 24px; display: inline-flex; align-items: center; text-decoration: none;">
                                                <i class="fa-solid fa-bars-sort fa-flip-horizontal fa-lg"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a href="javascript:void(0);"
                                                    wire:click="deleteCart({{ $cart->cart_id }})"
                                                    wire:confirm="{{ __('shop::admin.delete_cart_confirm') }}"
                                                    class="dropdown-item link-danger">
                                                    <i class="fas fa-trash me-2"></i> {{ __('shop::admin.delete_cart') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="empty">
                                        <p class="empty-title">{{ __('shop::admin.no_carts_found') }}</p>
                                        <p class="empty-subtitle text-muted">
                                            {{ __('shop::admin.no_carts_desc') }}
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
            @if ($carts->hasPages())
                {{ $carts->links() }}
            @else
                <div class="d-flex justify-content-between align-items-center mb-0">
                    <p class="small text-muted mb-0">
                        Toplam <span class="fw-semibold">{{ $carts->total() }}</span> sonuç
                    </p>
                </div>
            @endif
        </div>

        <!-- Bulk Actions -->
        @if(count($selectedItems) > 0)
            <div class="position-fixed bottom-0 start-50 translate-middle-x mb-4" style="z-index: 1050;">
                <div class="card shadow-lg border-0" style="min-width: 400px;">
                    <div class="card-body py-3 px-4">
                        <div class="d-flex align-items-center justify-content-between gap-3">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-blue-lt fs-5">{{ count($selectedItems) }}</span>
                                <span class="text-muted">{{ __('shop::admin.carts_selected', ['count' => count($selectedItems)]) }}</span>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-warning btn-sm"
                                        wire:click="cleanOldCarts"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="{{ __('shop::admin.clean_old_carts_desc') }}"
                                        wire:confirm="{{ __('shop::admin.clean_old_carts_confirm') }}">
                                    <i class="fas fa-broom"></i> {{ __('shop::admin.clean_old_carts') }}
                                </button>
                                <button class="btn btn-outline-danger btn-sm"
                                        wire:click="bulkDeleteSelected"
                                        wire:confirm="{{ __('shop::admin.delete_cart_confirm') }}">
                                    <i class="fas fa-trash"></i> {{ __('shop::admin.delete_selected_carts') }}
                                </button>
                                <button class="btn btn-ghost-secondary btn-sm"
                                        wire:click="$set('selectedItems', [])">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>
