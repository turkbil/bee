{{-- helper.blade.php'den section'lar gelecek --}}
@include('payment::admin.helper')

<div>
    <div class="card">
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
                                <button wire:click="viewPayment({{ $payment->payment_id }})" class="btn btn-sm btn-ghost-primary">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="{{ route('admin.payment.detail', $payment->payment_id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
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

    {{-- Payment Detail Modal --}}
    @if($showModal && $selectedPayment)
    <div class="modal modal-blur fade show" style="display: block; z-index: 10000;" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-receipt me-2"></i>
                        Ödeme Detayı: {{ $selectedPayment->payment_number }}
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        {{-- Sol Kolon: Ödeme Bilgileri --}}
                        <div class="col-md-8">
                            {{-- Temel Bilgiler --}}
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h4 class="card-title">
                                        <i class="fas fa-info-circle me-1"></i> Temel Bilgiler
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <strong class="text-muted d-block mb-1">Payment Number:</strong>
                                                <span class="badge bg-secondary fs-6">{{ $selectedPayment->payment_number }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <strong class="text-muted d-block mb-1">Gateway Transaction ID:</strong>
                                                <code>{{ $selectedPayment->gateway_transaction_id ?? '-' }}</code>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <strong class="text-muted d-block mb-1">Tutar:</strong>
                                            <h2 class="text-primary mb-0">{{ number_format($selectedPayment->amount, 2) }} {{ $selectedPayment->currency }}</h2>
                                        </div>
                                        <div class="col-md-6">
                                            <strong class="text-muted d-block mb-1">Durum:</strong>
                                            <span class="badge bg-{{ $selectedPayment->status === 'completed' ? 'success' : ($selectedPayment->status === 'failed' ? 'danger' : 'warning') }} fs-5">
                                                {{ ucfirst($selectedPayment->status) }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <strong class="text-muted d-block mb-1">Gateway:</strong>
                                            <span class="badge bg-primary">{{ strtoupper($selectedPayment->gateway) }}</span>
                                        </div>
                                        <div class="col-md-6">
                                            <strong class="text-muted d-block mb-1">Payment Type:</strong>
                                            <span class="badge bg-info">{{ ucfirst($selectedPayment->payment_type) }}</span>
                                        </div>
                                    </div>

                                    @if($selectedPayment->card_last_four)
                                    <hr class="my-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong class="text-muted d-block mb-1">Kart Bilgileri:</strong>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-credit-card text-primary fs-2 me-2"></i>
                                                <span>{{ $selectedPayment->card_brand }} **** {{ $selectedPayment->card_last_four }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <strong class="text-muted d-block mb-1">Taksit:</strong>
                                            <span class="badge bg-secondary">{{ $selectedPayment->installment_count }} Taksit</span>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Sipariş/Payable Bilgileri --}}
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h4 class="card-title">
                                        <i class="fas fa-shopping-cart me-1"></i> Sipariş Bilgileri
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <strong class="text-muted d-block mb-1">Payable Model:</strong>
                                            <span class="badge bg-dark">{{ class_basename($selectedPayment->payable_type) }} #{{ $selectedPayment->payable_id }}</span>
                                        </div>
                                    </div>

                                    @if($selectedPayment->metadata)
                                    <div class="alert alert-info">
                                        <strong><i class="fas fa-info-circle me-1"></i> Metadata:</strong>
                                        <pre class="mb-0 mt-2" style="font-size: 12px; max-height: 200px; overflow-y: auto;">{{ json_encode($selectedPayment->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Gateway Response --}}
                            @if($selectedPayment->gateway_response)
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h4 class="card-title">
                                        <i class="fas fa-code me-1"></i> Gateway Response
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <pre class="bg-dark text-white p-3 rounded" style="max-height: 300px; overflow-y: auto;">{{ json_encode($selectedPayment->gateway_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </div>
                            </div>
                            @endif
                        </div>

                        {{-- Sağ Kolon: Tarihler & Notlar --}}
                        <div class="col-md-4">
                            {{-- Tarih Bilgileri --}}
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h4 class="card-title">
                                        <i class="fas fa-clock me-1"></i> Tarihler
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <strong class="text-muted d-block mb-1">Oluşturulma:</strong>
                                        <span>{{ $selectedPayment->created_at->format('d.m.Y H:i:s') }}</span>
                                    </div>

                                    @if($selectedPayment->paid_at)
                                    <div class="mb-3">
                                        <strong class="text-success d-block mb-1">Ödeme Tarihi:</strong>
                                        <span>{{ $selectedPayment->paid_at->format('d.m.Y H:i:s') }}</span>
                                    </div>
                                    @endif

                                    @if($selectedPayment->failed_at)
                                    <div class="mb-3">
                                        <strong class="text-danger d-block mb-1">Hata Tarihi:</strong>
                                        <span>{{ $selectedPayment->failed_at->format('d.m.Y H:i:s') }}</span>
                                    </div>
                                    @endif

                                    @if($selectedPayment->cancelled_at)
                                    <div class="mb-3">
                                        <strong class="text-warning d-block mb-1">İptal Tarihi:</strong>
                                        <span>{{ $selectedPayment->cancelled_at->format('d.m.Y H:i:s') }}</span>
                                    </div>
                                    @endif

                                    @if($selectedPayment->refunded_at)
                                    <div class="mb-3">
                                        <strong class="text-info d-block mb-1">İade Tarihi:</strong>
                                        <span>{{ $selectedPayment->refunded_at->format('d.m.Y H:i:s') }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Notlar --}}
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h4 class="card-title mb-0">
                                            <i class="fas fa-sticky-note me-1"></i> Notlar
                                        </h4>
                                        @if(!$editingNotes)
                                            <button type="button" class="btn btn-sm btn-primary" wire:click="toggleEditNotes">
                                                <i class="fas fa-edit"></i> Düzenle
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-body">
                                    @if($editingNotes)
                                        <div>
                                            <textarea wire:model="notes" class="form-control mb-3" rows="4" placeholder="Not ekleyin..."></textarea>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-success btn-sm" wire:click="saveNotes">
                                                    <i class="fas fa-save me-1"></i> Kaydet
                                                </button>
                                                <button type="button" class="btn btn-secondary btn-sm" wire:click="cancelEditNotes">
                                                    <i class="fas fa-times me-1"></i> İptal
                                                </button>
                                            </div>
                                        </div>
                                    @else
                                        @if($selectedPayment->notes)
                                            <div class="alert alert-secondary mb-0">
                                                {{ $selectedPayment->notes }}
                                            </div>
                                        @else
                                            <p class="text-muted mb-0">Not yok</p>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="me-auto">
                        <button type="button" class="btn btn-outline-secondary" wire:click="previousPayment" wire:loading.attr="disabled" wire:target="previousPayment,nextPayment" @if(!$this->canGoPrevious()) disabled @endif>
                            <span wire:loading.remove wire:target="previousPayment">
                                <i class="fas fa-arrow-left me-1"></i> Önceki
                            </span>
                            <span wire:loading wire:target="previousPayment">
                                <span class="spinner-border spinner-border-sm me-1"></span> Yükleniyor...
                            </span>
                        </button>
                        <button type="button" class="btn btn-outline-secondary" wire:click="nextPayment" wire:loading.attr="disabled" wire:target="previousPayment,nextPayment" @if(!$this->canGoNext()) disabled @endif>
                            <span wire:loading.remove wire:target="nextPayment">
                                Sonraki <i class="fas fa-arrow-right ms-1"></i>
                            </span>
                            <span wire:loading wire:target="nextPayment">
                                <span class="spinner-border spinner-border-sm me-1"></span> Yükleniyor...
                            </span>
                        </button>
                        <small class="text-muted ms-2" wire:loading.remove wire:target="previousPayment,nextPayment">
                            <i class="fas fa-keyboard me-1"></i> ← → Ok tuşları ile geçiş yapabilirsiniz
                        </small>
                    </div>
                    <button type="button" class="btn btn-secondary" wire:click="closeModal">
                        <i class="fas fa-times me-1"></i> Kapat
                    </button>
                    <a href="{{ route('admin.payment.detail', $selectedPayment->payment_id) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-1"></i> Detaylı Düzenle
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" style="z-index: 9999;"></div>
    @endif
</div>
