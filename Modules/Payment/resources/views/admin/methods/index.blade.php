{{-- helper.blade.php'den section'lar gelecek --}}
@include('payment::admin.helper')

@section('title')
    Ödeme Yöntemleri
@endsection

<div class="card">
        <div class="card-header">
            <h3 class="card-title">Payment Methods (PayTR, Stripe, Iyzico...)</h3>
        </div>

        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <input type="text" wire:model.live="search" class="form-control" placeholder="Slug ile ara...">
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
                            <th>Başlık</th>
                            <th>Slug</th>
                            <th>Gateway</th>
                            <th>Mode</th>
                            <th>Para Birimleri</th>
                            <th>Durum</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($methods as $method)
                        <tr>
                            <td>
                                <strong>{{ is_array($method->title) ? ($method->title['tr'] ?? $method->title['en'] ?? 'N/A') : $method->title }}</strong>
                            </td>
                            <td>
                                <code>{{ $method->slug }}</code>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ strtoupper($method->gateway) }}</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $method->gateway_mode === 'live' ? 'success' : 'warning' }}">
                                    {{ strtoupper($method->gateway_mode) }}
                                </span>
                            </td>
                            <td>
                                @if(is_array($method->supported_currencies))
                                    @foreach($method->supported_currencies as $currency)
                                        <span class="badge bg-secondary">{{ $currency }}</span>
                                    @endforeach
                                @endif
                            </td>
                            <td>
                                <button wire:click="toggleActive({{ $method->payment_method_id }})"
                                        class="btn btn-sm btn-{{ $method->is_active ? 'success' : 'secondary' }}">
                                    {{ $method->is_active ? 'Aktif' : 'Pasif' }}
                                </button>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.payment.methods.manage', $method->payment_method_id) }}" class="btn btn-sm btn-primary">
                                    Düzenle
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                Henüz ödeme yöntemi tanımlanmamış
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $methods->links() }}
            </div>
        </div>
    </div>
