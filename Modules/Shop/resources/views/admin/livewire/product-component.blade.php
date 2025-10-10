@php
    View::share('pretitle', __('shop::admin.products'));
@endphp

@include('shop::admin.helper')

<div class="card">
    <div class="card-header d-flex flex-column flex-md-row gap-3 align-items-start align-items-md-center justify-content-between">
        <div class="d-flex gap-2">
            <input type="text"
                   wire:model.debounce.400ms="search"
                   class="form-control"
                   placeholder="{{ __('shop::admin.search_products') }}">

            <select wire:model="perPage" class="form-select" style="width: 120px;">
                @foreach ([10, 25, 50, 100] as $size)
                    <option value="{{ $size }}">{{ $size }}</option>
                @endforeach
            </select>
        </div>

        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary"
                    wire:click="bulkToggleSelected(true)"
                    @disabled(empty($selectedItems))>
                <i class="ti ti-player-play"></i>
                {{ __('shop::admin.activate_selected') }}
            </button>
            <button class="btn btn-outline-warning"
                    wire:click="bulkToggleSelected(false)"
                    @disabled(empty($selectedItems))>
                <i class="ti ti-player-pause"></i>
                {{ __('shop::admin.deactivate_selected') }}
            </button>
            <button class="btn btn-outline-danger"
                    wire:click="bulkDeleteSelected"
                    @disabled(empty($selectedItems))>
                <i class="ti ti-trash"></i>
                {{ __('shop::admin.delete_selected') }}
            </button>
            <a href="{{ route('admin.shop.products.create') }}" class="btn btn-primary">
                <i class="ti ti-plus"></i> {{ __('shop::admin.new_product') }}
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-vcenter">
            <thead>
                <tr>
                    <th style="width: 40px;">
                        <input type="checkbox" wire:model.live="selectAll" class="form-check-input">
                    </th>
                    <th>
                        <a href="#" wire:click.prevent="sortBy('title')" class="table-sort {{ $sortField === 'title' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}">
                            {{ __('shop::admin.product_title') }}
                        </a>
                    </th>
                    <th>{{ __('shop::admin.category') }}</th>
                    <th>{{ __('shop::admin.brand') }}</th>
                    <th class="text-end">{{ __('shop::admin.price') }}</th>
                    <th class="text-center">{{ __('shop::admin.status') }}</th>
                    <th class="text-end">{{ __('admin.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr wire:key="product-{{ $product->product_id }}">
                        <td>
                            <input type="checkbox"
                                   class="form-check-input"
                                   wire:model.live="selectedItems"
                                   value="{{ $product->product_id }}">
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $product->getTranslated('title', $currentSiteLocale) ?? \Illuminate\Support\Arr::first($product->title) }}</div>
                            <div class="text-muted small">{{ $product->sku }}</div>
                        </td>
                        <td>
                            {{ optional($product->category)->getTranslated('title', $currentSiteLocale) ?? '—' }}
                        </td>
                        <td>
                            {{ optional($product->brand)->getTranslated('title', $currentSiteLocale) ?? '—' }}
                        </td>
                        <td class="text-end">
                            @if ($product->price_on_request)
                                <span class="badge bg-secondary">{{ __('shop::admin.price_on_request') }}</span>
                            @elseif ($product->base_price)
                                <span class="fw-semibold">{{ number_format((float) $product->base_price, 2) }} {{ $product->currency }}</span>
                                @if ($product->compare_at_price)
                                    <div class="text-muted small">
                                        <s>{{ number_format((float) $product->compare_at_price, 2) }} {{ $product->currency }}</s>
                                    </div>
                                @endif
                            @else
                                —
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $product->is_active ? __('shop::admin.active') : __('shop::admin.inactive') }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="btn-list justify-content-end">
                                <button class="btn btn-outline-secondary btn-icon"
                                        wire:click="toggleActive({{ $product->product_id }})"
                                        title="{{ __('shop::admin.toggle_status') }}">
                                    <i class="ti ti-refresh"></i>
                                </button>
                                <a href="{{ route('admin.shop.products.edit', $product->product_id) }}"
                                   class="btn btn-outline-primary btn-icon"
                                   title="{{ __('admin.edit') }}">
                                    <i class="ti ti-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            {{ __('shop::admin.no_products_found') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-footer d-flex justify-content-between align-items-center">
        <div class="text-muted">
            {{ trans_choice('shop::admin.products_count', $products->total(), ['count' => $products->total()]) }}
        </div>
        <div>
            {{ $products->onEachSide(1)->links() }}
        </div>
    </div>
</div>
