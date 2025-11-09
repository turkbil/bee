{{-- helper.blade.php'den section'lar gelecek --}}
@include('payment::admin.helper')

@section('title')
    Ödeme Detayı: {{ $payment->payment_number }}
@endsection

<div class="container-xl">
    <div class="row mt-3">
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
</div>
