{{-- helper.blade.php'den section'lar gelecek --}}
@include('payment::admin.helper')

@section('title')
    {{ $paymentMethodId ? 'Ödeme Yöntemi Düzenle' : 'Yeni Ödeme Yöntemi' }}
@endsection

<form wire:submit.prevent="save">
    <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Temel Bilgiler</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Başlık (TR)</label>
                            <input type="text" wire:model="title.tr" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Başlık (EN)</label>
                            <input type="text" wire:model="title.en" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Slug</label>
                            <input type="text" wire:model="slug" class="form-control" required>
                            <small class="text-muted">Örnek: paytr-credit-card</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Açıklama (TR)</label>
                            <textarea wire:model="description.tr" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Açıklama (EN)</label>
                            <textarea wire:model="description.en" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Gateway Ayarları</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Gateway</label>
                                <select wire:model="gateway" class="form-select" required>
                                    <option value="paytr">PayTR</option>
                                    <option value="stripe">Stripe</option>
                                    <option value="iyzico">Iyzico</option>
                                    <option value="paypal">PayPal</option>
                                    <option value="manual">Manuel</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Mode</label>
                                <select wire:model="gateway_mode" class="form-select" required>
                                    <option value="test">Test</option>
                                    <option value="live">Live</option>
                                </select>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-1"></i>
                            Gateway config (API keys) JSON formatında girilmelidir.
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Ücret & Limitler</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Sabit Ücret</label>
                                <input type="number" wire:model="fixed_fee" class="form-control" step="0.01" min="0">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Yüzde Ücret (%)</label>
                                <input type="number" wire:model="percentage_fee" class="form-control" step="0.01" min="0" max="100">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Desteklenen Para Birimleri</label>
                                <select wire:model="supported_currencies" class="form-select" multiple>
                                    <option value="TRY">TRY</option>
                                    <option value="USD">USD</option>
                                    <option value="EUR">EUR</option>
                                    <option value="GBP">GBP</option>
                                </select>
                                <small class="text-muted">CTRL ile çoklu seçim yapabilirsiniz</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Sıra</label>
                                <input type="number" wire:model="sort_order" class="form-control" min="0">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Taksit Ayarları</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-check">
                                <input type="checkbox" wire:model="supports_installment" class="form-check-input">
                                <span class="form-check-label">Taksit Destekliyor</span>
                            </label>
                        </div>

                        @if($supports_installment)
                        <div class="mb-3">
                            <label class="form-label">Maksimum Taksit</label>
                            <input type="number" wire:model="max_installments" class="form-control" min="1" max="12">
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Durum</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-check form-switch">
                                <input type="checkbox" wire:model="is_active" class="form-check-input">
                                <span class="form-check-label">Aktif</span>
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-1"></i>
                            {{ $paymentMethodId ? 'Güncelle' : 'Kaydet' }}
                        </button>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Ödeme Tipleri Desteği</h3>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small">Bu ödeme yöntemi hangi işlem tiplerini destekliyor?</p>

                        <div class="mb-2">
                            <label class="form-check">
                                <input type="checkbox" checked disabled class="form-check-input">
                                <span class="form-check-label">Satış (Purchase)</span>
                            </label>
                        </div>

                        <div class="mb-2">
                            <label class="form-check">
                                <input type="checkbox" disabled class="form-check-input">
                                <span class="form-check-label">Abonelik (Subscription)</span>
                            </label>
                        </div>

                        <div class="mb-2">
                            <label class="form-check">
                                <input type="checkbox" disabled class="form-check-input">
                                <span class="form-check-label">Bağış (Donation)</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
