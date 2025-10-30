@include('shop::admin.helper')

@php
    View::share('pretitle', 'Shopping Carts');
@endphp

<div class="card">
    <div class="card-header d-flex flex-column flex-md-row gap-3 justify-content-between align-items-md-center">
        <div class="row g-2 flex-grow-1">
            <div class="col-12 col-md-6">
                <input type="text"
                       wire:model.live.debounce.400ms="search"
                       class="form-control"
                       placeholder="Search by cart ID, session ID, IP...">
            </div>
            <div class="col-12 col-md-3">
                <select wire:model.live="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="abandoned">Abandoned</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button class="btn btn-outline-warning"
                    wire:click="cleanOldCarts"
                    wire:confirm="Clean all abandoned carts older than 30 days?">
                <i class="fas fa-broom"></i> Clean Old
            </button>
            <button class="btn btn-outline-danger"
                    wire:click="bulkDeleteSelected"
                    @disabled(empty($selectedItems))>
                <i class="fas fa-trash"></i> Delete Selected
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-vcenter">
            <thead>
                <tr>
                    <th style="width: 40px;">
                        <input type="checkbox" wire:model.live="selectAll" class="form-check-input">
                    </th>
                    <th wire:click="sortBy('cart_id')" style="cursor: pointer;">
                        Cart ID
                        @if($sortField === 'cart_id')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th>Session</th>
                    <th>Items</th>
                    <th wire:click="sortBy('total')" style="cursor: pointer;">
                        Total
                        @if($sortField === 'total')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th>Currency</th>
                    <th>Status</th>
                    <th wire:click="sortBy('created_at')" style="cursor: pointer;">
                        Created
                        @if($sortField === 'created_at')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th wire:click="sortBy('last_activity_at')" style="cursor: pointer;">
                        Last Activity
                        @if($sortField === 'last_activity_at')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($carts as $cart)
                    <tr wire:key="cart-{{ $cart->cart_id }}">
                        <td>
                            <input type="checkbox"
                                   class="form-check-input"
                                   wire:model.live="selectedItems"
                                   value="{{ $cart->cart_id }}">
                        </td>
                        <td>
                            <span class="badge bg-azure-lt fs-5">#{{ $cart->cart_id }}</span>
                        </td>
                        <td>
                            <div class="small text-muted">{{ Str::limit($cart->session_id, 20) }}</div>
                            @if($cart->ip_address)
                                <div class="small text-muted">{{ $cart->ip_address }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-blue-lt">{{ $cart->items_count }} items</span>
                        </td>
                        <td>
                            @if($cart->currency)
                                {{ $cart->currency->formatPrice($cart->total) }}
                            @else
                                {{ number_format($cart->total, 2) }} {{ $cart->currency ?? 'TRY' }}
                            @endif
                        </td>
                        <td>
                            @if($cart->currency)
                                <span class="badge bg-secondary-lt">{{ $cart->currency->code }}</span>
                            @else
                                <span class="badge bg-secondary-lt">{{ $cart->currency ?? 'TRY' }}</span>
                            @endif
                        </td>
                        <td>
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
                        <td>
                            <span class="small text-muted">{{ $cart->created_at->diffForHumans() }}</span>
                        </td>
                        <td>
                            @if($cart->last_activity_at)
                                <span class="small text-muted">{{ $cart->last_activity_at->diffForHumans() }}</span>
                            @else
                                <span class="small text-muted">Never</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="btn-list justify-content-end">
                                @if($cart->status === 'active')
                                    <button class="btn btn-outline-warning btn-icon btn-sm"
                                            wire:click="markAsAbandoned({{ $cart->cart_id }})"
                                            wire:confirm="Mark this cart as abandoned?"
                                            title="Mark as Abandoned">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </button>
                                @endif
                                <button class="btn btn-outline-danger btn-icon btn-sm"
                                        wire:click="deleteCart({{ $cart->cart_id }})"
                                        wire:confirm="Delete this cart?"
                                        title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">
                            No carts found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($carts->hasPages())
        <div class="card-footer">
            {{ $carts->links() }}
        </div>
    @endif
</div>
