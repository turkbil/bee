@include('shop::admin.helper')

@php
    View::share('pretitle', __('shop::admin.brands'));
@endphp

<div class="card">
    <div class="card-header d-flex flex-column flex-md-row gap-3 justify-content-between align-items-md-center">
        <input type="text"
               wire:model.debounce.400ms="search"
               class="form-control"
               placeholder="{{ __('shop::admin.search_brands') }}">

        <div class="d-flex gap-2">
            <button class="btn btn-outline-danger"
                    wire:click="bulkDeleteSelected"
                    @disabled(empty($selectedItems))>
                <i class="ti ti-trash"></i> {{ __('shop::admin.delete_selected') }}
            </button>
            <a href="{{ route('admin.shop.brands.manage') }}" class="btn btn-primary">
                <i class="ti ti-plus"></i> {{ __('shop::admin.new_brand') }}
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
                    <th>{{ __('shop::admin.brand_name') }}</th>
                    <th>{{ __('shop::admin.country') }}</th>
                    <th class="text-center">{{ __('shop::admin.status') }}</th>
                    <th class="text-end">{{ __('admin.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($brands as $brand)
                    <tr wire:key="brand-{{ $brand->brand_id }}">
                        <td>
                            <input type="checkbox"
                                   class="form-check-input"
                                   wire:model.live="selectedItems"
                                   value="{{ $brand->brand_id }}">
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $brand->getTranslated('title', app()->getLocale()) ?? \Illuminate\Support\Arr::first($brand->title) }}</div>
                            <div class="text-muted small">{{ $brand->website_url }}</div>
                        </td>
                        <td>{{ $brand->country_code ?? '—' }}</td>
                        <td class="text-center">
                            <span class="badge {{ $brand->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $brand->is_active ? __('shop::admin.active') : __('shop::admin.inactive') }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="btn-list justify-content-end">
                                <button class="btn btn-outline-secondary btn-icon"
                                        wire:click="toggleActive({{ $brand->brand_id }})">
                                    <i class="ti ti-refresh"></i>
                                </button>
                                <a href="{{ route('admin.shop.brands.manage', $brand->brand_id) }}"
                                   class="btn btn-outline-primary btn-icon">
                                    <i class="ti ti-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            {{ __('shop::admin.no_brands_found') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-footer d-flex justify-content-between align-items-center">
        <div class="text-muted">
            {{ trans_choice('shop::admin.brands_count', $brands->total(), ['count' => $brands->total()]) }}
        </div>
        <div>{{ $brands->onEachSide(1)->links() }}</div>
    </div>
</div>
