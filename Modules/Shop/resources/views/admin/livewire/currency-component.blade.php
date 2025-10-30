@include('shop::admin.helper')

@php
    View::share('pretitle', 'Currencies');
@endphp

<div class="card">
    <div class="card-header d-flex flex-column flex-md-row gap-3 justify-content-between align-items-md-center">
        <input type="text"
               wire:model.debounce.400ms="search"
               class="form-control"
               placeholder="Search currencies...">

        <div class="d-flex gap-2">
            <button class="btn btn-outline-danger"
                    wire:click="bulkDeleteSelected"
                    @disabled(empty($selectedItems))>
                <i class="fas fa-trash"></i> Delete Selected
            </button>
            <a href="{{ route('admin.shop.currencies.manage') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> New Currency
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
                    <th wire:click="sortBy('code')" style="cursor: pointer;">
                        Code
                        @if($sortField === 'code')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th>Symbol</th>
                    <th>Name</th>
                    <th wire:click="sortBy('exchange_rate')" style="cursor: pointer;">
                        Exchange Rate
                        @if($sortField === 'exchange_rate')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th class="text-center">Default</th>
                    <th class="text-center">Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($currencies as $currency)
                    <tr wire:key="currency-{{ $currency->currency_id }}">
                        <td>
                            <input type="checkbox"
                                   class="form-check-input"
                                   wire:model.live="selectedItems"
                                   value="{{ $currency->currency_id }}"
                                   @disabled($currency->is_default)>
                        </td>
                        <td>
                            <span class="badge bg-azure-lt fs-5">{{ $currency->code }}</span>
                        </td>
                        <td>
                            <span class="fs-4">{{ $currency->symbol }}</span>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $currency->name }}</div>
                            @if($currency->name_translations)
                                <div class="text-muted small">
                                    {{ $currency->getTranslatedName('tr') }}
                                </div>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-secondary-lt">1 {{ $currency->code }} = {{ number_format($currency->exchange_rate, 4) }} TRY</span>
                        </td>
                        <td class="text-center">
                            @if($currency->is_default)
                                <span class="badge bg-success">
                                    <i class="fas fa-star"></i> Default
                                </span>
                            @else
                                <button class="btn btn-sm btn-outline-success"
                                        wire:click="setAsDefault({{ $currency->currency_id }})"
                                        wire:confirm="Set {{ $currency->code }} as default currency?">
                                    Set Default
                                </button>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $currency->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $currency->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="btn-list justify-content-end">
                                <button class="btn btn-outline-secondary btn-icon"
                                        wire:click="toggleActive({{ $currency->currency_id }})"
                                        title="Toggle Status">
                                    <i class="fas fa-sync"></i>
                                </button>
                                <a href="{{ route('admin.shop.currencies.manage', $currency->currency_id) }}"
                                   class="btn btn-outline-primary btn-icon"
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            No currencies found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($currencies->hasPages())
        <div class="card-footer">
            {{ $currencies->links() }}
        </div>
    @endif
</div>
