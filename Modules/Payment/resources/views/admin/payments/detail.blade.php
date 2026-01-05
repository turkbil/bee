{{-- helper.blade.php'den section'lar gelecek --}}
@include('payment::admin.helper')

@section('title')
    Ödeme Detayı: {{ $payment->payment_number }}
@endsection

<div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Ödeme Bilgileri</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Payment Number:</strong><br>
                            <span class="text-muted">{{ $payment->payment_number }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong>Gateway Transaction ID:</strong><br>
                            <span class="text-muted">{{ $payment->gateway_transaction_id ?? '-' }}</span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Tutar:</strong><br>
                            <span class="h3">{{ number_format($payment->amount, 2) }} {{ $payment->currency }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong>Durum:</strong><br>
                            <span class="badge bg-{{ $payment->status === 'completed' ? 'success' : ($payment->status === 'failed' ? 'danger' : 'warning') }} fs-5">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Gateway:</strong><br>
                            <span class="text-muted">{{ strtoupper($payment->gateway) }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong>Payment Type:</strong><br>
                            <span class="text-muted">{{ ucfirst($payment->payment_type) }}</span>
                        </div>
                    </div>

                    <hr>

                    {{-- Müşteri ve Adres Bilgileri --}}
                    @if($payment->payable)
                    @php
                        $order = $payment->payable;
                        $billingAddr = is_array($order->billing_address ?? null) ? $order->billing_address : json_decode($order->billing_address ?? '[]', true);
                        $shippingAddr = is_array($order->shipping_address ?? null) ? $order->shipping_address : json_decode($order->shipping_address ?? '[]', true);
                    @endphp

                    <div class="card mb-3">
                        <div class="card-header bg-primary-lt">
                            <h3 class="card-title"><i class="fas fa-user me-2"></i>Müşteri & Fatura Bilgileri</h3>
                        </div>
                        <div class="card-body">
                            {{-- Müşteri Bilgileri --}}
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong class="text-muted d-block mb-2">MÜŞTERİ BİLGİLERİ</strong>
                                    @if($order->customer_name)
                                        <div class="mb-2"><i class="fas fa-user text-primary me-2"></i><strong>{{ $order->customer_name }}</strong></div>
                                    @endif
                                    @if($order->customer_email)
                                        <div class="mb-2"><i class="fas fa-envelope text-muted me-2"></i>{{ $order->customer_email }}</div>
                                    @endif
                                    @if($order->customer_phone)
                                        <div class="mb-2"><i class="fas fa-phone text-muted me-2"></i>{{ $order->customer_phone }}</div>
                                    @endif
                                    @if($order->customer_company)
                                        <div class="mb-2"><i class="fas fa-building text-muted me-2"></i>{{ $order->customer_company }}</div>
                                    @endif
                                </div>

                                {{-- Fatura Bilgileri --}}
                                <div class="col-md-6">
                                    <strong class="text-muted d-block mb-2">FATURA BİLGİLERİ</strong>
                                    @if(!empty($billingAddr))
                                        @if(!empty($billingAddr['full_name']) || (!empty($billingAddr['first_name']) && !empty($billingAddr['last_name'])))
                                            <div class="mb-2">
                                                <i class="fas fa-user text-primary me-2"></i>
                                                <strong>{{ $billingAddr['full_name'] ?? ($billingAddr['first_name'] . ' ' . $billingAddr['last_name']) }}</strong>
                                            </div>
                                        @endif

                                        @if(!empty($billingAddr['company_name']))
                                            <div class="mb-2"><i class="fas fa-building text-muted me-2"></i>{{ $billingAddr['company_name'] }}</div>
                                        @endif

                                        @if(!empty($billingAddr['tax_office']) || !empty($billingAddr['tax_number']) || !empty($order->customer_tax_office) || !empty($order->customer_tax_number))
                                            <div class="mb-2">
                                                <i class="fas fa-file-invoice text-warning me-2"></i>
                                                @if(!empty($billingAddr['tax_office']) || !empty($order->customer_tax_office))
                                                    <strong>{{ $billingAddr['tax_office'] ?? $order->customer_tax_office }}</strong>
                                                @endif
                                                @if(!empty($billingAddr['tax_number']) || !empty($order->customer_tax_number))
                                                    - {{ $billingAddr['tax_number'] ?? $order->customer_tax_number }}
                                                @endif
                                            </div>
                                        @endif

                                        @if(!empty($billingAddr['phone']))
                                            <div class="mb-2"><i class="fas fa-phone text-muted me-2"></i>{{ $billingAddr['phone'] }}</div>
                                        @endif

                                        @if(!empty($billingAddr['email']))
                                            <div class="mb-2"><i class="fas fa-envelope text-muted me-2"></i>{{ $billingAddr['email'] }}</div>
                                        @endif

                                        @if(!empty($billingAddr['address_line_1']) || !empty($billingAddr['city']) || !empty($billingAddr['district']))
                                            <div class="mt-3 p-2 bg-light rounded">
                                                @if(!empty($billingAddr['address_line_1']))
                                                    <div class="small">{{ $billingAddr['address_line_1'] }}</div>
                                                @endif
                                                @if(!empty($billingAddr['address_line_2']))
                                                    <div class="small">{{ $billingAddr['address_line_2'] }}</div>
                                                @endif
                                                @if(!empty($billingAddr['neighborhood']))
                                                    <div class="small">{{ $billingAddr['neighborhood'] }}</div>
                                                @endif
                                                @if(!empty($billingAddr['district']) || !empty($billingAddr['city']))
                                                    <div class="small">{{ $billingAddr['district'] }}{{ !empty($billingAddr['district']) && !empty($billingAddr['city']) ? ', ' : '' }}{{ $billingAddr['city'] }}</div>
                                                @endif
                                                @if(!empty($billingAddr['postal_code']))
                                                    <div class="small">{{ $billingAddr['postal_code'] }}</div>
                                                @endif
                                            </div>
                                        @endif
                                    @else
                                        <div class="text-muted small">Fatura adresi bilgisi yok</div>
                                    @endif
                                </div>
                            </div>

                            {{-- Teslimat Adresi --}}
                            @if(!empty($shippingAddr) && ($shippingAddr['address_line_1'] ?? false))
                            <hr>
                            <div class="row">
                                <div class="col-12">
                                    <strong class="text-muted d-block mb-2"><i class="fas fa-truck me-2"></i>TESLİMAT ADRESİ</strong>
                                    <div class="p-2 bg-light rounded">
                                        @if(!empty($shippingAddr['full_name']) || (!empty($shippingAddr['first_name']) && !empty($shippingAddr['last_name'])))
                                            <div class="mb-1"><strong>{{ $shippingAddr['full_name'] ?? ($shippingAddr['first_name'] . ' ' . $shippingAddr['last_name']) }}</strong></div>
                                        @endif
                                        @if(!empty($shippingAddr['phone']))
                                            <div class="small mb-1">{{ $shippingAddr['phone'] }}</div>
                                        @endif
                                        <div class="small">{{ $shippingAddr['address_line_1'] }}</div>
                                        @if(!empty($shippingAddr['address_line_2']))
                                            <div class="small">{{ $shippingAddr['address_line_2'] }}</div>
                                        @endif
                                        @if(!empty($shippingAddr['neighborhood']))
                                            <div class="small">{{ $shippingAddr['neighborhood'] }}</div>
                                        @endif
                                        <div class="small">{{ $shippingAddr['district'] }}{{ !empty($shippingAddr['district']) && !empty($shippingAddr['city']) ? ', ' : '' }}{{ $shippingAddr['city'] }}</div>
                                        @if(!empty($shippingAddr['postal_code']))
                                            <div class="small">{{ $shippingAddr['postal_code'] }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <hr>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <strong>Payable Model:</strong><br>
                            <span class="text-muted">{{ $payment->payable_type }} #{{ $payment->payable_id }}</span>
                        </div>
                    </div>

                    @if($payment->card_last_four)
                    <hr>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Kart Bilgileri:</strong><br>
                            <span class="text-muted">{{ $payment->card_brand }} **** {{ $payment->card_last_four }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong>Taksit:</strong><br>
                            <span class="text-muted">{{ $payment->installment_count }} Taksit</span>
                        </div>
                    </div>
                    @endif

                    <hr>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Oluşturulma:</strong><br>
                            <span class="text-muted">{{ $payment->created_at->format('d.m.Y H:i:s') }}</span>
                        </div>
                        @if($payment->paid_at)
                        <div class="col-md-4">
                            <strong>Ödeme Tarihi:</strong><br>
                            <span class="text-muted">{{ $payment->paid_at->format('d.m.Y H:i:s') }}</span>
                        </div>
                        @endif
                        @if($payment->failed_at)
                        <div class="col-md-4">
                            <strong>Hata Tarihi:</strong><br>
                            <span class="text-muted text-danger">{{ $payment->failed_at->format('d.m.Y H:i:s') }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            @if($payment->gateway_response)
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Gateway Response</h3>
                </div>
                <div class="card-body">
                    <pre class="bg-dark text-white p-3" style="max-height: 400px; overflow-y: auto;">{{ json_encode($payment->gateway_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Notlar</h3>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="updateNotes">
                        <textarea wire:model="notes" class="form-control" rows="6" placeholder="Ödeme notları..."></textarea>
                        <button type="submit" class="btn btn-primary mt-2 w-100">
                            Kaydet
                        </button>
                    </form>
                </div>
            </div>

            @if($payment->metadata)
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Metadata</h3>
                </div>
                <div class="card-body">
                    <pre class="bg-light p-2" style="font-size: 11px;">{{ json_encode($payment->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </div>
            @endif
        </div>
    </div>
