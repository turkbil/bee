@php
    $payment = $order->payments->first();
    $billingAddress = $order->billing_address ?? [];

    $statusBadge = match($order->status) {
        'pending' => 'bg-yellow text-yellow-fg',
        'processing' => 'bg-blue text-white',
        'completed' => 'bg-green text-white',
        'cancelled' => 'bg-red text-white',
        default => 'bg-secondary'
    };

    $paymentBadge = match($order->payment_status) {
        'pending' => 'bg-yellow text-yellow-fg',
        'paid', 'completed' => 'bg-green text-white',
        'failed' => 'bg-red text-white',
        default => 'bg-secondary'
    };
@endphp

<div class="order-detail-modal">
    {{-- Başlık Bandı --}}
    <div class="bg-primary text-white rounded-3 p-4 mb-4">
        <div class="row align-items-center">
            <div class="col">
                <div class="text-white-50 small mb-1">Sipariş Tutarı</div>
                <div class="display-5 fw-bold">{{ number_format($order->total_amount, 2, ',', '.') }} TL</div>
            </div>
            <div class="col-auto text-end">
                <div class="d-flex gap-2 justify-content-end mb-2">
                    <span class="badge {{ $statusBadge }} fs-6">{{ __('cart::admin.order_status_' . $order->status) }}</span>
                    <span class="badge {{ $paymentBadge }} fs-6">{{ __('cart::admin.payment_status_' . $order->payment_status) }}</span>
                </div>
                <div class="text-white-50 small">
                    <i class="fas fa-calendar me-1"></i>{{ $order->created_at->format('d.m.Y H:i') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Fatura Bilgileri --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header border-0 bg-white py-3">
            <h4 class="card-title mb-0">
                <i class="fas fa-file-invoice-dollar text-primary me-2"></i>Fatura Bilgileri
            </h4>
        </div>
        <div class="card-body pt-0">
            <div class="row g-4">
                {{-- Müşteri Bilgileri --}}
                <div class="col-lg-6">
                    <div class="bg-light rounded-3 p-3 h-100">
                        <div class="text-muted small mb-3 text-uppercase fw-medium">
                            <i class="fas fa-user me-1"></i>Müşteri / Firma
                        </div>

                        <div class="mb-3">
                            <div class="fw-bold fs-5">{{ $order->customer_name ?: 'Belirtilmemiş' }}</div>
                            @if($order->customer_company)
                                <div class="text-primary fw-medium">
                                    <i class="fas fa-building me-1"></i>{{ $order->customer_company }}
                                </div>
                            @endif
                        </div>

                        <div class="row g-3">
                            <div class="col-12">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-envelope text-muted me-2" style="width: 16px;"></i>
                                    @if($order->customer_email)
                                        <a href="mailto:{{ $order->customer_email }}" class="text-reset">{{ $order->customer_email }}</a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-phone text-muted me-2" style="width: 16px;"></i>
                                    @if($order->customer_phone)
                                        <a href="tel:{{ $order->customer_phone }}" class="text-reset">{{ $order->customer_phone }}</a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if($order->customer_tax_office || $order->customer_tax_number)
                        <hr class="my-3">
                        <div class="row g-2">
                            @if($order->customer_tax_office)
                            <div class="col-12">
                                <span class="text-muted small">Vergi Dairesi:</span>
                                <span class="fw-medium ms-1">{{ $order->customer_tax_office }}</span>
                            </div>
                            @endif
                            @if($order->customer_tax_number)
                            <div class="col-12">
                                <span class="text-muted small">VKN/TCKN:</span>
                                <span class="fw-bold font-monospace ms-1 text-primary">{{ $order->customer_tax_number }}</span>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Fatura Adresi --}}
                <div class="col-lg-6">
                    <div class="bg-light rounded-3 p-3 h-100">
                        <div class="text-muted small mb-3 text-uppercase fw-medium">
                            <i class="fas fa-map-marker-alt me-1"></i>Fatura Adresi
                        </div>

                        @if(!empty($billingAddress) && !empty($billingAddress['address_line_1']))
                            <div class="fw-medium mb-2">
                                {{ $billingAddress['full_name'] ?? ($billingAddress['first_name'] ?? '') . ' ' . ($billingAddress['last_name'] ?? '') }}
                            </div>

                            <div class="text-dark">
                                {{ $billingAddress['address_line_1'] ?? '' }}
                                @if(!empty($billingAddress['address_line_2']))
                                    <br>{{ $billingAddress['address_line_2'] }}
                                @endif
                            </div>

                            <div class="mt-2">
                                @if(!empty($billingAddress['district']))
                                    {{ $billingAddress['district'] }},
                                @endif
                                @if(!empty($billingAddress['city']))
                                    {{ $billingAddress['city'] }}
                                @endif
                                @if(!empty($billingAddress['postal_code']))
                                    <span class="text-muted">{{ $billingAddress['postal_code'] }}</span>
                                @endif
                            </div>

                            @if(!empty($billingAddress['country_code']) && $billingAddress['country_code'] !== 'TR')
                                <div class="text-muted small mt-1">{{ $billingAddress['country_code'] }}</div>
                            @endif

                            @if(!empty($billingAddress['phone']))
                                <div class="mt-2 pt-2 border-top">
                                    <i class="fas fa-phone text-muted me-1"></i>
                                    <span>{{ $billingAddress['phone'] }}</span>
                                </div>
                            @endif
                        @else
                            <div class="text-muted fst-italic">
                                <i class="fas fa-info-circle me-1"></i>Fatura adresi girilmemiş
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Ödeme Detayları --}}
    @if($payment)
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header border-0 bg-white py-3">
            <h4 class="card-title mb-0">
                <i class="fas fa-credit-card text-success me-2"></i>Ödeme Detayları
            </h4>
        </div>
        <div class="card-body pt-0">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="text-muted small">Ödeme No</div>
                    <div class="fw-medium font-monospace">{{ $payment->payment_number }}</div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted small">Yöntem</div>
                    <div class="fw-medium">
                        @if($payment->gateway === 'paytr')
                            <i class="fas fa-credit-card text-purple me-1"></i>Kredi Kartı
                        @elseif(in_array($payment->gateway, ['manual', 'bank_transfer']))
                            <i class="fas fa-building-columns text-cyan me-1"></i>Havale/EFT
                        @else
                            <i class="fas fa-wallet text-muted me-1"></i>{{ ucfirst($payment->gateway) }}
                        @endif
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted small">Tutar</div>
                    <div class="fw-bold text-success">{{ number_format($payment->amount, 2, ',', '.') }} TL</div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted small">Tarih</div>
                    <div class="fw-medium">
                        {{ $payment->paid_at ? $payment->paid_at->format('d.m.Y H:i') : $payment->created_at->format('d.m.Y H:i') }}
                    </div>
                </div>
            </div>

            @if($payment->gateway_transaction_id)
            <div class="mt-3 pt-3 border-top">
                <span class="text-muted small">İşlem ID:</span>
                <code class="ms-2">{{ $payment->gateway_transaction_id }}</code>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Sipariş Kalemleri --}}
    @if($order->items->isNotEmpty())
    <div class="card border-0 shadow-sm">
        <div class="card-header border-0 bg-white py-3 d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">
                <i class="fas fa-shopping-bag text-orange me-2"></i>Sipariş Kalemleri
            </h4>
            <span class="badge bg-azure-lt text-azure">{{ $order->items->count() }} ürün</span>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Ürün / Hizmet</th>
                        <th class="text-center" style="width: 80px;">Adet</th>
                        <th class="text-end" style="width: 120px;">Birim Fiyat</th>
                        <th class="text-end" style="width: 120px;">Toplam</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    @php
                        $metadata = $item->metadata ?? [];
                        $isCorporate = ($metadata['type'] ?? null) === 'corporate_bulk';
                        $cycleLabel = $metadata['cycle_label'][app()->getLocale()] ?? $metadata['cycle_label']['tr'] ?? null;
                    @endphp
                    <tr>
                        <td>
                            <div class="fw-medium">{{ $item->product_name ?: $item->item_title }}</div>
                            @if($cycleLabel || $isCorporate)
                            <div class="mt-1">
                                @if($cycleLabel)
                                    <span class="badge bg-blue-lt text-blue">{{ $cycleLabel }}</span>
                                @endif
                                @if($isCorporate)
                                    <span class="badge bg-orange-lt text-orange">Kurumsal</span>
                                @endif
                            </div>
                            @endif
                        </td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-end font-monospace">{{ number_format($item->unit_price, 2, ',', '.') }}</td>
                        <td class="text-end font-monospace fw-medium">{{ number_format($item->total_price, 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Toplam Özeti --}}
        <div class="card-footer bg-light border-0">
            <div class="row justify-content-end">
                <div class="col-auto">
                    <table class="table table-sm table-borderless mb-0" style="min-width: 250px;">
                        @if(($order->subtotal ?? 0) > 0 && $order->subtotal != $order->total_amount)
                        <tr>
                            <td class="text-muted">Ara Toplam</td>
                            <td class="text-end font-monospace">{{ number_format($order->subtotal, 2, ',', '.') }} TL</td>
                        </tr>
                        @endif
                        @if(($order->discount_amount ?? 0) > 0)
                        <tr class="text-success">
                            <td><i class="fas fa-tag me-1"></i>İndirim</td>
                            <td class="text-end font-monospace">-{{ number_format($order->discount_amount, 2, ',', '.') }} TL</td>
                        </tr>
                        @endif
                        @if(($order->tax_amount ?? 0) > 0)
                        <tr>
                            <td class="text-muted">KDV</td>
                            <td class="text-end font-monospace">{{ number_format($order->tax_amount, 2, ',', '.') }} TL</td>
                        </tr>
                        @endif
                        <tr class="fw-bold fs-5 border-top">
                            <td>Genel Toplam</td>
                            <td class="text-end font-monospace text-primary">{{ number_format($order->total_amount, 2, ',', '.') }} TL</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Sipariş Bilgileri Footer --}}
    <div class="mt-4 pt-3 border-top">
        <div class="row text-muted small">
            <div class="col-auto">
                <i class="fas fa-hashtag me-1"></i>{{ $order->order_number }}
            </div>
            @if($order->confirmed_at)
            <div class="col-auto">
                <i class="fas fa-check-circle text-success me-1"></i>Onaylandı: {{ $order->confirmed_at->format('d.m.Y H:i') }}
            </div>
            @endif
            @if($order->ip_address)
            <div class="col-auto">
                <i class="fas fa-globe me-1"></i>{{ $order->ip_address }}
            </div>
            @endif
        </div>
    </div>
</div>
