@include('cart::admin.helper')

<div>
    <div class="card">
        <!-- Card Header -->
        <div class="card-header">
            <div class="row w-100 align-items-center">
                <div class="col-auto">
                    <div class="input-icon">
                        <span class="input-icon-addon"><i class="fas fa-search"></i></span>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control form-control-sm"
                            placeholder="{{ __('cart::admin.search') }}..." style="width: 200px;">
                    </div>
                </div>
                <div class="col-auto">
                    <select wire:model.live="status" class="form-select form-select-sm" style="width: 140px;">
                        <option value="">{{ __('cart::admin.all_statuses') }}</option>
                        @foreach($statuses as $statusOption)
                            <option value="{{ $statusOption }}">{{ __('cart::admin.status_' . $statusOption) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <label class="form-check form-check-inline mb-0">
                        <input class="form-check-input" type="checkbox" wire:model.live="includeGuests">
                        <span class="form-check-label">{{ __('cart::admin.include_guests') }}</span>
                    </label>
                    <label class="form-check form-check-inline mb-0">
                        <input class="form-check-input" type="checkbox" wire:model.live="includeEmpty">
                        <span class="form-check-label">{{ __('cart::admin.include_empty') }}</span>
                    </label>
                </div>
                <div class="col-auto ms-auto">
                    <div wire:loading class="spinner-border spinner-border-sm text-primary"></div>
                </div>
                <div class="col-auto">
                    <select wire:model.live="perPage" class="form-select form-select-sm" style="width: 70px;">
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Table -->
        @if($carts->isEmpty())
            <div class="card-body">
                <div class="empty">
                    <div class="empty-icon"><i class="fas fa-shopping-cart" style="font-size: 3rem; opacity: 0.2;"></i></div>
                    <p class="empty-title">{{ __('cart::admin.no_carts_found') }}</p>
                </div>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-hover">
                    <thead>
                        <tr>
                            <th style="width: 80px;">ID</th>
                            <th>{{ __('cart::admin.customer') }}</th>
                            <th class="text-center" style="width: 80px;">{{ __('cart::admin.product') }}</th>
                            <th class="text-end" style="width: 120px;">{{ __('cart::admin.total') }}</th>
                            <th class="text-center" style="width: 100px;">{{ __('cart::admin.status') }}</th>
                            <th style="width: 140px;">{{ __('cart::admin.last_activity') }}</th>
                            <th class="text-center" style="width: 50px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($carts as $cart)
                            <tr wire:key="cart-{{ $cart->cart_id }}">
                                <td><span class="text-reset font-monospace">#{{ $cart->cart_id }}</span></td>
                                <td>
                                    @if($cart->customer_id && $cart->customer)
                                        <div class="d-flex align-items-center">
                                            <span class="avatar avatar-sm bg-primary-lt me-2">{{ strtoupper(substr($cart->customer->name, 0, 2)) }}</span>
                                            <div class="flex-fill">
                                                <div class="font-weight-medium">{{ Str::limit($cart->customer->name, 18) }}</div>
                                                <div class="text-secondary small">{{ Str::limit($cart->customer->email, 22) }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="d-flex align-items-center">
                                            <span class="avatar avatar-sm bg-secondary-lt me-2"><i class="fas fa-user-secret"></i></span>
                                            <div class="flex-fill">
                                                <div class="text-secondary">{{ __('cart::admin.guest') }}</div>
                                                <div class="text-secondary small font-monospace">{{ Str::limit($cart->session_id, 12) }}</div>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center"><span class="badge bg-azure-lt">{{ $cart->items_count }}</span></td>
                                <td class="text-end font-monospace">{{ number_format($cart->total, 2) }} {{ $cart->currency_code }}</td>
                                <td class="text-center">
                                    @switch($cart->status)
                                        @case('active')<span class="badge bg-success-lt text-success">{{ __('cart::admin.status_active') }}</span>@break
                                        @case('abandoned')<span class="badge bg-warning-lt text-warning">{{ __('cart::admin.status_abandoned') }}</span>@break
                                        @case('converted')<span class="badge bg-info-lt text-info">{{ __('cart::admin.status_converted') }}</span>@break
                                        @case('merged')<span class="badge bg-secondary-lt">{{ __('cart::admin.status_merged') }}</span>@break
                                        @default<span class="badge">{{ $cart->status }}</span>
                                    @endswitch
                                </td>
                                <td>
                                    @if($cart->last_activity_at)
                                        <div class="text-secondary small">{{ $cart->last_activity_at->diffForHumans() }}</div>
                                    @else
                                        <span class="text-secondary">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="#" class="btn btn-ghost-primary btn-icon"
                                       data-bs-toggle="modal" data-bs-target="#cartDetailModal"
                                       onclick="loadCartDetail({{ $cart->cart_id }})">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Footer / Pagination -->
        @if($carts->hasPages())
            <div class="card-footer d-flex align-items-center">
                <p class="m-0 text-secondary">
                    <span>{{ $carts->firstItem() }}</span>-<span>{{ $carts->lastItem() }}</span> / <span>{{ $carts->total() }}</span>
                </p>
                <ul class="pagination m-0 ms-auto">
                    {{ $carts->onEachSide(1)->links() }}
                </ul>
            </div>
        @endif
    </div>

    <!-- Modal -->
    <div class="modal modal-blur fade" id="cartDetailModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-shopping-cart me-2"></i><span id="modalCartTitle">{{ __('cart::admin.cart_detail') }}</span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="cartModalBody">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary"></div>
                        <p class="mt-2 text-secondary">{{ __('cart::admin.loading') }}</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-bs-dismiss="modal">{{ __('cart::admin.close') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function loadCartDetail(cartId) {
    document.getElementById('cartModalBody').innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary"></div><p class="mt-2 text-secondary">{{ __('cart::admin.loading') }}</p></div>';
    fetch(`/admin/cart/${cartId}/detail`)
        .then(r => r.json())
        .then(data => {
            document.getElementById('modalCartTitle').textContent = data.success ? `{{ __('cart::admin.cart_detail') }} #${data.cart.cart_id}` : 'Hata';
            document.getElementById('cartModalBody').innerHTML = data.success ? data.html : '<div class="alert alert-danger">{{ __('cart::admin.no_carts_found') }}</div>';
        })
        .catch(() => { document.getElementById('cartModalBody').innerHTML = '<div class="alert alert-danger">Bir hata oluştu</div>'; });
}
</script>
@endpush
