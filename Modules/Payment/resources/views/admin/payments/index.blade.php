{{-- helper.blade.php'den section'lar gelecek --}}
@include('payment::admin.helper')

<div class="container-xl">
    <div class="card mt-3">
        <div class="card-header">
            <h3 class="card-title">Ödeme Listesi</h3>
        </div>

        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <input type="text" wire:model.live="search" class="form-control" placeholder="Payment Number / Transaction ID ile ara...">
                </div>
                <div class="col-md-3">
                    <select wire:model.live="status" class="form-select">
                        <option value="">Tüm Durumlar</option>
                        <option value="pending">Bekliyor</option>
                        <option value="processing">İşleniyor</option>
                        <option value="completed">Tamamlandı</option>
                        <option value="failed">Başarısız</option>
                        <option value="cancelled">İptal</option>
                        <option value="refunded">İade Edildi</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select wire:model.live="gateway" class="form-select">
                        <option value="">Tüm Gateway'ler</option>
                        <option value="paytr">PayTR</option>
                        <option value="stripe">Stripe</option>
                        <option value="iyzico">Iyzico</option>
                        <option value="paypal">PayPal</option>
                        <option value="manual">Manuel</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Payment #</th>
                            <th>Payable</th>
                            <th>Tutar</th>
                            <th>Gateway</th>
                            <th>Durum</th>
                            <th>Tarih</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                        <tr>
                            <td>
                                <strong>{{ $payment->payment_number }}</strong>
                            </td>
                            <td>
                                <div class="text-muted small">
                                    {{ class_basename($payment->payable_type) }} #{{ $payment->payable_id }}
                                </div>
                            </td>
                            <td>
                                <strong>{{ number_format($payment->amount, 2) }} {{ $payment->currency }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ strtoupper($payment->gateway) }}</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $payment->status === 'completed' ? 'success' : ($payment->status === 'failed' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                            <td>
                                {{ $payment->created_at->format('d.m.Y H:i') }}
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.payment.detail', $payment->payment_id) }}" class="btn btn-sm btn-primary">
                                    Detay
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                Henüz ödeme kaydı yok
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $payments->links() }}
            </div>
        </div>
    </div>
</div>
