<!-- Sepet Bilgileri -->
<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    @if($cart->customer_id && $cart->customer)
                        <span class="avatar avatar-lg bg-primary-lt me-3">{{ strtoupper(substr($cart->customer->name, 0, 2)) }}</span>
                        <div>
                            <div class="fw-bold">{{ $cart->customer->name }}</div>
                            <div class="text-secondary">{{ $cart->customer->email }}</div>
                            @if($cart->customer->phone)
                                <div class="text-secondary small">{{ $cart->customer->phone }}</div>
                            @endif
                        </div>
                    @else
                        <span class="avatar avatar-lg bg-secondary-lt me-3"><i class="fas fa-user-secret"></i></span>
                        <div>
                            <div class="fw-bold">{{ __('cart::admin.guest') }}</div>
                            <div class="text-secondary small font-monospace">{{ Str::limit($cart->session_id, 24) }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4 border-end">
                        <div class="text-secondary small">{{ __('cart::admin.status') }}</div>
                        <div class="mt-1">
                            @switch($cart->status)
                                @case('active')<span class="badge bg-success">{{ __('cart::admin.status_active') }}</span>@break
                                @case('abandoned')<span class="badge bg-warning">{{ __('cart::admin.status_abandoned') }}</span>@break
                                @case('converted')<span class="badge bg-info">{{ __('cart::admin.status_converted') }}</span>@break
                                @case('merged')<span class="badge bg-dark">{{ __('cart::admin.status_merged') }}</span>@break
                            @endswitch
                        </div>
                    </div>
                    <div class="col-4 border-end">
                        <div class="text-secondary small">{{ __('cart::admin.product') }}</div>
                        <div class="h3 mb-0 mt-1">{{ $cart->items_count }}</div>
                    </div>
                    <div class="col-4">
                        <div class="text-secondary small">{{ __('cart::admin.total') }}</div>
                        <div class="h3 mb-0 mt-1">{{ number_format($cart->total, 0) }}<small class="text-secondary fs-5"> {{ $cart->currency_code }}</small></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tarih Bilgileri -->
<div class="d-flex gap-4 text-secondary small mb-4">
    <span><i class="fas fa-calendar me-1"></i>{{ __('cart::admin.created_date') }}: {{ $cart->created_at->format('d.m.Y H:i') }}</span>
    @if($cart->last_activity_at)
        <span><i class="fas fa-clock me-1"></i>{{ __('cart::admin.last_activity_date') }}: {{ $cart->last_activity_at->format('d.m.Y H:i') }}</span>
    @endif
    @if($cart->ip_address)
        <span><i class="fas fa-globe me-1"></i>{{ $cart->ip_address }}</span>
    @endif
</div>

<!-- Ürünler -->
@if($cart->items->isEmpty())
    <div class="empty py-4">
        <div class="empty-icon"><i class="fas fa-box-open" style="font-size: 2rem; opacity: 0.3;"></i></div>
        <p class="empty-title">{{ __('cart::admin.empty_cart') }}</p>
    </div>
@else
    <div class="table-responsive">
        <table class="table table-vcenter">
            <thead>
                <tr>
                    <th>{{ __('cart::admin.product') }}</th>
                    <th class="text-center" style="width: 80px;">{{ __('cart::admin.quantity') }}</th>
                    <th class="text-end" style="width: 100px;">{{ __('cart::admin.unit_price') }}</th>
                    <th class="text-end" style="width: 100px;">{{ __('cart::admin.total') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cart->items as $item)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($item->item_image)
                                    <img src="{{ $item->item_image }}" class="avatar me-2" alt="">
                                @else
                                    <span class="avatar bg-secondary-lt me-2"><i class="fas fa-box"></i></span>
                                @endif
                                <div>
                                    <div class="fw-bold">{{ Str::limit($item->item_name, 30) }}</div>
                                    <div class="text-secondary small">{{ class_basename($item->cartable_type) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-azure-lt">{{ $item->quantity }}</span>
                        </td>
                        <td class="text-end font-monospace">{{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-end font-monospace fw-bold">{{ number_format($item->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Toplam -->
    <div class="card bg-light mt-3">
        <div class="card-body py-2">
            <div class="row align-items-center">
                <div class="col">
                    @if($cart->discount_amount > 0)
                        <span class="text-success me-3"><i class="fas fa-tag me-1"></i>-{{ number_format($cart->discount_amount, 2) }} {{ __('cart::admin.discount') }}</span>
                    @endif
                    @if($cart->tax_amount > 0)
                        <span class="text-secondary"><i class="fas fa-percent me-1"></i>{{ number_format($cart->tax_amount, 2) }} {{ __('cart::admin.tax') }}</span>
                    @endif
                </div>
                <div class="col-auto">
                    <span class="h2 mb-0">{{ number_format($cart->total, 2) }} {{ $cart->currency_code }}</span>
                </div>
            </div>
        </div>
    </div>
@endif
